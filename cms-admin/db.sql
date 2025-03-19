-- 講座類別表
CREATE TABLE `lecture_categories` (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `name` varchar(50) NOT NULL COMMENT '類別名稱(中文)',
                                      `name_en` varchar(50) NOT NULL COMMENT '類別名稱(英文)',
                                      `slug` varchar(50) NOT NULL COMMENT '網址別名',
                                      `description` text COMMENT '描述(中文)',
                                      `description_en` text COMMENT '描述(英文)',
                                      `sort_order` int(11) DEFAULT 0 COMMENT '排序順序',
                                      `is_visible` tinyint(1) DEFAULT 1 COMMENT '是否顯示',
                                      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                      PRIMARY KEY (`id`),
                                      UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 預設類別資料
INSERT INTO `lecture_categories` (`name`, `name_en`, `slug`, `description`, `description_en`, `sort_order`, `is_visible`) VALUES
                                                                                                                              ('科學系列', 'Science Series', 'science', '科學相關講座', 'Science related lectures', 1, 1),
                                                                                                                              ('經濟系列', 'Economy Series', 'economy', '經濟相關講座', 'Economy related lectures', 2, 1),
                                                                                                                              ('文史系列', 'History Series', 'history', '文史相關講座', 'History and literature related lectures', 3, 1)
CREATE TABLE `lectures` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
    -- 基本資訊
                            `title` varchar(255) NOT NULL COMMENT '講座標題(中文)',
                            `title_en` varchar(255) NOT NULL COMMENT '講座標題(英文)',
                            `speaker` varchar(100) NOT NULL COMMENT '講者姓名(中文)',
                            `speaker_en` varchar(100) NOT NULL COMMENT '講者姓名(英文)',
                            `speaker_title` text NOT NULL COMMENT '講者頭銜(中文)',
                            `speaker_title_en` text NOT NULL COMMENT '講者頭銜(英文)',
                            `speaker_photo` varchar(255) DEFAULT NULL COMMENT '講者照片',

    -- 時間地點
                            `lecture_date` date NOT NULL COMMENT '講座日期',
                            `lecture_time` varchar(50) NOT NULL COMMENT '講座時間',
                            `location` varchar(255) NOT NULL COMMENT '講座地點(中文)',
                            `location_en` varchar(255) NOT NULL COMMENT '講座地點(英文)',

    -- 狀態與分類
                            `status` enum('coming','passed') NOT NULL DEFAULT 'coming' COMMENT '狀態',
                            `category_id` int(11) NOT NULL COMMENT '講座類別ID',

    -- 內容描述
                            `description` text COMMENT '講座描述(中文)',
                            `description_en` text COMMENT '講座描述(英文)',
                            `agenda` text COMMENT '議程(中文)',
                            `agenda_en` text COMMENT '議程(英文)',
                            `summary` text COMMENT '講題摘要(中文)',
                            `summary_en` text COMMENT '講題摘要(英文)',
                            `speaker_intro` text COMMENT '講者簡介(中文)',
                            `speaker_intro_en` text COMMENT '講者簡介(英文)',

    -- 主辦單位
                            `organizer` varchar(255) NOT NULL COMMENT '主辦單位(中文)',
                            `organizer_en` varchar(255) NOT NULL COMMENT '主辦單位(英文)',
                            `organizer_url` varchar(255) DEFAULT NULL COMMENT '主辦單位網址',
                            `co_organizer` text COMMENT '協辦單位(中文)',
                            `co_organizer_en` text COMMENT '協辦單位(英文)',
                            `co_organizer_urls` text COMMENT '協辦單位網址(JSON格式)',

    -- 報名相關
                            `signup_url` varchar(255) DEFAULT NULL COMMENT '報名連結',
                            `signup_limit` int(11) DEFAULT NULL COMMENT '報名人數限制',
                            `signup_deadline` datetime DEFAULT NULL COMMENT '報名截止日期',
                            `current_signup` int(11) DEFAULT 0 COMMENT '目前報名人數',

    -- 線上會議
                            `online_url` varchar(255) DEFAULT NULL COMMENT '線上講座連結',
                            `meeting_id` varchar(100) DEFAULT NULL COMMENT '會議ID',
                            `meeting_password` varchar(100) DEFAULT NULL COMMENT '會議密碼',

    -- 其他
                            `detail_url` varchar(255) DEFAULT NULL COMMENT '詳細資訊連結',
                            `sort_order` int(11) DEFAULT 0 COMMENT '排序順序',
                            `is_visible` tinyint(1) DEFAULT 1 COMMENT '是否顯示',
                            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
