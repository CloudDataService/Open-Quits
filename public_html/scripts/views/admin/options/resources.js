// JavaScript Document
$(document).ready(function () {

	$('form#resource_form').validate({
		rules: {
			title: {
				required: true
			},
			cat_id: {
				required: true
			},
			description: {
				required: true
			},
			userfile: {
				required: function(element) {
					return ! $('#resource').length;
				},
				accept: "pdf,doc,docx"
			}
		},
		messages: {
			userfile: {
				accept: "Only PDF and DOC/DOCX files."
			}
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent("td").next(".e") );
		}
	});

});
