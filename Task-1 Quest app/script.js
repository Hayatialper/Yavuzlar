// Buradaki hiç bir şey ChatGPT tarafından yapılmadı sadece öğrenmek için üşenmeden yazıyorum

// SORU LİSTESİ
// SORU LİSTESİ
// SORU LİSTESİ
// SORU LİSTESİ

const question = [
    {
        question: 'What is 2+2?',
        answers: [
            {text: '4', correct: true},
            {text: '22', correct:false}
        ]
    },
    {
        question: 'Deneme sorusu?',
        answers: [
            {text: 'Doğru cevap', correct: true},
            {text: 'Yanlış cevap 1', correct:false},
            {text: 'Yanlış cevap 2', correct:false},
            {text: 'Yanlış cevap 3', correct:false}
        ]
    },
    {
        question: 'Deneme sorusu 2?',
        answers: [
            {text: 'Doğru cevap 2', correct: true},
            {text: 'Yanlış cevap 1.2', correct:false},
            {text: 'Yanlış cevap 2.2', correct:false},
            {text: 'Yanlış cevap 3.2', correct:false}
        ]
    }
]

// SORU LİSTESİ
// SORU LİSTESİ
// SORU LİSTESİ
// SORU LİSTESİ







const QuestionCardTemplate = document.querySelector("[data-question-template]")



let shuffledQuestions, currentQuestionIndex
const questionElement = document.getElementById('question')
const answerButtonsElement = document.getElementById('answer-buttons')
const startButton = document.getElementById("start-btn")
const nextButton = document.getElementById("next-btn")
const controlButton = document.getElementById("control-btn")
const listButton = document.getElementById("list-btn")
const panelTitle = document.getElementById("paneltitle")
const aramaInput = document.getElementById("arama")
const aramakart = document.getElementById("arama-kartları")
const questionContainer = document.getElementById("question-container")
const homeButton = document.getElementById("home-btn")






nextButton.addEventListener('click', () =>{
    currentQuestionIndex++
    setNextQuestion()
})
startButton.addEventListener('click', startGame)
// Almak istediğimiz değeri "click" olarak belirleyip "startGame" fonksiyonuna yönlendiricez.

controlButton.addEventListener('click',controlPanel)

// Soruları listelemek için bir listener daha
listButton.addEventListener('click',listquestion)

aramaInput.addEventListener('input', aramaFonk)

homeButton.addEventListener('input', anasayfa)



// Burada aramaFonksiyonunu ekleme yapıcaz.
// function aramaFonk() {
    
// }









function startGame() {
    // Start tuşuna hide class'ı ekleyip gizleyecek
    startButton.classList.add('hide')
    // Soru konteynırına atadığımız gizle class'ını silicez.
    controlButton.classList.add('hide')


    shuffledQuestions = question.sort(() => Math.random() - .5)
    currentQuestionIndex = 0
    // Konteynırdan hide kaldırır.
    questionContainer.classList.remove('hide')
    // Sonraki soruyu çağırmak için "setNextQuestion" fonksiyonu çağrısı yapıcaz
    setNextQuestion()
}


// Soruları göster (Düzenlemek adına)
function listquestion(){
    listButton.classList.add('hide')
    aramaInput.classList.remove('hide')
    aramakart.classList.remove('hide')
    fetch(question).then(data =>{
        const card = QuestionCardTemplate.content.cloneNode(true).children[0]
        console.log(card)
    })

}



// Kontrol paneli
function controlPanel() {
    startButton.classList.add('hide')
    controlButton.classList.add('hide')
    panelTitle.classList.remove('hide')
    listButton.classList.remove('hide')
    

}

function setNextQuestion() {

    resetState()

    showQuestion(shuffledQuestions[currentQuestionIndex])
}


function resetState(){
    clearStatusClass(document.body)
    nextButton.classList.add('hide')
    while(answerButtonsElement.firstChild){
        answerButtonsElement.removeChild(answerButtonsElement.firstChild)
    }
}


function showQuestion(question) {
    questionElement.innerText = question.question
    question.answers.forEach(answer => {
        const button = document.createElement('button')
        button.innerText = answer.text
        button.classList.add('btn')
        
        if(answer.correct){
            button.dataset.correct = answer.correct
        }
        
        button.addEventListener('click', selectAnswer)
        answerButtonsElement.appendChild(button)

    });
}



function selectAnswer(e) {
    const selectedButton = e.target
    const correct = selectedButton.dataset.correct
    setStatusClass(document.body, correct)
    Array.from(answerButtonsElement.children).forEach(button => {
        setStatusClass(button, button.dataset.correct)
    })

    if(shuffledQuestions.length > currentQuestionIndex +1){
        nextButton.classList.remove('hide')
    } else{
        startButton.innerText = 'Restart'
        startButton.classList.remove('hide')
        homeButton.classList.remove('hide')
    }
}

function setStatusClass(element, correct) {
    clearStatusClass(element)
    if(correct){
        element.classList.add('correct')
        
    } else
    element.classList.add('wrong')
}

function clearStatusClass(element){
    element.classList.remove('correct')
    element.classList.remove('wrong')
}


function anasayfa(){
    window.location.href = window.location.href;
}






// Buradaki hiç bir şey ChatGPT tarafından yapılmadı sadece öğrenmek için üşenmeden yazıyorum