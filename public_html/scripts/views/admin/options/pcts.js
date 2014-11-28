// JavaScript Document
$(document).ready(function() {


	$('form#pct_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			pct_name: {
				required: true
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});


	$("a.delete-pct").on("click", function(e) {
		return confirm('Are you sure you want to delete this Local Authority?');
	});


});
