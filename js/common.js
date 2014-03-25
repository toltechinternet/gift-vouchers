
// Use jQuery via $j(...)
jQuery(document).ready(function(){


/////////////////// Back End Voucher System jQuery

  //jQuery('.show_table_information').removeAttr('style'); 

  jQuery(".wp-list-table").filterTable({
  });


	jQuery(".del").click(function(){
    	if (!confirm("Are you sure you wish to delete this sold certificate?")){
      		return false;
    	}
    });

	jQuery(document).on("click","[id^=button\\-]",function() {
            var n = jQuery( this ).attr( "id" ).split( "-" ).pop();
            jQuery( "[id^=toggle\\-div\\-" + n + "]" ).toggle();
      });


  jQuery(document).on("click","[id^=id\\-]",function() {
            var n = jQuery( this ).attr( "id" ).split( "-" ).pop();
            jQuery( "[id^=payment\\-div\\-" + n + "]" ).toggle();
			
			if(jQuery( "#id-"+n+"" ).html()=='<span class="down-arrow"></span>'){
				jQuery( "#id-"+n+"" ).html('<span class="up-arrow"></span>');
				jQuery( 'html, body' ).animate({scrollTop: jQuery("#id-"+n+"").offset().top-40}, 1000);
			}else{
				jQuery( "#id-"+n+"" ).html('<span class="down-arrow"></span>');
			}
      });


//////////////////////////////////////////////////////////////////////////
     


/////////////////// Gift Voucher Front End UI jQuery


$("tr.postal-delivery-selected").hide();
$("tr.box-ticked").hide();

$('select[id^="method"]').change(function(){
	
	var id = $(this).closest('form').attr('id');	
	var option = this.options[this.selectedIndex];	
	
	if(option.value=="Postal"){									
		$("tr#postal-delivery-selected-"+id).show();			
		if($("#send_to_recipient_address-"+id).is(':checked')){
			$("tr#box-ticked-"+id+"-1").show();
			$("tr#box-ticked-"+id+"-2").show();
			$("tr#box-ticked-"+id+"-3").show();
			$("tr#box-ticked-"+id+"-4").show();
			$("tr#box-ticked-"+id+"-5").show();
			$("tr#box-ticked-"+id+"-6").show();
		}
	}else{
		$("tr#postal-delivery-selected-"+id).hide();
		$("tr#box-ticked-"+id+"-1").hide();
		$("tr#box-ticked-"+id+"-2").hide();
		$("tr#box-ticked-"+id+"-3").hide();
		$("tr#box-ticked-"+id+"-4").hide();
		$("tr#box-ticked-"+id+"-5").hide();
		$("tr#box-ticked-"+id+"-6").hide();
	}
});
$('.send_to_recipient_address').change(function () {
	var id = $(this).closest('form').attr('id');											 
    if($("#send_to_recipient_address-"+id).is(':checked')){
		$("tr#box-ticked-"+id+"-1").show();
		$("tr#box-ticked-"+id+"-2").show();
		$("tr#box-ticked-"+id+"-3").show();
		$("tr#box-ticked-"+id+"-4").show();
		$("tr#box-ticked-"+id+"-5").show();
		$("tr#box-ticked-"+id+"-6").show();
	}else{
		$("tr#box-ticked-"+id+"-1").hide();
		$("tr#box-ticked-"+id+"-2").hide();
		$("tr#box-ticked-"+id+"-3").hide();
		$("tr#box-ticked-"+id+"-4").hide();
		$("tr#box-ticked-"+id+"-5").hide();
		$("tr#box-ticked-"+id+"-6").hide();
	}
	
});


$('.buy').click(function () {
    
    var id = $(this).attr('id');

    // Delivery Information Toggle
    $('.delivery_content').hide();
    $('#show_delivery-' + id).click(function(){
        $('#delivery_content-' + id).toggle();
    });

    // Terms and ConditionsToggle
    $(".terms_content").hide();
    $('#show_terms-' + id).click(function(){
        $('#terms_content-' + id).toggle();
    });

        
        $('.form' + id).toggle(500);
        $('#process-'+ id).click(function () {

              
          //Check if exists before attempting monetary validation
          if ($('#cost-monetary-'+ id).length) {
              // Monetary Validation
              if ($('#cost-monetary-'+ id).val() < 20.00) {
              //FAIL
              $('#cost-monetary-'+ id).addClass('missing').removeClass('complete').focus();
              console.log("< 20.00 MATCH - "+$('#cost-monetary-'+ id).val());
                    return false
            } else{
              if ($('#cost-monetary-'+ id).val() <= 100.00) {
                //SUCCESS
                $('#cost-monetary-'+ id).removeClass('missing').addClass('complete');
                console.log("<= 100.00 MATCH - "+$('#cost-monetary-'+ id).val());
                
              }else{
                //FAIL
                $('#cost-monetary-'+ id).addClass('missing').removeClass('complete').focus();
                console.log("> 100.00 MATCH - "+$('#cost-monetary-'+ id).val());
                return false
              }
            }
          }

            // Form Validation
                if ($('#name-'+ id).val() == "") {
                    $('#name-'+ id).addClass('missing').removeClass('complete').focus();
                    return false

                } else{
                  $('#name-'+ id).removeClass('missing').addClass('complete')
                }

                if ($('#email-'+ id).val() == "") {
                    $('#email-'+ id).addClass('missing').removeClass('complete').focus();
                    return false

                } else{
                  $('#email-'+ id).removeClass('missing').addClass('complete')
                }

                if ($('#address1-'+ id).val() == "") {
                    $('#address1-'+ id).addClass('missing').removeClass('complete').focus();
                    return false

                } else{
                  $('#address1-'+ id).removeClass('missing').addClass('complete')
                }

                if ($('#city-'+ id).val() == "") {
                    $('#city-'+ id).addClass('missing').removeClass('complete').focus();
                    return false

                } else{
                  $('#city-'+ id).removeClass('missing').addClass('complete')
                }

                if ($('#postalcode-'+ id).val() == "") {
                    $('#postalcode-'+ id).addClass('missing').removeClass('complete').focus();
                    return false

                } else{
                  $('#postalcode-'+ id).removeClass('missing').addClass('complete')
                }

                if ($('#country-'+ id).val() == "") {
                    $('#country-'+ id).addClass('missing').removeClass('complete').focus();
                    return false

                } else{
                  $('#country-'+ id).removeClass('missing').addClass('complete')
                }

                if ($('#telephone-'+ id).val() == "") {
                    $('#telephone-'+ id).addClass('missing').removeClass('complete').focus();
                    return false

                } else{
                  $('#telephone-'+ id).removeClass('missing').removeClass('complete').addClass('complete')
                }

                if ($('#recipient-'+ id).val() == "") {
                    $('#recipient-'+ id).addClass('missing').removeClass('complete').focus();
                    return false

                } else{
                  $('#recipient-'+ id).removeClass('missing').addClass('complete')
                }

                // Checkbox Validation
                if($("#send_to_recipient_address-"+id).is(':checked')){

                    if ($('#Raddress1-'+ id).val() == "") {
                    $('#Raddress1-'+ id).addClass('missing').removeClass('complete').focus();
                    return false

                    } else{
                      $('#Raddress1-'+ id).removeClass('missing').addClass('complete')
                    }

                    if ($('#Rcity-'+ id).val() == "") {
                    $('#Rcity-'+ id).addClass('missing').removeClass('complete').focus();
                    return false

                    } else{
                      $('#Rcity-'+ id).removeClass('missing').addClass('complete')
                    }

                    if ($('#Rpostalcode-'+ id).val() == "") {
                    $('#Rpostalcode-'+ id).addClass('missing').removeClass('complete').focus();
                    return false

                    } else{
                      $('#Rpostalcode-'+ id).removeClass('missing').addClass('complete')
                    }


                }

            });

        return false;
  });

});
