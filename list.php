<?php define('home', true); ?>
<?php require './parts/header.php'; ?>

<?php if (isset($_GET['list'])): ?>
<?php
// db connection
$myPDO = new PDO('sqlite:data/kanjis.db');

if($_GET['list'] == 'jlpt') {
    $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.jlpt IS NOT NULL ORDER BY kanjis.jlpt DESC";
    $stmt = $myPDO->query($sql);
    $entries = $stmt->fetchAll();
}  elseif($_GET['list'] == 'heisg6') {
    $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.heisg6 IS NOT NULL ORDER BY kanjis.heisg6 ASC";
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
    $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis_study.added = 1";
    $stmt = $myPDO->query($sql);
    $entries = $stmt->fetchAll();
}  else {
    exit("No such list.");
}
?>

<div class="list">
    <?php if(!$entries): ?>
    No entries for this list.
    <?php else: ?>
        <div class="title">Showing <?php echo count($entries); ?> characters:</div>
        <?php foreach($entries as $entry): ?>
            <a href="kanji.php?literal=<?php echo $entry['literal']; ?>"<?php if($entry['added'] == 1) echo 'class="added"';?>>
                <?php echo $entry['literal']; ?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php else: ?>
    <div class="list">
        No list selected.
    </div>
<?php endif; ?>
                
<?php require './parts/footer.php'; ?>