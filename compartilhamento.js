jQuery(document).ready(function($) {
	// Quando o documento estiver carregado
	$(document).on('click', '.botao-compartilhar', function(e) {
		e.preventDefault();

		// Obter a URL a ser copiada
		var url = $(this).data('url');

		// Copiar a URL para a área de transferência
		copyToClipboard(url);

		// Exibir o toast de sucesso
		showToast('URL copiada para a área de transferência!', 'success');
	});

	// Função para copiar o texto para a área de transferência
	function copyToClipboard(text) {
		var tempInput = document.createElement('input');
		tempInput.value = text;
		document.body.appendChild(tempInput);
		tempInput.select();
		document.execCommand('copy');
		document.body.removeChild(tempInput);
	}

	// Função para exibir o Toastr
	function showToast(message, type) {
		toastr.options.positionClass = 'toast-top-center'; // Definir a classe de posição para o centro
		toastr[type](message);
	}
});
