//////////////////////////////////////////////////////////////////////////////////////////////////
/*  sLedit v1.0 - 3:35 PM 29/03/2007
//////////////////////////////////////////////////////////////////////////////////////////////////
	@author: kc@slajax.com
	@description: First version of sLedit. Makes all element of a class conditionally editable.
*/	
//////////////////////////////////////////////////////////////////////////////////////////////////
// init options - edit these for your purposes
//////////////////////////////////////////////////////////////////////////////////////////////////
window.addEvent('domready', function(){
    var edit = new sLedit('.inlineEdit');
});
//////////////////////////////////////////////////////////////////////////////////////////////////
// class - no editing below this line
//////////////////////////////////////////////////////////////////////////////////////////////////
	tinyMCE.init({
				mode : "",
				theme : "advanced",
				plugins : "",
				theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,|,image",
				theme_advanced_buttons2 : "",
				theme_advanced_buttons3 : "",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_path_location : "bottom",
				extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
				file_browser_callback : "fileBrowserCallBack",
				paste_use_dialog : false,
				theme_advanced_resizing : true,
				theme_advanced_resize_horizontal : false,
				apply_source_formatting : false
			});
			function fileBrowserCallBack(field_name, url, type, win) {
				var connector = "../../filemanager/browser.html?Connector=connectors/php/connector.php";
				var enableAutoTypeSelection = true;		
				var cType;
				tinymcpuk_field = field_name;
				tinymcpuk = win;
				switch (type) {
					case "image":
						cType = "Image";
						break;
					case "flash":
						cType = "Flash";
						break;
					case "file":
						cType = "File";
						break;
				}
				if (enableAutoTypeSelection && cType) {	connector += "&Type=" + cType;	}
				window.open(connector, "tinymcpuk", "modal,width=600,height=400");
				}
	
	
	var sLedit = new Class({
		initialize: function(sLclass) {
		//start editor
			
			$$(sLclass).each(function(el) {

					var hilighter = new Fx.Style(el, 'background-color', {duration: 150, wait: false});
					
						$(el).addEvent('mouseover', function() {
							hilighter.start('#ffffcc');
						});
						
						$(el).addEvent('mouseout', function() {
							hilighter.start('#ffffff');					
						});

						if($(el).getProperty('rel').indexOf('textarea') > -1){
												
								$(el).addEvent('click', function() {
																 								
									var rel = $(el).getProperty('rel');
									var params = parseQuery(rel.substr(5,999));
									
										$(el).style.display = 'none';
										var content = this.innerHTML;
										var container = new Element('div').injectAfter(el);

										//check params are suitable or set base attributes
										if(params['rows'] == undefined)	params['rows'] = '15';
										if(params['cols'] == undefined) params['cols'] = '65';

										var txtID = 'mce_'+$(el).getProperty('id');
											
										//textarea
										var textarea = new Element('textarea').injectInside(container);
										textarea.value = content;
										textarea.setProperty('class', 'mceEditor');
										textarea.setProperty('id', txtID);
										textarea.setStyle('position','relative');										
										textarea.setProperties({
											rows: params['rows'],
											cols: params['cols']
										});
													  
										setTextareaToTinyMCE(txtID);
														
										//save + cancel div
										var saveDiv = new Element('div').injectInside(container);
										saveDiv.setProperty('class', 'txtAreaSave');
																				
										//cancel
										var cancel = new Element('a').injectInside(saveDiv);
										cancel.setProperty('href', 'javascript:;');
										cancel.innerHTML = 'cancel';
										
										//seperator
										var span = new Element('span').injectAfter(cancel);
										span.innerHTML = ' - ';
										
										//save
										var save = new Element('a').injectInside(saveDiv);
										save.setProperty('href', 'javascript:;');
										save.innerHTML = 'save';
		
		
										//response
										var response = new Element('div').injectBefore(saveDiv);
										response.setStyles({
														//   'color': 'green',
														   'width': '600px'
														   });
										response.id = 'txtReponse';
										
										save.addEvent('click', function() {
											unsetTextareaToTinyMCE(txtID);
												$(el).style.display = 'block';
												$(el).innerHTML = textarea.value;

											//values
												var rel = $(el).getProperty('rel');
												var params = parseQuery(rel.substr(5,999));
												var module_type = params['module_type'];
												var type_sid = params['type_sid'];
												var sid = params['sid'];
																							
												var pars = 'module_type='+module_type+'&type_sid='+type_sid+'&sid='+sid+'&content='+escape(textarea.value);
												var url = '/ajax/update.php';
												
												new Ajax( url, {
															 method: 'post',
															 postBody: pars,
															 update: $('txtReponse'),
															 onComplete: function(){   
															 		var responseFade = new Fx.Style('txtReponse', 'opacity', {
																										duration:3000, 
																										onComplete:function(){
																											$('txtReponse').remove();
																										}
																									});
																	responseFade.start.pass([1,0],responseFade).delay(2000);
															 	}
															 }
															).request(); 								
										
												textarea.remove();
												this.remove();
												cancel.remove();
												span.remove();
												saveDiv.remove();
										});
										
										cancel.addEvent('click', function() {
										unsetTextareaToTinyMCE(txtID);
											$(el).style.display = 'block';
											textarea.remove();
											this.remove();
											save.remove();
											span.remove();
											saveDiv.remove();
										});	

		
									});
						}
						if($(el).getProperty('rel') == 'input'){
								$(el).addEvent('click', function() {
										$(el).style.display = 'none';

										var content = this.innerHTML;
										var container = this.parentNode;
//										alert(container.innerHTML);
										
										//input
										var textarea = new Element('input').injectInside(container);
										textarea.value = content;
										textarea.setProperty('size',5);

										//save + cancel div
										var saveDiv = new Element('span').injectInside(container);
										saveDiv.setProperty('class', 'inputSave');
										
										//cancel
										var cancel = new Element('a').injectInside(saveDiv);
										cancel.setProperty('href', 'javascript:;');
										cancel.innerHTML = 'x';
										
										//seperator
										var span = new Element('span').injectAfter(cancel);
										span.innerHTML = ' - ';
										
										//save
										var save = new Element('a').injectInside(saveDiv);
										save.setProperty('href', 'javascript:;');
										save.innerHTML = 's';

										
										save.addEvent('click', function() {
											$(el).style.display = 'block';
											$(el).innerHTML = textarea.value;
/* new Ajax() */											
											textarea.remove();
											this.remove();
											cancel.remove();
											span.remove();
											saveDiv.remove();
										});
										cancel.addEvent('click', function() {
											$(el).style.display = 'block';
											textarea.remove();
											this.remove();
											save.remove();
											span.remove();
											saveDiv.remove();
										});		
									});
						}
						if($(el).getProperty('rel').indexOf('image') > -1){
							
							var rel = $(el).getProperty('rel');
							var params = parseQuery(rel.substr(5,999));
							$(el).style.cursor = 'pointer';
							if( $(el).getParent().getParent().href ) $(el).getParent().getParent().href = '#';
							if( $(el).getParent().href ) $(el).getParent().href = '#';
							
								$(el).addEvent('click', function() {
	
										var imgForm = new Element('form').setProperties({
											'action': '/ajax/update.php',
											'method': 'post',
											'id': 'newImageForm',
											'enctype':'multipart/form-data'
										});
										imgForm.setStyles({ 'text-align':'left' });
										imgForm.innerHTML = '<img src="'+$(el).src+'" alt="'+$(el).alt+'"/><br /><br />'
															+'<input type="hidden" id="module_type" name="module_type" value="skin_variable"/>'
															+'<input type="hidden" id="type_sid" name="type_sid" value="'+params["type_sid"]+'"/>'
															+'<input type="hidden" id="sid" name="sid" value="'+params["sid"]+'"/>'
															+'Alt Tag:<br /><input type="input" class="textField" id="alt" name="alt" value="'+$(el).alt+'" style="width:375px;"/><br /><br />'
															+'File to Upload:<br /><input type="file" class="textField" id="userimage" name="userimage"/><br /><br />';
										
										var boxHtml = new MooPrompt('Upload a New Image', imgForm, {
											buttons: 2,
											button1: 'Submit',
											button2: 'Cancel',
											width: 500,
											height: 400,
											onButton1: function() {
												console.log($('userimage').value.length > 0)
												if( $('userimage').value.length < 1 || 
													$('userimage').value.length > 0 && 
													$('userimage').value.indexOf('.jpg') > -1)	
													$('newImageForm').submit();
													else
														alert('Please only upload .jpg files');
											}
								});
							 });
						}
					});	
		
			}
	});
