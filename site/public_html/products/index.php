<!doctype html>
<html lang="en">

<head>

  <?php
  $pgTitle = "Products";
  $pgDesc = "Browse RGS Products";
  include '../includes/meta.php';
  ?>

</head>

<body>

  <?php
  include '../includes/nav.php';
  include '../includes/header.php';
  ?>
  <main>

    <section class="productlayout">
      <div class="container-fluid widecont h-100">
        <div id="app"></div>
      </div>
    </section>

  </main>

  <?php
  include '../includes/footer.php';
  ?>
  <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
  <script src="https://cdn.jsdelivr.net/npm/fetchival@0.3.3"></script>
  <script>
    let rollbarToken = '<?php echo getenv('ROLLBER_CLIENT_TOKEN') ?>';
  </script>
  <script type="module" src="/vuejs/product-listing.js"></script>
</body>

</html>
