<?php
require_once 'cms-admin/config/database.php';
$categories_sql = "SELECT * FROM lecture_categories WHERE is_visible = 1 ORDER BY sort_order ASC";
$categories_result = $conn->query($categories_sql);

$items_per_page = 5; // 每頁顯示5個講座
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // 當前頁碼
$offset = ($current_page - 1) * $items_per_page; // 計算偏移量
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>國立臺灣師範大學｜國際頂尖學者系列講座</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css" />
    <!--owl slider stylesheet -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <!-- nice select  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css" integrity="sha512-CruCP+TD3yXzlvvijET8wV5WxxEh5H8P4cmz0RFbKK6FlZ2sYl3AEsKlLPHbniXKSrDdFewhbmBK5skbdsASbQ==" crossorigin="anonymous" />
    <!-- font awesome style -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="assets/css/responsive.css" rel="stylesheet" />
    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">
<header id="header" class="header sticky-top">
    <div class="branding d-flex align-items-center">
        <div class="container position-relative d-flex align-items-center justify-content-end">
            <a href="index.php" class="logo d-flex align-items-center me-auto">
                <img src="assets/img/logo-blue.svg" alt="LOGO">
            </a>
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li class="dropdown">
                        <a href="#"><span>系列講座</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="index.php">全部系列</a></li>
                            <?php
                            // 重置結果指標
                            $categories_result->data_seek(0);
                            while($category = $categories_result->fetch_assoc()): ?>
                                <li><a href="index.php?category=<?php echo htmlspecialchars($category['slug']); ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>系列
                                    </a></li>
                            <?php endwhile; ?>
                        </ul>
                    </li>
                </ul>
                <i id="hbg" class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
            <a class="cta-btn" href="en/index.php">English</a>
        </div>
    </div>
</header>

