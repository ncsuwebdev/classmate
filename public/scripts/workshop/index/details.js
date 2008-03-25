var editing = false;
var myStickyWin = null;

window.addEvent('domready', function() {

    var sitePrefix = $('sitePrefix').value;
    var updateEl = $('response');
    
    if ($('uploadForm')) {
	   var multiupload = new MultiUpload($('uploadForm').uploadDocuments, 10, '[{id}]', true, true );
	}
    
    var editorClass = '.inlineEdit';
    
    $$(editorClass).each(function(el) {
        el.addEvent('click', function() {
            var editor = new myIEdit(el, editorClass);
        });
    });
     
	$$('.document .delete').each(function(item){
	 	    
	    item.addEvent('click', function(e) {
	        e = new Event(e).stop();	        

            if (editing) {
                alert('No deletes can be made during edit mode');
                return;
            }
            
            if (confirm('Are you sure you want to delete this document?')) {
                var documentId = item.getParent().id.replace(/^[^_]*\_/i, '');                  
                        
				new Ajax(sitePrefix + '/workshop/index/delete-document/', {
				    method: 'post',
				    postBody: 'documentId=' + documentId,
				    update: updateEl,
				    onComplete: function(){
				        var responseFade = new Fx.Style(updateEl.getProperty('id'), 'opacity', {
				            duration:2000
				        });
				        responseFade.start.pass([1,0],responseFade).delay(1000);
				    }
		       }).request();
				        
               item.getParent().remove(); 
           }       
	    });
	});
	
    $$('.linkPackage .delete').each(function(item){
            
        item.addEvent('click', function(e) {
            e = new Event(e).stop();            

            if (editing) {
                alert('No deletes can be made during edit mode');
                return;
            }
            
            if (confirm('Are you sure you want to delete this online resource?')) {
                var workshopLinkId = item.getParent().id.replace(/^[^_]*\_/i, '');                        
                        
                new Ajax( sitePrefix + '/workshop/index/delete-link/', {
                    method: 'post',
                    postBody: 'workshopLinkId=' + workshopLinkId,
                    update: updateEl,
                    onComplete: function(){
                        var responseFade = new Fx.Style(updateEl.getProperty('id'), 'opacity', {
                            duration:2000
                        });
                        responseFade.start.pass([1,0],responseFade).delay(1000);
                    }
               }).request();
                        
               item.getParent().remove(); 
                                       
           }       
        });
    });	
	
	if ($('addDocumentForm')) {
		$('addDocument').addEvent('click', function(e) {
		   e = new Event(e);
		   
		   toggleDocForm();
		   
		   e.stop();	   
		});
    }
    
    if ($('addEvent')) {
        $('addEvent').addEvent('click', function(e) {
            
            location.href=$E('a', $('addEvent')).href;
        });
    }
    
    $$('.event').each(function(el) {
        el.addEvent('click', function() {
            if ($E('a', el)) {
                location.href=$E('a', el).href;
            }
        });
    });
    
    if ($('manageWorkshop')) {
	    // we need to store the html in a variable and then remove the whole thing from the
	    // dom so that it doesn't interfere when we make it the content in the modal popup
	    eventPopupHtml = $('createEventPopup').innerHTML;
	    $('createEventPopup').remove();
	        
	    $('manageWorkshop').addEvent('click', function(e) {
	    
	        myStickyWin = new StickyWinModal({
	            modalOptions: {
	               hideOnClick: false
	            },
	            onDisplay: initEventPopup,
	            content: stickyWinHTML('Manage Workshop Options', eventPopupHtml, {
	                width: '500px',
	                buttons: [
	                    {
	                        text: 'Cancel', 
	                        onClick: function() {
	                           myStickyWin.destroy();
	                        }
	                    },
	                    {
	                        text: 'Save', 
	                        onClick: function(e) {
	                            $('workshopOptionForm').submit();
	                        }
	                    }
	                 ]
	            })
	        });
	    });  
	}  
	
    if ($('links')) {

        var sortList = new Sortables('links', {
            handles: $$('#links div.order'),
            onStart: function(element) {
                $$('#links div.link').each(function(el){
                    if (el.hasClass('activeDrag')) {
                        el.removeClass('activeDrag');
                    }
                });

                $$('.delete').each(function(el) {
                    el.style.display = 'none';
                });
                
                $$('.inlineEdit').each(function(el) {
                    el.style.display = 'none';
                });
                
                
                element.addClass('activeDrag');
            },
            onComplete: function(element){
                $$('#links div.link').each(function(el,i){

                    if (el.hasClass('activeDrag')) {
                        el.removeClass('activeDrag');
                    }
                })
                
                $$('.delete').each(function(el) {
                    el.style.display = '';
                });
                
                $$('.inlineEdit').each(function(el) {
                    el.style.display = '';
                });

                var queryString = Object.toQueryString({workshopId: $('workshopId').value, order: sortList.serialize()})

                new Ajax($('sortUrl').innerHTML, {
                    method: 'post',
                    postBody: queryString,
                    update: updateEl,
                    onComplete: function(){
                        var responseFade = new Fx.Style(updateEl.getProperty('id'), 'opacity', {
                            duration:2000
                        });
                        responseFade.start.pass([1,0],responseFade).delay(1000);
                    }
                }).request();
            },

            ghost: true
        });
        $$('#links div.order').each(function(el){
            el.disableSelection();
        });
    }
	
});

