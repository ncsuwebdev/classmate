var iEdit = new Class({
    element: null,
    
    target: null,
    
    params: null,
    
    bTextareaWasTinyfied: false,
    
	initialize: function(el) {
	   
	    this.element = el;
	    this.target  = $(el.getProperty('target'));
	    this.params = this.parseQuery(this.target.getProperty('rel'));
        this.inlineEdit(this.target);	
	},
	    
    cleanup: function() {
    },
    
    save: function(modifiedEl) {
        this.target.style.display = 'block';
        this.target.innerHTML = modifiedEl.value;    
        
        var pars = modifiedEl.getProperty('name') + '=' + escape(modifiedEl.value);
        var url = this.params['url'];
                                    
        $$('.postArgs', '.' + modifiedEl.getProperty('name')).each( function(arg) {
            if (pars != '') {
                pars += '&';
            }
            pars += arg.getProperty('name') + '=' + arg.getProperty('value');
        });  
        
        var updateEl = $(this.params['response']);
                
        new Ajax( url, {
            method: 'post',
            postBody: pars,
            update: updateEl,
            onComplete: function(){   
                var responseFade = new Fx.Style(updateEl.getProperty('id'), 'opacity', {
                    duration:2000,
                });
                responseFade.start.pass([1,0],responseFade).delay(1000);
            }
        }).request();                
    },
    
	inlineEdit: function(el) {
	    if(this.params['type'] == 'textarea'){                
	        var rel = el.getProperty('rel');
	                            
	        el.style.display = 'none';
	        var content = el.innerHTML;
	        var container = new Element('div').injectAfter(el);
	
	        //check params are suitable or set base attributes
	        if (this.params['rows'] == undefined) {
	            this.params['rows'] = '15';
	        }
	        
	        if (this.params['cols'] == undefined) {
	            this.params['cols'] = '65';
	        }
	
	        var txtID = 'mce_' + el.getProperty('id');
	                                    
	        //textarea
	        var textarea = new Element('textarea').injectInside(container);
	        textarea.value = content;
	        textarea.setProperty('class', 'mceEditor');
	        textarea.setProperty('id', txtID);
	        textarea.setProperty('name', el.getProperty('id'));
	        textarea.setStyle('position','relative');                                       
	        textarea.setProperties({
	            rows: this.params['rows'],
	            cols: this.params['cols']
	        });
	                                              
	        this.setTextareaToTinyMCE(txtID);
	                                                
	        //save + cancel div
	        var saveDiv = new Element('div').injectInside(container);
	        saveDiv.setProperty('class', 'txtAreaSave');
	                                                                       
            //cancel
            var cancel = new Element('input').injectInside(saveDiv);
            cancel.setProperty('type', 'button');
            cancel.setProperty('value', 'Cancel');
            
            new Element('br').injectBefore(cancel);             
            var span = new Element('span').injectBefore(cancel);
            span.innerHTML = '  ';
            
            //save
            var save = new Element('input').injectInside(saveDiv);
            save.setProperty('type', 'button');
            save.setProperty('value', 'Save');
	                  
	        obj = this;
	                      
	        save.addEvent('click', function() {
	            obj.unsetTextareaToTinyMCE(txtID);                 
	            textarea.remove();
	            this.remove();
	            cancel.remove();
	            span.remove();
	            saveDiv.remove();
	            obj.save(textarea);
	            obj.cleanup();
	        });
	                                
	        cancel.addEvent('click', function() {
	            obj.unsetTextareaToTinyMCE(txtID);
	            el.style.display = 'block';
	            textarea.remove();
	            this.remove();
	            save.remove();
	            span.remove();
	            saveDiv.remove();
	            obj.cleanup();
	        }); 
	    }
	    
	    if(this.params['type'] == 'input'){  
            el.style.display = 'none';
    
            var content = el.innerHTML;
            var container = el.parentNode;
                                        
            //input
            var textarea = new Element('input').injectInside(container);
            textarea.value = content;
            textarea.setProperty('name', el.getProperty('id'));
            
            if (this.params['size'] == undefined) {
                textarea.setProperty('size', 20);
            } else {
                textarea.setProperty('size', this.params['size']);
            }
    
            //save + cancel div
            var saveDiv = new Element('span').injectInside(container);
            saveDiv.setProperty('class', 'txtAreaSave');
            
            //cancel
            var cancel = new Element('input').injectInside(saveDiv);
            cancel.setProperty('type', 'button');
            cancel.setProperty('value', 'Cancel');
            
            var span = new Element('span').injectBefore(cancel);
            span.innerHTML = '  ';
            
            //save
            var save = new Element('input').injectInside(saveDiv);
            save.setProperty('type', 'button');
            save.setProperty('value', 'Save');
            
            obj = this;
            
            save.addEvent('click', function() {
                el.style.display = 'block';
                el.innerHTML = textarea.value;                                   
                textarea.remove();
                this.remove();
                cancel.remove();
                span.remove();
                saveDiv.remove();
                obj.save(textarea);
                obj.cleanup();
            });
            
            cancel.addEvent('click', function() {
                el.style.display = 'block';
                textarea.remove();
                this.remove();
                save.remove();
                span.remove();
                saveDiv.remove();
                obj.cleanup();
            });     
	    } 
	},

    parseQuery: function(query) {
        var Params = new Object ();
        if (!query) { 
            return Params;
        } 
                
        var Pairs = query.split(/[;&]/);
        for (var i = 0; i < Pairs.length; i++) {
            var KeyVal = Pairs[i].split('=');
            if (!KeyVal || KeyVal.length != 2) {
                continue;
            }
            
            var key = unescape( KeyVal[0] );
            var val = unescape( KeyVal[1] );
            val = val.replace(/\+/g, ' ');
            Params[key] = val;
        }           
        
        return Params;
    },	
    
    setTextareaToTinyMCE: function(sEditorID) {
        var oEditor = document.getElementById(sEditorID);
        if(oEditor && !this.bTextareaWasTinyfied) {
            tinyMCE.execCommand('mceAddControl', true, sEditorID);
            this.bTextareaWasTinyfied = true;
        }
        return;
    },
    
    unsetTextareaToTinyMCE: function (sEditorID) {
        var oEditor = document.getElementById(sEditorID);
        if(oEditor && this.bTextareaWasTinyfied) {
            tinyMCE.execCommand('mceRemoveControl', true, sEditorID);
            this.bTextareaWasTinyfied = false;
        }
        return;
    }    
});