<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// check if a literal has been provided
if(!isset($_POST['id'])) exit('No id provided');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

// remove kanji to study
$sql = "UPDATE examples_study SET added = '0' WHERE examples_id = ?";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['id']]);

if(!$results) exit("Error removing example ". $_POST['id']);

header("Location: ../kanji.php?literal=" . $_POST['literal']);
exit;