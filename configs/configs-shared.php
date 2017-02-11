<?php

return [
	'localisation' => [
		'locale'  => 'en_US',
		'domain'  => 'en',
		'charset' => 'UTF-8'
	],
	'security.jwt' => [
		'secret_key' => '1Gwk7s1AdQaj014TCq4yiVG5JlF2qc94',
	    'life_time'  => 86400,
	    'algorithm'  => ['HS256'],
	    'login_route'  => 'api/user/login',
	    'header_name' => 'X-Access-Token',
	    'token_prefix' => 'Bearer'
	],
];

