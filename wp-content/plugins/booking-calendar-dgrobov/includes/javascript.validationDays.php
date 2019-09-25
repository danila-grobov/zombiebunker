<script type="text/JavaScript">
<!--

 var feePay = <?php echo $dayPrice?>;
  var discount = 0;
 var discountType = 'rel';
function checkForm() {
	
	
	var err=0;
	var msg2="";
<?php
$reqFields=array(
	"name",
	"phone",
	"email",
	"captcha",
        "dateFrom",
        "dateTo"
	
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

	
if (err==0) {
             
		 return true;
	} else {
		alert("Please complete all highlighted fields to continue.");
		return false;
	}
	
}


	
function checkFieldBack(fieldObj) {
	if (fieldObj.value!=0) {
		fieldObj.style.backgroundColor='#FFF';
	}
}

function checkAvailability(){
    var serviceId = $("input[name='serviceID']").val();
    var date_From =$('#dateFrom').val();
    var date_To =$('#dateTo').val();
    var couponCode = ($("#couponCode").length)?$("#couponCode").val():'';
    //console.log(date_From)
    //console.log(date_To)
    //console.log(serviceId)
    if(date_To!=''){
        $.getJSON("ajax/checkDayAvailability.php",{dateFrom:date_From,dateTo:date_To,serviceID:serviceId,couponCode:couponCode},function(data){
            //console.log(data)
            if(!data.responce){
                addMessage(data.message,'error');
                $("#submit_button").hide();
            }else{
                var el=$('#feeValue');
                var elOld=$('#feeValueOld');
                var elMess = $('#fee_message');
                var elMessCont = $('#fee_message_text');
                var price = data.price;
                    price = price<0?0:price;
                var oldPrice = data.price_old;
                if(data.qty>1){
                    
                        elMess.fadeIn('slow');
                        elMessCont.html(data.message);
                }else{
                        elMess.hide();
                }

                el.html('<?php echo (getOption('currency_position')=='b'?getOption('currency')." ":"")?>'+price.toFixed(2)+' <?php echo (getOption('currency_position')=='a'?getOption('currency'):"")?>');
                if(oldPrice>0)
                 elOld.html('<?php echo (getOption('currency_position')=='b'?getOption('currency')." ":"")?>'+oldPrice.toFixed(2)+' <?php echo (getOption('currency_position')=='a'?getOption('currency'):"")?>');
                $("#submit_button").css('display','block');
            }
        })
    }
}

$(function(){
	
		
		//checkAvailability();

                 $("#couponCode").bind("blur",function(){
                    //console.log($(this).val());
                    var serviceId = $("input[name='serviceID']").val();
                    var code = $(this).val();
                    if(code.length>0){
                    $.getJSON("ajax/checkCoupon.php",{serviceID:serviceId,couponCode:code},function(data){
                        //console.log(data);
                        if(!data.responce){
                            addMessage(data.message,'error');
                            //$("#submit_button").hide();
                            discount = 0;
                            discountType = "";
                            $("#discountDetails").html("");
                            checkAvailability();
                        }else{
                            //$("#submit_button").show();
                            discount = data.value;
                            discountType = data.type;
                            $("#discountDetails").html(discountType=='abs'?'<?php echo (getOption('currency_position')=='b'?getOption('currency')." ":"")?>'+formatNumber(discount)+'<?php echo (getOption('currency_position')=='a'?getOption('currency')." ":"")?>':discount+" %");
                            checkAvailability();
                        }
                    })
                    }
                })
    if($("#couponCode").length && $("#couponCode").val()!=''){
        $("#couponCode").trigger("blur")
    }else{
        checkAvailability();
    }
})

//-->
</script> 