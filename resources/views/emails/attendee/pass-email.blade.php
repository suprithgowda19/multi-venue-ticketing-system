<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f3f3f3; padding:20px;">
    <div style="background:white; padding:25px; border-radius:10px; max-width:480px; margin:auto;">
        
        <h2 style="text-align:center;">Your Festival Pass is Ready 🎉</h2>

        <p style="font-size:15px;">
            Hi {{ $attendee->name }}, <br><br>
            Thank you for registering! Your festival pass is attached as a PDF.
        </p>

        <p style="font-size:15px;">
            <strong>Pass ID:</strong> {{ $attendee->pass_id }}<br>
            <strong>Category:</strong> {{ $attendee->category }}
        </p>

        <p style="font-size:14px; color:#777;">
            Please bring this pass (digital or printed) to the venue.
        </p>

    </div>
</body>
</html>
