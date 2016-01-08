/**
 * Admin
 *
 * for admin pages
 */
var Admin = {};


/**
 * Navigation
 *
 * @return void
 */
Admin.navigation = function() {
	if (typeof $.prototype.supersubs == 'function') {
		$('ul.sf-menu').supersubs({
			minWidth: 12,
			maxWidth: 27,
			extraWidth: 1
		}).superfish({
			delay: 200,
			animation: {opacity:'show',height:'show'},
			speed: 'fast',
			autoArrows: true,
			dropShadows: false,
			disableHI: true
		});
	}

	var $sidebar = $('#sidebar-menu');
	var $topLevelMenus = $('#sidebar-menu > li > .hasChild');
	$topLevelMenus.attr('href','javascript:void(0)');
	// no item is current, fallback to current controller index
	var $current = $('.sidebar .current');
	if ($current.length == 0) {
		$('#sidebar-menu').children(":first").children('a').addClass('current');
	}
	// traverse parent elements and mark as current
	$($current.selector).parentsUntil('.sidebar', 'ul').each(function() {
		$(this).siblings('a.sidebar-item').addClass('current')
	});
	if (window.innerWidth >= 979) {
		$topLevelMenus.parent().find('> .current').next('ul').toggle();
	}

	var dropdownOpen = function() {
		$(this)
			.addClass('dropdown-open')
			.removeClass('dropdown-close')
			.siblings('.sidebar-item')
			.addClass('dropdown-open')
			.removeClass('dropdown-close');
	};

	var dropdownClose = function() {
		$(this)
			.addClass('dropdown-close')
			.removeClass('dropdown-open')
			.siblings('.sidebar-item')
			.addClass('dropdown-close')
			.removeClass('dropdown-open');
	};

	$topLevelMenus.on('click blur', function(e) {
		var $this = $(this);
		var $ul = $(this).next('ul');
		var sidebarWidth = $sidebar.width();

		if (e.type == 'blur' && window.innerWidth > 979) {
			return;
		}

		if ($ul.is(':visible')) {

			var onComplete = function() {
				dropdownClose.call($ul.get(0));
				$ul.css({'margin-left': sidebarWidth + 'px', 'margin-top': 'inherit'})
			}

			if (window.innerWidth <= 979) {
				$ul.fadeOut('fast', onComplete);
			} else {
				$ul.slideUp('fast', onComplete);
			}
		} else {
			$topLevelMenus.siblings('ul:visible').slideUp('fast', function() {
				dropdownClose.call(this);
			});
			dropdownOpen.call(this);
			if (window.innerWidth <= 979) {
				$ul.css({'position': 'absolute', 'margin-left': sidebarWidth + 'px', 'margin-top': '-41px'});
				$ul.fadeIn('fast');
			} else {
				$ul.css({'margin-left': 0, 'position': 'relative'});
				$ul.slideDown('fast');
			}
		}
		e.stopPropagation();
		return false;
	});

	$(window).on('resize', function() {
		$('#sidebar-menu > li ul:visible').each(function() {
			$(this).toggle();
			dropdownClose.call(this);
		});
	});
}


/**
 * Forms
 *
 * @return void
 */
Admin.form = function() {
	// Tooltips activation
	$('[rel=tooltip],*[data-title]:not([data-content]),input[title],textarea[title]').tooltip();
	if (typeof $.prototype.tipsy == 'function') {
		$('a.tooltip').tipsy({gravity: 's', html: false}); // Legacy tooltip
	}

	var ajaxToggle = function(e) {
		var $this = $(this);
		$this.addClass('icon-spinner icon-spin').find('i').attr('class', 'icon-none');
		var url = $this.data('url');
		$.post(url, function(data) {
			$this.parent().html(data);
		});
	}

	// Row Actions
	$('body')
		.on('click', 'a[data-row-action]', Admin.processLink)
		.on('click', 'a.ajax-toggle', ajaxToggle)
	;
}

/**
 * Helper to process row action links
 */
Admin.processLink = function(event) {
	var $el = $(event.currentTarget);
	var checkbox = $(event.currentTarget.attributes["href"].value);
	var form = checkbox.get(0).form;
	var action = $el.data('row-action');
	var confirmMessage = $el.data('confirm-message');
	if (confirmMessage && !confirm(confirmMessage)) {
		return false;
	}
	$('input[type=checkbox]', form).prop('checked', false);
	checkbox.prop("checked", true);
	$('#bulk-action select', form).val(action);
	form.submit();
	return false;
}

