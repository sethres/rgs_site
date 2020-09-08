<!DOCTYPE html>



<html lang="en" dir="ltr">



<head>
  <?php
  $skuvar = $_GET['skuvar'];
  $skucolor = $_GET['color'];
  $pgTitle = "Products: ".$skuvar;
  $pgDesc = "Browse the RGS Product Database";
  include "includes/meta.php";
  ?>
</head>

<?php
// GET PRODUCT & IMAGES //
$getprod="SELECT * FROM regency_products WHERE Prefix='$skuvar' AND Color='$skucolor'";
$getcol= "SHOW COLUMNS FROM regency_products";
$resultprod = $conn->query($getprod);
$resultcol = $conn->query($getcol);
$prod = $resultprod->fetch_assoc();
while($col = $resultcol->fetch_assoc()){
  $columns[] = $col['Field'];
}
$dirname = "images/products/".$prod['SKU'];
$images = glob($dirname."*.jpg");
// GET PRODUCT & IMAGES //
?>

<body>
  <!-- NAV -->
  <?php include "includes/nav.php"; ?>
  <!-- NAV -->


  <!-- BODY CONTENT -->
  <main>
    <section class="py-5 pro-info">
      <div class="container-fluid widecont">
        <div class="row">
          <div class="col-lg-6 pb-5 pb-lg-0">
            <div class="row px-3">
              <div id="detCarousel" class="detcarousel carousel slide carousel-fade w-100" data-ride="carousel">
                <div class="carousel-inner">
                  <?php
                  $n = 0; // RESET $n
                  foreach($images as $image){
                    ?>
                    <div class="carousel-item <?php if($n==0) echo "active" ?> bg-white border" data-slide-number="<?php echo $n?>">
                      <a class="stretched-link" href="<?php echo $image?>" target="_blank">
                        <?php
                        if (file_exists($images['0'])) {
                        ?>
                        <img class="d-block w-100" src="<?php echo $image?>" alt="<?php echo $image?>">
                        <?php
                        }

                        else{
                        ?>
                        <img class="d-block w-100" src="https://via.placeholder.com/3000/ffffff/212529?text=IMG+Coming+Soon" alt="">
                        <?php
                        }
                        ?>
                      </a>
                    </div>
                    <?php
                    if($n==7) break;
                    $n++;
                  }

                  if(empty($images)){
                    ?>
                    <div class="carousel-item <?php if($n==0) echo "active" ?> bg-white border" data-slide-number="<?php echo $n?>">
                      <a class="stretched-link" href="https://via.placeholder.com/3000/ffffff/212529?text=IMG+Coming+Soon" target="_blank">
                        <img class="d-block w-100" src="https://via.placeholder.com/3000/ffffff/212529?text=IMG+Coming+Soon" alt="">
                      </a>
                    </div>
                    <?php
                  }
                  ?>
                </div>

                <ul class="carousel-indicators list-inline mb-0 mt-3 mx-auto">
                  <?php
                  $n = 0; // RESET $n
                  foreach($images as $image){
                    ?>
                    <li class="list-inline-item <?php if($n==0) echo "active" ?> border">
                      <a id="carousel-selector-0" class="selected" data-slide-to="<?php echo $n?>" data-target="#detCarousel">
                        <?php
                        if (file_exists($images['0'])) {
                        ?>
                        <img class="d-block" src="<?php echo $image?>" alt="<?php echo $image?>">
                        <?php
                        }

                        else{
                        ?>
                        <img class="d-block" src="https://via.placeholder.com/3000/ffffff/212529?text=IMG+Coming+Soon" alt="">
                        <?php
                        }
                        ?>
                      </a>
                    </li>
                    <?php
                    if($n==6) break;
                    $n++;
                  }

                  if(empty($images)){
                    ?>
                    <li class="list-inline-item active border">
                      <a id="carousel-selector-0" class="selected" data-slide-to="0" data-target="#detCarousel" data-toggle="tooltip" data-placement="bottom" title="Coming Soon">
                        <img class="d-block" src="https://via.placeholder.com/3000/ffffff/212529?text=IMG+Coming+Soon" alt="">
                      </a>
                    </li>
                    <?php
                  }
                  ?>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-3">
            <h1 style="font-weight: 700" class="mb-3"><?php echo $prod['Name']?></h1>
            <p class="m-0" style="font-weight: 400; color: #6c757d;">SKU Variation: <b><?php echo $prod['Prefix']; ?></b></p>
            <p class="m-0" style="font-weight: 400; color: #6c757d;">Current SKU: <b><?php echo $prod['SKU']; ?></b></p>
            <p class="m-0" style="font-weight: 400; color: #6c757d;">List Price: <b>$<?php echo $prod['List_Price'] ?></b></p>
            <p class="m-0" style="font-weight: 400; color: #6c757d;">Current Stock: <b>UNKWN</b></p>


            <?
            if (empty($prod['Description'])) {
              ?>
              <p class="my-4" style="font-weight: 400; color: #6c757d;">Description Currently Unavailable</p>
              <?
            }

            else{
              ?>
              <p class="my-4" style="font-weight: 400; color: #6c757d;"><? echo $prod['Description']?></p>
              <?
            }
            ?>


            <p>
              <?php echo $columns['3']?>:
            </p>
            <hr class="p-0 ml-0">
            <div style="font-size: 0">
              <?php
              $getcolor="SELECT DISTINCT Color FROM regency_products WHERE Prefix='$skuvar'";
              $resultcolor = $conn->query($getcolor);
              $n = 0; // RESET $n
              while($prod = $resultcolor->fetch_assoc()){
                ?>
                <a href="details.php?skuvar=<? echo $skuvar?>&color=<? echo $prod['Color']?>" class="btn btn-outline-secondary shadow-none py-3 px-4 m-1 <?php if ($skucolor == $prod['Color']) echo "active"?>" style="font-size: .85rem"><?php echo $prod['Color']?></a>
                <?php
                $n++;
                }
                ?>
            </div>


            <?php
            $getconf="SELECT DISTINCT Configuration FROM regency_products WHERE Prefix='$skuvar'";
            $resultconf = $conn->query($getconf);
            if (mysqli_num_rows($resultconf) < 2) {
              echo "<p class='pt-5' style='font-weight: 400; color: #6c757d;'>No Configurations Available</p>";
              echo "<hr>";
            }
            else{
              ?>
              <p class="mt-5">
                <?php echo $columns['4']?>:
              </p>
              <hr class="p-0 ml-0">
              <div div style="font-size: 0">
                <?php
                $n=0;
                while($prod = $resultconf->fetch_assoc()){
                  ?>
                  <a href="" class="btn btn-outline-secondary shadow-none py-3 px-4 m-1 <?php if ($n == 0) echo "active"?>" style="font-size: .85rem"><?php echo $prod['Configuration'] ?></a>
                <?php
                $n++;
                }
                ?>
              </div>
              <?php
            }
            ?>


          </div>
        </div>
      </div>
    </section>
    <section id="moreinfo" class="more-pro-info pb-5">
      <div class="accordion container-fluid widecont" id="accordionExample">
        <div class="card">
          <div class="card-header" id="headingOne">
            <h2 class="mb-0">
              <button class="btn btn-link shadow-none stretched-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Downloads
              </button>
            </h2>
          </div>

          <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
            <div class="card-body">
              LOL
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header" id="headingTwo">
            <h2 class="mb-0">
              <button class="btn btn-link collapsed shadow-none stretched-link" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Collapsible Group Item #2
              </button>
            </h2>
          </div>
          <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
            <div class="card-body">
              Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header" id="headingThree">
            <h2 class="mb-0">
              <button class="btn btn-link collapsed shadow-none stretched-link" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Collapsible Group Item #3
              </button>
            </h2>
          </div>
          <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
            <div class="card-body">
              Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!-- BODY CONTENT -->


  <!-- FOOTER -->
  <?php include ("includes/footer.php"); ?>
  <!-- FOOTER -->
</body>



</html>
