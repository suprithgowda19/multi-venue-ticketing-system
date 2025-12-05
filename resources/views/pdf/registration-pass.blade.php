<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Festival Pass</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .pass-container {
            width: 100%;
            max-width: 480px;
            margin: auto;
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .header {
            text-align: center;
            margin-bottom: 18px;
        }
        .header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        .qr-box {
            text-align: center;
            margin: 20px 0;
        }
        .qr-box img {
            width: 180px;
            height: 180px;
            border-radius: 10px;
            border: 2px solid #ddd;
        }
        .details {
            background: #fafafa;
            border-radius: 12px;
            padding: 14px 18px;
            border: 1px solid #e5e5e5;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .label {
            font-weight: bold;
            color: #444;
        }
        .value {
            color: #222;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 16px;
        }
    </style>
</head>
<body>

<div class="pass-container">

    <div class="header">
        <h2>🎫 Festival Registration Pass</h2>
    </div>

    <div class="qr-box">
        <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code">
    </div>

    <div class="details">

        <div class="details-row">
            <span class="label">Name:</span>
            <span class="value">{{ $attendee->name }}</span>
        </div>

        <div class="details-row">
            <span class="label">Email:</span>
            <span class="value">{{ $attendee->email }}</span>
        </div>

        <div class="details-row">
            <span class="label">Mobile:</span>
            <span class="value">{{ $attendee->mobile }}</span>
        </div>

        <div class="details-row">
            <span class="label">Category:</span>
            <span class="value">{{ $attendee->category }}</span>
        </div>

        <div class="details-row">
            <span class="label">Pass ID:</span>
            <span class="value" style="font-weight:bold;">{{ $attendee->pass_id }}</span>
        </div>

    </div>

    <div class="footer">
        Please show this pass (digital or printed) at the festival entry gate.
    </div>

</div>

</body>
</html>
