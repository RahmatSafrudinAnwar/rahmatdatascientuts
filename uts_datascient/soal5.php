<?php
require 'layout.php';

function train_test_split(array $data, float $test_size = 0.2, bool $shuffle = true): array {
    if ($test_size <= 0 || $test_size >= 1) throw new InvalidArgumentException('test_size harus di antara 0 dan 1');
    $data = array_values($data);
    $n = count($data);
    $idx = range(0, $n - 1);
    if ($shuffle) {                       // Fisher-Yates shuffle pada indeks
        for ($i = $n - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$idx[$i], $idx[$j]] = [$idx[$j], $idx[$i]];
        }
    }
    $nt = max(1, min($n - 1, (int)round($n * $test_size)));
    $test = $train = [];
    foreach (array_slice($idx, 0, $nt) as $i) $test[] = $data[$i];
    foreach (array_slice($idx, $nt) as $i) $train[] = $data[$i];
    return ['train' => $train, 'test' => $test];
}

$seed = isset($_GET['seed']) && $_GET['seed'] !== '' ? (int)$_GET['seed'] : null;
if ($seed !== null) mt_srand($seed);

$dataset = [];
for ($i = 1; $i <= 50; $i++) $dataset[] = ['id' => $i, 'fitur1' => mt_rand(50, 100), 'fitur2' => mt_rand(50, 100), 'label' => mt_rand(0, 1)];

$res = train_test_split($dataset, 0.2, true);
$train = $res['train']; $test = $res['test'];
$idT = array_column($train, 'id'); $idU = array_column($test, 'id');
$all = array_merge($idT, $idU); sort($all);
$noDup = !array_intersect($idT, $idU);
$intact = $all === range(1, count($dataset));

page_start('5. Train-Test Split', 5);
echo '<form method="get" class="flex flex-wrap gap-2 mb-6">
<input name="seed" placeholder="Seed (opsional)" value="' . h($_GET['seed'] ?? '') . '" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
<button class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium">Bagi ulang</button></form>';

grid_start();
stat_card('Total dataset', count($dataset), 'secondary');
stat_card('Latih (80%)', count($train), 'success');
stat_card('Uji (20%)', count($test), 'warning');
stat_card('Verifikasi', $noDup && $intact ? 'Tanpa duplikasi' : 'GAGAL', $noDup && $intact ? 'primary' : 'danger');
grid_end();

$cols = ['ID', 'Fitur 1', 'Fitur 2', 'Label'];
card_start('Data latih (' . count($train) . ' baris)');
render_table($cols, array_map('array_values', $train));
card_end();
card_start('Data uji (' . count($test) . ' baris)');
render_table($cols, array_map('array_values', $test));
card_end();
page_end();
