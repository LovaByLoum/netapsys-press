<?php
define('WPI_URL',plugin_dir_url( dirname(__FILE__)));
define('WPI_PATH', realpath(plugin_dir_path(__FILE__).'../').  DIRECTORY_SEPARATOR);
define('WPI_THEME_MODEL_PATH', WPI_PATH . 'models'.  DIRECTORY_SEPARATOR .'themes'.  DIRECTORY_SEPARATOR);
define('WPI_MUPLUGINS_MODEL_PATH', WPI_PATH . 'models'.  DIRECTORY_SEPARATOR .'mu-plugins'.  DIRECTORY_SEPARATOR);
define('WPI_PROFILES_PATH', WPI_PATH . 'profiles'.  DIRECTORY_SEPARATOR);