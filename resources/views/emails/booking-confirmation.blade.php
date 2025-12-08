<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Booking Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif;">

    <h2>Booking Confirmed!</h2>

    <p>Dear Attendee,</p>

    <p>Your booking has been successfully confirmed.</p>

    <p><strong>Booking ID:</strong> {{ $booking->id }}</p>

    <p><strong>Movie:</strong> {{ $booking->assignment->movie }}</p>
    <p><strong>Screen:</strong> {{ $booking->assignment->screen->name }}</p>
    <p><strong>Time:</strong> {{ $booking->assignment->slot->start_time }}</p>

    <p><strong>Seats:</strong>
        @foreach($booking->seats as $seat)
            {{ $seat->seat->seat_code }}@if(!$loop->last), @endif
        @endforeach
    </p>

    <p>Thank you for booking with us!</p>

</body>
</html>
