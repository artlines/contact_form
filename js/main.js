(function($) {
	$.fn.serializefiles = function() {
	    var obj = $(this);
	    /* ADD FILE TO PARAM AJAX */
	    var formData = new FormData();
	    $.each($(obj).find("input[type='file']"), function(i, tag) {
	        $.each($(tag)[0].files, function(i, file) {
	            formData.append(tag.name, file);
	        });
	    });

	    var params = $(obj).serializeArray();
	    $.each(params, function (i, val) {
	        formData.append(val.name, val.value);
	    });

        var result = calculator();
        formData.append('result', result);
        
	    return formData;
	};
})(jQuery);

$(function(){
    $("#phone").mask("+7 (999) 999-9999");

    $( "#order_form" ).on( "submit", function( event ) {
		event.preventDefault();
		
    	var result = calculator();

		var formData = $(this).serializefiles();
		Pace.track(function(){
			$.ajax
		    ({
				type: "POST",
				url: 'http://vsesnpch.ru/modules/mod_photo/default.php',
	            cache: false,
	            contentType: false,
	            processData: false,
			    data: formData,
				success: function(data){
					$('.out').html(data).slideDown(600);
				},
				error: function(error){
					console.error('Не могу получить данные: ' + error);
					$('.out').html(error).slideDown(600);
				}
		    });
	    });
	});
//калькулятор
    $( "#calc" ).on( "click", function( event ) {

    	var result = calculator();

    	$( "#result" ).text(result);
	});
});

function calculator() {

    var result, sum, delivery;

	var quantity = $( "#quantity" ).val();
	var format = $( "#format" ).val();
	var paper = $( "#paper" ).val();

	if (quantity > 0 && quantity < 50) {
		if (paper == 'Глянцевая' || paper == 'Матовая' ) {
    		switch(format){
    			case '10 x 15':
    				sum = quantity*6;
    				break;
    			case '15 x 21':
    				sum = quantity*14;
    				break;
    			case '20 x 30':
    				sum = quantity*24;
    				break;
    			case '30 x 40':
    				sum = quantity*70;
    				break;
			}
    	}else{
    		switch(format){
    			case '10 x 15':
    				sum = quantity*8;
    				break;
    			case '15 x 21':
    				sum = quantity*18;
    				break;
    			case '20 x 30':
    				sum = quantity*30;
    				break;
    			case '30 x 40':
    				sum = quantity*80;
    				break;
			}	
    	}
		delivery = 100;
	}else if (quantity >= 50 && quantity <= 100) {
		if (paper == 'Глянцевая' || paper == 'Матовая' ) {
    		switch(format){
    			case '10 x 15':
    				sum = quantity*5;
    				break;
    			case '15 x 21':
    				sum = quantity*13;
    				break;
    			case '20 x 30':
    				sum = quantity*22;
    				break;
    			case '30 x 40':
    				sum = quantity*65;
    				break;
    		}
    	}else{
    		switch(format){
    			case '10 x 15':
    				sum = quantity*7;
    				break;
    			case '15 x 21':
    				sum = quantity*17;
    				break;
    			case '20 x 30':
    				sum = quantity*28;
    				break;
    			case '30 x 40':
    				sum = quantity*75;
    				break;
    		}
		}
		delivery = 50;
	}else if (quantity > 100) {
		if (paper == 'Глянцевая' || paper == 'Матовая' ) {

    		switch(format){
    			case '10 x 15':
    				sum = quantity*4,5;
    				break;
    			case '15 x 21':
    				sum = quantity*12;
    				break;
    			case '20 x 30':
    				sum = quantity*20;
    				break;
    			case '30 x 40':
    				sum = quantity*60;
    				break;
    		}
    	}else{
    		switch(format){
    			case '10 x 15':
    				sum = quantity*6,5;
    				break;
    			case '15 x 21':
    				sum = quantity*16;
    				break;
    			case '20 x 30':
    				sum = quantity*26;
    				break;
    			case '30 x 40':
    				sum = quantity*70;
    				break;
    		}
    	}
		delivery = 0;
	}

	if (typeof(sum) == "number") {
		result = 'Итого: '
		result += sum + delivery;
		result += ' руб.'
	}else{
		result = 'Введите количество!';
	}

	return result;
}