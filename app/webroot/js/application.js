// Some general UI pack related JS
// Extend JS String with repeat method
String.prototype.repeat = function(num) {
    return new Array(num + 1).join(this);
};

(function($) {

  // Add segments to a slider
  $.fn.addSliderSegments = function (amount) {
    return this.each(function () {
      var segmentGap = 100 / (amount - 1) + "%"
        , segment = "<div class='ui-slider-segment' style='margin-left: " + segmentGap + ";'></div>";
      $(this).prepend(segment.repeat(amount - 2));
    });
  };

  $(function() {
  
    // Todo list
    $(".todo li").click(function() {
        $(this).toggleClass("todo-done");
    });

    // Custom Selects
    $("select.select-ui-huge").selectpicker({style: 'btn-hg btn-primary', menuStyle: 'dropdown-inverse'});
    $("select.select-ui-primary").selectpicker({style: 'btn-primary', menuStyle: 'dropdown-inverse'});
    $("select.select-ui-info").selectpicker({style: 'btn-info'});

    // Tooltips
    $("[data-toggle=tooltip]").tooltip();
    $(".hasTooltip").tooltip();

    // jQuery UI Sliders
    var $slider = $("#slider");
    if ($slider.length) {
      $slider.slider({
        min: 1,
        max: 5,
        value: 2,
        orientation: "horizontal",
        range: "min"
      }).addSliderSegments($slider.slider("option").max);
    }

    // Make pagination demo work
    $(".pagination a").on('click', function() {
      $(this).parent().siblings("li").removeClass("active").end().addClass("active");
    });
	
	$('form.limitbox select').change(function(){
		$(this).parent().submit();
		$(this).prev('span').html($(this).val());
	});
	$('form.limitbox select').each(function(){
		$(this).prev('span').html($(this).val());
	});
    $(".btn-group a").on('click', function() {
      $(this).siblings().removeClass("active").end().addClass("active");
    });

    // Disable link clicks to prevent page scrolling
    $('a[href="#fakelink"]').on('click', function (e) {
      e.preventDefault();
    });

    // Switch
    $("[data-toggle='switch']").wrap('<div class="switch" />').parent().bootstrapSwitch();
    
  });
 
})(jQuery);

function confirmDialog(link, message){
	BootstrapDialog.confirm(message, function(result){
		if(result){
			window.location.href = link;
		}else {
			return false;
		}
	});
}

function confirmSubmitForm(el, message){
	BootstrapDialog.confirm(message, function(result){
		if(result){
			el.closest('form').submit();
		}else {
			return false;
		}
	});
}
