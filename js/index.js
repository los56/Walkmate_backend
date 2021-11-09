window.onload = () => {
    sessionCheck(initMain)
};

function initMain(response) {
    if(response.isLogin) {
        const statusDiv = document.querySelector("#statusDiv");
        const loginRegDiv = document.querySelector("#loginRegDiv");
        const userDiv = document.querySelector("#userDiv");

        statusDiv.innerHTML = "ID : " + response.id + " | " + "닉네임 : " + response.nickname;
        loginRegDiv.style.display = "none";
        userDiv.style.display = "block";

        getWalkList();
    }
}

function getWalkList() {
    const con = new XMLHttpRequest();
    const reqBody = {
        requireCount: 10
    };
    con.onreadystatechange = () => {
        if(con.status === 200 && con.readyState === XMLHttpRequest.DONE) {
            const res = JSON.parse(con.responseText);
            const listDiv = document.getElementById("walkList");

            if(res.isSuccess) {
                if(res.walksCount > 0) {
                    for(i = 0;i < res.walksCount;i++){
                        a = res.walks[i];
                        listDiv.innerHTML += makeListElement(a.title, a.location, a.description, a.nowMemberCount,
                                                a.maxMemberCount, a.requireList.require, a.hostNickName);
                    }
                } else {
                    listDiv.innerHTML = "산책이 없습니다.";
                }
            } else {
                listDiv.innerHTML = "DB 오류";
            }
        }
    }

    con.open("POST", "api/getWalkList.php");
    con.setRequestHeader("Content-Type", "application/json");
    con.send(JSON.stringify(reqBody));
}

function makeListElement(title, location, desc, nowMember, maxMember, require, hostNick) {
    return `<div class="listElement">
        <strong>${title}</strong> ${nowMember} / ${maxMember}<br>
        ${hostNick} | ${location} | ${require} <br>
        ${desc}
        <div> <br>`;
}