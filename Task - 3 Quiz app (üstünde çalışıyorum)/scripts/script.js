const startButton = document.getElementById('startButton');
const controlButton = document.getElementById('controlButton');
const listButton = document.getElementById('listButton');
const addButton = document.getElementById('addButton');
const resButton = document.getElementById('restartButton');


startButton.addEventListener('click', startFun);
controlButton.addEventListener('click', controlFun);
listButton.addEventListener('click', listFun);
addButton.addEventListener('click', addFun);
resButton.addEventListener('click', resFun);


function startFun(){
    window.location.href= "quiz.php?n=1";
}

function controlFun(){
    window.location.href= "yönetim_paneli.php";
}
function addFun(){
    window.location.href= "soru_panel.php";
}
function listFun(){
    window.location.href= "edit.php";

}
function resFun(){
    window.location.href="easteregg.html";
}