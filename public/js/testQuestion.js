async function setAnswer() {
	var formData = new FormData();
	formData.append('questionId', questionId.value);
	formData.append('questionNumber', questionNumber.value);
	formData.append('questionType', questionType.value);
	formData.append('testId', testId.value);
    formData.append('_token', document.querySelector("meta[name='csrf-token']").getAttribute('content'));


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
		formData.append('value', JSON.stringify(answer.value));
	}

	var response = await fetch('/addAnswer', {
		method: 'POST',
		body: formData
	});

	if (response.ok) {
		var newQuestion = await response.json();
		newQuestion = newQuestion[0];

		// console.log(JSON.parse(newQuestion.answer));

		questionNumber.value = newQuestion.number;
		questionId.value = newQuestion.id;
		questionType.value = newQuestion.type;
		questionText.innerHTML = newQuestion.questions;
		questionsBalls.innerHTML = "За ответ на этот вопрос даётся " + newQuestion.balls + " баллов";

		if (newQuestion.type == "oneAnswer") {
			printUlAnswers("radio", newQuestion.answer);
		} else if (newQuestion.type == "multipleAnswers") {	
			printUlAnswers("checkbox", newQuestion.answer);
		} else if (newQuestion.type == "textAnswer") {
			printInputAnswer("text");
		} else if (newQuestion.type == "numberAnswer") {
			printInputAnswer("number");
		}
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

function printInputAnswer(type) {
	answersBlock.innerHTML = "";

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