get_page_count = function() {

	$.ajax({
		type: "post",
		cache: false,
		data: { mmd_content: $("textarea#mmd_content").html() },
		url: "/service-providers/mail-merge/get_page_count/",
		success: function(res) {
			$("span#page_count").text(res.page_count);
			$("dd#page_count_dd").show();
		},
		error: function(err) {
			$("dd#page_count_dd").hide();
		}
	});

}


$(document).ready(function() {


	// Initialise the TinyMCE editor on the document content textarea
	var tmce = $("textarea[name='mmd_content']");

	tmce.tinymce({
		script_url : '/scripts/tiny_mce/tiny_mce.js',
		theme : "advanced",
		plugins: "save,paste,table",
		// Theme options
		theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontsizeselect,|,forecolor,backcolor",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,table,|,undo,redo,|,hr,removeformat,cleanup",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : false
	}).css("width", "600px");


	// Clicking on tags
	$("p.mail_merge_tags").on("click", "a", function(e) {
		e.preventDefault();
		var content = "[" + $(this).attr("rel") + "]";
		tmce.tinymce().execCommand('mceInsertContent', false, content);
	});


	// Form validation
	$('#mail_merge_document_form').validate({
		success: function(label) {
			label.addClass("valid").text("");
		},
		rules: {
			mmd_title: {
				required: true,
				minlength: 1,
				maxlength: 128
			}
		},
		errorPlacement: function(error, element) {
			error.appendTo(element.parent("td").next(".e"));
		}
	});


	// Preview. Listen to the tab.selected event to be fired.
	$("body").on("tab.selected", function(o) {
		var preview = $("li#previewTab");

		// If preview tab is selected...
		if (o.tab == "#preview") {
			// Update the container
			var content = $("textarea#mmd_content").html();
			$.ajax({
				type: "post",
				cache: false,
				data: { mmd_content: content },
				url: "/service-providers/mail-merge/preview/",
				success: function(res) {
					preview.html(res);
				},
				error: function(err) {
					preview.html("Error: " + err);
				}
			});
			get_page_count();
		}
	});

	$("li#previewTab").css("min-height", $("li#setTab").css("height"));

});
