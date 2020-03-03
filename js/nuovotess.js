var nuovoTesseratoPop = {};

nuovoTesseratoPop.ruolo = '';
nuovoTesseratoPop.id = 0;
	
nuovoTesseratoPop.mostra = function(ruolo)
{
	this.ruolo = ruolo;
	this.svuota();
	this.setId();
	$('#dialog-nuovo-tesserato').modal('show');
};

nuovoTesseratoPop.setId = function() {
	this.id = new Date().getTime();
	$('#form_nuovo_tesserato_id').val(this.id);
};
	
nuovoTesseratoPop.init = function()
{
	$('#dialog-nuovo-tesserato .alert').hide();
	$('#invia-nuovo-tesserato').empty().append('Nuovo').removeClass('disabled waiting ');
	$('#nuovo_tesserato').show();	
};

nuovoTesseratoPop.svuotaErr = function(){
	$('#nuovo_tesserato .errore').empty();
};
	
nuovoTesseratoPop.svuota = function(){
	this.svuotaErr();
	$('#nuovo_tesserato input[type!=checkbox]').val('').change();
	$('#nuovo_tesserato input[type=checkbox]').removeAttr('checked').change();
	$('#nuovo_tesserato select').val('').change();
	this.init();
};
	
nuovoTesseratoPop.invia = function() {
	var but = $('#invia-nuovo-tesserato');
	if (but.hasClass('disabled')) return;
	
	$('#dialog-nuovo-tesserato .alert').hide();
	var form = $('#nuovo_tesserato');
	
	but.empty().append('Invio...').addClass('disabled waiting ');
	form.hide();
	$('#dialog-nuovo-tesserato .alert.inviando').show();
	
	$.post(path_ajax('nuovotess'), form.serialize(), nuovoTesseratoPop.ajaxOk, 'json').fail(this.ajaxErr);
};

nuovoTesseratoPop.ajaxErr = function(jqXHR, textStatus, errorThrown) {
	nuovoTesseratoPop.init();
	nuovoTesseratoPop.setId();
	$('#dialog-nuovo-tesserato .alert.errore-invio').show();
	
};
	
nuovoTesseratoPop.ajaxOk = function(data, textStatus, jqXHR) {
	if (data.stato == 0) {
		nuovoTesseratoPop.init();
		nuovoTesseratoPop.svuotaErr();
		nuovoTesseratoPop.setId();
		//errori nei dati
		for (var el in data.err) {
			$(document.getElementById('form_'+el)).siblings('.errore').empty().append(data.err[el]);
		}
	} else if (data.stato == 1) {	
		//aggiunge il nuovo a tutti gli elenchi
		$('#consiglio_mod select').append($('<option value="'+data.idtess+'">'
				+data.nome+'</option>'));
		//TODO ordinare elenchi
		//seleziona in ruolo
		$('#form_'+nuovoTesseratoPop.ruolo).val(data.idtess);
		$('#dialog-nuovo-tesserato').modal('hide');
	} else {
		if (data.idform == this.id)
			nuovoTesseratoPop.ajaxErr(jqXHR, textStatus, '');
	}
};
