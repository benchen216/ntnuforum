<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

if(isset($_POST['submit'])) {
    $required_fields = [
        'title' => '講座標題(中文)',
        'title_en' => '講座標題(英文)',
        'speaker' => '講者姓名(中文)',
        'speaker_en' => '講者姓名(英文)',
        'speaker_title' => '講者頭銜(中文)',
        'speaker_title_en' => '講者頭銜(英文)',
        'lecture_date' => '講座日期',
        'lecture_time' => '講座時間',
        'location' => '地點(中文)',
        'location_en' => '地點(英文)',
        'organizer' => '主辦單位(中文)',
        'organizer_en' => '主辦單位(英文)'
    ];

    $errors = [];
    foreach($required_fields as $field => $label) {
        if(empty($_POST[$field])) {
            $errors[] = $label . "為必填欄位";
        }
    }

    if(!empty($errors)) {
        $error = implode("<br>", $errors);
    } else {
        // 基本資訊
        $title = $conn->real_escape_string($_POST['title']);
        $title_en = $conn->real_escape_string($_POST['title_en']);
        $speaker = $conn->real_escape_string($_POST['speaker']);
        $speaker_en = $conn->real_escape_string($_POST['speaker_en']);
        $speaker_title = $conn->real_escape_string($_POST['speaker_title']);
        $speaker_title_en = $conn->real_escape_string($_POST['speaker_title_en']);

        // 時間地點
        $lecture_date = $conn->real_escape_string($_POST['lecture_date']);
        $lecture_time = $conn->real_escape_string($_POST['lecture_time']);
        $location = $conn->real_escape_string($_POST['location']);
        $location_en = $conn->real_escape_string($_POST['location_en']);

        // 狀態與分類
        $status = $conn->real_escape_string($_POST['status']);
        $category_id = (int)$_POST['category_id'];
        $sort_order = (int)$_POST['sort_order'];
        $is_visible = isset($_POST['is_visible']) ? 1 : 0;

        // 內容描述
        $description = $conn->real_escape_string($_POST['description']);
        $description_en = $conn->real_escape_string($_POST['description_en']);
        $agenda = $conn->real_escape_string($_POST['agenda']);
        $agenda_en = $conn->real_escape_string($_POST['agenda_en']);
        $summary = $conn->real_escape_string($_POST['summary']);
        $summary_en = $conn->real_escape_string($_POST['summary_en']);
        $speaker_intro = $conn->real_escape_string($_POST['speaker_intro']);
        $speaker_intro_en = $conn->real_escape_string($_POST['speaker_intro_en']);

        // 主辦單位
        $organizer = $conn->real_escape_string($_POST['organizer']);
        $organizer_en = $conn->real_escape_string($_POST['organizer_en']);
        $organizer_url = $conn->real_escape_string($_POST['organizer_url']);
        $co_organizer = $conn->real_escape_string($_POST['co_organizer']);
        $co_organizer_en = $conn->real_escape_string($_POST['co_organizer_en']);
        $co_organizer_urls = $conn->real_escape_string($_POST['co_organizer_urls']);

        // 報名相關
        $signup_url = $conn->real_escape_string($_POST['signup_url']);
        $signup_limit = empty($_POST['signup_limit']) ? 0 : (int)$_POST['signup_limit'];
        $signup_deadline = $conn->real_escape_string($_POST['signup_deadline'])===''?null:$conn->real_escape_string($_POST['signup_deadline']);

        // 線上會議
        $online_url = $conn->real_escape_string($_POST['online_url']);
        $meeting_id = $conn->real_escape_string($_POST['meeting_id']);
        $meeting_password = $conn->real_escape_string($_POST['meeting_password']);

        // 處理講者照片上傳
        $speaker_photo = '';
        if (isset($_FILES['speaker_photo']) && $_FILES['speaker_photo']['error'] == 0) {
            $target_dir = "../../assets/img/speakers/";
            $file_extension = pathinfo($_FILES["speaker_photo"]["name"], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;

            // 確保目錄存在
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES["speaker_photo"]["tmp_name"], $target_file)) {
                $speaker_photo = $new_filename;
            }
        }

        $sql = "INSERT INTO lectures (
            title, title_en, speaker, speaker_en, speaker_title, speaker_title_en, 
            speaker_photo, lecture_date, lecture_time, location, location_en,
            status, category_id, sort_order, is_visible,
            description, description_en, agenda, agenda_en,
            summary, summary_en, speaker_intro, speaker_intro_en,
            organizer, organizer_en, organizer_url,
            co_organizer, co_organizer_en, co_organizer_urls,
            signup_url, signup_limit, signup_deadline,
            online_url, meeting_id, meeting_password
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // 修改參數綁定的類型，signup_limit 使用 i
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssssiisssssssssssssssisssss",
            $title, $title_en, $speaker, $speaker_en, $speaker_title, $speaker_title_en,
            $speaker_photo, $lecture_date, $lecture_time, $location, $location_en,
            $status, $category_id, $sort_order, $is_visible,
            $description, $description_en, $agenda, $agenda_en,
            $summary, $summary_en, $speaker_intro, $speaker_intro_en,
            $organizer, $organizer_en, $organizer_url,
            $co_organizer, $co_organizer_en, $co_organizer_urls,
            $signup_url, $signup_limit, $signup_deadline,
            $online_url, $meeting_id, $meeting_password
        );

        if($stmt->execute()) {
            $_SESSION['message'] = "講座新增成功！";
            header('Location: index.php');
            exit();
        } else {
            echo $stmt->error;
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
                    <h1 class="h2">新增講座</h1>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- 基本資訊部分 -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">講座標題(中文) <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">講座標題(英文) <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">講者姓名(中文) <span class="text-danger">*</span></label>
                            <input type="text" name="speaker" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">講者姓名(英文) <span class="text-danger">*</span></label>
                            <input type="text" name="speaker_en" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">講者頭銜(中文) <span class="text-danger">*</span></label>
                            <textarea name="speaker_title" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">講者頭銜(英文) <span class="text-danger">*</span></label>
                            <textarea name="speaker_title_en" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">講者照片</label>
                        <input type="file" name="speaker_photo" class="form-control" accept="image/*">
                    </div>

                    <!-- 時間地點部分 -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">講座日期 <span class="text-danger">*</span></label>
                            <input type="date" name="lecture_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">講座時間 <span class="text-danger">*</span></label>
                            <input type="text" name="lecture_time" class="form-control" placeholder="例：13:30-15:30" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">地點(中文) <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">地點(英文) <span class="text-danger">*</span></label>
                            <input type="text" name="location_en" class="form-control" required>
                        </div>
                    </div>

                    <!-- 狀態與分類 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">狀態與分類</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">講座類別 <span class="text-danger">*</span></label>                                    <select name="category_id" class="form-select" required>
                                        <option value="">請選擇類別</option>
                                        <?php
                                        $categories_sql = "SELECT * FROM lecture_categories WHERE is_visible = 1 ORDER BY sort_order ASC";
                                        $categories_result = $conn->query($categories_sql);
                                        while($category = $categories_result->fetch_assoc()):
                                            ?>
                                            <option value="<?php echo $category['id']; ?>">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                                (<?php echo htmlspecialchars($category['name_en']); ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">狀態</label>
                                    <select name="status" class="form-select" required>
                                        <option value="coming">即將舉辦</option>
                                        <option value="passed">已辦理</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">排序順序</label>
                                    <input type="number" name="sort_order" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="is_visible" class="form-check-input" id="is_visible" checked>
                                <label class="form-check-label" for="is_visible">顯示於前台</label>
                            </div>
                        </div>
                    </div>

                    <!-- 內容描述 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">內容描述</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">講座描述(中文)</label>
                                    <textarea name="description" class="form-control" rows="4"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">講座描述(英文)</label>
                                    <textarea name="description_en" class="form-control" rows="4"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">議程(中文)</label>
                                    <textarea name="agenda" class="form-control" rows="4"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">議程(英文)</label>
                                    <textarea name="agenda_en" class="form-control" rows="4"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">講題摘要(中文)</label>
                                    <textarea name="summary" class="form-control" rows="4"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">講題摘要(英文)</label>
                                    <textarea name="summary_en" class="form-control" rows="4"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">講者簡介(中文)</label>
                                    <textarea name="speaker_intro" class="form-control" rows="4"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">講者簡介(英文)</label>
                                    <textarea name="speaker_intro_en" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 主辦單位 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">主辦與協辦單位</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- 主辦單位部分 -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">主辦單位(中文) <span class="text-danger">*</span></label>
                                    <input type="text" name="organizer" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">主辦單位(英文) <span class="text-danger">*</span></label>
                                    <input type="text" name="organizer_en" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">主辦單位網址</label>
                                    <input type="url" name="organizer_url" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">協辦單位(中文)</label>
                                    <textarea name="co_organizer" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">協辦單位(英文)</label>
                                    <textarea name="co_organizer_en" class="form-control" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">協辦單位網址</label>
                                <textarea name="co_organizer_urls" class="form-control" rows="3"></textarea>
                                <small class="text-muted">每行一個網址</small>
                            </div>
                        </div>
                    </div>

                    <!-- 報名相關 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">報名資訊</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">報名連結</label>
                                    <input type="url" name="signup_url" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">報名人數限制</label>
                                    <input type="number" name="signup_limit" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">報名截止日期</label>
                                    <input type="datetime-local" name="signup_deadline" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 線上會議 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">線上會議資訊</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">線上講座連結</label>
                                <input type="url" name="online_url" class="form-control">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">會議ID</label>
                                    <input type="text" name="meeting_id" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">會議密碼</label>
                                    <input type="text" name="meeting_password" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" name="submit" class="btn btn-primary">新增講座</button>
                        <a href="index.php" class="btn btn-secondary">取消</a>
                    </div>
                </form>
            </main>
        </div>
    </div>

<?php require_once '../includes/footer.php'; ?>