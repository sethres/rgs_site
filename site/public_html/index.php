<!doctype html>
<html lang="en">

<head>

  <?php
  $pgTitle = "Home";
  $pgDesc = "Welcome to RGS Furniture! America's leading provider of innovative, functional furniture solutions";
  include 'includes/meta.php';
  ?>

</head>

<body>

  <?php
  include 'includes/nav.php'
  ?>

  <main>

  <header class="heading">
    <div class="container-fluid widecont h-100">
      <div class="jumbotxt d-flex h-100">
        <div class="my-auto">
          <h1 class="display-3 font-weight-bold">Tomorrow's Furniture, <br> Ships Today</h1>
          <p class="lead">Welcome to RGS Furniture! America's leading provider of innovative, functional furniture solutions</p>
          <button class="btn btn-outline-dark rounded-0 text-uppercase px-5 py-3 shadow-none">Learn More</button>
        </div>
      </div>
    </div>
  </header>

  <section class="procarousel carousel slide carousel-fade bg-white" data-ride="carousel">

    <div class="container-fluid widecont h-100">

      <?php
      $query = "SELECT DISTINCT Category from regency_products ORDER BY Category ASC";
      $result = $conn->query($query);
      $even = 0;
      $n = 0;
      while ($row = $result->fetchAll()) {
        if ($even % 2 == 0) {
          ?>
          <div class="carousel-item h-100 <?php if($n==0) echo "active"?> bg-white">
            <div class="row h-100 align-items-center py-5">
              <div class="prodslideimg col-lg-8 col-md-12"></div>
              <div class="col-lg-4 col-md-12 pl-lg-3 text-center text-lg-right order-12 order-lg-12 pb-lg-0">
                <h2 class="font-weight-bold"><?php echo $row['Category']?></h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                <a href="#">Browse <?php echo $row['Category']?> <i class="fa fa-angle-double-right"></i></a>
              </div>
            </div>
          </div>
          <?php
        }
        else {
          ?>
          <div class="carousel-item h-100 bg-white">
            <div class="row h-100 align-items-center py-5">
              <div class="prodslideimg col-lg-8 col-md-12 order-1 order-lg-12"></div>
              <div class="col-lg-4 col-md-12 pl-lg-3 text-center text-lg-left order-12 order-lg-1 pb-lg-0">
                <h2 class="font-weight-bold"><?php echo $row['Category']?></h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                <a href="#">Browse <?php echo $row['Category']?> <i class="fa fa-angle-double-right"></i></a>
              </div>
            </div>
          </div>
          <?php
          }
          $n++;
          $even++;
      }
      ?>
      <ol class="carousel-indicators w-100 p-0 m-0 justify-content-between" style="height: 2%;">
        <li data-target=".procarousel" data-slide-to="0" class="active"></li>
        <li data-target=".procarousel" data-slide-to="1"></li>
        <li data-target=".procarousel" data-slide-to="2"></li>
        <li data-target=".procarousel" data-slide-to="3"></li>
        <li data-target=".procarousel" data-slide-to="4"></li>
        <li data-target=".procarousel" data-slide-to="5"></li>
      </ol>

    </div>

  </section>

  <section class="information" style="background-color: #e9ecef;">
    <div class="container-fluid w-100 h-100 no-gutters mx-auto">
      <div class="row h-100 align-items-center py-0 py-lg-5">
        <div class="infoimg col-lg-7 col-md-12 order-1 order-lg-1"></div>
        <div class="col-lg-5 col-md-12 text-center order-12 order-lg-12 py-5 py-lg-0">
          <h2 class="font-weight-bold">A Brief History</h2>
          <p>This is an excerpt about the following [CATEGORY]</p>
          <a href="#">Learn More <i class="fa fa-angle-double-right"></i></a>
        </div>
      </div>
      <div class="row h-100 align-items-center py-0 pb-lg-5">
        <div class="infoimg col-lg-7 col-md-12 order-1 order-lg-12"></div>
        <div class="col-lg-5 col-md-12 text-center order-12 order-lg-1 py-5 py-lg-0">
          <h2 class="font-weight-bold">Resource Database</h2>
          <p>This is an excerpt about the following [CATEGORY]</p>
          <a href="#">Browse Resource Database <i class="fa fa-angle-double-right"></i></a>
        </div>
      </div>
    </div>
  </section>

</main>


  <?php
  include 'includes/footer.php';
  ?>


</body>

</html>
