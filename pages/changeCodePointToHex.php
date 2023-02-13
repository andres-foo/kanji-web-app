<?php
// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');


$sql = "SELECT * FROM kanjis WHERE meanings LIKE 'unicode%'";
$stmt = $myPDO->query($sql);
$entries = $stmt->fetchAll();          

foreach($entries as $entry) {
    $meaning = 'unicode ' . 'U+'.strtoupper(dechex(mb_ord($entry['literal'], "UTF-8")));
    $sql = "UPDATE kanjis SET meanings = ? WHERE literal = ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$meaning, $entry['literal']]); 
}