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
            <h2>Settings</h2>
            <p class="alert alert-success" ><b>Webhook URL(Copy this URL on your MailChimp webhook page):</b> <br><br><i><?php echo $webhook; ?></i></p>
            <!-- mailchimp form -->
            <div class="well">
              <h5>MailChimp API</h5>
             <?php if($MCkey){ ?>
                <p class="alert alert-success" ><b>Key:</b> <i><?php echo $MCkey; ?></i><br>
                  <b>List:</b> <i><?php echo $McList; ?></i><a class="pull-right" id="changeMC">Change</a></p>
              <?php }elseif($MCkey == ''){ ?>
                <script>
                 $(document).ready(function() {
                    $('.MCkeyform').show();
                  });
                </script>
              <?php } ?>
              <div class="MCkeyform">
                <form class="form-inline" name="frm_MC" id="frm_MC" method="post" action="<?php echo $actionMC; ?>">
                  <input type="hidden" name="type" value="MC">
                   <input type="text" class="input-large" id="key" name="key" placeholder="MailChimp Api Key">
                   <input type="button" class="btn btn-mini btn-primary" id="get_list" value="Get List">
                   <select name="MC_lsit" id="MClists">
                      <option value="0">Select a List</option>
                    </select>
                   <button type="submit" class="btn">Submit</button>
                   <span id="closeMcFrm" class="icon-remove pull-right">&nbsp;</span>
                </form>

              </div>
            </div>
            <!-- shopify form -->
            <div class="well">
              <h5>Shopify</h5>
             <!--  <?php if($shopifyData){?>

              <?php } ?> -->
              <div class="shopifyFrm">
                <?php if($getToken){?>
                  <div class="alert alert-success"><span class="icon-ok"></span>&nbsp; Auhtenticated</div>
                <?php } ?>
                <form class="form-horizontal" name="frm_shopify" id="frm_shopify" method="post" action="<?php echo $actionShopify;?>">
                  <input type="hidden" value="<?php echo $getToken; ?>" name="shopifyToken"> 
                  <div class="control-group">
                    <label class="control-label" for="shopName">Shop Name</label>
                    <div class="controls">
                      <label class="control-label show" for="shopName" style="width:200px"><i><?php echo $getShop;?></i></label>
                      <input type="text" id="shopName" class="edit" value="<?php echo $getShop;?>" name="shopName" placeholder="Shop Name">
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label" for="API">API</label>
                    <div class="controls">
                      <label class="control-label show" for="API"><i><?php echo $getApi;?></i></label>
                      <input type="text" id="shopifyApi" class="edit" value="<?php echo $getApi;?>" name="shopifyApi" placeholder="API">
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label" for="Password">Password</label>
                    <div class="controls">
                      <label class="control-label show" for="Password"><i><?php echo $getPass;?></i></label>
                      <input type="text" id="shopifyApi" class="edit" value="<?php echo $getPass;?>" name="shopifyPass" placeholder="Password">
                    </div>
                  </div>
                   <div class="control-group">
                    <label class="control-label" for="shopifySecret">Secret</label>
                    <div class="controls">
                      <label class="control-label show" for="shopifySecret"><i><?php echo $getSecret;?></i></label>
                      <input type="text" id="shopifySecret" class="edit" value="<?php echo $getSecret;?>" name="shopifySecret" placeholder="Client Secret">
                    </div>
                  </div>
                   <div class="controls">
                      <label class="control-label show" for="re-subscribe">Re Subscribe?</label>
                      <select name="re_subscribe" id="re_subscribe" class="dropdown" style="width: initial;">
                        <option value="0" selected="selected" >NO</option>
                        <option value="1">YES</option>
                      </select>
                    </div>
                  </div>
                  <div class="control-group">
                  <div class="controls">
                    <a class="show" id="editShopify">Edit</a>
                  <button type="submit" class="btn edit">Submit</button>
                  </div>
                  </div>
                </form>
            </div>
            </div>
          </div>
          
        </div><!--/span-->
      </div><!--/row-->
      <?php if($getShop){?>
      <script>
        $(document).ready(function() {
            $('.show').show();
            $('.edit').hide();
        });
      </script>
       <?php }else{ ?>
        <script>
        $(document).ready(function() {
            $('.show').hide();
            $('.edit').show();
        });
      </script>
       <?php } ?>
       <script>
       $('#editShopify').click(function(){
          $('.show').hide();
            $('.edit').show();
        });
       </script>