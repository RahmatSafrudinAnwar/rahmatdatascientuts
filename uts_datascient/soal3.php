<?php
require 'layout.php';

$data = [80, null, 75, "", 90, null, 65, 85, 100];
$ok = fn($v) => $v !== null && $v !== '' && is_numeric($v);

$valid = array_filter($data, $ok);
$mean = array_sum($valid) / count($valid);

$imp = array_map(fn($v) => $ok($v) ? (float)$v : $mean, $data);
$min = min($imp); $max = max($imp);
$norm = array_map(fn($v) => $max == $min ? 0.0 : ($v - $min) / ($max - $min), $imp);

$rows = [];
foreach ($data as $i => $v) {
    $rows[] = [$i + 1, $v === null ? 'null' : ($v === '' ? '""' : $v),
        number_format($imp[$i], 2, '.', ''), number_format($norm[$i], 4, '.', '')];
}

page_start('3. Data Cleaning, Imputation & Normalization', 3);
grid_start();
stat_card('Valid values', count($valid) . ' of ' . count($data), 'success');
stat_card('Sum of valid', array_sum($valid), 'secondary');
stat_card('Mean', number_format($mean, 2, '.', ''));
stat_card('Min / Max', "$min / $max", 'info');
grid_end();

card_start('Formulas');
echo '<p class="mb-2">Mean = ' . array_sum($valid) . ' / ' . count($valid) . ' = <b>' . number_format($mean, 4, '.', '') . '</b></p>
<p>Min-Max = (x &minus; min) / (max &minus; min) &rarr; range 0 to 1</p>';
card_end();

card_start('Result');
render_table(['No', 'Original', 'Imputed (mean)', 'Min-Max (0-1)'], $rows);
card_end();
page_end();
