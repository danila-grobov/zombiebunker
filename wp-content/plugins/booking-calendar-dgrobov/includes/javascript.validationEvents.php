<script type="text/JavaScript">
    <!--
      var discount = 0;
      var discountType = 'rel';
    function calcFee(){
        var tax = <?php echo getOption("enable_tax") ? getOption("tax") : 0; ?>; 
        var price =<?php echo $eventInfo['entryFee'] ?>; 
        var el = jQuery("#qty");
        var elOld = $('#feeValueOld');
        
        var qty=(el.length)?el.val()*1:1;
        var fee = (qty*price);
        console.log(fee);
        var feeOr = fee;
        if(discount>0){
            if(discountType == 'abs'){
                fee = fee-discount*1;
                fee = fee<0?0:fee;
            }else{
                fee = fee*(1-(discount/100));
                
            }
            if(fee>0){
                var taxValOr = fee*tax/100;
                feeOr = feeOr+taxValOr;
            }

            console.log(feeOr);
            elOld.html('<?php echo (getOption('currency_position')=='b'?getOption('currency')." ":"")?>'+feeOr.toFixed(2)+' <?php echo (getOption('currency_position')=='a'?getOption('currency'):"")?>');
        }else{
            elOld.html("");
        }
        var taxVal = fee*tax/100;
        fee = fee+taxVal;
        jQuery('#price').html(fee.toFixed(2))
    }
    function checkForm() {
        var err=0;
        var msg2="";
<?php
$reqFields = array(
    "name",
    "phone",
    "email",
    "captcha"
);

foreach ($reqFields as $v) {
    ?>

              if (document.getElementById('<?php echo $v ?>').value==0 || document.getElementById('<?php echo $v ?>').value=="00") {
                  if (err==0) {
                      document.getElementById('<?php echo $v ?>').focus();
                  }
                  document.getElementById('<?php echo $v ?>').style.backgroundColor='#ffa5a5';
                  err=1;
              }<?php
}
?>
	
      var reg1 = /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/; // not valid
      var reg2 = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,5}|[0-9]{1,5})(\]?)$/; // valid   
      if (document.getElementById('email').value==0 || !reg2.test(document.getElementById('email').value)) {
          if (err==0) {
              document.getElementById('email').focus();
          }
          document.getElementById('email').style.backgroundColor='#ffa5a5';
          err=1;
      }
	

		


      if (err==0) {
          return true;
      } else {
          alert('<?php echo MSG_JS_ALLFIELDS; ?>');
          return false;
      }
	
  }


  function checkFieldBack(fieldObj) {
      if (fieldObj.value!=0) {
          fieldObj.style.backgroundColor='#fff';
      }
  }

  $(function(){
	
		
		calcFee();
                 $("#couponCode").bind("blur",function(){
                    console.log($(this).val());
                    var serviceId = $("input[name='serviceID']").val();
                    var code = $(this).val();
                    if(code.length>0){
                    $.getJSON("ajax/checkCoupon.php",{serviceID:serviceId,couponCode:code},function(data){
                        console.log(data);
                        if(!data.responce){
                            addMessage(data.message,'error');
                            //$("#submit_button").hide();
                            discount = 0;
                            discountType = "";
                            $("#discountDetails").html("");
                            calcFee();
                        }else{
                            //$("#submit_button").show();
                            discount = data.value-0;
                            discountType = data.type;
                            $("#discountDetails").html(discountType=='abs'?'<?php echo (getOption('currency_position')=='b'?getOption('currency')." ":"")?>'+discount.toFixed(2)+'<?php echo (getOption('currency_position')=='a'?getOption('currency')." ":"")?>':discount+" %");
                            calcFee();
                        }
                    })
                    }
                })
      $("#couponCode").trigger('blur');
                $("#qty").bind("change",function(){
                    calcFee();
                })
})      
  //-->
</script> 