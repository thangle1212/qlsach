// Member Dashboard JavaScript

$(document).ready(function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
    
    // Confirm actions
    $('.confirm-action').on('click', function(e) {
        if (!confirm('Bạn có chắc chắn muốn thực hiện thao tác này?')) {
            e.preventDefault();
        }
    });
    
    // Search with debounce
    let searchTimer;
    $('#search-input').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            $('#search-form').submit();
        }, 500);
    });
    
    // Format dates
    $('.format-date').each(function() {
        const date = new Date($(this).text());
        $(this).text(date.toLocaleDateString('vi-VN'));
    });
    
    // Calculate days remaining
    $('.due-date').each(function() {
        const dueDate = new Date($(this).data('due'));
        const today = new Date();
        const diffTime = dueDate - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays > 0) {
            $(this).append(` <span class="badge bg-success">Còn ${diffDays} ngày</span>`);
        } else if (diffDays === 0) {
            $(this).append(` <span class="badge bg-warning">Hôm nay</span>`);
        } else {
            $(this).append(` <span class="badge bg-danger">Quá hạn ${Math.abs(diffDays)} ngày</span>`);
        }
    });
    
    // Book availability check
    $('.check-availability').on('click', function(e) {
        e.preventDefault();
        const bookId = $(this).data('book-id');
        
        $.ajax({
            url: `/api/books/${bookId}/availability`,
            method: 'GET',
            success: function(response) {
                if (response.available) {
                    alert(`Sách còn ${response.count} bản có sẵn`);
                } else {
                    alert('Sách đã hết, vui lòng đặt trước');
                }
            },
            error: function() {
                alert('Không thể kiểm tra tình trạng sách');
            }
        });
    });
    
    // Auto-refresh dashboard every 60 seconds
    if (window.location.pathname === '/member/dashboard') {
        setInterval(function() {
            $.ajax({
                url: '/api/dashboard/stats',
                method: 'GET',
                success: function(stats) {
                    // Update stats cards
                    $('#current-borrowings').text(stats.current_borrowings);
                    $('#near-due').text(stats.near_due);
                    $('#unpaid-fines').text(stats.unpaid_fines.toLocaleString() + ' đ');
                }
            });
        }, 60000);
    }
    
    // Print receipt
    $('.print-receipt').on('click', function() {
        window.print();
    });
    
    // Mobile menu toggle
    $('#mobile-menu-toggle').on('click', function() {
        $('.sidebar-mobile').toggleClass('show');
    });
});

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    let isValid = true;
    
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    // Validate required fields
    $(form).find('[required]').each(function() {
        if (!$(this).val().trim()) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Trường này là bắt buộc</div>');
            isValid = false;
        }
    });
    
    // Email validation
    const emailInput = $(form).find('input[type="email"]');
    if (emailInput.length && emailInput.val()) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.val())) {
            emailInput.addClass('is-invalid');
            emailInput.after('<div class="invalid-feedback">Email không hợp lệ</div>');
            isValid = false;
        }
    }
    
    // Phone validation
    const phoneInput = $(form).find('input[type="tel"]');
    if (phoneInput.length && phoneInput.val()) {
        const phoneRegex = /^[0-9]{10,11}$/;
        if (!phoneRegex.test(phoneInput.val().replace(/\D/g, ''))) {
            phoneInput.addClass('is-invalid');
            phoneInput.after('<div class="invalid-feedback">Số điện thoại không hợp lệ</div>');
            isValid = false;
        }
    }
    
    return isValid;
}

// Book renewal countdown
function updateRenewalCountdown() {
    $('.renewal-countdown').each(function() {
        const renewDate = new Date($(this).data('renew-date'));
        const now = new Date();
        const diffHours = Math.floor((renewDate - now) / (1000 * 60 * 60));
        
        if (diffHours > 0) {
            $(this).text(`Có thể gia hạn sau ${diffHours} giờ`);
        } else {
            $(this).html('<span class="text-success">Có thể gia hạn ngay</span>');
        }
    });
}

// Initialize on page load
updateRenewalCountdown();
setInterval(updateRenewalCountdown, 60000); // Update every minute