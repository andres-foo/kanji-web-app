<style>
pre {
    font-family: "Babelstone Han";
}
</style>
<?php

// db connection
$myPDO = new PDO('sqlite:data/kanjis.db');

// select every kanji that's either jlpt, grade or heisg6
$sql = "SELECT * FROM kanjis WHERE jlpt IS NOT NULL OR grade IS NOT NULL OR heisg6 IS NOT NULL AND frequency IS NOT NULL";
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
        if(!empty($component) && !in_array($component, $missing_components)) {
            array_push($missing_components, [$component, mb_ord($component, "UTF-8")]);
        }
    }
}

//
echo "Missing components: " . count($missing_components) . "<br>";
foreach($missing_components as $component) {
    echo $component[0] ." unicode ". $component[1] . "<br>";
    $sql = "INSERT INTO kanjis (literal, meanings) VALUES ('".$component[0]."', 'unicode ".$component[1]."')";
    //$myPDO->query($sql);
}

?>