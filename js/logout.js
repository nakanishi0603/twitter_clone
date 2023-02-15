'use strict'

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
