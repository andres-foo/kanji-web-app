<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');



// update kanjis
$sql = "UPDATE kanjis SET score = 0, added = 0";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute();

if(!$results) exit("Error when trying to reset kanjis");

// update examples
$sql = "UPDATE examples SET added = 0";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute();

if(!$results) exit("Error when trying to reset examples");



header("Location: ../pages/list.php?list=my_list");
exit;