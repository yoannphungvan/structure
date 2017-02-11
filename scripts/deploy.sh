#!/bin/bash

echo ""
echo "Composer install..."
echo ""
php /usr/local/bin/composer install --no-dev -o

echo ""
echo "Remove useless folders..."
echo ""
rm -rf schema_resources
rm -rf migrations
rm -rf tests
rm -rf scripts
rm -rf src/be/portal/tests
rm -rf vendor/be/dompdf/dompdf/www
rm -rf vendor/be/twig/twig/doc
rm -rf vendor/be/twig/twig/test
rm -rf vendor/be/twig/extensions/doc
rm -rf vendor/be/twig/extensions/test
rm -rf vendor/be/predis/predis/tests
rm -rf vendor/be/predis/predis/examples
rm -rf vendor/be/silex/silex/tests
rm -rf vendor/be/silex/silex/doc
rm -rf vendor/be/guzzle/guzzle/tests
rm -rf vendor/be/guzzle/guzzle/docs

echo ""
echo "Remove useless files..."
echo ""
rm -f *.sh *.xml *.json *.lock Vagrantfile README.md web/app.dev.php configs/configs.sample.php src/be/portal/README.md
