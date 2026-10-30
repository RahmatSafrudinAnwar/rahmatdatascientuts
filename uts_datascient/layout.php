<?php
// Layout bersama: Tailwind CSS (CDN) + DataTables (CDN). <table class="dt"> otomatis jadi DataTable.
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function warna($c) {
    $m = ['primary' => 'blue', 'secondary' => 'slate', 'success' => 'emerald', 'danger' => 'rose', 'warning' => 'amber', 'info' => 'sky'];
    return $m[$c] ?? 'blue';
}

function page_start($title, $active) {
    $nav = [1 => 'Pipeline', 2 => 'EDA CSV', 3 => 'Cleaning', 4 => 'Statistik', 5 => 'Train-Test'];
    echo '<!doctype html><html lang="id"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1"><title>' . h($title) . '</title>
<script src="https://cdn.tailwindcss.com/3.4.17"></script>
<link href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" rel="stylesheet">
<style>
.dataTables_wrapper{font-size:.875rem;color:#334155}
.dataTables_wrapper .dataTables_filter input,.dataTables_wrapper .dataTables_length select{border:1px solid #cbd5e1;border-radius:.375rem;padding:.25rem .5rem;margin:0 .25rem}
.dataTables_wrapper .dataTables_paginate .paginate_button{border-radius:.375rem;border:1px solid transparent!important}
.dataTables_wrapper .dataTables_paginate .paginate_button.current{background:#2563eb!important;color:#fff!important;border-color:#2563eb!important}
table.dataTable thead th{padding:.6rem .75rem}
table.dataTable tbody td{padding:.5rem .75rem}
.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate{margin-top:.75rem}
</style></head>
<body class="bg-slate-100 text-slate-800 min-h-screen">
<nav class="bg-gradient-to-r from-blue-600 to-indigo-600 shadow mb-8"><div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-3">
<a href="index.php" class="text-white text-lg font-bold">Data Science PHP</a><div class="flex flex-wrap gap-1">';
    foreach ($nav as $k => $n) {
        $cls = $k == $active ? 'bg-white text-blue-700 font-semibold' : 'text-blue-50 hover:bg-white/20';
        echo '<a class="px-3 py-1.5 rounded-md text-sm ' . $cls . '" href="soal' . $k . '.php">' . $k . '. ' . $n . '</a>';
    }
    echo '</div></div></nav><main class="max-w-6xl mx-auto px-4 pb-12"><h1 class="text-2xl font-bold mb-6">' . h($title) . '</h1>';
}

function page_end() {
    echo '</main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>$(function(){ $("table.dt").DataTable({ pageLength: 10 }); $("table.dt-simple").DataTable({ paging: false, searching: false, info: false }); });</script>
</body></html>';
}

function grid_start() { echo '<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">'; }
function grid_end() { echo '</div>'; }

function stat_card($label, $value, $color = 'primary') {
    $w = warna($color);
    echo '<div class="bg-white rounded-xl shadow-sm border-t-4 border-' . $w . '-500 p-4 text-center">
<div class="text-xs uppercase tracking-wide text-slate-500">' . h($label) . '</div>
<div class="mt-1 text-xl font-bold text-' . $w . '-600">' . h($value) . '</div></div>';
}

function badge($text, $color = 'primary') {
    $w = warna($color);
    return '<span class="inline-block rounded-full bg-' . $w . '-100 text-' . $w . '-700 px-3 py-1 text-sm font-medium">' . h($text) . '</span>';
}

function alert($text, $color = 'primary') {
    $w = warna($color);
    return '<div class="rounded-lg border border-' . $w . '-200 bg-' . $w . '-50 text-' . $w . '-800 px-4 py-3 mb-4">' . h($text) . '</div>';
}

function card_start($title) {
    echo '<section class="bg-white rounded-xl shadow-sm mb-6"><h2 class="px-5 py-3 border-b border-slate-200 font-semibold">' . h($title) . '</h2><div class="p-5">';
}
function card_end() { echo '</div></section>'; }

// Tabel; $dt = true => DataTables
function render_table($head, $rows, $dt = true) {
    echo '<div class="overflow-x-auto"><table class="' . ($dt === 'simple' ? 'dt-simple ' : ($dt ? 'dt ' : '')) . 'display w-full text-sm stripe hover"><thead><tr>';
    foreach ($head as $c) echo '<th class="text-left">' . h($c) . '</th>';
    echo '</tr></thead><tbody>';
    foreach ($rows as $r) {
        echo '<tr>';
        foreach ($r as $c) echo '<td>' . h($c) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}