/**
 * Extra stuff
 *
 * rounded corners, striped table rows, etc
 *
 * @return void
 */
Admin.extra = function() {
	// Activates the first tab in #content
	$('#content .nav-tabs > li:first-child a').tab('show');

	// Box toggle
	$('body').on('click', '.box-title', function() {
		$(this).next().slideToggle();
	});

	if (typeof $.prototype.tabs == 'function') {
		$('.tabs').tabs(); // legacy tabs from jquery-ui
	}
	if (typeof $.prototype.elastic == 'function') {
		$('textarea').not('.content').elastic();
	}
	$("div.message").addClass("notice");
	$('#loading p').addClass('ui-corner-bl ui-corner-br');
}
function submitPermission(el, act){
	var form = el.parent().parent().prev();
	var tr = el.parent().parent();
	BootstrapDialog.confirm('Are you sure?', function(result){
		if(result){
			$.ajax({
				type: "POST",
				url: form.attr('action'),
				data: form.serialize() + '&action=' + act,
				dataType: "json",
				success: function(response){
					if(response){
						if(act == 'set' && (response.success == 1)){
							el.next('input').remove();
							el.after('<input onclick="submitPermission($(this), \'reset\');" type="button" class="btn btn-xs btn-danger" name="reset" value="Reset"/>');
						}
						if(act == 'reset' && (response.success == 1)){
							el.remove();
							tr.find('.input_read'+ response._read).prop("checked", true);
							tr.find('.input_update'+ response._update).prop("checked", true);
							tr.find('.input_delete'+ response._delete).prop("checked", true);
							tr.find('.input_create'+ response._create).prop("checked", true);
						}
					}
				}
			});
		}else {
			return false;
		}
	});
}

function modiferPrice(){
	var modify = $('#modifer-discount-rate').val();
	if(!modify){
		BootstrapDialog.alert('Please fill a modifer number.');
	}else if(modify > 100){
		BootstrapDialog.alert('Max discount is 100%!');
	}else{
		$('.service_amount_new').each(function(){
			var standardVal = $(this).attr('default-data');
			var newVal = standardVal * (100 - modify) / 100;
			$(this).val(newVal.toFixed(2));
		});
	}
}

function exportToCSV(el, model){
	var urlval = 'Export[model]=' + model;
	var newhref = '';
	var input = el.parent().parent().find('label.checkbox.checked');
	input.each(function(){
		var keyname = $(this).children('.fieldname').val();
		var keyhead = $(this).children('.fieldheader').val();
		urlval += '&Export[fields]['+ keyname +']=' + keyhead;
	});
	if(window.location.search){
		newhref = window.location.href + '&' + urlval;
	}else{
		newhref = window.location.href + '?' + urlval;
	}
	
	BootstrapDialog.confirm = function(message, callback) {
		new BootstrapDialog({
			title: 'Confirmation',
			message: message,
			closable: true,
			data: {
				'callback': callback
			},
			buttons: [{
					label: 'CSV',
					cssClass: 'btn-success',
					action: function(dialog) {
						typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
						dialog.close();
					}
				}, {
					label: 'Excel',
					cssClass: 'btn-primary',
					action: function(dialog) {
						typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
						dialog.close();
					}
				}]
		}).open();
	};
	BootstrapDialog.confirm('Please choose type which you want to export!', function(result){
		if(result) {
			newhref = newhref + '&type=1';
			// console.log(newhref);
			window.location.href = newhref;
		}else {
			newhref = newhref + '&type=0';
			// console.log(newhref);
			window.location.href = newhref;
		}
	});

}
/**
 * Document ready
 *
 * @return void
 */
$(document).ready(function() {
	Admin.navigation();
	Admin.form();
	Admin.extra();
	$( "#show-search" ).click(function() {
	  $( "#search" ).toggle( "slow" );
	});
	$( "#show-export" ).click(function() {
	  $( "#export-options" ).toggle( "slow" );
	});
	// Ladda.bind( '.ladda-button', { timeout: 2000 } );
});
