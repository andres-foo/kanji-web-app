<?php define('home', true); ?>
<?php require '../parts/header.php'; ?>

<?php if (!isset($_GET['list'])): ?>
<div class="list">
    No list selected.
</div>

<?php else: ?>

<?php
// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

function loop_entries($entries) {
    $text = '';
    foreach($entries as $entry) {
        $text .= '<a href="kanji.php?literal='.$entry['literal'].'"';
        if($entry['added'] == 1) $text .= ' class="added"';
        $text .= '>'.$entry['literal'].'</a>'; 
    }
    return $text;
}

function text_jlpt($PDO, $level) {
    $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.jlpt = ?";
    $stmt = $PDO->prepare($sql);
    $result = $stmt->execute([$level]);
    $entries = $stmt->fetchAll();
    $text = '<div class="title">JLPT '.$level.' ('.count($entries).' characters):</div>';
    foreach($entries as $entry) {
        $text .= '<a href="kanji.php?literal='.$entry['literal'].'"';
        if($entry['added'] == 1) $text .= ' class="added"';
        $text .= '>'.$entry['literal'].'</a>'; 
    }
    return $text;
}

function text_grade($PDO, $grade) {
    $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.grade = ?";
    $stmt = $PDO->prepare($sql);
    $result = $stmt->execute([$grade]);
    $entries = $stmt->fetchAll();
    if($grade != 8) {
        $text = '<div class="title">PRIMARY SCHOOL - GRADE '.$grade.' ('.count($entries).' characters):</div>';
    } else {
        $text = '<div class="title">SECONDARY SCHOOL ('.count($entries).' characters):</div>';
    }
    $text .= loop_entries($entries);
    return $text;
}

function text_kanken($PDO, $grade) {
    $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.kanken = ?";
    $stmt = $PDO->prepare($sql);
    $result = $stmt->execute([$grade]);
    $entries = $stmt->fetchAll();
    if($grade == 1.5) {
        $text = '<div class="title">KANKEN LEVEL PRE 1 ('.count($entries).' characters):</div>';
    } elseif($grade == 2.5) {
        $text = '<div class="title">KANKEN LEVEL PRE 2 ('.count($entries).' characters):</div>';
    } else {
        $text = '<div class="title">KANKEN LEVEL '.$grade.' ('.count($entries).' characters):</div>';
    }
    $text .= loop_entries($entries);
    return $text;
}

?>



<div class="list">
    <?php if($_GET['list'] == 'jlpt'): ?> 
        <?php 
            echo text_jlpt($myPDO, 5);
            echo text_jlpt($myPDO, 4);
            echo text_jlpt($myPDO, 3);
            echo text_jlpt($myPDO, 2);
            echo text_jlpt($myPDO, 1);
        ?>
    <?php elseif($_GET['list'] == 'jouyou') :?>
        <?php 
            echo text_grade($myPDO, 1);
            echo text_grade($myPDO, 2);
            echo text_grade($myPDO, 3);
            echo text_grade($myPDO, 4);
            echo text_grade($myPDO, 5);
            echo text_grade($myPDO, 6);
            echo text_grade($myPDO, 8);
        ?>     
       
    <?php elseif($_GET['list'] == 'my_list'): ?>
        <?php
            $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis_study.added = 1";
            $stmt = $myPDO->query($sql);
            $entries = $stmt->fetchAll();
        ?>
        <div class="title">MY STUDY LIST (<?php echo count($entries); ?> characters):</div>
        <?php echo loop_entries($entries); ?>

    <?php elseif($_GET['list'] == 'heisg6'): ?>
        <?php
            $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.heisg6 IS NOT NULL ORDER BY kanjis.heisg6 ASC";
            $stmt = $myPDO->query($sql);
            $entries = $stmt->fetchAll();
        ?>
        <div class="title">HEISG6 (<?php echo count($entries); ?> characters):</div>
        <?php echo loop_entries($entries); ?>

    <?php elseif($_GET['list'] == 'frequency'): ?>
        <?php
            $sql = "SELECT kanjis.literal, kanjis_study.added FROM kanjis LEFT JOIN kanjis_study ON kanjis.literal = kanjis_study.literal WHERE kanjis.frequency IS NOT NULL ORDER BY kanjis.frequency ASC";
            $stmt = $myPDO->query($sql);
            $entries = $stmt->fetchAll();
        ?>
        <div class="title">BY FREQUENCY (<?php echo count($entries); ?> characters):</div>
        <?php echo loop_entries($entries); ?>

    <?php elseif($_GET['list'] == 'kanken'): ?>
        <?php 
            echo text_kanken($myPDO, 10);
            echo text_kanken($myPDO, 9);
            echo text_kanken($myPDO, 8);
            echo text_kanken($myPDO, 7);
            echo text_kanken($myPDO, 6);
            echo text_kanken($myPDO, 5);
            echo text_kanken($myPDO, 4);
            echo text_kanken($myPDO, 3);
            echo text_kanken($myPDO, 2.5);
            echo text_kanken($myPDO, 2);
            echo text_kanken($myPDO, 1.5);
            echo text_kanken($myPDO, 1);
        ?>

    <?php else: ?>
            No such list.
    <?php endif; //list == ? ?>


</div>


<?php endif; //isset list ?>
                
<?php require '../parts/footer.php'; ?>