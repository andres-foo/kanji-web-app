<?php define("home", true); ?>

<?php
// only for parse_story
require "../parts/helper.php";

// db connection
$myPDO = new PDO("sqlite:../data/kanjis.db");

// search
if (isset($_GET["literal"])) {
    if (empty($_GET["literal"])) {
        $error = "No selected kanji.";
    } else {
        $sql = "SELECT * FROM kanjis WHERE literal = ? LIMIT 1";
        $stmt = $myPDO->prepare($sql);
        $results = $stmt->execute([$_GET["literal"]]);
        $entry = $stmt->fetch();
    }
}

// get total added to properly mark jlpt levels and kanken levels with different colors of importance
$sql = "SELECT COUNT(*) FROM kanjis WHERE added = 1 AND is_component IS NULL";
$stmt = $myPDO->prepare($sql);
$stmt->execute();
$totalKnown = $stmt->fetchColumn();
?>

<?php require "../parts/header.php"; ?>

<?php if (isset($_GET["literal"]) && $entry): ?>
    <div class="card">
        <div class="review-options">
            <form action="../actions/mark_difficulty.php" class="review-scoring" method="POST">
                <button class="review-good" name="review_good">⮝</button>
                <a href="./kanji.php?literal=<?= $_GET["literal"] ?>" class="review-neutral">🗘</a>
                <button class="review-bad" name="review_bad">⮟</button>
                <input type="hidden" name="literal" value="<?php echo $entry["literal"]; ?>">
            </form>
        </div>

        <div class="review-card">
            <div class="kanji">
                <?php echo $entry["literal"]; ?>
            </div>

            <?php
            $sql =
                "SELECT * FROM examples WHERE kanji != '' AND kanji LIKE ? AND added = 1";
            $stmt = $myPDO->prepare($sql);
            $stmt->execute(["%" . $entry["literal"] . "%"]);
            $examples = $stmt->fetchAll();
            ?>
            <?php if (!empty($examples)): ?>
                <div class="words">
                    <!-- priority examples -->
                    <?php foreach ($examples as $example): ?>
                        <span class="word"><?php echo formatKanjis(
                                                $example["kanji"],
                                            ); ?></span>
                    <?php endforeach; ?>
                </div><!-- words -->
            <?php endif; ?>

            <?php
            $sql =
                "SELECT * FROM phrases WHERE phrase LIKE ?";
            $stmt = $myPDO->prepare($sql);
            $stmt->execute(["%" . $entry["literal"] . "%"]);
            $phrases = $stmt->fetchAll();
            ?>
            <?php if (!empty($phrases)): ?>
                <div class="phrases">
                    <!-- priority examples -->
                    <?php foreach ($phrases as $phrase): ?>
                        <span class="phrase"><?= $phrase["phrase"] ?></span>
                    <?php endforeach; ?>
                </div><!-- words -->
            <?php endif; ?>
        </div><!-- flex-colum -->
    </div><!-- card -->

<?php endif; ?>


<script src="../data/script.js"></script>

<?php require "../parts/footer.php"; ?>