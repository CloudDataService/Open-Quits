// JavaScript Document
$(document).ready(function () {

	$('#claim_options_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			initial: {
				number: true
			},
			automatic_emails: {
				required: function () {
					return $('#automatic_email:checked').length;
				}
			},
			rejected_claims_email_note: {
				maxlength: 500
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});


	function disable(checkbox, element)
	{
		if(checkbox.is(':checked'))
		{
			element.removeAttr('disabled');
		}
		else
		{
			element.attr('disabled', 'disabled');
		}
	}

	disable($('#automatic_email'), $('#automatic_emails'));

	$('#automatic_email').change(function () {

		disable($(this), $('#automatic_emails'));

	});

	disable($('#rejected_claims_email_enabled'), $('#rejected_claims_email_note'));

	$('#rejected_claims_email_enabled').change(function () {

		disable($(this), $('#rejected_claims_email_note'));

	});


	$("select[name=pct_id]").on("change", function() {
		$(this).parents("form").submit();
	});

});
