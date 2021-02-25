function copyLink() {
	var link = document.querySelector('#testLink');
	link.select();
	document.execCommand("copy");
}