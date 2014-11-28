$(document).ready(function () {

	if (window.CKEDITOR) CKEDITOR.replace("body");

	if (typeof RELANG !== 'undefined') {
		$("textarea[name='body']").redactor({
		overlay: false,
		cleanup: true,
		fixed: true,
		minHeight: 400,
		// imageGetJson: site_url + 'admin/redactor/images',
		imageUpload: '/admin/redactor/upload_image',
		imageUploadErrorCallback: function(obj, json) { alert(json.error); },
		fileUpload: '/admin/redactor/upload_file',
		fileUploadErrorCallback: function(obj, json) { alert(json.error); }
	});
	}

	// Initialise the TinyMCE editor on the document content textarea
	/* var tmce = $("textarea");

	tmce.tinymce({
		script_url : '/scripts/tiny_mce/tiny_mce.js',
		theme : "advanced",
		plugins: "save,paste,table,advimage,imagemanager",
		// Theme options
		theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontsizeselect,|,forecolor,backcolor",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,table,link,insertimage,image,|,undo,redo,|,hr,removeformat,cleanup",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : false
	}).css("width", "100%"); */

	$('#news_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			title: {
				required: true
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});

	$("input[name=all_areas]").on("click", function(e) {
		if ($(this).is(":checked")) {
			$("input.pct").prop("checked", true);
		} else {
			$("input.pct").prop("checked", false);
		}
	});

	$("input.pct").on("click", function(e) {
		var all = $("input.pct").length;
		var selected = $("input.pct:checked").length;

		if (selected == all) {
			$("input[name='all_areas']").prop("checked", true);
		} else {
			$("input[name='all_areas']").prop("checked", false);
		}
	});

});
