<?php
$message = '';
if($_POST['submit']){
    unset($_POST['submit']);
    update_option('jenpress_options',$_POST);
    jp_build_reconstruct();
    $message = 'Fichier <a target="__blank" href="' . site_url('build.xml').'">build.xml</a> regeneré.';
}
$jenpress_options = get_option('jenpress_options');

$defaultUrljenkins = '#';
if (isset($jenpress_options['urljk_project']) && $jenpress_options['urljk_project'] != '' ) {
	$defaultUrljenkins = $jenpress_options['urljk_project'];
}

?>
<div class="wrap">
    <h2>Jenkins Press</h2>
    <?php $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'jenkins-build';?>
    <h2 class="nav-tab-wrapper">
        <a href="options-general.php?page=jenkins-press&tab=jenkins-build" class="nav-tab <?php if($current_tab == 'jenkins-build'):?>nav-tab-active<?php endif;?>" id="jenkins-build-tab">Configuration du fichier build.xml</a>
        <!--a href="options-general.php?page=jenkins-press&tab=jenkins-board" class="nav-tab <?php if($current_tab == 'jenkins-board'):?>nav-tab-active<?php endif;?>" id="jenkins-board-tab">Tableau de bord Jenkins</a-->
        <a href="<?php echo $defaultUrljenkins; ?>" target="_blank" class="nav-tab <?php if($current_tab == 'jenkins-board'):?>nav-tab-active<?php endif;?>" id="jenkins-board-tab">Tableau de bord Jenkins</a>
    </h2>
    <div id="tab-content">
        <?php
            switch($current_tab){
                case 'jenkins-build':
                    include 'tabs/tab-jenkins-build.php';
                    break;
                case 'jenkins-board':
                    include 'tabs/tab-jenkins-board.php';
                    break;
                default:
                    include 'tabs/tab-jenkins-build.php';
            }
            ?>
    </div>
</div>