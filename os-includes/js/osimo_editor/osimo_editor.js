/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/js/osimo_editor.js - Javascript for Osimo's BBCode editor
*	The class is included in the standard Osimo header, but
*	is *not* automatically instantiated; the theme developer must do it manually.
*	This is to allow for more flexibilty and more control over it.
*	--------------------------------------------------------------
*	The Osimo BBCode Editor requires jQuery >= 1.3.x. This is already included
*	with Osimo automatically, but if you are using this outside of
*	Osimo you will need to provide jQuery on your own.
*/

function OsimoEditor(elements,options){
	/* 
	 *	This should be the only thing you will have to edit
	 *	if not using this editor as a part of Osimo.
	 *	Enter in the path to the folder that contains the
	 *	osimo_editor files. This path *must* have a trailing
	 *	slash and is relative to the document root.
	 */
	this.editor_path = "os-includes/js/osimo_editor/";
	
	/*
	 *	You can change this value to any theme that you have
	 *	in the themes folder for the Osimo Editor. This is
	 *	simply the folder name that the theme files are in.
	 *	You can override this value with the options parameter.
	 */
	this.theme = "default";
	
	/* Do not edit the rest of the code below unless you know what you are doing! */
	this.majorVersion = "0";
	this.minorVersion = "5";
	this.statusVersion = "beta";
	this.releaseDate = "May 27, 2009 @ 9:45pm EDT";
	
	if(arguments.length > 0){
		if(arguments.length == 1){
			this.init(elements,null);
		}
		else{
			this.init(elements,options);
		}
	}
	else{
		return null;
	}
}

/*
 *	this.init(elements,options)
 *	Runs some initialization checks to make sure
 *	everything will run smoothly. This is automatically
 *	called by the constructor.
 *	This function also automatically starts the
 *	editor building process.
 *	---------------------------------------------------
 *	elements: array of elements (Strings) to turn into editors
 *	options: object with various customizations for the editor
 */
OsimoEditor.prototype.init = function(elements,options){
	/* Check to make sure we have jQuery >= 1.3.x */
	if(!this.check_jquery()){
		alert("Osimo BBCode Editor requires jQuery >= 1.3.x in order to function");
		return null;
	}
	
	/* Make sure the elements variable
	 * is either a string or an array object */
	if(typeof elements == 'string'){
		this.elements = new Array(1);
		this.elements[0] = elements;
	}
	else if($.isArray(elements)){
		this.elements = elements;
	}
	else{
		alert("Invalid elements specified for Osimo BBCode Editor");
		return null;
	}
	
	this.options = options;
	if(options.theme){
		this.theme = options.theme;
	}
	if(options.path){
		this.editor_path = options.path;
	}
	if(this.editor_path.substring(this.editor_path.length-1,this.editor_path.length) != "/"){
		this.editor_path += "/";
	}
	
	this.theme_path = this.editor_path + "themes/" + this.theme + "/";
	this.template = '';
	this.controls = new OsimoEditorControls();
	
	if(!this.filterElements()) return;
	 
	this.injectCSS();
	this.loadTheme(true);
}

/*
 *	this.filterElements()
 *	Runs through each of the elements provided at
 *	class instantiation time and removes all items
 *	from the elements array that are not on the page in
 *	order to improve efficiency and stop the theme loader
 *	ajax call from happening if there are no elements to
 *	transform on the page.
 */
OsimoEditor.prototype.filterElements = function(){
	var temp = new Array();
	$.each(this.elements,function(i,val){
		if($(val).length > 0){
			temp.push(val);
		}
	});
	
	if(temp.length == 0){ return false; }
	
	this.elements = temp;
	return this.elements.length;
}

/*
 *	this.loadTheme()
 *	Loads the theme via ajax through the theme_loader.php
 *	file. If this gets called multiple times, it will 
 *	just pull the data from the class variable once
 *	it has loaded.
 *	------------------------------------------------------------
 *	init: (boolean) Should we begin the editor creation process?
 */
