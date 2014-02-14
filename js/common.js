// Use jQuery via $j(...)
jQuery(document).ready(function(){

	jQuery(".del").click(function(){
    	if (!confirm("Are you sure you wish to delete this sold certificate?")){
      		return false;
    	}
    });

});
