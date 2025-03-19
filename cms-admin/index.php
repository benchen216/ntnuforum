<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';
require_once 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>

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
        </main>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
