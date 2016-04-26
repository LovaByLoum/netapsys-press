<?php
/**
 * envoier les comptes user par email
 */

$wploadpath = realpath(dirname(__FILE__).'/../../../../');
require_once( $wploadpath . '/wp-load.php' );

global $wpdb, $wp_utils;

$offset = get_option('send_user_access_offset');
update_option('send_user_access_offset',$offset+100);

add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

$users = get_users(array(
        'role' => array('subscriber', 'admin_club'),
        'offset' => $offset,
        'number' => 100
    ));

foreach ($users as $user) {
    if(!empty($user->data->user_email)){
        $emailto = $user->data->user_email;
        $login = $user->data->user_login;
        $displayname = $user->data->display_name;
        //$pass = sanitize_title(mb_strtolower(trim(str_replace(" ","",$displayname))));
        $pass = wp_generate_password( 12, false);

        echo $emailto . ' - ' . $pass . '<br>';

        $wp_utils->log($login . ' / ' . $pass, 'user_account.txt');

        //change password
        wp_set_password($pass, $user->ID);

        //mail
        $message  = "Madame, Monsieur,<br> C'est avec plaisir que nous vous informons de la mise en ligne du nouveau site de la FFCV : <a href='".site_url(). "'>www.ffcv.org</a>.<br>Vous y trouverez les actualités, les événements de la FFCV, mais aussi un forum de discussion. N'hésitez pas à nous transmettre vos remarques par le biais du formulaire de contact. <br><br>Un compte a été crée pour que vous bénéficiiez de privilèges sur le site, comme écrire sur le forum.<br>Voici les accès pour acceder à votre compte : <br>Identifiant : <strong>" . $login  ."</strong><br>Mot de passe : <strong>" . $pass."</strong><br><br>Pour modifier vos informations, rendez-vous dans votre <a href='". site_url('/wp-admin'). "'>page de profil</a>.<br><br> Très cordialement,";

        $html = '
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Fédération Française de Char à Voile</title>
    </head>
    <body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0">
    <table width="700" border="0" cellspacing="2" cellpadding="2">
        <tr>
            <td width="194"><a href="' . site_url() .'" target="_blank"><img src="' . get_template_directory_uri().'/images/logo.png" alt="FFCV" width="194" height="266" border="0" style="height:266px;display:block;text-decoration:none;"></a></td>
            <td width="309" valign="top" bgcolor="#ffffff" style="font-size:12px;display:block;">
                <font face="Arial, Helvetica, sans-serif" color="#757575">' . $message. '</font></td>
        </tr>
    </table>
    </body>
    </html>
    ';

        $headers[] = 'From: Fédération Française de Char à Voile <noreply@ffcv.org>';
        wp_mail($emailto, 'LA FFCV A UN NOUVEAU SITE',$html,$headers);

    }
}
