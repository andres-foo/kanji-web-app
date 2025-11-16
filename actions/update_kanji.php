<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
// check if a literal has been provided
if (!isset($_POST['literal'])) exit('No literal provided');


// db connection
$myPDO = new PDO('sqlite:../data/kanjis.db');


//components & other forms
$unfinished = isset($_POST['unfinished']) ? 1 : NULL;
$is_component = isset($_POST['is_component']) ? 1 : NULL;
$sql = "UPDATE kanjis SET keyword = ?, components = ?, other_forms = ?, story = ?, unfinished = ?, is_component = ?, related = ? WHERE literal = ?";
$stmt = $myPDO->prepare($sql);
$results = $stmt->execute([trim($_POST['keyword']), trim($_POST['components']), trim($_POST['otherForms']), trim($_POST['story']), $unfinished, $is_component, $_POST['related'], $_POST['literal']]);

if (!$results) exit('Unable to update kanji');

// file upload
if (file_exists($_FILES['image']['tmp_name']) && getimagesize($_FILES["image"]["tmp_name"])) {
    $imageFileType = strtolower(pathinfo(basename($_FILES["image"]["name"]), PATHINFO_EXTENSION));

    if ($imageFileType != "png" && $imageFileType != "jpg") {
        exit("Only jpg/png allowed");
    }

    $target = "../data/images/" . $_POST['literal'] . '.jpg';
    $temp_img = $_FILES["image"]["tmp_name"];

    // remove previous image if any
    if (file_exists($target)) {
        unlink($target);
    }

    // handle png to jpg conversion
    if ($imageFileType == "png") {
        $image = imagecreatefrompng($temp_img);

        // If the PNG has transparency, handle it (e.g., fill with white background)
        if (imagealphablending($image, false)) {
            imagesavealpha($image, true);
            $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
            imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255)); // White background
            imagealphablending($bg, TRUE);
            imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            imagedestroy($image); // Destroy original image resource
            $image = $bg; // Use the new image with background
        }

        // Save the image as JPEG
        imagejpeg($image, $target, 70);

        // Destroy the image resource to free up memory
        imagedestroy($image);
    } else {
        move_uploaded_file($temp_img, $target);
    }
}

header("Location: ../pages/kanji.php?literal=" . $_POST['literal']);
exit;
