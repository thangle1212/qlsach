<?php

require_once __DIR__ . '/BaseApiController.php';
require_once __DIR__ . '/../../Services/BorrowService.php';
require_once __DIR__ . '/../../Services/ReturnService.php';

class BorrowingApiController extends BaseApiController
{
    private $borrowService;
    private $returnService;

    private function getLoanOrFail($loanSlipId)
    {
        $loan = $this->borrowService->getLoanSlipById($loanSlipId);
        if (!$loan) {
            $this->error('Phiếu mượn không tồn tại', [], 404);
        }

        return $loan;
    }

    private function normalizeReturnItems($rawReturnItems)
    {
        if (!is_array($rawReturnItems) || empty($rawReturnItems)) {
            return [];
        }

        $returnItems = [];
        if (array_keys($rawReturnItems) === range(0, count($rawReturnItems) - 1)) {
            foreach ($rawReturnItems as $item) {
                $loanItemId = $item['loan_item_id'] ?? $item['id'] ?? null;
                $quantity = (int)($item['quantity'] ?? 1);

                if ($loanItemId !== null && $quantity > 0) {
                    $returnItems[(int)$loanItemId] = $quantity;
                }
            }
            return $returnItems;
        }

        return $rawReturnItems;
    }

    public function __construct()
    {
        parent::__construct();

        $this->borrowService = new BorrowService();
        $this->returnService = new ReturnService();
    }

