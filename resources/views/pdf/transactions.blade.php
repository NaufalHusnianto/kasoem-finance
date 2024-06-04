<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $transaction->number }}</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
        }

        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* background: grey; */
        }

        .wrapper {
            /* width: 424px; */
            /* height: 600px; */
            position: relative;
            padding: 4rem 4rem;
        }

        .border-design {
            display: flex;
            width: 100%;
            justify-content: flex-end;
        }

        .border-design .c1,
        .border-design .c2,
        .border-design .c3,
        .border-design .c4,
        .border-design .c5 {
            width: 30px;
            height: 10px;
        }

        .c1 { background: gold; }
        .c2 { background: red; }
        .c3 { background: purple; }
        .c4 { background: blue; }
        .c5 { background: cyan; }

        .border-design.top {
            position: absolute;
            top: 0;
            right: 0;
        }

        .border-design.bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            flex-direction: row-reverse;
        }

        .invoice-header {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        .logo {
            text-transform: uppercase;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -1px;
        }

        .logo span {
            font-weight: 400;
        }

        .title {
            text-transform: uppercase;
            font-size: 2rem;
            font-weight: 600;
            text-align: right;
        }

        .inv-number, .inv-date {
            display: flex;
            justify-content: space-between;
        }

        .inv-number {
            padding: 10px 45px 0 0;
        }

        .inv-date {
            padding: 10px 45px 0 0;
        }

        .inv-number h3, .inv-date h3 {
            font-weight: 700;
            font-size: 1rem;
        }

        .inv-number h4, .inv-date h4 {
            font-size: 1rem;
            font-weight: 500;
        }

        .billing-detail {
            margin-top: 15px;
        }

        .billing-detail p:nth-child(1),
        .billing-detail p:nth-child(2) {
            text-transform: uppercase;
        }

        .billing-detail p:nth-child(1) {
            font-size: .8rem;
        }

        .billing-detail p:nth-child(2) {
            font-size: 1rem;
            font-weight: 700;
            width: 150px;
            border-bottom: 1px solid black;
            margin-bottom: 5px;
        }

        .billing-detail p:nth-child(3),
        .billing-detail p:nth-child(4),
        .billing-detail p:nth-child(5) {
            font-size: 1rem;
        }

        .billing-detail p span {
            font-weight: 600;
        }

        table {
            border-collapse: collapse;
            font-size: 1rem;
            width: 100%;
            margin-top: 20px;
        }

        table thead tr td {
            text-align: center;
            font-weight: 600;
            padding: 8px;
        }

        table tbody td {
            padding: 7px 5px;
            text-align: center;
        }

        .l-col {
            text-align: left;
        }

        .r-col {
            text-align: right;
        }

        table tbody tr:nth-child(odd) {
            background: #f0f0f0;
        }

        table tbody tr:nth-child(even) {
            background: #fcfcfc;
            border-bottom: 1px solid #b4b4b4;
            border-top: 1px solid #b4b4b4;
        }

        .total-section {
            position: absolute;
            right: 40px;
            font-size: 1rem;
            width: 160px;
            margin-top: 5px;
            padding-right: 5px;
        }

        .total {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            padding-top: 5px;
        }

        .total p {
            font-weight: 600;
        }

        .payment-terms {
            position: absolute;
            bottom: 30px;
            width: 45%;
        }

        .payment-detail {
            margin-bottom: 8px;
        }

        .payment-detail p:nth-child(1) {
            font-size: 1rem;
            font-weight: 700;
            width: 150px;
        }

        .payment-detail p:nth-child(2),
        .payment-detail p:nth-child(3),
        .payment-detail p:nth-child(4) {
            font-size: 1rem;
        }

        .payment-detail p span {
            font-weight: 600;
        }

        .terms p:nth-child(1) {
            font-size: 1rem;
            font-weight: 700;
            width: 150px;
        }

        .terms {
            margin-bottom: 20px;
        }

        .terms p:nth-child(2) {
            font-size: 1rem;
        }

        .message p {
            font-size: 1rem;
            font-weight: 700;
        }

        .signature p {
            font-size: 1rem;
            font-weight: 700;
            position: absolute;
            right: 40px;
            bottom: 30px;
            border-top: 1px solid black;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="border-design top">
            <div class="c1"></div>
            <div class="c2"></div>
            <div class="c3"></div>
            <div class="c4"></div>
            <div class="c5"></div>
        </div>

        <div class="invoice-header">
            <div class="logo">A. Kasoem <span>Optical</span></div>
            <div class="title">Invoice</div>
            <div class="inv-number">
                <h3>Invoice #</h3>
                <h4>{{ $transaction->number }}</h4>
            </div>
            <div class="inv-date">
                <h3>Date</h3>
                <h4>{{ $transaction->created_at }}</h4>
            </div>     
        </div>

        <div class="billing-detail">
            <p>Billing to</p>
            <p>{{ $transaction->customer->name }}</p>
            <p><span>Telp.</span> {{ $transaction->customer->phone }}</p>
            <p><span>Alamat:</span> {{ $transaction->customer->address }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <td>No.</td><td>Deskripsi</td><td>Jumlah</td><td>Harga</td><td>Total Harga</td>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $index => $item)
                <tr><td>{{ $index + 1 }}</td><td class="1-col">{{ $item->product->name }}</td><td>{{ $item->quantity }}</td><td>Rp.{{ $item->unit_price }}</td><td>Rp.{{ $item->unit_price * $item->quantity }}</td></tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">        
            <div class="total">
                <p>Jumlah Total</p>
                <p style="font-weight: lighter">Rp.{{ $transaction->amount }}</p>
            </div>
            <div class="total">
                <p>Nominal dibayar</p>
                <p style="font-weight: lighter">Rp.{{ $transaction->pay_amount }}</p>
            </div>
            <div class="total">
                <p>kembalian</p>
                <p style="font-weight: lighter">Rp.{{ $transaction->cash_change }}</p>
            </div>
        </div>

        <div class="payment-terms">
            <div class="payment-detail">
                <p>Payment Info</p>
                <p>Account #<span></span> {{ $transaction->user_id }}</p>
                <p>A/c Name<span></span> {{ $transaction->user->name }}</p>
            </div>
            <div class="terms">
                <p>Terms & Conditions</p>
                <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
            </div>
            <div class="message">
                <p>Thank you for your order</p>
            </div>
        </div>

        <div class="signature">
            <p>Authorized Signature</p>
        </div>

        <div class="border-design bottom">
            <div class="c1"></div>
            <div class="c2"></div>
            <div class="c3"></div>
            <div class="c4"></div>
            <div class="c5"></div>
        </div>
    </div>
</body>
</html>