OsimoEditor.prototype.loadTheme = function(init){
	if(this.template==''){
		var obj = this;
		var postData = {"theme":this.theme};
		$.ajax({
			type:'POST',
			url:obj.editor_path+"theme_loader.php",
			data:postData,
			success:function(data){
				obj.template = data;
				if(init){
					obj.buildEditor();
				}
			}
		});
	}
	else if(this.template != '' && init){
		this.buildEditor();
	}
	else return;
}

/*
 *	this.buildEditor()
 *	Begins the editor building process. All data
 *	is now being pulled from class variables.
 *	This function retrieves the contents of each
 *	element that will be turned into an editor
 *	and calls the function that constructs the editor and
 *	injects it into the DOM.
 */
OsimoEditor.prototype.buildEditor = function(){
	var curElement;
	var eleContents;
	for(var i=0;i<this.elements.length;i++){
		curElement = this.elements[i];
		if($(curElement).length == 0) continue;
		
		if((eleContents = this.getElementContents(curElement)) == null){
			eleContents = "";
		}
		this.constructEditor(curElement,eleContents);
	}
}

/*
 *	this.constructEditor(element,contents)
 *	Injects the editor into the DOM and replaces
 *	the old element with the new one.
 *	--------------------------------------------------------
 *	element: selector for the element we are working with
 *	contents: contents of the element we are editing, if any
 */
OsimoEditor.prototype.constructEditor = function(element,contents){
	var eleObj = $(element);
	var eleID = eleObj.attr('id');
	var eleClasses = eleObj.attr('class');
	var eleStyles = eleObj.attr('style');
	
	/* Begin DOM Injection */
	var inject = '<textarea';
	if(eleID!=null){
		inject += ' id="'+eleID+'"';
	}
	inject += ' class="osimo-editor-postbox';
	if(eleClasses!=null && eleClasses!=''){
		inject += ' '+eleClasses;
	}
	inject += '"';
	if(eleStyles!=null){
		inject += ' style="'+eleStyles+'"';
	}
	inject += '>'+contents+'</textarea>';
	var template = this.template.replace(/\{\*osimo_editor\*\}/,inject);
	
	eleObj.replaceWith(template);
	/* End DOM Injection */
	
	if(this.options.width){
		$('.osimo-editor').css({'width':this.options.width});
	}
	if(this.options.height){
		$(element).css({'height':this.options.height});
	}
	if(this.options.styles){
		$('.osimo-editor').css(this.options.styles);
	}
	
	this.controls.activateControls(element);
}

/*
 *	this.check_jquery()
 *	Utility function that checks for the presence of
 *	of jQuery and also makes sure that it is at least
 *	version >= 1.3.x
 */
OsimoEditor.prototype.check_jquery = function(){
	if($()==null){ return false; }
	
	var jVer = $().jquery.split('.');
	if(jVer[0]>=1 && jVer[1]>=3){
		return true;
	}
	else{
		return false;
	}
}

/*
 *	this.getElementContents(element)
 *	Retrieves the contents of an element before it is changed
 *	into a bbcode editor. This works for both textareas and
 *	normal div's. Using input elements other than textareas might
 *	produce strange results.
 *	---------------------------------------------------------------
 *	element: (String) name of the element whose contents are needed
 */
OsimoEditor.prototype.getElementContents = function(element){
	var eleObj = $(element);
	if(eleObj.attr('value') != null){
		return eleObj.attr('value');
	}
	else{
		return eleObj.html();
	}
}

/*
 *	this.injectCSS()
 *	Injects the stylesheet for the editor into the DOM so
 *	that theme developers only have to include the js file
 *	in order to have everything covered.
 */
OsimoEditor.prototype.injectCSS = function(){
	$('head').append('<link rel="stylesheet" href="'+this.theme_path+'osimo_editor.css" type="text/css" />');
}

