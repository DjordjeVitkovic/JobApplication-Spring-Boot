<?php
    $name = array();
    if( isset($_SESSION['usersess']) && isset($_SESSION['uservals']) && isset($_SESSION['uservals']['username']) && isset($_SESSION['uservals']['userid']) ){
?>
    <div class="p-subtitle"><i class="fa fa-user"></i>&nbsp;&nbsp;&nbsp;<?php echo $_SESSION['uservals']['userid'] ?># <?php echo $_SESSION['uservals']['username'] ?></div>
<?php
    }
?>