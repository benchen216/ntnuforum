<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

// 處理圖片上傳
if(isset($_POST['submit'])) {
    $target_file = $_POST['image_position']; // 獲取目標文件位置
    $upload_success = false;
    $error = null;

    if(isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['banner']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if(!in_array($ext, $allowed)) {
            $error = "只允許上傳 JPG 或 PNG 格式的圖片";
        } else {
            // 確保目標目錄存在
            $target_dir = dirname('../../' . $target_file);
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // 上傳並處理圖片
            $upload_path = '../../' . $target_file;
            if(move_uploaded_file($_FILES['banner']['tmp_name'], $upload_path)) {
                // 如果是 PNG，轉換為 JPG
                if($ext == 'png') {
                    $image = imagecreatefrompng($upload_path);
                    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                    imagealphablending($bg, TRUE);
                    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                    imagedestroy($image);

                    // 修改副檔名為 jpg
                    $jpg_path = preg_replace('/\.png$/', '.jpg', $upload_path);
                    imagejpeg($bg, $jpg_path, 90);
                    imagedestroy($bg);
                    if(file_exists($upload_path)) {
                        unlink($upload_path); // 刪除原始的 PNG
                    }
                }
                $upload_success = true;
            }
        }
    } else {
        $error = "請選擇要上傳的圖片";
    }
}

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once '../includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">首頁輪播圖片管理</h1>
            </div>

            <?php if(isset($upload_success) && $upload_success): ?>
                <div class="alert alert-success">圖片上傳成功！</div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="row">
                <!-- 中文版輪播圖片 1 -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">中文版輪播圖片 1</h5>
                        </div>
                        <div class="card-body">
                            <?php if(file_exists('../../assets/img/carousel/carousel-1.jpg')): ?>
                                <div class="mb-3">
                                    <img src="/series_lecture/assets/img/carousel/carousel-1.jpg"
                                         class="img-fluid"
                                         alt="目前輪播圖片 1">
                                </div>
                            <?php endif; ?>
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="image_position" value="assets/img/carousel/carousel-1.jpg">
                                <div class="mb-3">
                                    <label class="form-label">上傳新圖片</label>
                                    <input type="file" name="banner" class="form-control" accept="image/jpeg,image/png" required>
                                    <div class="form-text">建議尺寸 1920x1080 像素，格式為 JPG</div>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">更新圖片</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- 中文版輪播圖片 2 -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">中文版輪播圖片 2</h5>
                        </div>
                        <div class="card-body">
                            <?php if(file_exists('../../assets/img/carousel/carousel-2.jpg')): ?>
                                <div class="mb-3">
                                    <img src="/series_lecture/assets/img/carousel/carousel-2.jpg"
                                         class="img-fluid"
                                         alt="目前輪播圖片 2">
                                </div>
                            <?php endif; ?>
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="image_position" value="assets/img/carousel/carousel-2.jpg">
                                <div class="mb-3">
                                    <label class="form-label">上傳新圖片</label>
                                    <input type="file" name="banner" class="form-control" accept="image/jpeg,image/png" required>
                                    <div class="form-text">建議尺寸 1920x1080 像素，格式為 JPG</div>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">更新圖片</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- 英文版輪播圖片 1 -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">英文版輪播圖片 1</h5>
                        </div>
                        <div class="card-body">
                            <?php if(file_exists('../../en/assets/img/carousel/carousel-1.jpg')): ?>
                                <div class="mb-3">
                                    <img src="/series_lecture/en/assets/img/carousel/carousel-1.jpg"
                                         class="img-fluid"
                                         alt="Current English Carousel 1">
                                </div>
                            <?php endif; ?>
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="image_position" value="en/assets/img/carousel/carousel-1.jpg">
                                <div class="mb-3">
                                    <label class="form-label">上傳新圖片</label>
                                    <input type="file" name="banner" class="form-control" accept="image/jpeg,image/png" required>
                                    <div class="form-text">建議尺寸 1920x1080 像素，格式為 JPG</div>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">更新圖片</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- 英文版輪播圖片 2 -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">英文版輪播圖片 2</h5>
                        </div>
                        <div class="card-body">
                            <?php if(file_exists('../../en/assets/img/carousel/carousel-2.jpg')): ?>
                                <div class="mb-3">
                                    <img src="/series_lecture/en/assets/img/carousel/carousel-2.jpg"
                                         class="img-fluid"
                                         alt="Current English Carousel 2">
                                </div>
                            <?php endif; ?>
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="image_position" value="en/assets/img/carousel/carousel-2.jpg">
                                <div class="mb-3">
                                    <label class="form-label">上傳新圖片</label>
                                    <input type="file" name="banner" class="form-control" accept="image/jpeg,image/png" required>
                                    <div class="form-text">建議尺寸 1920x1080 像素，格式為 JPG</div>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">更新圖片</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>