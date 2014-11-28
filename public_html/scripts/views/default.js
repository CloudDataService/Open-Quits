// JavaScript Document
var milliseconds = 900000;

var count_down_timeout;

function removeModal() {
	$('div.advisor_id_modal').fadeOut('normal', function() {
		$('div.advisor_id_modal').remove();
	});
	$('div.modal_bg').fadeOut('normal', function() {
		$('div.modal_bg').remove();
	});
}

function advisor_id_modal(email, fname, sname, advisor_code) {
	if (email === undefined) {
		email = '';
	}
	if (fname === undefined) {
		fname = '';
	}
	if (sname === undefined) {
		sname = '';
	}
	if (advisor_code === undefined) {
		advisor_code = '';
	}

	if ($('.advisor_id_modal').length > 0) {
		return "Already loaded...";
	}

	$('body').append('<div class="modal_bg">&nbsp;</div>');
	var div_bg = $('div.modal_bg');
	div_bg.css("width", ($(window).width() + 40) + "px");
	div_bg.css("height", ($(window).height() + 40) + "px");
	div_bg.bind('mousewheel DOMMouseScroll', function (e) {
		var e0 = e.originalEvent,
			delta = e0.wheelDelta || -e0.detail;

		this.scrollTop += (delta < 0 ? 1 : -1) * 30;
		e.preventDefault();
	});
	div_bg.click(function () {
		removeModal();
	});

	$('body').append('<div class="advisor_id_modal"><h2>Please confirm your Advisor Code</h2><p style="padding-top: 10px; margin-bottom: -10px;">Your details are below, if the Advisor Code is empty or invalid, please change it to the correct Code.</p><br><br><table><tr><th><label for="email">Email Address</label></th><td><input type="text" name="email" id="email" value="' + email + '" disabled="disabled" class="text"></td></tr><tr><th><label for="fname">First Name</label></th><td><input type="text" name="fname" id="fname" value="' + fname + '" disabled="disabled" class="text" style="text-transform:capitalize;"></td></tr><tr><th><label for="sname">Surname</label></th><td><input type="text" name="sname" id="sname" value="' + sname + '" disabled="disabled" class="text" style="text-transform:capitalize;"></td></tr><tr><th><label for="advisor_code">Advisor Code</label></th><td><input type="text" name="advisor_code" id="advisor_code" value="' + advisor_code + '" class="text" style="text-transform:uppercase;"></td></tr></table><br><div class="buttons"><a href="#" id="cancel"><img src="/img/btn/cancel.png" alt="Cancel" /></a> <a href="#" id="update_details"><img src="/img/btn/save.png" alt="Save" /></a></div></div>');

	var div = $('div.advisor_id_modal');
	div.css("z-index", "99999");
	div.css("position", "fixed");
	div.css("top", ($(window).height() - div.height()) / 2 + $(window).scrollTop() + "px");
	div.css("left", ($(window).width() - div.width()) / 2 + $(window).scrollLeft() + "px");

	$.ajax({
		url: '/ajax/get_advisor_id',
		type: 'GET',
		data: {
			term: fname + ' ' + sname
		},
		success: function (data) {
			if (typeof data.code !== undefined) {
				$('#advisor_code').val(data.code);
			}
		}
	});

	div.find('a').click(function () {
		if ($(this).attr('id') === 'update_details') {
			var advisor_code = $('#advisor_code').val(),
				length = advisor_code.length;

			if (length < 1 || length > 7) {
				$('#advisor_code').css('border', '2px solid red');
				alert('Please enter a valid Advisor Code');

				return false;
			}

			$.ajax({
				url: '/ajax/set_advisor_id',
				type: 'POST',
				data: {
					advisor_code: advisor_code
				},
				success: function (data) {
					removeModal();
					alert('Thanks for submitting your Advisor Code');
				}
			});
		} else {
			removeModal();
		}

		return false;
	});

}

