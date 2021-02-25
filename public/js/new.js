function openTimeLimitField() {
    if (timeLimitCheckbox.checked) {
        timeLimitField.classList.remove('d-none');
        timeLimitField.classList.add('d-inline-block');
    } else {
        timeLimitField.classList.remove('d-inline-block');        
        timeLimitField.classList.add('d-none');
    }
}

async function addTest(e) {
    if (document.getElementById('errorsBlock')) {
        document.querySelector('#errorsBlock').remove();
    } 

    var testName = document.querySelector('#testName').value;
    var testForeword = document.querySelector('#testForeword').value;
    var minBalls = Number(document.querySelector('#passingScore').value);
    var timeLimit = document.querySelector('#timeLimit').value;
    var tags = document.querySelector('#tags').value;

    var errors = checkTestData(testName, tags, minBalls);
    if (errors.length != 0) {
        printErrors(errors);
        return;
    }

    nextStepButton.disabled = true;

    var formData = new FormData();
    formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));
    formData.append('testName', testName);
    formData.append('testForeword', testForeword);
    formData.append('minBalls', minBalls);
    formData.append('timeLimit', timeLimit);
    
    if (document.querySelector('#showWrongAnswers').checked) {
        formData.append('showWrongAnswers', true);
    }

    if (document.querySelector('#publicResults').checked) {
        formData.append('publicResults', publicResults);
    }    

    if (tags != "") {
        formData.append('tags', tags);    
    }

    var response = await fetch('/addTest', {
        method: 'POST',
        body: formData
    });

    if (response.ok) {
        nextStepButton.disabled = false;
        openQuestionStep(await response.text());
    } else {
        if (response.status === 422) {
            var json = await response.json();
            printErrors(json);
        } else {
            console.log(response.status);
            printErrorMessage('Ой... Что-то пошло нет так:(');
        }

        nextStepButton.disabled = false;
    }
}

function checkTestData(name, tags, balls) {
    var errors = [];

    if (name == "") {
        errors.push("Введите название");
    }

    if (balls < 0 || balls == "") {
        errors.push("Введите минимальный балл")
    }

    var regexp = /[^\w\d\sа-яё,-]/iu;
    if (regexp.test(tags)) {
        errors.push("Теги могут включать буквы, цифвы, пробелы, запятые и дефис");
    }

    return errors;
}

function openQuestionStep(data) {
    registerInfo.remove();

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

    var nextStepButton = document.querySelector('#nextStepButton').cloneNode(true);
    nextStepButton.setAttribute('onclick', 'addQuestion(this)');

    testInfo.remove();
    questionBlock.append(question);
    questionBlock.append(nextStepButton);

    var testId = document.createElement('input');
    testId.id = 'testId';
    testId.type = "hidden";
    testId.value = data;
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

    $('#questionsList').sortable({
        update: function(event, ui) {
            changeOrderOfQuestionNumbers();
        }
    });
}

async function changeOrderOfQuestionNumbers() {
    var questionsList = document.querySelectorAll('.questionFromList');
    var numbersArray = [];
    
    for (var i = 0; i < questionsList.length; i++) {
        questionsList[i].querySelector('p').innerHTML = i + 1;
        numbersArray[i] = [parseInt(questionsList[i].getAttribute('questionid')), i + 1];
    }
    
    var formData = new FormData();
    formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));
    formData.append('testId', testId.value);
    formData.append('numbersArray', JSON.stringify(numbersArray));

    var response = await fetch('/changeOrderOfQuestionNumbers', {
        method: 'POST',
        body: formData
    });

    if (!response.ok) {
        console.log(response.status);
        printErrorMessage('Ой... Что-то пошло нет так:(');
    }
}

