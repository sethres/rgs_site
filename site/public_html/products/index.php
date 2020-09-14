<!doctype html>
<html lang="en">

<head>
  <?php
  $pgTitle = "Products";
  $pgDesc = "Browse RGS Products";
  include '../includes/meta.php';
  include '../includes/rollbar.php';
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
  include '../includes/vue.php';
  ?>
  <script type="module" src="/vuejs/product-listing.js"></script>
</body>

</html>
