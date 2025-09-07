<?php define('home', true); ?>
<?php
require '../parts/header.php';
require '../parts/helper.php';
?>

<div class="list">

    <h1>COMPONENTS</h1>

    <?php

    // db connection
    $myPDO = new PDO('sqlite:../data/kanjis.db');

    $sql = "SELECT * FROM kanjis WHERE added = 1 AND is_component = 1 ORDER BY component_group ASC";
    $stmt = $myPDO->query($sql);
    $entries = $stmt->fetchAll();

    // check groups
    $sql = "SELECT DISTINCT component_group FROM kanjis ORDER BY component_group ASC";
    $stmt = $myPDO->query($sql);
    $groups = $stmt->fetchAll();

    ?>

    <?php if (count($entries) == 0) : ?>
        <p>You haven't added any kanjis yet! To do so click on the "<strong>Add</strong>" button on the top right of the page when viewing a kanji.</p>
    <?php else : ?>
        <div class="component-list">
            <?php foreach ($entries as $entry): ?>
                <?php
                if (!isset($previous_group)) {
                    // first ever group
                    echo "<div class='component-group' id='group-" . $entry['component_group'] . "' ondrop='dropHandler(event)' ondragover='dragoverHandler(event)'>";
                    echo "<div class='component-group-title'>" . ($entry['component_group'] == '0' ? 'No group' : 'Group ' . $entry['component_group']) . "</div>";
                } else {
                    // starting from second group
                    if ($previous_group != $entry['component_group']) {
                        echo "</div><!-- component-group -->";
                        echo "<div class='component-group' id='group-" . $entry['component_group'] . "' ondrop='dropHandler(event)' ondragover='dragoverHandler(event)'>";
                        echo "<div class='component-group-title'>group " . $entry['component_group'] . "</div>";
                    }
                }
                ?>
                <div class="component-card<?php echo $entry['unfinished'] ? ' unfinished' : ''; ?>" draggable="true" ondragstart="dragstartHandler(event)" id="<?= $entry['literal'] ?>">
                    <div class="component-literal">
                        <a href="kanji.php?literal=<?= $entry['literal']; ?>"><?= $entry['literal']; ?></a>
                    </div>
                    <div class="component-meaning">
                        <?= str_replace(';', ', ', $entry['meanings']); ?>
                    </div>
                    <div class="component-story">
                        <?= parse_story($entry['story']); ?>
                    </div>
                    <div class="component-words">
                        <?php
                        $sql = "SELECT literal FROM kanjis WHERE components LIKE '%" . $entry['literal'] . "%' AND added = 1";
                        $stmt = $myPDO->query($sql);
                        $examples = $stmt->fetchAll();
                        ?>
                        <?php foreach ($examples as $example): ?>
                            <a href="kanji.php?literal=<?= $example['literal']; ?>"><?= $example['literal']; ?></a>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <form method="POST" action="/actions/update-group.php">
                            <input type="hidden" name="literal" value="<?= $entry['literal'] ?>">
                            <select name="group" class="component-group-dropdown" onchange="updateGroup(this)">
                                <?php foreach ($groups as $group): ?>
                                    <?php if ($group['component_group'] == $entry['component_group']): ?>
                                        <option value="<?= $group['component_group'] ?>" disabled selected><?= $group['component_group'] ?>
                                        <?php else: ?>
                                        <option value="<?= $group['component_group'] ?>" <?php echo $group['component_group'] == $entry['component_group'] ? 'disabled' : '' ?>><?= $group['component_group'] ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                        <option value="-1">New
                            </select>
                        </form>
                    </div>
                </div><!-- component-card -->
                <?php
                $previous_group = $entry['component_group'];
                ?>
            <?php endforeach; ?>
        </div><!-- component-group (close last group) -->
        <!-- extra group to increase -->

</div><!-- component-list -->

<?php endif; ?>

</div>

<?php require '../parts/footer.php'; ?>

<script>
    function updateGroup(el) {
        el.closest('form').submit();
    }
</script>