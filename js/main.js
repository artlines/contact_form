$(function(){
    $("#phone").mask("+7 (999) 999-9999");

    $( "#order_form" ).on( "submit", function( event ) {
		event.preventDefault();
		
		var formData = $(this).serializefiles();
		$.ajax
	    ({
			type: "POST",
			url: '../index.php',
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

