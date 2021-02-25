setInterval(checkTime, 1000);

async function setAnswer() {
    if (!checkAnswer(questionType.value)) {
    	return;
    }

    replyButton.disabled = true;

	var formData = new FormData();
	formData.append('questionId', questionId.value);
	formData.append('questionType', questionType.value);
	formData.append('testId', testId.value);
    formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));

    var numbers = document.querySelectorAll('.number');
    var selectedNumber = document.querySelector('.selectedNumber p').innerHTML;

    if (selectedNumber != numbers.length) {
	    for (var i = 0; i < numbers.length; i++) {
	    	if (!numbers[i].classList.contains('bg-primary') && 
	    		numbers[i].querySelector('p').innerHTML != selectedNumber
	    	) {
	    		formData.append('questionNumber', numbers[i].querySelector('p').innerHTML);
	    		break;
	    	}	
	    }
    } else {
    	formData.append('questionNumber', selectedNumber + 1);
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
			return;
		}

		updateQuestion(response);

		var selector = ".number p[questionid='" + response['id'] + "']"; 
		var nextNumber = document.querySelector(selector);
		var selectedNumber = document.querySelector('.selectedNumber');

		nextNumber.parentNode.removeAttribute('onclick');
		nextNumber.parentNode.classList.add('selectedNumber');
		selectedNumber.setAttribute('onclick', "openQuestion(this)");
		selectedNumber.classList.remove('selectedNumber');
		selectedNumber.classList.remove('bg-light');
		selectedNumber.classList.add('bg-primary');
		selectedNumber.classList.add('text-white');

		changeButtonText();

		replyButton.disabled = false;
	} else {
		printErrorMessage('Ой... Что-то пошло нет так:(');
		replyButton.disabled = false;
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

function updateQuestion(response) {
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
}

function changeButtonText() {
	var numbers = document.querySelectorAll('.number');

	if (numbers[numbers.length - 1].classList.contains('selectedNumber')) {
		replyButton.innerHTML = "Завершить тест";
	} else {
		replyButton.innerHTML = "Ответить";
	}
}

async function openQuestion(el) {
	var formData = new FormData();
	formData.append('questionId', el.querySelector('p').getAttribute('questionid'));
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
		updateQuestion(response['question']);

		if (response.hasOwnProperty("answer")) {
			var userAnswer = JSON.parse(response['answer']);

			if (questionType.value == "oneAnswer") {
				var answers = document.querySelectorAll('#answer input');
				answers[userAnswer].checked = true;
			} else if (questionType.value == "multipleAnswers") {
				var answers = document.querySelectorAll('#answer input');

				for (var i = 0; i < userAnswer.length; i++) {
					answers[userAnswer[i]].checked = true;
				}
			} else if (questionType.value == "textAnswer" || questionType.value == "numberAnswer") {
				document.querySelector('#answer').value = userAnswer;
			} 
		}

		changeButtonText();
	} else {
		printErrorMessage('Ой... Что-то пошло нет так:(');
	}
}

function checkTime() {
	if (document.querySelector('#timer')) {
		var minutes = timer.getAttribute('minutes');
		var seconds = timer.getAttribute('seconds');

		if (minutes == 0 && seconds == 0) {
			stopTest();
		}
	  
		if (seconds == 0) {
			minutes -= 1;
			seconds = 59;
		} else {
			seconds -= 1;
		}

		if (seconds == 0 && minutes == 1) {
			timer.classList.add('text-danger');
		}
	  
		timer.setAttribute('minutes', minutes);
		timer.setAttribute('seconds', seconds);  

		if (seconds < 10) {
			timer.innerHTML = "Осталось времени: " + minutes + ":0" + seconds;	
		} else {
			timer.innerHTML = "Осталось времени: " + minutes + ":" + seconds;
		}		
	}
}

async function stopTest() {
	var formData = new FormData();
	formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));
	formData.append('testId', testId.value);

	var response = await fetch('/stopTest', {
		method: 'POST',
		body: formData
	});

	if (response.ok) {
		document.location.href = '/' + testId.value + '/result';
	} else {
		console.log(response.status);
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