f<?php
    /* 
Plugin Name: Form Contact
Plugin URL: https://www.google.com
Description: Un formulario de contacto y mucho más
Version: 1.1.1
Author: Adrian Sanchez
Author: URI: https://www.google.com
License: GLI
License URI: https://www.google.com
Text Domain form_contact
*/

    if (!defined('ABSPATH')) die();

    //Activacion del plugin
    register_activation_hook(__FILE__, function () {
        //Creacion de tablas personalizadas
        global $wpdb;
        $wpdb->query(
            "create table if not exists
        {$wpdb->prefix}form_contact(
            id int not null auto_increment,
            nombre varchar(100),
            correo varchar(100),
            fecha date,
            primary key(id)
        )"
        );

        /* indices de form_contact*/
        $wpdb->query("alter table {$wpdb->prefix}form_contact add index(`nombre`) ");
        $wpdb->query("alter table {$wpdb->prefix}form_contact add index(`correo`) ");

        $wpdb->query(
            "create table if not exists
        {$wpdb->prefix}form_contact_respuestas(
            id int not null auto_increment,
            form_contact_id int,
            nombre varchar(100) not null,
            correo varchar(100) not null,
            telefono varchar(100) not null,
            mensaje text not null,
            fecha date,
            primary key(id)
        )"
        );

        /* indices de form_contact_respuestas*/
        $wpdb->query("alter table {$wpdb->prefix}form_contact_respuestas add index(`nombre`) ");
        $wpdb->query("alter table {$wpdb->prefix}form_contact_respuestas add index(`correo`) ");
        $wpdb->query("alter table {$wpdb->prefix}form_contact_respuestas add index(`telefono`) ");

        /* Definir llaves foraneas */
        $wpdb->query("alter table {$wpdb->prefix}form_contact_respuestas add constraint fk_form_contact_id foreign key (form_contact_id) references {$wpdb->prefix}form_contact(id);");
    });

    //desactivacion del plugin
    register_deactivation_hook(__FILE__, function () {
        /* Limpiador de enlaces permanentes */
        flush_rewrite_rules();
    });

    /* enqueue */
    add_action('admin_enqueue_scripts', function ($hook) {

        if ($hook === 'form-contact/includes/listar.php') {
            /* CSS */
            wp_enqueue_style('bootstrap', plugins_url('form-contact/assets/css/bootstrap.min.css'), [], '5.3');
            wp_enqueue_style('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css', ['bootstrap'], '11.10.0');

            /* JS */
            wp_enqueue_script('app', plugins_url('form-contact/assets/js/app.js'), [], '1.1.1', true);
            wp_enqueue_script('bootstrap', plugins_url('form-contact/assets/js/bootstrap.min.js'), ['jquery'], '5.3', true);
            wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js', ['jquery'], '11.10.0', true);
        }
    });

    /* Agregar al menu del backend */
    add_action('admin_menu', function () {
        add_menu_page(
            'Form contact', // Título de la página
            'Form contact', // Título del menú
            'manage_options', // Capacidad requerida
            plugin_dir_path(__FILE__) . 'includes/listar.php', //Archivo del contenido de la pagina
            null,
            'dashicons-welcome-write-blog', // URL del icono del menú
            132 // Posición del menú
        );
    });

    /* Registrar el shortcode */
    add_action('init', function () {
        add_shortcode('form_contact', 'form_contact_display');
    });

    if (!function_exists('form_contact_display')) {

        function form_contact_display($args, $content = "")
        {
            global $wpdb;
            $nonce = wp_create_nonce('seg');

            if (isset($_POST['nonce'])) {
                $data = [
                    'form_contact_id' => $args['id'],
                    'nombre' => sanitize_text_field($_POST['nombre']),
                    'correo' => sanitize_text_field($_POST['correo']),
                    'telefono' => sanitize_text_field($_POST['telefono']),
                    'mensaje' => sanitize_text_field($_POST['mensaje']),
                    'fecha' => date('Y-m-d')
                ];
                $tabla_respuesta = "{$wpdb->prefix}form_contact_respuestas";
                $wpdb->insert($tabla_respuesta, $data);
    ?>

<?php
            }

            $html = '<section class="contenedor-single">';
            $html .= '<h3 class="text-center" >Llene el formulario</h3>';
            $html .= '<form method="POST" action="" class="form_contact_display" novalidate>';
            /* CAMPO NOMBRE*/
            $html .= '<div class="form-spacing" id="form_container_nombre">';
            $html .= '<label for="nombre">Nombre </label>';
            $html .= '<input id="nombre" type="text" placeholder="Escriba su nombre" name="nombre">';
            $html .= '</div>';
            /* CAMPO CORREO*/
            $html .= '<div class="form-spacing" id="form_container_correo">';
            $html .= '<label for="correo">Correo </label>';
            $html .= '<input id="correo" type="email" placeholder="Escribe tu E-Mail" name="correo">';
            $html .= '</div>';
            /* CAMPO TELEFONO*/
            $html .= '<div class="form-spacing" id="form_container_telefono">';
            $html .= '<label for="tel">Número de telefono </label>';
            $html .= '<input id="tel" type="number"  placeholder="Escribe tu número de telefono" name="telefono">';
            $html .= '</div>';
            /* CAMPO MENSAJE*/
            $html .= '<div class="form-spacing" id="form_container_mensaje">';
            $html .= '<label for="mensaje">mensaje </label>';
            $html .= '<textarea id="mensaje" rows="10" name="mensaje"></textarea>';
            $html .= '</div>';
            /* CAMPO NONCE*/
            $html .= '<input id="nonce" type="hidden" name="nonce" value="' . esc_attr($nonce) . '" >';

            /* CAMPO submit*/
            $html .= '<div class="form-spacing">';
            $html .= '<input type="submit" />';
            $html .= '</div>';


            $html .= '</form>';
            return $html;
        }
    }
 
