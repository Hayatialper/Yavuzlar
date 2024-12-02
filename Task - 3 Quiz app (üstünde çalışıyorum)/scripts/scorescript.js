
const resButton = document.getElementById('restartButton');


resButton.addEventListener('click', resFun);

function resFun(){
    window.location.href="questions.php?n=1";
}