<?php
$message = WP_Project_Init_Admin::process_post();
add_thickbox();
?>
<div class="wrap">
    <h2><img src="<?php echo WPI_URL?>/admin/images/logo.png" width="100">WP Project Init</h2>
    <?php include 'main-notice.php'; ?>

    <?php $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'generate-themes';?>
    <h2 class="nav-tab-wrapper">
        <?php 
        $i=0;
        foreach (WP_Project_Init_Admin::$_tabs as $tab => $tablabel) :$i++;?>
            <a href="admin.php?page=project-init&tab=<?php echo $tab;?>" class="nav-tab <?php if($current_tab == $tab):?>nav-tab-active<?php endif;?>"><?php echo $i.'-'.$tablabel;?></a>
        <?php endforeach;?>
    </h2>
    <div id="tab-content">
        <?php
        $pathtab = WPI_PATH . 'admin' . DIRECTORY_SEPARATOR . 'tabs' . DIRECTORY_SEPARATOR . 'tab-' . $current_tab . '.php';
        if(is_file($pathtab)){
            include($pathtab);
        }else{
            include ('tabs/tab-generate-themes.php');
        }
        ?>
    </div>
</div>