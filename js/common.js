
// Use jQuery via $j(...)
jQuery(document).ready(function(){


/////////////////// Back End Voucher System jQuery

	jQuery(".del").click(function(){
    	if (!confirm("Are you sure you wish to delete this sold certificate?")){
      		return false;
    	}
    });

	jQuery(document).on("click","[id^=button\\-]",function() {
            var n = jQuery( this ).attr( "id" ).split( "-" ).pop();
            jQuery( "[id^=toggle\\-div\\-" + n + "]" ).toggle();
            console.log(n);
      });

  jQuery(document).on("click","[id^=id\\-]",function() {
            var n = jQuery( this ).attr( "id" ).split( "-" ).pop();
            jQuery( "[id^=payment\\-div\\-" + n + "]" ).toggle();
            console.log(n);
      });


//////////////////////////////////////////////////////////////////////////
     


/////////////////// Gift Voucher Front End UI jQuery

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

            });

        return false;
  });

});
