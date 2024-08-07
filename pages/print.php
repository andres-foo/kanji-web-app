<?php define('home', true); ?>

<?php require_once '../parts/helper.php'; ?>

<?php
// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');

// search
$sql = "SELECT * FROM kanjis WHERE added = 1";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute();
$entries = $stmt->fetchAll();

if(!$entries) exit("No kanjis to print. Make sure to add some kanjis to your list first.");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My List</title>
    <link rel="stylesheet" href="../data/style.css">    
</head>
<body class="print-body">
    
<?php if(!$entries): ?>
        No results.
<?php else: ?>
    <div class="print">
    <?php foreach($entries as $entry): ?>
        <div class="print-row">
            <div class="print-literal">
                <?php echo $entry['literal']; ?>
            </div>
            <div class="print-extras">      
                <div class="print-meanings">
                    <?php echo str_replace(";", ", ", $entry['meanings']); ?>
                </div>
                <?php if(!empty($entry['story'])): ?>
                <div class="print-story">
                <?php 
                    // links
                    $pattern = '/#(.+?)#/';
                    $story = preg_replace($pattern, '$1',$entry['story']);
                    // emphasis
                    $pattern = '/\_(.+?)\_/';
                    echo preg_replace($pattern, '$1',$story); 
                ?>
                </div><!-- print-story -->
                <?php endif; //story ?>

                <?php
                    $sql = "SELECT * FROM examples WHERE added = 1 AND kanji LIKE ?";
                    $stmt = $myPDO->prepare($sql);
                    $stmt->execute(['%'.$entry['literal'].'%']);
                    $words = $stmt->fetchAll();
                ?>
                <?php if(!empty($words)): ?>
                <div class="print-examples">
                    <?php foreach($words as $word): ?>
                    <?php echo $word['kanji'].'「'.$word['kana'].'」'; ?>
                    <?php endforeach; ?>
                </div><!-- print-examples -->
                <?php endif; ?>

            </div><!-- print-extras -->
            </div><!-- print-row -->
    <?php endforeach; ?>    
    </div>
<?php endif;?>

</body>
</html>