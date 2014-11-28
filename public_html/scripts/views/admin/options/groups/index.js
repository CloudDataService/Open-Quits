// JavaScript Document
$(document).ready(function () {

	$('#group_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			name: {
				required: true
			},
			initial: {
				required: true,
				number: true
			},
			follow_up_quit: {
				required: true,
				number: true
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});

});
