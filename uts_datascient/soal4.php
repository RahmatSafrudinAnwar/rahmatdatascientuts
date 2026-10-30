<?php
require 'layout.php';

$default = '67, 72, 85, 90, 78, 85, 92, 64, 73, 85, 88, 95, 70, 61, 79, 85, 99, 74, 82, 68, 77, 91, 85, 100, 66';
$input = $_POST['data'] ?? $default;
$data = array_values(array_map('floatval', array_filter(preg_split('/[\s,;]+/', $input), 'is_numeric')));
$n = count($data);

page_start('4. Statistik Deskriptif & Diagram Frekuensi', 4);
echo '<form method="post" class="bg-white rounded-xl shadow-sm p-5 mb-6">
<label class="block font-semibold mb-2">Data numerik (pisahkan dengan koma, spasi, atau baris baru)</label>
<textarea name="data" rows="3" class="w-full rounded-lg border border-slate-300 p-2 mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500">' . h($input) . '</textarea>
<button class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium">Hitung</button></form>';

if ($n === 0) { echo alert('Tidak ada data numerik yang valid.', 'warning'); page_end(); exit; }

$mean = array_sum($data) / $n;
$s = $data; sort($s);
$mid = intdiv($n, 2);
$median = $n % 2 ? $s[$mid] : ($s[$mid - 1] + $s[$mid]) / 2;

$frek = [];
foreach ($data as $v) { $k = (string)$v; $frek[$k] = ($frek[$k] ?? 0) + 1; }
$maks = max($frek);
$modus = [];
if ($maks > 1) foreach ($frek as $k => $f) if ($f === $maks) $modus[] = $k + 0;
sort($modus);

$ss = 0; foreach ($data as $v) $ss += ($v - $mean) ** 2;
$sdPop = sqrt($ss / $n);
$sdSam = $n > 1 ? sqrt($ss / ($n - 1)) : 0;

grid_start();
stat_card('Mean', number_format($mean, 2));
stat_card('Median', number_format($median, 2), 'success');
stat_card('Modus', $modus ? implode(', ', $modus) . " ({$maks}x)" : 'tidak ada', 'warning');
stat_card('Std Dev (pop / sampel)', number_format($sdPop, 2) . ' / ' . number_format($sdSam, 2), 'danger');
grid_end();

$rentang = ['60-69' => [60, 69], '70-79' => [70, 79], '80-89' => [80, 89], '90-100' => [90, 100]];
$dist = array_fill_keys(array_keys($rentang), 0);
foreach ($data as $v) foreach ($rentang as $l => [$a, $b]) if ($v >= $a && $v <= $b) { $dist[$l]++; break; }
$maxF = max($dist) ?: 1;

card_start('Diagram batang frekuensi');
foreach ($dist as $l => $f) {
    $w = $f / $maxF * 100;
    echo '<div class="flex items-center gap-3 mb-2"><div class="w-20 font-semibold">' . h($l) . '</div>
<div class="flex-1 bg-slate-200 rounded h-7 overflow-hidden"><div class="bg-blue-600 h-7 text-white text-xs leading-7 px-2 whitespace-nowrap overflow-hidden" style="width:' . $w . '%">' . str_repeat('#', $f) . '</div></div>
<div class="w-24 text-sm text-slate-600">' . $f . ' (' . number_format($f / $n * 100, 1) . '%)</div></div>';
}
card_end();

card_start('Tabel distribusi frekuensi');
$rows = [];
foreach ($dist as $l => $f) $rows[] = [$l, $f, number_format($f / $n * 100, 1) . '%'];
render_table(['Rentang', 'Frekuensi', 'Persentase'], $rows);
card_end();

card_start('Data input (' . $n . ' nilai)');
$rows = [];
foreach ($data as $i => $v) $rows[] = [$i + 1, $v + 0];
render_table(['No', 'Nilai'], $rows);
card_end();
page_end();
