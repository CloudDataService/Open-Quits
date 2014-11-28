$(document).ready(function() {

	// "Other" option for dropdowns
	$("select.other").on("change", function() {
		var $sel = $(this);
		if ($sel.val() === 'Other') {
			$sel.siblings('.other_label, .other_value').css({ 'display': 'inline' });
		} else {
			$sel.siblings('.other_label, .other_value').hide();
		}
	}).trigger("change");

	// Validation
	$('#ac_form').validate({
		success: function(label) {
			label.addClass("valid").text("");
		},
		rules: {
			ac_title: {
				required: true
			},
			ac_title_other: {
				required: function(element) {
					return $("select[name='ac_title']").val() === 'Other';
				}
			},
			ac_fname: {
				required: true
			},
			ac_sname: {
				required: true
			}
		},
		errorPlacement: function(error, element) {
			error.appendTo(element.parent("td").next(".e"));
		}
	});

});
