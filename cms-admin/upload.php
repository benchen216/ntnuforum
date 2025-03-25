<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Unauthorized']));
}

if ($_FILES['file']['error']) {
    die(json_encode(['error' => 'File upload error']));
}

$target_dir = "../assets/uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// 生成唯一的文件名
$file_extension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
$new_filename = uniqid() . '.' . $file_extension;
$target_file = $target_dir . $new_filename;

// 允許的圖片類型
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($file_extension, $allowed_types)) {
    die(json_encode(['error' => 'Invalid file type']));
}

// 檢查文件大小 (這裡設置為最大 5MB)
if ($_FILES["file"]["size"] > 5000000) {
    die(json_encode(['error' => 'File too large']));
}

if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    echo json_encode([
        'location' => '/series_lecture/assets/uploads/' . $new_filename
    ]);
} else {
    echo json_encode(['error' => 'Upload failed']);
}
?>