<!DOCTYPE html>
<html lang="en">
<?php $this->load->view('common/head'); ?>

<!--<body class="login-container" style=" background-image: linear-gradient(to left, #0277BD , #03A9F4);">-->
<body class="login-container" style=" background-image: url('assets/images/ap2.jpeg'); background-size:cover;">
  <div class="page-container">
    <div class="page-content">
      <div class="content-wrapper">
        <div class="content pb-20">
          <form action="" id="form_login">
            <div class="panel panel-body login-form" style="box-shadow: 5px 10px 18px #000;">
              <div class="text-center">

                 <div class="icon-object border-blue-800 bg-blue-700"><i class="icon-reading"></i></div> 
                <!--<img src="<?php echo base_url().'/assets/images/favico2.png'?>">-->
                <h5 class="content-group">Login to your account
                  <!-- <small class="display-block">Enter your credentials below</small> -->
                </h5>
              </div>

              <div class="form-group has-feedback has-feedback-left">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
                <div class="form-control-feedback">
                  <i class="icon-user text-muted"></i>
                </div>
              </div>

              <div class="form-group has-feedback has-feedback-left">
                <input type="password" name="password" id="niPassword" class="form-control" placeholder="Password" required>
                <div class="form-control-feedback">
                  <i class="icon-lock2 text-muted"></i>
                </div>
              </div>

              <div class="form-group login-options">
                <div class="row">
                  <div class="col-sm-6">
                    <label class="checkbox-inline">
                      <input type="checkbox" onclick="showPassword()" class="styled">Show Password
                    </label>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <button type="submit" class="btn bg-angkasa2 btn-block">Login <i class="icon-arrow-right14 position-right"></i></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </body>
  <script type="text/javascript">
    url_login = "<?php echo site_url(); ?>login/do_login";
  </script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/common/login.js"></script>
  </html>