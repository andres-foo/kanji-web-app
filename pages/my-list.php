<?php define('home', true); ?>
<?php require '../parts/header.php'; ?>


<?php

if (isset($_GET["order"]) && $_GET["order"] == "date") {
    $order = "date";
} else {
    $order = "score";
}

// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

?>

<div class="list">

    <div class="list-filter">
        <?php if ($order == "date"): ?>
            <a href="my-list.php?order=score">order by score</a>
        <?php else: ?>
            <a href="my-list.php?order=date">order by date</a>
        <?php endif; ?>
    </div>

    <?php
    $sql = "SELECT count(*) FROM kanjis WHERE added = 1";
    $stmt = $myPDO->query($sql);
    $total = $stmt->fetch()[0];

    $sql = "SELECT count(*) FROM kanjis WHERE added = 1 AND unfinished = 1";
    $stmt = $myPDO->query($sql);
    $red = $stmt->fetch()[0];

    $green = $total - $red;
    ?>
    <h1>MY STUDY LIST</h1>
    <div class="subtitle"><?= $total ?> characters ( <span class="finished"><?= $green ?></span> / <span class="unfinished"><?= $red ?></span> )</div>

    <?php if ($order == "date"): ?>
        <div>
            <?php
            $sql = "SELECT * FROM kanjis WHERE added = 1 ORDER BY added_at DESC";
            $stmt = $myPDO->query($sql);
            $entries = $stmt->fetchAll();
            ?>
            <div class="title">kanjis by date added:</div>
            <?php if (count($entries) == 0) : ?>
                <p>You haven't added any kanjis yet! To do so click on the "<strong>Add</strong>" button on the top right of the page when viewing a kanji.</p>
            <?php else : ?>
                <div class="kanji-list">
                    <?php
                    foreach ($entries as $entry) {
                        $unfinished = ($entry['unfinished']) ? ' unfinished ' : '';
                        $ignore = ($entry['ignore'] == 1) ? ' ignore ' : '';
                        echo '<a href="kanji.php?literal=' . $entry['literal'] . '"';
                        echo ' class="added' . $unfinished . $ignore . '"' . ' title="Added: ' . $entry['added_at'] . ' • Score: ' . $entry['score'] . '"';
                        echo '>' . $entry['literal'] . '</a>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div>
            <?php
            $sql = "SELECT * FROM kanjis WHERE added = 1 ORDER BY score ASC, added_at DESC";
            $stmt = $myPDO->query($sql);
            $entries = $stmt->fetchAll();
            ?>
            <div class="title">kanjis by score:</div>
            <?php if (count($entries) == 0) : ?>
                <p>You haven't added any kanjis yet! To do so click on the "<strong>Add</strong>" button on the top right of the page when viewing a kanji.</p>
            <?php else : ?>
                <div class="kanji-list">
                    <?php
                    $score = null;
                    foreach ($entries as $entry) {
                        if ($entry["score"] != $score) {
                            $score = $entry["score"];
                            echo '</div><div class="list-score">score: ' . $score . '</div><div class="kanji-list">';
                        }
                        $unfinished = ($entry['unfinished']) ? ' unfinished ' : '';
                        $ignore = ($entry['ignore'] == 1) ? ' ignore ' : '';
                        echo '<a href="kanji.php?literal=' . $entry['literal'] . '"';
                        echo ' class="added' . $unfinished . $ignore . '"' . ' title="Added: ' . $entry['added_at'] . ' • Score: ' . $entry['score'] . '"';
                        echo '>' . $entry['literal'] . '</a>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        </div>


        <?php require '../parts/footer.php'; ?>