<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f7f7f7; padding:20px;">
    <div style="background:white; padding:25px; border-radius:10px; max-width:480px; margin:auto;">

        <h2 style="text-align:center;">Your OTP Code</h2>

        <p style="font-size:15px;">
            Use the following OTP to complete your {{ ucfirst($purpose) }} process:
        </p>

        <div style="text-align:center; margin:20px 0;">
            <span style="font-size:32px; letter-spacing:6px; font-weight:bold;">
                {{ $otp }}
            </span>
        </div>

        <p style="font-size:14px; color:#555;">
            This OTP is valid for 3 minutes. Do not share it with anyone.
        </p>

    </div>
</body>
</html>
