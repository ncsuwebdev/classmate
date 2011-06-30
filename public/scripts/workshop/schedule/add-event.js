$('document').ready(function () {
	$('legend').addClass('ui-widget-header').addClass('ui-corner-top');
	
	$('#date').datepicker({
		dateFormat: 'DD, MM d, yy'
	});
	
	var locationCapacities = eval(locationCapacitiesString);
		
	$('#location').change(function() {
		$('#maxSize').val(locationCapacities['loc_' + $(this).val()]);
		$('#maxSize').animate({backgroundColor: '#FEFF5F'}, 1000)
	      			 .animate({backgroundColor: '#FFFFFF'}, 1000);
	});
	
	$('#instructors').parent().after('<div id="instructorDisplay"></div>');
    
    $('#instructors option:selected').each(function() {
    	addInstructor($(this).attr('value'), $(this).text());
    });
    
    var instructorData = new Array();
    $('#instructors option').each(function() {
    	instructorData[instructorData.length] = {id: $(this).attr('value'), name: $(this).text()};
    });
    
    $('#instructors').css('display', 'none');
    
    $('#instructors').parent().append('<input id="instructorSearch" type="text" />');
    
    $('#instructorSearch').autocomplete(instructorData, {
    	multiple: false,
    	minChars: 0,
    	matchContains: true,
    	formatItem: function(row, i, max) {
			return row.name;
		}

    }).result(function(event, data, formatted) {
    	$('#instructors option[value=' + data.id + ']').attr('selected', true);
    	$('#instructorSearch').val('');
    	
    	addInstructor(data.id, data.name);
    });
    
    evaluations_init();
});

var instructors = new Array();

function addInstructor(id, display)
{
	if ($.inArray(id, instructors) == -1) {
		var html = '<div class="instructor" id="instructor_' + id + '">'
			+ '<a class="ui-state-default ui-corner-all linkButtonNoText removeInstructor" title="Remove Instructor"><span class="ui-icon ui-icon-minusthick"/></a>'
			+ '<span class="instructorName">' + display + '</span>'
			+ '</div>';
    	
    	$('#instructorDisplay').append(html).css('display', 'block');
    	
    	instructors[id] = id;
    	
    	$('#instructor_' + id + ' a').click(function(e) {
    		e.preventDefault();
    		
    		var instructorId = $(this).parent().attr('id').replace(/^[^_]*_/, '');
    		
    		$('#instructors option[value=' + instructorId + ']').attr('selected', false);
    		
    		$(this).parent().remove();
    		instructors[instructorId] = undefined;
    		if ($('#instructorDisplay').children().length == 0) {
    			$('#instructorDisplay').css('display', 'none');
    		}
    	});
	}
}

function evaluations_init() {
	$('#evaluationHelp').hide();
	
	if($('#evaluationType').val() == 'default') {
		$('#formKey, #answerKey').hide();
	}
	
	$('#evaluationType').change(function() {
		if($(this).val() == 'default') {
			$('#formKey, #answerKey').slideUp('fast');
		} else if ($(this).val() == 'google') {
			$('#formKey, #answerKey').slideDown('fast');
		}
	});
	
	
	$('#evaluationInfo').click(function() {
		$('#evaluationHelp').dialog();
	});
}