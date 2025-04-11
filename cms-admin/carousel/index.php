<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

// 處理圖片上傳
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'upload':
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $language = $_POST['language'] ?? 'zh';
                    $target_dir = $language == 'zh' ? "../../assets/img/carousel/" : "../../en/assets/img/carousel/";
                    
                    // 確保目錄存在
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }

                    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                    $new_filename = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $new_filename;

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $relative_path = ($language == 'zh' ? 'assets/img/carousel/' : 'en/assets/img/carousel/') . $new_filename;
                        $sql = "INSERT INTO carousel_images (image_path, language, sort_order) VALUES (?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM carousel_images AS ci WHERE language = ?))";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sss", $relative_path, $language, $language);
                        $stmt->execute();
                    }
                }
                break;

            case 'delete':
                if (isset($_POST['id'])) {
                    // 獲取圖片路徑
                    $sql = "SELECT image_path FROM carousel_images WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        // 刪除實體檔案
                        $file_path = "../../" . $row['image_path'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                        // 刪除資料庫記錄
                        $sql = "DELETE FROM carousel_images WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $_POST['id']);
                        $stmt->execute();
                    }
                }
                break;

            case 'update_order':
                if (isset($_POST['orders'])) {
                    $orders = json_decode($_POST['orders'], true);
                    foreach ($orders as $item) {
                        $sql = "UPDATE carousel_images SET sort_order = ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $item['order'], $item['id']);
                        $stmt->execute();
                    }
                }
                break;

            case 'toggle_visibility':
                if (isset($_POST['id'])) {
                    $sql = "UPDATE carousel_images SET is_visible = NOT is_visible WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                }
                break;
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// 獲取輪播圖片列表
$sql = "SELECT * FROM carousel_images ORDER BY language, sort_order";
$result = $conn->query($sql);
$images = [
    'zh' => [],
    'en' => []
];
while ($row = $result->fetch_assoc()) {
    $images[$row['language']][] = $row;
}

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once '../includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">輪播圖片管理</h1>
            </div>

            <!-- 中文版輪播圖片 -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">中文版輪播圖片</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="mb-4">
                        <input type="hidden" name="action" value="upload">
                        <input type="hidden" name="language" value="zh">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">上傳新圖片</label>
                                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" required>
                                    <div class="form-text">建議尺寸 1920x1080 像素，格式為 JPG</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary mt-4">上傳圖片</button>
                            </div>
                        </div>
                    </form>

                    <div class="image-list" data-language="zh">
                        <?php foreach ($images['zh'] as $image): ?>
                            <div class="card mb-3" data-id="<?php echo $image['id']; ?>">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="/series_lecture/<?php echo $image['image_path']; ?>" class="img-fluid" alt="輪播圖片">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary handle">拖曳排序</button>
                                                    <button type="button" class="btn btn-sm btn-outline-<?php echo $image['is_visible'] ? 'success' : 'danger'; ?> toggle-visibility">
                                                        <?php echo $image['is_visible'] ? '顯示中' : '已隱藏'; ?>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-image">刪除</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- 英文版輪播圖片 -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">英文版輪播圖片</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="mb-4">
                        <input type="hidden" name="action" value="upload">
                        <input type="hidden" name="language" value="en">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Upload New Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" required>
                                    <div class="form-text">Recommended size: 1920x1080 pixels, JPG format</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary mt-4">Upload Image</button>
                            </div>
                        </div>
                    </form>

                    <div class="image-list" data-language="en">
                        <?php foreach ($images['en'] as $image): ?>
                            <div class="card mb-3" data-id="<?php echo $image['id']; ?>">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="/series_lecture/<?php echo $image['image_path']; ?>" class="img-fluid" alt="Carousel Image">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary handle">Drag to Sort</button>
                                                    <button type="button" class="btn btn-sm btn-outline-<?php echo $image['is_visible'] ? 'success' : 'danger'; ?> toggle-visibility">
                                                        <?php echo $image['is_visible'] ? 'Visible' : 'Hidden'; ?>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-image">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- 引入 Sortable.js -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 初始化拖曳排序
    document.querySelectorAll('.image-list').forEach(function(el) {
        new Sortable(el, {
            handle: '.handle',
            animation: 150,
            onEnd: function() {
                updateOrder(el);
            }
        });
    });

    // 更新排序
    function updateOrder(container) {
        const items = container.querySelectorAll('.card');
        const orders = Array.from(items).map((item, index) => ({
            id: item.dataset.id,
            order: index + 1
        }));

        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=update_order&orders=${JSON.stringify(orders)}`
        });
    }

    // 刪除圖片
    document.querySelectorAll('.delete-image').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('確定要刪除這張圖片嗎？')) {
                const card = this.closest('.card');
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', card.dataset.id);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                }).then(() => window.location.reload());
            }
        });
    });

    // 切換可見性
    document.querySelectorAll('.toggle-visibility').forEach(button => {
        button.addEventListener('click', function() {
            const card = this.closest('.card');
            const formData = new FormData();
            formData.append('action', 'toggle_visibility');
            formData.append('id', card.dataset.id);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(() => window.location.reload());
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>