// JavaScript Document
$(document).ready(function () {

	$("#ci_gp").autocomplete({
		source: "/ajax/get_gps/",
		minLength: 1
	});

	// hide prescribing gp tr
	$("#mf_gp_code_tr").hide();

	//$("#mf_gp_code").autocomplete("/service_providers/monitoring_forms/get_gps/");
	$("#mf_gp_code").autocomplete({
		source: "/ajax/get_gps/",
		minLength: 1
	});

	$("#advisor").autocomplete({
		source: "/ajax/get_advisors/",
		minLength: 1,
		select: function(event, ui) {
			if (ui.item) {
				event.preventDefault();
				$(this).val(ui.item.label);
				$("input#a_id").val(ui.item.value);
			}
		}
	});

	$('#support_1, #support_2').each(function() {
		prescribing_gp($(this).val());
	});

	$('#support_1, #support_2').change(function() {
		prescribing_gp($(this).val());
	});

	function prescribing_gp(support)
	{
		if(support == 'Champix' || support == 'Zyban') {
			$('#mf_gp_code_tr').show();
		}
	}

	$('a.fancybox').fancybox();


	// Disable advanced date functionality for some problematic SPs
	var advanced_dates = true;
	var problematic_sps = [347, 351, 190, 225, 64, 264, 237, 86, 311, 163, 33, 424, 154, 22, 189, 216];
	$.each(problematic_sps, function(i, sp_id) {
		if ($("body").hasClass("sp-" + sp_id)) {
			advanced_dates = false;
			return false;
		}
	});

	if (advanced_dates) {

		$('.datepicker').datepicker();
		$('#date_of_last_tobacco_use').datepicker({'maxDate' : new Date()});
		$(window).unload(function() {
			$('.datepicker, #date_of_last_tobacco_use').datepicker('destroy');
		});

		// Auto-calculate and complete the 4-week and 12-week dates
		$("#agreed_quit_date").on("change", function() {
			// Form fields
			var $date_4_week = $("#date_of_4_week_follow_up");
			var $date_12_week = $("#date_of_12_week_follow_up");

			// Get date entered for this field
			var dt = $(this).val();
			// format the date. DD/MM/YYYY is the expected format of the form field
			var m = moment(dt, "DD/MM/YYYY");

			if ( ! m) { return; }

			// Alter the date for +4 and +12 weeks
			var date_4_weeks = m.clone().add('weeks', 4).format('DD/MM/YYYY');
			var date_12_weeks = m.clone().add('weeks', 12).format('DD/MM/YYYY');

			// set the date values on the form if not already completed
			//if ($date_4_week.val() === '')
			$date_4_week.val(date_4_weeks);
			//if ($date_12_week.val() === '')
			$date_12_week.val(date_12_weeks);
		});

	}

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

	$('.other_select').each(function () {
		other_select($(this));
	});

	$('.other_select').change(function () {
		other_select($(this));
	});

	function title(title)
	{
		if(title == 'Mr')
		{
			$('#gender').val('Male');

			gender('Male');
		}
		else if(title in {'Mrs':1, 'Miss':1})
		{
			$('#gender').val('Female');

			gender('Female');
		}

	}

	title($('#title').val());

	$('#title').on("change", function () {
		title($(this).val());
	});

	function gender(gender)
	{
		if(gender == 'Male')
		{
			$('#pregnant').attr('disabled', 'disabled');
			$('#breastfeeding').attr('disabled', 'disabled');
		}
		else if(gender == 'Female')
		{
			$('#pregnant').removeAttr('disabled');
			$('#breastfeeding').removeAttr('disabled');
		}
	}

	gender($('#gender').val());

	$('#gender').on("change", function () {
		gender($(this).val());
	});

	function sms(tel_mobile)
	{
		if(tel_mobile !== '')
		{
			$('#sms').removeAttr('disabled');
		}
		else
		{
			$('#sms').attr('disabled', 'disabled');
		}
	}

	sms($('#tel_mobile').val());

	$('#tel_mobile').on("change", function () {
		sms($(this).val());
	});


	function pad(number)
	{
		var str = '' + number;

		if(str.length < 2)
		{
			str = '0' + str;
		}

		return str;
	}

	function tier_3(sp_id)
	{
		if(sp_id)
		{
			$('#tier_3').hide();
		}
		else
		{
			$('#tier_3').show();
		}
	}


	$(".tickbox_activator").on("change", function(e) {
		// Which things relate to what
		var group = $(this).data("group");
		var not = $(this).data("not");
		var condition = (not === true ? $(this).is(":unchecked") : $(this).is(":checked"));

		if (condition) {
			$(".tickbox_activate_content[data-group='" + group + "']").show().focus();
		} else {
			$(".tickbox_activate_content[data-group='" + group + "']").hide();
		}
	}).trigger("change");


	/* Quit CO reading box */
	function quit_co() {
		var quit = ($("select#treatment_outcome_4").val() == 'Quit CO verified'),
			$co_row = $("tr.js-co-quit");

		if (quit) {
			$co_row.show();
		} else {
			$co_row.hide();
		}
	}

	$("select#treatment_outcome_4").on("change", function() {
		quit_co();
	}).trigger("change");

	/*
	tier_3($('#sp_id').val());

	$('#sp_id').change(function() {
		tier_3($(this).val());
	});
	*/


	/*
	$("#monitoring_form_form").validate({
		success: function(label) {
			label.addClass("valid").text("");
		},
		rules: {
			fname: {
				required: true
			}
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent("td").next(".e") );
		}
	});
	*/




	jQuery.validator.addMethod("valid_future_date", function(value, element) {
		var parts = value.match(/(\d+)/g);
		if (value.length === 0) return true;
		var date_12_week = new Date(parts[2], parts[1]-1, parts[0]);
		var date_valid = new Date(2012, 11, 24);
		return (date_12_week >= date_valid);
	}, "The 12 week quit date must be after 24th December 2012");




	$("#monitoring_form_form").validate({
		success: function(label) {
			label.addClass("valid").text("");

			// if the save has been initiated but the form was invalid, then hide the submitting and re-show the save button
			$('.loading-save').hide();
			$('.btn-save').removeAttr('disabled').show();
		},
		rules: {
			nhs_number: {
				rangelength: [10, 10]
			},
			title: {
				required: true
			},
			title_other: {
				required: function(element) {
					return $('select#title').val() === 'Other';
				}
			},
			fname: {
				required: true
			},
			sname: {
				required: true
			},
			gender: {
				required: true
			},
			address: {
				required: true
			},
			post_code: {
				required: true
			},
			occupation_code: {
				required: true
			},
			ethnic_group: {
				required: true
			},
			ms_id: {
				required: true
			},
			marketing_other: {
				required: function(element) {
					return $('select#ms_id').val() == 'Other';
				}
			},
			agreed_quit_date: {
				required: true,
				british_date: true
			},
			date_of_last_tobacco_use: {
				required: true,
				british_date: true
			},
			"date_of_4_week_follow_up": {
				required: true,
				british_date: true
			},
			"date_of_12_week_follow_up": {
				british_date: true,
				valid_future_date: true
			},
			intervention_type: {
				required: true
			},
			intervention_type_other: {
				required: function(element) {
					return $('select#intervention_type').val() === 'Other';
				}
			},
			tel_daytime: {
				required: function(element) {
					return $("#tel_mobile").val() === "" && $("form#monitoring_form_form").data("mode") === "add";
				}
			},
			tel_mobile: {
				required: function(element) {
					return $("#tel_daytime").val() === "" && $("form#monitoring_form_form").data("mode") === "add";
				}
			}
		},
		messages: {
			nhs_number: 'Must be 10 characters long',
			tel_daytime: "At least one phone number is required.",
			tel_mobile: "At least one phone number is required."
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent("td").next(".e") );
		}
	});



	// Initialise the autosave feature
	if (window.AS) {
		AS.init("#monitoring_form_form");
	}


	if ($("#monitoring_form_form").data("mode") == "add") {
		DS.init("#monitoring_form_form");
	}

	// when form is submitted, wait for
	$("#monitoring_form_form").on('submit', function() {
		$('.loading-save').show();
		$('.btn-save').attr('disabled', 'disabled');
		$('.btn-save').hide();
	});

});
