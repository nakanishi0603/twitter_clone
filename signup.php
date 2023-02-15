<?php
session_start();
require_once "functions/db.inc.php";

//ログイン済みの場合は
//=>二重登録を防ぐ為に強制的にメインページにリダイレクトさせる
if (isset($_SESSION["login"])) {
  if ($_SESSION["login"] == true) {
    header("location:mypage.php");
    exit;
  }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  //ID,PASS,ユーザー名の取得
  $userName = $_POST["username"];
  $userId   = $_POST["userid"];
  $pass     = $_POST["pass"];


  //入力の不備のチェック
  $isValidated = true;

  //ユーザーIDのバリデーション
  if ($userId === "") {
    $errorId = "※ユーザーIDが未入力です";
    $isValidated = false;
  } elseif (mb_strlen($userId) > 30) {
    $errorId = "※30字以内で入力してください";
    $isValidated = false;
  }

  //パスワードのバリデーション
  if ($pass === "") {
    $errorPass = "※パスワードが未入力です";
    $isValidated = false;
  } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $pass)) {
    $errorPass = "※使用できない文字が含まれています";
    $isValidated = false;
  }

  //ユーザー名のバリデーション
  if ($userName === "") {
    $errorName = "※ユーザー名が未入力です";
    $isValidated = false;
  } elseif (mb_strlen($userName) > 30) {
    $errorName = "※30字以内で入力してください";
    $isValidated = false;
  }

  //バリデーション完了後にパスワードをハッシュ化
  $pass = password_hash($pass, PASSWORD_BCRYPT);

  if ($isValidated == true) {
    //不備がなければデータベースと照合する
    try {
      $pdo = db_init();
      $sql = "select * from users where user_id = ?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$userId]);
      $data = $stmt->fetch(PDO::FETCH_ASSOC);  //存在しなければDBからfalseが返る

      //入力されたIDが存在してないかチェック
      if ($data == true) {
        $singupIdError = "※入力されたユーザーIDは既に使用されています";
      } else {
        //更にIDは存在しないが入力されたユーザ名が存在してないかチェック
        $pdo = db_init();
        $sql = "select * from users where user_name = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userName]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userName !== $data["user_name"]) {
          //いずれも存在しなければデータベースに登録しメインページへ飛ばす
          //その際にユーザー名をsessionに格納
          try {
            $pdo = db_init();
            $sql = "insert into users value (null,?,?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $pass, $userName]);
            //セキュリティを高める為にセッションIDの再発行 ⇒ ログイン時と同様にユーザー名をセッションに格納
            session_regenerate_id();
            $_SESSION["username"] = $userName;
            //会員専用ページへ移動(リダイレクト)
            $_SESSION["login"] = true;
            header("location:mypage.php");
            exit;
          } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
          }
        } else {
          //入力したユーザー名がデータベースにあった場合
          $singupNameError = "※入力されたユーザー名は既に使用されています";
        }
      }
    } catch (PDOException $e) {
      echo $e->getMessage();
      exit;
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
  <title>新規登録 | twitter_demo</title>
  <link href="css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/signup.css">
</head>

<body>
  <header>
    <h1><i class="fab fa-twitter"></i> Twitter</h1>
  </header>
  <main>
    <h2>新規登録</h2>
    <form action="" method="post">
      <!-- このセクションのif文はエラー表示の為の記述 -->
      <?php if (isset($errorName)) : ?>
        <?php echo "<span class='error'>" . $errorName . "</span>"; ?>
      <?php endif; ?>
      <?php if (isset($singupNameError)) : ?>
        <?php echo "<span class='error'>" . $singupNameError . "</span>"; ?>
      <?php endif; ?>
      <?php if (isset($singupIdError)) : ?>
        <?php echo "<span class='error'>" . $singupIdError . "</span>"; ?>
      <?php endif; ?>
      <p><span>ユーザー名</span><input type="text" name="username" placeholder="例:山田 太郎"></p>

      <?php if (isset($errorId)) : ?>
        <?php echo "<span class='error'>" . $errorId . "</span>"; ?>
      <?php endif; ?>
      <p><span>ユーザーID</span><input type="text" name="userid" placeholder="ユーザーID(半角英数)"></p>

      <?php if (isset($errorPass)) : ?>
        <?php echo "<span class='error'>" . $errorPass . "</span>"; ?>
      <?php endif; ?>
      <p><span>パスワード</span><input type="password" name="pass" placeholder="パスワード"></p>
      <input type="submit" value="新規登録">
      <p><a class="login" href="login.php">ログイン</a></p>
    </form>
  </main>
</body>

</html>