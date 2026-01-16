<?php
require_once __DIR__ . '/../Core/Database.php';

class PermissionService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Check if user can access borrowing management
     */
    public function canAccessBorrowingManagement($userRole) {
        return $userRole === 'admin' || $userRole === 'librarian';
    }

    /**
     * Check if user can create borrowings
     */
    public function canCreateBorrowings($userRole) {
        return $userRole === 'admin' || $userRole === 'librarian';
    }

    /**
     * Check if user can process returns
     */
    public function canProcessReturns($userRole) {
        return $userRole === 'admin' || $userRole === 'librarian';
    }

    /**
     * Check if user can process returns for a specific loan
     */
    public function canProcessReturnsForLoan($userRole, $userId, $loanUserId) {
        // Admin and librarian can process returns for any loan
        if ($userRole === 'admin' || $userRole === 'librarian') {
            return true;
        }

        // Member can only return their own loans
        return $userId == $loanUserId;
    }

    /**
     * Check if user can view loan details
     */
    public function canViewLoanDetails($userRole, $userId, $loanUserId) {
        // Admin and librarian can view any loan
        if ($userRole === 'admin' || $userRole === 'librarian') {
            return true;
        }

        // Member can only view their own loans
        return $userId == $loanUserId;
    }

    /**
     * Check if user can view member information
     */
    public function canViewMemberInfo($userRole) {
        return $userRole === 'admin' || $userRole === 'librarian';
    }

    /**
     * Check if user can manage reservations
     */
    public function canManageReservations($userRole) {
        return $userRole === 'admin' || $userRole === 'librarian';
    }

    /**
     * Check if user can renew a loan
     */
    public function canRenewLoan($userRole, $userId, $loanUserId) {
        // Admin and librarian can renew any loan
        if ($userRole === 'admin' || $userRole === 'librarian') {
            return true;
        }

        // Member can renew their own loans
        return $userId == $loanUserId;
    }
}