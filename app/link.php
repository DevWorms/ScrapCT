<?php

	define("APP_URL", "http://localhost/ScrapCT");

    function app_url() {
        if ((strtolower(substr(APP_URL, 0, 7)) == "http://") || (strtolower(substr(APP_URL, 0, 8)) == "https://")) {
            if (substr(APP_URL, -1) == "/") {
                return APP_URL;
            } else {
                return APP_URL . "/";
            }
        } else {
            if (substr(APP_URL, -1) == "/") {
                return "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://" . APP_URL;
            } else {
                return "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://" . APP_URL . "/";
            }
        }
    }