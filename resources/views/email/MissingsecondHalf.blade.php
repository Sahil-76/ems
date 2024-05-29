<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>{{ $subject }}</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6;">
    <table style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #F5F5F5; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px;">
                <p style="margin-bottom: 20px;">Hello <strong>{{ $user->name }}</strong>,</p>
                    <p style="margin-bottom: 20px;">System has observed that, {{ $employee->name }} ({{ $employee->biometric_id }}) is absent for <strong>{{ $date }}</strong>.
                    <p style="margin-bottom: 20px;"> Therefore, we kindly request that you investigate this matter promptly and take any necessary steps to address it.</p>
            
                <p style="margin-bottom: 20px;">Thank you for your attention to this matter.</p>
            
                <p style="margin-bottom: 0;">Regards,</p>
                <p style="margin-bottom: 20px;">EMS</p>
            </td>
            
        </tr>
    </table>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
