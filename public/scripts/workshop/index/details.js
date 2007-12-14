window.addEvent('domready', function() {

    /* Grid Tool Tips */
    var tips = new Tips($$('.document'), {

        className: 'documentDescription',

        initialize:function(){
            this.fx = new Fx.Style(this.toolTip, 'opacity', {duration: 500, wait: false}).set(0);
        },

        onShow: function(toolTip) {
            this.fx.start(1);
        },

        onHide: function(toolTip) {
            this.fx.start(0);
        }
    });
    
    myTabs1 = new mootabs('tabContainer', {
        width: '100%',
        height: '300px',
        changeTransition: '',
        duration: 0,
        mouseOverClass: 'over',
        activateOnLoad: 'first',
        useAjax: false,
    });


    var editorClass = '.inlineEdit';
    
    $$(editorClass).each(function(el) {
        el.addEvent('click', function() {
            var editor = new myIEdit(el, editorClass);
        });
    });
    
}); 
    
var myIEdit = iEdit.extend({
    class: '',
    
    initialize: function(el, class) {
        $$(class).each(function (hide) {
            hide.style.display = 'none';
        });
        
        this.class = class;
        this.parent(el);
    },
    
    cleanup: function() {
        $$(this.class).each(function (show) {
            show.style.display = '';
        });
            
        this.parent();
    }
});

