<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Ticket #{{ $booking->id }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 20px;
            font-size: 14px;
            color: #333;
        }

        .ticket {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }

        .info p {
            margin: 4px 0;
        }

        .qr {
            margin-top: 20px;
            text-align: center;
        }

        .qr img {
            width: 180px;
            height: 180px;
        }
    </style>
</head>

<body>

<div class="ticket">

    <div class="header">
        <h2>Movie Ticket</h2>
        <p>Booking ID: <strong>{{ $booking->id }}</strong></p>
    </div>

    <div class="section-title">Show Details</div>
    <div class="info">
        <p><strong>Movie:</strong> {{ $booking->assignment->movie }}</p>
        <p><strong>Venue:</strong> {{ $booking->assignment->venue->name }}</p>
        <p><strong>Screen:</strong> {{ $booking->assignment->screen->name }}</p>
        <p><strong>Show Time:</strong> {{ $booking->assignment->slot->start_time }}</p>
        <p><strong>Day:</strong> {{ $booking->assignment->day }}</p>
    </div>

    <div class="section-title">Seats</div>
    <div class="info">
        <p>
            @foreach($booking->seats as $bs)
                {{ $bs->seat->seat_code }}@if(!$loop->last), @endif
            @endforeach
        </p>
    </div>

    <div class="section-title">Entry QR Code</div>
    <div class="qr">
        <img src="{{ public_path('storage/' . $booking->qr_path) }}" alt="QR Code">
        <p style="margin-top:10px; font-size:12px;">Show this QR at entry</p>
    </div>

</div>

</body>
</html>
