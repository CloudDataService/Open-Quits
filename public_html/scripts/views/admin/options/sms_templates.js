// JavaScript Document
$(document).ready(function() {


	// Validate form
	$('form#sms_template_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			sms_t_title: {
				required: true
			},
			sms_t_text: {
				required: true
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});


	// Attach event listeners to the textareas to calculate sms count and chars remaining
	$("textarea[name=sms_t_text]").on("change keyup paste", function() {

		var id = $(this).attr("id");

		// Get handle to the textarea via the ID
		var input = $("textarea#" + id);

		// Count how many SMSs will be used based on number of chars entered
		var sms_count = Math.floor(input.val().length/160) + 1;

		// Plural suffix for sms count text
		var plural = (sms_count > 1) ? "s" : "";

		// Calculate how many characters remaining until next SMS limit
		var chars_remaining = -1 * (input.val().length - sms_count * 160);

		// Get handles to notice elements based on the ID of the textarea
		var sms_count_el = $("#" + id + "_sms_count");
		var chars_remaining_el = $("#" + id + "_chars_remaining");

		// Set contents of notice DIVs
		sms_count_el.html("<span>Using " + sms_count + " message" + plural + "</span>");
		chars_remaining_el.html("<span>" + chars_remaining + " characters remaining</span>");

		// Update styles accordingly
		sms_count_el.css("color", (sms_count > 1) ? "red" : "black");
		chars_remaining_el.css("color", (chars_remaining < 10) ? "red" : "black");

	}).trigger("change");



	// Handle delete link
	$("a.delete-template").on("click", function(e) {
		return confirm('Are you sure you want to delete this SMS template?');
	});


});
