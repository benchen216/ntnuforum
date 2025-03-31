<?php
require_once '../cms-admin/config/database.php';
$categories_sql = "SELECT * FROM lecture_categories WHERE is_visible = 1 ORDER BY sort_order ASC";
$categories_result = $conn->query($categories_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>National Taiwan Normal University | Distinguished International Scholar Lecture Series</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="../assets/css/responsive.css" rel="stylesheet" />
    <link href="../assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">
<header id="header" class="header sticky-top">
    <div class="branding d-flex align-items-center">
        <div class="container position-relative d-flex align-items-center justify-content-end">
            <a href="index.php" class="logo d-flex align-items-center me-auto">
                <img src="../assets/img/logo-blue.svg" alt="LOGO">
            </a>
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li class="dropdown">
                        <a href="#"><span>Lecture Series</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="index.php">All Series</a></li>
                            <?php
                            // Reset result pointer
                            $categories_result->data_seek(0);
                            while($category = $categories_result->fetch_assoc()): ?>
                                <li><a href="index.php?category=<?php echo htmlspecialchars($category['slug']); ?>">
                                        <?php echo htmlspecialchars($category['name_en']); ?>
                                    </a></li>
                            <?php endwhile; ?>
                        </ul>
                    </li>
                </ul>
                <i id="hbg" class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
            <a class="cta-btn" href="../index.php">中文</a>
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
                <div id="carouselIndicators" class="carousel slide" data-bs-ride="carousel">
                    <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                        <div class="carousel-inner">
                            <!-- 一張圖 -->
                            <div class="carousel-item active">
                                <img src="assets/img/carousel/carousel-1.jpg" class="d-block w-100" alt="">
                            </div><!-- End 一張圖 -->
                            <!-- 一張圖 -->
                            <div class="carousel-item">
                                <img src="../assets/img/carousel/carousel-2.jpg" class="d-block w-100" alt="">
                            </div><!-- End 一張圖 -->
                        </div>
                        <!-- 左箭頭 -->
                        <a class="carousel-control-prev" href="#hero-carousel" role="button" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </a>
                        <!-- 右箭頭 -->
                        <a class="carousel-control-next" href="#hero-carousel" role="button" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </a>
                        <!-- 下方點點選單 (只顯示兩個點) -->
                        <div class="carousel-indicators">
<!--                            <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>-->
<!--                            <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="1" aria-label="Slide 2"></button>-->
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Lecture List -->
    <section class="lecture_section layout_padding-bottom">
        <div class="container">
            <div class="heading_wrapper">
                <div class="heading_container">
                    <h2>
                        Distinguished International Scholar
                        <?php
                        if(isset($_GET['category'])) {
                            // 重置結果指標
                            $categories_result->data_seek(0);
                            while($category = $categories_result->fetch_assoc()) {
                                if($category['slug'] === $_GET['category']) {
                                    echo '[' . htmlspecialchars($category['name_en']) . ']';
                                    break;
                                }
                            }
                        }
                        ?>
                        Lecture Series
                    </h2>
                </div>
                <ul class="filters_menu">
                    <li class="active" data-filter="*">All</li>
                    <li data-filter=".coming">Upcoming</li>
                    <li data-filter=".passed">Past</li>
                </ul>
            </div>

            <div class="filters-content">
                <div class="row grid">
                    <?php
                    $where = '';
                    $params = [];
                    $types = '';

                    if(isset($_GET['category'])) {
                        $where = 'WHERE c.slug = ?';
                        $params[] = $_GET['category'];
                        $types .= 's';
                    }

                    $sql = "SELECT l.*, c.name_en as category_name 
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
    END DESC";

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
                                        <img src="../assets/img/speakers/<?php echo htmlspecialchars($lecture['speaker_photo']); ?>" alt="<?php echo htmlspecialchars($lecture['speaker_en']); ?>">
                                    <?php else: ?>
                                        <img src="../assets/img/avatar.png" alt="Default Avatar">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-8 col-sm-6">
                                <div class="detail-box">
                                    <h3><?php echo htmlspecialchars($lecture['title_en']); ?></h3>
                                    <h2><?php echo htmlspecialchars($lecture['speaker_en']); ?></h2>
                                    <p><?php echo nl2br(htmlspecialchars($lecture['speaker_title_en'])); ?></p>

                                    <dl class="lecture_detail">
                                        <div class="lecture-item">
                                            <dt>Date |</dt>
                                            <dd><?php echo date('F j, Y', strtotime($lecture['lecture_date'])); ?></dd>
                                        </div>
                                        <div class="lecture-item">
                                            <dt>Time |</dt>
                                            <dd><?php echo htmlspecialchars($lecture['lecture_time']); ?></dd>
                                        </div>
                                        <div class="lecture-item">
                                            <dt>Location |</dt>
                                            <dd><?php echo htmlspecialchars($lecture['location_en']); ?></dd>
                                        </div>
                                    </dl>

                                    <div class="sponsors">
                                        <span class="sponsors-title">Organizer |</span>
                                        <div class="sponsors-links">
                                            <?php
                                            $organizers = explode("\n", $lecture['organizer_en']);
                                            foreach($organizers as $org):
                                                ?>
                                                <a href="#" target="_blank"><?php echo htmlspecialchars($org); ?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <?php if($lecture['co_organizer_en']): ?>
                                        <div class="sponsors">
                                            <span class="sponsors-title">Co-organizer |</span>
                                            <div class="sponsors-links">
                                                <?php
                                                $co_organizers = explode("\n", $lecture['co_organizer_en']);
                                                foreach($co_organizers as $co_org):
                                                    ?>
                                                    <a href="#" target="_blank"><?php echo htmlspecialchars($co_org); ?></a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <a class="btn-box" href="lecture.php?id=<?php echo $lecture['id']; ?>">Details</a>
                                    <?php if($lecture['signup_url']): ?>
                                        <a class="btn-box" href="<?php echo htmlspecialchars($lecture['signup_url']); ?>" target="_blank">Registration</a>
                                    <?php endif; ?>
                                    <?php if($lecture['online_url']): ?>
                                        <a class="btn-box" href="<?php echo htmlspecialchars($lecture['online_url']); ?>" target="_blank">Online Lecture Link</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
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
                <img src="../assets/img/logo-white.svg" alt="LOGO">
            </div>
            <div class="footer-contact">
                <p>Tel: 02-7749-1324</p>
                <p>Email: friend29@ntnu.edu.tw</p>
                <p>Address: No. 162, Sec. 1, Heping E. Rd., Da'an Dist., Taipei City 106308, Taiwan (R.O.C.)</p>
            </div>
        </div>
        <div class="footer-info">
            <p>&copy; <span id="displayYear"></span> All Rights Reserved By
                <a href="https://www.ntnu.edu.tw/en/">National Taiwan Normal University</a>
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

<!-- Vendor JS Files -->
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/php-email-form/validate.js"></script>
<script src="../assets/vendor/aos/aos.js"></script>
<script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>
<!-- jQery -->
<script src="../assets/js/jquery-3.4.1.min.js"></script>
<!-- popper js -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
</script>
<!-- bootstrap js -->
<script src="../assets/js/bootstrap.js"></script>
<!-- owl slider -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js">
</script>
<!-- isotope js -->
<script src="https://unpkg.com/isotope-layout@3.0.4/dist/isotope.pkgd.min.js"></script>
<!-- nice select -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
<!-- custom js -->
<script src="../assets/js/custom.js"></script>
<!-- Main JS File -->
<script src="../assets/js/main.js"></script>
</body>
</html>