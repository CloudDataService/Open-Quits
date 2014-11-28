var AS = (function($) {

	// URLs
	var config = {
		post_url: "/service-providers/autosave/save",
		get_url: "/service-providers/autosave/get",
		delete_url: "/service-providers/autosave/delete"
	};

	// jQuery handle to the form to be saved
	var $form = null;

	// local copy of autosaved items
	var autosaves = {};

	// Shortcut to get the current URI
	var uri = window.location.pathname;


	/**
	 * Update the UI based on new autosave data
	 */
	var update_ui = function() {
		if ($.isEmptyObject(autosaves)) {
			$(".autosave-control").hide();
		} else {

			// Count of items
			var as_count = 0;

			// Dropdown box
			var $sel = $("select[name=as_id]");

			// Remove all existing options (if any)
			$sel.find("option").remove();

			var as_created_datetime_format = '';

			// Loop all the autosave entries
			$.each(autosaves, function() {
				// Create option and append to dropdown
				var opt = $('<option value="' + this.as_id + '">' + this.as_created_datetime_format + '</option>');
				opt.appendTo($sel);
				// Update the hidden input value
				$("input[name=as_id]").val(this.as_id);
				as_created_datetime_format = this.as_created_datetime_format;
				as_count++;
			});

			if (as_count === 1) {
				// Just one - show the single item UI
				// Update info for date
				$(".autosave-single span.date").text(as_created_datetime_format);
				$(".autosave-single").show();
				$(".autosave-multi").hide();
			} else {
				// Multiple - show dropdown
				$(".autosave-multi").show();
				$(".autosave-single").hide();
			}

			// Show autosave box.
			$(".autosave-control").show();

			$(".autosave-restore").on("click", function() {

				// Get the type of restore being done - single one or from select
				var type = $(this).val();
				if (type === "single") {
					// Single - get the ID from the hidden input element
					var as_id = $("input[name=as_id]").val();
				} else if (type === "multi") {
					// Multi - get the ID from the dropdown
					var as_id = $("select[name=as_id]").val();
				}

				// Restore the data for this autosave entry
				restore(as_id);
			});
		}
	}


	/**
	 * Find if any autosaves are relevant for the page we're on
	 *
	 * @param string el		Element identifier for form to pass to jQuery
	 */
	var init = function(el) {

		// Handle for form
		$form = $(el);

		// Do a GET to retrieve any autosaves.
		$.ajax({
			url: config.get_url,
			dataType: "json",
			data: { uri: uri },
			success: function(data) {
				autosaves = data;
				update_ui();
			}
		});

	}


	/**
	 * Save the form data on the page and send to server
	 *
	 * @param function callback		Callback function to execute when request has been made
	 */
	var save = function(callback) {

		// Track number of inputs
		var ti = 0, ci = 0, ri = 0;
		// Store data about the inputs
		var values = { text: {}, check: {}, radio: {} };

		// Gather all form data in their respective formats
		$form.find("*").filter(':text, :radio, :checkbox, select, textarea').each(function () {
			if ($(this).is(':text, textarea')) {
				values.text[ti] = { id: $(this).attr("id"), name: $(this).attr("name"), val: $(this).val() }
				ti++;
			} else if ($(this).is('select')) {
				values.text[ti] = { id: $(this).attr("id"), name: $(this).attr("name"), val: $(this).val() }
				ti++;
			} else if ($(this).is(':checkbox')) {
				values.check[ci] = { id: $(this).attr("id"), name: $(this).attr("name"), checked: $(this).is(":checked") }
				ci++;
			} else {
				values.radio[ri] = { id: $(this).attr("id"), name: $(this).attr("name"), checked: $(this).is(":checked") }
				ri++;
			}
		});

		// Data object to send to server
		var data = {
			uri: uri,
			form_data: values
		};

		// Make AJAX request to save details
		$.ajax({
			type: "POST",
			async: true,
			url: config.post_url,
			data: data,
			success: function() {
				// Execute the callback
				callback();
				window.onbeforeunload = null;
			},
			error: function() {
				// Execute the callback (even on failure!)
				callback();
			}
		});

	}


	/**
	 * Restore a specific autosave entry to the page and delete it on the server
	 *
	 * @param int as_id		Autosave entry ID to get and restore
	 */
	var restore = function(as_id) {

		var $inputs = $form.find(":input");
		$inputs.each(function() {
			var type = this.type;
			var tag = this.tagName.toLowerCase();
			if (type == 'text' || type == 'password' || tag == 'textarea') {
				this.value = "";
			} else if (type == 'checkbox' || type == 'radio') {
				this.checked = false;
			} else if (tag == 'select') {
				this.selectedIndex = -1;
			}
		});

		// Get data
		var form_data = autosaves[as_id].as_data;

		$.each(form_data.text, function() {
			var $input = $form.find("[name='" + this.name + "']");
			$input.val("" + this.val + "").trigger("change");
		});

		$.each(form_data.check, function() {
			var $input = $form.find("[type=checkbox][id='" + this.id + "']");
			$input.attr("checked", (this.checked == "true"));
		});

		if (form_data.radio) {
			$.each(form_data.radio, function() {
				var $input = $form.find("[type=radio][id='" + this.id + "']");
				$input.attr("checked", (this.checked == "true"));
			});
		}

		// Show notification that the form has been updated with the saved details
		$('<div class="action">The form has been updated.</div>')
			.prependTo('.body_content').fadeIn();
		setTimeout(function() { $('div.action').fadeOut('slow'); }, 2500);

		// Delete this autosave data
		delete_autosave(as_id);
	};


	/**
	 * Delete an autosave entry once it has been restored
	 *
	 * @param int as_id		Autosave ID
	 */
	var delete_autosave = function(as_id) {
		$.ajax({
			type: "POST",
			async: false,
			url: config.delete_url,
			data: { as_id: as_id }
		});
	};


	// Return the object with the publicly-callable functions
	return {
		init: init,
		save: save
	};


})(jQuery);
