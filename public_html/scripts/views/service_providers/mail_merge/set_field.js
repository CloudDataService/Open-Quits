$(document).ready(function() {

	$('#mail_merge_field_form').validate({
		success: function(label) {
			label.addClass("valid").text("");
		},
		rules: {
			mmf_name: {
				required: true,
				minlength: 1,
				maxlength: 32
			},
			mmf_format: {
				required: true
			},
			mmf_value: {
				required: true
			}
		},
		errorPlacement: function(error, element) {
			error.appendTo(element.parent("td").next(".e"));
		}
	});

});
