<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

// 獲取所有類別
$sql = "SELECT * FROM lecture_categories ORDER BY sort_order ASC";
$result = $conn->query($sql);

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once '../includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">講座類別管理</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="add.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-plus"></i> 新增類別
                    </a>
                </div>
            </div>

            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>排序</th>
                            <th>類別名稱(中文)</th>
                            <th>類別名稱(英文)</th>
                            <th>網址別名</th>
                            <th>狀態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($category = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['sort_order']); ?></td>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars($category['name_en']); ?></td>
                                <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                <td>
                                    <?php if($category['is_visible']): ?>
                                        <span class="badge bg-success">顯示</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">隱藏</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-primary">編輯</a>
                                    <a href="delete.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('確定要刪除此類別嗎？\n注意：只有在沒有關聯講座的情況下才能刪除。')">刪除</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>