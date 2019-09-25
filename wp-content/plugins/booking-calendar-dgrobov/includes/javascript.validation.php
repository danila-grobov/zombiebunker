<script type="text/JavaScript">
<!--
 var bookQty = <?php echo $availebleSpaces?>;
 var bookQtyTmp = bookQty;
 var feePay = <?php echo $fee?>;
 var discount = 0;
 var discountType = 'rel';
 var maximumBookings = <?php echo $maximumBookings ?>;
 var minimumBookings = <?php echo $minimumBookings ?>;
 var maxfordisp = <?php echo $maximumBookings * $int / 60 ?>;
 var minfordisp = <?php echo $minimumBookings * $int / 60 ?>;
 
function checkForm() {
	
	
	var err=0;
	var msg2="";
<?php
$reqFields=array(
	"name",
	"phone",
	"email",
	"captcha"
	
);

foreach ($reqFields as $v) { ?>

	if (document.getElementById('<?php echo $v?>').value==0 || document.getElementById('<?php echo $v?>').value=="00") {
		if (err==0) {
			document.getElementById('<?php echo $v?>').focus();
		}
		document.getElementById('<?php echo $v?>').style.backgroundColor='#ffa5a5';
		err=1;
	}
<?php } ?>
	
	var reg1 = /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/; // not valid
	var reg2 = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,5}|[0-9]{1,5})(\]?)$/; // valid   
	if (document.getElementById('email').value==0 || !reg2.test(document.getElementById('email').value)) {
	if (err==0) {
		document.getElementById('email').focus();
	}
	document.getElementById('email').style.backgroundColor='#ffa5a5';
	err=1;
	}
	
	//check checkboxes, must be at least 1, and not more than 3
  var checks = document.getElementsByName('time[]');
  var boxLength = checks.length;
  var totalChecked = 0;
    
    for ( i=0; i < boxLength; i++ ) {
      if ( checks[i].checked == true ) {
		totalChecked++;
      }
	}
	
	try{
		var qty = document.getElementById('qty').value;
		
		if(qty==''){
			document.getElementById('qty').style.backgroundColor='#ffa5a5';
			err=1;
		}
	}catch(e){
		var qty=1;
	}
	
if (err==0) {
		 if(totalChecked>0 && totalChecked>=minimumBookings && totalChecked<=maximumBookings){
			 //return false;
			err==0; 
		 } else { 
                     
		 	if(maximumBookings==99){ 
                            var tt = ""; 
                        } else { 
                            var tt = " maximum "+getTimeText(maxfordisp); 
                        }
		 	alert("Minimum booking time "+getTimeText(minfordisp)+" hour(s)"+tt+". Please adjust your booking!");
		 	return false;
		 }
		 
		 if(err==0 && ((bookQtyTmp-qty)>=0)){
			//alert ('ok');
			return true;
		 }else{
			
			alert("Maximum booking qty "+bookQtyTmp+" . Please adjust your booking!");
		 	return false;
		}
	} else {
		alert("Please complete all highlighted fields to continue.");
		return false;
	}
	
}
function disableOthersTimeSpots(){
    $("input[name='time[]']").not(":checked").attr("disabled","disabled");
    
}
function enableAllTimeSpots(){
    $("input[name='time[]']").removeAttr("disabled");
}
function calcFee(){
	var el=$('#feeValue');
    var elOld = $('#feeValueOld');
    var tax = <?php echo getOption("enable_tax") ? getOption("tax") : 0; ?>;
	bookQtyTmp=bookQty;
	var tmp=bookQtyTmp*1;
	var intervals=$("input[name='time[]']:checked").length; 
	$("input[name='time[]']:checked").each(function(){
		if(($(this).attr('rel'))*1<=tmp){
			tmp=$(this).attr('rel');
		}
		
	});
	//console.log(tmp);
        
        if(intervals>=maximumBookings){
            disableOthersTimeSpots()
        }else{
            enableAllTimeSpots()
        }
	
	if(tmp<bookQtyTmp){
		bookQtyTmp=tmp
	}
	console.log(bookQtyTmp);
	if($('#qty').length){
		var qty =$('#qty').val();
	}else{
		var qty =1;
	}
	//console.log(intervals);
	//console.log(feePay);
	//console.log(qty);
	var fee=qty*feePay*intervals;
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
            elOld.html('(<?php echo (getOption('currency_position')=='b'?getOption('currency')." ":"")?>'+feeOr.toFixed(2)+' <?php echo (getOption('currency_position')=='a'?getOption('currency'):"")?>)');
        }else{
            elOld.html("");
        }
    var taxVal = fee*tax/100;
    fee = fee+taxVal;
	el.html('<?php echo (getOption('currency_position')=='b'?getOption('currency')." ":"")?>'+fee.toFixed(2)+' <?php echo (getOption('currency_position')=='a'?getOption('currency'):"")?>');
       
        
        
}
function getTimeText(hours){
    var _hours = Math.floor(hours);
    var min = (hours-_hours)*60;
    var text = _hours+" hour(s)";
    if(min>0){
        text+=" "+min+" minutes";
    }
    return text;
}

function checkFieldBack(fieldObj) {
	if (fieldObj.value!=0) {
		fieldObj.style.backgroundColor='#FFF';
	}
}


$(function(){
	
		$("input[name='time[]']").bind('change',calcFee);
		if($('#qty').length){
			$('#qty').bind('change',function(){calcFee();})
			$('#qty').bind('keyup',function(){calcFee();})
			
		}
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
                            discount = data.value;
                            discountType = data.type;
                            $("#discountDetails").html(discountType=='abs'?'<?php echo (getOption('currency_position')=='b'?getOption('currency')." ":"")?>'+formatNumber(discount)+'<?php echo (getOption('currency_position')=='a'?getOption('currency')." ":"")?>':discount+" %");
                            calcFee();
                        }
                    })
                    }
                });
                $("#couponCode").trigger('blur');
})

//-->
</script> 