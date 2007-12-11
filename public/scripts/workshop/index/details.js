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
        changeTransition: Fx.Transitions.Quad.easeOut,
        duration: 1000,
        mouseOverClass: 'over',
        activateOnLoad: 'first',
        useAjax: false,
    });
    
    $$('.inlineEdit').each(function (el) {
    
        el.addEvent('dblclick', function(e) {
            
        });
    });
    
});