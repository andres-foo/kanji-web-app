<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// check if a literal has been provided
if(!isset($_POST['literal'])) exit('No literal provided');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

// find current score
$sql = "SELECT * FROM kanjis_study WHERE literal = ? LIMIT 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['literal']]);
$entry = $stmt->fetch();

$sql = "UPDATE kanjis_study SET score = ? WHERE literal = ?";
$stmt = $myPDO->prepare($sql);

if($_POST['difficulty'] == "hard") {
    $new_score = $entry['score']-2;
} elseif($_POST['difficulty'] == "easy") {
    $new_score = $entry['score']+2;
} else {
    exit('Wrong difficulty provided.');
}
$results = $stmt->execute([$new_score,$_POST['literal']]);

if(!$results) exit("Error marking kanji ". $_POST['literal'] . " as " . $_POST['difficulty'] . ".");

header("Location: ../pages/kanji.php?literal=" . $_POST['literal']);
exit;