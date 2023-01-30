<?php define('home', true); ?>

<?php require_once 'helper.php'; ?>

<?php
// db connection
$myPDO = new PDO('sqlite:data/kanjis.db');

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
                
            $entries = $stmt->fetchAll(); 
            } else {
                if(isOnlyHiragana($_GET['query']) ) {
                    $hiragana = $_GET['query'];
                    $katakana = toKatakana($_GET['query']);
                    $sql = "SELECT kanjis.*, kanjis_study.story, kanjis_study.score, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.onReadings LIKE ? OR kanjis.kunReadings LIKE ?";
                    $stmt = $myPDO->prepare($sql);
                    $results = $stmt->execute(['%'.$katakana.'%','%'.$hiragana.'%']);                     
                    $entries = $stmt->fetchAll(); 
                } elseif(isOnlyKatakana($_GET['query'])) {
                    $hiragana = toHiragana($_GET['query']);
                    $katakana = $_GET['query'];
                    $sql = "SELECT kanjis.*, kanjis_study.story, kanjis_study.score, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.onReadings LIKE ? OR kanjis.kunReadings LIKE ?";
                    $stmt = $myPDO->prepare($sql);
                    $results = $stmt->execute(['%'.$katakana.'%','%'.$hiragana.'%']);                    
                    $entries = $stmt->fetchAll(); 
                } else {
                    $error = "When using kana only, use either hiragana or katakana but not both.";
                } 
            } 
        } else {
            if(strlen($_GET['query']) <= 2) {
                $error = "The query must be 3 characters minimum for English.";
            } else {
                $sql = <<<SQL
                SELECT 
                    kanjis.*,
                    kanjis_study.story,
                    kanjis_study.score,
                    kanjis_study.added
                FROM kanjis
                LEFT JOIN kanjis_study 
                ON kanjis.literal = kanjis_study.literal
                WHERE
                    meanings = ? OR
                    meanings LIKE ? OR 
                    meanings LIKE ? OR 
                    meanings LIKE ? OR
                    meanings LIKE ?
                ORDER BY CASE
                    WHEN meanings = ? THEN 1
                    WHEN meanings LIKE ? THEN 2
                    WHEN meanings LIKE ? THEN 3
                    WHEN meanings LIKE ? THEN 4
                    WHEN meanings LIKE ? THEN 5
                END
                SQL;
                $stmt = $myPDO->prepare($sql);
                $results = $stmt->execute([
                    $_GET['query'],
                    $_GET['query'].';%',
                    '%;'.$_GET['query'],
                    '%;'.$_GET['query'].';%',
                    '%'.$_GET['query'].'%',
                    $_GET['query'],
                    $_GET['query'].';%',
                    '%;'.$_GET['query'],
                    '%;'.$_GET['query'].';%',
                    '%'.$_GET['query'].'%'
                ]);
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
                <?php if(!isset($_SESSION['simple']) || $_SESSION['simple'] == 'off'): ?>
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
                <?php endif; ?>
                <?php if(!empty($entry['other_forms'])): ?>
                <div class="other_forms">
                    ALSO 
                    <?php 
                        $other_forms = explode(";",$entry['other_forms']); 
                        foreach($other_forms as $other_form) {
                            echo '<a href="index.php?query='.$other_form.'">'.$other_form.'</a>';
                        }
                    ?>
                </div>
                <?php endif; ?>
                <div class="meanings">
                    <?php echo str_replace(";", ", ", $entry['meanings']); ?>
                </div><!-- meanings -->
                <?php if(!isset($_SESSION['simple']) || $_SESSION['simple'] == 'off'): ?>
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
                <?php endif; //session for readings ?>
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
                        // links
                        $pattern = '/#(.+?)#/';
                        $story = preg_replace($pattern, '<a href="index.php?query=$1">$1</a>',$entry['story']);
                        // emphasis
                        $pattern = '/\_(.+?)\_/';
                        echo preg_replace($pattern, '<span>$1</span>',$story); 
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
                        <?php echo $word['word']; ?>
                        <?php if(!isset($_SESSION['simple']) || $_SESSION['simple'] == 'off'): ?>
                            [<?php echo $word['hiragana']; ?>]: <?php echo $word['meaning']; ?>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div><!-- words -->
                <?php endif; ?>

                <div class="edit" id="edit#<?php echo $entry['literal'];?>">
                    <form action="actions/update_kanji.php" method="POST">
                        <span>Components</span>
                        <input type="hidden" name="literal" value="<?php echo $entry['literal']; ?>">
                        <input type="hidden" name="query" value="<?php echo $_GET['query']; ?>">
                        <input type="text" name="components" value="<?php echo $entry['components']; ?>" placeholder="𠂇;口">
                        <span>Other forms</span>
                        <input type="text" name="otherForms" value="<?php echo $entry['other_forms']; ?>" placeholder="亻;人">
                        <span>Story (Use #日# to create links or _day_ for emphasis)</span>
                        <textarea rows="4" name="story"><?php echo $entry['story']; ?></textarea>
                        <button type="submit">Save changes</button>
                    </form>
                    <hr>
                    <span>Add an example</span>
                    <form action="actions/add_example.php" method="POST">                     
                        <input type="hidden" name="query" value="<?php echo $_GET['query']; ?>">
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
<?php else: ?>
    <div class="card empty rules">
        This is a simple app for studying kanjis and here are the rules:
        <h2>Searching</h2>
        The easiest way is to just type the kanji you're looking for, there're no radicals lookups or drawing since that's not the purpose of the app, but you can do the following:
        <ul>
            <li>You can pick a kanji from any list and select it (eg. <a href="index.php?query=日">日</a>)</li>
            <li>You can type in English and it will look within the meanings of the kanji (eg. <a href="index.php?query=sound">sound</a>)</li>
            <li>You can type a string that contains several kanjis (eg. <a href="index.php?query=日本語は...">日本語は...</a>) and it will look up all of them</li>
            <li>You can type in hiragana and it will look within the readings (both on and kun)(eg. <a href="index.php?query=なな.つ">なな.つ</a>)</li>
            <li>You can type a reading in English and the hiragana will be suggested (eg. <a href="index.php?query=nana.tsu">nana.tsu</a>)</li>
        </ul>

        <h2>Studying</h2>
        The studying portion is quite simple:
        <ol>
            <li>Find the kanjis you want to study</li>
            <li>Click on the <b>add</b> button on the top right of the card to add them to your list</li>
            <li>Click on the green <b>review</b> button and a kanji from your study list will be selected for review</li>
            <li>If the kanji that gets selected is easy or hard for you click on the corresponding button, otherwise just let it be</li>
        </ol>
        That's it, if you have more time keep clicking on the <b>review</b> button to review another kanji.

        <h2>How are kanji selected for review?</h2>
        There's a very simple system in place that's based on scores, the rules are as follow:
        <ul>
            <li>Each kanji begins with a score of zero</li>
            <li>Clicking on the <b>review</b> button will select a kanji with a low score</li>
            <li>Just by being selected by the review, one point will be added to the score of the kanji</li>
            <li>Clicking on easy, will add two additional points to the score of the kanji</li>
            <li>Clicking on hard, will substract two points from the score of the kanji</li>
        </ul>

        <h2>What about the editing part?</h2>
        Some parts are in place to standardize the kanjis and other parts are for studying:
        <ul>
            <li><b>Components</b>: For the most part they're done so they don't need to be touched, but I have the editing in place to fine tune them as I study the kanjis.</li>
            <li><b>Other forms</b>: This is in place for kanjis that take different forms when used as components.</li>
            <li><b>Story</b>: This is a personal story that one can add to help remember the kanji</li>
            <li><b>Add an example</b>: This allows to add example words for kanjis. Adding a word that contains more than one kanji will add the example to all kanjis involved.</li>
        </ul>

        <h2>What's next?</h2>
        I contemplated adding a built in database for examples (words and/or phrases) like JMdict_e but I decided against it since I don't want this app to turn into a dictionary. For the mime_content_type 
        the app has everything I want it to have.


    </div>
<?php endif;?><!-- isset query -->

<?php require './parts/footer.php'; ?>