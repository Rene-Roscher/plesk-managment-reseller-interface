<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>A simple, clean, and responsive HTML invoice template</title>

    <style>
        .invoice-box {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            YourMusicBot
                            {{--<img src="https://prohosting24.de/img/logo/logo2.png" style="width:100%; max-width:300px;">--}}
                        </td>

                        <td>
                            <h1>Rechnung</h1><br>
                            <span size="16">ID: {{ date('Ym').\Illuminate\Support\Facades\Auth::user()->id }}</span><br>
                            <span size="16">Erstellt am: {{ date('d-m-Y') }}</span><br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            {{ \Illuminate\Support\Facades\Auth::user()->name }}<br>
                            {STREET} {NUMBER}<br>
                            {PLZ} {PLACE}<br>
                            {LAND}
                        </td>

                        <td>
                            YourMusicBot<br>
                            Christopher Sakel<br>
                            accounting@yourmusicbot.eu
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="heading">
            <td>ID</td>
            <td></td>

            <td>
                Check #
            </td>
        </tr>

        <tr class="details">
            <td>
                Check
            </td>

            <td>
                1000
            </td>
        </tr>

        <tr class="heading">
            <td>
                Item
            </td>

            <td>
                Price
            </td>
        </tr>

        <tr class="item">
            <td>
                Website design
            </td>

            <td>
                $300.00
            </td>
        </tr>

        <tr class="item">
            <td>
                Hosting (3 months)
            </td>

            <td>
                $75.00
            </td>
        </tr>

        <tr class="item last">
            <td>
                Domain name (1 year)
            </td>

            <td>
                $10.00
            </td>
        </tr>

        <tr class="total">
            <td></td>

            <td>
                Total: $385.00
            </td>
        </tr>

    </table>
</div>
</body>
</html>
adsdaasdasd