<?php

define('home', true);

require('../parts/helper.php');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

//check if exists as a kanji
$sql = "SELECT * FROM kanjis WHERE added = 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute();
$entries = $stmt->fetchAll();
if (!$entries) exit("No kanjis to export. Make sure to add some kanjis to your list first.");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="kanjis.csv"');

//
$doc = "";
foreach ($entries as $kanji) {
    // basics
    $doc .= $kanji['literal'] . ";";
    $doc .= str_replace(";", ", ", $kanji['meanings']) . ";";
    $doc .= str_replace(";", ", ", $kanji['components']) . ";";

    // story
    if (empty($kanji['story'])) {
        $doc .= ";";
    } else {
        $pattern = '/#(.+?)#/';
        $story = preg_replace($pattern, '<b style="color:lightgreen">$1</b>', $kanji['story']);
        // emphasis
        $pattern = '/\_(.+?)\_/';
        $story = preg_replace($pattern, '<b style="color:lightgreen">$1</b>', $story);
        // todo
        $pattern = '/\?(.+?)\?/';
        $story = preg_replace($pattern, '<b style="color:red">TODO: $1</b>', $story);

        $doc .= $story . ";";
    }

    // examples
    $sql = "SELECT * FROM examples WHERE added = 1 AND kanji != '' AND kanji LIKE ?";
    $stmt = $myPDO->prepare($sql);
    $stmt->execute(['%' . $kanji['literal'] . '%']);
    $my_examples = $stmt->fetchAll();

    // examples
    $examples = '';
    foreach ($my_examples as $my_example) {
        $examples .= '<span class="tag"> ' . str_replace(';', ' / ', $my_example['kanji']) . '</span> <span class="kana"> [' . str_replace(';', ' / ', $my_example['kana']) . '] </span><br>' . formatMeanings($my_example['meanings']) . '<hr>';
        //$examples .= '<span class="tag"> ' . str_replace(';', ' / ', $my_example['kanji']) . '</span> <span class="kana"> [' . str_replace(';', ' / ', $my_example['kana']) . '] </span><br> • ' . str_replace(';', '<br> • ', $my_example['meanings']) . '<hr>';
    }
    $doc .= $examples . ";";

    // examples front (no kana / meanings)
    $examples = '';
    foreach ($my_examples as $my_example) {
        $examples .= '<span class="tag">' . str_replace(';', ' / ', $my_example['kanjiClean']) . '</span>';
    }
    $doc .= $examples . ";";

    // image
    if (file_exists("../data/images/" . $kanji['literal'] . ".jpg")) {
        $doc .= "true";
    }

    // end of kanji
    $doc .= "\n";
}
echo $doc;
