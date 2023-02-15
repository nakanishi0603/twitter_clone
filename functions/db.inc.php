<?php
function db_init()
{
  $pdo = new PDO("mysql:host=*********;dbname=********", "******", "******");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->exec("set names utf8");
  return $pdo;
}
// function db_init()
// {
//   $pdo = new PDO("mysql:host=localhost;dbname=tw", "tweetuser", "test");
//   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//   $pdo->exec("set names utf8");
//   return $pdo;
// }
