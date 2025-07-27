document.addEventListener('DOMContentLoaded', function () {
  const bookButton = document.querySelector('.book-car-btn');

  if (bookButton) {
    bookButton.addEventListener('click', function () {
      const carId = this.dataset.carId;

      if (!confirm("Are you sure you want to book this car?")) return;

      fetch(carBooking.ajax_url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          action: 'book_car',
          car_id: carId,
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          window.location.href = data.redirect_url;
        } else {
          alert('Booking failed: ' + data.message);
          console.error(data);
        }
      })
      .catch(err => {
        console.error('AJAX Error:', err);
      });
    });
  }
});
