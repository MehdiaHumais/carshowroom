jQuery(document).ready(function ($) {
    $('.book-this-car-btn').on('click', function (e) {
        e.preventDefault();

        const carId = $(this).data('car-id');
        if (!carId) return;

        if (!confirm('Do you want to book this car?')) return;

        $.ajax({
            url: carBookingData.ajax_url,
            method: 'POST',
            data: {
                action: 'book_this_car',
                car_id: carId,
                _ajax_nonce: carBookingData.nonce
            },
            success: function (res) {
                if (res.success) {
                    window.location.href = res.data.cart_url;
                } else {
                    alert('Booking failed: ' + res.data);
                }
            },
            error: function () {
                alert('AJAX error.');
            }
        });
    });
});
