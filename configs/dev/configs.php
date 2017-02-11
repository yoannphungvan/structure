<?php

return [
	'debug' => true,
	'repositories' => [
		'mysql' => [
			'host'     => 'localhost',
			'username' => 'user',
			'password' => '123456',
			'db'       => 'questions',
			'port' => '3306',

		],
		'redis' => [
			'host' => '4.4.4.17',
			'port' => '6379'
		],
	],
	'messaging' => [
		'twilio' => [
			'accountSid' => 'AC269d99a010c66bbd3e03defbc53b4293',
			'authToken' => '3799752f1a5461222925efc1a0c0a619',
			'phonenumber' => '+14387949792' 
		] 
	],
	'twig' => [
		'cache' => false
	],
	'logging' => [
		'monolog' => [
			'name' => 'app',
			'level' => Monolog\Logger::DEBUG,
			'logfile' => PATH_ROOT . '/logs/app.log',
			'session_id' => uniqid(rand())
		]
	],
	'cors' => [
		'domains' => '*'
	]
];
