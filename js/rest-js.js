function send()
{
	$(".btn-send").button('loading');
	$("#result").removeClass('alert-success');
	$("#result").removeClass('alert-error');
	$("#result").html('/* Resultat de la requ&ecirc;te */');

	$('input[type=hidden]').remove();

	if($("#ajax_crossdomain").is(':checked')) {
		$('input.input-name').each(function(i, input) { 
			$("#data").append(
				'<input type="hidden" name="' + $(input).val() + '" value="' + $('#' + input.parentNode.id + ' input.input-value').val() + '" />'
			);
		});

		$.ajax({
			url: $("#link").val() + $("#entry").val(),
			cache: false,
			data: $("#data").serialize(),
			type: $("#method").val(),
			success: function(data) {
				$(".btn-send").button('reset');
				$("#result").addClass('alert-success');
				$("#result").html(JSON.stringify(data, undefined, 2));
			},
			error: function(data) {
				$("#result").addClass('alert-error');
				$(".btn-send").button('reset');

				try {
					var json_data = JSON.parse(data.responseText);
					$("#result").html(JSON.stringify(json_data, undefined, 2));
				} catch(e) {
					$("#result").html(data.responseText);
				}
			}
		});
	} else {
		var data = {};
		data['url'] = $("#link").val() + $("#entry").val();
		data['method'] = $("#method").val();
		$('input.input-name').each(function(i, input) { 
			data[$(input).val()] = $('#' + input.parentNode.id + ' input.input-value').val();
		});

		console.log(data);

		$.ajax({
			url: 'rest_client.php',
			cache: false,
			data: data,
			type: 'POST',
			success: function(data) {
				$(".btn-send").button('reset');
				$("#result").addClass('alert-success');
				$("#result").html(JSON.stringify(data, undefined, 2));
			},
			error: function(data) {
				$("#result").addClass('alert-error');
				$(".btn-send").button('reset');

				try {
					var json_data = JSON.parse(data.responseText);
					$("#result").html(JSON.stringify(json_data, undefined, 2));
				} catch(e) {
					$("#result").html(data.responseText);
				}
			}
		});
	}
}

function add_param()
{
	var id = Math.round(new Date().getTime() / 1000);
	$(".btn-addparam").before(
		'<div id=' + id + '>' +
			'<input type="text" class="input-name" placeholder="Param&egrave;tre"> ' +
			'<input type="text" class="input-value" placeholder="Valeur"> ' +
			'<a href="#" class="btn btn-danger" onclick="remove_param(' + id + ')">Supprimer</a>' +
		'</div>'
	);
}

function remove_param(id) {
	$("#" + id).remove();
}

function reset() {
	$("#data").html(
		'<div id="first-param">' +
			'<input type="text" class="input-name" placeholder="Param&egrave;tre"> ' +
			'<input type="text" class="input-value" placeholder="Valeur"> ' +
		'</div>' + 
		'<a href="#" class="btn btn-primary btn-addparam" onclick="add_param()">Ajouter un param&egrave;tre</a> ' +
		'<a href="#" class="btn btn-warning" onclick="reset()">Reset</a>'
	);
}
