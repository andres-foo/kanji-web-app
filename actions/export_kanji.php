<?php

define('home', true);

require('../parts/helper.php');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

//check if exists as a kanji
$sql = "SELECT
    k.literal AS kliteral,
    k.meanings AS kmeanings,
    k.components AS kcomponents,
    k.onReadings AS konReadings,
    k.kunReadings AS kkunReadings,
    k.story AS kstory,
    e.kanji AS ekanji,
    e.kana AS ekana,
    e.meanings as emeanings,
    e.added as eadded
  FROM
    kanjis AS k
  LEFT JOIN
    examples as e
  ON
    e.kanji LIKE concat('%', k.literal, '%') AND e.added = 1
  WHERE
    k.added = 1 AND k.unfinished IS NULL";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute();
$entries = $stmt->fetchAll();
if (!$entries) exit("No kanjis to export. Make sure to add some kanjis to your list first.");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="kanjis.csv"');


// prepare kanjis
$kanjis = [];
foreach ($entries as $row) {
    if (!is_null($row["ekanji"])) {
        $example = [
            "kanji" => $row["ekanji"],
            "kana" => $row["ekana"],
            "meanings" => $row["emeanings"]
        ];
    } else {
        $example = null;
    }

    if (array_key_exists($row["kliteral"], $kanjis)) {
        $kanjis[$row["kliteral"]]["examples"][] = $example;
    } else {
        $kanjis[$row["kliteral"]] = [
            "literal" => $row["kliteral"],
            "meanings" => $row['kmeanings'],
            "components" => $row['kcomponents'],
            "onReadings" => $row['konReadings'],
            "kunReadings" => $row['kkunReadings'],
            "story" => $row['kstory'],
            "examples" => is_null($example) ? [] : [$example],
        ];
    }
}

//
$doc = "";
foreach ($kanjis as $literal => $kanji) {
    // basics
    $doc .= $literal . ";";
    $doc .= str_replace(";", ", ", $kanji['meanings']) . ";";
    $doc .= str_replace(";", ", ", $kanji['components']) . ";";

    $doc .= (!is_null($kanji['onReadings']) ? str_replace(";", " / ", $kanji['onReadings']) : '') . ";";
    $doc .= (!is_null($kanji['kunReadings']) ? str_replace(";", " / ", $kanji['kunReadings']) : '') . ";";

    // story
    if (empty($kanji['story'])) {
        $doc .= ";";
    } else {
        $pattern = '/#(.+?)#/';
        $story = preg_replace($pattern, '<span class="link">$1</span>', $kanji['story']);
        // emphasis
        $pattern = '/\_(.+?)\_/';
        $story = preg_replace($pattern, '<span class="em">$1</span>', $story);
        // todo
        $pattern = '/\?(.+?)\?/';
        $story = preg_replace($pattern, '<span class="todo">TODO: $1</span>', $story);

        $doc .= $story . ";";
    }

    // examples
    $example_text = '';
    foreach ($kanji["examples"] as $example) {
        $example_text .= '<span class="tag"> ' . str_replace(';', ' / ', $example['kanji']) . '</span> <span class="kana"> [' . str_replace(';', ' / ', $example['kana']) . '] </span><br>' . formatMeanings($example['meanings']) . '<hr>';
        //$examples .= '<span class="tag"> ' . str_replace(';', ' / ', $my_example['kanji']) . '</span> <span class="kana"> [' . str_replace(';', ' / ', $my_example['kana']) . '] </span><br> • ' . str_replace(';', '<br> • ', $my_example['meanings']) . '<hr>';
    }
    $doc .= $example_text . ";";

    // examples front (no kana / meanings)
    $example_text = '';
    foreach ($kanji["examples"] as $example) {
        // remove meta in parethesis 亜米利加(ateji)(rK) -> 亜米利加
        $example_text .= '<span class="tag">' . preg_replace('/\(.*\z/', '', $example['kanji']) . '</span>';
    }
    $doc .= $example_text . ";";

    // image
    if (file_exists("../data/images/" . $literal . ".jpg")) {
        $doc .= "true";
    }

    // end of kanji
    $doc .= "\n";
}
echo $doc;