function printErrors(data){
    var errorsBlock = document.createElement('div');
    errorsBlock.id = "errorsBlock";
    errorsBlock.classList.add('alert');
    errorsBlock.classList.add('alert-danger');

    var ul = document.createElement('ul');

    for (error in data) {
        var li = document.createElement('li');
        li.innerHTML = data[error];
        ul.append(li);
    }

    errorsBlock.append(ul);
    document.querySelector('.container').prepend(errorsBlock);
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

function changeAnswersId(answerType){
    var requestForSelector = ".answer input[type='" + answerType + "']";   
    var answers = document.querySelectorAll(requestForSelector);
    var answersCount = answers.length;

    for (var i = 0; i < answersCount; i++) {
        answers[i].setAttribute('answerid', i);    
    }
}

function addQuestionToList(data) {
    var template = questionForListTemplate.innerHTML;
    template = template.replace('[[cutQuestion]]', data['cutQuestion']);
    template = template.replace('[[fullQuestion]]', data['fullQuestion']);
    template = template.replace('[[cutAnswer]]', data['cutAnswer']);
    template = template.replace('[[fullAnswer]]', data['fullAnswer']);
    template = template.replace('[[balls]]', data['ballsForQuestion']);
    template = template.replace('[[number]]', data['questionNumber']);
    template = template.replace('[[questionId]]', data['questionId']);

    var question = document.createElement('div');
    question.innerHTML = template;

    questionsList.append(question);
}

function checkQuestionData(answerType){
    var errors = [];

    if (document.querySelector('#question').value == "") {
        errors.push("Введите вопрос");
    }

    if (ballsCount.value == "") {
        errors.push("Введите количество баллов");
    }

    if (answerType == "oneAnswer") {
        if (!document.querySelectorAll(".answer input[type='radio']:checked").length) {
            errors.push("Не выбран правильный ответ");
        } else {
            var answers = document.querySelectorAll(".answer input[type='text']");

            for (var i = 0; i < answers.length; i++) {
                if (answers[i].value == "") {
                    errors.push("Какой-то из ответов пустой");
                    break;
                }
            }
        }
    } else if (answerType == "multipleAnswers") {
        if (!document.querySelectorAll(".answer input[type='checkbox']:checked").length) {
            errors.push("Не выбран правильный ответ");
        } else {
            var answers = document.querySelectorAll(".answer input[type='text']");

            for (var i = 0; i < answers.length; i++) {
                if (answers[i].value == "") {
                    errors.push("Какой-то из ответов пустой");
                    break;
                }
            }
        }
    } else {
        if (document.querySelector('#answers input').value == "") {
            errors.push('Ответ пустой');
        }
    }

    return errors;
}

async function openPublish(){
    if (questionsCount.innerHTML == 'Всего вопросов: 0') {
        printErrorMessage('Добавьте ещё вопрос');
    } else {
        var formData = new FormData();
        formData.append('testId', testId.value);
        formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));

        var response = fetch ('/finishCreatingTest', {
            method: "POST",
            body: formData
        });

        newTest.remove();
        rightBlock.innerHTML = "";
        
        var publishTemplate = document.querySelector('#publishTemplate').innerHTML;

        publishTemplate = publishTemplate.replace(/publishLink/g, 'http://localhost/test/preface/' + testId.value);
        publishTemplate = publishTemplate.replace(/resultsLink/g, 'http://localhost/stat/results/' + testId.value);
        publishTemplate = publishTemplate.replace(/myTestsLink/g, 'http://localhost/stat/results');

        var publishBlock = document.createElement('div');
        publishBlock.id = "publishBlock";
        publishBlock.innerHTML = publishTemplate;
        publishBlock.classList.add('col-9');
        publishBlock.classList.add('pr-5');

        document.querySelector('.container .row').prepend(publishBlock);
    }
}

