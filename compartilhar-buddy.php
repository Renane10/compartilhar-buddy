<?php
/**
 * Plugin Name:     Compartilhar Buddy
 * Plugin URI:      https://github.com/Renane10/compartilhar-buddy
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
	add_action('bp_activity_entry_meta', 'cb_exibir_botao_compartilhar');
}
add_action('bp_init', 'compartilharBuddy_botao_compartilhar');

// Exibir o botão de compartilhamento
function cb_exibir_botao_compartilhar() {
	if (is_user_logged_in()) {
		$activity_id = bp_get_activity_id();
		$url = add_query_arg('activity_id', $activity_id, home_url('/compartilhar-pagina/'));
		echo '<a href="#" class="botao-compartilhar" data-url="' . esc_url($url) . '">COMPARTILHAR</a>';
	}
}

// Registrar o estilo e o script
function compartilharBuddy_estilo_script() {
	wp_register_style('cb-estilo', plugins_url('assets/compartilhamento.css', __FILE__));
	wp_register_style('cb-toastr-css', plugins_url('assets/toastr.min.css', __FILE__));
	wp_register_script('cb-toastr', plugins_url('assets/toastr.min.js', __FILE__), array('jquery'), '2.1.4', true);
	wp_register_script('cb-script', plugins_url('assets/compartilhamento.js', __FILE__), array('jquery', 'cb-toastr'), '1.0', true);

	// Adicionar estilo e script do Toastr
	wp_enqueue_style('cb-estilo');
	wp_enqueue_style('cb-toastr-css');
	wp_enqueue_script('cb-toastr');
	wp_enqueue_script('cb-script');
}

add_action('bp_init', 'compartilharBuddy_estilo_script');

// Adicionar o estilo e o script à página
function cb_adicionar_estilo_script() {
	wp_enqueue_style('cb-estilo');
	wp_enqueue_script('cb-toastr');
	wp_enqueue_script('cb-script');
}

function compartilhar_buddy_shortcode() {
	global $wpdb;

	// Execute a consulta no banco de dados para obter os dados desejados
	$query = $wpdb->prepare("SELECT a.user_id, a.type, a.content, u.display_name, p.photo_url
    FROM wp_bp_activity AS a
    INNER JOIN wp_users AS u ON a.user_id = u.ID
    LEFT JOIN wp_bp_activity_photos AS p ON a.id = p.activity_id
    WHERE a.id = %d
        AND (a.type = 'activity_update' OR a.type = 'activity_photo' OR a.type = 'activity_video')
    LIMIT 1", $_GET['activity_id']);

	$result = $wpdb->get_results($query);

	// Inicie a saída
	$output = '';

	// Verifique se existem resultados
	if ($result) {
		// Processar os dados e adicioná-los à saída
		$output .= '<div class="compartilhar-buddy-container">';
		$output .= '<h2>' . $result[0]->display_name . ' compartilhou esta publicação com você</h2>';

		// Verificar o tipo da atividade
		if ($result[0]->type === 'activity_photo') {
			$output .= '<img src="' . $result[0]->photo_url . '" alt="Activity Photo">';
		} elseif ($result[0]->type === 'activity_video') {
			$output .= '<video src="' . $result[0]->content . '" controls></video>';
		} else {
			$output .= '<p class="compartilhar-buddy-content">' . $result[0]->content . '</p>';
		}

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


// Shortcode para exibir o conteúdo da atividade
function compartilhar_buddy_content_shortcode() {
	global $wpdb;

	// Execute a consulta no banco de dados para obter o conteúdo da atividade
	$query = "SELECT content FROM wp_bp_activity WHERE id = " . $_GET['activity_id'] . " LIMIT 1";

	$result = $wpdb->get_var($query);

	// Retorne o conteúdo
	return $result;
}
add_shortcode('compartilhar_buddy_content', 'compartilhar_buddy_content_shortcode');

// Shortcode para exibir o nome de usuário
function compartilhar_buddy_username_shortcode() {
	global $wpdb;

	// Execute a consulta no banco de dados para obter o nome de usuário
	$query = "SELECT u.display_name
        FROM wp_bp_activity AS a
        INNER JOIN wp_users AS u ON a.user_id = u.ID
        WHERE a.id = " . $_GET['activity_id'] . " LIMIT 1";

	$result = $wpdb->get_var($query);

	// Retorne o nome de usuário
	return $result;
}

add_shortcode('compartilhar_buddy_user', 'compartilhar_buddy_username_shortcode');

add_action('bp_init', 'cb_adicionar_estilo_script');
