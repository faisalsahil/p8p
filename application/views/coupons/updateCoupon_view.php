<div class="row-fluid">
        <!-- <div class="span3">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">Sidebar</li>
              <li class="active"><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li class="nav-header">Sidebar</li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li class="nav-header">Sidebar</li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
              <li><a href="#">Link</a></li>
            </ul>
          </div>
        </div> -->
        <div class="span9">
          <div class="hero-unit">
            <h2>Update Coupons</h2>
            <!-- mailchimp form -->
            <div class="well">
              <fieldset>
              <legend>Select Date Range to Update Coupons</legend>
              <form action="<?php echo $updateAction; ?>" method="post" name="frmUpdate" id="frmUpdate" class="form-inline">
                <input type="hidden" name="update">
                <input type="text" class="input-large" name="start" placeholder="Start Date" id="startdate">
                <input type="text" class="input-large" name="end" placeholder="End Date" id="enddate">
                  <button class="btn btn-primary" id="btnUpload" type="submit">Update Coupons</button>
              </form>
               <div id="progress" class="progress progress-striped" style="display:none">
                <div class="bar" style="width: 0%;"></div>
              </div>
              <div id="output1"></div>
            </fieldset>
            </div>
          </div>
          
        </div><!--/span-->
      </div><!--/row-->
      <script>
      $('#startdate').datepicker({
          format: 'yyyy-mm-dd'
      });
      $('#enddate').datepicker({
          format: 'yyyy-mm-dd'
      });
      </script>