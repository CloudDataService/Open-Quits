// JavaScript Document
$(document).ready(function () {

	$('.datepicker').datepicker({'maxDate' : new Date()});

	$(".js-quarters").on("change", function() {
		var q = $(this).find(":selected").data();
		$("input#date_from").val(q.from);
		$("input#date_to").val(q.to);
	});

});