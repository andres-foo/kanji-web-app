<?php define("home", true); ?>

<?php
// only for parse_story
require "../parts/helper.php";

// db connection
$myPDO = new PDO("sqlite:../data/kanjis.db");

// search
$entries = [];
if (isset($_GET["literal"])) {
    if (empty($_GET["literal"])) {
        $error = "No selected kanji.";
    } else {
        $sql = "SELECT * FROM kanjis WHERE literal = ?";
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

<?php if (!empty($error)): ?>

    <div class="error">
        <?php echo $error; ?>
    </div>

<?php elseif (isset($_GET["literal"])): ?>
    <?php if (!$entry): ?>
        <div class="card empty">
            No results.
        </div>
    <?php else: ?>

        <div class="card <?php
                            if ($entry["added"] == 1) {
                                echo " added";
                            }
                            if ($entry["unfinished"] == 1) {
                                echo " unfinished";
                            }
                            ?>">
            <?php if ($entry["is_component"]): ?>
                <div class="component-flag">basic component</div>
            <?php endif; ?>

            <div class="left<?= $entry["ignore"] == 1 ? " ignore" : "" ?>">
                <div class="kanji"><?php echo $entry["literal"]; ?></div>
                <?php if (!empty($entry["components"])) {
                    echo '<div class="components">';
                    $componentsArray = explode(";", $entry["components"]);
                    foreach ($componentsArray as $component) {
                        echo "<a href='kanji.php?literal={$component}'>{$component}</a>";
                    }
                    echo "</div><!-- components -->";
                } ?>
                <div class="big-kanji"><?php echo $entry["literal"]; ?></div>

            </div><!-- left -->
            <div class="right">

                <div class="extras">
                    <?php if (!empty($entry["jlpt"]) || !empty($entry["grade"])): ?>
                        <?php if (!empty($entry["jlpt"])): ?>
                            <div class="kanji-tag <?= getImportanceJLPT(
                                                        $entry["jlpt"],
                                                        $totalKnown,
                                                    ) ?>">N<?= $entry["jlpt"] ?></div>
                        <?php endif; ?><!-- jlpt -->

                        <?php if (!empty($entry["kanken"])): ?>
                            <div class="kanji-tag <?= getImportanceKANKEN(
                                                        $entry["kanken"],
                                                        $totalKnown,
                                                    ) ?>">K<?= $entry["kanken"] ?></div>
                        <?php endif; ?><!-- kanken -->
                    <?php endif; ?>
                    <?php if (!empty($entry["other_forms"])): ?>
                        <div>
                            aka
                            <?php
                            $other_forms = explode(";", $entry["other_forms"]);
                            foreach ($other_forms as $other_form) {
                                echo "<a href='kanji.php?literal={$other_form}'>{$other_form}</a>";
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($entry["related"])): ?>
                        <div>
                            see
                            <?php
                            $relatedArray = explode(";", $entry["related"]);
                            foreach ($relatedArray as $related) {
                                echo "<a href='kanji.php?literal={$related}'>{$related}</a>";
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <a href="https://www.kanshudo.com/kanji/<?php echo $entry["literal"]; ?>" target="_blank">↗ kanshudo</a> •
                        <a href="https://en.wiktionary.org/wiki/<?php echo $entry["literal"]; ?>" target="_blank">↗ wiki</a>
                    </div>
                    <div>
                        score: <?= $entry["score"] ?>
                    </div>
                    <div>
                        <form class="kanji-actions" action="../actions/toggle_kanji_study.php" method="POST">
                            <input type="hidden" name="literal" value="<?php echo $entry["literal"]; ?>">
                            <button type="submit"><?= ($entry["added"] == 1) ? "⏻ remove" : "⏻ add" ?></button>
                        </form>&nbsp;&nbsp;• <a id="edit-toggle" href="#edit-area">✎ edit</a>
                    </div>
                </div><!-- /extra -->

                <div class="meanings">
                    <?php echo str_replace(";", ", ", $entry["meanings"]); ?>
                </div><!-- meanings -->

                <?php if (
                    !empty($entry["onReadings"]) or
                    !empty($entry["kunReadings"])
                ) {
                    echo '<div class="readings">';
                    echo str_replace(
                        ";",
                        "&nbsp;&nbsp;/&nbsp;&nbsp;",
                        $entry["onReadings"],
                    );
                    if (!empty($entry["kunReadings"])) {
                        if (!empty($entry["onReadings"])) {
                            echo "&nbsp;&nbsp;•&nbsp;&nbsp;";
                        }
                        echo str_replace(
                            ";",
                            "&nbsp;&nbsp;/&nbsp;&nbsp;",
                            $entry["kunReadings"],
                        );
                    }
                    echo "</div><!-- readings -->";
                } ?>

                <div class="story-plus-image">
                    <!-- image -->
                    <?php
                    $img = "../data/images/" . $entry["literal"] . ".jpg";
                    if (file_exists($img)) {
                        echo "<a href='{$img}' target='_blank' class='story-image'><img src='{$img}'></a>";
                    }
                    ?>

                    <?php if (!empty($entry["story"])): ?>
                        <div class="story">
                            <?php echo parse_story($entry["story"]); ?>
                        </div><!-- story -->
                    <?php endif; ?>
                </div>
                <?php
                $sql = "SELECT * FROM examples WHERE kanji != '' AND kanji LIKE ? AND added = 1 ORDER BY CASE WHEN jlpt IS NOT NULL THEN 1 WHEN freq_wiki IS NOT NULL THEN 2 ELSE 3 END";
                //"SELECT * FROM examples WHERE kanji != '' AND kanji LIKE ? AND added = 1 ORDER BY CASE WHEN jlpt IS NOT NULL THEN 1 WHEN freq_wiki IS NOT NULL THEN 2 ELSE 3 END, jlpt DESC, freq_wiki ASC";
                $stmt = $myPDO->prepare($sql);
                $stmt->execute(["%" . $entry["literal"] . "%"]);
                $my_examples = $stmt->fetchAll();

                $sql =
                    "SELECT * FROM examples WHERE kanji != '' AND kanji LIKE ? AND (added = 0 OR added IS NULL) ORDER BY CASE WHEN jlpt IS NOT NULL THEN 1 WHEN freq_wiki IS NOT NULL THEN 2 ELSE 3 END, jlpt DESC, freq_wiki ASC";

                //$sql = "SELECT * FROM examples WHERE kanji != '' AND kanji LIKE ? AND (jlpt IS NULL AND freq_wiki IS NULL AND added != 1)";
                $stmt = $myPDO->prepare($sql);
                $stmt->execute(["%" . $entry["literal"] . "%"]);
                $more_examples = $stmt->fetchAll();

                $sql = "SELECT * FROM phrases WHERE phrase LIKE ?";
                $stmt = $myPDO->prepare($sql);
                $stmt->execute(["%" . $entry["literal"] . "%"]);
                $phrases = $stmt->fetchAll();


                $sql = "SELECT * FROM kanjis WHERE components LIKE ? ORDER BY added DESC";
                $stmt = $myPDO->prepare($sql);
                $stmt->execute(["%" . $entry["literal"] . "%"]);
                $contained_in_kanjis = $stmt->fetchAll();
                ?>
                <?php if (!empty($my_examples)): ?>
                    <div class="title">My words</div>
                    <div class="words">
                        <!-- priority examples -->
                        <?php foreach ($my_examples as $example): ?>
                            <div class="word added">
                                <?php if (
                                    $example["jlpt"] != 0
                                ): ?><span class="word-meta">N<?php echo $example["jlpt"]; ?></span><?php endif; ?>
                                <?php if (
                                    $example["freq_wiki"] != 0
                                ): ?><span class="word-meta">F<?php echo $example["freq_wiki"]; ?></span><?php endif; ?>
                                <a href="search.php?query=<?php echo $example["kanji"]; ?>" class="example-kanji"><?php echo formatKanjis(
                                                                                                                        $example["kanji"],
                                                                                                                    ); ?></a>

                                <span class="example-text">
                                    <span style="font-size:1.6rem"><?= $example["kanji"] ?></span><br>
                                    「<?= str_replace(
                                            ";",
                                            " / ",
                                            $example["kana"],
                                        ) ?>」<br>
                                    <?php if (
                                        !empty($example["kanji_alternative"])
                                    ) {
                                        echo "[also " .
                                            str_replace(
                                                ";",
                                                " / ",
                                                $example["kanji_alternative"],
                                            ) .
                                            "]<br>";
                                    } ?>
                                    <?php echo formatMeanings(
                                        $example["meanings"],
                                    ); ?>
                                    <form action="../actions/toggle_example_study.php" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $example["id"]; ?>">
                                        <input type="hidden" name="literal" value="<?php echo $_GET["literal"]; ?>">
                                        <button type="submit">
                                            <?= $example["added"] == 1
                                                ? "remove"
                                                : "add" ?>
                                        </button>
                                    </form>
                                </span>
                            </div>
                        <?php endforeach; ?>

                    </div><!-- /words -->
                <?php endif; ?><!-- /my_examples -->

                <?php if (!empty($phrases)): ?>
                    <div class="title">Phrases</div>
                    <div class="phrases">
                        <?php foreach ($phrases as $phrase): ?>
                            <div class="phrase">
                                <div class="phrase-ruby">
                                    <a href="search.php?query=<?= $phrase["phrase"] ?>"><?= $phrase["phrase_ruby"] ?></a>
                                </div>
                                <div class="phrase-translation">
                                    <?= $phrase["translation"] ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?><!-- /phrases -->

                <?php if (!empty($more_examples)): ?>
                    <div class="words">
                        <?php if (!empty($more_examples)): ?>
                            <p><a href="#" id="more-word-examples-toggle">toggle examples ...</a></p>
                            <!-- more examples -->
                            <div id="more-word-examples">
                                <?php foreach ($more_examples as $example): ?>
                                    <div class="word">
                                        <?php if (
                                            $example["jlpt"] != 0
                                        ): ?><span class="word-meta">N<?php echo $example["jlpt"]; ?></span><?php endif; ?>
                                        <?php if (
                                            $example["freq_wiki"] != 0
                                        ): ?><span class="word-meta">F<?php echo $example["freq_wiki"]; ?></span><?php endif; ?>
                                        <a href="search.php?query=<?php echo $example["kanji"]; ?>" class="example-kanji"><?php echo formatKanjis(
                                                                                                                                $example["kanji"],
                                                                                                                            ); ?></a>

                                        <span class="example-text">
                                            <span style="font-size:1.6rem"><?= $example["kanji"] ?></span><br>
                                            「<?= str_replace(
                                                    ";",
                                                    " / ",
                                                    $example["kana"],
                                                ) ?>」<br>
                                            <?php if (
                                                !empty($example["kanji_alternative"])
                                            ) {
                                                echo "[also " .
                                                    str_replace(
                                                        ";",
                                                        " / ",
                                                        $example["kanji_alternative"],
                                                    ) .
                                                    "]<br>";
                                            } ?>
                                            <?php echo formatMeanings(
                                                $example["meanings"],
                                            ); ?>
                                            <form action="../actions/toggle_example_study.php" method="POST">
                                                <input type="hidden" name="id" value="<?php echo $example["id"]; ?>">
                                                <input type="hidden" name="literal" value="<?php echo $_GET["literal"]; ?>">
                                                <button type="submit">
                                                    <?= $example["added"] == 1
                                                        ? "remove"
                                                        : "add" ?>
                                                </button>
                                            </form>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div><!-- /words -->
                <?php endif; ?><!-- more_examples -->

                <?php if (!empty($contained_in_kanjis)): ?>
                    <div class="title">Kanjis that contain this component</div>
                    <div class="words">
                        <?php foreach ($contained_in_kanjis as $example): ?>
                            <?php
                            $added = $example["added"] == 1 ? " added " : "";
                            $ignore = $example["ignore"] == 1 ? " ignore " : "";
                            ?>
                            <div class="word
                            <?= $added ?>
                            <?= $ignore ?>">
                                <a href="kanji.php?literal=<?php echo $example["literal"]; ?>" class="example-kanji"><?php echo $example["literal"]; ?></a>
                            </div>
                        <?php endforeach; ?>
                    </div><!-- contained_in_kanjis -->
                <?php endif; ?>

                <div class="edit" id="edit-area">
                    <form action="../actions/update_kanji.php" enctype="multipart/form-data" method="POST">
                        <input type="hidden" name="literal" value="<?php echo $entry["literal"]; ?>">
                        <span>Components</span>
                        <input type=" text" name="components" value="<?php echo $entry["components"]; ?>" placeholder="𠂇;口">
                        <span>Other forms</span>
                        <input type="text" name="otherForms" value="<?php echo $entry["other_forms"]; ?>" placeholder="亻;人">
                        <span>See also</span>
                        <input type="text" name="related" value="<?php echo $entry["related"]; ?>" placeholder="亻;人">
                        <span>Story (#query# to create a search, _day_ for emphasis and ?msg? for TODO)</span>
                        <textarea rows="4" name="story"><?php echo $entry["story"]; ?></textarea>
                        <p><input id="unfinished" type="checkbox" <?= $entry["unfinished"] == 1
                                                                        ? "checked"
                                                                        : "" ?> name="unfinished"><label for="unfinished">is missing information</label></p>
                        <p><input id="is_component" type="checkbox" <?= $entry["is_component"] == 1
                                                                        ? "checked"
                                                                        : "" ?> name="is_component"><label for="is_component">is a basic component</label></p>
                        <span>Image</span>
                        <input type="file" name="image">
                        <p><button type="submit">Save changes</button></p>
                    </form>
                </div>
            </div><!-- right -->

            <div class="review-button">
                <form action="../actions/review.php" method="POST">
                    <button type="submit" class="review">🢅</button>
                </form>
            </div>




        </div><!-- card -->
    <?php endif; ?>
<?php else: ?>
    <div class="card empty">
        No kanji selected.
    </div>
<?php endif; ?><!-- isset literal -->

<script src="../data/script.js"></script>

<?php require "../parts/footer.php"; ?>

<script>
    let btn = document.querySelector("#more-word-examples-toggle");
    let moreExamples = document.querySelector("#more-word-examples");
    btn.addEventListener("click", function(e) {
        e.preventDefault();
        if (moreExamples.style.display === "block") {
            moreExamples.style.display = "none";
        } else {
            moreExamples.style.display = "block";
        }
    });
</script>