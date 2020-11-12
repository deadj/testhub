async function addTest(e) {
    var testName = document.querySelector('#testName').value;
    var tags = document.querySelector('#tags').value;
    var testForeword = document.querySelector('#testForeword').value;
    var passingScore = document.querySelector('#passingScore').value;
    var timeLimit = document.querySelector('#timeLimit').value;

    if (document.querySelector('#showWrongAnswers').checked) {
        var showWrongAnswers = true;
    } else {
        var showWrongAnswers = false;
    }

    if (document.querySelector('#publicResults').checked) {
        var publicResults = true;
    } else {
        var publicResults = false;
    }

    var formData = new FormData();
    formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));
    formData.append('testName', testName);
    formData.append('tags', tags);
    formData.append('testForeword', testForeword);
    formData.append('passingScore', passingScore);
    formData.append('timeLimit', timeLimit);
    formData.append('showWrongAnswers', showWrongAnswers);
    formData.append('publicResults', publicResults);    

    var response = await fetch('/addTest', {
        method: 'POST',
        body: formData
    });

    if (response.ok) {
        var questionPopup = document.getElementById('questionPopup').innerHTML;
        var oneAnswer = document.createElement('div');
        oneAnswer.id = "answers";
        oneAnswer.innerHTML = document.getElementById('oneAnswerPopup').innerHTML;

        var question = document.createElement('div');
        question.id = 'questionBlock';
        question.innerHTML = questionPopup;
        question.append(oneAnswer);
        
        var testInfo = document.getElementById('testInfo');
        var questionBlock = testInfo.parentNode;

        var continueButton = testInfo.querySelector("input[type='submit']").cloneNode(true);
        continueButton.setAttribute('onclick', 'addQuestion(this)');

        testInfo.remove();
        questionBlock.append(question);
        questionBlock.append(continueButton);

        var testId = document.createElement('input');
        testId.id = 'testId';
        testId.type = "hidden";
        testId.value = await response.text();
        document.querySelector('body').append(testId);

        var questionsCount = document.createElement('p');
        questionsCount.id = "questionsCount";
        questionsCount.innerHTML = "Всего вопросов: 0";

        var allBalls = document.createElement('p');
        allBalls.id = "allBalls";
        allBalls.innerHTML = "Всего баллов: 0";

        var timeForQuestion = document.createElement('p');
        timeForQuestion.id = "timeForQuestion";
        timeForQuestion.innerHTML = "&infin; минут на вопрос";     
        
        var endButton = document.createElement('button');
        endButton.classList.add('btn');
        endButton.classList.add('btn-outline-primary');
        endButton.classList.add('btn-block');
        endButton.setAttribute('onclick', 'openPublish()');
        endButton.innerHTML = 'Закончить создание';

        testData.append(questionsCount);
        testData.append(allBalls);
        testData.append(timeForQuestion);
        testData.append(endButton);
    } else {
        console.log(response.status);
    }
}

function changeAnswerType(el){
    var popupName = el.id + "Popup";
    var answerPopup = document.getElementById(popupName).innerHTML;
    document.getElementById('answers').innerHTML = answerPopup;

    document.querySelector('#answersTypes .disabled').classList.remove('text-success');
    document.querySelector('#answersTypes .disabled').classList.remove('disabled');
    el.classList.add('text-success');
    el.classList.add('disabled');
}

function addNewAnswer(el){
    var emptyAnswer = document.querySelector('.answer').cloneNode(true);
    emptyAnswer.querySelector("input[type='text']").value = "";
    el.parentNode.parentNode.parentNode.before(emptyAnswer);

    var answerType = emptyAnswer.querySelector('input').getAttribute('type');
    var requestForSelector = ".answer input[type='" + answerType + "']";
    var answers = el.parentNode.parentNode.parentNode.parentNode.querySelectorAll(requestForSelector);
    var answersCount = answers.length;
    answers[answersCount - 1].setAttribute('answerid', answersCount - 1);
}

function deleteAnswer(el){
    var answersBlock = document.getElementById('answers');
    var answersCount = answersBlock.querySelectorAll('.answer').length;

    if (answersCount >= 3) {
        var answer = el.parentNode.parentNode.parentNode;
        var answerType = answer.querySelector('input').getAttribute('type'); 

        answer.remove();
        changeAnswersId(answerType);
    } else {
        printErrorMessage('Должно быть не меньше двух вариантов ответа');
    }
}

