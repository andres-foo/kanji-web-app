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
<style>
/* FONTS */
@font-face {
    font-family: "KanjiStrokeOrders";
    src: url("./data/fonts/KanjiStrokeOrders_v4.004.ttf");
}
@font-face {
    font-family: "NotoSansJP";
    src: url("./data/fonts/NotoSansJP-Regular.otf");
}

body {
    background:#121212;
    color:#e4e4e4;
    font-family: "NotoSansJP";
}
/* card */
.card {
    background:#1e1e1e;
    display:flex;
    border-radius: 5px;
    box-shadow: 1px 2px 1px rgba(0, 0, 0, 0.1);
}
.card .right {
    display: flex;
    flex-direction: column;
    flex-grow: 1;    
    align-items: flex-start;

}
.card .left .kanji {
    font-family:"NotoSansJP";
    font-size:120pt;
    padding:10px;
}
.card .left .kanji:hover {
    font-family:"KanjiStrokeOrders";
}
.card .right .meta, .card .right .readings, .card .right .components {
    display:flex;
    flex-direction: row;
    justify-items: center;
    padding:10px 0;
}
.card .right .story {
    padding:10px 0;
    color:#959595;
}
.card .right .meta .item {
    margin:0 20px 0 0;
    color:#959595;
    font-size: 0.7em;
    text-transform: uppercase;
}
.card .right .meta .item .ref::after {
    content:': ';
}
.card .right .meanings {
    font-size: 1.4em;;
}
.card .right .readings .reading, .card .right .components .component a {
    background-color:#121212;
    margin-right:10px;
    padding:5px 10px;
    border-radius: 5px;    
    font-family: "NotoSansJP";
}

.card .right .components .component a {
    font-size: 1.4em;
    text-decoration: none;
    color:#bb85fe;
}
.card .right .components .component a:hover {
    color:#fff;
}
</style>