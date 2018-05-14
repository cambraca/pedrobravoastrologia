<?php

$content = file_get_contents('./posts utf8.txt');
$lines = explode("\n", $content);
$lines = array_map('trim', $lines);
$lines = array_filter($lines);

$current_date = [NULL, NULL, NULL]; // y, m, d

$final = []; //final html, keyed by date

$current_lines = [];

function singleLine($l) {
  $l = htmlentities($l);
  foreach (['Mercurio','Venus','Marte','Júpiter','Saturno','Urano','Neptuno','Plutón','Luna','Sol','Aries','Tauro','Cáncer','Géminis','Leo','Virgo','Libra','Escorpio','Escorpión','Sagitario','Capricornio','Acuario','Piscis'] as $word) {
    $l = preg_replace('/\b'.preg_quote(htmlentities($word)).'\b/', '<strong>$0</strong>', $l);
  }
  return $l;
}

function getHtml($lines) {
  $ret = '';
  foreach ($lines as $l)
    $ret .= '<p>' . singleLine($l) . '</p>';
  return $ret;
}

function store(&$final, &$current_lines, $current_date) {
  if (count($current_lines) === 0)
    return;
  $key = implode('/', $current_date);
  $final[$key] = getHtml($current_lines);
  $current_lines = [];
}

foreach ($lines as $line) {
  if (preg_match('/^(\d+)(\/)(\d+)(\/)(\d+)$/', $line, $matches)) {
    store($final, $current_lines, $current_date);
    $current_date = [intval($matches[1]), str_pad(intval($matches[3]), 2, '0', STR_PAD_LEFT), str_pad(intval($matches[5]), 2, '0', STR_PAD_LEFT)];
    continue;
  }
  if (preg_match('/^\/(\d+)$/', $line, $matches)) {
    store($final, $current_lines, $current_date);
    $current_date[2] = str_pad(intval($matches[1]), 2, '0', STR_PAD_LEFT);
    continue;
  }
  if (preg_match('/^[^\d ]+ (\d+)$/', $line, $matches)) {
    store($final, $current_lines, $current_date);
    $current_date[2] = str_pad(intval($matches[1]), 2, '0', STR_PAD_LEFT);
    continue;
  }
  $current_lines[] = $line;
}

store($final, $current_lines, $current_date);

echo json_encode($final, JSON_PRETTY_PRINT);