Element.extend({
    disableSelection: function(){
        if (window.ie) this.onselectstart = function(){ return false };
        this.style.MozUserSelect = "none";
        return this;
    },

    removeChildren: function() {
        while (this.lastChild) this.removeChild(this.lastChild);
    }

});

Sortables.implement({

    serialize: function(){
        var serial = [];
        this.list.getChildren().each(function(el, i){
            serial[i] = el.id;
        });
        return serial;
    }
});


function toggleDocForm() {
    var docForm = $('addDocumentForm');
    var button  = $('addDocument');
    
    if (docForm.style.display == 'none' || docForm.style.display == '') {
        button.style.display = 'none';
        docForm.style.display = 'block';
    } else {
        docForm.style.display = 'none';
        button.style.display = 'block';
    }
}

var myIEdit = iEdit.extend({
    editClass: '',
    
    hidden: new Array(),
    
    initialize: function(el, editClass) {
        var hideThese = new Array();
        $$(editClass).each(function (hide) {
            if (hide.style.display != 'none') {
                hide.style.display = 'none';
                hideThese[hideThese.length] = hide;
            }
        });
        
        $$('.delete').each(function (del) {
            del.style.display = 'none';
        }); 
        
        this.hidden = hideThese;
        this.editClass = editClass;
        this.parent(el);
        editing = true;
    },
    
    save: function(modifiedEl) {
        if (modifiedEl.name == 'addLinkForm') {
            var form = $('newLinkForm');
            
            var hidden = new Element('input');
            hidden.name = 'workshopId';
            hidden.value = $('workshopId').value;
            hidden.type = 'hidden';
            
            hidden.injectInside(form);

            form.submit();
        } else {
            this.parent(modifiedEl);
        }
    },
    
    cleanup: function() {
        this.hidden.each(function (show) {
            show.style.display = '';
        });
            
        this.parent();
        editing = false;
                
        if (this.target.innerHTML == '') {
            this.target.innerHTML = '&nbsp;';
        }
        
        $$('.delete').each(function (del) {
            del.style.display = '';
        });        
    }
});

