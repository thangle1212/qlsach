<?php
require_once __DIR__ . '/../Services/BorrowService.php';
require_once __DIR__ . '/../Services/ReturnService.php';
require_once __DIR__ . '/../Services/FineService.php';
require_once __DIR__ . '/../Models/Book.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Reservation.php';
require_once __DIR__ . '/BaseController.php';

class BorrowingController extends BaseController {
    private $borrowService;
    private $returnService;
    private $fineService;

    public function __construct() {
        parent::__construct();
        $this->borrowService = new BorrowService();
        $this->returnService = new ReturnService();
        $this->fineService = new FineService();
    }

    public function index() {
        if ($this->permissionService->canAccessBorrowingManagement($_SESSION['role'])) {
            $activeLoans = $this->borrowService->getAllActiveLoans();
            $overdueLoans = $this->borrowService->getOverdueLoans();
        } else {
            $this->handleUnauthorized();
        }

        require __DIR__ . '/../Views/borrowings/index.php';
    }

    public function create() {
        $this->checkPermission('canCreateBorrowings', [$_SESSION['role']]);
        $books = (new Book())->getAll();
        $users = (new User())->getAll();
        require __DIR__ . '/../Views/borrowings/create.php';
    }

    public function store() {
        $this->checkPermission('canCreateBorrowings', [$_SESSION['role']]);
        
        $userId = $_POST['user_id'] ?? null;
        $bookIds = array_filter($_POST['book_ids'] ?? [$_POST['book_id'] ?? null], function($id) { return !empty($id); });
        $dueDate = $_POST['due_date'] ?? null;

        if (empty($userId) || empty($bookIds) || empty($dueDate)) {
            $this->handleRedirect(
                "index.php?controller=borrowing&action=create",
                'Vui lòng điền đầy đủ thông tin',
                'error'
            );
        }

        try {
            $this->borrowService->borrowBooks($userId, $bookIds, $dueDate, $_SESSION['user_id']);
            $this->handleRedirect("index.php?controller=borrowing", 'Mượn sách thành công');
        } catch (Exception $e) {
            $this->handleRedirect(
                "index.php?controller=borrowing",
                'Mượn sách thất bại: ' . $e->getMessage(),
                'error'
            );
        }
    }

    public function return() {
        // Only allow admin and librarian to process returns
        if (!$this->permissionService->canProcessReturns($_SESSION['role'])) {
            $this->handleUnauthorized();
        }

        $loanSlipId = $_GET['id'] ?? null;
        $loan = $this->validateAndGetLoan($loanSlipId, $this->borrowService);

        $this->checkPermission('canProcessReturnsForLoan', [$_SESSION['role'], $_SESSION['user_id'], $loan['user_id']]);

        try {
            $returnableItems = $this->returnService->getReturnableItems($loanSlipId);
            if (empty($returnableItems)) {
                $this->handleRedirect(
                    "index.php?controller=borrowing",
                    'Không có sách nào cần trả cho phiếu mượn này',
                    'error'
                );
            }

            $returnItems = [];
            foreach ($returnableItems as $item) {
                $returnItems[$item['id']] = $item['returnable_quantity'];
            }

            $processingUserId = $_SESSION['user_id']; // Only admin/librarian can access this, so use their ID

            $this->returnService->returnBooks($loanSlipId, $returnItems, $processingUserId);
            $this->handleRedirect("index.php?controller=borrowing", 'Trả sách thành công');
        } catch (Exception $e) {
            $this->handleRedirect(
                "index.php?controller=borrowing",
                'Trả sách thất bại: ' . $e->getMessage(),
                'error'
            );
        }
    }

    public function processReturn() {
        // Only allow admin and librarian to process returns
        if (!$this->permissionService->canProcessReturns($_SESSION['role'])) {
            $this->handleUnauthorized();
        }

        $loanSlipId = $_POST['loan_slip_id'] ?? null;
        $returnItems = $_POST['return_items'] ?? [];
        $note = $_POST['note'] ?? null;

        if (!$loanSlipId || empty($returnItems)) {
            $this->handleRedirect(
                "index.php?controller=borrowing",
                'Thông tin trả sách không đầy đủ',
                'error'
            );
        }

        $loan = $this->validateAndGetLoan($loanSlipId, $this->borrowService);
        $this->checkPermission('canProcessReturnsForLoan', [$_SESSION['role'], $_SESSION['user_id'], $loan['user_id']]);

        try {
            $processingUserId = $_SESSION['user_id']; // Only admin/librarian can access this, so use their ID

            $this->returnService->returnBooks($loanSlipId, $returnItems, $processingUserId, $note);
            $this->handleRedirect("index.php?controller=borrowing", 'Trả sách thành công');
        } catch (Exception $e) {
            $this->handleRedirect(
                "index.php?controller=borrowing",
                'Trả sách thất bại: ' . $e->getMessage(),
                'error'
            );
        }
    }

