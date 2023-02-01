<?php define('home', true); ?>

<?php require_once 'helper.php'; ?>

<?php
// db connection
$myPDO = new PDO('sqlite:data/kanjis.db');

// search
$entries = [];
if(isset($_GET['literal'])) {	
    if(empty($_GET['literal'])) {
        $error = "No selected kanji.";
    } else {
        $sql = "SELECT kanjis.*, kanjis_study.story, kanjis_study.score, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.literal = ?";
        $stmt = $myPDO->prepare($sql);
        $results = $stmt->execute([$_GET['literal']]); 
        $entry = $stmt->fetch();          
    }        
}
?>
<?php require './parts/header.php'; ?>

<?php if(!empty($error)): ?>

     <div class="error">
        <?php echo $error;?>
    </div>

<?php elseif(isset($_GET['literal'])) :?>
    <?php if(!$entry): ?>
        <div class="card empty">
            No results.
        </div>
    <?php else: ?>

        <div class="card <?php if($entry['added'] == 1) echo ' added'; ?>">
            <div class="left">
                <div class="kanji big-kanji"><?php echo $entry['literal']; ?></div>
                <?php if(isset($_GET['ref']) && $_GET['ref'] == 'review'): ?>
                <div class="rating">
                    <form action="actions/mark_difficulty.php" method="POST">
                        <input type="hidden" name="difficulty" value="easy">
                        <input type="hidden" name="literal" value="<?php echo $entry['literal']; ?>">
                        <button type="submit">Easy</button>
                    </form>
                    <form action="actions/mark_difficulty.php" method="POST">
                        <input type="hidden" name="difficulty" value="hard">
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
           
                <?php if(!empty($entry['other_forms'])): ?>
                <div class="other_forms">
                    ALSO 
                    <?php 
                        $other_forms = explode(";",$entry['other_forms']); 
                        foreach($other_forms as $other_form) {
                            echo '<a href="kanji.php?literal='.$other_form.'">'.$other_form.'</a>';
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
                            echo '<div class="component"><a href="kanji.php?literal='.$component.'">'.$component.'</a></div>';
                        }
                        echo '</div><!-- components -->';
                    }
                ?>
                <?php if(!empty($entry['story'])): ?>
                <div class="story">
                    <?php 
                        // links
                        $pattern = '/#(.+?)#/';
                        $story = preg_replace($pattern, '<a href="kanji.php?literal=$1">$1</a>',$entry['story']);
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
                    <div class="word" title="[<?php echo $word['hiragana']; ?>]: <?php echo $word['meaning']; ?>">
                        <span class="example-kanji"><?php echo $word['word']; ?></span><span class="example-text">[<?php echo $word['hiragana']; ?>]: <?php echo $word['meaning']; ?></span>                     
                    </div>
                    <?php endforeach; ?>
                </div><!-- words -->
                <?php endif; ?>

                <div class="edit" id="edit-area">
                    <form action="actions/update_kanji.php" method="POST">
                        <span>Components</span>
                        <input type="hidden" name="literal" value="<?php echo $entry['literal']; ?>">
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
                        <input type="hidden" name="literal" value="<?php echo $_GET['literal']; ?>">
                        <p><input type="text" name="word" placeholder="kanji;hiragana;english"><p>
                        <button type="submit">Add</button>
                    </form>
                </div>
            </div><!-- right -->
        
            <div class="action">
                <?php if($entry['added'] == '' || $entry['added'] == 0): ?>
                    <form action="actions/add_kanji_to_study.php" method="POST">
                        <input type="hidden" name="literal" value="<?php echo $entry['literal'];?>">
                        <button type="submit">add</button>
                    </form>
                <?php else: ?>
                    <form action="actions/remove_kanji_from_study.php" method="POST">
                        <input type="hidden" name="literal" value="<?php echo $entry['literal'];?>">
                        <button type="submit">remove</button>
                    </form>
                <?php endif; ?>
                <button id="edit-toggle">edit</button>
            </div><!-- action -->


        </div><!-- card -->
    <?php endif;?>
<?php else: ?>
    <div class="card empty">
        No kanji selected.
    </div>
<?php endif;?><!-- isset literal -->

<?php require './parts/footer.php'; ?>