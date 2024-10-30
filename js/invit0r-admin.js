var $invit0r = jQuery.noConflict();

$invit0r(document).ready(function($){

	$('input[name=invit0r_use_wp_cron]').click(function(){

		if ($('.yes').hasClass('show')) {
			$('.yes').fadeOut('slow', function(){$(this).removeClass('show').addClass('hidden');});
		} else {
			$('.yes').fadeIn('slow', function(){$(this).removeClass('hidden').addClass('show');});
		}

		if ($('.no').hasClass('show')) {
			$('.no').fadeOut('slow', function(){$(this).removeClass('show').addClass('hidden');});
		} else {
			$('.no').fadeIn('slow', function(){$(this).removeClass('hidden').addClass('show');});
		}

	});

	$('#invit0r_chart_switch_1').click(function(){
		$('#invit0r_chart_switch a').removeClass('invit0r_chart_switch_active');
		$(this).addClass('invit0r_chart_switch_active');
		$('.invit0r_chart').hide();
		$('#invit0r_chart_1').parents('div').show();
		return false;
	});

	$('#invit0r_chart_switch_7').click(function(){
		$('#invit0r_chart_switch a').removeClass('invit0r_chart_switch_active');
		$(this).addClass('invit0r_chart_switch_active');
		$('.invit0r_chart').hide();
		$('#invit0r_chart_7').parents('div').show();
		return false;
	});

	$('#invit0r_chart_switch_31').click(function(){
		$('#invit0r_chart_switch a').removeClass('invit0r_chart_switch_active');
		$(this).addClass('invit0r_chart_switch_active');
		$('.invit0r_chart').hide();
		$('#invit0r_chart_31').parents('div').show();
		return false;
	});

});

var scriptSource = (function(scripts) {
	var scripts = document.getElementsByTagName('script'),
		script = scripts[scripts.length - 1];

	if (script.getAttribute.length !== undefined) {
		return script.src
	}

	return script.getAttribute('src', -1)
}());

var invit0r_dir = scriptSource.replace('js/invit0r-admin.js', '')

swfobject.embedSWF(
invit0r_dir + "open-flash-chart.swf", "invit0r_chart_1", "700", "200",
"9.0.0", "expressInstall.swf",
{"data-file": invit0r_dir + "stats-data-file.php"} );

swfobject.embedSWF(
invit0r_dir + "open-flash-chart.swf", "invit0r_chart_7", "700", "200",
"9.0.0", "expressInstall.swf",
{"data-file": invit0r_dir + "stats-data-file.php?unit=7"} );

swfobject.embedSWF(
invit0r_dir + "open-flash-chart.swf", "invit0r_chart_31", "700", "200",
"9.0.0", "expressInstall.swf",
{"data-file": invit0r_dir + "stats-data-file.php?unit=31"} );
