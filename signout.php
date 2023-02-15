<?php
require_once "functions/db.inc.php";
session_start();
$userName = $_SESSION["username"];
//ログインしてなければログインページへリダイレクト
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
  //退会ボタンが押下された場合にDB上にあるデータを全て削除してログインページへ遷移
  if (isset($_GET["yes"])) {
    //まずDB上のデータ削除
    $pdo = db_init();
    $sql = "delete from users where users.user_name=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userName]);

    $sql = "delete from tweets where tweets.user_name=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userName]);

    $sql = "delete from likes where likes.like_name=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userName]);

    $sql = "delete from comments where comments.comment_user=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userName]);

    $sql = "delete from follow where follow.follow_name=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userName]);

    //続いてセッションの破棄=>ログインページへ
    $_SESSION = [];
    $params = session_get_cookie_params();
    setcookie(
      session_name(),
      "",
      time() - 36000,
      $params["path"],
      $params["domain"],
      $params["secure"],
      $params["httponly"]
    );
    session_destroy();
    header("location:login.php");
  }
  //戻るが押下された場合の処理
  if (isset($_GET["no"])) {
    header("location:mypage.php");
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <title>退会 || twitter-demo</title>
  <link href="css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/mypage.css">
</head>

<body class="logout signout">
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
    <h2>退会</h2>
    <p>■ 退会にあたり下記内容をご確認ください ■</p>
    <ul>
      <li>1. 退会された場合、当サービスで保管しているお客様の全データを削除し復元することは出来なくなります。</li>
      <li>2. 退会に伴い削除されたデータにおいてお客様に対して何らかの不利益が発生した場合に当方は一切責任を負いません。</li>
    </ul>
    <form action="" method="get">
      <p class="check"><input id="checkbox" type="checkbox"><label for="checkbox">上記注意事項に同意する</label></p>
      <p id="ischecked" class="no_checked"><input id="signout" type="submit" name="yes" value="退会"></p>
      <p><input type="submit" name="no" value="戻る"></p>
    </form>
  </main>
  <footer>
    <p>
      <span><a href="mypage.php"><i class="fas fa-user"></i></a></span>
      <span><a href="others.php"><i class="fas fa-users"></i></a></span>
      <span><a href="followlist.php"><i class="fas fa-handshake"></i></a></span>
    </p>
  </footer>
  <!-- 共通のバーガーメニューのJS -->
  <script src="js/logout.js" async defer></script>
  <!-- 注意事項に同意のチェック後でないとsubmit不可にする為のJS -->
  <script src="js/signout.js" async defer></script>
</body>

</html>