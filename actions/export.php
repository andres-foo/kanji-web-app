<?php



// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

//check if exists as a kanji
$sql = "SELECT * FROM kanjis WHERE added = 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute();
$entries = $stmt->fetchAll();
if(!$entries) exit("No kanjis to export. Make sure to add some kanjis to your list first.");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="my_kanjis.csv"');

//
$doc = "";
foreach($entries as $kanji) {
    // basics
    $doc .= $kanji['literal'].";";
    $doc .= str_replace(";", ", ", $kanji['meanings']).";"; 
    $doc .= str_replace(";", ", ", $kanji['components']).";"; 
    $doc .= $kanji['story'].";";

    // examples
    $sql = "SELECT * FROM examples WHERE added = 1 AND kanji != '' AND kanji LIKE ? ORDER BY jlpt DESC";
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