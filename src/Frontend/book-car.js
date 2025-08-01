jQuery(document).ready(function($) {
    $('.book-this-car-btn').on('click', function(e) {
        e.preventDefault();
        const carId = $(this).data('car-id');
        if (!carId) return;

        if (!confirm('Do you want to book this car?')) return;

        $.post(carBookingData.ajax_url, {
            action: 'book_this_car',
            car_id: carId,
            _ajax_nonce: carBookingData.nonce
        }, function(res) {
            if (res.success) {
                alert(res.data.message);
                // Redirect admin/user to order edit page (admin view)
                if (res.data.order_admin_url) {
                    window.location.href = res.data.order_admin_url;
                }
            } else {
                alert('Booking failed: ' + (res.data || 'Unknown error'));
            }
        });
    });
});
