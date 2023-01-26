<?php
if (!defined('home')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KanjiApp</title>
    <link rel="stylesheet" href="data/style.css">
</head>
<body>

<div class="content">
    <div class="header">
        <div class="header-actions">
            <form action="actions/review.php" method="POST">
                <button type="submit" class="review">REVIEW</button>
            </form>
            <a href="list.php?list=my_list"<?php if(isset($_GET['list']) && $_GET['list'] == 'my_list') echo ' class="selected"';?>>MY LIST</a>
            <a href="list.php?list=jlpt"<?php if(isset($_GET['list']) && $_GET['list'] == 'jlpt') echo ' class="selected"';?>>JLPT</a>
            <a href="list.php?list=kyouiku"<?php if(isset($_GET['list']) && $_GET['list'] == 'kyouiku') echo ' class="selected"';?> title="Elementary School">KYOUIKU</a>
            <a href="list.php?list=jouyou"<?php if(isset($_GET['list']) && $_GET['list'] == 'jouyou') echo ' class="selected"';?>>JOUYOU</a>
            <a href="list.php?list=heisg6"<?php if(isset($_GET['list']) && $_GET['list'] == 'heisg6') echo ' class="selected"';?>>HEISG6</a>
            <a href="list.php?list=frequency"<?php if(isset($_GET['list']) && $_GET['list'] == 'frequency') echo ' class="selected"';?>>FREQUENCY</a>
        </div>
        <div class="header-form">
            <form action="index.php" method="GET">
                <input type="search" placeholder="Search by literal, readings or meanings" name="query" value="<?php if(isset($_GET['query'])) echo $_GET['query'];?>">
            </form>
        </div>

    </div>