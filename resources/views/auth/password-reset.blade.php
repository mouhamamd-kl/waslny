<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
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
            background-color: #04473a;
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

        .cta-button {
            display: inline-block;
            margin: 30px auto;
            background-color: #04473a;
            color: white !important;
            text-decoration: none;
            padding: 18px 32px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .cta-button:hover {
            background-color: #18996e;
        }

        .footer {
            font-size: 15px;
            text-align: center;
            padding: 20px;
            color: #000;
            background-color: #f9f9f9;
        }

        .footer a {
            color: #20b486;
            text-decoration: none;
        }

        @media only screen and (max-width: 620px) {
            .body {
                padding: 30px 20px;
            }

            .body h1 {
                font-size: 22px;
            }

            .cta-button {
                font-size: 15px;
                padding: 16px 28px;
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
                            <img src="https://laravel-test-bucket-8642.s3.ap-southeast-2.amazonaws.com/profile-pictures/white_logo.png"
                                alt="Astra Logo" aria-label="Astra Logo">
                        </td>
                    </tr>
                    <tr>
                        <td class="body">
                            <h1>Hello {{ $user->first_name ?? $user->name ?? 'User' }},</h1>
                            <p>We received a request to reset your Astra account password.</p>
                            <a href="{{ $resetUrl }}" class="cta-button" role="button" aria-label="Reset Password">
                                Reset Password
                            </a>
                            <p>If the button doesn't work, paste this link into your browser:</p>
                            <p style="word-break: break-all;">
                                <a href="{{ $resetUrl }}" style="color: #20b486;">{{ $resetUrl }}</a>
                            </p>
                            <p>This link will expire in {{ $expireTime }} minutes.</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p>If you didn't request this password reset, you can safely ignore this email.</p>
                            <p><a href="#">Privacy Policy</a> • <a href="#">Terms of Service</a></p>
                            <p>&copy; {{ date('Y') }} Astra Real Estate. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>