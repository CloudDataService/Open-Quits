// JavaScript Document
$(document).ready(function () {

	$('.schemas').change(function() {
		$(this).parent('form').submit();
	});

	$(".sortable").sortable({axis: 'y'});

	$('.field_add').click(function () {

		var type = $(this).attr('rel');

		var field_name = $('#' + type + '_field_name :selected');

		if(field_name.val())
		{
			$('ul#' + type + '_sortable').append('<li><div><input type="hidden" name="fields[' + field_name.val() + ']" value="' + field_name.text() + '" />' + field_name.text() + '<a href="#"><img src="/img/icons/cross.png" alt="Delete" /></a></div></li>');
		}

		sortable_bind();

		return false;
	});

	function sortable_bind() {

		$('.sortable li').hover(
			function () {
				$(this).css({'cursor' : 'move'});
			},
			function () {

			}
		);

		$('.sortable li div a').click(function () {

			var li = $(this).parent().parent();

			li.fadeOut(400, function () { li.remove() });

			return false;
		});

	}

	sortable_bind();

});
