function fv_autolista_autoload(src, dest, ajax) {
	var cbd = $(document.getElementById(dest));
	cbd.attr('disabled','disabled').empty().change();
	var val = $(document.getElementById(src)).val();
	if (val != null && val != '') {
		cbd.addClass('waiting');
		$.getJSON(path_ajax(ajax), 'id='+val, function(data) {
			var cbd = $(document.getElementById(dest));
			cbd.removeClass('waiting').empty().append($('<option value="">Scegli...</option>'));
			$.each(data, function(id, val) {
				cbd.append($('<option value="'+id+'">'+val+'</option>'));
			});
			cbd.removeAttr('disabled').trigger('fv_autoload');
		});
	}
}
