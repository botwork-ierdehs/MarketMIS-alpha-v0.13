@component('mail::message')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f7f8fc;
            color: #51545e;
            line-height: 1.6;
        }
        
        .email-wrapper {
            width: 100%;
            margin: 0;
            padding: 20px 0;
            background-color: #f7f8fc;
        }
        
        .email-content {
            max-width: 570px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-header h1 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
        }
        
        .message {
            font-size: 16px;
            margin-bottom: 30px;
            color: #4a5568;
        }
        
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #0A45B9 0%, #5e9bf8 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .expiry-notice {
            background: #f7fafc;
            border-left: 4px solid #4299e1;
            padding: 16px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .expiry-notice p {
            margin: 0;
            font-size: 14px;
            color: #2d3748;
        }
        
        .security-notice {
            font-size: 14px;
            color: #718096;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .email-footer {
            background: #f7fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-text {
            font-size: 14px;
            color: #718096;
            margin-bottom: 15px;
        }
        
        .footer-link {
            color: #4299e1;
            word-break: break-all;
            text-decoration: none;
        }
        
        .footer-link:hover {
            text-decoration: underline;
        }
        
        .copyright {
            font-size: 12px;
            color: #a0aec0;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        @media (max-width: 600px) {
            .email-content {
                margin: 0 10px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .email-body {
                padding: 30px 20px;
            }
            
            .email-footer {
                padding: 20px;
            }
            
            .reset-button {
                padding: 14px 28px;
                font-size: 15px;
            }
        }

        h1 {
            text-align: center;
            font-weight: bold;
            color: #0A45B9;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                @if(file_exists(public_path('images/market-MIS-logo.png')))
                    <img src="{{ asset('images/market-MIS-logo.png') }}" alt="{{ config('app.name') }} Logo" class="logo">
                @endif
                <h1>Verify Your Email Address</h1>
            </div>
            
            <div class="email-body">
                <div class="greeting">
                    Hello!
                </div>
                
                <div class="message">
                    Please click the button below to verify your email address and activate your account.
                </div>
                
                <div class="button-container">
                    <a href="{{ $url }}" class="reset-button">
                        Verify Email
                    </a>
                </div>
                
                <div class="expiry-notice">
                    <p>
                        <strong>⏰ Time Sensitive:</strong> This password reset link will expire in {{ $expire }} minutes for security reasons.
                    </p>
                </div>
                
                <div class="security-notice">
                    <p><strong>Didn't request this?</strong> If you did not create an account, no further action is required.</p>
                    <p style="margin-top: 10px;"><strong>Security tip:</strong> If you continue receiving these emails, please contact our support team.</p>
                </div>
            </div>
            
            <div class="email-footer">
                <p class="footer-text">
                    Having trouble with the button? Copy and paste this link into your browser:
                </p>
                <a href="{{ $url }}" class="footer-link">{{ $url }}</a>
                
                <div class="copyright">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                    <p>This email was sent to {{ $user->email }} because a new account created.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>