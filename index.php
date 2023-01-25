<?php

// db connection
$myPDO = new PDO('sqlite:data/kanjis.db');


$entries = [];
if(isset($_GET['q'])) {	
    //$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $sql = "SELECT * FROM kanjis WHERE literal = ? OR meanings LIKE ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_GET['q'], '%'.$_GET['q'].'%']);
    $entries = $stmt->fetchAll();
    // echo "<pre>";
    // var_dump($entries);
    // echo "</pre>";
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
    
<form action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="search" name=q>
    <input type="submit" name="submit" value="Search">
</form>

<?php if(!$entries): ?>
       "No results."
<?php else: ?>
    <?php foreach($entries as $entry): ?> {
        <?php echo $entry['meanings'];?>
    <?php endforeach; ?>
<?php endif;?>


<div class="card">
    <div class="left">
        <div class="kanji">名</div>
    </div><!-- left -->
    <div class="right">
        <div class="meta">
            <div class="item"><span class="ref">strokes</span><span class="number">6</span></div>
            <div class="item"><span class="ref">frequency</span><span class="number">6</span></div>
            <div class="item"><span class="ref">jlpt</span><span class="number">6</span></div>
            <div class="item"><span class="ref">grade</span><span class="number">6</span></div>
            <div class="item"><span class="ref">heisg6</span><span class="number">6</span></div>
        </div><!-- meta -->
        <div class="meanings">
            Asia, rank next, come after, -ous
        </div><!-- meanings -->
        <div class="readings">
            <div class="reading">ア</div>
            <div class="reading">アイ</div>
            <div class="reading">ワ</div>
        </div><!-- readings -->
        <div class="readings">
            <div class="reading">あわ.れ</div>
            <div class="reading">あわ.れむ</div>
            <div class="reading">かな.しい</div>
        </div><!-- readings -->
        <div class="components">
            <div class="component"><a href="">爫</a></div>
            <div class="component"><a href="">冖</a></div>
            <div class="component"><a href="">𢖻</a></div>
        </div><!-- readings -->
        <div class="story">
            Some text that represents the story
        </div><!-- readings -->
    </div><!-- right -->

</div>

</body>
</html>