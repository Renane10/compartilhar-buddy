<?php
/**
 * Plugin Name:     Compartilhar Buddy
 * Plugin URI:      www.rtechmkt.com.br
 * Description:     Plugin para compartilhamentos de posts do buddypress para não cadastrados
 * Author:          Prozyn10
 * Author URI:      www.rtechmkt.com.br
 * Text Domain:     compartilhar-buddy
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Compartilhar_Buddy
 */
// Registrar o botão de compartilhamento
function compartilharBuddy_botao_compartilhar() {
	add_action('bp_activity_entry_meta', 'meu_plugin_exibir_botao_compartilhar');
}
add_action('bp_init', 'compartilharBuddy_botao_compartilhar');

// Exibir o botão de compartilhamento
function meu_plugin_exibir_botao_compartilhar() {
	if (is_user_logged_in()) {
		$activity_id = bp_get_activity_id();
		$url = add_query_arg('activity_id', $activity_id, home_url('/compartilhar-pagina/'));
		echo '<a href="#" class="botao-compartilhar" data-url="' . $url . '">COMPARTILHAR</a>';
	}
}

// Registrar o estilo e o script
function compartilharBuddy_estilo_script() {
	wp_register_style('meu-plugin-estilo', plugins_url('compartilhamento.css', __FILE__));
	wp_register_script('meu-plugin-toastr', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js', array('jquery'), '2.1.4', true);
	wp_register_style('meu-plugin-toastr-css', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css');

	wp_register_script('meu-plugin-script', plugins_url('compartilhamento.js', __FILE__), array('jquery', 'meu-plugin-toastr'), '1.0', true);

	// Adicionar estilo e script do Toastr
	wp_enqueue_style('meu-plugin-estilo');
	wp_enqueue_style('meu-plugin-toastr-css');
	wp_enqueue_script('meu-plugin-toastr');
	wp_enqueue_script('meu-plugin-script');
}

add_action('bp_init', 'compartilharBuddy_estilo_script');

// Adicionar o estilo e o script à página
function meu_plugin_adicionar_estilo_script() {
	wp_enqueue_style('meu-plugin-estilo');
	wp_enqueue_script('meu-plugin-toastr');
	wp_enqueue_script('meu-plugin-script');
}

function compartilhar_buddy_shortcode() {
	global $wpdb;

	// Execute a consulta no banco de dados para obter os dados desejados
	$query = "SELECT a.user_id, a.content, u.display_name
		FROM wp_bp_activity AS a
		INNER JOIN wp_users AS u ON a.user_id = u.ID
		WHERE a.id = " . $_GET['activity_id']." LIMIT 1";

	$result = $wpdb->get_results($query);

	// Inicie a saída
	$output = '';
	// Verifique se existem resultados
	if ($result) {
		// Processar os dados e adicioná-los à saída
		$output .= '<div class="compartilhar-buddy-container">';
		$output .= '<h2>' . $result[0]->display_name . ' compartilhou esta publicação com você</h2>';
		$output .= '<p class="compartilhar-buddy-content">' . $result[0]->content . '</p>';
		$output .= '</div>';
		$output .= '<style>.compartilhar-buddy-container {display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px solid #000;} .compartilhar-buddy-content {text-align: center;}</style>';
	} else {
		// Caso não haja resultados
		$output .= '<p>Nenhum dado encontrado.</p>';
	}

	// Retorne a saída
	return $output;
}
add_shortcode('compartilhar_buddy', 'compartilhar_buddy_shortcode');

add_action('bp_init', 'meu_plugin_adicionar_estilo_script');
