// JavaScript Document
$(document).ready(function() {


	// Validate form
	$('form#marketing_source_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			ms_title: {
				required: true
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});

	// Handle delete link
	$("a.delete-source").on("click", function(e) {
		return confirm('Are you sure you want to permanently delete this marketing source?');
	});


});
