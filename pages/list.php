<?php define('home', true); ?>
<?php require '../parts/header.php'; ?>

<?php if (!isset($_GET['list'])) : ?>
    <div class="list">
        No list selected.
    </div>

<?php else : ?>

    <?php
    // db connection
    $myPDO = new PDO('sqlite:../data/kanjis.db');

    function loop_entries($entries)
    {
        $text = '';
        foreach ($entries as $entry) {
            $text .= '<a href="kanji.php?literal=' . $entry['literal'] . '"';
            if ($entry['added'] == 1) {
                $unfinished = ($entry['unfinished']) ? ' unfinished' : '';
                $text .= ' class="added' . $unfinished . '"' . ' title="Added: ' . $entry['added_at'] . ' • Score: ' . $entry['score'] . '"';
            }
            $text .= '>' . $entry['literal'] . '</a>';
        }
        return $text;
    }

    function text_jlpt($PDO, $level)
    {
        $sql = "SELECT literal, added, added_at, score, unfinished FROM kanjis WHERE jlpt = ?";
        $stmt = $PDO->prepare($sql);
        $result = $stmt->execute([$level]);
        $entries = $stmt->fetchAll();
        $text = '<div class="title">JLPT ' . $level . ' (' . count($entries) . ' characters):</div>';
        foreach ($entries as $entry) {
            $text .= '<a href="kanji.php?literal=' . $entry['literal'] . '"';
            if ($entry['added'] == 1) $text .= ' class="added"';
            $text .= '>' . $entry['literal'] . '</a>';
        }
        return $text;
    }

    function text_grade($PDO, $grade)
    {
        $sql = "SELECT literal, added, added_at, score, unfinished FROM kanjis WHERE grade = ?";
        $stmt = $PDO->prepare($sql);
        $result = $stmt->execute([$grade]);
        $entries = $stmt->fetchAll();
        if ($grade != 8) {
            $text = '<div class="title">PRIMARY SCHOOL - GRADE ' . $grade . ' (' . count($entries) . ' characters):</div>';
        } else {
            $text = '<div class="title">SECONDARY SCHOOL (' . count($entries) . ' characters):</div>';
        }
        $text .= loop_entries($entries);
        return $text;
    }

    function text_kanken($PDO, $grade)
    {
        $sql = "SELECT literal, added, added_at, score, unfinished FROM kanjis WHERE kanken = ?";
        $stmt = $PDO->prepare($sql);
        $result = $stmt->execute([$grade]);
        $entries = $stmt->fetchAll();
        if ($grade == 1.5) {
            $text = '<div class="title">KANKEN LEVEL PRE 1 (' . count($entries) . ' characters):</div>';
        } elseif ($grade == 2.5) {
            $text = '<div class="title">KANKEN LEVEL PRE 2 (' . count($entries) . ' characters):</div>';
        } else {
            $text = '<div class="title">KANKEN LEVEL ' . $grade . ' (' . count($entries) . ' characters):</div>';
        }
        $text .= loop_entries($entries);
        return $text;
    }

    ?>



    <div class="list">



        <?php if ($_GET['list'] == 'jlpt') : ?>

            <h1>JLPT</h1>

            <?php
            echo text_jlpt($myPDO, 5);
            echo text_jlpt($myPDO, 4);
            echo text_jlpt($myPDO, 3);
            echo text_jlpt($myPDO, 2);
            echo text_jlpt($myPDO, 1);
            ?>
        <?php elseif ($_GET['list'] == 'jouyou') : ?>

            <h1>JOUYOU</h1>

            <?php
            echo text_grade($myPDO, 1);
            echo text_grade($myPDO, 2);
            echo text_grade($myPDO, 3);
            echo text_grade($myPDO, 4);
            echo text_grade($myPDO, 5);
            echo text_grade($myPDO, 6);
            echo text_grade($myPDO, 8);
            ?>

        <?php elseif ($_GET['list'] == 'my_list') : ?>

            <h1>MY LIST</h1>

            <?php
            $sql = "SELECT literal, added, added_at, score, unfinished FROM kanjis WHERE added = 1 ORDER BY added_at DESC";
            $stmt = $myPDO->query($sql);
            $entries = $stmt->fetchAll();
            ?>
            <div class="title">MY STUDY LIST (<?php echo count($entries); ?> characters):</div>
            <?php if (count($entries) == 0) : ?>
                <p>You haven't added any kanjis yet! To do so click on the "<strong>Add</strong>" button on the top right of the page when viewing a kanji.</p>
            <?php else : ?>
                <?php echo loop_entries($entries); ?>
            <?php endif; ?>

        <?php elseif ($_GET['list'] == 'heisg6') : ?>

            <h1>HEISG6</h1>

            <?php
            $sql = "SELECT literal, added, added_at, score, unfinished FROM kanjis WHERE heisg6 IS NOT NULL ORDER BY heisg6 ASC";
            $stmt = $myPDO->query($sql);
            $entries = $stmt->fetchAll();
            ?>
            <div class="title">HEISG6 (<?php echo count($entries); ?> characters):</div>
            <?php echo loop_entries($entries); ?>

        <?php elseif ($_GET['list'] == 'frequency') : ?>

            <h1>FREQUENCY</h1>

            <?php
            $sql = "SELECT literal, added, added_at, score, unfinished FROM kanjis WHERE frequency IS NOT NULL ORDER BY frequency ASC";
            $stmt = $myPDO->query($sql);
            $entries = $stmt->fetchAll();
            ?>
            <div class="title">BY FREQUENCY (<?php echo count($entries); ?> characters):</div>
            <?php echo loop_entries($entries); ?>

        <?php elseif ($_GET['list'] == 'kanken') : ?>

            <h1>KANKEN</h1>

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

        <?php else : ?>
            No such list.
        <?php endif; //list == ? 
        ?>


    </div>


<?php endif; //isset list 
?>

<?php require '../parts/footer.php'; ?>