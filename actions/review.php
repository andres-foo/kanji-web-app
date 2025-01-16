<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

$sql = "SELECT * FROM kanjis WHERE added = 1 ORDER BY SCORE ASC";
$stmt = $myPDO->query($sql);
$entries = $stmt->fetchAll();
if (!$entries) {
    exit("There are no kanjis to study.");
} else {
    session_start();
    if (!isset($_SESSION['last10'])) {
        $entries = [$entries[0]];
        $_SESSION['last10'] = [$entries['literal']];
    } else {
        $worst = $entries[0];
        $found = false;
        foreach ($entries as $entry) {
            if (!in_array($entry['literal'], $_SESSION['last10'])) {
                $found = true;
                $entries = [$entry];
                break;
            }
        }
        if (!$found) {
            $entries = [$worst];
        }
        array_unshift($_SESSION['last10'], $entries[0]['literal']);
        if (count($_SESSION['last10']) > 10) {
            array_pop($_SESSION['last10']);
        }
    }

    header("Location: ../pages/kanji.php?literal=" . $entries[0]['literal'] . "&ref=review");
    exit;
}
