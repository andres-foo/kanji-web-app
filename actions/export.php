<?php

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="my_kanjis.csv"');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

//check if exists as a kanji
$sql = "SELECT * FROM kanjis JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis_study.added = 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute();
$entries = $stmt->fetchAll();
if(!$entries) exit('No kanjis');

//
$doc = "";
foreach($entries as $kanji) {
    // basics
    $doc .= $kanji['literal'].";";
    $doc .= str_replace(";", ",", $kanji['meanings']).";"; 
    $doc .= str_replace(";", ",", $kanji['components']).";"; 
    $doc .= $kanji['story'].";";

    // examples
    $sql = "SELECT examples.*, examples_study.added FROM examples LEFT JOIN examples_study ON examples.id = examples_study.examples_id WHERE examples_study.added = 1 AND examples.kanji != '' AND kanji LIKE ? ORDER BY jlpt DESC";
    $stmt = $myPDO->prepare($sql);
    $stmt->execute(['%'.$kanji['literal'].'%']);
    $my_examples = $stmt->fetchAll();
    $examples = array();
    foreach($my_examples as $my_example) {
        $examples[] = $my_example['kanji']."[".$my_example['kana']."]";
    }
    $doc .= join(", ", $examples);
    $doc .= "\n";
}
echo $doc;