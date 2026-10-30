<?php
require 'layout.php';

$file = __DIR__ . '/dataset_mahasiswa.csv';
page_start('2. Pengambilan & Eksplorasi Awal Dataset (EDA CSV)', 2);

$fh = is_readable($file) ? fopen($file, 'r') : false;
$first = $fh ? fgets($fh) : false;
if ($first === false) {
    echo alert('File dataset_mahasiswa.csv tidak ditemukan atau kosong. Letakkan di folder yang sama dengan soal2.php.', 'danger');
    page_end(); exit;
}

// Deteksi pemisah (, ; tab |) dan buang BOM bila ada
$first = preg_replace('/^\xEF\xBB\xBF/', '', $first);
$delim = ','; $best = 0;
foreach ([',', ';', "\t", '|'] as $d) {
    $c = substr_count($first, $d);
    if ($c > $best) { $best = $c; $delim = $d; }
}
rewind($fh);
if (fread($fh, 3) !== "\xEF\xBB\xBF") rewind($fh);

// Baca dengan fgetcsv()
$kolom = array_map('trim', fgetcsv($fh, 0, $delim));
$data = []; $tidakLengkap = 0;
while (($r = fgetcsv($fh, 0, $delim)) !== false) {
    if ($r === [null]) continue;                       // lewati baris kosong
    if (count($r) !== count($kolom)) $tidakLengkap++;  // jumlah kolom tidak sesuai header
    $data[] = array_pad(array_slice(array_map('trim', $r), 0, count($kolom)), count($kolom), '');
}
fclose($fh);

// Analisis tipe data per kolom
function analisis(array $vals): array {
    $int = $num = true; $kosong = 0; $angka = [];
    foreach ($vals as $v) {
        if ($v === '') { $kosong++; continue; }
        if (!is_numeric($v))                  { $num = $int = false; }
        else { $angka[] = $v + 0; if (!preg_match('/^-?\d+$/', $v)) $int = false; }
    }
    $terisi = count($vals) - $kosong;
    $tipe = $terisi === 0 ? 'unknown' : ($int ? 'integer' : ($num ? 'float' : 'string'));
    return [
        'tipe' => $tipe, 'terisi' => $terisi, 'kosong' => $kosong,
        'unik' => count(array_unique(array_filter($vals, fn($v) => $v !== ''))),
        'min' => ($num && $angka) ? min($angka) : '-',
        'max' => ($num && $angka) ? max($angka) : '-',
        'mean' => ($num && $angka) ? number_format(array_sum($angka) / count($angka), 2) : '-',
    ];
}

$tipeRows = []; $totalMissing = 0;
foreach ($kolom as $i => $k) {
    $vals = array_column($data, $i);
    $a = analisis($vals);
    $totalMissing += $a['kosong'];
    $contoh = '-';
    foreach ($vals as $v) if ($v !== '') { $contoh = $v; break; }
    $tipeRows[] = [$k, $a['tipe'], $a['terisi'], $a['kosong'], $a['unik'], $a['min'], $a['max'], $a['mean'], $contoh];
}

// ---------- 1. Jumlah total baris / rekaman ----------
echo '<h2 class="text-lg font-semibold mb-3">1. Jumlah total baris/rekaman data</h2>';
grid_start();
stat_card('Total rekaman (data)', count($data));
stat_card('Total baris di file (+header)', count($data) + 1, 'secondary');
stat_card('Jumlah kolom', count($kolom), 'success');
stat_card('Total missing value', $totalMissing, $totalMissing ? 'danger' : 'info');
grid_end();
if ($tidakLengkap) echo alert("Ada $tidakLengkap baris yang jumlah kolomnya tidak sesuai header (sudah dirapikan).", 'warning');

// ---------- 2. Daftar nama kolom ----------
card_start('2. Daftar nama kolom yang tersedia');
echo '<div class="flex flex-wrap gap-2">';
foreach ($kolom as $i => $k) echo badge(($i + 1) . '. ' . $k, 'primary');
echo '</div><p class="text-sm text-slate-500 mt-3">Pemisah (delimiter) terdeteksi: <code class="bg-slate-100 px-1 rounded">' . h($delim === "\t" ? 'TAB' : $delim) . '</code></p>';
card_end();

// ---------- 3. Pratinjau 5 baris pertama ----------
card_start('3. Pratinjau 5 baris pertama data (head)');
render_table($kolom, array_slice($data, 0, 5), 'simple');
card_end();

// ---------- 4. Pengecekan tipe data ----------
card_start('4. Pengecekan tipe data pada masing-masing kolom');
render_table(['Kolom', 'Tipe data', 'Terisi', 'Missing', 'Nilai unik', 'Min', 'Max', 'Mean', 'Contoh'], $tipeRows, 'simple');
echo '<p class="text-sm text-slate-500 mt-3">Catatan: kolom <b>nim</b> terdeteksi integer karena seluruh isinya angka, namun secara makna adalah identitas (bukan untuk dihitung).</p>';
card_end();

// ---------- Seluruh dataset ----------
card_start('Seluruh dataset');
render_table($kolom, $data);
card_end();
page_end();
