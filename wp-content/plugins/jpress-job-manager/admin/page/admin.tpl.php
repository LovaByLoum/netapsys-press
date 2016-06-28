<?php
/**
 * template administration
 */

?>
    <div class="wrap">
        <h2>jPress Job Manager</h2>
        <?php
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
        if(isset($_GET['tab'])) $active_tab = $_GET['tab'];
        ?>
        <h2 class="nav-tab-wrapper">

            <a href="?page=jpress-job-manager&amp;tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">Configuration générale</a>

            <a href="?page=jpress-job-manager&amp;tab=societe" class="nav-tab <?php echo $active_tab == 'societe' ? 'nav-tab-active' : ''; ?>">Sociétés</a>

            <a href="?page=jpress-job-manager&amp;tab=offre" class="nav-tab <?php echo $active_tab == 'offre' ? 'nav-tab-active' : ''; ?>">Offres</a>

            <a href="?page=jpress-job-manager&amp;tab=candidature" class="nav-tab <?php echo $active_tab == 'candidature' ? 'nav-tab-active' : ''; ?>">Candidatures</a>

            <a href="?page=jpress-job-manager&amp;tab=capabilities" class="nav-tab <?php echo $active_tab == 'capabilities' ? 'nav-tab-active' : ''; ?>">Droits et Utilisateurs</a>

            <a href="?page=jpress-job-manager&amp;tab=template" class="nav-tab <?php echo $active_tab == 'template' ? 'nav-tab-active' : ''; ?>">Templates</a>

            <a href="?page=jpress-job-manager&amp;tab=import-export" class="nav-tab <?php echo $active_tab == 'import-export' ? 'nav-tab-active' : ''; ?>">Import / Export</a>

        </h2>
    </div>
<?php
    include $active_tab . '.tpl.php';
