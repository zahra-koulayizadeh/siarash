<?php require_once('../private/initialize.php'); ?>

<?php
  // echo WWW_ROOT;
  $opts = [];
  $preview = false;
  if(isset($_GET['preview'])){
    // previewing should require admin to be logged in
    $preview = $_GET['preview'] == 'true' && is_logged_in() ? true : false;
  }
  $opts['visible'] = !$preview;
  
  if(isset($_GET['subject_id'])){ //if subject_id is in url parameters
    $subject_id = $_GET['subject_id'];
    $subject = find_subject_by_id($subject_id, $opts);
    if(!$subject) { // if subject is not visible or doesn't exist
      redirect_to(url_for('/index.php'));
    }
    $page_set = find_pages_by_subject_id($subject_id, $opts);
    $page = mysqli_fetch_assoc($page_set); // First page of subject id
    if(!$page) {
      redirect_to(url_for('/index.php'));
    }
    mysqli_free_result($page_set);
    $page_id = $page['id'];
  } elseif(isset($_GET['id'])){ //else if id is in url parameters
    $page_id = $_GET['id'];
    $page = find_page_by_id($page_id, $opts);
    if(!$page) {
      redirect_to(url_for('/index.php'));
    }
    $subject_id = $page['subject_id'];
    $subject = find_subject_by_id($subject_id, $opts);
    if(!$subject) {
      redirect_to(url_for('/index.php'));
    }
  } else {
    // nothing selected; show the homepage
  }
?>

<?php include(SHARED_PATH . '/public_header.php'); ?>

<div id="main">
  <?php include(SHARED_PATH . '/public_navigation.php'); ?>
  <div id="page">

  <?php 
    if(isset($page) ) {
      //show the page from the database
      $allowed_tags = '<div><img><h1><h2><p><br><strong><em><ul><li>';
      echo strip_tags($page['content'], $allowed_tags);
    } else {
      // Show the homepage
      // The homepage content could:
      // * be static content (here or in a shared file)
      // * show the first page from the nav
      // * be in the database but add code to hide in the nav
      include(SHARED_PATH . '/static_homepage.php'); 
    }
  ?>
  </div>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>