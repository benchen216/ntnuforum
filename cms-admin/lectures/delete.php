
<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // 先獲取講座信息以刪除照片
    $sql = "SELECT speaker_photo FROM lectures WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $lecture = $result->fetch_assoc();
        if($lecture['speaker_photo']) {
            $photo_path = "../../assets/img/speakers/" . $lecture['speaker_photo'];
            if(file_exists($photo_path)) {
                unlink($photo_path);
            }
        }
    }

    // 刪除講座記錄
    $sql = "DELETE FROM lectures WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if($stmt->execute()) {
        $_SESSION['message'] = "講座刪除成功！";
    } else {
        $_SESSION['message'] = "刪除失敗，請稍後再試。";
    }
}

header('Location: index.php');
exit();
?>
