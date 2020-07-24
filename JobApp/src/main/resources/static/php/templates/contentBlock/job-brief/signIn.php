<?php
    $name = array();
    if( isset($_SESSION['usersess']) && isset($_SESSION['uservals']) && isset($_SESSION['uservals']['login']) ){
?>
    <div class="p-subtitle"><i class="fa fa-user"></i>&nbsp;&nbsp;&nbsp;<?php echo $_SESSION['uservals']['login'] ?></div>
<?php
    }
?>