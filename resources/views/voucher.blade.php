<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de venta</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        h1 {
            margin-bottom: 0rem;
            margin-top: 0rem;
            font-size: 2rem;
        }

        nav>h2,
        nav>p {
            text-align: center;
            margin: 0rem;
        }

        nav>img {
            max-width: 15%;
            max-height: 15%;
        }

        header>div {
            width: 42%;
            margin: .25rem;
        }

        header {
            padding-top: 1.25rem;
        }

        header h3 {
            color: black;
            font-weight: bold;
            margin-bottom: .2rem;
            margin-top: 0;
        }

        header p {
            color: #2c2c2c;
            margin: 0;
        }

        th {
            text-align: left;
            border-bottom: 1px solid black;
        }

        th,
        td {
            margin-left: .25rem;
            margin-right: .25rem;
            padding: .25rem
        }

        tbody>tr:last-child>td {
            border-top: 1px solid black;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center; margin-bottom: .25rem">Cevichería Capitán Chino</h2>

    <h2>{{ $order->client }} ({{ $order->phone }})</h2>
    <nav style="margin-top: 1.5rem">
        <p>Número del pedido: {{ $order->order_id }}</p>
    </nav>
    <main style="margin-top: 5.5rem">
        <table style="width:100%; max-width:100%;">
            <thead>
                <tr>
                    <th style="text-align: right">Cant.</th>
                    <th>Producto</th>
                    <th style="text-align: right">Precio Unitario</th>
                    <th style="text-align: right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td style="text-align: right">{{ $item->quantity }}</td>
                        <td>
                            {{ $item->item->name }}
                            @if ($item->note != '')
                                <span style="font-style: italic; color: green">({{ $item->note }})</span>
                            @endif
                        </td>
                        <td style="text-align: right">{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td style="text-align: right">
                            {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td style="font-weight: bold">{{ count($order->items) }}</td>
                    <td style="font-weight: bold"></td>
                    <td style="font-weight: bold; text-align: right">TOTAL</td>
                    <td style="font-weight: bold; text-align: right">
                        {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </main>
</body>

</html>
