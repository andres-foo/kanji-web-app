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
    <?php foreach($entries as $entry): ?>

<div class="card">
    <div class="left">
        <div class="kanji"><?php echo $entry['literal']; ?></div>
    </div><!-- left -->
    <div class="right">
        <div class="meta">
            <div class="item">
                <span class="ref">strokes</span>
                <span class="number"><?php echo $entry['strokes']; ?></span>
            </div>
            <?php if(!empty($entry['frequency'])): ?>
            <div class="item">
                <span class="ref">frequency</span>
                <span class="number"><?php echo $entry['frequency']; ?></span>
            </div>
            <?php endif; ?>
            <?php if(!empty($entry['jlpt'])): ?>
            <div class="item">
                <span class="ref">jlpt</span>
                <span class="number"><?php echo $entry['jlpt']; ?></span>
            </div>
            <?php endif; ?>
            <?php if(!empty($entry['grade'])): ?>
            <div class="item">
                <span class="ref">grade</span>
                <span class="number"><?php echo $entry['grade']; ?></span>
            </div>
            <?php endif; ?>
            <?php if(!empty($entry['heisg6'])): ?>
            <div class="item">
                <span class="ref">heisg6</span>
                <span class="number"><?php echo $entry['heisg6']; ?></span>
            </div>
            <?php endif; ?>
        </div><!-- meta -->
        <div class="meanings">
            <?php echo str_replace(";", ", ", $entry['meanings']); ?>
        </div><!-- meanings -->
        <?php 
            if(!empty($entry['onReadings'])) { 
                echo '<div class="readings">';
                $onReadingsArray = explode(";", $entry['onReadings']);
                foreach($onReadingsArray as $onReading) {
                    echo '<div class="reading">'.$onReading.'</div>';
                }
                echo '</div><!-- readings -->';
            }
        ?>
        <?php 
            if(!empty($entry['kunReadings'])) { 
                echo '<div class="readings">';
                $kunReadingsArray = explode(";", $entry['kunReadings']);
                foreach($kunReadingsArray as $kunReading) {
                    echo '<div class="reading">'.$kunReading.'</div>';
                }
                echo '</div><!-- readings -->';
            }
        ?>
        <?php 
            if(!empty($entry['components'])) { 
                echo '<div class="components">';
                $componentsArray = explode(";", $entry['components']);
                foreach($componentsArray as $component) {
                    echo '<div class="component"><a href="index.php?q='.$component.'">'.$component.'</a></div>';
                }
                echo '</div><!-- components -->';
            }
        ?>
        <div class="story">
            Some text that represents the story
        </div><!-- readings -->
    </div><!-- right -->
</div><!-- card -->


    <?php endforeach; ?>
<?php endif;?>




</body>
</html>