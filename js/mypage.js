"use strict";
  // mypage投稿時に必要な要素の取得
  const postButton = document.getElementById("post_button");
  const form = document.getElementById("form");
  const tweetText = document.getElementById("tweet");
  const postCancelButton = document.querySelector(".post-cancel");
  const submit = document.getElementById("submit");
  
  //モーダル画面をサイドからスライドで表示、非表示
  postButton.addEventListener("click", () => {
    form.classList.add("show");
  })
  //キャンセルの×ボタンを押した際にエラー文字を消す
  postCancelButton.addEventListener("click", () => {
    form.classList.remove("show");
    let error = document.getElementById("error");
    error.innerText = "";
    document.tweet.tweet.value = "";
  })

  //テキストエリアが空文字もしくはスペースのみで送信された場合にイベントを中止してエラー表示
  const validate = () => {
    if (document.tweet.tweet.value === "" || document.tweet.tweet.value === " ") {
      event.preventDefault();
      let error = document.getElementById("error");
      error.classList.add("error");
      error.innerText = "※投稿内容が入力されていません";
    }
  }

  //入力文字数のバリデーション
  tweetText.addEventListener("keyup",() => {
    let textLength = tweetText.value.length;
    if(textLength > 100){
      event.preventDefault();
      let error = document.getElementById("error");
      error.classList.add("error");
      error.innerText = "※文字数は100文字までです";
    }else{
      error.innerText = "";
    }
  })


  //バーガーメニューの表示/非表示
  const navClose = document.getElementById("nav-close");
  const navshow = document.getElementById("nav");
  const navShow = document.getElementById("nav-show")  

  navShow.addEventListener("click", () => {
    nav.classList.add("nav-show");
  })
  navClose.addEventListener("click", () => {
    nav.classList.remove("nav-show");
  })
