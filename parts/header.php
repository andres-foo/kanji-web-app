<?php
if (!defined('home')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $title = "KanjiApp";
    if (preg_match("/.*kanji.*/", $_SERVER['REQUEST_URI'], $matches) && $entry) {
        $title .= ' - ' . $entry['literal'] . " - " . str_replace(";", ", ", $entry['meanings']);
    }
    ?>
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="../data/style.css">
</head>

<body>

    <div class="content">
        <div class="header">
            <div class="header-actions">
                <a href="index.php">HOME</a>
                <form action="../actions/review.php" method="POST">
                    <button type="submit" class="review">REVIEW</button>
                </form>
                <a href="list.php?list=my_list" <?php if (isset($_GET['list']) && $_GET['list'] == 'my_list') echo ' class="selected"'; ?>>MY LIST</a>
                <a href="list.php?list=jlpt" <?php if (isset($_GET['list']) && $_GET['list'] == 'jlpt') echo ' class="selected"'; ?>>JLPT</a>
                <a href="list.php?list=jouyou" <?php if (isset($_GET['list']) && $_GET['list'] == 'jouyou') echo ' class="selected"'; ?>>JOUYOU</a>
                <a href="list.php?list=heisg6" <?php if (isset($_GET['list']) && $_GET['list'] == 'heisg6') echo ' class="selected"'; ?>>HEISG6</a>
                <a href="list.php?list=frequency" <?php if (isset($_GET['list']) && $_GET['list'] == 'frequency') echo ' class="selected"'; ?>>FREQUENCY</a>
                <a href="list.php?list=kanken" <?php if (isset($_GET['list']) && $_GET['list'] == 'kanken') echo ' class="selected"'; ?>>KANKEN</a>
                <a href="../actions/export_kanji.php" class="export">&#8595; KANJIS</a>
                <a href="../actions/export_words.php" class="export">&#8595; WORDS</a>
                <a href="../pages/history.php" class="export">HISTORY</a>
                <a href="../pages/print.php" target="_blank" class="export">TO PRINT</a>
            </div>
            <div class="header-form">
                <form action="search.php" method="GET">
                    <input type="search" placeholder="Search by literal, readings or meanings" name="query" value="<?php if (isset($_GET['query'])) echo $_GET['query']; ?>">
                </form>
                <?php
                if (isset($_GET['query']) && !empty($_GET['query']) && !itHasJapanese($_GET['query'])) {
                    require_once 'helper.php';
                    $hiragana = toHiragana($_GET['query']);
                    if (isOnlyHiragana($hiragana)) {
                        echo '<div class="alternative">Search for <a href="search.php?query=' . $hiragana . '">' . $hiragana . '</a> instead?</div>';
                    }
                }
                ?>
            </div>



        </div>