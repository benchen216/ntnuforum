<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

// 獲取當前設定
$sql = "SELECT * FROM website_settings LIMIT 1";
$result = $conn->query($sql);
$settings = $result->fetch_assoc();

if(isset($_POST['submit'])) {
    $page_title = $conn->real_escape_string($_POST['page_title']);
    $page_title_en = $conn->real_escape_string($_POST['page_title_en']);
    $meta_description = $conn->real_escape_string($_POST['meta_description']);
    $meta_description_en = $conn->real_escape_string($_POST['meta_description_en']);
    $copyright_text = str_replace(["\r\n", "\r"], "\n",$_POST['copyright_text']);
    $copyright_text_en = str_replace(["\r\n", "\r"], "\n",$_POST['copyright_text_en']);
    $google_analytics_code = $_POST['google_analytics_code'];
    $sw_english = isset($_POST['sw_english']) ? 1 : 0;

    // 如果已有記錄則更新，否則插入
    if($result->num_rows > 0) {
        $sql = "UPDATE website_settings SET 
                page_title = ?, 
                page_title_en = ?, 
                meta_description = ?, 
                meta_description_en = ?, 
                copyright_text = ?, 
                copyright_text_en = ?, 
                sw_english = ?,
                google_analytics_code = ?";
    } else {
        $sql = "INSERT INTO website_settings 
                (page_title, page_title_en, meta_description, meta_description_en, 
                copyright_text, copyright_text_en, sw_english, google_analytics_code) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssis",
        $page_title,
        $page_title_en,
        $meta_description,
        $meta_description_en,
        $copyright_text,
        $copyright_text_en,
        $sw_english,
        $google_analytics_code
    );

    if($stmt->execute()) {
        $_SESSION['message'] = "網站設定已更新成功！";
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
                <h1 class="h2">網站設定</h1>
            </div>

            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="needs-validation" novalidate>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">頁面標題(中文)</label>
                                <input type="text" name="page_title" class="form-control" 
                                       value="<?php echo htmlspecialchars($settings['page_title'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">頁面標題(英文)</label>
                                <input type="text" name="page_title_en" class="form-control" 
                                       value="<?php echo htmlspecialchars($settings['page_title_en'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta描述(中文)</label>
                                <textarea name="meta_description" class="form-control" rows="3"><?php echo htmlspecialchars($settings['meta_description'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta描述(英文)</label>
                                <textarea name="meta_description_en" class="form-control" rows="3"><?php echo htmlspecialchars($settings['meta_description_en'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">版權聲明(中文)</label>
                                <textarea name="copyright_text" class="form-control" rows="3"><?php echo htmlspecialchars($settings['copyright_text'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">版權聲明(英文)</label>
                                <textarea name="copyright_text_en" class="form-control" rows="3"><?php echo htmlspecialchars($settings['copyright_text_en'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">google analytics code</label>
                            <textarea name="google_analytics_code" class="form-control" rows="3"><?php echo htmlspecialchars($settings['google_analytics_code'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="sw_english" class="form-check-input" id="sw_english" 
                                       <?php echo ($settings['sw_english'] ?? 0) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="sw_english">啟用英文版網站</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" name="submit" class="btn btn-primary">儲存設定</button>
                </div>
            </form>
        </main>
    </div>
</div>

