<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
// check if a literal has been provided
if(!isset($_POST['id'])) exit('No id provided');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

//check if example exists
$sql = "SELECT * FROM examples WHERE id = ? LIMIT 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['id']]);
$entry = $stmt->fetch();
if(!$entry) exit('No such example found.');

// check if it was previously added to study list
$sql = "SELECT * FROM examples_study WHERE examples_id = ? LIMIT 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['id']]);
$entry = $stmt->fetch();
if($entry) {
    //exists so must update
    $sql = "UPDATE examples_study SET added = 1 WHERE examples_id = ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['id']]);
} else {
    // does not exists, must be created
    $sql = "INSERT INTO examples_study (examples_id, added) VALUES (?,?)";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['id'],1]);
}
if(!$results) exit("Error adding example ". $_POST['id']);

header("Location: ../pages/kanji.php?literal=" . $_POST['literal']);
exit;