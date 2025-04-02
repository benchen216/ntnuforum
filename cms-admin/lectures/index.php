<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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
                <h1 class="h2">儀表板</h1>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">總講座數</h5>
                            <?php
                            $sql = "SELECT COUNT(*) as total FROM lectures";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc();
                            ?>
                            <p class="card-text display-4"><?php echo $row['total']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">即將舉辦</h5>
                            <?php
                            $sql = "SELECT COUNT(*) as coming FROM lectures WHERE status = 'coming'";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc();
                            ?>
                            <p class="card-text display-4"><?php echo $row['coming']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">已辦理</h5>
                            <?php
                            $sql = "SELECT COUNT(*) as passed FROM lectures WHERE status = 'passed'";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc();
                            ?>
                            <p class="card-text display-4"><?php echo $row['passed']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

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

                    // 分頁設定
                    $items_per_page = 10; // 每頁顯示的項目數
                    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($current_page - 1) * $items_per_page;

                    // 計算總記錄數
                    $count_sql = "SELECT COUNT(*) as total FROM lectures l {$where_clause}";
                    $count_stmt = $conn->prepare($count_sql);
                    if(!empty($params)) {
                        $count_stmt->bind_param($param_types, ...$params);
                    }
                    $count_stmt->execute();
                    $count_result = $count_stmt->get_result();
                    $total_items = $count_result->fetch_assoc()['total'];
                    $total_pages = ceil($total_items / $items_per_page);

                    // 獲取分頁數據
                    $sql = "SELECT l.*, c.name as category_name, c.name_en as category_name_en 
                            FROM lectures l 
                            LEFT JOIN lecture_categories c ON l.category_id = c.id 
                            {$where_clause} 
                            ORDER BY l.lecture_date DESC
                            LIMIT ?, ?";

                    $stmt = $conn->prepare($sql);

                    // 添加 LIMIT 參數
                    $limit_params = $params;
                    $limit_params[] = $offset;
                    $limit_params[] = $items_per_page;
                    $limit_param_types = $param_types . "ii";

                    if(!empty($limit_params)) {
                        $stmt->bind_param($limit_param_types, ...$limit_params);
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

            <!-- 分頁控制 -->
            <?php if($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php
                        // 保留所有搜尋參數
                        $query_params = $_GET;

                        // 上一頁按鈕
                        if($current_page > 1):
                            $query_params['page'] = $current_page - 1;
                            $prev_link = '?' . http_build_query($query_params);
                            ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $prev_link; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        // 頁碼按鈕
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);

                        // 如果當前頁面靠近開始或結束，調整顯示的頁碼範圍
                        if($start_page <= 3) {
                            $end_page = min(5, $total_pages);
                        }
                        if($end_page >= $total_pages - 2) {
                            $start_page = max(1, $total_pages - 4);
                        }

                        for($i = $start_page; $i <= $end_page; $i++):
                            $query_params['page'] = $i;
                            $page_link = '?' . http_build_query($query_params);
                            ?>
                            <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo $page_link; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- 下一頁按鈕 -->
                        <?php
                        if($current_page < $total_pages):
                            $query_params['page'] = $current_page + 1;
                            $next_link = '?' . http_build_query($query_params);
                            ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo $next_link; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <!-- 顯示分頁資訊 -->
            <div class="text-center mt-2 text-muted">
                <small>
                    顯示 <?php echo $total_items; ?> 筆資料中的
                    <?php echo min(($current_page - 1) * $items_per_page + 1, $total_items); ?> -
                    <?php echo min($current_page * $items_per_page, $total_items); ?> 筆
                </small>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
