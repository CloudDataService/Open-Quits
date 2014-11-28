// JavaScript Document
$(document).ready(function () {

	function enabled(checkbox)
	{
		if(checkbox.is(':checked'))
		{
			checkbox.parent('td').prev('td').children('textarea').removeAttr('disabled');
		}
		else
		{
			checkbox.parent('td').prev('td').children('textarea').attr('disabled', 'disabled');
		}
	}

	$('input.enabled').each(function () {
		enabled($(this));
	});

	$('input.enabled').change(function () {
		enabled($(this));
	});

	$("select[name=pct_id]").on("change", function() {
		$(this).parents("form").submit();
	});

});
