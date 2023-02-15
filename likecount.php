<?php 
require_once "functions/db.inc.php";
session_start();
//sessionを利用してログインユーザー名取得(後述のDBとの照合 & 登録時に利用)
$userName = $_SESSION["username"];

//ページへのアクセスを防ぐため記述
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

if($_SERVER["REQUEST_METHOD"] === "POST"){
    //json形式でjsから送られたデータの取得(解析)
    $json = file_get_contents("php://input");
    $data = json_decode($json,true);
    $tweetId = (int)$data["id"]; 
    try {
        //クリックしたユーザーがまだその投稿に対していいねしていないかの確認
        //まだされていなければ登録へ、されていた場合はdelete文実行へ
        $pdo = db_init();
        $sql = "select * from likes where tweet_id=? and like_name=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tweetId,$userName]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        //もしデータベースに既に登録されていたら削除
        if($data == true){
            $sql = "delete from likes where tweet_id=? and like_name=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$tweetId,$userName]);
            //登録後レスポンスする為のデータをDBからCOUNTを使い抽出
            $sql = "select count(*) from likes where tweet_id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$tweetId]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = $data[0]["count(*)"];
        
        //そうでなければ登録
        }else{
        //データベースにユーザー名、tweetidの登録
            $sql = "insert into likes (id, like_count, tweet_id, like_name) values(null, 1, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$tweetId,$userName]);
            //登録後レスポンスする為のデータをDBからCOUNTを使い抽出
            $sql = "select count(*) from likes where tweet_id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$tweetId]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = $data[0]["count(*)"];
        }
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
}

echo json_encode($data);