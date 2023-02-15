<?php
require_once "functions/db.inc.php";
session_start();
//sessionを利用してログインユーザー名取得(後述のDBとの照合 & 登録時に利用)
$userName = $_SESSION["username"];

//ログインされてない場合強制的にログインページにリダイレクト
if (isset($_SESSION["login"])) {
  if ($_SESSION["login"] != true) {
    unset($_SESSION["username"]);
    header("location:login.php");
    exit;
  }
} else {
  header("location:login.php");
  exit;
}
if ($_SERVER["REQUEST_METHOD"] === "GET") {
  try {
    //初回レンダーで投稿データの取得
    $pdo = db_init();
    $sql = "select tweets.*,count(likes.like_count) 
            from tweets 
            LEFT OUTER join likes 
            on tweets.id=likes.tweet_id 
            group by tweets.id
            order by posted desc";
    $stmt = $pdo->query($sql);
    $tweetData = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}


$search = $_GET["search"];
if (isset($_GET["search"])) {
  try {
    $pdo = db_init();
    $sql = "select tweets.*,count(likes.like_count) 
            from tweets 
            LEFT OUTER join likes 
            on tweets.id=likes.tweet_id 
            where tweet like ? 
            group by tweets.id 
            order by posted desc";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%{$search}%"]);
    $tweetData = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <title>つぶやき一覧 || twitter-demo</title>
  <link href="css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/mypage.css">
</head>

<body class="others">
  <div class="wrapper">
    <header>
      <h1><a href="mypage.php"><i class="fab fa-twitter"></i></a> Twitter</h1>
      <i id="nav-show" class="fas fa-bars"></i>
      <div id="nav" class="nav">
        <nav>
          <span id="nav-close" class="nav-close_button">×</span>
          <ul>
            <li><a href="logout.php">ログアウト</a></li>
            <li><a href="signout.php">退会</a></li>
            <li><a href="contact.php">お問い合わせ</a></li>
          </ul>
        </nav>
      </div>
    </header>
    <main>
      <div class="login-user_info">
        <h2>つぶやき一覧</h2>
      </div>
      <form action="" method="get" value="search">
        <p><input id="search-form" type="text" name="search" placeholder="つぶやきを検索"><input type="submit" value="検索"></p>
      </form>
      <?php if (empty($tweetData)) : ?>
        <p>投稿されたつぶやきはありません</p>
      <?php else : ?>
        <?php foreach ($tweetData as $tweet) : ?>
          <div class="posted_comment">
            <div class="flex_container">
              <a href="followuser.php?user_name=<?php echo $tweet["user_name"]; ?>">
                <div class="inner_flex">
                  <i class="far fa-user-circle"></i>
                  <p><?php echo $tweet["user_name"]; ?></p>
                </div>
              </a>
              <p class="text"><?php echo nl2br($tweet["tweet"]); ?></p>
            </div>
            <p class="like_count"><i class="fas fa-thumbs-up" data-id="<?php echo $tweet["id"]; ?>"> <?php echo $tweet["count(likes.like_count)"]; ?></i>
              <span class="date">
                <!-- 日時データを変換して出力 -->
                <?php $date = new DateTime($tweet["posted"]);
                echo $date->format('Y年m月d日'); ?>
                <a class="read-more" href="postedtweet.php?id=<?php echo $tweet["id"]; ?>"><span>詳細</span></a>
              </span>
            </p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </main>
  </div>
  <footer>
    <p>
      <span><a href="mypage.php"><i class="fas fa-user"></i></a></span>
      <span><a href="others.php"><i class="fas fa-users"></i></a></span>
      <span><a href="followlist.php"><i class="fas fa-handshake"></a></i></span>
    </p>
  </footer>
  <script src="js/others.js" async defer></script>
  <script src="js/likecount.js" async defer></script>
</body>

</html>