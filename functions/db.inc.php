<?php
function db_init()
{
  $pdo = new PDO("mysql:host=ftp2.sim.zdrv.com;dbname=DBzd2G15", "zd2G15", "AS5XKG");
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
