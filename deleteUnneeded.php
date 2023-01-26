<?php

// db connection
$myPDO = new PDO('sqlite:data/kanjis.db');

// select every kanji that has either jlpt, grade, heisg6 or frequency
$sql = "SELECT * FROM kanjis WHERE jlpt IS NOT NULL OR grade IS NOT NULL OR heisg6 IS NOT NULL OR frequency IS NOT NULL";
$stmt = $myPDO->query($sql);
$entries = $stmt->fetchAll();

// gather all components from all selected kanjis
$components = [];
if(!$entries) {
    echo "No results.";
} else {
    foreach($entries as $entry) {
        $entry_components = explode(';',$entry['components']);
        foreach($entry_components as $entry_component) {
            if(!in_array($entry_component, $components)) {
                array_push($components, $entry_component);
            }
        }
    }
}

// gather all kanjis that have no jlpt, grade, heisg6 or frequency and are potentially unneeded
$to_delete = [];
$sql = "SELECT * FROM kanjis WHERE jlpt IS NULL AND grade IS NULL AND heisg6 IS NULL AND frequency IS NULL";
$stmt = $myPDO->query($sql);
$entries = $stmt->fetchAll();
if(!$entries) {
    echo "No results.";
} else {
    foreach($entries as $entry) {
        if(!in_array($entry['literal'], $components)) {
            array_push($to_delete, $entry['literal']);
        }
    }
}
echo "Deleting ". count($to_delete) . " elements";
foreach($to_delete as $kanji) {
    $sql = "DELETE FROM kanjis WHERE literal = '".$kanji."'";
    $myPDO->query($sql);
}

?>