async function printQuestion(el) {
    newTest.querySelector('h2').innerHTML = "Редактирование вопроса";

    if (document.querySelector('#editableQuestion')) {
        editableQuestion.style.opacity = 0.4;
        editableQuestion.id = "";
    }

    var questionsList = document.querySelectorAll('.questionFromList');
    
    el.id = 'editableQuestion';
    el.style.opacity = 1;
    var id = el.getAttribute('questionid');

    var formData = new FormData();
    formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));
    formData.append('id', el.getAttribute('questionid'));

    var response = await fetch('/getQuestion', {
        method: 'POST',
        body: formData
    });

    if (response.ok) {
        var questionData = await response.json();

        document.querySelector('#question').value = questionData['questions'];
        ballsCount.value = questionData['balls'];

        if (questionData['type'] == "oneAnswer") {
            changeAnswerType(oneAnswer);

            var answers = JSON.parse(questionData['answer']);
            var trueAnswer = JSON.parse(questionData['trueAnswer']);
            var answersCount = answers.length;

            for (var i = 0; i < answersCount - 2; i++) {
                addNewAnswer(addAnswerToOneType);
            }

            var answersField = document.querySelectorAll("#answers input[type='text']");
            var answersRadio = document.querySelectorAll("#answers input[type='radio']");

            for (var i = 0; i < answersCount; i++) {
                answersField[i].value = answers[i];
            }

            answersRadio[trueAnswer].checked = true;
        } else if (questionData['type'] == "multipleAnswers") {
            changeAnswerType(multipleAnswers);

            var answers = JSON.parse(questionData['answer']);
            var trueAnswer = JSON.parse(questionData['trueAnswer']);
            var answersCount = answers.length;

            for (var i = 0; i < answersCount - 2; i++) {
                addNewAnswer(addAnswerToMultiType);
            }

            var answersField = document.querySelectorAll("#answers input[type='text']");
            var answersCheckbox = document.querySelectorAll("#answers input[type='checkbox']");

            for (var i = 0; i < answersCount; i++) {
                answersField[i].value = answers[i];
            }

            for (var i = 0; i < trueAnswer.length; i++) {
                answersCheckbox[trueAnswer[i]].checked = true;
            }
        } else if (questionData['type'] == "numberAnswer") {
            var answer = JSON.parse(questionData['trueAnswer']);

            changeAnswerType(numberAnswer);

            numberAnswerField.value = answer[0];
            errorAnswerField.value = answer[1];
        } else if (questionData['type'] == 'textAnswer') {
            changeAnswerType(textAnswer);
            textAnswerField.value = JSON.parse(questionData['trueAnswer']);
        }
    } else {
        console.log(response.status);
        printErrorMessage('Ой... Что-то пошло нет так:(');
    }
    
    if (!document.querySelector('#newQuestionButton')) {
        var newQuestionButton = document.createElement('button');
        newQuestionButton.id = "newQuestionButton";
        newQuestionButton.classList.add('btn');
        newQuestionButton.classList.add('btn-outline-primary');
        newQuestionButton.classList.add('mt-4');
        newQuestionButton.classList.add('float-right');
        newQuestionButton.classList.add('mr-2');
        newQuestionButton.setAttribute('onclick', 'closeEdit()');
        newQuestionButton.innerHTML = "Создать новый вопрос";

        nextStepButton.after(newQuestionButton);

        nextStepButton.value = "Редактировать";
        nextStepButton.setAttribute('onclick', "updateQuestion()");
    }

}

async function closeEdit() {
    var formData = new FormData();
    formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));
    formData.append('testId', testId.value);

    var response = await fetch('/getTestInfoToView', {
        method: 'POST',
        body: formData
    });    

    if (response.ok) {
        newTest.querySelector('h2').innerHTML = "Создание теста";
        
        newQuestionButton.remove();

        clearQuestionBlock();
        updateTestData(await response.json());

        nextStepButton.value = "Продолжить";
        nextStepButton.setAttribute('onclick', "addQuestion()");

        var questionsList = document.querySelectorAll('.questionFromList');

        for (question of questionsList) {
            question.style.opacity = 0.4;
        }      
    } else {
        console.log(response.status);
        printErrorMessage('Ой... Что-то пошло не так :(');
    }
}

