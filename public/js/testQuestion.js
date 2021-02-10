async function setAnswer() {
	var formData = new FormData();
	formData.append('questionId', questionId.value);
	formData.append('questionNumber', questionNumber.value);
	formData.append('questionType', questionType.value);
	formData.append('testId', testId.value);
    formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));

    if (!checkAnswer(questionType.value)) {
    	return;
    }

	if (questionType.value == "oneAnswer") {
		var answers = document.querySelectorAll("input[name='answer']");
		for (var i = 0; i < answers.length; i++) {
			if (answers[i].checked) {
				formData.append('value', JSON.stringify(i));
				break;
			}
		}
	} else if (questionType.value == "multipleAnswers") {
		var answers = document.querySelectorAll("input[name='answer']");
		var value = [];
		
		for (var i = 0; i < answers.length; i++) {
			if (answers[i].checked) {
				value.push(i);
			}
		}

		formData.append('value', JSON.stringify(value));
	} else if (questionType.value == "textAnswer" || questionType.value == "numberAnswer") {
		formData.append('value', JSON.stringify(document.querySelector("input[name='answer']").value));
	}

	var response = await fetch('/addAnswer', {
		method: 'POST',
		body: formData
	});

	if (response.ok) {
		var response = await response.json();

		if (response == "lastQuestion") {
			document.location.href = '/' + testId.value + '/result';
		}

		questionNumber.value = response.number;
		questionId.value = response.id;
		questionType.value = response.type;
		questionText.innerHTML = response.questions;
		questionsBalls.innerHTML = "За ответ на этот вопрос даётся " + response.balls + " баллов";

		if (response.type == "oneAnswer") {
			printUlAnswers("radio", response.answer);
		} else if (response.type == "multipleAnswers") {	
			printUlAnswers("checkbox", response.answer);
		} else if (response.type == "textAnswer") {
			printInputAnswer("text");
		} else if (response.type == "numberAnswer") {
			printInputAnswer("number");
		}
	} else {
		printErrorMessage('Ой... Что-то пошло нет так:(');
	}
}

function checkAnswer(questionType) {
	var error = false;

	if (questionType == "oneAnswer" || questionType == "multipleAnswers" ) {
		var answers = document.querySelectorAll('#answer input');
		var checkedAnswer = false;
		
		for (answer of answers) {
			if (answer.checked) {
				var checkedAnswer = true;
				break;
			}
		}

		if (!checkedAnswer) {
			error = true;
		}
	} else if (
		(questionType == "numberAnswer" || questionType == "textAnswer") && 
		document.querySelector('#answer').value == ""
	) {
		error = true;
		document.querySelector('#answer').classList.add('border');
		document.querySelector('#answer').classList.add('border-danger');
	}

	if (error) {
		printErrorMessage('Вы забыли ответить');
		return false;
	} else {
		return true;
	}
}

async function openQuestion(el) {
	var requestNumber = el.querySelector('p').getAttribute('number');

	var formData = new FormData();
	formData.append('number', requestNumber);
	formData.append('testId', testId.value);
	formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));

	var response = await fetch('/getQuestionForTest', {
		method: 'POST',
		body: formData
	});

	if (response.ok) {
		document.querySelector('.selectedNumber').setAttribute('onclick', "openQuestion(this)");
		document.querySelector('.selectedNumber').classList.remove('selectedNumber');

		el.classList.add('selectedNumber');
		el.removeAttribute('onclick', "openQuestion(this)");

		var response = await response.json();

		questionNumber.value = response.number;
		questionId.value = response.id;
		questionType.value = response.type;
		questionText.innerHTML = response.questions;
		questionsBalls.innerHTML = "За ответ на этот вопрос даётся " + response.balls + " баллов";

		if (response.type == "oneAnswer") {
			printUlAnswers("radio", response.answer);
		} else if (response.type == "multipleAnswers") {	
			printUlAnswers("checkbox", response.answer);
		} else if (response.type == "textAnswer") {
			printInputAnswer("text");
		} else if (response.type == "numberAnswer") {
			printInputAnswer("number");
		}
	} else {
		printErrorMessage('Ой... Что-то пошло нет так:(');
	}
}

async function checkTime() {
	
}

async function setUserName() {
	if (userName.value == "") {
		document.querySelector('#userNameField input').classList.add('border');
		document.querySelector('#userNameField input').classList.add('border-danger');

		return;
	}

	formData = new FormData();
	formData.append('userName', userName.value);
	formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));

	var response = await fetch('/setUserName', {
		method: 'POST',
		body: formData
	});

	if (response.ok) {
		userNameField.innerHTML = "<h5>Спасибо</h5>";
	} else {
		printErrorMessage('Ой... Что-то пошло нет так:(');
	}
}

//type = radio or checkbox
function printUlAnswers(type, answers) {
	var ul = document.createElement('ul');
	ul.id = "answer";
	ul.style.listStyleType = "none";
	ul.classList.add('pl-0');

	answers = JSON.parse(answers);
	for (answer of answers) {
		var input = document.createElement('input');
		input.type = type;
		input.name = "answer";
		input.classList.add('mr-1');

		var label = document.createElement('label');
		label.append(input);
		label.append(answer);

		var li = document.createElement('li');
		li.append(label);

		ul.append(li);
	}

	var p = document.createElement('p');
	if (type == "radio") {
		p.innerHTML = "Выберите один вариант ответа";
	} else {
		p.innerHTML = "Выберите ответ(ы)";
	}

	answersBlock.innerHTML = "";
	answersBlock.append(p);
	answersBlock.append(ul);
}

//type = text or number
function printInputAnswer(type) {
	var p = document.createElement('p');
	var input = document.createElement('input');
	input.id = "answer";
	input.name = "answer";
	input.type = type;

	if (type == "number") {
		p.innerHTML = "Введите число";
	} else {
		p.innerHTML = "Введите ответ";
		input.classList.add('col-md-10');
	}	

	answersBlock.innerHTML = "";
	answersBlock.append(p);
	answersBlock.append(input);
}

function printErrorMessage(newMessage){
    if (document.getElementById('message')) {
        message.querySelector('div').innerHTML = newMessage
    } else {
        var errorPopup = document.querySelector('#errorPopup').innerHTML;
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

function closeMessage(){
    message.remove();
}