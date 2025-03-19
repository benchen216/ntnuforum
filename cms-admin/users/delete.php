<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // 不能刪除自己的帳號
    if($id == $_SESSION['user_id']) {
        $_SESSION['message'] = "無法刪除目前登入的帳號！";
        header('Location: index.php');
        exit();
    }

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if($stmt->execute()) {
        $_SESSION['message'] = "使用者刪除成功！";
    } else {
        $_SESSION['message'] = "刪除失敗，請稍後再試。";
    }
}

header('Location: index.php');
exit();
?>
