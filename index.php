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

// update components or story
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

// add word
if(isset($_POST['action']) && $_POST['action'] == 'add_word') {
    $sql = "INSERT INTO words (word, hiragana, meaning) VALUES (?,?,?)";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute(explode(";",$_POST['word']));
}

// mark as easy
if(isset($_POST['action']) && $_POST['action'] == 'easy') {
    // find current score
    $sql = "SELECT * FROM kanjis_study WHERE literal = ? LIMIT 1";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['literal']]);
    $entry = $stmt->fetch();

    $sql = "UPDATE kanjis_study SET score = ? WHERE literal = ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$entry['score']+2,$_POST['literal']]);
}
// mark as hard
if(isset($_POST['action']) && $_POST['action'] == 'hard') {
    // find current score
    $sql = "SELECT * FROM kanjis_study WHERE literal = ? LIMIT 1";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_POST['literal']]);
    $entry = $stmt->fetch();

    $sql = "UPDATE kanjis_study SET score = ? WHERE literal = ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$entry['score']-2,$_POST['literal']]);
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
    }  elseif($_GET['list'] == 'kyouiku') {
        $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.grade IS NOT NULL AND kanjis.grade <= 6 ORDER BY kanjis.grade ASC";
        $stmt = $myPDO->query($sql);
        $entries = $stmt->fetchAll();
    }  elseif($_GET['list'] == 'jouyou') {
        $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.grade IS NOT NULL AND kanjis.grade <= 8 ORDER BY kanjis.grade ASC";
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
} elseif( isset($_GET['review'])) {
    $sql = "SELECT kanjis.*, kanjis_study.story, kanjis_study.score, kanjis_study.added FROM kanjis JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis_study.added = 1 ORDER BY SCORE ASC";
    $stmt = $myPDO->query($sql);
    $entries = $stmt->fetchAll();
    if(!$entries) {
        exit("There are no kanjis to study.");
    } else {
        session_start();
        if(!isset($_SESSION['last10'])) {
            $entries = [$entries[0]];
            $_SESSION['last10'] = [$entries['literal']];
        } else {
            $worst = $entries[0];
            $found = false;
            foreach($entries as $entry) {
                if(!in_array($entry['literal'], $_SESSION['last10'])) {
                    $found = true;
                    $entries = [$entry];
                    break;
                }  
            }
            if(!$found) {
                $entries = [$worst];
            }
            array_unshift($_SESSION['last10'], $entries[0]['literal']);
            if(count($_SESSION['last10']) > 10) {
                array_pop($_SESSION['last10']);
            } 
        }
        $sql = "UPDATE kanjis_study SET score = ? WHERE literal = ?";
        $stmt = $myPDO->prepare($sql);
        $results = $stmt->execute([$entries[0]['score']+1, $entries[0]['literal']]);
        $query = $entries[0]['literal'];
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
            <a href="index.php?review" class="review">REVIEW</a>
            <a href="index.php?list=my_list"<?php if(isset($_GET['list']) && $_GET['list'] == 'my_list') echo ' class="selected"';?>>MY LIST</a>
            <a href="index.php?list=jlpt"<?php if(isset($_GET['list']) && $_GET['list'] == 'jlpt') echo ' class="selected"';?>>JLPT</a>
            <a href="index.php?list=kyouiku"<?php if(isset($_GET['list']) && $_GET['list'] == 'kyouiku') echo ' class="selected"';?> title="Elementary School">KYOUIKU</a>
            <a href="index.php?list=jouyou"<?php if(isset($_GET['list']) && $_GET['list'] == 'jouyou') echo ' class="selected"';?>>JOUYOU</a>
            <a href="index.php?list=heisg6"<?php if(isset($_GET['list']) && $_GET['list'] == 'heisg6') echo ' class="selected"';?>>HEISG6</a>
            <a href="index.php?list=frequency"<?php if(isset($_GET['list']) && $_GET['list'] == 'frequency') echo ' class="selected"';?>>FREQUENCY</a>
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
            <p>Showing <?php echo count($entries); ?> characters:</p>
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
            No results for <b><?php echo $query; ?></b>.
        <?php else: ?>
            <?php foreach($entries as $entry): ?>

                <div class="card">
                    <div class="left">
                        <div class="kanji"><?php echo $entry['literal']; ?></div>
                        <?php if(isset($_GET['review'])): ?>
                        <div class="rating">
                            <form action="index.php?q=<?php echo $query; ?>" method="POST">
                                <input type="hidden" name="action" value="easy">
                                <input type="hidden" name="literal" value="<?php echo $entry['literal']; ?>">
                                <button type="submit">Easy</button>
                            </form>
                            <form action="index.php?q=<?php echo $query; ?>" method="POST">
                                <input type="hidden" name="action" value="hard">
                                <input type="hidden" name="literal" value="<?php echo $entry['literal']; ?>">
                                <button type="submit">Hard</button>

                            </form>                              
                        </div>
                        <?php endif; ?>
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
                                <span class="number">
                                <?php 
                                    if($entry['grade'] <= 6) {
                                        echo $entry['grade'];
                                    } elseif($entry['grade'] == 8) {
                                        echo "HighSchool(Jouyou)";
                                    } elseif($entry['grade'] == 9) {
                                        echo "Jinmeiyou(Names)";
                                    } else {
                                        echo "Jinmeiyou(Names extra)";
                                    }
                                ?>
                                </span>
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
                        <?php if(!empty($entry['story'])): ?>
                        <div class="story">
                                <?php 
                                    $pattern = '/#(.+)#/';
                                    echo preg_replace($pattern, '<a href="index.php?q=$1">$1</a>',$entry['story']); 
                                ?>
                        </div><!-- story -->
                        <?php endif; ?>

                        <?php
                            $sql = "SELECT * FROM words WHERE word LIKE ?";
                            $stmt = $myPDO->prepare($sql);
                            $stmt->execute(['%'.$entry['literal'].'%']);
                            $words = $stmt->fetchAll();
                        ?>
                        <?php if(!empty($words)): ?>
                        <div class="words">
                            <?php foreach($words as $word): ?>
                            <div class="word">
                                <?php echo $word['word']; ?>[<?php echo $word['hiragana']; ?>]: <?php echo $word['meaning']; ?>
                            </div>
                            <?php endforeach; ?>
                        </div><!-- words -->
                        <?php endif; ?>

                        <button class="edit-toggle" data-toggle="edit#<?php echo $entry['literal'];?>">toggle edit</button>

                        <div class="edit" id="edit#<?php echo $entry['literal'];?>">
                            <span>Components</span>
                            <form action="index.php?q=<?php echo $query; ?>" method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="literal" value="<?php echo $entry['literal']; ?>">
                                <input type="text" name="components" value="<?php echo $entry['components']; ?>">
                                <span>Story</span>
                                <textarea rows="4" name="story"><?php echo $entry['story']; ?></textarea>
                                <button type="submit">Save changes</button>
                            </form>
                            <hr>
                            <span>Add a word</span>
                            <form action="index.php?q=<?php echo $query; ?>" method="POST">
                                <input type="hidden" name="action" value="add_word">
                                <input type="text" name="word" placeholder="kanji;hiragana;english">
                                <button type="submit">Add a word</button>
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