"use strict";
const searchForm = document.getElementById("search-form");

// // 文字入力中はバックスペースでの戻るが聞かないようにする;
addEventListener("click", () => {
  if (document.activeElement == searchForm) {
  } else {
    window.document.addEventListener("keyup", (e) => {
      if (e.keyCode == 8) {
        location.href = "others.php";
      }
    })
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
