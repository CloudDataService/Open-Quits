// JavaScript Document
$(document).ready(function () {

	$('#service_provider_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			name: {
				required: true
			},
			post_code: {
				required: true
			},
			location: {
				required: true
			},
			location_other: {
				required: function(element) {
					return $("select#location").val() == "Other";
				}
			},
			claim_options_initial: {
				number: true
			},
			claim_options_follow_up_quit: {
				number: true
			},
			group_id: {
				required: true
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});

	function claim_options_enabled()
	{
		if($('#claim_options_enabled:checked').length)
		{
			$('.claim_options').removeAttr('disabled');
			$('.claim_options_tr').show();
		}
		else
		{
			$('.claim_options').attr('disabled', 'disabled');
			$('.claim_options_tr').hide();
		}
	}

	claim_options_enabled();

	$('#claim_options_enabled').click(function () {
		claim_options_enabled();
	});


	function other_select($this)
	{
		if($this.val() == 'Other')
		{
			$this.siblings('.other_label, .other_value').css({'display' : 'inline'});
		}
		else
		{
			$this.siblings('.other_label, .other_value').hide();
		}
	}

	$('.other_select').on("change", function () {
		other_select($(this));
	}).trigger("change");

});
