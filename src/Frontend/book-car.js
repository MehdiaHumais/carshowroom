jQuery(document).ready(function($) {
    console.log("Booking script loaded ✅");

    $(document).on('click', '.book-this-car-btn', function(e) {
        e.preventDefault();
        const carId = $(this).data('car-id');

        if (!carId) {
            alert('Car ID missing.');
            return;
        }

        if (!confirm('Do you want to book this car?')) return;

        $.ajax({
            url: carBookingData.ajax_url,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'book_this_car',
                car_id: carId,
                _ajax_nonce: carBookingData.nonce
            },
            success: function(res) {
                console.log('AJAX response:', res);
                if (res.success) {
                    alert(res.data.message || 'Car booked.');
                    if (res.data.cart_url) {
                        window.location.href = res.data.cart_url;
                    }
                } else {
                    alert('Booking failed: ' + (typeof res.data === 'string' ? res.data : JSON.stringify(res.data)));
                }
            },
            error: function(xhr) {
                console.error('AJAX error:', xhr.responseText);
                alert('AJAX error occurred.');
            }
        });
    });
});
