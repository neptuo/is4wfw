/**
 *
 *  @author  mara@neptuo.com
 *  @date    2018/02/09
 *
 */
function initTiny(taId, language, isEditable) {
	var config = {
		selector: 'textarea#' + taId,
		theme: 'modern',
		plugins: 'code print preview searchreplace autolink visualblocks visualchars fullscreen image link media table hr pagebreak nonbreaking anchor lists textcolor imagetools contextmenu colorpicker help',
		toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link image | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat code',
		image_advtab: true,
		suffix: '.min',
		convert_urls: false,
		content_css: [
			"/wysiwyg-styles.php"
		]
	};

	if (isEditable === false) {
		config.readonly = true;
	}

	if (typeof(language) != 'undefined' && language != null) {
		config.language = language;
	}

	tinymce.init(config);

	// var config = {
	// 	// General options
	// 	mode : "none",
	// 	theme : "advanced",
	// 	plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	// 	// Theme options
	// 	theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
	// 	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,forecolor,backcolor",
	// 	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl",
	// 	//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
	// 	theme_advanced_toolbar_location : "top",
	// 	theme_advanced_toolbar_align : "left",
	// 	theme_advanced_statusbar_location : "bottom",
	// 	theme_advanced_resizing : false,

	// 	// Example content CSS (should be your site CSS)
	// 	content_css : "/wysiwyg-styles.php",

	// 	// Drop lists for link/image/media/template dialogs
	// 	template_external_list_url : "lists/template_list.js",
	// 	external_link_list_url : "lists/link_list.js",
	// 	external_image_list_url : "lists/image_list.js",
	// 	media_external_list_url : "lists/media_list.js",
	// };
}