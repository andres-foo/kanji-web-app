<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
// check if a literal has been provided
if(!isset($_POST['literal'])) exit('No literal provided');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

//check if exists as a kanji
$sql = "SELECT * FROM kanjis WHERE literal = ? LIMIT 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['literal']]);
$entry = $stmt->fetch();
if(!$entry) exit('No such kanji found.');

// check if it was previously added to study list
$sql = "SELECT * FROM kanjis_study WHERE literal = ? LIMIT 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['literal']]);
$entry = $stmt->fetch();
if($entry) {
    //exists so must update
    $sql = "UPDATE kanjis_study SET added = 1 WHERE literal = ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['literal']]);
} else {
    // does not exists, must be created
    $sql = "INSERT INTO kanjis_study (literal, score, story, added) VALUES (?,?,?,?)";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['literal'],0,'',1]);
}
if(!$results) exit("Error adding kanji ". $_POST['literal']);

header("Location: ../kanji.php?literal=" . $_POST['literal']);
exit;