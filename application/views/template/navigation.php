    <?php

// Prepping Navigation Links
    $nav = array('home' => 'Home',
                 'books' => 'Books',
                 'categories' => 'Categories',
                 'cart' => 'My Cart',
                 'account' => 'My Account');



    function navigation($cur_nav){

        if ($cur_nav == 'home') {
            echo '<li class="active">
                                    <a href="#">Home</a>
                                </li>';
        }else if($cur_nav == 'categories'){
            echo '';
        }

    }



    ?>



    <!-- Start Navigation -->
    <div class="row">
                <div class="navbar navbar-default">
                    <div class="container">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                                <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
                            </button><a href="#" class="navbar-brand"></a>
                        </div>
                        <div class="collapse navbar-collapse">
                        <center>
                        <img class="mainlogo" src="<?php echo base_url() . 'assets/img/logo.jpg' ?>" alt=""> 
                        </center>
                        
                            <ul class="nav nav-tabs">

                                <li class="active">
                                    <a href="<?php echo site_url('home');?>">Home</a>
                                </li>

                                <li>

                                    <a href="<?php echo site_url('book/browse/all');?>">Books</a>

                                </li>                           

                                
                               <li class="dropdown pull-right">
                                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">My Account <span class="caret"></span></a>
                                  <ul class="dropdown-menu">
                                    <li><a href="<?php echo site_url('account/login');?>">Login Account</a></li>
                                    <li><a href="<?php echo site_url('account/register');?>">Register Account</a></li>
                                    <li class="divider"></li>
                                     <li><a href="<?php echo site_url('account/logout')?>">Logout Account</a></li>
                                  </ul>
                                </li>    

                                <li class="pull-right">
                                    <a href="<?php echo site_url('cart'); ?>">My Cart  <span class="badge badge-info"><?php echo $this->cart->total_items(); ?></span></a>
                                </li>


                                
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    <!-- End Navigation -->


<div class="row">
                    <div class="col-md-4">

                    <?php 
                    
                    if ($this->session->userdata('login')) {
                        $var = $this->session->userdata('login');
                        echo '<h4>Welcome! '.$var["name"].'</h4>';
                    }else{
                        echo '<h4>Welcome Guest!</h4>';
                    }

                    ?>

                   
                    </div>

                    <div class="col-md-8">
 
    <div class="input-group">
      <input placeholder="What will you find today?" type="text" class="form-control">
      <span class="input-group-btn">
        <button class="btn btn-success" type="button">Go!</button>
      </span>
    </div><!-- /input-group -->

</div>

</div>