function printErrorMessage(newMessage){
    if (document.getElementById('message')) {
        message.querySelector('div').innerHTML = newMessage
    } else {
        var errorPopup = document.getElementById('errorPopup').innerHTML;
        errorPopup = errorPopup.replace('[[message]]', newMessage);

        var errorMessage = document.createElement('div');
        errorMessage.id = 'message';
        errorMessage.classList.add('position-absolute');
        errorMessage.classList.add('col-md-4');
        errorMessage.classList.add('text-center');
        errorMessage.innerHTML = errorPopup;
        errorMessage.style.bottom = "10px";
        errorMessage.style.right = "10px";
        errorMessage.style.zIndex = "1500";

        body.prepend(errorMessage);
    }

    setTimeout(closeMessage, 3000, errorMessage);
}

function changeAnswersId(answerType){
    var requestForSelector = ".answer input[type='" + answerType + "']";   
    var answers = document.querySelectorAll(requestForSelector);
    var answersCount = answers.length;

    for (var i = 0; i < answersCount; i++) {
        answers[i].setAttribute('answerid', i);    
    }
}

function closeMessage(){
    message.remove();
}

async function addQuestion(el) {
    var question = document.getElementById('question').value;
    var ballsCount = document.getElementById('ballsCount').value;
    var answerType = document.getElementById('answersTypes').querySelector('button.disabled').id;

    if (answerType == "oneAnswer" || answerType == "multipleAnswers") {
        var answers = document.getElementById("answers").querySelectorAll("input[type='text']");
        var answersValue = [];

        for (var i = 0; i < answers.length - 1; i++) {
            answersValue[i] = answers[i].value;
        }  

        var jsonAnswers = JSON.stringify(answersValue);

        if (answerType == "oneAnswer") {
            var trueAnswersId = document.querySelector(".answer input[type='radio']:checked").getAttribute('answerid');
        } else {
            var trueAnswers = document.querySelectorAll(".answer input[type='checkbox']:checked");
            var trueAnswersId = [];

            for (var i = 0; i < trueAnswers.length; i++) {
                trueAnswersId[i] = trueAnswers[i].getAttribute('answerid');
            }
        }

        var jsonTrueAnswers = JSON.stringify(trueAnswersId);
    } else {
        var answer = document.querySelector('#answers input').value;
        var jsonTrueAnswers = JSON.stringify(answer);
        var jsonAnswers = null;
    }

    var formData = new FormData();
    formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));
    formData.append('question', question);
    formData.append('balls', ballsCount);
    formData.append('type', answerType);
    formData.append('trueAnswer', jsonTrueAnswers);
    formData.append('answer', jsonAnswers);
    formData.append('testId', testId.value);

    var response = await fetch('/addQuestion', {
        method: 'POST',
        body: formData
    });

    if (response.ok) {
        document.querySelector('#question').value = "";
        document.querySelector('#ballsCount').value = "";
        changeAnswerType(oneAnswer);

        responseData = await response.json();

        questionsCount.innerHTML = "Всего вопросов: " + responseData['questionCount'];
        allBalls.innerHTML = "Всего баллов: " + responseData['balls'];
        timeForQuestion.innerHTML = responseData['time'] + " мин. на один вопрос";
    } else {
        console.log(response.status);
    }    
}

async function openPublish(){
    if (questionsCount.innerHTML == 'Всего вопросов: 0') {
        printErrorMessage('Добавьте ещё вопрос');
    } else {
        newTest.remove();
        var registerP = rightBlock.querySelectorAll('p');
        registerP[1].remove();
        registerP[2].remove();
        rightBlock.querySelector('hr').remove();
        testData.remove();
        
        var publishTemplate = document.querySelector('#publishTemplate').innerHTML;
        //replace publishLink
        //replace resultsLink
        //replace myTestsLin

        var publishBlock = document.createElement('div');
        publishBlock.id = "publishBlock";
        publishBlock.innerHTML = publishTemplate;
        publishBlock.classList.add('col-9');
        publishBlock.classList.add('pr-5');

        document.querySelector('.container .row').prepend(publishBlock);


    }
}