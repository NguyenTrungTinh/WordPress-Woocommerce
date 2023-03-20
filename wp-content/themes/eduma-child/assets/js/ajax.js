jQuery(document).ready(function($){
	// Quick view ajax function on products page
	$('.quick-view-link, .quick-view-button').on('click', function() {
	var post_id = $(this).data('id');
	alert(post_id);
	$.ajax({
	url : modal_ajax.url,
	type : 'post',
	data : {
	post_id : post_id,
	action : 'fetch_modal_content',
	security : modal_ajax.nonce,
	},
	success : function(response) {
	$('#modal').modal('show');
	$('#modal_target').html(response);
}
});
});
});
