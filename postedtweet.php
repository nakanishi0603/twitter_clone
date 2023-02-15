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

if(isset($_GET["id"])){
  $id = $_GET["id"];
    try {
      //初回レンダーで投稿データの取得
      $pdo = db_init();
      $sql = "select tweets.*,count(likes.like_count) 
              from tweets 
              LEFT OUTER join likes 
              on tweets.id=likes.tweet_id 
              where tweets.id=?
              group by tweets.id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$id]);
      $tweetData = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if(empty($tweetData)){
        header("location:mypage.php");
        exit;
      }
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
    try {
      //コメントの取得
      $pdo = db_init();
      $sql = "select * from tweets 
              join comments
              on tweets.id=comments.tweet_id 
              where tweets.id=?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$id]);
      $commentData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo $e->getMessage();
    }


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //投稿内容の取得
    $comment = $_POST["tweet"];
    if ($comment === "" || mb_strlen($comment) > 100) {
      //投稿が空文字列の場合(エラー表示はJSに任せているためPHPでは何もしない)
    } else {
      //投稿内容のバリデーションがOKの場合にデータベースに登録
      try {
        $pdo = db_init();
        $sql = "insert into comments values(null, ?, ?, now(), ?)";
        $stmt = $pdo->prepare($sql);
        //投稿された内容をデータベースに登録
        $stmt->execute([$id, $comment, $userName]);
        header("location:postedtweet.php?id={$id}");
        exit;
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
    }

      //削除処理
    if(isset($_POST["delete"])){
      try {
        $pdo = db_init();
        $sql = "delete from tweets where id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $sql = "delete from comments where tweet_id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        header("location:mypage.php");
        exit;
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
    }
  }
}else{
  header("location:mypage.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <title>つぶやき詳細 || twitter-demo</title>
  <link href="css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/mypage.css">
</head>

<body class="posted-tweet">
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
      <div class="posted-user_info">
        <i class="far fa-user-circle user_icon"></i>
        <h2><?php echo $tweetData[0]["user_name"]; ?>のつぶやき</h2>
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
            <p class="like_count"><i class="fas fa-thumbs-up"  data-id="<?php echo $tweet["id"];?>"> <?php echo $tweet["count(likes.like_count)"]; ?></i>
              <span class="date">
              <!-- 日時データを変換して出力 -->
              <?php $date = new DateTime($tweet["posted"]);
              echo $date->format('Y年m月d日'); ?>
              </span>
            </p>
            <!-- 自分で投稿した内容の詳細を開いた時のみ削除ボタンを表示 -->
            <?php if($userName == $tweetData[0]["user_name"]): ?>
              <form id="delete-form" action="" method="post">
                <p id="tweet-delete" class="tweet-delete">
                  <input type="submit" name="delete" value="削除">
                </p>
              </form>
            <?php else: ?>
            <?php endif; ?>
          </div>
          <?php if (empty($commentData)) : ?>
            <p class="others-comment">コメントはまだありません</p>
          <?php else: ?>
            <h2 class="comment-title">コメント</h2>
            <?php foreach ($commentData as $comment) : ?>
          <div class="comments">
            <div class="flex_container">
              <div class="inner_flex">
                <i class="far fa-user-circle"></i>
                <p><?php echo $comment["comment_user"]; ?></p>
              </div>
              <p class="text"><?php echo nl2br($comment["comment"]); ?></p>
            </div>
            <p class="date">
              <span class="date">
                <!-- 日時データを変換して出力 -->
                <?php $date = new DateTime($comment["posted"]);
                echo $date->format('Y年m月d日'); ?>
              </span>
            </p>
          </div>
        <?php endforeach; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </main>

    <!-- コメント投稿用(投稿ボタンを押すと横からスライド表示) -->
    <i id="comment_button" class="fas fa-comments"></i>
    <div id="form" class="post-container">
      <div class="postform-wrap">
        <form class="tweet-form" action="" method="post" name="tweet">
        <p class="cancel-position"><span class="post-cancel">×</span></p>
          <p id="error"></p>
          <p>
            <textarea id="tweet" class="tweet" name="tweet" cols="30" rows="10" placeholder="コメント投稿"></textarea>
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
  <script src="js/comment.js" async defer></script>
  <script src="js/likecount.js" async defer></script>
</body>

</html>