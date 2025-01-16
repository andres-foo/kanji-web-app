<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// check if a literal has been provided
if (!isset($_POST['literal'])) exit('No literal provided');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

// find current score
$sql = "SELECT * FROM kanjis WHERE literal = ? LIMIT 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['literal']]);
$entry = $stmt->fetch();

if (isset($_POST['review_good'])) {
    $new_score = $entry['score'] + 2;
    $type = "good";
} elseif (isset($_POST['review_bad'])) {
    $new_score = $entry['score'] - 2;
    $type = "bad";
} elseif (isset($_POST['review_neutral'])) {
    $new_score = $entry['score'] + 1;
    $type = "neutral";
} else {
    exit('Wrong difficulty provided.');
}

// update value
$sql = "UPDATE kanjis SET score = ? WHERE literal = ?";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$new_score, $_POST['literal']]);

if (!$results) exit("Error marking kanji " . $_POST['literal'] . " as " . $_POST['difficulty'] . ".");

// save history
date_default_timezone_set('America/Santiago');
$sql = "INSERT INTO review_history (kanji, date, before, after, type) VALUES(?, ?, ?, ?, ?)";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['literal'], date('Y-m-d'), $entry['score'], $new_score, $type]);

header("Location: ../pages/kanji.php?literal=" . $_POST['literal']);
exit;
