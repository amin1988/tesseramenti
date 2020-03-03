
function toggleTipiBox(idtess, check) {
	if (check.checked) 
		$('#box-tess-'+idtess).show('blind', 'fast');
	else
		$('#box-tess-'+idtess).hide('blind', 'fast');
}

function rinnovaOnLoad() {
	$('.box-tipi.nascondi').hide();
	$('.chk_tipo').change(function() {
		var th = $(this);
		$('#grado_'+th.data('tess')+'_'+th.data('tipo')).toggle(th.prop('checked'));
	}).change();
	waitingview_ready();
}