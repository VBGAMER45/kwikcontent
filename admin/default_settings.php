<?php
//provide the root domain for your site
define("BASE_DOMAIN", "%base%");

//provide the full path of your site
define("BASE_URL", "%fullurl%");

define("SMARTY_COMPILE_DIR", BASE_DIRECTORY."templates_c/%site%/");
define("SMARTY_CACHE_DIR", BASE_DIRECTORY."cache/%site%/");

$multi_prefix = '%prefix%';
?>