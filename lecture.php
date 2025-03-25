<?php
require_once 'cms-admin/config/database.php';

// 檢查是否有講座ID
if(!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

// 獲取講座資訊
$id = (int)$_GET['id'];
$sql = "SELECT l.*, c.name as category_name, c.name_en as category_name_en 
        FROM lectures l 
        LEFT JOIN lecture_categories c ON l.category_id = c.id 
        WHERE l.id = ? AND l.is_visible = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    header('Location: index.php');
    exit();
}

$lecture = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>國立臺灣師範大學｜<?php echo htmlspecialchars($lecture['title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($lecture['description']); ?>">
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
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href="assets/css/responsive.css" rel="stylesheet" />
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
            <a class="cta-btn" href="en/lecture.php?id=<?php echo $id; ?>">English</a>
        </div>
    </div>
</header>

<main class="main">
    <!-- Banner -->
    <div id="lecture_detail" class="lecture-detail">
        <div id="lecture-banner" class="lecture-banner">
            <img src="assets/img/banner/detail.jpg" alt="Lecture Detail Banner Image" class="lecture-image">
        </div>
    </div>

    <!-- 講座內容 -->
    <section class="lecture_section layout_padding-bottom">
        <div class="container">
            <div class="filters-content">
                <div class="row grid">
                    <div class="box all <?php echo $lecture['status']; ?>">
                        <div class="col-lg-4 col-sm-6">
                            <div class="img-box">
                                <?php if($lecture['speaker_photo']): ?>
                                    <img src="assets/img/speakers/<?php echo htmlspecialchars($lecture['speaker_photo']); ?>"
                                         alt="<?php echo htmlspecialchars($lecture['speaker']); ?>">
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
                                        <?php if($lecture['organizer_url']): ?>
                                            <a href="<?php echo htmlspecialchars($lecture['organizer_url']); ?>" target="_blank">
                                                <?php echo htmlspecialchars($lecture['organizer']); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($lecture['organizer']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if($lecture['co_organizer']): ?>
                                    <div class="sponsors">
                                        <span class="sponsors-title">協辦單位｜</span>
                                        <div class="sponsors-links">
                                            <?php
                                            $co_organizers = explode("\n", $lecture['co_organizer']);
                                            $co_organizer_urls = explode("\n", $lecture['co_organizer_urls']);
                                            foreach($co_organizers as $index => $co_org):
                                                $url = isset($co_organizer_urls[$index]) ? trim($co_organizer_urls[$index]) : '';
                                                ?>
                                                <?php if($url): ?>
                                                <a href="<?php echo htmlspecialchars($url); ?>" target="_blank">
                                                    <?php echo htmlspecialchars($co_org); ?>
                                                </a>
                                            <?php else: ?>
                                                <?php echo htmlspecialchars($co_org); ?>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
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
                </div>
            </div>
        </div>
    </section>

    <!-- 詳細內容 -->
    <div class="container">
        <div class="de_container">
            <?php if($lecture['summary']): ?>
                <h3>講題摘要</h3>
                <p><?php echo nl2br(htmlspecialchars($lecture['summary'])); ?></p>
            <?php endif; ?>

            <?php if($lecture['speaker_intro']): ?>
                <h3>講者簡介</h3>
                <p><?php echo nl2br(htmlspecialchars($lecture['speaker_intro'])); ?></p>
            <?php endif; ?>

            <?php if($lecture['agenda']): ?>
                <h3>議程</h3>
                <?php echo nl2br(htmlspecialchars($lecture['agenda'])); ?>
            <?php endif; ?>

            <?php if($lecture['description']): ?>
                <h3>講座描述</h3>
                <p><?php echo nl2br(htmlspecialchars($lecture['description'])); ?></p>
            <?php endif; ?>

            <?php if($lecture['online_url'] && ($lecture['meeting_id'] || $lecture['meeting_password'])): ?>
                <h3>線上會議資訊</h3>
                <?php if($lecture['meeting_id']): ?>
                    <p>會議 ID：<?php echo htmlspecialchars($lecture['meeting_id']); ?></p>
                <?php endif; ?>
                <?php if($lecture['meeting_password']): ?>
                    <p>會議密碼：<?php echo htmlspecialchars($lecture['meeting_password']); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
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
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/js/jquery-3.4.1.min.js"></script>
<script src="https://unpkg.com/isotope-layout@3.0.4/dist/isotope.pkgd.min.js"></script>
<script src="assets/js/custom.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>