async function updateQuestion() {
    if (document.getElementById('errorsBlock')) {
        document.querySelector('#errorsBlock').remove();
    } 

    var errors = checkQuestionData(document.getElementById('answersTypes').querySelector('button.disabled').id);

    if (errors.length != 0) {
        printErrors(errors);
    } else {
        var formData = getDataForQuestionsChange();   
        formData.append('id', editableQuestion.getAttribute('questionid'));

        var response = await fetch('/updateQuestion', {
            method: 'POST',
            body: formData
        });

        if (response.ok) {
            newTest.querySelector('h2').innerHTML = "Создание теста";

            clearQuestionBlock();

            var responseData = await response.json();

            updateTestData(responseData);

            newQuestionButton.remove();
            nextStepButton.setAttribute('onclick', 'addQuestion(this)');
            nextStepButton.value = "Продолжить";
            
            editableQuestion.querySelector('.questionText').innerHTML = responseData['cutQuestion'];
            editableQuestion.querySelector('.questionText').setAttribute('title', responseData['fullQuestion']);
            editableQuestion.querySelector('.answerText').innerHTML = responseData['cutAnswer'];
            editableQuestion.querySelector('.answerText').setAttribute('title', responseData['fullAnswer']);
            editableQuestion.querySelector('.ballsCount').innerHTML = "Баллы: " + responseData['balls'];
            editableQuestion.style.opacity = 0.4;
            editableQuestion.id = "";
        } else {
            if (response.status === 422) {
                var json = await response.json();
                printErrors(json);
            } else {
                console.log(response.status);
                printErrorMessage('Ой... Что-то пошло нет так:(');
            }
        }          
    }
}

async function addQuestion(el) {
    if (document.getElementById('errorsBlock')) {
        document.querySelector('#errorsBlock').remove();
    } 
    
    var errors = checkQuestionData(document.getElementById('answersTypes').querySelector('button.disabled').id);

    if (errors.length != 0) {
        printErrors(errors);
    } else {
        nextStepButton.disabled = true;

        var formData = getDataForQuestionsChange();    
        var response = await fetch('/addQuestion', {
            method: 'POST',
            body: formData
        });

        if (response.ok) {
            var responseData = await response.json();

            clearQuestionBlock();
            updateTestData(responseData);
            addQuestionToList(responseData);

            nextStepButton.disabled = false;
        } else {
            if (response.status === 422) {
                var json = await response.json();
                printErrors(json);
            } else {
                console.log(response.status);
                printErrorMessage('Ой... Что-то пошло нет так:(');

                nextStepButton.disabled = true;
            }
        }          
    }
}

function getDataForQuestionsChange() {
    var question = document.getElementById('question').value;
    var ballsCount = document.getElementById('ballsCount').value;
    var answerType = document.getElementById('answersTypes').querySelector('button.disabled').id;

    if (answerType == "oneAnswer" || answerType == "multipleAnswers") {
        var answers = document.getElementById("answers").querySelectorAll("input[type='text']");
        var answersValue = [];

        for (var i = 0; i < answers.length; i++) {
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
    } else if (answerType == "numberAnswer") {
        var answerNumber = parseFloat(numberAnswerField.value);
        var error = parseFloat(errorAnswerField.value);
        var answer = [answerNumber, error];
        var jsonTrueAnswers = JSON.stringify(answer);
        var jsonAnswers = null;
    } else if (answerType == "textAnswer") {
        var answer = textAnswerField.value;
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

    return formData;
}

function clearQuestionBlock() {
    document.querySelector('#question').value = "";
    document.querySelector('#ballsCount').value = "";
    changeAnswerType(oneAnswer);    
}

function updateTestData(data) {
    questionsCount.innerHTML = "Всего вопросов: " + data['questionCount'];
    allBalls.innerHTML = "Всего баллов: " + data['balls'];
    timeForQuestion.innerHTML = data['time'] + " мин. на один вопрос";    
}

$(function() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/getTags', false);

    try {
        xhr.send();
        var availableTags = JSON.parse(xhr.response);
    } catch {
        console.log('error');
    }

    function split( val ) {
        return val.split( /,\s*/ );
    }

    function extractLast( term ) {
        return split( term ).pop();
    }
 
    $( "#tags" )
      // don't navigate away from the field on tab when selecting an item
        .on( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).autocomplete( "instance" ).menu.active ) {
                event.preventDefault();
            }
        })
        .autocomplete({
        minLength: 0,
        source: function( request, response ) {
          // delegate back to autocomplete, but extract the last term
          response( $.ui.autocomplete.filter(
            availableTags, extractLast( request.term ) ) );
        },
        focus: function() {
          // prevent value inserted on focus
          return false;
        },
        select: function( event, ui ) {
          var terms = split( this.value );
          // remove the current input
          terms.pop();
          // add the selected item
          terms.push( ui.item.value );
          // add placeholder to get the comma-and-space at the end
          terms.push( "" );
          this.value = terms.join( ", " );
          return false;
        }
    });
});