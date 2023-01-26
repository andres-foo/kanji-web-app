<?php define('home', true); ?>

<?php
// db connection
$myPDO = new PDO('sqlite:data/kanjis.db');

// add word
if(isset($_POST['action']) && $_POST['action'] == 'add_word') {
    $sql = "INSERT INTO words (word, hiragana, meaning) VALUES (?,?,?)";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute(explode(";",$_POST['word']));
}

// search
$entries = [];
if(isset($_GET['query'])) {	
    $sql = "SELECT kanjis.*, kanjis_study.story, kanjis_study.score, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.literal = ? OR kanjis.meanings LIKE ?";
    $stmt = $myPDO->prepare($sql);
    $results = $stmt->execute([$_GET['query'], '%'.$_GET['query'].'%']);
    $entries = $stmt->fetchAll();
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
<?php require './parts/header.php'; ?>

    
    <?php if(isset($_GET['query']) || isset($_GET['review'])) :?>
        <?php if(!$entries): ?>
            No results.
        <?php else: ?>
            <?php foreach($entries as $entry): ?>

                <div class="card">
                    <div class="left">
                        <div class="kanji"><?php echo $entry['literal']; ?></div>
                        <?php if(isset($_GET['review'])): ?>
                        <div class="rating">
                            <form action="actions/mark_difficulty.php" method="POST">
                                <input type="hidden" name="difficulty" value="easy">
                                <input type="hidden" name="query" value="<?php echo $_GET['query']; ?>">
                                <input type="hidden" name="literal" value="<?php echo $entry['literal']; ?>">
                                <button type="submit">Easy</button>
                            </form>
                            <form action="actions/mark_difficulty.php" method="POST">
                                <input type="hidden" name="difficulty" value="hard">
                                <input type="hidden" name="query" value="<?php echo $_GET['query']; ?>">
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
                            <form action="actions/update_kanji.php" method="POST">
                                <input type="hidden" name="literal" value="<?php echo $entry['literal']; ?>">
                                <input type="hidden" name="query" value="<?php echo $_GET['query']; ?>">
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
                            <form action="actions/add_kanji_to_study.php" method="POST">
                                <input type="hidden" name="literal" value="<?php echo $entry['literal'];?>">                                
                                <input type="hidden" name="query" value="<?php echo $_GET['query']; ?>">
                                <button type="submit">add</button>
                            </form>
                        <?php else: ?>
                            <form action="actions/remove_kanji_from_study.php" method="POST">
                                <input type="hidden" name="literal" value="<?php echo $entry['literal'];?>">                                
                                <input type="hidden" name="query" value="<?php echo $_GET['query']; ?>">
                                <button type="submit">remove</button>
                            </form>
                        <?php endif; ?>
                    </div><!-- action -->


                </div><!-- card -->
            <?php endforeach; ?>
        <?php endif;?>
    <?php endif;?><!-- isset query -->
        
</div><!-- content -->

<?php require './parts/footer.php'; ?>