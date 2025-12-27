<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Member ID Card</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }

        .header {
            margin-top: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #E11D48;
            padding-bottom: 10px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #E11D48;
            /* Red-600 */
        }

        .title {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 5px;
        }

        .member-info {
            margin: 30px 0;
        }

        .member-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #111;
        }

        .member-code {
            font-family: monospace;
            font-size: 14px;
            color: #555;
            background: #f3f4f6;
            padding: 6px 12px;
            border-radius: 4px;
            display: inline-block;
        }

        .qr-code {
            margin: 30px 0;
            text-align: center;
        }

        .qr-code img {
            width: 180px;
            height: 180px;
        }

        .footer {
            font-size: 10px;
            color: #888;
            position: absolute;
            bottom: 30px;
            width: 100%;
            left: 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">FIGHT RIGHT</div>
        <div class="title">Member ID Card</div>
    </div>

    <div class="member-info">
        <div class="member-name">{{ $member->name }}</div>
        <div class="member-code">{{ $member->member_code }}</div>
    </div>

    <div class="qr-code">
        <img src="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}" alt="QR Code">
    </div>

    <div class="footer">
        Scan to view profile<br>
        fightright.app
    </div>
</body>

</html>