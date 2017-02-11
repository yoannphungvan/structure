<?php

/* ---------------------------------------------------------
 * locales/locales.php
 *
 * Setup of localization.
 *
 * Copyright 2015 - PROJECT
 * ---------------------------------------------------------*/

// Set language
putenv('LC_ALL=' . $app['locale'] . '.' . $app['charset']);
setlocale(LC_ALL, $app['locale'] . '.' . $app['charset']);

// Specify the location of the translation tables
bindtextdomain($app['domain'], PATH_LOCALES);
bind_textdomain_codeset($app['domain'], $app['charset']);

// Choose domain
textdomain($app['domain']);