function initEventPopup()
{
    $$('.closeButton').each(function(el) {
        el.remove();
    });
    
    $('workshopCategoryId').setStyle('visibility', 'visible');
    $('workshopCategoryId').setStyle('opacity', '100');
    $('workshopCategoryId').setStyle('width', '225');
    
    $('editorList').setStyle('visibility', 'visible');
    $('editorList').setStyle('opacity', '100');
    $('editorList').setStyle('width', '200'); 
    
    var tmpListBox = $('editorList');
    for (var i=0; i < tmpListBox.options.length; i++) {
        if (tmpListBox[i].selected) {
            
            var tmpBox = new Element('div');
            tmpBox.title = tmpListBox.options[i].value;
            tmpBox.addClass('editorName');
            
            var hidden = new Element('input');
            hidden.type = 'hidden';
            hidden.name = 'editor[]';
            hidden.value = tmpListBox.options[i].value;
            
            var tmpLeft = new Element('p');
            tmpLeft.addClass('left');
                                    
            var tmpRight = new Element('p');
            tmpRight.addClass('right');
            
            var tmpCloseBtn = new Element('p');
            tmpCloseBtn.innerHTML = "&nbsp;";
            tmpCloseBtn.title = i;
            tmpCloseBtn.addClass('closeBtn');
            
            tmpCloseBtn.addEvent('click', function(e) {
                this.parentNode.remove();
                
                if ($('editors').innerHTML == "") {
                    $('editors').innerHTML = "None Added";
                }
            });
            
            var tmpP = new Element('a');
            tmpP.innerHTML = tmpListBox.options[i].label;
            tmpP.addClass('content');
            
            tmpBox.adopt(hidden);
            tmpBox.adopt(tmpLeft);
            tmpBox.adopt(tmpRight);
            tmpBox.adopt(tmpCloseBtn);
            tmpBox.adopt(tmpP);
            
            if($('editors').innerHTML == "None Added") {
                $('editors').empty();
            }
            
            $('editors').adopt(tmpBox);                       
            
            tmpListBox.options[i].selected = false;
        }
    }
    
    tmpListBox.multiple = false;
    
    $('editorAddButton').addEvent('click', function(e) {
        var tmpListBox = $('editorList');
        
        if (tmpListBox.options.selectedIndex >= 0) {
        
            var tmpChildren = $('editors').getChildren();
            var found = false;
            for (var i = 0; i < tmpChildren.length; i++) {
                if (tmpChildren[i].title == tmpListBox.options[tmpListBox.options.selectedIndex].value) {
                    found = true;
                }
            }
            
            if (!found) {        
	            var tmpBox = new Element('div');
	            tmpBox.title = tmpListBox.options[tmpListBox.options.selectedIndex].value;
	            tmpBox.addClass('editorName');
	            
	            var hidden = new Element('input');
	            hidden.type = 'hidden';
	            hidden.name = 'editor[]';
	            hidden.value = tmpListBox.options[tmpListBox.options.selectedIndex].value;
	                        
	            var tmpLeft = new Element('p');
	            tmpLeft.addClass('left');
	                                    
	            var tmpRight = new Element('p');
	            tmpRight.addClass('right');
	            
	            var tmpCloseBtn = new Element('p');
	            tmpCloseBtn.innerHTML = "&nbsp;";
	            tmpCloseBtn.title = tmpListBox.options.selectedIndex;
	            tmpCloseBtn.addClass('closeBtn');
	            
	            tmpCloseBtn.addEvent('click', function(e) {
	                this.parentNode.remove();
	                
	                if ($('editors').innerHTML == "") {
	                    $('editors').innerHTML = "None Added";
	                }
	            });
	            
	            
	            var tmpP = new Element('a');
	            tmpP.innerHTML = tmpListBox.options[tmpListBox.options.selectedIndex].label;
	            tmpP.addClass('content');
	            
	            tmpBox.adopt(hidden);
	            tmpBox.adopt(tmpLeft);
	            tmpBox.adopt(tmpRight);
	            tmpBox.adopt(tmpCloseBtn);
	            tmpBox.adopt(tmpP);
	            
	            if($('editors').innerHTML == "None Added") {
	                $('editors').empty();
	            }
	            
	            $('editors').adopt(tmpBox);  
	        }                     
        }
    });
}