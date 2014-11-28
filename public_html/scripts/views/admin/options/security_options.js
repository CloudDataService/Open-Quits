// JavaScript Document
$(document).ready(function () {

	$('#security_options_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			organisation_name: {
				required: true
			},
			address: {
				required: true
			},
			email: {
				required: true,
				email: true
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

	disable($('#tel_support_enabled'), $('#tel_support'));

	$('#tel_support_enabled').change(function () {

		disable($(this), $('#tel_support'));

	});

	$("select[name=pct_id]").on("change", function() {
		$(this).parents("form").submit();
	});

});