    public function index()
    {
        try {
            $status = $this->request->get('status', 'active');
            $filterUserId = $this->request->get('user_id');

            $role = $_SESSION['role'] ?? 'admin';
            if ($this->permissionService->canAccessBorrowingManagement($role)) {
                if ($filterUserId) {
                    $borrowings = $this->borrowService->getUserActiveLoans($filterUserId);
                } elseif ($status === 'overdue') {
                    $borrowings = $this->borrowService->getOverdueLoans();
                } else {
                    $borrowings = $this->borrowService->getAllActiveLoans();
                }
            } else {
                if ($filterUserId && $filterUserId != ($_SESSION['user_id'] ?? null)) {
                    return $this->error('Bạn không có quyền xem phiếu mượn của người khác', [], 403);
                }

                $borrowings = $this->borrowService->getUserActiveLoans($_SESSION['user_id'] ?? 0);

                if ($status === 'overdue') {
                    $borrowings = array_values(array_filter($borrowings, function ($loan) {
                        return $loan['due_date'] < date('Y-m-d');
                    }));
                }
            }

            return $this->success(['borrowings' => $borrowings], 'Danh sách phiếu mượn');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    public function show($params = [])
    {
        try {
            $loanSlipId = $params['id'] ?? $this->request->get('id');
            if (!$loanSlipId) {
                return $this->error('Thiếu ID phiếu mượn', [], 422);
            }

            $loanSlip = $this->getLoanOrFail($loanSlipId);

            $loanDetails = $this->borrowService->getLoanDetails($loanSlipId);
            $returnHistory = $this->returnService->getReturnDetails($loanSlipId);

            return $this->success([
                'loan_slip' => $loanSlip,
                'loan_items' => $loanDetails,
                'return_history' => $returnHistory,
            ], 'Chi tiết phiếu mượn');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    public function store($params = [])
    {
        try {
            if (!$this->permissionService->canCreateBorrowings($_SESSION['role'] ?? 'admin')) {
                return $this->error('Bạn không có quyền tạo phiếu mượn', [], 403);
            }

            $userId = $this->request->get('user_id');
            $bookIds = $this->request->get('book_ids', []);
            $dueDate = $this->request->get('due_date');

            if (!is_array($bookIds)) {
                $bookIds = array_filter(explode(',', (string)$bookIds), function ($id) {
                    return trim($id) !== '';
                });
            }

            if (empty($userId) || empty($bookIds) || empty($dueDate)) {
                return $this->error('Vui lòng cung cấp đầy đủ user_id, book_ids và due_date', [], 422);
            }

            $loanSlipId = $this->borrowService->borrowBooks($userId, $bookIds, $dueDate, $_SESSION['user_id'] ?? null);
            return $this->success(['loan_slip_id' => $loanSlipId], 'Mượn sách thành công', 201);
        } catch (Exception $e) {
            return $this->error('Mượn sách thất bại: ' . $e->getMessage(), [], 400);
        }
    }

    public function renew($params = [])
    {
        try {
            $loanSlipId = $params['id'] ?? $this->request->get('id');
            if (!$loanSlipId) {
                return $this->error('Thiếu ID phiếu mượn', [], 422);
            }

            $loan = $this->borrowService->getLoanSlipById($loanSlipId);
            if (!$loan) {
                return $this->error('Phiếu mượn không tồn tại', [], 404);
            }

            if (!$this->permissionService->canRenewLoan($_SESSION['role'] ?? 'admin', $_SESSION['user_id'] ?? 0, $loan['user_id'])) {
                return $this->error('Bạn không có quyền gia hạn phiếu mượn này', [], 403);
            }

            $result = $this->borrowService->renewLoan($loanSlipId);
            if ($result) {
                return $this->success(['loan_slip_id' => $loanSlipId], 'Gia hạn mượn sách thành công');
            }

            return $this->error('Gia hạn mượn sách thất bại', [], 400);
        } catch (Exception $e) {
            return $this->error('Gia hạn mượn sách thất bại: ' . $e->getMessage(), [], 400);
        }
    }

    public function return($params = [])
    {
        try {
            $loanSlipId = $params['id'] ?? $this->request->get('id');
            if (!$loanSlipId) {
                return $this->error('Thiếu ID phiếu mượn', [], 422);
            }

            $loan = $this->borrowService->getLoanSlipById($loanSlipId);
            if (!$loan) {
                return $this->error('Phiếu mượn không tồn tại', [], 404);
            }

            if (!$this->permissionService->canProcessReturnsForLoan($_SESSION['role'] ?? 'admin', $_SESSION['user_id'] ?? 0, $loan['user_id'])) {
                return $this->error('Bạn không có quyền trả sách cho phiếu mượn này', [], 403);
            }

            $rawReturnItems = $this->request->get('return_items', []);
            $note = $this->request->get('note');

            $returnItems = $this->normalizeReturnItems($rawReturnItems);

            if (empty($returnItems)) {
                return $this->error('Vui lòng cung cấp return_items dưới dạng {loan_item_id: quantity}', [], 422);
            }

            $this->returnService->returnBooks($loanSlipId, $returnItems, $_SESSION['user_id'] ?? null, $note);
            return $this->success(['loan_slip_id' => $loanSlipId], 'Trả sách thành công');
        } catch (Exception $e) {
            return $this->error('Trả sách thất bại: ' . $e->getMessage(), [], 400);
        }
    }

    public function destroy($params = [])
    {
        try {
            $loanSlipId = $params['id'] ?? $this->request->get('id');
            if (!$loanSlipId) {
                return $this->error('Thiếu ID phiếu mượn', [], 422);
            }

            $loan = $this->getLoanOrFail($loanSlipId);

            $db = Database::getInstance();
            $db->beginTransaction();

            $loanItems = $this->borrowService->getLoanDetails($loanSlipId);
            foreach ($loanItems as $item) {
                $db->prepare('UPDATE books SET available_copies = available_copies + ? WHERE id = ?')
                    ->execute([$item['quantity'], $item['book_id']]);
                $db->prepare('UPDATE users SET current_borrow_count = GREATEST(current_borrow_count - ?, 0) WHERE id = ?')
                    ->execute([$item['quantity'], $loan['user_id']]);
            }

            $db->prepare('DELETE FROM fines WHERE loan_id = ?')->execute([$loanSlipId]);
            $db->prepare('DELETE ri FROM return_items ri JOIN loan_items li ON ri.loan_item_id = li.id WHERE li.loan_id = ?')->execute([$loanSlipId]);
            $db->prepare('DELETE FROM return_slips WHERE loan_id = ?')->execute([$loanSlipId]);
            $db->prepare('DELETE FROM loan_items WHERE loan_id = ?')->execute([$loanSlipId]);
            $db->prepare('DELETE FROM loan_slips WHERE id = ?')->execute([$loanSlipId]);

            $db->commit();

            return $this->success(['loan_slip_id' => $loanSlipId], 'Xóa phiếu mượn thành công');
        } catch (Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            return $this->error('Xóa phiếu mượn thất bại: ' . $e->getMessage(), [], 400);
        }
    }
}