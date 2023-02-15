<?php
session_start();
require_once "functions/db.inc.php";

//ログイン済みの場合は
//=>二重ログインを防ぐ為に強制的にメインページにリダイレクトさせる
if (isset($_SESSION["login"])) {
  if ($_SESSION["login"] == true) {
    header("location:mypage.php");
    exit;
  }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  //ID,PASSの取得
  $userId = $_POST["userid"];
  $pass   = $_POST["pass"];

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


  // パスワードのバリデーション
  if ($pass === "") {
    $errorPass = "※パスワードが未入力です";
    $isValidated = false;
  } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $pass)) {
    $errorPass = "※使用できない文字が含まれています";
    $isValidated = false;
  }

  if ($isValidated == true) {
    //不備がなければ、正しいID,PASSか確認
    //データベースと照合する
    try {
      $pdo = db_init();
      $sql = "select * from users where user_id = ?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$userId]);
      $data = $stmt->fetch(PDO::FETCH_ASSOC);  //存在しなければDBからfalseが返る
      if ($data == false) {
        //該当IDが存在しない場合
        $loginCheck = false;
      } else {
        //該当IDが存在する場合
        //=>入力されたpassとDB上のハッシュ化されたpassが一致するか確認
        if (!password_verify($pass, $data["password"])) {
          //否定となっているためこの場合はパスワードが異なる場合
          $loginCheck = false;
        } else {
          //パスワードが正しい場合
          $loginCheck = true;
        }
      }
    } catch (PDOException $e) {
      echo $e->getMessage();
      exit;
    }

    if ($loginCheck == true) {
      //セキュリティを高める為にセッションIDの再発行 ⇒ ログインチェックに問題がなければSESSIONにログイン済を表す｢true｣を代入
      //後々利用するためにユーザー名もセッションに格納
      session_regenerate_id();
      $_SESSION["login"] = true;
      $_SESSION["username"] = $data["user_name"];
      //会員専用ページへ移動(リダイレクト)
      header("location:mypage.php");
      exit;
    } else {
      $error = "※ユーザーIDまたはパスワードが正しくありません";
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
  <title>ログイン | twitter_demo</title>
  <link href="css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/login.css">
</head>

<body>
  <header>
    <h1><i class="fab fa-twitter"></i> Twitter</h1>
  </header>
  <main>
    <h2>ログイン</h2>
    <form action="" method="post">
      <?php if (isset($errorId)) : ?>
        <?php echo "<span class='error'>" . $errorId . "</span>"; ?>
      <?php endif; ?>
      <?php if (isset($error)) : ?>
        <?php echo "<span class='error'>" . $error . "</span>"; ?>
      <?php endif; ?>
      <p><span>ユーザーID</span><input type="text" name="userid" placeholder="ユーザーID(半角英数)"></p>
      <?php if (isset($errorPass)) : ?>
        <?php echo "<span class='error'>" . $errorPass . "</span>"; ?>
      <?php endif; ?>
      <p><span>パスワード</span><input type="password" name="pass" placeholder="パスワード"></p>
      <p><input type="submit" value="ログイン"></p>
    </form>
    <p><a class="signup" href="signup.php">新規登録</a></p>
  </main>
</body>

</html>