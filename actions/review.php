<?php

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 403 Forbidden");
    exit();
}

// db connection
$myPDO = new PDO("sqlite:../data/kanjis.db");

// select kanjis with lowest score
$sql = "SELECT literal FROM kanjis WHERE added = 1 ORDER BY SCORE ASC LIMIT 11";
$stmt = $myPDO->query($sql);
$worst_added = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$worst_added) {
    exit("There are no kanjis to study.");
} else {
    session_start();
    // no session data
    if (!isset($_SESSION["last10"])) {
        $to_review = $worst_added[0]["literal"];
        $_SESSION["last10"] = [$to_review];
    } else {
        foreach ($worst_added as $candidate) {
            if (!in_array($candidate["literal"], $_SESSION["last10"])) {
                $to_review = $candidate["literal"];
                break;
            }
        }
        // no candidate was valid if for example only have added 10 kanjis
        if (!isset($to_review)) {
            // grab last element to review again
            $to_review = array_pop($_SESSION["last10"]);
        }

        // add chosen kanji to session
        array_unshift($_SESSION["last10"], $to_review);

        // purge any extra over 10
        if (count($_SESSION["last10"]) > 10) {
            array_pop($_SESSION["last10"]);
        }
    }
}

/*
echo "<pre>";
if (isset($_SESSION["last10"])) {
    echo "last10: " . implode(", ", $_SESSION["last10"]);
}
echo "</pre>";
echo "<b>Chosen: {$to_review} </b>";
exit();
 */

header("Location: ../pages/review.php?literal=" . $to_review);
exit();
