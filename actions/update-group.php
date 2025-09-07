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


// if "new" check for any empty group number to reuse
if ($group == -1) {
    $sql = "SELECT DISTINCT component_group FROM kanjis ORDER BY component_group ASC";
    $stmt = $myPDO->query($sql);
    $groups = $stmt->fetchAll();
    $new_group = 0;
    foreach ($groups as $group) {
        if ($group['component_group'] == $new_group) {
            $new_group++;
        } else {
            break;
        }
    }
    $group = $new_group;
}


$sql = "UPDATE kanjis SET component_group = ? WHERE literal = ?";
$stmt = $myPDO->prepare($sql);

$results = $stmt->execute([$group, $literal]);
if (!$results) {
    exit('Error updating');
}

header("Location: ../pages/components.php");
exit;
