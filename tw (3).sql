-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost
-- 生成日時: 
-- サーバのバージョン： 8.0.18
-- PHP のバージョン: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `tw`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `tweet_id` int(11) NOT NULL,
  `comment` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `posted` datetime NOT NULL,
  `comment_user` varchar(20) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `comments`
--

INSERT INTO `comments` (`id`, `tweet_id`, `comment`, `posted`, `comment_user`) VALUES
(42, 41, 'test', '2020-07-22 16:12:23', 'test'),
(43, 41, 'test3', '2020-07-27 11:38:36', 'test3');

-- --------------------------------------------------------

--
-- テーブルの構造 `follow`
--

CREATE TABLE `follow` (
  `id` int(11) NOT NULL,
  `follow_name` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `follow_add_time` datetime NOT NULL,
  `follower_name` varchar(20) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `follow`
--

INSERT INTO `follow` (`id`, `follow_name`, `follow_add_time`, `follower_name`) VALUES
(19, 'test2', '2020-07-29 14:41:39', 'test3'),
(27, 'test3', '2020-07-29 15:58:20', 'test2'),
(28, 'test', '2020-07-29 15:58:28', 'test2');

-- --------------------------------------------------------

--
-- テーブルの構造 `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `like_count` int(11) NOT NULL,
  `tweet_id` int(11) NOT NULL,
  `like_name` varchar(20) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `likes`
--

INSERT INTO `likes` (`id`, `like_count`, `tweet_id`, `like_name`) VALUES
(88, 1, 34, 'test2'),
(91, 1, 39, 'test'),
(93, 1, 11, 'test'),
(96, 1, 42, 'test'),
(104, 1, 18, 'test'),
(105, 1, 34, 'test'),
(106, 1, 14, 'test'),
(110, 1, 41, 'test2'),
(145, 1, 40, 'test3'),
(146, 1, 41, 'test3'),
(147, 1, 43, 'test3'),
(150, 1, 44, 'test'),
(161, 1, 41, 'test'),
(168, 1, 45, 'test'),
(171, 1, 47, 'test2'),
(172, 1, 40, 'test2');

-- --------------------------------------------------------

--
-- テーブルの構造 `tweets`
--

CREATE TABLE `tweets` (
  `id` int(11) NOT NULL,
  `tweet` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `posted` datetime NOT NULL,
  `user_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `tweets`
--

INSERT INTO `tweets` (`id`, `tweet`, `posted`, `user_name`) VALUES
(11, '今日も引き続きTwitterアプリっぽいものを作成中。\r\nなんか時間がかかる・・・', '2020-07-17 11:06:21', 'test'),
(14, 'ツイート一覧画面の作成中', '2020-07-17 11:31:18', 'test'),
(34, 'バーガーメニューの高さが合わない…', '2020-07-21 14:42:21', 'test2'),
(40, 'テスト', '2020-07-22 13:15:03', 'test2'),
(41, 'イイね機能の実装でデータベースの操作を悩んでたら2時間ぐらいけいかしてしまった…', '2020-07-22 15:00:00', 'test'),
(43, '今日は退会画面の作成まで終わり。\r\nあとはフォロ―リスト、問合せページを作成すればひとまずは完了。\r\n\r\n残りもがんばろ。', '2020-07-27 12:29:41', 'test3'),
(44, 'やっとお問い合わせのメール送信関連も完了したから、あとはフォローリストとフォローしたユーザーの投稿一覧画面を作成すれば完了だ…\r\n\r\n疲れた。。', '2020-07-27 16:07:06', 'test'),
(45, 'テスト', '2020-07-27 16:09:42', 'test'),
(46, '今日はフォローに関するページ・機能を実装中。\r\n\r\n明日の発表の為になんとか間に合わせたいところ…', '2020-07-29 13:21:50', 'test3'),
(47, 'フォローをする為の項目が完成したから、あとはフォローリストのページから遷移出来るようにするのと、一覧からもフォロー削除出来るようにして完成。\r\n\r\nあと少しだから頑張ろ。。', '2020-07-29 14:51:26', 'test2');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `user_id`, `password`, `user_name`) VALUES
(43, 'test', '$2y$10$BsBX254C/kQtBJc4R2NPqeT4fF966BnW13Fj2LdTH0Eo/q3aHRkQu', 'test'),
(44, 'test2', '$2y$10$EG8rgh.Vw1I4oIzePKlWh.q9zv.XpaUIT1v2ol1EMIcMXaW9o5z02', 'test2'),
(47, 'test3', '$2y$10$UoW1o.2r662fY8W9tDCsneAfXul7Lf2qTx7zak9AZHjnkldMwfDee', 'test3');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `follow`
--
ALTER TABLE `follow`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `tweets`
--
ALTER TABLE `tweets`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- ダンプしたテーブルのAUTO_INCREMENT
--

--
-- テーブルのAUTO_INCREMENT `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- テーブルのAUTO_INCREMENT `follow`
--
ALTER TABLE `follow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- テーブルのAUTO_INCREMENT `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- テーブルのAUTO_INCREMENT `tweets`
--
ALTER TABLE `tweets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- テーブルのAUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