//////////////////////////////////////////////////////////////////////////	
// TinyMCE helper functions
//////////////////////////////////////////////////////////////////////////
bTextareaWasTinyfied = false; //this should be global, could be stored in a cookie...
	function setTextareaToTinyMCE(sEditorID) {
		var oEditor = document.getElementById(sEditorID);
		if(oEditor && !bTextareaWasTinyfied) {
			tinyMCE.execCommand('mceAddControl', true, sEditorID);
			bTextareaWasTinyfied = true;
		}
		return;
	}
	function unsetTextareaToTinyMCE(sEditorID) {
		var oEditor = document.getElementById(sEditorID);
		if(oEditor && bTextareaWasTinyfied) {
			tinyMCE.execCommand('mceRemoveControl', true, sEditorID);
			bTextareaWasTinyfied = false;
		}
		return;
	}
//////////////////////////////////////////////////////////////////////////	
// parseQuery code borrowed from ibox borrowed from thickbox, Thanks Cody!
// retrieve rel attributes with cols=x&rows=x
//////////////////////////////////////////////////////////////////////////
		parseQuery = function(query) {
		   var Params = new Object ();
		   if (!query) return Params; 
		   var Pairs = query.split(/[;&]/);
		   for ( var i = 0; i < Pairs.length; i++ ) {
			  var KeyVal = Pairs[i].split('=');
			  if ( ! KeyVal || KeyVal.length != 2 ) continue;
			  var key = unescape( KeyVal[0] );
			  var val = unescape( KeyVal[1] );
			  val = val.replace(/\+/g, ' ');
			  Params[key] = val;
		   }		   
		   return Params;
		}
