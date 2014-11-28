$(document).ready(function() {

	$(".schedule").on("click", ".free", function(e) {
		var $cell = $(this);
		var data = $(this).data();

		$cell.text("Wait...");

		$.ajax({
			type: "post",
			data: data,
			url: "/service-providers/appointments/book/",
			success: function(res) {
				// Should have generated appointment ID. Go and set details
				if (res.status === 'ok') {
					var url = "/service-providers/appointments/set/" + res.a_id;
					if (res.reschedule) {
						url += "?reschedule=success";
					}
					window.location = url;
				} else {
					alert(res.msg);
					window.location.reload();
				}
				$cell.text("");
			},
			error: function() {
				alert('There was an error reserving this appointment time.');
				$cell.text("");
				window.location.reload();
			}
		});

		e.preventDefault();
	});

})