/*	this.getContents(element)
 *	Retrieves the contents of an osimo bbcode editor.
 *	If no element is specified, we assume the first one
 *	in our saved elements list.
 */
OsimoEditor.prototype.getContents = function(element){
	if(arguments.length==1){
		if(this.elements && !$.inArray(element,this.elements)) return;
		
		return $(element).attr('value');
	}
	else if(arguments.length==0){
		return $(this.elements[0]).attr('value');
	}
	else return;
}

OsimoEditor.prototype.setContents = function(element,contents){
	if(arguments.length==2){
		if(this.elements && !$.inArray(element,this.elements)) return;
		
		return $(element).attr('value',contents);
	}
	else return;
}

/*
 *	This class takes care of all the editor controls and
 *	is a sub-class of OsimoEditor accessed through the 
 *	variable controls, i.e. this.controls.xxx
 */
function OsimoEditorControls(){

}

/*	this.controls.activateControls(element)
 *	This activates all the buttons/controls for the editor
 *	by referencing each button through a required class name.
 */
OsimoEditorControls.prototype.activateControls = function(element){
	var obj = this;
	/* Start Text Alignment */
	if($(".osimo-editor-align-left").length > 0){
		$.each($(".osimo-editor-align-left"),function(){
			$(this).bind("mouseup",function(){
				obj.alignText('left',element);
			});
		});
	}
	if($(".osimo-editor-align-center").length > 0){
		$.each($(".osimo-editor-align-center"),function(){
			$(this).bind("mouseup",function(){
				obj.alignText('center',element);
			});
		});
	}
	if($(".osimo-editor-align-right").length > 0){
		$.each($(".osimo-editor-align-right"),function(){
			$(this).bind("mouseup",function(){
				obj.alignText('right',element);
			});
		});
	}
	/* End Text Alignment */
	
	/* Start Text Styles */
	if($(".osimo-editor-text-bold").length > 0){
		$.each($(".osimo-editor-text-bold"),function(){
			$(this).bind("mouseup",function(){
				obj.textStyles('bold',element);
			});
		});
	}
	if($(".osimo-editor-text-italic").length > 0){
		$.each($(".osimo-editor-text-italic"),function(){
			$(this).bind("mouseup",function(){
				obj.textStyles('italic',element);
			});
		});
	}
	if($(".osimo-editor-text-underline").length > 0){
		$.each($(".osimo-editor-text-underline"),function(){
			$(this).bind("mouseup",function(){
				obj.textStyles('underline',element);
			});
		});
	}
	/* End Text Styles */
	
	/* Start Text Lists */
	if($(".osimo-editor-bullet-list").length > 0){
		$.each($(".osimo-editor-bullet-list"),function(){
			$(this).bind("mouseup",function(){
				obj.textLists('bullet',element);
			});
		});
	}
	
	/* Start Images */
	if($(".osimo-editor-image-add").length > 0){
		$.each($(".osimo-editor-image-add"),function(){
			$(this).bind("mouseup",function(){
				obj.imageAdd(element);
			});
		});
	}
	
	/* Start Quotes */
	if($(".osimo-editor-quote-user").length > 0){
		$.each($(".osimo-editor-quote-user"),function(){
			$(this).bind("mouseup",function(){
				obj.quoteUser(element);
			});
		});
	}
	
	/* Start No-Code Tag */
	if($(".osimo-editor-no-code").length > 0){
		$.each($(".osimo-editor-no-code"),function(){
			$(this).bind("mouseup",function(){
				obj.noCode(element);
			});
		});
	}
	
	/* Start Link Add */
	if($(".osimo-editor-link-add").length > 0){
		$.each($(".osimo-editor-link-add"),function(){
			$(this).bind("mouseup",function(){
				obj.linkAdd(element);
			});
		});
	}
	
	/* Start Email Add */
	if($(".osimo-editor-email-add").length > 0){
		$.each($(".osimo-editor-email-add"),function(){
			$(this).bind("mouseup",function(){
				obj.emailAdd(element);
			});
		});
	}
	
	/* Start Font Family */
	if($(".osimo-editor-font-family").length > 0){
		$.each($(".osimo-editor-font-family"),function(){
			$(this).bind("change",function(){
				obj.fontFamily(element);
			});
		});
	}
	
	/* Start Font Size */
	if($(".osimo-editor-font-size").length > 0){
		$.each($(".osimo-editor-font-size"),function(){
			$(this).bind("change",function(){
				obj.fontSize(element);
			});
		});
	}
	
	/* Start Color Picker */
	if($(".osimo-editor-color-picker").length > 0){
		$.each($(".osimo-editor-color-picker"),function(){
			$(this).bind("change",function(){
				obj.colorPicker(element);
			});
		});
	}
	
	/* Start Flickr image posting */
	if($(".osimo-editor-flickr-image").length > 0){
		$.each($(".osimo-editor-flickr-image"),function(){
			$(this).bind("mouseup",function(){
				obj.flickrImage(element);
			});
		});
	}
}

