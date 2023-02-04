<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
// check if a literal has been provided
if(!isset($_POST['literal'])) exit('No literal provided');


// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

//components & other forms
$sql = "UPDATE kanjis SET components = ?, other_forms = ? WHERE literal = ?";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['components'],$_POST['otherForms'],$_POST['literal']]);
if(!$results) exit('Unable to update components');

//story
//check if exists
$sql = "SELECT * FROM kanjis_study WHERE literal = ? LIMIT 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([$_POST['literal']]);
$entry = $stmt->fetch();

if($entry) {        
    //exists so must update the story
    $sql = "UPDATE kanjis_study SET story = ? WHERE literal = ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['story'],$_POST['literal']]);
} else {
    // does not exists, must be created
    $sql = "INSERT INTO kanjis_study (literal, score, story, added) VALUES (?,?,?,?)";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['literal'],0,$_POST['story'],0]);
}
if(!$results) exit('Unable to update story');

header("Location: ../pages/kanji.php?literal=" . $_POST['literal']);
exit;