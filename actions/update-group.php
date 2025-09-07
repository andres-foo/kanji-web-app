<?php

// is post
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// has data
if (!isset($_POST['literal']) || !isset($_POST['group'])) {
    exit('Missing data');
}
$literal = $_POST['literal'];
$group = $_POST['group'];

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

$sql = "UPDATE kanjis SET component_group = ? WHERE literal = ?";
$stmt = $myPDO->prepare($sql);

$results = $stmt->execute([$group, $literal]);
if (!$results) {
    exit('Error updating');
}

header("Location: ../pages/components.php");
exit;
