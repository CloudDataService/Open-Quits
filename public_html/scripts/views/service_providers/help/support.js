// JavaScript Document
$(document).ready(function () {

	$('#contact').change(function () {

		if($(this).val() == 'Yes')
		{
			$('tr.contact').show();
		}
		else
		{
			$('tr.contact').hide();
		}

	});

	$('#support_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			problem: {
				required: true
			},
			description: {
				required: true
			},
			contact: {
				required: true
			},
			contact_telephone: {
				required: function(element) {
					return ($('#contact').val() == 'Yes' ? true : false);
				}
			},
			contact_time: {
				required: function(element) {
					return ($('#contact').val() == 'Yes' ? true : false);
				}
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});

});