    public function viewReturnForm() {
        // Only allow admin and librarian to view return form
        if (!$this->permissionService->canProcessReturns($_SESSION['role'])) {
            $this->handleUnauthorized();
        }

        $loanSlipId = $_GET['id'] ?? null;
        $loan = $this->validateAndGetLoan($loanSlipId, $this->borrowService);

        $this->checkPermission('canProcessReturnsForLoan', [$_SESSION['role'], $_SESSION['user_id'], $loan['user_id']]);

        $returnableItems = $this->returnService->getReturnableItems($loanSlipId);
        $loanDetails = $this->borrowService->getLoanDetails($loanSlipId);
        $loanSlip = $this->borrowService->getLoanSlipById($loanSlipId);

        if (empty($returnableItems)) {
            $this->handleRedirect(
                "index.php?controller=borrowing",
                'Không có sách nào cần trả cho phiếu mượn này',
                'error'
            );
        }

        require __DIR__ . '/../Views/borrowings/return_form.php';
    }

    public function renew() {
        $loanSlipId = $_GET['id'] ?? null;
        $loan = $this->validateAndGetLoan($loanSlipId, $this->borrowService);

        $this->checkPermission('canRenewLoan', [$_SESSION['role'], $_SESSION['user_id'], $loan['user_id']]);

        try {
            $result = $this->borrowService->renewLoan($loanSlipId);
            if ($result) {
                $this->handleRedirect("index.php?controller=borrowing", 'Gia hạn mượn sách thành công');
            } else {
                $this->handleRedirect(
                    "index.php?controller=borrowing",
                    'Gia hạn mượn sách thất bại',
                    'error'
                );
            }
        } catch (Exception $e) {
            $this->handleRedirect(
                "index.php?controller=borrowing",
                'Gia hạn mượn sách thất bại: ' . $e->getMessage(),
                'error'
            );
        }
    }

    public function viewMember() {
        $this->checkPermission('canViewMemberInfo', [$_SESSION['role']]);

        $user_id = $_GET['user_id'] ?? null;
        if (!$user_id) {
            $this->handleRedirect(
                "index.php?controller=borrowing",
                'ID người dùng không hợp lệ',
                'error'
            );
        }

        $user = (new User())->getById($user_id);
        $userActiveLoans = $this->borrowService->getUserActiveLoans($user_id);
        $userFines = $this->fineService->getUserFines($user_id);

        require __DIR__ . '/../Views/borrowings/member_info.php';
    }

    public function reservations() {
        $this->checkPermission('canManageReservations', [$_SESSION['role']]);
        $reservations = (new Reservation())->getAll();
        require __DIR__ . '/../Views/borrowings/reservations.php';
    }

    public function approveReservation() {
        $this->checkPermission('canManageReservations', [$_SESSION['role']]);

        $reservation_id = $_GET['id'];
        $reservation = (new Reservation())->getById($reservation_id);

        if (!$reservation) {
            $this->handleRedirect(
                "index.php?controller=borrowing&action=reservations",
                'Đặt trước không tồn tại',
                'error'
            );
        }

        $result = (new Reservation())->update($reservation_id, ['status' => 'available']);

        if ($result) {
            $this->handleRedirect(
                "index.php?controller=borrowing&action=reservations",
                'Duyệt đặt trước thành công'
            );
        } else {
            $this->handleRedirect(
                "index.php?controller=borrowing&action=reservations",
                'Duyệt đặt trước thất bại',
                'error'
            );
        }
    }

    public function rejectReservation() {
        $this->checkPermission('canManageReservations', [$_SESSION['role']]);

        $reservation_id = $_GET['id'];
        $reservation = (new Reservation())->getById($reservation_id);

        if (!$reservation) {
            $this->handleRedirect(
                "index.php?controller=borrowing&action=reservations",
                'Đặt trước không tồn tại',
                'error'
            );
        }

        $result = (new Reservation())->cancel($reservation_id);

        if ($result) {
            $this->handleRedirect(
                "index.php?controller=borrowing&action=reservations",
                'Từ chối đặt trước thành công'
            );
        } else {
            $this->handleRedirect(
                "index.php?controller=borrowing&action=reservations",
                'Từ chối đặt trước thất bại',
                'error'
            );
        }
    }

    public function viewLoanDetails() {
        $loanSlipId = $_GET['id'] ?? null;
        $loan = $this->validateAndGetLoan($loanSlipId, $this->borrowService);

        $this->checkPermission('canViewLoanDetails', [$_SESSION['role'], $_SESSION['user_id'], $loan['user_id']]);

        $loanDetails = $this->borrowService->getLoanDetails($loanSlipId);
        $loanSlip = $this->borrowService->getLoanSlipById($loanSlipId);
        $returnHistory = $this->returnService->getReturnDetails($loanSlipId);

        require __DIR__ . '/../Views/borrowings/loan_details.php';
    }
}