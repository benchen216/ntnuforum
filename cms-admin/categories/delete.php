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