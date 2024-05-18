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

if($entry['added'] == 1) {
    $sql = "UPDATE examples SET added = 0 WHERE id = ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['id']]);
} else {
    $sql = "UPDATE examples SET added = 1 WHERE id = ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['id']]);
}
if(!$results) exit("Error toggling example ". $_POST['id']);

header("Location: ../pages/kanji.php?literal=" . $_POST['literal']);
exit;