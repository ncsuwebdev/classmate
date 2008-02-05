var editing = false;

window.addEvent('domready', function() {

    var sitePrefix = $('sitePrefix').value;
    
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
                var updateEl = $('response');                        
                        
				new Ajax(sitePrefix + '/workshop/index/deleteDocument/', {
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
                var updateEl = $('response');                        
                        
                new Ajax( sitePrefix + '/workshop/index/deleteLink/', {
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
		var docForm = $('addDocumentForm');
		docForm.style.display = 'none';
		$('addDocument').addEvent('click', function(e) {
		   e = new Event(e);
		   
		   if (docForm.style.display == 'none') {
		       docForm.style.display = 'block';
		   } else {
		       docForm.style.display = 'none';
		   }
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
	    
	        new StickyWinModal({
	            onDisplay: initEventPopup,
	            content: stickyWinHTML('Manage Workshop Options', eventPopupHtml, {
	                width: '500px',
	                buttons: [
	                    {
	                        text: 'Cancel', 
	                        onClick: function() {
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
});


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
                $('editorList').options[this.title].setStyle('display', '');
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
            tmpListBox.options[i].setStyle('display', 'none');
        }
    }
    
    tmpListBox.multiple = false;
    
    $('editorAddButton').addEvent('click', function(e) {
        var tmpListBox = $('editorList');
        
        if (tmpListBox.options.selectedIndex >= 0) {
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
                $('editorList').options[this.title].setStyle('display', '');
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
            
            tmpListBox.options[tmpListBox.options.selectedIndex].setStyle('display', 'none');
        }
    });
}