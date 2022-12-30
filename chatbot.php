<?php
/*
Plugin Name: Chatbot Assistant
Plugin URI: 
Description: A chatbot assistant plugin for WordPress that answers customer questions using the OpenAI chat model.
Version: 1.0
Author: Your Name
Author URI: 
License: GPL2
*/

// Register a shortcode to display the chatbot form
function chatbot_shortcode() {
    ob_start();
    ?>
    <form id="chatbot-form">
        <label for="chatbot-question">Enter your question:</label>
        <?php wp_editor( '', 'chatbot_question', array( 'textarea_name' => 'chatbot_question' ) ); ?>
        <button type="submit" id="chatbot-submit">Submit</button>
    </form>
    <div id="chatbot-response"></div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'chatbot', 'chatbot_shortcode' );

// Enqueue the plugin's JS file
function chatbot_enqueue_scripts() {
    wp_enqueue_script( 'chatbot-script', plugin_dir_url( __FILE__ ) . 'chatbot.js', array( 'jquery' ), '1.0', true );
    wp_localize_script( 'chatbot-script', 'chatbot_ajax_url', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'chatbot_enqueue_scripts' );

// Process the form submission and send the question to the OpenAI chat model
function chatbot_process_form() {
    // Check nonce and capabilities
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'chatbot_nonce' ) || ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Unauthorized request.' );

          }
    // Sanitize and validate form data
    $question = sanitize_text_field( $_POST['question'] );
    if ( empty( $question ) ) {
        wp_send_json_error( 'Please enter a valid question.' );
    }
    // Send the question to the OpenAI chat model and get the response
    $response = openai_chat_model_response( $question );
    // Return the response to the user
    wp_send_json_success( $response );
}
add_action( 'wp_ajax_chatbot_process_form', 'chatbot_process_form' );
add_action( 'wp_ajax_nopriv_chatbot_process_form', 'chatbot_process_form' );

// Function to send the question to the OpenAI chat model and get the response
function openai_chat_model_response( $question ) {
    // Set up the API request
    $api_key = 'YOUR_API_KEY';
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, "https://api.openai.com/v1/chat" );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, "{\"model\": \"YOUR_MODEL_NAME\", \"prompt\": \"$question\"}" );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json", "Authorization: Bearer $api_key" ) );
    // Send the request and get the response
    $response = curl_exec( $ch );
    curl_close( $ch );
    // Decode the response and return it
    $response_decoded = json_decode( $response );
    return $response_decoded->data->response;
}
?>
