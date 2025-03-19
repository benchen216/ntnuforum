<?php
require_once '../config/database.php';

// 設定管理員帳號資訊
$username = 'admin';
$password = password_hash('ntnu7734', PASSWORD_DEFAULT);
$name = '系統管理員';
$email = 'admin@example.com';

// 檢查帳號是否已存在
$check_sql = "SELECT id FROM users WHERE username = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if($check_result->num_rows > 0) {
    echo "管理員帳號已存在！\n";
} else {
    // 新增管理員帳號
    $sql = "INSERT INTO users (username, password, name, email) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $password, $name, $email);

    if($stmt->execute()) {
        echo "管理員帳號建立成功！\n";
        echo "帳號：admin\n";
        echo "密碼：ntnu7734\n";
    } else {
        echo "發生錯誤，請稍後再試。\n";
    }
}

$conn->close();
?>
