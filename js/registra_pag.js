registra_pag = { _sel: 0, _ins: 0, _tot: 0 };

//ricalcola i prezzi selezionati
registra_pag.ricalcola = function() {
	registra_pag._sel = 0;
	
	$('.chk-sett').each(function() {
		var chkSett = $(this);
		var idsett = chkSett.data('idsett');
		var totsett = 0;
		if (this.checked) {
			totsett = chkSett.data('prezzo');
			$('.chk-sett_'+idsett+':checked').each(function() {
				if (this.checked)
					totsett += $(this).data('prezzo');
			});
		}
		registra_pag._sel += totsett;
		//mostra il totale del settore
		$('#totsett_'+idsett).text(registra_pag.euro(totsett)); 
	});
	
	registra_pag.mostraSelezione();
};

//modifica il totale inserito
registra_pag.cambiaTotale = function(err) {
	var err = (err != undefined && err == true);
	$('.err_msg').hide();
	
	var totEl = $('#form_totale');
	var tot = totEl.val();
	if (tot == '') {
		tot = 0;
		if (err) $('#err_obblig').show();
	} else {
		if (!registra_pag.regex.test(tot)) {
			if (err) $('#err_format').show();
			return false;
		}
		tot = tot.replace(",",".");
		if (!$.isNumeric(tot))
			return false;
	}
	
	//se il nuovo valore è il totale da pagare selezina tutto
	if (tot != registra_pag._ins && tot == registra_pag._tot) {
		registra_pag.selezionaTutto();
	}
	registra_pag._ins = tot;
	
	registra_pag.mostraSelezione();
	return true;
}

//mostra i valori nei campi "Selezionato" e "Da selezionare"
registra_pag.mostraSelezione = function() {
	//mostra il totale selezionato
	$('#totsel').text(registra_pag.euro(registra_pag._sel));
	
	//mostra il totale da selezionare
	var dasel = registra_pag._ins - registra_pag._sel
	var parent = $('#totnonsel').text(registra_pag.euro(dasel)).parent();
	if (dasel < 0) 
		parent.removeClass('text-success').addClass('text-error');
	else if (dasel > 0)
		parent.removeClass('text-error').addClass('text-success');
	else
		parent.removeClass('text-error').removeClass('text-success');
	
	//modifica il colore del pulsante
	if (registra_pag.isValido(false)) 
		$('#form_invia').removeClass('btn-danger').addClass('btn-success');
	else
		$('#form_invia').removeClass('btn-success').addClass('btn-danger');	
}

//formatta un numero come 0,00
registra_pag.euro = function(val) {
	return val.toFixed(2).replace(".",","); 
};

//seleziona tutti i checkbox
registra_pag.selezionaTutto = function() {
	$('.chk-prezzo').prop('checked',true);
	$('.tab_sett').show();
	registra_pag.ricalcola();
}

//deseleziona tutti i checkbox abilitati
registra_pag.deselezionaTutto = function() {
	$('.chk-tess').prop('checked',false);
	$('.chk-sett:not([disabled])').prop('checked',false);
	registra_pag.mostraTabSelezionate();
	registra_pag.ricalcola();
}

//verifica se il prezzo selezionato è uguale a quello inserito
registra_pag.isValido = function(ricalcola, err) {
	var err = (err != undefined && err == true);
	if (err) $('.err_msg').hide();

	var totok = true;
	if (ricalcola == undefined || ricalcola == true) {
		totok = registra_pag.cambiaTotale(err);
		registra_pag.ricalcola();
	}
	
	if (registra_pag._ins <= 0) {
		return false;
	}
	if (registra_pag._ins != registra_pag._sel) {
		if (err && totok) $('#err_sel').show('fast');
		return false;
	}
	return true;
};

//nasconde le tabelle dei settori non selezionati
registra_pag.mostraTabSelezionate = function() {
	$('.tab_sett').hide();
	$('.chk-sett:checked').each(function() {
		$('#tab_sett'+$(this).data('idsett')).toggle(this.checked);
	});
}

registra_pag.init = function(totale) {
	registra_pag._tot = totale;
	var totEl = $('#form_totale');
	registra_pag.regex = new RegExp(totEl.attr('pattern'));
	
	//controllo del submit
	$('#pagamenti').submit(function(event) {
		if (registra_pag.isValido(true, true)) 
			return true;
		event.preventDefault();
		return false;
	})
	
	//alla modifica del campo totale
	totEl.keyup(registra_pag.cambiaTotale).blur(registra_pag.cambiaTotale);
	//alla selezione di un settore
	$('.chk-sett').change(function() {
		if (this.checked)
			$('#tab_sett'+$(this).data('idsett')).show('fast');
		else
			$('#tab_sett'+$(this).data('idsett')).hide('fast');
		registra_pag.ricalcola();
	});
	//alla selezione di un tesserato
	$('.chk-tess').change(registra_pag.ricalcola);
	
	registra_pag.mostraTabSelezionate();
	
	registra_pag.cambiaTotale();
	registra_pag.ricalcola();
	waitingview_ready();
};