<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// check if a literal has been provided
if (empty($_POST['phrase']) || empty($_POST['phrase_ruby']) || empty($_POST['translation'])) die('All fields required');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

// check if example already exists
$sql = "SELECT * FROM phrases WHERE phrase = ? LIMIT 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['phrase']]);
$entry = $stmt->fetch();

if ($entry) {
    die("An entry with that phrase already exists");
}

$sql = "INSERT INTO phrases (phrase, phrase_ruby, translation) VALUES(?, ?, ?)";
$stmt = $myPDO->prepare($sql);

$results = $stmt->execute([$_POST['phrase'], $_POST['phrase_ruby'], $_POST['translation']]);

header("Location: ../pages/add-phrase.php?state=success");
exit;
