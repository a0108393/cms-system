/* =============================================================
 * flatui-radio.js v0.0.3
 * ============================================================ */

!function ($) {

 /* RADIO PUBLIC CLASS DEFINITION
	* ============================== */
$(document).ready(function(){
	
	var $radio = $('input[type="radio"][data-toggle="radio"]');
	$radio.each(function(){
		if($(this).attr('checked')){
			$(this).parent('label').addClass('checked');
		}
	});
	$radio.parent('label').append('<span class="icons"><span class="first-icon fui-radio-unchecked"></span><span class="second-icon fui-radio-checked"></span></span>');
	$('label.radio').children('input[type="hidden"]').remove();
	$('label.radio').click(function(){
		$(this).parent().find('label.radio').removeClass('checked');
		$(this).addClass('checked');
	});
	
});

}(window.jQuery);