function session_timer() {
	if ($('.timeout').length > 0) {
		return "Already loaded...";
	}

	$('body').append('<div class="modal_bg">&nbsp;</div>');
	var div_bg = $('div.modal_bg');
	div_bg.css("width", ($(window).width() + 40) + "px");
	div_bg.css("height", ($(window).height() + 40) + "px");
	div_bg.bind('mousewheel DOMMouseScroll', function (e) {
		var e0 = e.originalEvent,
			delta = e0.wheelDelta || -e0.detail;

		this.scrollTop += (delta < 0 ? 1 : -1) * 30;
		e.preventDefault();
	});

	$('body').append('<div class="timeout"><h2>Your session is about to time out.</h2><p>You have been inactive for 15 minutes. If you would like to continue working please click "Continue" otherwise you will be logged out in <span id="seconds">11</span> seconds.</p> <div class="continue"><a href="#" id="persist_session"><img src="/img/btn/continue.png" alt="Continue" /></a></div></div>');

	var div = $('div.timeout');
	div.css("z-index", 99999);
	div.css("position", "absolute");
	div.css("top", ($(window).height() - div.height()) / 2 + $(window).scrollTop() + "px");
	div.css("left", ($(window).width() - div.width()) / 2 + $(window).scrollLeft() + "px");

	count_down();

	$('a#persist_session').click(function () {
		clearTimeout(count_down_timeout);

		$.ajax({
			url: '/persist',
			success: function (data) {
				$('div.timeout').fadeOut('normal', function () {
					$('div.timeout').remove();
				});
				$('div.modal_bg').fadeOut('normal', function () {
					$('div.modal_bg').remove();
				});
			}
		});

		setTimeout("session_timer()", milliseconds);

		return false;
	});
}

function count_down() {
	var second = parseInt($('span#seconds').text(), 10);

	if (second > 0) {
		$('span#seconds').text(second - 1);

		count_down_timeout = setTimeout("count_down()", 1000);
	} else {
		if (window.AS) {
			// Do autosave if present and THEN logout
			AS.save(function () {
				window.location = '/logout?timeout=1';
			});
		} else {
			window.location = '/logout?timeout=1';
		}
	}
}

$(document).ready(function () {
	// advisor_id_modal("foo@bar.com", "Kathleen", "Waugh");

	$.ajaxSetup({
		cache: false
	});

	if ($('div.action').length) {
		$('div.action').fadeIn();
		setTimeout(function () {
			$('div.action').fadeOut('slow');
		}, 2500);
	}

	$.datepicker.setDefaults({
		dateFormat: "dd/mm/yy",
		changeMonth: false,
		changeYear: false
	});

	var timeout;

	$('li.has_more').hover(
		function () {
			$('ul.sub_nav').hide();

			clearTimeout(timeout);

			$(this).children('ul.sub_nav').css({
				'display': 'block'
			});
		},
		function () {
			var $this = $(this);

			timeout = setTimeout(function () {
				$this.children('ul.sub_nav').css({
					'display': 'none'
				});
			}, 500);
		}
	);

	$('a.action, input.action').click(function () {
		return confirm($(this).attr('title'));
	});

	$('tr.row').hover(
		function () {
			$(this).addClass('hover');
		},
		function () {
			$(this).removeClass('hover');
		}
	);

	$('tr.row').click(function () {
		if (!$(this).hasClass('no_click')) {
			if ($(this).children('td').children('a').attr('target') == "_blank") {
				var w = window.open($(this).children('td').children('a').attr('href'), '__blank', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=970,height=800');
				w.focus();
				return false;
			} else {
				window.location = $(this).children('td').children('a').attr('href');
			}
		}
	});

	$('a.window').click(function () {
		var w = window.open($(this).attr('href'), '__blank', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=970,height=800');
		w.focus();
		return false;
	});

	$('a.back').click(function () {
		history.go(-1);
		return false;
	});

	// Attach events for tabs
	$("dl.htabs > dd > a").click(function (e) {
		var location = $(this).attr("href");

		if (location.charAt(0) == "#") {
			e.preventDefault();

			$(this).closest("dl").find("a.selected").removeClass("selected");
			$(this).addClass("selected");
			$(location + "Tab").closest(".htabs-content").children("li").hide();
			$(location + "Tab").show();

			$("body").triggerHandler({
				type: "tab.selected",
				tab: location
			});
		}
	});

	setTimeout("session_timer()", milliseconds);
});
