 <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?php base_url('index.php'); ?>">P81P</a>
          <div class="nav-collapse collapse">
            <?php if($this->session->userdata('username')){?>
            <p class="navbar-text pull-right">
              Logged in as <a href="#" class="navbar-link"><?php echo $this->session->userdata('username');?></a> | <a href="<?php echo base_url('index.php/login/login/do_logout');?>" class="">Logout</a>
            </p>
            <?php } ?>
            <ul class="nav">
              <li><a href="<?php echo base_url('index.php/common/settings'); ?>">Settings</a></li>
              <li><a href="<?php echo base_url('index.php/coupons/add'); ?>">Add Coupons</a></li>
              <li><a href="<?php echo base_url('index.php/coupons/update'); ?>">Update Coupons</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <div class="container-fluid">