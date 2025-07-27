<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f2f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .email-wrapper {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background-color: #2949FA;
            padding: 30px;
            text-align: center;
        }

        .header img {
            max-width: 140px;
        }

        .body {
            padding: 40px 30px;
            text-align: center;
        }

        .body h1 {
            font-size: 26px;
            color: #162946;
            margin-bottom: 20px;
        }

        .body p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }

        .otp-code {
            display: inline-block;
            font-size: 28px;
            font-weight: bold;
            color: #0417B9;
            background-color: #5A6BFB;
            padding: 14px 24px;
            border-radius: 50px;
            margin: 30px 0;
            letter-spacing: 4px;
        }

        .footer {
            font-size: 15px;
            text-align: center;
            padding: 20px;
            color: #000;
            background-color: #f9f9f9;
        }

        .footer a {
            color: #2964FA;
            text-decoration: none;
        }

        @media only screen and (max-width: 620px) {
            .body {
                padding: 30px 20px;
            }

            .body h1 {
                font-size: 22px;
            }

            .otp-code {
                font-size: 24px;
                padding: 12px 20px;
                letter-spacing: 3px;
            }
        }
    </style>
</head>

<body>
    <table role="presentation">
        <tr>
            <td align="center">
                <table class="email-wrapper" role="presentation">
                    <tr>
                        <td class="header">
                            <img src="https://cqrlbirnwzidgerehsdq.supabase.co/storage/v1/object/public/waslny/storage/v1/s3/waslny/waslny/system/Gm63IEBgl6QFmnPUhgBGb6dT0R6TuBU9i9PM32VK.png" alt="Waslny Logo" aria-label="Waslny Logo">
                        </td>
                    </tr>
                    <tr>
                        <td class="body">
                            <h1>Hello {{ $user->first_name ?? 'Admin' }},</h1>
                            <p>Your One-Time Password (OTP) is below:</p>
                            <div class="otp-code">{{ $user->two_factor_code }}</div>
                            <p>This code will expire in 5 minutes.</p>
                            <p>If you didn’t request this code, you can safely ignore this email.</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p>Thanks,<br>Waslny Team</p>
                            <p><a href="#">Privacy Policy</a> • <a href="#">Terms of Service</a></p>
                            <p>&copy; 2025 Waslny company. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>