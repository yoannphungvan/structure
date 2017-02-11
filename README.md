PROJECT repository

## Getting Started

## Style Guide
All code in the repo should conform strictly to the PSR-2 PHP coding standard.

You can find a detailed reference with examples here: [PSR-2 Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

## Comment Markers
1. Use 'TODO' comments to mark a pending future task or something that needs to be completed,
but doesn't impact the code functionality as is.
1. Use 'FIXME' comments to denote something that should be refactored or fixed to make the code better.
1. Use 'HACK' to mark something hacky or unclean in the code or that otherwise defies intuition.

## Install dependancies
composer install

## Update dependancies
composer update

## Generate autoload
composer du

## Generate documentation
apidoc -i src/ -o doc/

## ngrok 
ngrok http -host-header=rewrite mydomain.com:80

