<?php define('home', true); ?>

<?php require 'helper.php'; ?>

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
    if(empty($_GET['query'])) {
        $error = "Don't leave the search empty.";
    } else {
        //if japanese
        if(itHasJapanese($_GET['query'])) {
            if(itHasKanji($_GET['query'])) {
                $kanjis = obtainKanjis($_GET['query'])[0];
                $qty = count($kanjis);
                $i = 0;
                $str = '';
                foreach($kanjis as $kanji) {
                    $i++;
                    $str .= " kanjis.literal = ?";
                    if($i != $qty) $str .= " OR";
                }
                $sql = "SELECT kanjis.*, kanjis_study.story, kanjis_study.score, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE " . $str;
                $stmt = $myPDO->prepare($sql);
                $results = $stmt->execute($kanjis);
            } else {
                $sql = "SELECT kanjis.*, kanjis_study.story, kanjis_study.score, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.onReadings LIKE ? OR kanjis.kunReadings LIKE ?";
                $stmt = $myPDO->prepare($sql);
                $results = $stmt->execute(['%'.$_GET['query'].'%','%'.$_GET['query'].'%']);
            }
            $entries = $stmt->fetchAll();  
        } else {
            if(strlen($_GET['query']) <= 2) {
                $error = "The query must be 3 characters minimum for English.";
            } else {
                $sql = "SELECT kanjis.*, kanjis_study.story, kanjis_study.score, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.meanings LIKE ?";
                $stmt = $myPDO->prepare($sql);
                $results = $stmt->execute(['%'.$_GET['query'].'%']);
                $entries = $stmt->fetchAll();  
            }
        }
        
    }

}

?>
<?php require './parts/header.php'; ?>

<?php if(!empty($error)): ?>

     <div class="error">
        <?php echo $error;?>
    </div>

<?php elseif(isset($_GET['query'])) :?>
    <?php if(!$entries): ?>
        <div class="card empty">
            No results.
        </div>
    <?php else: ?>
        <?php foreach($entries as $entry): ?>

        <div class="card <?php if($entry['added'] == 1) echo ' added'; ?>">
            <div class="left">
                <div class="kanji"><?php echo $entry['literal']; ?></div>
                <?php if(isset($_GET['ref']) && $_GET['ref'] == 'review'): ?>
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
                            echo '<div class="component"><a href="index.php?query='.$component.'">'.$component.'</a></div>';
                        }
                        echo '</div><!-- components -->';
                    }
                ?>
                <?php if(!empty($entry['story'])): ?>
                <div class="story">
                        <?php 
                            $pattern = '/#(.+)#/';
                            echo preg_replace($pattern, '<a href="index.php?query=$1">$1</a>',$entry['story']); 
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

                <div class="edit" id="edit#<?php echo $entry['literal'];?>">
                    <span>Components</span>
                    <form action="actions/update_kanji.php" method="POST">
                        <input type="hidden" name="literal" value="<?php echo $entry['literal']; ?>">
                        <input type="hidden" name="query" value="<?php echo $_GET['query']; ?>">
                        <input type="text" name="components" value="<?php echo $entry['components']; ?>">
                        <span>Story (Use #日# to create links)</span>
                        <textarea rows="4" name="story"><?php echo $entry['story']; ?></textarea>
                        <button type="submit">Save changes</button>
                    </form>
                    <hr>
                    <span>Add an example</span>
                    <form action="index.php?q=<?php echo $query; ?>" method="POST">
                        <input type="hidden" name="action" value="add_word">
                        <p><input type="text" name="word" placeholder="kanji;hiragana;english"><p>
                        <button type="submit">Add</button>
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
                <button class="edit-toggle" data-toggle="edit#<?php echo $entry['literal'];?>">edit</button>
            </div><!-- action -->


        </div><!-- card -->
        <?php endforeach; ?>
    <?php endif;?>
<?php endif;?><!-- isset query -->

<?php require './parts/footer.php'; ?>