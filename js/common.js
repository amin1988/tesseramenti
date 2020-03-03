
$(document).ready(function () {
	$('#dialog-segnala').modal({ show: false });
});

// ########################## SEGNALAZIONI ###############################

function mostraSegnalazione() {
	initSegnala();
	$('#dialog-segnala').modal('show');
	$('#segnala_msg').focus();
}

function initSegnala() {
	$('#dialog-segnala .alert').hide();
	$('#dialog-segnala input, #dialog-segnala textarea').removeAttr('disabled');
	$('#invia-segnalazione').empty().append('Invia').removeClass('disabled waiting ');
}

function segnalaErr(jqXHR, textStatus, errorThrown) {
	initSegnala();
	$('#segnalazione_no').show();
}

function segnalaOk(data, textStatus, jqXHR) {
	if (data == '1') {
		initSegnala();
		$('#dialog-segnala .alert').hide();
		$('#segnalazione_ok').show();
		$('#segnala_msg').val('');
	} else {
		segnalaErr(jqXHR, textStatus, data);
	}
}

function inviaSegnalazione() {
	var but = $('#invia-segnalazione');
	if (but.hasClass('disabled')) return;
	
	$('#dialog-segnala .alert').hide();
	if ($.trim($('#segnala_msg').val()).length == 0) {
		$('#segnalazione_obblig').show();
		$('#segnala_msg').focus();
		return;
	}
	var form = $('#form-segnala');
	var data = form.serialize()+"&pagina="+encodeURIComponent(location.href);
	
	but.empty().append('Invio...').addClass('disabled waiting ');
	form.children('input, textarea').attr('disabled','disabled');
	
	$.post(path_ajax('segnala'), data).done(segnalaOk).fail(segnalaErr);
}
