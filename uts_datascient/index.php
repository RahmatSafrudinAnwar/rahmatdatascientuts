<?php
require 'layout.php';
page_start('Dashboard Tugas Data Sains (PHP Native)', 0);

$soal = [
    [1, 'Data Pipeline', 'Validasi, penyaringan, dan laporan status pipeline transaksi penjualan.', 'primary'],
    [2, 'EDA Ekstraksi CSV', 'Membaca CSV dengan fopen() dan fgetcsv(): jumlah baris, kolom, head, tipe data.', 'success'],
    [3, 'Pembersihan Data', 'Mean, imputasi missing value, dan normalisasi Min-Max.', 'warning'],
    [4, 'Statistik Deskriptif', 'Mean, median, modus, standar deviasi, dan diagram batang frekuensi.', 'info'],
    [5, 'Train-Test Split', 'Pembagian data latih 80% dan data uji 20% dengan Fisher-Yates.', 'danger'],
];

echo alert('Pilih salah satu soal di bawah untuk melihat hasilnya.', 'primary');
echo '<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">';
foreach ($soal as [$no, $judul, $desk, $c]) {
    $w = warna($c);
    echo '<div class="bg-white rounded-xl shadow-sm border-t-4 border-' . $w . '-500 p-5 flex flex-col">
<div class="mb-3">' . badge('Soal ' . $no, $c) . '</div>
<h3 class="text-lg font-semibold mb-1">' . h($judul) . '</h3>
<p class="text-slate-500 text-sm mb-4">' . h($desk) . '</p>
<a href="soal' . $no . '.php" class="mt-auto inline-block text-center rounded-lg bg-' . $w . '-600 hover:bg-' . $w . '-700 text-white px-4 py-2 text-sm font-medium">Buka</a></div>';
}
echo '</div>';
page_end();
