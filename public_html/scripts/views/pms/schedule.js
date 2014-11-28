var schedule = (function($) {


	var current_sp_id = 0;
	var current_when = 0;

	var reschedule_a_id = null;


	var init = function() {

		// Respond to view changes - if requested view is schedule, show ourselves!
		$(document).on("view/switch", function(e, view) {
			if (view === "schedule") {
				show();
			} else {
				hide();
			}
		});


		// Listen for the event to trigger a schedule loading
		$(document).on("schedule/load", function(e, data) {
			load(data.sp_id, data.when);
		});


		$("#schedule").on("click", ".map-btn", function(e) {
			$(document).trigger("view/switch", "map");
		});


		$("#schedule").on("click", ".free", function(e) {
			var $cell = $(this);
			var data = $(this).data();

			$cell.text("Wait...");

			$.ajax({
				type: "post",
				data: data,
				url: "/pms/appointments/book/",
				success: function(res) {
					// Should have generated appointment ID. Go and set details
					if (res.status === 'ok') {
						var url = "/pms/appointments/set/" + res.a_id;
						if (res.reschedule) {
							url += "?reschedule=success";
						}
						window.location = url;
					} else {
						alert(res.msg);
						reload();
					}
					$cell.text("");
				},
				error: function() {
					alert('There was an error reserving this appointment time.');
					$cell.text("");
					reload();
				}
			});
			e.preventDefault();
		});

	}


	/**
	 * Load a schedule for a given service provider and for this week/next
	 */
	var load = function(sp_id, when) {

		// Store requested details
		current_sp_id = sp_id;
		current_when = when;

		var data = {};

		if (reschedule_a_id !== null) {
			data.action = "reschedule";
			data.a_id = reschedule_a_id;
		}

		// Make AJAX request to load the schedule page
		$.ajax({
			url: "/pms/service-providers/schedule/" + sp_id + "/" + when,
			data: data,
			cache: false,
			error: function(res) {
				$("#schedule").html(res);
				$(document).trigger("view/switch", "schedule");
			},
			success: function(res) {
				$("#schedule").html(res);
				$(document).trigger("view/switch", "schedule");
			}
		});
	}


	/**
	 * Reload the schedule via AJAX and update page
	 */
	var reload = function() {
		load_schedule(current_sp_id, current_when);
	}


	/**
	 * Store the appointment ID for rescheduling it
	 */
	var reschedule = function(a_id) {
		reschedule_a_id = a_id;
		$(document).trigger("view/switch", "schedule");
	}


	var hide = function() {
		$("#schedule").hide();
		$(".key-type-schedule").hide();
	}


	var show = function() {
		$("#schedule").show();
		$(".key-type-schedule").show();
	}





	return {
		init: init,
		load: load,
		reload: reload,
		reschedule: reschedule
	};


})(jQuery);
