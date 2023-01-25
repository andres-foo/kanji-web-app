<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KanjiApp</title>
    <link rel="stylesheet" href="data/style.css">
</head>
<body>
<?php

// db connection
$myPDO = new PDO('sqlite:data/kanjis.db');

// select every kanji that's either jlpt, grade or heisg6
$sql = "SELECT * FROM kanjis WHERE jlpt IS NOT NULL OR grade IS NOT NULL OR heisg6 IS NOT NULL";
$stmt = $myPDO->query($sql);
$entries = $stmt->fetchAll();


// gather all components from all selected kanjis
$all_components = [];
if(!$entries) {
    echo "No results.";
} else {
    foreach($entries as $entry) {
        $entry_components = explode(';',$entry['components']);
        foreach($entry_components as $entry_component) {
            if(!in_array($entry_component, $all_components)) {
                array_push($all_components, $entry_component);
            }
        }
    }
}

// find missing components
$missing_components = [];
foreach($all_components as $component) {
    $sql = "SELECT * FROM kanjis WHERE literal = ? LIMIT 1";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$component]);
    $entry = $stmt->fetch();
    if(!$entry) {
        if(!in_array($component, $missing_components)) {
            array_push($missing_components, $component);
        }
    }
}

//
echo "Missing components: " . count($missing_components);
foreach($missing_components as $component) {
    echo $component;
}


?>
</body>
</html>

