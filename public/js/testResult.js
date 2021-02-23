async function setUserName() {
	if (userName.value == "") {
		document.querySelector('#userNameField input').classList.add('border');
		document.querySelector('#userNameField input').classList.add('border-danger');

		return;
	}

	var formData = new FormData();
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