<!doctype html>
<html lang="en">

<head>

  <?php
  $pgTitle = "Products";
  $pgDesc = "Browse RGS Products";
  include 'includes/meta.php';
  ?>

</head>

<body>

  <?php
  include 'includes/nav.php';
  include 'includes/header.php';
  ?>

  <main>

    <section class="productlayout">
      <div class="container-fluid widecont h-100">
        <div class="row py-5">
          <div class="col-12 col-lg-2 py-3">
            <div class="pb-5">
              <button id="categories" class="btn btn-outline-dark shadow-none" type="button" data-toggle="collapse" data-target="#collapseCat" aria-expanded="false" aria-controls="collapseCat">
                Categories <i class="fas fa-caret-down"></i>
              </button>
              <hr class="p-0 ml-0">
              <div id="collapseCat" class="collapse">
                <ul class="list-unstyled">
                  <?php
                  $sql = "SELECT DISTINCT Category FROM regency_products ORDER BY Category ASC";
                  $result = $conn->query($sql);
                  $removethisstuff = array(" ", "&");
                  $addthisstuff = array("", "+");

                  while($row=$result->fetch_assoc()){
                    $removeamp = str_replace($removethisstuff, $addthisstuff, $row);
                    $lowercasecol = array_map('strtolower', $removeamp);
                    ?>
                    <li class="mb-1"><a href="<? echo '?category='; echo $lowercasecol['Category']?>" id="<? echo $lowercasecol['Category']?>" style="font-size: .85rem;"><?php echo $row['Category'] ?></a></li>
                  <?php }
                  ?>
                </ul>
              </div>
            </div>
            <div class="pb-5">
              <button id="collections" class="btn btn-outline-dark shadow-none" type="button" data-toggle="collapse" data-target="#collapseCol" aria-expanded="false" aria-controls="collapseCol">
                Collections <i class="fas fa-caret-down"></i>
              </button>
              <hr class="p-0 ml-0">
              <div id="collapseCol" class="collapse">
                <ul class="list-unstyled h-100">
                  <?php
                  $sql = "SELECT DISTINCT Collection, MAX(Category) FROM regency_products GROUP BY Collection ORDER BY MAX(Category) ASC, Collection";
                  $result = $conn->query($sql);
                  $removethisstuff = array(" ", "&");
                  $addthisstuff = array("", "+");
                  while($row=$result->fetch_assoc()){
                    $removeamp = str_replace($removethisstuff, $addthisstuff, $row);
                    $lowercasecol = array_map('strtolower', $removeamp);
                    ?>
                    <li class="mb-1"><a href="<? echo '?collection='; echo $lowercasecol['Collection']?>" id="" style="font-size: .85rem;"><?php echo $row['Collection'] ?></a></li>
                  <?php } ?>
                </ul>
              </div>
            </div>
            <div class="pb-5">
              <button id="subcollections" class="btn btn-outline-dark shadow-none" type="button" data-toggle="collapse" data-target="#collapseSubCol" aria-expanded="false" aria-controls="collapseSubCol">
                Sub-Collections <i class="fas fa-caret-down"></i>
              </button>
              <hr class="p-0 ml-0">
              <div id="collapseSubCol" class="collapse">
                <ul class="list-unstyled">
                  <?php
                  $sql = "SELECT DISTINCT Sub_Collection, MAX(Category) FROM regency_products GROUP BY Sub_Collection ORDER BY MAX(Category) ASC, Sub_Collection";
                  $result = $conn->query($sql);
                  $removethisstuff = array(" ", "&");
                  $addthisstuff = array("", "+");
                  while($row=$result->fetch_assoc()){
                    $removeamp = str_replace($removethisstuff, $addthisstuff, $row);
                    $lowercasecol = array_map('strtolower', $removeamp);
                    ?>
                    <li class="mb-1"><a href="<? echo '?subcollection='; echo $lowercasecol['Sub_Collection']?>" id="" style="font-size: .85rem;"><?php echo $row['Sub_Collection'] ?></a></li>
                  <?php } ?>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-lg-10">
            <div class="row no-gutters" id="result">
              <?php
              if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
              $start_from = ($page-1) * $rpp;
              $sql = "SELECT COUNT(DISTINCT Prefix) AS total FROM regency_products";
              $result = $conn->query($sql);
              $row = $result->fetch_assoc();
              $total_pages = ceil($row["total"] / $rpp); // calculate total pages with results
              ?>
              <div class="col-12 pagenumbers p-3">
                Page:
                <?php
                for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
                  echo "<a style='font-size: 1rem; margin: 0 .5rem;' href='products.php?page=".$i."'";
                  if ($i==$page)  echo " class='curPage'";
                  echo ">".$i."</a>";
                };
                ?>
              </div>
              <?php
              $sql="SELECT * FROM regency_products GROUP BY Prefix ORDER BY Prefix, Name ASC LIMIT $start_from, ".$rpp;
              $result=$conn->query($sql);
              while($row=$result->fetch_assoc()){
                ?>
                <div class="col-12 col-md-3 p-3 text-center">
                  <div class="card h-100">
                    <div style="" class="card-img-top img-fluid">
                      <?php
                      if (file_exists('images/products/'.$row['SKU'].'_1.jpg')) {
                        ?>
                        <img src="images/products/<?php echo $row['SKU']?>_1.jpg" style="object-fit: contain; padding: 1rem; height: 20rem; width: 100%" class="img-responsive" alt="<?php echo $row['Prefix']?>.jpg">
                        <?php
                      }

                      elseif (file_exists('images/products/'.$row['SKU'].'.jpg')){
                        ?>
                        <img src="images/products/<?php echo $row['SKU']?>.jpg" style="object-fit: contain; padding: 1rem; height: 20rem; width: 100%" class="img-responsive" alt="<?php echo $row['Prefix']?>.jpg">
                        <?php
                      }

                      else{
                        ?>
                        <img src="https://via.placeholder.com/3000/ffffff/212529?text=IMG+Coming+Soon" style="object-fit: contain; padding: 1rem; height: 20rem; width: 100%" class="img-responsive" alt="<?php echo $row['Prefix']?>.jpg">
                        <?php
                      }
                      ?>
                    </div>
                    <div class="card-body">
                      <h6 class="card-title mb-2" style="font-size: .9375rem; font-weight: 600;"><?php echo $row['Name'] ?></h6>
                      <a class="stretched-link" style="font-size: .75rem" href="details.php?skuvar=<?php echo $row['Prefix']?>">View Product <i class="fa fa-angle-double-right"></i></a>
                    </div>
                  </div>
                </div>
                <?php
              }
              ?>
              <div class="col-12 pagenumbers p-3">
                Page:
                <?php
                for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
                  echo "<a style='font-size: 1rem; margin: 0 .5rem;' href='products.php?page=".$i."'";
                  if ($i==$page)  echo " class='curPage'";
                  echo ">".$i."</a>";
                };
                ?>
              </div>
            </div>
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
