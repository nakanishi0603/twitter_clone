'use strict'

const checkbox = document.getElementById("checkbox");
const isChecked = document.getElementById("ischecked");
const signOutButton = document.getElementById("signout");

function eventStop(e) {
    e.preventDefault();
}
//初期状態でsubmitのイベントを停止させておく
signOutButton.addEventListener("click", eventStop, { passive: false });

//チェック後に別ページに移動して再度開くとチェックがされたままになるので、load時にチェックをfalseに設定
addEventListener('load', () => {
    checkbox.checked = false;
})

checkbox.addEventListener("click", (e) => {
    let check = checkbox.checked;
    if (check === true) {
        isChecked.classList.remove("no_checked");
        //チェックが入っている場合にのみイベントを復活させる
        signOutButton.removeEventListener("click", eventStop, { passive: false });
    } else {
        signOutButton.addEventListener("click", eventStop, { passive: false });
        isChecked.classList.add("no_checked");
    }
})