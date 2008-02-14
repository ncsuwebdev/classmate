window.addEvent('domready', function() {

    $$('.graphQuestion').toChart({
            'legend': false,
            'barWidthFillFraction': 0.5,
            'barOrientation': 'vertical',
            'backgroundColor': '#BBBBBB',
            'yTickPrecision': 0,
            'width': 300,
            'height': 250,
            'colors': [
                    '#C2573A'
            ]
    });
    
    slidingTabs = new SlidingTabs('evaluationControlsButtons', 'questionPanes');
    
    $('previous').addEvent('click', slidingTabs.previous.bind(slidingTabs));
    $('next').addEvent('click', slidingTabs.next.bind(slidingTabs));
    
});