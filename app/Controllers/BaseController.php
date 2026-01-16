<?php
require_once __DIR__ . '/../Services/PermissionService.php';

abstract class BaseController {
    protected $permissionService;

    public function __construct() {
        $this->checkAuth();
        $this->permissionService = new PermissionService();
    }

    protected function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
    }

    protected function handleUnauthorized($redirect = "index.php?controller=book") {
        $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
        header("Location: $redirect");
        exit;
    }

    protected function handleRedirect($location, $message = null, $type = 'success') {
        if ($message) {
            $_SESSION[$type] = $message;
        }
        header("Location: $location");
        exit;
    }

    protected function validateAndGetLoan($loanSlipId, $borrowService = null, $action = null) {
        if (!$loanSlipId) {
            $this->handleRedirect(
                "index.php?controller=borrowing",
                'ID phiếu mượn không hợp lệ',
                'error'
            );
        }

        if ($borrowService === null) {
            $this->handleRedirect(
                "index.php?controller=borrowing",
                'Service không được cung cấp',
                'error'
            );
        }

        $loan = $borrowService->getLoanSlipById($loanSlipId);
        if (!$loan) {
            $this->handleRedirect(
                "index.php?controller=borrowing",
                'Phiếu mượn không tồn tại',
                'error'
            );
        }

        return $loan;
    }

    protected function checkPermission($permissionMethod, $params = []) {
        $callable = [$this->permissionService, $permissionMethod];
        if (!call_user_func_array($callable, $params)) {
            $this->handleUnauthorized();
        }
    }
}