<?php
require_once "functions/db.inc.php";
session_start();
//sessionを利用してログインユーザー名取得(後述のDBとの照合 & 登録時に利用)
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


if (isset($_GET["user_name"])) {
  $userName = $_GET["user_name"];
  $follower_name = $_SESSION["username"];
  //アクセス時にセッションに保存されているユーザー名と選択されたユーザー名を判別して同一ユーザーだった場合はマイページに飛ばす
  if ($userName === $_SESSION["username"]) {
    header("location:mypage.php");
    exit;
  } else {
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
      //既にフォローしていないかの確認を実施
      $sql = "select * from follow where follow_name=? and follower_name=?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$userName, $follower_name]);
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($data != true) {
        $buttonText = "フォロー";
      } else {
        //データベースへの登録は[id,フォローされるユーザ名,登録日,フォローしたユーザー名
        $buttonText = "フォロー解除";
      }
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
  }
} else {
  header("location:mypage.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $follower_name = $_SESSION["username"];
  try {
    $pdo = db_init();
    //既にフォローしていないかの確認を実施
    $sql = "select * from follow where follow_name=? and follower_name=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userName, $follower_name]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data == true) {
      $sql = "delete from follow where follow_name=? and follower_name=?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$userName, $follower_name]);
      $_SESSION["followcheck"] = true;
    } else {
      //データベースへの登録は[id,フォローされるユーザ名,登録日,フォローしたユーザー名]
      $sql = "insert into follow values(null,?,now(),?)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$userName, $follower_name]);
      $_SESSION["followcheck"] = true;
    }
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  if (isset($_SESSION["followcheck"])) {
    header("location:{$_SERVER['PHP_SELF']}?user_name=$userName");
    unset($_SESSION["followcheck"]);
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="ja">

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <title>フォローユーザーページ || twitter-demo</title>
  <link href="css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/mypage.css">
</head>

<body class="followuser">
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
      <form action="" method="post">
        <p><input class="follow_button" type="submit" value="<?php echo $buttonText; ?>"></p>
      </form>
    </div>
    <?php if (empty($tweetData)) : ?>
      <p>投稿されたつぶやきはありません</p>
    <?php else : ?>
      <?php foreach ($tweetData as $tweet) : ?>
        <div class="posted_comment">
          <div class="flex_container">
            <div class="inner_flex">
              <i class="far fa-user-circle"></i>
              <p><?php echo $tweet["user_name"]; ?></p>
            </div>
            <p class="text"><?php echo nl2br($tweet["tweet"]); ?></p>
          </div>
          <p class="like_count"><i class="fas fa-thumbs-up" data-id="<?php echo $tweet["id"]; ?>" onclick=""> <?php echo $tweet["count(likes.like_count)"]; ?></i>
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
  <footer>
    <p>
      <span><a href="mypage.php"><i class="fas fa-user"></i></a></span>
      <span><a href="others.php"><i class="fas fa-users"></i></a></span>
      <span><a href="followlist.php"><i class="fas fa-handshake"></a></i></span>
    </p>
  </footer>
  <!-- バーガーメニュー表示の為の使いまわし -->
  <script src="js/others.js" async defer></script>
  <script src="js/likecount.js" async defer></script>

</body>

</html>