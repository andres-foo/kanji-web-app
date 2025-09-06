<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
// check if a literal has been provided
if (!isset($_POST['literal'])) exit('No literal provided');


// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');


//components & other forms
$unfinished = isset($_POST['unfinished']) ? 1 : NULL;
$is_component = isset($_POST['is_component']) ? 1 : NULL;
$sql = "UPDATE kanjis SET components = ?, other_forms = ?, story = ?, unfinished = ?, is_component = ? WHERE literal = ?";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([trim($_POST['components']), trim($_POST['otherForms']), trim($_POST['story']), $unfinished, $is_component, $_POST['literal']]);

if (!$results) exit('Unable to update kanji');

// file upload
if (file_exists($_FILES['image']['tmp_name']) && getimagesize($_FILES["image"]["tmp_name"])) {
    $imageFileType = strtolower(pathinfo(basename($_FILES["image"]["name"]), PATHINFO_EXTENSION));

    if ($imageFileType != "jpg") {
        exit("Only JPG images allowed");
    }

    $target = "../data/images/" . $_POST['literal'] . '.jpg';

    if (file_exists($target)) {
        unlink($target);
    }

    move_uploaded_file($_FILES["image"]["tmp_name"], $target);
}

header("Location: ../pages/kanji.php?literal=" . $_POST['literal']);
exit;
