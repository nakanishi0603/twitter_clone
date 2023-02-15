<?php
require_once "functions/db.inc.php";
session_start();
//sessionを利用してログインユーザー名取得(後述のDBとの照合 & 登録時に利用)
$userName = $_SESSION["username"];
if(isset($_SESSION["login"])){
  if ($_SESSION["login"] != true) {
    unset($_SESSION["username"]);
    header("location:login.php");
    exit;
  }
}else{
  header("location:login.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "GET") {
  try {
    //初回レンダーで投稿データの取得
    $pdo = db_init();
    $sql = "select tweets.*,count(likes.like_count) 
            from tweets LEFT OUTER join likes 
            on tweets.id=likes.tweet_id 
            where tweets.user_name=?
            group by tweets.id
            order by posted desc";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userName]);
    $tweetData = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  //投稿内容の取得
  $tweet = $_POST["tweet"];
  if ($tweet === "" || mb_strlen($tweet) > 100) {
    //投稿が空文字列の場合(エラー表示はJSに任せているためPHPでは何もしない)
  } else {
    //投稿内容のバリデーションがOKの場合にデータベースに登録
    try {
      $pdo = db_init();
      $sql = "insert into tweets values(null,?,now(),?)";
      $stmt = $pdo->prepare($sql);
      //投稿された内容とログイン中のユーザー名をデータベースに登録
      $stmt->execute([$tweet, $userName]);
      header("location:mypage.php");
      exit;
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
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
  <title>マイページ || twitter-demo</title>
  <link href="css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/mypage.css">
</head>

<body class="mypage">
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
        <i class="far fa-user-circle user_icon"></i>
        <p><?php echo $userName; ?></p>
      </div>
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
            <p class="like_count"><i class="fas fa-thumbs-up" data-id="<?php echo $tweet["id"];?>" onclick=""> <?php echo $tweet["count(likes.like_count)"]; ?></i>
              <span class="date">
                <!-- 日時データを変換して出力 -->
                <?php $date = new DateTime($tweet["posted"]);
                echo $date->format('Y年m月d日'); ?>
                <a class="read-more" href="postedtweet.php?id=<?php echo $tweet["id"];?>"><span>詳細</span></a>
              </span>
            </p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </main>
    <!-- 投稿用画面(投稿ボタンを押すと横からスライド表示) -->
    <i id="post_button" class="fas fa-comment-medical"></i>
    <div id="form" class="post-container">
      <div class="postform-wrap">
        <form class="tweet-form" action="mypage.php" method="post" name="tweet">
          <p class="cancel-position"><span class="post-cancel">×</span></p>
          <p id="error"></p>
          <p>
            <textarea id="tweet" class="tweet" name="tweet" cols="30" rows="10" placeholder="つぶやき(100字以内)"></textarea>
          </p>
          <p><input id="submit" type="submit" value="投稿" onclick="validate();"></p>
        </form>
      </div>
    </div>
  </div>
  <footer>
    <p>
      <span><a href="mypage.php"><i class="fas fa-user"></i></a></span>
      <span><a href="others.php"><i class="fas fa-users"></i></a></span>
      <span><a href="followlist.php"><i class="fas fa-handshake"></a></i></span>
    </p>
  </footer>
  <script src="js/mypage.js" async defer></script>
  <script src="js/likecount.js" async defer></script>
</body>

</html>