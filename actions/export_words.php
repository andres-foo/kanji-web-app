<?php

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

//check if exists as a kanji
$sql = "SELECT * FROM examples WHERE added = 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute();
$entries = $stmt->fetchAll();
if (!$entries) exit("No words to export. Make sure to add some words to your list first.");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="words.csv"');

//
$doc = "";
foreach ($entries as $word) {
    // basics
    $doc .= $word['kanji'] . ";";
    $doc .= trim($word['kana']) . ";";
    $doc .= str_replace(";", ", ", trim($word['meanings'])) . ";";
    $doc .= "true"; // fromKanjiApp
    // end of word
    $doc .= "\n";
}
echo $doc;
