<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>講座管理系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/series_lecture/cms-admin/assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">講座管理系統</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav flex-row"> <!-- 修改為 flex-row 讓按鈕並排 -->
        <div class="nav-item text-nowrap me-3"> <!-- 加入 me-3 增加右邊距 -->
            <a class="nav-link px-3" href="/series_lecture/" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i> 回到前台
            </a>
        </div>
        <div class="nav-item text-nowrap">
            <a class="nav-link px-3" href="/series_lecture/cms-admin/logout.php">
                <i class="bi bi-box-arrow-right"></i> 登出
            </a>
        </div>
    </div>
</header>
