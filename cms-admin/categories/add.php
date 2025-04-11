<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

if(isset($_POST['submit'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $name_en = $conn->real_escape_string($_POST['name_en']);
    $slug = $conn->real_escape_string($_POST['slug']);
    $description = $conn->real_escape_string($_POST['description']);
    $description_en = $conn->real_escape_string($_POST['description_en']);
    $sort_order = (int)$_POST['sort_order'];
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;
    if(isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['banner']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if(in_array($ext, $allowed)) {
            // 使用 slug 作為檔案名稱
            $new_filename = $slug . '.' . $ext;
            $upload_path = '../../assets/img/banner/' . $new_filename;

            if(move_uploaded_file($_FILES['banner']['tmp_name'], $upload_path)) {
                // 如果是 PNG，轉換為 JPG
                if($ext == 'png') {
                    $image = imagecreatefrompng($upload_path);
                    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                    imagealphablending($bg, TRUE);
                    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                    imagedestroy($image);
                    $jpg_path =  '../../assets/img/banner/' . $slug . '.jpg';
                    imagejpeg($bg, $jpg_path, 90);
                    imagedestroy($bg);
                    unlink($upload_path); // 刪除原始的 PNG
                }
            }
        }
    }
    if(isset($_FILES['banner_en']) && $_FILES['banner_en']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['banner_en']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if(in_array($ext, $allowed)) {
            // 確保英文版 banner 目錄存在
            if (!file_exists('../../en/assets/img/banner/')) {
                mkdir('../../en/assets/img/banner/', 0777, true);
            }

            // 使用 slug 作為檔案名稱
            $new_filename = $slug . '.' . $ext;
            $upload_path = '../../en/assets/img/banner/' . $new_filename;

            if(move_uploaded_file($_FILES['banner_en']['tmp_name'], $upload_path)) {
                // 如果是 PNG，轉換為 JPG
                if($ext == 'png') {
                    $image = imagecreatefrompng($upload_path);
                    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                    imagealphablending($bg, TRUE);
                    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                    imagedestroy($image);
                    $jpg_path = '../../en/assets/img/banner/' . $slug . '.jpg';
                    imagejpeg($bg, $jpg_path, 90);
                    imagedestroy($bg);
                    unlink($upload_path); // 刪除原始的 PNG
                }
            }
        }
    }

    // 檢查 slug 是否已存在
    $check_sql = "SELECT id FROM lecture_categories WHERE slug = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $slug);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if($check_result->num_rows > 0) {
        $error = "此網址別名已被使用";
    } else {
        $sql = "INSERT INTO lecture_categories (name, name_en, slug, description, description_en, sort_order, is_visible) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssii", $name, $name_en, $slug, $description, $description_en, $sort_order, $is_visible);

        if($stmt->execute()) {
            $_SESSION['message'] = "類別新增成功！";
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
                <h1 class="h2">新增講座類別</h1>
            </div>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">類別名稱(中文)<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">類別名稱(英文)<span class="text-danger">*</span></label>
                                <input type="text" name="name_en" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">網址別名<span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control" required>
                            <div class="form-text">請使用英文小寫、數字和連字符(-)</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Banner 圖片</label>
                            <input type="file" name="banner" class="form-control" accept="image/jpeg,image/png">
                            <div class="form-text">建議尺寸 1920x1080 像素，格式為 JPG</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Banner 圖片 (英文版)</label>
                            <input type="file" name="banner_en" class="form-control" accept="image/jpeg,image/png">
                            <div class="form-text">建議尺寸 1920x1080 像素，格式為 JPG</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">描述(中文)</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">描述(英文)</label>
                                <textarea name="description_en" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">排序順序</label>
                                <input type="number" name="sort_order" class="form-control" value="0">
                                <div class="form-text">由小到大排序，若數字相同，則依據類別建立時間排序（越早建立越前）。</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="is_visible" class="form-check-input" id="is_visible" checked>
                                    <label class="form-check-label" for="is_visible">顯示於前台</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" name="submit" class="btn btn-primary">新增類別</button>
                    <a href="index.php" class="btn btn-secondary">取消</a>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const nameEnInput = document.querySelector('input[name="name_en"]');
                        const slugInput = document.querySelector('input[name="slug"]');

                        // 轉換函數：將英文名稱轉換為合法的 slug
                        function convertToSlug(text) {
                            return text
                                .toLowerCase() // 轉小寫
                                .replace(/[^a-z0-9-]/g, '-') // 非英文數字轉為連字符
                                .replace(/--+/g, '-') // 多個連字符轉為單個
                                .replace(/^-+|-+$/g, ''); // 移除開頭和結尾的連字符
                        }

                        // 當英文名稱改變時自動更新 slug
                        nameEnInput.addEventListener('input', function() {
                            // 只有在 slug 欄位為空，或使用者尚未手動修改過 slug 時才自動更新
                            if (!slugInput.dataset.manuallyChanged) {
                                slugInput.value = convertToSlug(this.value);
                            }
                        });

                        // 標記使用者是否手動修改過 slug
                        slugInput.addEventListener('input', function() {
                            this.dataset.manuallyChanged = 'true';
                        });
                    });
                </script>
            </form>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>