/**
 * @author Kishor Mali
 */


jQuery(document).ready(function(){
	

	$().on("click", ".searchList", function(){

	});

	jQuery(document).on("click", ".searchList", function(){
		
	});
	
});
function CopyToClipboard(container_id) {
	$('.code-copy-message').hide();
	if (window.getSelection) {
		if (window.getSelection().empty) { // Chrome
			window.getSelection().empty();
		} else if (window.getSelection().removeAllRanges) { // Firefox
			window.getSelection().removeAllRanges();
		}
	} else if (document.selection) { // IE?
		document.selection.empty();
	}

	if (document.selection) {
		var range = document.body.createTextRange();
		range.moveToElementText(document.getElementById(container_id));
		range.select().createTextRange();
		document.execCommand("copy");
	} else if (window.getSelection) {
		var range = document.createRange();
		range.selectNode(document.getElementById(container_id));
		window.getSelection().addRange(range);
		document.execCommand("copy");
	}
	if (window.getSelection) {
		if (window.getSelection().empty) { // Chrome
			window.getSelection().empty();
		} else if (window.getSelection().removeAllRanges) { // Firefox
			window.getSelection().removeAllRanges();
		}
	} else if (document.selection) { // IE?
		document.selection.empty();
	}
	$('.code-copy-message').show();
	$('.code-copy-message').fadeToggle(1000);
}
