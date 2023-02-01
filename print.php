<?php define('home', true); ?>

<?php require_once 'helper.php'; ?>

<?php
// db connection
$myPDO = new PDO('sqlite:data/kanjis.db');

// search
$sql = "SELECT kanjis.*, kanjis_study.story, kanjis_study.score, kanjis_study.added FROM kanjis JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis_study.added = 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute();
$entries = $stmt->fetchAll();

?>
        
        
<style>
@font-face {
    font-family: "NotoSansJP";
    src: url("./fonts/NotoSansJP-Regular.otf");
}
* {
    box-sizing: border-box;
}

.print {
    display:flex;
    flex-wrap: wrap;
    justify-content: space-evenly;
}
.print-row {
    border:1px solid #EFEFEF;
    display:flex;
    font-family: "NotoSansJP";
    width:48%;
    margin-bottom:10px;
    page-break-inside: avoid;
}
.print-literal {
    font-size:2.5em;
    padding:10px;
}
.print-meanings {
    font-size:1.2em;
}
.print-extras {
    display: flex;
    flex-direction: column;
    width:100%;
    padding:10px;
}
</style>

<?php if(!$entries): ?>
        No results.
<?php else: ?>
    <div class="print">
    <?php foreach($entries as $entry): ?>
        <div class="print-row">
            <div class="print-literal">
                <?php echo $entry['literal']; ?>
            </div>
            <div class="print-extras">      
                <div class="print-meanings">
                    <?php echo str_replace(";", ", ", $entry['meanings']); ?>
                </div>
                <?php if(!empty($entry['story'])): ?>
                <div class="print-story">
                <?php 
                    // links
                    $pattern = '/#(.+?)#/';
                    $story = preg_replace($pattern, '$1',$entry['story']);
                    // emphasis
                    $pattern = '/\_(.+?)\_/';
                    echo preg_replace($pattern, '$1',$story); 
                ?>
                </div><!-- print-story -->
                <?php endif; //story ?>

                <?php
                    $sql = "SELECT * FROM words WHERE word LIKE ?";
                    $stmt = $myPDO->prepare($sql);
                    $stmt->execute(['%'.$entry['literal'].'%']);
                    $words = $stmt->fetchAll();
                ?>
                <?php if(!empty($words)): ?>
                <div class="print-examples">
                    <?php foreach($words as $word): ?>
                    <?php echo $word['word'].'['.$word['hiragana'].']'; ?>
                    <?php endforeach; ?>
                </div><!-- print-examples -->
                <?php endif; ?>

            </div><!-- print-extras -->
            </div><!-- print-row -->
    <?php endforeach; ?>    
    </div>
<?php endif;?>