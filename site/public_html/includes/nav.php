<nav class="container-fluid bg-white border-bottom">
  <div class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="index.php">rgs</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <i class="fas fa-angle-double-down"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav">
        <li <?php if ($pgTitle=="Home") echo " id=\"currentpage\""; ?> class="nav-item first" data-toggle="tooltip" data-placement="bottom" title="Home">
          <a class="nav-link" href="index.php">
            <i class="fas fa-home"></i>
            <span class="nav-link-text">Home</span>
          </a>
        </li>
        <li <?php if ($pgTitle=="Products") echo " id=\"currentpage\""; ?> class="nav-item" data-toggle="tooltip" data-placement="bottom" title="Products">
          <a class="nav-link" href="products.php">
            <i class="fas fa-chair"></i>
            <span class="nav-link-text">Products</span>
          </a>
        </li>
        <li <?php if ($pgTitle=="Spaces") echo " id=\"currentpage\""; ?> class="nav-item" data-toggle="tooltip" data-placement="bottom" title="Spaces">
          <a class="nav-link" href="#">
            <i class="fas fa-paint-brush"></i>
            <span class="nav-link-text">Spaces</span>
          </a>
        </li>
        <li <?php if ($pgTitle=="Catalogs") echo " id=\"currentpage\""; ?> class="nav-item" data-toggle="tooltip" data-placement="bottom" title="Catalogs">
          <a class="nav-link" href="#">
            <i class="fas fa-download"></i>
            <span class="nav-link-text">Catalogs</span>
          </a>
        </li>
        <li <?php if ($pgTitle=="Contact") echo " id=\"currentpage\""; ?> class="nav-item last" data-toggle="tooltip" data-placement="bottom" title="Contact">
          <a class="nav-link" href="#">
            <i class="fas fa-envelope"></i>
            <span class="nav-link-text">Contact</span>
          </a>
        </li>
      </ul>

      <form class="search-bar my-auto d-inline mx-1 ml-lg-auto mr-lg-0">
        <div class="input-group">
          <input type="search" class="form-control form-control-sm rounded-left border-right-0 shadow-none border-dark" placeholder="Search...">
          <span class="input-group-append">
            <button class="btn btn-sm btn-outline-dark border-left-0 shadow-none" type="button">
              <i class="fas fa-search" style="font-size: .75rem;"></i>
            </button>
          </span>
        </div>
      </form>

    </div>
  </div>
</nav>
