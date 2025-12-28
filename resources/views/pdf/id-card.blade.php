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
            margin: 0;
            padding: 0;
            background-color: #fff;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body style="background-image: url('{{ public_path('images/bg-id.png') }}');">
    <div style="height: 430px; width: 100%; text-align: center;">
        <img style="display: block; width: 240px; height: auto; margin-top: 105px;" src="{{ public_path('images/logo.svg') }}" alt="Fight Right Logo">
    </div>
    <table style="width: 100%;">
        <tr>
            <td style="vertical-align: top; padding: 13px 18px;">
                <div style="margin-top: 0px; font-size: 18px;"><b>{{ $member->name }}</b></div>
                <div style="font-family: 'Courier', 'Courier New', monospace; margin-top: 4px; color: #737373;">{{ $member->member_code }}</div>
            </td>
            <td style="text-align: right; vertical-align: top; padding: 5px 3px 15px 3px; background-color: #fff;">
                <img style="display: block; width: 114px;" src="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}" alt="QR Code">
            </td>
        </tr>
        <tr>
            <td style="text-align: left; vertical-align: top; padding: 0px 18px; color: #737373;">
                <div style="font-size: 12px;">Show this ID card before your session starts</div>
            </td>
            <td style="text-align: right; vertical-align: top; padding: 0px 18px; color: #737373;">
                <div style="font-size: 12px;">Scan to view profile & attendance history</div>
            </td>
        </tr>
    </table>
</body>

</html>