/*
 *	this.getTextSelection()
 *	Utility function that returns the text that is selected
 *	and is cross-browser compatible.
 */
OsimoEditorControls.prototype.getTextSelection = function(element){
	var textarea = document.getElementById($(element).attr('id'));
	if (window.getSelection) {
		var len = textarea.value.length;
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		var sel = textarea.value.substring(start, end);
		var result = {"textarea":textarea,"start":start,"end":end,"len":len,"sel":sel};
		return result;
	}
	else if (document.selection) {
		/* Damn you IE, why must you torture me so? */
		var range = document.selection.createRange();
		var stored_range = range.duplicate();
		stored_range.moveToElementText( textarea );
		stored_range.setEndPoint( 'EndToEnd', range );
		textarea.selectionStart = stored_range.text.length - range.text.length;
		textarea.selectionEnd = textarea.selectionStart + range.text.length;
		var result = {"textarea":textarea,"start":textarea.selectionStart,"end":textarea.selectionEnd,"len":textarea.value.length,"sel":range.text};
		return result;
	}
}

/*	this.controls.replaceText
 *	This does the actual text replacing in the editor.
 */
OsimoEditorControls.prototype.replaceText = function(result,element){
	result.textarea.value = result.textarea.value.substring(0,result.start) + result.replace + result.textarea.value.substring(result.end,result.len);
	$(element).focus();
}

/*	From here on, all the functions handle the bbcode
 *	equivalents of the buttons that were used in the editor.
 */
OsimoEditorControls.prototype.alignText = function(dir,element){
	var result = this.getTextSelection(element);
	if(dir=='left'){
		result.replace = '[left]' + result.sel + '[/left]';
	}
	if(dir=='center'){
		result.replace = '[center]' + result.sel + '[/center]';
	}
	if(dir=='right'){
		result.replace = '[right]' + result.sel + '[/right]';
	}
	
	this.replaceText(result,element);
}

OsimoEditorControls.prototype.textStyles = function(type,element){
	var result = this.getTextSelection(element);
	if(type=='bold'){
		result.replace = '[b]' + result.sel + '[/b]';
	}
	if(type=='italic'){
		result.replace = '[i]' + result.sel + '[/i]';
	}
	if(type=='underline'){
		result.replace = '[u]' + result.sel + '[/u]';
	}
	
	this.replaceText(result,element);
}

OsimoEditorControls.prototype.textLists = function(type,element){
	var result = this.getTextSelection(element);
	if(type=='bullet'){
		result.replace = "[list]\n[*]" + result.sel + "\n[/list]";
	}
	
	this.replaceText(result,element);
}

