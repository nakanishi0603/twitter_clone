<?php
require_once "vendor/autoload.php";
session_start();
//ログインしてなければログインページへリダイレクト
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
//送信完了画面からブラウザバックしてきた際に送信完了か判定して送信済みの場合にマイページに飛ばす
//その際に再度問い合わせページにアクセス出来るようにセッションを取り外す
if(isset($_SESSION["sendComp"])){
  if($_SESSION["sendComp"] == true){
  header("location:mypage.php");
  unset($_SESSION["sendComp"]);
  exit;
  }
}


//日本語設定
Swift::init(function () {
  Swift_DependencyContainer::getInstance()
      ->register('mime.qpheaderencoder')
      ->asAliasOf('mime.base64headerencoder');

  Swift_Preferences::getInstance()->setCharset('iso-2022-jp');
});

if($_SERVER["REQUEST_METHOD"] === "POST") {
  //入力内容の取得
  $name = $_POST["name"];
  $email = $_POST["email"];
  $content = $_POST["content"];

  //入力項目のバリデーション
  $isValidated = true;
  if($name === ""){
    $nameError = "※氏名が入力されていません";
    $isValidated = false;
  }

  if($email === "") {
    $emailError = "※メールアドレスが入力されていません";
    $isValidated = false;
  }elseif(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$email)){
   
  }else{
    $emailError = "※メールアドレスの形式が正しくありません";
    $isValidated = false;
  }

  if($content === "") {
    $contentError = "※問い合わせ内容が入力されていません";
    $isValidated = false;
  }

  if($isValidated == true){
//メール送信時の本文をセット
$body = <<<EOT
下記の内容でお問い合わせを受けつけました。
  
■氏名: {$name} 様
■メールアドレス: {$email}
■お問い合わせ内容

{$content}

確認出来次第当方からご連絡をさせて致します。
今しばらくお待ち頂きますようお願い致します。

万が一返信がない場合には大変申し訳御座いませんが再度お問い合わせをお願いいたします。
EOT;
    //SMTPの設定
    $transport = new Swift_SmtpTransport('w1.sim.zdrv.com',25);
    //メール送信に関わる設定
    $message = new Swift_Message();
    $message->setFrom(['zd2G15@sim.zdrv.com'=>'Twitter-demo']);
    $message->setTo(["{$email}"=>"{$name}"]);
    $message->setBcc(["sn3187@gmail.com"=>"twitter-demo",'zd2G15@sim.zdrv.com'=>'Twitter-demo']);
    $message->setSubject('お問い合わせありがとうございます。');
    $message->setBody($body);

    //メールの送信
    $mailer = new Swift_Mailer($transport);
    $result = $mailer->send($message);
    if($result) {
      $sendText = "送信しました";
      $_SESSION["sendComp"] = true;
    }else{
      $sendError = "送信に失敗しました";
    }
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
  <title>お問い合わせ || twitter-demo</title>
  <link href="css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/mypage.css">
</head>

<body class="logout contact">
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
  <h2>お問い合わせ</h2>
  <?php if(isset($sendText)): ?>
    <p class="send-text"><?php echo $sendText; ?></p>
    <p class="send-text"><a href="mypage.php">戻る</a></p>
  <?php elseif(isset($sendError)): ?>
    <p class="send-error"><?php echo $sendError; ?></p>
    <p class="send-error"><a href="contact.php">再入力する</a></p>
  <?php else: ?>
    <form action="" method="POST">
      <!-- 氏名入力のエラー -->
      <?php if(isset($nameError)): ?>
        <span class="error"><?php echo $nameError ?></span>
      <?php endif; ?>
      <p>氏名<input name="name" type="text" value="<?php echo $name?>" placeholder="例:山田 太郎"></p>
      <!-- アドレス入力のエラー -->
      <?php if(isset($emailError)): ?>
        <span class="error"><?php echo $emailError ?></span>
      <?php endif; ?>
      <p>メールアドレス<input name="email" type="email" value="<?php echo $email?>" placeholder="foobar@hoge.com"></p>
      <p>問い合わせ内容</p>
      <!-- 問い合わせ内容の入力のエラー -->
      <?php if(isset($contentError)): ?>
        <span class="error"><?php echo $contentError ?></span>
      <?php endif; ?>
      <textarea name="content" id="" cols="30" rows="10"><?php echo $name?></textarea>
      <p><input type="submit" value="送信"></p>
    </form>
  <?php endif; ?>
  <footer>
    <p>
      <span><a href="mypage.php"><i class="fas fa-user"></i></a></span>
      <span><a href="others.php"><i class="fas fa-users"></i></a></span>
      <span><a href="followlist.php"><i class="fas fa-handshake"></i></a></span>
    </p>
  </footer>
  <!-- 共通のバーガーメニューのJS読み込み -->
  <script src="js/logout.js" async defer></script>
</body>
</html>