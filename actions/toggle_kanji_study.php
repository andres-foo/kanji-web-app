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

// if its already added remove and reset score
if($entry['added'] == 1) {
    $sql = "UPDATE kanjis SET added = 0, score = 0, added_at = null WHERE literal = ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['literal']]);
} else {
    $sql = "UPDATE kanjis SET added = 1, score = 0, added_at = CURRENT_TIMESTAMP WHERE literal = ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['literal']]);
}
if(!$results) exit("Error toggling kanji ". $_POST['literal']);

header("Location: ../pages/kanji.php?literal=" . $_POST['literal']);
exit;