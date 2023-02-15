<?php
require_once "functions/db.inc.php";
session_start();
//sessionを利用してログインユーザー名取得(後述のDBとの照合 & 登録時に利用)
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

if($_SERVER["REQUEST_METHOD"] === "GET"){
  $follower_name = $_SESSION["username"];
  //アクセス時にセッションに保存されているユーザー名と選択されたユーザー名を判別して同一ユーザーだった場合はマイページに飛ばす
  if($userName === $_SESSION["username"]){
    header("location:mypage.php");
    exit;
  }else{
    try {
      //初回レンダーで投稿データの取得
      $pdo = db_init();
      //既にフォローしていないかの確認を実施
      $sql = "select * from follow where follower_name=?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$follower_name]);
      $followData = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $buttonText = "フォロー解除"; 
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
  }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $followId = $_POST["followid"];
    try {
      $pdo = db_init();
      $sql = "delete from follow where id=?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$followId]);
      $_SESSION["followcheck"] = true;
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
    if(isset($_SESSION["followcheck"])){
      header("location:{$_SERVER['PHP_SELF']}");
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
  <title>フォローリスト || twitter-demo</title>
  <link href="css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/mypage.css">
</head>
<body class="followlist">
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
      <?php if(empty($followData)): ?>
        <p class="nofollow">フォローしているユーザーはいません</p>
      <?php endif; ?>
      <?php foreach($followData as $followUser): ?>
        <form action="" method="post">
          <div class="login-user_info">
            <a href="followuser.php?user_name=<?php echo $followUser["follow_name"]; ?>">
              <i class="far fa-user-circle user_icon"></i>
              <p><?php echo $followUser["follow_name"]; ?></p>
            </a>
            <p>
              <button name="followid" class="follow_button" type="submit" value="<?php echo $followUser["id"]; ?>"><?php echo $buttonText; ?></button>
            </p>
          </div>
        </form>
      <?php endforeach; ?>
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

</body>
</html>