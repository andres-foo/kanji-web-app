<?php define('home', true); ?>

<?php require_once '../parts/helper.php'; ?>

<?php
// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

// search
$kanjis = [];
$examples = [];
if (isset($_GET['query'])) {
    if (empty($_GET['query'])) {
        $error = "Don't leave the search empty.";
    } else {
        // verify search is not same as last one
        $sql = "SELECT query FROM search_history ORDER BY id DESC LIMIT 1";
        $stmt = $myPDO->prepare($sql);
        $last_search = $stmt->execute() ? $stmt->fetch()['query'] : null;
        // save new search
        if ($last_search !== $_GET['query']) {
            $sql = "INSERT INTO search_history (query) VALUES (?)";
            $stmt = $myPDO->prepare($sql);
            $result = $stmt->execute([$_GET['query']]);
        }

        //if japanese
        if (itHasJapanese($_GET['query'])) {
            if (itHasKanji($_GET['query'])) {
                $kanjis = obtainKanjis($_GET['query'])[0];
                $qty = count($kanjis);
                $i = 0;
                $str = '';
                foreach ($kanjis as $kanji) {
                    $i++;
                    $str .= " literal = ?";
                    if ($i != $qty) $str .= " OR";
                }
                $sql = "SELECT * FROM kanjis WHERE " . $str;
                $stmt = $myPDO->prepare($sql);
                $results = $stmt->execute($kanjis);
                $kanjis = $stmt->fetchAll();

                // examples
                $sql = "SELECT * FROM examples WHERE kanji LIKE ?";
                $stmt = $myPDO->prepare($sql);
                $results = $stmt->execute(['%' . $_GET['query'] . '%']);
                $examples = $stmt->fetchAll();
            } else {
                if (isOnlyHiragana($_GET['query'])) {
                    $hiragana = $_GET['query'];
                    $katakana = toKatakana($_GET['query']);
                    $sql = "SELECT * FROM kanjis WHERE onReadings LIKE ? OR kunReadings LIKE ?";
                    $stmt = $myPDO->prepare($sql);
                    $results = $stmt->execute(['%' . $katakana . '%', '%' . $hiragana . '%']);
                    $kanjis = $stmt->fetchAll();

                    // examples
                    $sql = "SELECT * FROM examples WHERE kana LIKE ?";
                    $stmt = $myPDO->prepare($sql);
                    $results = $stmt->execute(['%' . $_GET['query'] . '%']);
                    $examples = $stmt->fetchAll();
                } elseif (isOnlyKatakana($_GET['query'])) {
                    $hiragana = toHiragana($_GET['query']);
                    $katakana = $_GET['query'];
                    $sql = "SELECT * FROM kanjis WHERE onReadings LIKE ? OR kunReadings LIKE ?";
                    $stmt = $myPDO->prepare($sql);
                    $results = $stmt->execute(['%' . $katakana . '%', '%' . $hiragana . '%']);
                    $kanjis = $stmt->fetchAll();
                    // examples
                    $sql = "SELECT * FROM examples WHERE kana LIKE ?";
                    $stmt = $myPDO->prepare($sql);
                    $results = $stmt->execute(['%' . $_GET['query'] . '%']);
                    $examples = $stmt->fetchAll();
                } else {
                    $error = "When using kana only, use either hiragana or katakana but not both.";
                }
            }
        } else {
            if (strlen($_GET['query']) <= 2) {
                $error = "The query must be 3 characters minimum for English.";
            } else {
                $sql = <<<SQL
                SELECT 
                    *
                FROM kanjis
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
                    $_GET['query'] . ';%',
                    '%;' . $_GET['query'],
                    '%;' . $_GET['query'] . ';%',
                    '%' . $_GET['query'] . '%',
                    $_GET['query'],
                    $_GET['query'] . ';%',
                    '%;' . $_GET['query'],
                    '%;' . $_GET['query'] . ';%',
                    '%' . $_GET['query'] . '%'
                ]);
                $kanjis = $stmt->fetchAll();

                // examples
                $sql = "SELECT * FROM examples WHERE meanings LIKE ?";
                $stmt = $myPDO->prepare($sql);
                $results = $stmt->execute(['%' . $_GET['query'] . '%']);
                $examples = $stmt->fetchAll();
            }
        }
    }
}

?>
<?php require '../parts/header.php'; ?>

<?php if (!empty($error)) : ?>

    <div class="error">
        <?php echo $error; ?>
    </div>

<?php elseif (isset($_GET['query'])) : ?>
    <?php if (!$kanjis) : ?>
        <div class="card empty">
            No kanjis found.
        </div>
    <?php else : ?>

        <div class="title"><?php echo count($kanjis); ?> kanjis found<?php if (!empty($examples)) echo ' <a href="#examples">↓ see ' . count($examples) . ' examples</a>'; ?></div>

        <?php foreach ($kanjis as $entry) : ?>

            <div class="card search<?php if ($entry['added'] == 1) echo ' added'; ?>">
                <div class="left">
                    <div class="kanji"><a href="kanji.php?literal=<?php echo $entry['literal']; ?>"><?php echo $entry['literal']; ?></a></div>
                </div><!-- left -->
                <div class="right">
                    <div class="meanings">
                        <?php echo str_replace(";", ", ", $entry['meanings']); ?>
                    </div><!-- meanings -->
                    <?php
                    if (!empty($entry['onReadings'])) {
                        echo '<div class="readings">';
                        $onReadingsArray = explode(";", $entry['onReadings']);
                        foreach ($onReadingsArray as $onReading) {
                            echo '<div class="reading">' . $onReading . '</div>';
                        }
                        echo '</div><!-- readings -->';
                    }
                    ?>
                    <?php
                    if (!empty($entry['kunReadings'])) {
                        echo '<div class="readings">';
                        $kunReadingsArray = explode(";", $entry['kunReadings']);
                        foreach ($kunReadingsArray as $kunReading) {
                            echo '<div class="reading">' . $kunReading . '</div>';
                        }
                        echo '</div><!-- readings -->';
                    }
                    ?>
                </div><!-- right -->

            </div><!-- card -->
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($examples)) : ?>
        <div class="title" id="examples"><?php echo count($examples); ?> examples found</div>
        <?php foreach ($examples as $example) : ?>
            <div class="card search search-word<?php if ($example['added'] == 1) echo ' added'; ?>">
                <?php echo '<a href="search.php?query=' . $example['kanji'] . '">' . $example['kanji'] . '</a>「' . $example['kana'] . '」'; ?> (jlpt<?php echo $example['jlpt']; ?>)
                <?php echo $example['meanings']; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?><!-- isset query -->

<?php require '../parts/footer.php'; ?>