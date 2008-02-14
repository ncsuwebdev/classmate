var editing = false;

window.addEvent('domready', function() {

    var sitePrefix = $('sitePrefix').value;
    
    var editorClass = '.inlineEdit';
    
    $$(editorClass).each(function(el) {
        el.addEvent('click', function() {
            var editor = new myIEdit(el, editorClass);
        });
    });
    
    var el = $('status');
    
        el.addEvent('click', function() {
            var msg = '';
            var status = '';
            var label = '';
            
            if (el.hasClass('status_enabled')) {
                msg = 'Are you sure you want to disable this location?  It will ' +
                    'no longer be able to be scheduled if you do';
                label = 'Enable This Location';
                status = 'disabled';
            } else {
                msg = 'Are you sure you want to enable this location?';
                label = 'Disable This Location';
                status = 'enabled';
            }
            
            if (confirm(msg)) {
                
                var locationId = $('locationId').value;
                var updateEl = $('response');                        
                        
                new Ajax(sitePrefix + '/workshop/location/edit/', {
                    method: 'post',
                    postBody: 'locationId=' + locationId + '&status=' + status,
                    update: updateEl,
                    onComplete: function(){
                        var responseFade = new Fx.Style(updateEl.getProperty('id'), 'opacity', {
                            duration:2000
                        });
                        responseFade.start.pass([1,0],responseFade).delay(1000);
                    }                    
               }).request();
               
               el.value = label;
               el.toggleClass('status_enabled');
               el.toggleClass('status.disabled');
               
               if (status == 'disabled') {
                   var span = new Element('span');
                   span.id = 'disabled';
                   span.innerHTML = 'DISABLED!';
                   
                   span.injectBefore($('name'));
                   
                   var img = new Element('img');
                   img.src = sitePrefix + '/public/images/cross.png';
                   img.id  = 'disabledImage';
                   
                   img.injectBefore($('disabled'));
                   
               } else {
                   $('disabled').remove();
                   $('disabledImage').remove();
               } 
         
            }             
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