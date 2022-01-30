<?php

/*
  Plugin Name: Pet Adoption (New DB Table)
  Version: 1.0
  Author: Krupiceva
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once plugin_dir_path(__FILE__) . 'inc/generatePet.php';

class PetAdoptionTablePlugin {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "pets";

    add_action('activate_new-database-table/new-database-table.php', array($this, 'onActivate'));
    //add_action('admin_head', array($this, 'populateFast'));
    add_action('wp_enqueue_scripts', array($this, 'loadAssets'));
    add_filter('template_include', array($this, 'loadTemplate'), 99);
    //This is the hook that is created from POST request from template file
    add_action('admin_post_createpet', array($this, 'createPet'));
    add_action('admin_post_nopriv_createpet', array($this, 'createPet'));
  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      birthyear smallint(5) NOT NULL DEFAULT 0,
      petweight smallint(5) NOT NULL DEFAULT 0,
      favfood varchar(60) NOT NULL DEFAULT '',
      favhobby varchar(60) NOT NULL DEFAULT '',
      favcolor varchar(60) NOT NULL DEFAULT '',
      petname varchar(60) NOT NULL DEFAULT '',
      species varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY  (id)
    ) $this->charset;");
  }

  function onAdminRefresh() {
    global $wpdb;
    $wpdb->insert($this->tablename, generatePet());
  }

  function createPet(){
    if(current_user_can('administrator')){
      $pet = generatePet();
      $pet['petname'] = sanitize_text_field($_POST['incomingpetname']);
      global $wpdb;
      $wpdb->insert($this->tablename, $pet);
      wp_redirect(site_url('/pet-adoption'));
    } else {
      wp_redirect(site_url());
    }
  }

  function loadAssets() {
    if (is_page('pet-adoption')) {
      wp_enqueue_style('petadoptioncss', plugin_dir_url(__FILE__) . 'pet-adoption.css');
    }
  }

  function loadTemplate($template) {
    if (is_page('pet-adoption')) {
      return plugin_dir_path(__FILE__) . 'inc/template-pets.php';
    }
    return $template;
  }

  function populateFast() {
    $query = "INSERT INTO $this->tablename (`species`, `birthyear`, `petweight`, `favfood`, `favhobby`, `favcolor`, `petname`) VALUES ";
    $numberofpets = 100000;
    for ($i = 0; $i < $numberofpets; $i++) {
      $pet = generatePet();
      $query .= "('{$pet['species']}', {$pet['birthyear']}, {$pet['petweight']}, '{$pet['favfood']}', '{$pet['favhobby']}', '{$pet['favcolor']}', '{$pet['petname']}')";
      if ($i != $numberofpets - 1) {
        $query .= ", ";
      }
    }
    /*
    Never use query directly like this without using $wpdb->prepare in the
    real world. I'm only using it this way here because the values I'm 
    inserting are coming fromy my innocent pet generator function so I
    know they are not malicious, and I simply want this example script
    to execute as quickly as possible and not use too much memory.
    */
    global $wpdb;
    $wpdb->query($query);
  }

}

$petAdoptionTablePlugin = new PetAdoptionTablePlugin();