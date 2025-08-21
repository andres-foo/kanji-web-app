<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// check if a literal has been provided
if (empty($_POST['kanji']) || empty($_POST['kana']) || empty($_POST['meanings'])) die('All fields required');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

// check if example already exists
$sql = "SELECT * FROM examples WHERE kanji = ? AND kana = ? LIMIT 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['kanji'], $_POST['kana']]);
$entry = $stmt->fetch();

if ($entry) {
    die("An entry with that kanji/kana already exists");
}

$sql = "INSERT INTO examples (kanji, kana, meanings, jlpt, added) VALUES(?, ?, ?, ?, ?)";
$stmt = $myPDO->prepare($sql);

$added = !isset($_POST['added']) ? 0 : 1;
$results = $stmt->execute([$_POST['kanji'], $_POST['kana'], $_POST['meanings'], $_POST['jlpt'], $added]);

header("Location: ../pages/search.php?query=" . $_POST['kanji']);
exit;
