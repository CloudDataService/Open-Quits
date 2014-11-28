// JavaScript Document
$(document).ready(function() {

	function get_graph(range)
	{
		$.ajax({
			url: '/service-providers/home/get_graph/' + range,
			success: function(url) {
				$('#graph').attr('src', url).fadeIn('fast');
			}
		});
	}

	get_graph('month');

	$('#range').change(function() {

		$('#graph').fadeOut('fast', function () {  get_graph($('#range').val()) });

	});

});
