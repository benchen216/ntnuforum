<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

if(!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = (int)$_GET['id'];

// 檢查是否有關聯的講座
$check_sql = "SELECT COUNT(*) as count FROM lectures WHERE category_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$lecture_count = $check_result->fetch_assoc()['count'];

if($lecture_count > 0) {
    $_SESSION['error'] = "無法刪除此類別：還有 {$lecture_count} 個講座使用此類別。請先修改或刪除相關講座。";
    header('Location: index.php');
    exit();
}

// 檢查是否為最後一個類別
$count_sql = "SELECT COUNT(*) as count FROM lecture_categories";
$count_result = $conn->query($count_sql);
$total_categories = $count_result->fetch_assoc()['count'];

if($total_categories <= 1) {
    $_SESSION['error'] = "無法刪除此類別：系統必須至少保留一個類別。";
    header('Location: index.php');
    exit();
}

// 在執行刪除之前，先刪除相關的圖片文件
$category_sql = "SELECT slug FROM lecture_categories WHERE id = ?";
$category_stmt = $conn->prepare($category_sql);
$category_stmt->bind_param("i", $id);
$category_stmt->execute();
$category_result = $category_stmt->get_result();
$category = $category_result->fetch_assoc();

if($category) {
    // 刪除中文版 banner
    $banner_path = '../../assets/img/banner/' . $category['slug'] . '.jpg';
    if(file_exists($banner_path)) {
        unlink($banner_path);
    }

    // 刪除英文版 banner
    $banner_en_path = '../../en/assets/img/banner/' . $category['slug'] . '.jpg';
    if(file_exists($banner_en_path)) {
        unlink($banner_en_path);
    }
}

// 執行刪除
$sql = "DELETE FROM lecture_categories WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if($stmt->execute()) {
    $_SESSION['message'] = "類別已成功刪除！";
} else {
    $_SESSION['error'] = "刪除失敗，請稍後再試。";
}

header('Location: index.php');
exit();