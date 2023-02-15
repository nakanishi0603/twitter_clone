'use strict';
//likeCountを押した時に非同期でPHPへreqを投げ、返ってきたres(数字)をイベントを発生させた要素に入れる。 
const likeCountButton = document.querySelectorAll('.fa-thumbs-up');
const el = document.querySelectorAll(".posted_comment");


likeCountButton.forEach(button => {
    button.addEventListener('click', (e) => {
        //DB上に登録する際に利用する投稿内容のIDを取得
        let tweetId = e.target.dataset.id;
        let postData = { "id": tweetId };
        //PHPを介しDBへの登録 => レスポンスが返ってきたらイイね数を要素に入れる＆カラーの変更
        fetch(`http://zd2g15b.sim.zdrv.com/twitter_demo/likecount.php`, {
            method: "POST",
            body: JSON.stringify(postData),
            cache: "no-cache",
            headers: { "Content-Type": "application/json; charset=utf-8", },
        })
            .then((res) => {
                return res.json();
            })
            .then((data) => {
                e.target.innerText = ` ${data}`;
            })
    })

});