<main class="main">
    <!-- Banner -->
    <div id="lecture_detail" class="lecture-detail">
        <div id="lecture-banner" class="lecture-banner">
            <?php
            if(isset($_GET['category'])) {
                $category_slug = $_GET['category'];
                $banner_image = 'detail.jpg'; // 預設圖片
                // 檢查是否有對應的 banner 圖片
                if(file_exists("assets/img/banner/{$category_slug}.jpg")) {
                    $banner_image = $category_slug . '.jpg';
                }
                ?>
                <img src="assets/img/banner/<?php echo $banner_image; ?>" alt="Banner Image" class="lecture-image">
            <?php } else { ?>
                <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner">
                        <!-- 一張圖 -->
                        <div class="carousel-item active">
                            <img src="assets/img/carousel/carousel-1.jpg" class="d-block w-100" alt="">
                        </div>
                        <!-- 一張圖 -->
                        <div class="carousel-item">
                            <img src="assets/img/carousel/carousel-2.jpg" class="d-block w-100" alt="">
                        </div>
                    </div>
                    <!-- 左箭頭 -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <!-- 右箭頭 -->
                    <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    <!-- 下方點點選單 -->
                    <div class="carousel-indicators">
<!--                        <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="0" class="active" aria-current="true"></button>-->
<!--                        <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="1"></button>-->
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- 講座列表 -->
    <section id="lecture_section" class="lecture_section layout_padding-bottom">
        <div class="container">
            <div class="heading_wrapper">
                <div class="heading_container">
                    <h2>
                        國際頂尖學者
                        <?php
                        if(isset($_GET['category'])) {
                            // 重置結果指標
                            $categories_result->data_seek(0);
                            while($category = $categories_result->fetch_assoc()) {
                                if($category['slug'] === $_GET['category']) {
                                    echo '【' . htmlspecialchars($category['name']) . '】';
                                    break;
                                }
                            }
                        }
                        ?>
                        系列講座
                    </h2>
                </div>
                <ul class="filters_menu">
                    <li class="active" data-filter="*">全部講座</li>
                    <li data-filter=".coming">即將舉辦</li>
                    <li data-filter=".passed">已辦理講座</li>
                </ul>
            </div>

            <div class="filters-content">
                <div class="row grid">
                    <?php
                    // 在處理 WHERE 條件的部分（約在第 190 行附近）修改為：
                    $where = '';
                    $params = [];
                    $types = '';

                    // 處理分類過濾
                    if(isset($_GET['category'])) {
                        $where = 'WHERE c.slug = ?';
                        $params[] = $_GET['category'];
                        $types .= 's';
                    }

                    // 處理狀態過濾
                    $filter = isset($_GET['filter']) ? $_GET['filter'] : '*';
                    if ($filter === '.coming') {
                        $where .= ($where ? ' AND ' : 'WHERE ') . 'l.status = "coming"';
                    } elseif ($filter === '.passed') {
                        $where .= ($where ? ' AND ' : 'WHERE ') . 'l.status = "passed"';
                    }

                    // 計算總記錄數的 SQL
                    $count_sql = "SELECT COUNT(*) as total FROM lectures l 
              LEFT JOIN lecture_categories c ON l.category_id = c.id 
              $where";

                    $count_stmt = $conn->prepare($count_sql);
                    if(!empty($params)) {
                        $count_stmt->bind_param($types, ...$params);
                    }
                    $count_stmt->execute();
                    $total_records = $count_stmt->get_result()->fetch_assoc()['total'];
                    $total_pages = ceil($total_records / $items_per_page);

                    // 修改主要查詢加入 LIMIT 和 OFFSET
                    // 主要查詢 SQL
                    $sql = "SELECT l.*, c.name as category_name 
                        FROM lectures l 
                        LEFT JOIN lecture_categories c ON l.category_id = c.id 
                        $where 
                        ORDER BY 
                            CASE 
                                WHEN l.status = 'coming' THEN 1 
                                ELSE 2 
                            END,
                            CASE 
                                WHEN l.status = 'coming' THEN l.lecture_date
                                ELSE NULL
                            END ASC,
                            CASE 
                                WHEN l.status != 'coming' THEN l.lecture_date
                                ELSE NULL
                            END DESC
                        LIMIT ? OFFSET ?";

                    // 添加分頁參數
                    $params[] = $items_per_page;
                    $params[] = $offset;
                    $types .= 'ii';

                    $stmt = $conn->prepare($sql);
                    if(!empty($params)) {
                        $stmt->bind_param($types, ...$params);
                    }
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while($lecture = $result->fetch_assoc()):
                        $status_class = $lecture['status'] == 'coming' ? 'coming' : 'passed';
                        ?>
                        <div class="box all <?php echo $status_class; ?>">
                            <div class="col-lg-4 col-sm-6">
                                <div class="img-box">
                                    <?php if($lecture['speaker_photo']): ?>
                                        <img src="assets/img/speakers/<?php echo htmlspecialchars($lecture['speaker_photo']); ?>" alt="<?php echo htmlspecialchars($lecture['speaker']); ?>">
                                    <?php else: ?>
                                        <img src="assets/img/avatar.png" alt="Default Avatar">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-8 col-sm-6">
                                <div class="detail-box">
                                    <h3><?php echo htmlspecialchars($lecture['title']); ?></h3>
                                    <h2><?php echo htmlspecialchars($lecture['speaker']); ?></h2>
                                    <p><?php echo nl2br(htmlspecialchars($lecture['speaker_title'])); ?></p>

                                    <dl class="lecture_detail">
                                        <div class="lecture-item">
                                            <dt>講座日期｜</dt>
                                            <dd><?php echo date('Y年m月d日', strtotime($lecture['lecture_date'])); ?></dd>
                                        </div>
                                        <div class="lecture-item">
                                            <dt>講座時間｜</dt>
                                            <dd><?php echo htmlspecialchars($lecture['lecture_time']); ?></dd>
                                        </div>
                                        <div class="lecture-item">
                                            <dt>講座地點｜</dt>
                                            <dd><?php echo htmlspecialchars($lecture['location']); ?></dd>
                                        </div>
                                    </dl>

                                    <div class="sponsors">
                                        <span class="sponsors-title">主辦單位｜</span>
                                        <div class="sponsors-links">
                                            <?php
                                            $organizers = explode("\n", $lecture['organizer']);
                                            foreach($organizers as $org):
                                                ?>
                                                <a href="#" target="_blank"><?php echo htmlspecialchars($org); ?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <?php if($lecture['co_organizer']): ?>
                                        <div class="sponsors">
                                            <span class="sponsors-title">協辦單位｜</span>
                                            <div class="sponsors-links">
                                                <?php
                                                $co_organizers = explode("\n", $lecture['co_organizer']);
                                                foreach($co_organizers as $co_org):
                                                    ?>
                                                    <a href="#" target="_blank"><?php echo htmlspecialchars($co_org); ?></a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($lecture['agenda']||$lecture['speaker_intro']||$lecture['description']||$lecture['summary']): ?>
                                    <a class="btn-box" href="lecture.php?id=<?php echo $lecture['id']; ?>">詳細資訊</a>
                                    <?php endif; ?>
                                    <?php if($lecture['signup_url']): ?>
                                        <a class="btn-box" href="<?php echo htmlspecialchars($lecture['signup_url']); ?>" target="_blank">報名連結</a>
                                    <?php endif; ?>
                                    <?php if($lecture['online_url']): ?>
                                        <a class="btn-box" href="<?php echo htmlspecialchars($lecture['online_url']); ?>" target="_blank">線上講座連結</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="pagination-container">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php
                            // 構建基礎 URL 參數
                            $url_params = [];
                            if(isset($_GET['category'])) {
                                $url_params['category'] = $_GET['category'];
                            }
                            if(isset($_GET['filter']) && $_GET['filter'] !== '*') {
                                $url_params['filter'] = $_GET['filter'];
                            }

                            // 生成 URL 函數
                            function buildPageUrl($page, $params) {
                                $params['page'] = $page;
                                return '?' . http_build_query($params);
                            }
                            ?>

                            <?php if($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo buildPageUrl($current_page - 1, $url_params); ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo buildPageUrl($i, $url_params); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo buildPageUrl($current_page + 1, $url_params); ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="footer_section">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="assets/img/logo-white.svg" alt="LOGO">
            </div>
            <div class="footer-contact">
                <p>電話：02-7749-1324</p>
                <p>Email：friend29@ntnu.edu.tw</p>
                <p>地址：106308台北市大安區和平東路一段162號</p>
            </div>
        </div>
        <div class="footer-info">
            <p>&copy; <span id="displayYear"></span> All Rights Reserved By
                <a href="https://www.ntnu.edu.tw/">National Taiwan Normal University</a>
            </p>
        </div>
    </div>
</footer>

<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
</a>

<!-- Preloader -->
<div id="preloader"></div>

<!-- JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<!-- jQery -->
<script src="assets/js/jquery-3.4.1.min.js"></script>
<!-- popper js -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
</script>
<!-- bootstrap js -->
<script src="assets/js/bootstrap.js"></script>
<!-- owl slider -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js">
</script>
<!-- isotope js -->
<script src="https://unpkg.com/isotope-layout@3.0.4/dist/isotope.pkgd.min.js"></script>
<!-- nice select -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
<!-- custom js -->
<script src="assets/js/custom.js"></script>
<!-- Main JS File -->
<script src="assets/js/main.js"></script>
</body>
</html>