<?php

// db connection
$myPDO = new PDO('sqlite:data/kanjis.db');



// add kanji to study
if(isset($_POST['action']) && $_POST['action'] == 'add') {
    //$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    //check if exists
    $sql = "SELECT * FROM kanjis_study WHERE literal = ? LIMIT 1";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['literal']]);
    $entry = $stmt->fetch();

    if($entry) {
        //exists so must update
        $sql = "UPDATE kanjis_study SET added = 1 WHERE literal = ?";
        $stmt = $myPDO->prepare($sql);
        $results = $stmt->execute([$_POST['literal']]);
    } else {
        // does not exists, must be created
        $sql = "INSERT INTO kanjis_study (literal, score, story, added) VALUES (?,?,?,?)";
        $stmt = $myPDO->prepare($sql);
        $results = $stmt->execute([$_POST['literal'],0,'',1]);
    }
}

// remove kanji to study
if(isset($_POST['action']) && $_POST['action'] == 'remove') {
        //exists so must update
        $sql = "UPDATE kanjis_study SET added = '0' WHERE literal = ?";
        $stmt = $myPDO->prepare($sql);
        $results = $stmt->execute([$_POST['literal']]);
}

// add kanji to study
if(isset($_POST['action']) && $_POST['action'] == 'update') {
    //components
    $sql = "UPDATE kanjis SET components = ? WHERE literal = ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['components'],$_POST['literal']]);

    //story
    //$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    //check if exists
    $sql = "SELECT * FROM kanjis_study WHERE literal = ? LIMIT 1";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['literal']]);
    $entry = $stmt->fetch();

    if($entry) {        
        //exists so must update the story
        $sql = "UPDATE kanjis_study SET story = ? WHERE literal = ?";
        $stmt = $myPDO->prepare($sql);
        $results = $stmt->execute([$_POST['story'],$_POST['literal']]);
    } else {
        // does not exists, must be created
        $sql = "INSERT INTO kanjis_study (literal, score, story, added) VALUES (?,?,?,?)";
        $stmt = $myPDO->prepare($sql);
        $results = $stmt->execute([$_POST['literal'],0,$_POST['story'],0]);
    }
}


// search
$entries = [];
if(isset($_GET['q'])) {	
    $query = $_GET['q'];
    //$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $sql = "SELECT kanjis.*, kanjis_study.story, kanjis_study.score, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.literal = ? OR kanjis.meanings LIKE ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_GET['q'], '%'.$_GET['q'].'%']);
    $entries = $stmt->fetchAll();
    // echo "<pre>";
    // var_dump($entries);
    // echo "</pre>";
} elseif (isset($_GET['list'])) {
    if($_GET['list'] == 'jlpt') {
        $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.jlpt IS NOT NULL ORDER BY kanjis.jlpt DESC";
        $stmt = $myPDO->query($sql);
        $entries = $stmt->fetchAll();
    }  elseif($_GET['list'] == 'heisg6') {
        $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.heisg6 IS NOT NULL ORDER BY kanjis.heisg6 ASC";
        $stmt = $myPDO->query($sql);
        $entries = $stmt->fetchAll();
    }  elseif($_GET['list'] == 'grade') {
        $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.grade IS NOT NULL ORDER BY kanjis.grade ASC";
        $stmt = $myPDO->query($sql);
        $entries = $stmt->fetchAll();
    }  elseif($_GET['list'] == 'frequency') {
        $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.frequency IS NOT NULL ORDER BY kanjis.frequency ASC";
        $stmt = $myPDO->query($sql);
        $entries = $stmt->fetchAll();
    }  elseif($_GET['list'] == 'my_list') {
        $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis_study.added = 1 ORDER BY kanjis.frequency ASC";
        $stmt = $myPDO->query($sql);
        $entries = $stmt->fetchAll();
    }
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
            <a href="My kanjis" class="review">REVIEW</a>
            <a href="index.php?list=my_list">MY LIST</a>
            <a href="index.php?list=jlpt">JLPT</a>
            <a href="index.php?list=grade">GRADE</a>
            <a href="index.php?list=heisg6">HEISG6</a>
            <a href="index.php?list=frequency">FREQUENCY</a>
        </div>
        <div class="header-form">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="search" placeholder="Search by literal, readings or meanings" name=q value="<?php if(isset($query)) echo $query;?>">
            </form>
        </div>

    </div>

    <?php if(isset($_GET['list'])) :?>
    <div class="list">
        <?php if(!$entries): ?>
            No entries for this list.
        <?php else: ?>
            <?php foreach($entries as $entry): ?>
                <a href="index.php?q=<?php echo $entry['literal']; ?>"<?php if($entry['added'] == 1) echo 'class="added"';?>>
                    <?php echo $entry['literal']; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if(isset($query)) :?>
        <?php if(!$entries): ?>
            No results for <b>$query</b>.
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
                            <?php if(!empty($entry['story'])): ?>
                                <?php 
                                    $pattern = '/#(.+)#/';
                                    echo preg_replace($pattern, '<a href="index.php?q=$1">$1</a>',$entry['story']); 
                                ?>
                            <?php endif; ?>
                        </div><!-- readings -->

                        <button class="edit-toggle" data-toggle="edit#<?php echo $entry['literal'];?>">toggle edit</button>

                        <div class="edit" id="edit#<?php echo $entry['literal'];?>">
                            <span>Components</span>
                            <form action="index.php?q=<?php echo $query; ?>" method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="literal" value="<?php echo $entry['literal']; ?>">
                                <input type="text" name="components" value="<?php echo $entry['components']; ?>">
                                <span>Story</span>
                                <textarea rows="4" name="story"><?php echo $entry['story']; ?></textarea>
                                <button type="submit">Save</button>
                        </form>
                        </div>
                    </div><!-- right -->
                
                    <div class="action">
                        <?php if($entry['added'] == '' || $entry['added'] == 0): ?>
                            <form action="index.php?q=<?php echo $query; ?>" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="literal" value="<?php echo $entry['literal'];?>">
                                <button type="submit">add</button>
                            </form>
                        <?php else: ?>
                            <form action="index.php?q=<?php echo $query; ?>" method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="literal" value="<?php echo $entry['literal'];?>">
                                <button type="submit">remove</button>
                            </form>
                        <?php endif; ?>
                    </div><!-- action -->


                </div><!-- card -->
            <?php endforeach; ?>
        <?php endif;?>
    <?php endif;?><!-- isset query -->
        
</div><!-- content -->

<script src="data/script.js"></script>

</body>
</html>