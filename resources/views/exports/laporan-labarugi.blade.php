<!DOCTYPE html>
<html>
<head>
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 14px; color: #555; }
        table { w-full: 100%; width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f5; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .bg-gray { background-color: #f4f4f5; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo Ayam Geprek Rejo" style="height: 80px; object-fit: contain; margin-bottom: 10px;">
        <div class="title">Laporan Laba Rugi - Ayam Geprek Rejo</div>
        <div class="subtitle">Periode: {{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Komponen</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2" class="bg-gray font-bold">PENDAPATAN</td>
            </tr>
            <tr>
                <td>Penjualan Kotor (Total Order)</td>
                <td class="text-right">Rp {{ number_format($report['total_omset'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Diskon Voucher</td>
                <td class="text-right text-red">- Rp {{ number_format($report['total_discount'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Redeem Poin</td>
                <td class="text-right text-red">- Rp {{ number_format($report['total_points_redeemed'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="font-bold">Pendapatan Bersih</td>
                <td class="text-right font-bold">Rp {{ number_format($report['net_revenue'] ?? 0, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2" class="bg-gray font-bold">HARGA POKOK PENJUALAN (HPP)</td>
            </tr>
            <tr>
                <td>Total HPP Terjual</td>
                <td class="text-right text-red">- Rp {{ number_format($report['total_hpp'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="font-bold">Laba Kotor (Gross Profit)</td>
                <td class="text-right font-bold text-green">Rp {{ number_format($report['gross_profit'] ?? 0, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2" class="bg-gray font-bold">BIAYA OPERASIONAL (PENGELUARAN)</td>
            </tr>
            @php $totalOp = 0; @endphp
            @if(isset($report['expenses_by_category']))
                @foreach($report['expenses_by_category'] as $cat => $val)
                    <tr>
                        <td>{{ ucwords(str_replace('_', ' ', $cat)) }}</td>
                        <td class="text-right text-red">- Rp {{ number_format($val, 0, ',', '.') }}</td>
                    </tr>
                    @php $totalOp += $val; @endphp
                @endforeach
            @endif
            <tr>
                <td class="font-bold">Total Biaya Operasional</td>
                <td class="text-right font-bold text-red">- Rp {{ number_format($totalOp, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2" class="bg-gray font-bold">LABA BERSIH (NET PROFIT)</td>
            </tr>
            <tr>
                <td class="font-bold" style="font-size: 14px;">Total Laba Bersih</td>
                <td class="text-right font-bold {{ ($report['net_profit'] ?? 0) >= 0 ? 'text-green' : 'text-red' }}" style="font-size: 14px;">
                    Rp {{ number_format($report['net_profit'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    @if(!empty($topItems))
    <div style="margin-top: 30px;">
        <div class="subtitle font-bold" style="margin-bottom: 10px;">Menu Terlaris (Top 10)</div>
        <table>
            <thead>
                <tr>
                    <th>Nama Menu</th>
                    <th class="text-right">Qty Terjual</th>
                    <th class="text-right">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topItems as $item)
                <tr>
                    <td>{{ $item['menu_item_name'] ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item['total_qty'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item['total_revenue'] ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
</html>
