window.addEvent('domready', function() {

    slidingTabs = new SlidingTabs('evaluationControlsButtons', 'questionPanes');
    
    $('previous').addEvent('click', slidingTabs.previous.bind(slidingTabs));
    $('next').addEvent('click', slidingTabs.next.bind(slidingTabs));
});