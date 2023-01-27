<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
// check if an example has been provided
if(!isset($_POST['word'])) exit('No example provided');

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

// add example
$sql = "INSERT INTO words (word, hiragana, meaning) VALUES (?,?,?)";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute(explode(";",$_POST['word']));

if(!$results) exit("Error adding example");

header("Location: ../index.php?query=" . $_POST['query']);
exit;