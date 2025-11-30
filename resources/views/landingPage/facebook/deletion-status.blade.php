<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Deletion Status - AgriSys</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }
        .container {
            background: #f8f9fa;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #0A6953; }
        .code {
            background: #fff;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-family: monospace;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✓ Data Deletion Request Received</h1>
        <p>Your data has been successfully deleted from AgriSys.</p>
        
        @if($confirmation_code)
        <div class="code">
            <strong>Confirmation Code:</strong><br>
            {{ $confirmation_code }}
        </div>
        @endif
        
        <p><small>If you have any questions, please contact us at agriculture@sanpedro.gov.ph</small></p>
    </div>
</body>
</html>