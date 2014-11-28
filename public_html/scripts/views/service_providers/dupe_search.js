/**
 * Duplicate client search
 *
 * This works by the magic of timers to introduce some delay on input field value changes.
 * When the timer times out it will submit the information to carry out the search.
 * The returned markup will be shown in a layer next to the form.
 *
 */

var DS = (function($) {

	// URLs
	var config = {
		search_url: "/service-providers/monitoring-forms/dupe-search",
		timeout: 1500,		// 1500ms between a form value change and intialising the search
		wrapper: ".dupe_wrapper"
	};

	// timer reference
	var timer = null;

	// form element name
	var els = {
		"fname": "#fname",
		"sname": "#sname",
		"gender": "#gender",
		"dob_date": "#dob_date",
		"dob_month": "#dob_month",
		"dob_year": "#dob_year",
		"post_code": "#post_code",
		"address": "#address",
		"tel_daytime": "#tel_daytime",
		"tel_mobile": "#tel_mobile"
	};

	// jQuery handle to the form to be saved
	var $form = null;
	var $wrapper = null;


	/**
	 * Obtain form values and then make the request to search
	 */
	var do_search = function() {

		var data = {},
			el = null;

		for (param in els) {
			data[param] = $form.find(els[param]).val();
		}

		$.ajax({
			type: "post",
			url: config.search_url,
			data: data,
			success: function(res) {
				if (res.length > 0) {
					show_ui(res);
				} else {
					hide_ui();
				}
			}
		});

	}


	var show_ui = function(res) {
		$wrapper.html(res).fadeIn('fast');
	}


	var hide_ui = function() {
		$wrapper.html("").fadeOut('fast');
	}


	/**
	 * Initialise the short timer. It gets killed and re-created when form els change.
	 */
	var set_timer = function() {
		window.clearTimeout(timer);
		timer = window.setTimeout(do_search, config.timeout);
	}


	/**
	 * Initialise the watching of the forms
	 *
	 * @param string el		Element identifier for form to pass to jQuery
	 */
	var init = function(el) {

		// Handle for form
		$form = $(el);

		$form.on("change", "input, select, textarea", function(e) {
			set_timer();
		});

		$wrapper = $(config.wrapper);

	}


	// Return the object with the publicly-callable functions
	return {
		init: init
	};


})(jQuery);