<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';
require_once '../includes/header.php';
?>

    <div class="container-fluid">
        <div class="row">
            <?php require_once '../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">講座管理</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="add.php" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus"></i> 新增講座
                        </a>
                    </div>
                </div>

                <?php if(isset($_SESSION['message'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <!-- 搜尋和篩選 -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">講座類別</label>
                                <select name="category_id" class="form-select">
                                    <option value="">全部類別</option>
                                    <?php
                                    $categories_sql = "SELECT * FROM lecture_categories WHERE is_visible = 1 ORDER BY sort_order ASC";
                                    $categories_result = $conn->query($categories_sql);
                                    while($category = $categories_result->fetch_assoc()):
                                        $selected = (isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $selected; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">狀態</label>
                                <select name="status" class="form-select">
                                    <option value="">全部狀態</option>
                                    <option value="coming" <?php echo (isset($_GET['status']) && $_GET['status'] == 'coming') ? 'selected' : ''; ?>>即將舉辦</option>
                                    <option value="passed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'passed') ? 'selected' : ''; ?>>已辦理</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">搜尋</label>
                                <input type="text" name="keyword" class="form-control" placeholder="搜尋講座標題或講者" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">搜尋</button>
                                    <a href="index.php" class="btn btn-secondary">重置</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>類別</th>
                            <th>講座標題</th>
                            <th>講者</th>
                            <th>日期</th>
                            <th>時間</th>
                            <th>地點</th>
                            <th>狀態</th>
                            <th>報名</th>
                            <th>顯示</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        // 構建查詢條件
                        $where_conditions = [];
                        $params = [];
                        $param_types = "";

                        if(!empty($_GET['category_id'])) {
                            $where_conditions[] = "l.category_id = ?";
                            $params[] = (int)$_GET['category_id'];
                            $param_types .= "i";
                        }

                        if(!empty($_GET['status'])) {
                            $where_conditions[] = "l.status = ?";
                            $params[] = $_GET['status'];
                            $param_types .= "s";
                        }

                        if(!empty($_GET['keyword'])) {
                            $where_conditions[] = "(l.title LIKE ? OR l.title_en LIKE ? OR l.speaker LIKE ? OR l.speaker_en LIKE ?)";
                            $keyword = "%{$_GET['keyword']}%";
                            $params[] = $keyword;
                            $params[] = $keyword;
                            $params[] = $keyword;
                            $params[] = $keyword;
                            $param_types .= "ssss";
                        }

                        $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

                        $sql = "SELECT l.*, c.name as category_name, c.name_en as category_name_en 
                            FROM lectures l 
                            LEFT JOIN lecture_categories c ON l.category_id = c.id 
                            {$where_clause} 
                            ORDER BY l.lecture_date DESC";

                        $stmt = $conn->prepare($sql);
                        if(!empty($params)) {
                            $stmt->bind_param($param_types, ...$params);
                        }
                        $stmt->execute();
                        $result = $stmt->get_result();

                        while($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($row['title']); ?>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($row['title_en']); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['speaker']); ?>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($row['speaker_en']); ?></small>
                                </td>
                                <td><?php echo $row['lecture_date']; ?></td>
                                <td><?php echo $row['lecture_time']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($row['location']); ?>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($row['location_en']); ?></small>
                                </td>
                                <td>
                                <span class="badge bg-<?php echo $row['status'] == 'coming' ? 'primary' : 'secondary'; ?>">
                                    <?php echo $row['status'] == 'coming' ? '即將舉辦' : '已辦理'; ?>
                                </span>
                                </td>
                                <td>
                                    <?php if($row['signup_url']): ?>
                                        <a href="<?php echo htmlspecialchars($row['signup_url']); ?>" target="_blank"
                                           class="btn btn-sm btn-success" title="報名連結">
                                            <i class="bi bi-link-45deg"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if($row['signup_limit']): ?>
                                        <span class="badge bg-info">
                                        <?php echo $row['current_signup']; ?>/<?php echo $row['signup_limit']; ?>
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                <span class="badge bg-<?php echo $row['is_visible'] ? 'success' : 'danger'; ?>">
                                    <?php echo $row['is_visible'] ? '顯示' : '隱藏'; ?>
                                </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" title="編輯">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('確定要刪除此講座嗎？')" title="刪除">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
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