OsimoEditorControls.prototype.imageAdd = function(element){
	var result = this.getTextSelection(element);
	if(result.sel==''){
		var url = prompt("Enter the URL to the image.","http://");
		if(url==null || url=="") return;
		result.replace = "[img]" + url + "[/img]";
	}
	else{
		result.replace = "[img]" + result.sel + "[/img]";
	}
	
	this.replaceText(result,element);
}

OsimoEditorControls.prototype.quoteUser = function(element){
	var result = this.getTextSelection(element);
	var user = prompt("Enter the username of the person you are quoting (optional).","");
	if(user==null) return;
	if(user==''){
		result.replace = "[quote]" + result.sel + "[/quote]";
	}
	else{
		result.replace = "[quote=" + user + "]" + result.sel + "[/quote]";
	}
	
	this.replaceText(result,element);
}

OsimoEditorControls.prototype.noCode = function(element){
	var result = this.getTextSelection(element);
	result.replace = "[nocode]" + result.sel + "[/nocode]";
	this.replaceText(result,element);
}

OsimoEditorControls.prototype.linkAdd = function(element){
	var result = this.getTextSelection(element);
	if(result.sel==''){
		var url = prompt("Enter the URL you wish to link to.","http://");
		if(url==null || url=="") return;
		var content = prompt("Enter the text you want to turn into a link (optional).","");
		if(content==null) return;
		if(content==""){
			result.replace = "[url]" + url + "[/url]";
		}
		else{
			result.replace = "[url=" + url + "]" + content + "[/url]";
		}
	}
	else{
		var url = prompt("Enter the URL you wish to link to.","http://");
		if(url==null || url=="") return;
		result.replace = "[url=" + url + "]" + result.sel + "[/url]";
	}
	
	this.replaceText(result,element);
}

OsimoEditorControls.prototype.emailAdd = function(element){
	var result = this.getTextSelection(element);
	if(result.sel==''){
		var email = prompt("Enter the email address you wish to link to.","");
		if(email==null || email=="") return;
		var content = prompt("Enter the text you want to turn into a link (optional).","");
		if(content==null) return;
		if(content==""){
			result.replace = "[email]" + email + "[/email]";
		}
		else{
			result.replace = "[email=" + email + "]" + content + "[/email]";
		}
	}
	else{
		var email = prompt("Enter the email address you wish to link to.","");
		if(email==null || email=="") return;
		result.replace = "[email=" + email + "]" + result.sel + "[/email]";
	}
	
	this.replaceText(result,element);
}

OsimoEditorControls.prototype.fontFamily = function(element){
	var result = this.getTextSelection(element);
	var font = $(".osimo-editor-font-family").attr('value');
	if(font=="") return;
	result.replace = "[font=" + font + "]" + result.sel + "[/font]";
	this.replaceText(result,element);
}

OsimoEditorControls.prototype.fontSize = function(element){
	var result = this.getTextSelection(element);
	var size = $(".osimo-editor-font-size").attr('value');
	if(size=="") return;
	result.replace = "[size=" + size + "]" + result.sel + "[/size]";
	this.replaceText(result,element);
}

OsimoEditorControls.prototype.colorPicker = function(element){
	var result = this.getTextSelection(element);
	var color = $(".osimo-editor-color-picker").attr('value');
	if(color=="") return;
	result.replace = "[color=" + color + "]" + result.sel + "[/color]";
	this.replaceText(result,element);
}

OsimoEditorControls.prototype.flickrImage = function(element){
	var result = this.getTextSelection(element);
	if(result.sel==''){
		var photoID = prompt("Enter the Flickr photo ID for the image.\nThis is found when viewing the photo page in the URL.","");
		if(photoID==null || photoID=='') return;
		result.replace = "[flickr]"+photoID+"[/flickr]";
	}
	else{
		result.replace = "[flickr]"+result.sel+"[/flickr]";
	}
	
	this.replaceText(result,element);
}