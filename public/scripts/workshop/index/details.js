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