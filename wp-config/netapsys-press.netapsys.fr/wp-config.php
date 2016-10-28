<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'netapsys-press');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'dev');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'D7JoT6MwWwdWUP5vX91h');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8');

/** Type de collation de la base de données.
 * N'y touchez que si vous savez ce que vous faites.
 */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'wa=.lrwP]p@68IuAadf1@^LdOCKynIky+3K+4+@4GOR+Z{jgz7kGe9DLV|R1o@e^');
define('SECURE_AUTH_KEY',  'r#F5e$K-cI_+td{bG!s@.Bry(cyd%$8*#0o6!&x52V,0TNjt*BF,AB,2(KP:,pN`');
define('LOGGED_IN_KEY',    ':LM;A|{t/uY<-o`3Sp}$:6Kxc[3<js?[.WuVS2hh+{LRVZsN-NC;aw&LXj%SaqnN');
define('NONCE_KEY',        'Ip@Wh-mNPC5-5:c|t~|Rlh$V%+CQlvb`qNJp|l]=;@|W(6|CNUjh942EZo+g+9vK');
define('AUTH_SALT',        'u07mv3qT?F(#)-:Rf_,+q#Jy1,Cx,p]dRjf&PsRI ~EYEq(8[V(pnY?BQkxN(N-B');
define('SECURE_AUTH_SALT', 'v`tv.Ls<M9sNpi;=g2ja.?[X*5$U/>NeG1x~P9=R,:.P+0@mF9+KTD4n[`<@[85-');
define('LOGGED_IN_SALT',   '&|Ak/_+eQ(-,4~_bxi+n*FG]^jA{)xWW %UqwPgfAH+tXE]%$kYoTX5KJ?PX;K/}');
define('NONCE_SALT',       '+)cd;f]#AhSf!QE^F(u%E{Q>$E*QlBZ|1-_=,rpu]-/Ye5J+bFBfwl&6[u2$Ba(o');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'wp_';

/**
 * Pour les développeurs : le mode deboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 */
define('WP_DEBUG', false);

define('DISABLE_WP_CRON', true);
