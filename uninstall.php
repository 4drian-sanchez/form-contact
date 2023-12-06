<?php
if (!defined('ABSPATH')) die();
//Esto es para que no haga nada si se presiono la función por error
if(!defined('WP_UNINSTALL_PLUGIN')) die();

function form_contact_borrar() {
    global $wpdb;
    $wpdb->query("drop table {$wpdb->prefix}form_contact_respuestas");
    $wpdb->query("drop table {$wpdb->prefix}form_contact");
}


form_contact_borrar();