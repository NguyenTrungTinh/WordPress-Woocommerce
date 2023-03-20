<?php do_action( 'thim_above_footer_area' ); ?>

<footer id="colophon" class="<?php thim_footer_class(); ?>">
	<?php if ( is_active_sidebar( 'footer' ) ) : ?>
        <div class="footer">
            <div class="container">
                <div class="row">
					<?php dynamic_sidebar( 'footer' ); ?>
                </div>
            </div>
        </div>
	<?php endif; ?>

	<?php do_action( 'thim_copyright_area' ); ?>

</footer><!-- #colophon -->
</div><!--end main-content-->

<?php do_action( 'thim_end_content_pusher' ); ?>

</div><!-- end content-pusher-->

<?php do_action( 'thim_end_wrapper_container' ); ?>


</div><!-- end wrapper-container -->

<?php wp_footer(); ?>


</body>

</html>

<script type="text/javascript">
	
	jQuery(document).ready(function(){
	//	jQuery('#popup').fancybox();
	//Fancybox.show([{src: "popup", type: "inline"}]);
	});
</script>
<script type="text/javascript">
        var geocoder = new GoongGeocoder({
        accessToken: 'QCcde2OFpTDlPnMtmTq3KN95xtL2yuDaovM6rdMz'
    });
    geocoder.addTo('#location');
    var results = document.getElementById('result');
 	geocoder.on('result', function (e) {
      var address = results.innerText = JSON.stringify(e.result, null, 2);
       var obj = JSON.parse(address);
       function printValues(obj){
        for(var k in obj){
            if(obj[k] instanceof Object){
                printValues(obj[k]);
            }
            else{
                document.write(obj[k] + "<br>");
            }
        }
       };
    //   printValues(obj);
    //   console.log(obj["result"]["formatted_address"]);
       var json_address = obj["result"]["formatted_address"];
       document.getElementById("billing_address_1").value = json_address;
    //   document.getElementById("address_map").value = json_address;
       //alert(json_address);
    }); 
    // Clear results container when search is cleared.
    geocoder.on('clear', function () {
        results.innerText = '';
    });
</script>
