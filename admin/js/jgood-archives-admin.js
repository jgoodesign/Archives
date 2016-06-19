(function( $ ) {
	'use strict';

	/**
	 * All of the code for your Dashboard-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

	 // toggle enable switch if clicked or initialized
	function switchToggle(object){
		var switchText = "<span class='jgood-options-switch-text'>Disabled</span>";

		if(object.prop('checked')){
			switchText = "<span class='jgood-options-switch-text'>Enabled</span>";
		}

		object.parent().siblings('span').remove();
		object.parent().after(switchText);
	}

	$( window ).load(function() {
		$(".jgood-options-switch").each(function() {
			switchToggle($(this));
		});

		$(".jgood-options-switch").click(function() {
			switchToggle($(this));
		});

		// add option function
		$(document.body).on("click", ".jgood_archives_option_group .jgood_archives_option_add", function() {
			var currentGroup = $(this).parent().children(".jgood_archives_option_sub_group").last();

			var clonedGroup = $(this).parent().children(".jgood_archives_option_sub_group").last().clone();
			var clonedContent = clonedGroup.html();

			var regex = /(jgood_archives.{0,1}\w*-{0,1}\w*)(\[{0,1})(\d)(\]{0,1})/g;

			var currentCount = $(this).parent().children(".jgood_archives_option_sub_group").length;

			var newContent = clonedContent.replace(regex, function(fullMatch, a, b, c, d) {
				return a+b+currentCount+d;
			});

			clonedGroup.html(newContent);

			clonedGroup.show();

			currentGroup.after( clonedGroup );

		});

		// remove option function
		$(document.body).on("click", ".jgood_archives_option_sub_group .jgood_archives_option_remove", function() {
			var currentGroup = $(this).parent();

			var currentCount = currentGroup.parent().children(".jgood_archives_option_sub_group:visible").length;
			
			currentGroup.children("select").val('');
			
			if(currentCount > 1){ currentGroup.hide(); }
		});

		$(document.body).on("click", ".jgood_archives_rule_group .jgood_archives_rule_add", function() {
			var currentGroup = $(this).parent().parent();

			var clonedGroup = currentGroup.clone();

			var clonedContent = clonedGroup.html();

			var regex = /(jgood_archives_rule_text|jgood_archives_rule_text_)(\[{0,1})(\d)(\]{0,1})/g;
			var regexB = /(jgood_archives_rule_sidebar|jgood_archives_rule_sidebar_)(\[{0,1})(\d)(\]{0,1})/g;

			var currentCount = currentGroup.parent().children(".jgood_archives_rule_group").length;

			var newContent = clonedContent.replace(regex, function(fullMatch, a, b, c, d) {
				return a+b+currentCount+d;
			});

			newContent = newContent.replace(regexB, function(fullMatch, a, b, c, d) {
				return a+b+currentCount+d;
			});

			clonedGroup.html(newContent);

			console.log(clonedGroup);

			clonedGroup.children("td").children(".jgood_archives_option_rule_content").children(".jgood_archives_rule_text").val('');
			clonedGroup.children("td").children(".jgood_archives_option_rule_content").children(".jgood_archives_rule_select").val('');

			currentGroup.after( clonedGroup );
		});

		$(document.body).on("click", ".jgood_archives_rule_group .jgood_archives_rule_remove", function() {
			var currentGroup = $(this).parent().parent().parent();

			currentGroup.children("td").children(".jgood_archives_option_rule_content").children(".jgood_archives_rule_text").val('');
			currentGroup.children("td").children(".jgood_archives_option_rule_content").children(".jgood_archives_rule_select").val('');

			var currentCount = currentGroup.parent().children(".jgood_archives_rule_group:visible").length;

			if(currentCount > 1){ currentGroup.hide(); }
		});


		// Widget stuff

		// when type is changed, change specific option drop down
		$(document.body).on("change", ".jgood_archives_widget_item .jgood_widget_option_type", function() {
			var currentOption = $(this).attr("id");
			var currentVal = $(this).val();

			var selectChange = currentOption.replace("jgood_widget_option", "jgood_widget_option_"+currentVal);

			$("#"+currentOption+" ~ .jgood_widget_option_value").each(function( index ) {
				$( this ).removeClass("active");
				$( this ).val();
			});

			$("#"+selectChange).addClass("active");
		});

		$(document.body).on("click", ".jgood_archives_widget_item .jgood_widget_button", function() {
			var currentGroup = $(this).parent();

			var clonedGroup = currentGroup.clone();
			var clonedContent = clonedGroup.html();

			var regex = /(\[jgood_widget_option.{0,1}\w*.{0,1}\w*\]|jgood_widget_option_\w*.{0,1}\w*\W*)(\[{0,1})(\d)(\]{0,1})/g;

			var currentCount = currentGroup.parent().children(".jgood_widget_option_row").length;

			var newContent = clonedContent.replace(regex, function(fullMatch, a, b, c, d) {
				return a+b+currentCount+d;
			});

			clonedGroup.html(newContent);

			currentGroup.after( clonedGroup );

			clonedGroup.children(".jgood_widget_option_type").prop("selectedIndex", 0).change();
		});

		$(document.body).on("click", ".jgood_archives_widget_item .jgood_widget_button_remove", function() {
			var currentGroup = $(this).parent();

			currentGroup.children("select").each(function(){
				$(this).val('');
			});

			var currentCount = currentGroup.parent().children(".jgood_widget_option_row:visible").length;

			if(currentCount > 1){ currentGroup.hide(); }
		});

	});

})( jQuery );
