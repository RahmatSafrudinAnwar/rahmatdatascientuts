<?php
require 'layout.php';

$raw = [
    ['id_transaksi'=>'TRX001','tanggal'=>'2026-10-01','produk'=>'Laptop','jumlah'=>2,'harga'=>8500000],
    ['id_transaksi'=>'TRX002','tanggal'=>'2026-10-02','produk'=>'Mouse','jumlah'=>10,'harga'=>75000],
    ['id_transaksi'=>'TRX003','tanggal'=>'2026-10-02','produk'=>'','jumlah'=>1,'harga'=>150000],
    ['id_transaksi'=>'TRX004','tanggal'=>'2026-13-45','produk'=>'Monitor','jumlah'=>3,'harga'=>2000000],
    ['id_transaksi'=>'TRX005','tanggal'=>'2026-10-04','produk'=>'Keyboard','jumlah'=>-5,'harga'=>250000],
    ['id_transaksi'=>'TRX006','tanggal'=>'2026-10-05','produk'=>'Headset','jumlah'=>4,'harga'=>'murah'],
    ['id_transaksi'=>'TRX007','tanggal'=>'2026-10-06','produk'=>'Webcam','jumlah'=>5,'harga'=>450000],
    ['id_transaksi'=>'TRX008','tanggal'=>'2026-10-07','produk'=>'Printer'],
    ['id_transaksi'=>'TRX009','tanggal'=>'2026-10-08','produk'=>'Flashdisk','jumlah'=>20,'harga'=>90000],
    ['id_transaksi'=>'TRX010','tanggal'=>'2026-10-09','produk'=>'Tablet','jumlah'=>1,'harga'=>4200000],
];

function validasi(array $b): array {
    $e = [];
    foreach (['id_transaksi','tanggal','produk','jumlah','harga'] as $k)
        if (!array_key_exists($k, $b)) $e[] = "kolom '$k' tidak ada";
    if ($e) return $e;
    if (trim($b['produk']) === '') $e[] = 'produk kosong';
    $t = explode('-', $b['tanggal']);
    if (count($t) !== 3 || !checkdate((int)$t[1], (int)$t[2], (int)$t[0])) $e[] = 'tanggal tidak valid';
    if (!is_int($b['jumlah']) || $b['jumlah'] <= 0) $e[] = 'jumlah harus bilangan bulat > 0';
    if (!is_numeric($b['harga']) || $b['harga'] <= 0) $e[] = 'harga harus numerik > 0';
    return $e;
}

$valid = $invalid = $siap = [];
foreach ($raw as $i => $b) {
    $err = validasi($b);
    if ($err) $invalid[] = [$i + 1, $b['id_transaksi'] ?? '-', implode('; ', $err)];
    else $valid[] = $b;
}
foreach ($valid as $b) {
    $total = $b['jumlah'] * $b['harga'];
    $siap[] = [$b['id_transaksi'], $b['tanggal'], ucwords(strtolower(trim($b['produk']))), $b['jumlah'],
        number_format($b['harga'], 0, ',', '.'), number_format($total, 0, ',', '.'),
        $total >= 5000000 ? 'Besar' : ($total >= 1000000 ? 'Menengah' : 'Kecil')];
}

page_start('1. Data Pipeline: Sales Transactions', 1);
grid_start();
stat_card('Raw', count($raw), 'secondary');
stat_card('Valid', count($valid), 'success');
stat_card('Rejected', count($invalid), 'danger');
stat_card('Ready for analysis', count($siap), 'primary');
grid_end();

card_start('Pipeline status');
$stages = [['Raw data', 'Received', 'secondary'], ['Validation', 'Passed ' . count($valid) . ' / ' . count($raw), 'success'],
           ['Processing', count($siap) . ' rows transformed', 'info'], ['Ready for analysis', count($siap) ? 'READY' : 'FAILED', count($siap) ? 'primary' : 'danger']];
echo '<div class="flex flex-wrap gap-2">'; foreach ($stages as $s) echo badge($s[0] . ': ' . $s[1], $s[2]); echo '</div>';
card_end();

card_start('Rejected rows');
render_table(['Row', 'ID', 'Reason'], $invalid);
card_end();

card_start('Data ready for analysis');
render_table(['ID', 'Date', 'Product', 'Qty', 'Price', 'Total', 'Category'], $siap);
card_end();
page_end();
