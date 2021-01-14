pageSelector.onchange = function() {
	var url = window.location.search;

	if (/search/iu.test(url)) {
		var parameters = url.replace('?','').split('&');

		for (parameter of parameters) {
			if (/search/iu.test(parameter)) {
				document.location.href = "/tests?page=" + pageSelector.value + "&" + parameter;
			}
		}
	} else {
		document.location.href = "/tests?page=" + pageSelector.value;
	}
}