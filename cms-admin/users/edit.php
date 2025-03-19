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
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    header('Location: index.php');
    exit();
}

$user = $result->fetch_assoc();

if(isset($_POST['submit'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);

    $sql = "UPDATE users SET name=?, email=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $email, $id);

    // 如果有輸入新密碼
    if(!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name=?, email=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $password, $id);
    }

    if($stmt->execute()) {
        $_SESSION['message'] = "使用者資料更新成功！";
        header('Location: index.php');
        exit();
    } else {
        $error = "發生錯誤，請稍後再試。";
    }
}

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once '../includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">編輯使用者</h1>
            </div>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">帳號</label>
                    <input type="text" class="form-control" value="<?php echo $user['username']; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">新密碼 (如不修改請留空)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">姓名</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $user['name']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                </div>

                <div class="mb-3">
                    <button type="submit" name="submit" class="btn btn-primary">更新資料</button>
                    <a href="index.php" class="btn btn-secondary">取消</a>
                </div>
            </form>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>