
$(document).ready(function(){$(".disappear").addClass("in").fadeOut(5500);

	/* swap open/close side menu icons */
	$('[data-toggle=collapse]').click(function(){
	  	// toggle icon
	  	$(this).find("i").toggleClass("glyphicon-chevron-right glyphicon-chevron-down");
	});

	// Delete 
	$('#delete').click(function(e){
		if ($('#form input:checked').length == 0) {
			alert('Please select items to delete!');
			e.preventDefault();
		}
		else {
			confirm('Are You Sure?!') ? $('#form').submit() : e.preventDefault();
		}
	})

	$('.delete').click(function(e){
		confirm('Are You Sure?!') ? '' : e.preventDefault();
	});	

});

