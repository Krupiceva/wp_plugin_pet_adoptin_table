<?php

get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg'); ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Pet Adoption</h1>
    <div class="page-banner__intro">
      <p>Providing forever homes one search at a time.</p>
    </div>
  </div>  
</div>

<div class="container container--narrow page-section">

  <p>This page took <strong><?php echo timer_stop();?></strong> seconds to prepare. Found <strong>x</strong> results (showing the first x).</p>

  <?php 
    global $wpdb;
    $tablename = $wpdb->prefix . 'pets';
    $ourQuery = $wpdb->prepare("SELECT * FROM $tablename LIMIT 100");
    $pets = $wpdb->get_results($ourQuery);
  ?>

  <table class="pet-adoption-table">
    <tr>
      <th>Name</th>
      <th>Species</th>
      <th>Weight</th>
      <th>Birth Year</th>
      <th>Hobby</th>
      <th>Favorite Color</th>
      <th>Favorite Food</th>
    </tr>
    <?php
      foreach($pets as $pet){ ?>
        <tr>
          <td><?php echo $pet->petname; ?></td>
          <td><?php echo $pet->species; ?></td>
          <td><?php echo $pet->petweight; ?></td>
          <td><?php echo $pet->birthyear; ?></td>
          <td><?php echo $pet->favhobby; ?></td>
          <td><?php echo $pet->favcolor; ?></td>
          <td><?php echo $pet->favfood; ?></td>
        </tr>
      <?php } 
    ?>
  </table>
  
</div>

<?php get_footer(); ?>