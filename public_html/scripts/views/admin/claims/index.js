// JavaScript Document
$(document).ready(function() {

	$('.datepicker').datepicker({'maxDate' : new Date()});

	$('#check_all').change(function () {

		if($('#check_all:checked').length)
		{
			$('input[type="checkbox"]').each(function () {$(this).attr('checked', 'checked')});
		}
		else
		{
			$('input[type="checkbox"]').each(function () {$(this).removeAttr('checked')});
		}

	});

	$('form#claims_form').on("submit", function (e) {

		var set_status = $('select#set_status option:selected').val();

		var total_claims = $('.results input.claim:checked').size();

		if(set_status == "" || total_claims == 0)
		{
			return false;
		}
		else
		{
			return confirm('Click ok to set ' + total_claims + (total_claims == 1 ? ' claim' : ' claims') + ' as "' + set_status + '"');
		}
	});

});
