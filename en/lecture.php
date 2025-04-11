<?php
require_once '../cms-admin/config/database.php';

// Get website settings
$settings_sql = "SELECT * FROM website_settings LIMIT 1";
$settings_result = $conn->query($settings_sql);
$settings = $settings_result->fetch_assoc();

// Check if lecture ID exists
if(!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

// Get lecture information
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
$categories_sql = "SELECT * FROM lecture_categories WHERE is_visible = 1 ORDER BY sort_order ASC";
$categories_result = $conn->query($categories_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>National Taiwan Normal University | <?php echo htmlspecialchars($lecture['title_en']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($lecture['description_en']); ?>">
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
    <!-- Google Analytics -->
    <?php if(!empty($settings['google_analytics_code'])): ?>
        <?php echo $settings['google_analytics_code']; ?>
    <?php endif; ?>
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
            <a class="cta-btn" href="../lecture.php?id=<?php echo $id; ?>">中文</a>
        </div>
    </div>
</header>

<main class="main">
    <!-- Banner -->
    <div id="lecture_detail" class="lecture-detail">
        <div id="lecture-banner" class="lecture-banner">
            <img src="../assets/img/banner/detail.jpg" alt="Lecture Detail Banner Image" class="lecture-image">
        </div>
    </div>

    <!-- Lecture Content -->
    <section class="lecture_section layout_padding-bottom">
        <div class="container">
            <div class="filters-content">
                <div class="row grid">
                    <div class="box all <?php echo $lecture['status']; ?>">
                        <div class="col-lg-4 col-sm-6">
                            <div class="img-box">
                                <?php if($lecture['speaker_photo']): ?>
                                    <img src="../assets/img/speakers/<?php echo htmlspecialchars($lecture['speaker_photo']); ?>"
                                         alt="<?php echo htmlspecialchars($lecture['speaker_en']); ?>">
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
                                        <?php if($lecture['organizer_url']): ?>
                                            <a href="<?php echo htmlspecialchars($lecture['organizer_url']); ?>" target="_blank">
                                                <?php echo htmlspecialchars($lecture['organizer_en']); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($lecture['organizer_en']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if($lecture['co_organizer_en']): ?>
                                    <div class="sponsors">
                                        <span class="sponsors-title">Co-organizer |</span>
                                        <div class="sponsors-links">
                                            <?php
                                            if($lecture['co_organizer_en']) {
                                                $co_organizers = array_map('trim', explode("\n", $lecture['co_organizer_en']));
                                                $co_organizer_urls = array_map('trim', explode("\n", $lecture['co_organizer_urls']));

                                                foreach($co_organizers as $index => $co_org):
                                                    $co_org = trim($co_org);
                                                    if(empty($co_org)) continue;

                                                    $url = isset($co_organizer_urls[$index]) ? trim($co_organizer_urls[$index]) : '';

                                                    if($url): ?>
                                                        <div class="sponsor-item">
                                                            <a href="<?php echo htmlspecialchars($url); ?>" target="_blank">
                                                                <?php echo htmlspecialchars($co_org); ?>
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="sponsor-item">
                                                            <?php echo htmlspecialchars($co_org); ?>
                                                        </div>
                                                    <?php endif;
                                                endforeach;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php
                                // Check if signup URL exists and deadline hasn't passed
                                if($lecture['signup_url']):
                                    $show_signup = true;

                                    // If signup deadline is set, check if it's passed
                                    if($lecture['signup_deadline']) {
                                        $deadline = new DateTime($lecture['signup_deadline']);
                                        $now = new DateTime();
                                        if($now > $deadline) {
                                            $show_signup = false;
                                        }
                                    }

                                    // Only show signup link if not past deadline
                                    if($show_signup):
                                        ?>
                                        <a class="btn-box" href="<?php echo htmlspecialchars($lecture['signup_url']); ?>" target="_blank">Registration</a>
                                    <?php
                                    endif;
                                endif;
                                ?>
                                <?php if($lecture['online_url']): ?>
                                    <a class="btn-box" href="<?php echo htmlspecialchars($lecture['online_url']); ?>" target="_blank">Online Lecture Link</a>
                                <?php endif; ?>
                                <?php if($lecture['video_url']): ?>
                                    <a class="btn-box" href="<?php echo htmlspecialchars($lecture['video_url']); ?>" target="_blank">Lecture Recording</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Content -->
    <div class="container">
        <div class="de_container">
            <?php if($lecture['summary_en']): ?>
                <h3>【Abstract】</h3>
                <p><?php echo nl2br(htmlspecialchars($lecture['summary_en'])); ?></p>
            <?php endif; ?>

            <?php if($lecture['speaker_intro_en']): ?>
                <h3>【Speaker Biography】</h3>
                <p><?php echo nl2br(htmlspecialchars($lecture['speaker_intro_en'])); ?></p>
            <?php endif; ?>

            <?php if($lecture['agenda_en']): ?>
                <h3>【Agenda】</h3>
                <div class="agenda-content">
                    <?php
                    $agenda = str_replace("\r\n", "\n", $lecture['agenda_en']);
                    echo nl2br(htmlspecialchars($agenda));
                    ?>
                </div>
            <?php endif; ?>

            <?php if($lecture['description_en']): ?>
                <br>
                <?php echo $lecture['description_en']; ?>
            <?php endif; ?>

            <?php if($lecture['online_url'] && ($lecture['meeting_id'] || $lecture['meeting_password'])): ?>
                <h3>Online Meeting Information</h3>
                <?php if($lecture['meeting_id']): ?>
                    <p>Meeting ID: <?php echo htmlspecialchars($lecture['meeting_id']); ?></p>
                <?php endif; ?>
                <?php if($lecture['meeting_password']): ?>
                    <p>Meeting Password: <?php echo htmlspecialchars($lecture['meeting_password']); ?></p>
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
                <img src="../assets/img/logo-white.svg" alt="LOGO">
            </div>
            <div class="footer-contact">
                <?php echo $settings['copyright_text_en']?? '' ; ?>
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

<!-- JS Files -->
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/aos/aos.js"></script>
<script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="../assets/js/jquery-3.4.1.min.js"></script>
<script src="https://unpkg.com/isotope-layout@3.0.4/dist/isotope.pkgd.min.js"></script>
<script src="../assets/js/custom.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>