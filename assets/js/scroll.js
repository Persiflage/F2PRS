$(document).ready(function(){

	var button = $("#search_btn");
	var box = $("#searchbox");
	var submit = $("#input_btn");
	var isOpen = false;

  	button.click(function() {
  		if(isOpen == false) {
    		button.css('z-index', '2');
  			box.slideDown();
  			box.focus();
  			isOpen = true;
  		} else {
  			validate();
  			box.slideUp();
  			box.focusout();
  			isOpen = false;
  		}
    });

    function validate() {
    	if(box.val() == "") {
    		submit.css('z-index', '0');
    		button.css('z-index', '2');
    	} else {
    		submit.click();
    	}
    }

    button.mouseup(function() { return false; });
    box.mouseup(function() { return false; });

    $(document).mouseup(function() {
    	if(isOpen == true) {
    		box.slideUp();
    		isOpen = false;
    	}
    });

});
