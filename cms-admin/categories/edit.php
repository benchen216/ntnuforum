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
$sql = "SELECT * FROM lecture_categories WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    header('Location: index.php');
    exit();
}

$category = $result->fetch_assoc();

if(isset($_POST['submit'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $name_en = $conn->real_escape_string($_POST['name_en']);
    $slug = $conn->real_escape_string($_POST['slug']);
    $description = $conn->real_escape_string($_POST['description']);
    $description_en = $conn->real_escape_string($_POST['description_en']);
    $sort_order = (int)$_POST['sort_order'];
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    // 檢查 slug 是否已被其他類別使用
    $check_sql = "SELECT id FROM lecture_categories WHERE slug = ? AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $slug, $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if($check_result->num_rows > 0) {
        $error = "此網址別名已被使用";
    } else {
        $sql = "UPDATE lecture_categories SET 
                name = ?, name_en = ?, slug = ?, 
                description = ?, description_en = ?, 
                sort_order = ?, is_visible = ? 
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssiis", 
            $name, $name_en, $slug,
            $description, $description_en,
            $sort_order, $is_visible, $id);

        if($stmt->execute()) {
            $_SESSION['message'] = "類別更新成功！";
            header('Location: index.php');
            exit();
        } else {
            $error = "發生錯誤，請稍後再試。";
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once '../includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">編輯講座類別</h1>
            </div>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="needs-validation" novalidate>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">類別名稱(中文)</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">類別名稱(英文)</label>
                                <input type="text" name="name_en" class="form-control" value="<?php echo htmlspecialchars($category['name_en']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">網址別名</label>
                            <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($category['slug']); ?>" required>
                            <div class="form-text">請使用英文小寫、數字和連字符(-)</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">描述(中文)</label>
                                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">描述(英文)</label>
                                <textarea name="description_en" class="form-control" rows="3"><?php echo htmlspecialchars($category['description_en']); ?></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">排序順序</label>
                                <input type="number" name="sort_order" class="form-control" value="<?php echo htmlspecialchars($category['sort_order']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="is_visible" class="form-check-input" id="is_visible" <?php echo $category['is_visible'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_visible">顯示於前台</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" name="submit" class="btn btn-primary">更新類別</button>
                    <a href="index.php" class="btn btn-secondary">取消</a>
                </div>
            </form>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>