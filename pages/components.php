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

    $sql = "SELECT * FROM kanjis WHERE added = 1 AND component_only = 1 ORDER BY component_group ASC";
    $stmt = $myPDO->query($sql);
    $entries = $stmt->fetchAll();
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
                    echo "<div class='component-group-title'>group " . $entry['component_group'] . "</div>";
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
                        <?= $entry['meanings']; ?>
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
                </div><!-- component-card -->
                <?php
                $previous_group = $entry['component_group'];
                ?>
            <?php endforeach; ?>
        </div><!-- component-group (close last group) -->
        <!-- extra group to increase -->
        <?php
        // extra group at the end
        $new_group = $previous_group + 1;
        echo "<div class='component-group' id='group-" . $new_group . "' ondrop='dropHandler(event)' ondragover='dragoverHandler(event)'>";
        echo "<div class='component-group-title'>group " . $new_group . "</div>";
        echo "</div>";
        ?>
</div><!-- component-list -->

<?php endif; ?>




</div>

<?php require '../parts/footer.php'; ?>

<script>
    function dragstartHandler(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
    }

    function dragoverHandler(ev) {
        ev.preventDefault();
    }

    function dropHandler(ev) {
        ev.preventDefault();
        const data = ev.dataTransfer.getData("text");
        ev.target.closest('.component-group').appendChild(document.getElementById(data));

        // info
        let groupValue = ev.target.closest('.component-group').id.split('-')[1];
        let literalValue = data;

        // ajax
        fetch('/actions/ajax-update-group.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    group: groupValue,
                    literal: literalValue,
                }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json(); // Or response.text() if your API returns plain text
            })
            .then(data => {
                console.log('Success:', data);
                // Handle the response from the server
            })
            .catch((error) => {
                console.error('Error:', error);
                // Handle any errors that occurred during the fetch
            });
    }
</script>