<!DOCTYPE html>



<html lang="en" dir="ltr">



<head>
  <?php
  $pgTitle = "Products: ".$skuvar;
  $pgDesc = "Browse the RGS Product Database";
  include '../includes/meta.php';
  include '../includes/rollbar.php';
  ?>
</head>

<body>
  <!-- NAV -->
  <?php include "../includes/nav.php"; ?>
  <!-- NAV -->
  
  <!-- BODY CONTENT -->
  <main id="app"></main>
  <!-- BODY CONTENT -->


  <!-- FOOTER -->
  <?php 
    include "../includes/footer.php"; 
    include '../includes/vue.php';
  ?>
  <!-- FOOTER -->
  <script type="module" src="/vuejs/product.js"></script>
</body>



</html>
