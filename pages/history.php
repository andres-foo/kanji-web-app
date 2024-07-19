<?php define('home', true); ?>
<?php require '../parts/header.php'; ?>


<?php
// db connection
$PDO = new PDO('sqlite:../data/kanjis.db');

// dates for review history
$sql = "SELECT DISTINCT date FROM review_history ORDER BY date DESC";
$stmt = $PDO->prepare($sql);
$result = $stmt->execute();
$dates = $stmt->fetchAll();

// search history
$sql = "SELECT query FROM search_history ORDER BY id DESC";
$stmt = $PDO->prepare($sql);
$result = $stmt->execute();
$queries = $stmt->fetchAll();
?>

<div class="title">Review history</div>

<?php if (empty($dates)) : ?>
    No review history.
<?php else : ?>
    <div class="list">
        <?php foreach ($dates as $date) : ?>
            <?php
            $sql = "SELECT * FROM review_history WHERE date = ? ORDER BY id DESC";
            $stmt = $PDO->prepare($sql);
            $result = $stmt->execute([$date['date']]);
            $entries = $stmt->fetchAll();
            ?>
            <div class="title"><?= $date['date'] ?> (<?= count($entries) ?>)</div>
            <?php foreach ($entries as $entry) : ?>
                <a href="./kanji.php?literal=<?= $entry['kanji'] ?>"><?= $entry['kanji'] ?></a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

<div class="title">Search history</div>
<?php if (empty($queries)) : ?>
    No search history.
<?php else : ?>
    <div class="list search-history">
        <?php foreach ($queries as $query) : ?>
            <a href="./search.php?query=<?= $query['query'] ?>"><?= $query['query'] ?></a>
        <?php endforeach; ?>
    </div>

<?php endif; ?>


<?php require '../parts/footer.php'; ?>