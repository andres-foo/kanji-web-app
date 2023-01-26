<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// check if a literal has been provided
if(!isset($_POST['literal'])) exit('No literal provided');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

// remove kanji to study
$sql = "UPDATE kanjis_study SET added = '0' WHERE literal = ?";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['literal']]);

if(!$results) exit("Error removing kanji ". $_POST['literal']);

header("Location: ../index.php?query=" . $_POST['query']);
exit;