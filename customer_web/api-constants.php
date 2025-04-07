<?php


    define("ROOT_DIR", __DIR__ . "/");
    define("ROOT_CLASS", ROOT_DIR . "/classes/");

    if (stristr($_SERVER["HTTP_ORIGIN"], "")) {

        define("DB_HOST", "localhost");
        define("DB_USERNAME", "");
        define("DB_PASSWORD", "");
        define("DB_NAME","");

    }else{
        define("DB_HOST", "localhost");
        define("DB_USERNAME", "root");
        define("DB_PASSWORD", "");
        define("DB_NAME", "crm");
        define("IMAGE_URL", "http://localhost/crm-api/uploads");
    }