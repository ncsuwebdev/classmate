$('document').ready(function() {
	$('#evaluationInfo').click(function() {
		$('#evaluationHelp').dialog({
			height		: 400,
			width		: 700,
			resizable	: false,
			title		: 'Adding Custom Evaluation Forms',
			show		: 'blind',
			hide		: 'blind',
			dialogClass : 'helpDialog'
		});
	});
	
	$('div.slide').css('height',338).wrapAll('<div id="slideWrapper">').last().css({'border' : 'none'});
	$('#slideWrapper').width($('div.slide').length * $('div.slide:first').outerWidth(true))
		.height($('div.slide:first').height());
	
	$('#slideWrapper').css( {
			'height'	: $('div.middle').height(),
	});
	
	var slideWidth = $('div.slide').outerWidth(true) - 1;
	var offset = Math.abs($('#slideWrapper').position()['left']);
	
	$('div.left').css({
		'opacity': 0,
		'cursor' : 'auto'
	});
	
	$('div.left').click(function() {
		if((offset - slideWidth) > 0 ) {
			
			offset -= slideWidth;
			
			$('#slideWrapper').animate({
				left : '+=' + slideWidth
			},1000, function(){
				
			});
			refreshNav(offset, slideWidth);
		}
	});
	
	$('div.right').click(function() {
		if(offset + slideWidth < $('#slideWrapper').width()) {
			
			offset += slideWidth;
			
			$('#slideWrapper').animate({
				left : '-=' + slideWidth
			}, 1000, function(){
				
			});
			refreshNav(offset, slideWidth);
		}
	});
	
//	$('div.nav').hover(function(){
//		console.log('Hover in');
//		console.log($(this).find('span'));
//		$(this).find('span').css({
//			'background-position' : '0 -30px',
//		});
//	}, function() {
//		console.log('Hover out');
//		$(this).find('span').css({
//			'background-position' : '0 0',
//		});
//	});
	
	
});

function refreshNav(offset, slideWidth) {
	if ( (offset + slideWidth)  > $('#slideWrapper').width()) {
		$('#right_arrow').fadeOut();
		$('div.right').css({
			'cursor' : 'auto'
		});
	} else {
		$('#right_arrow').fadeIn();
		$('div.right').css({
			'cursor' : 'pointer'
		});
	}
	
	if ( (offset - slideWidth) > 0) {
		$('div.left').animate({
			'opacity' : 100
		}, 1500);
		$('div.left').css({
			'cursor' : 'pointer'
		});
	} else {
		$('div.left').animate({
			'opacity' : 0
		}, 100);
		$('div.left').css({
			'cursor' : 'auto'
		});
	}
}