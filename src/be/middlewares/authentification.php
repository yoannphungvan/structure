 <?php

/**
 * Middleware to pull out raw filters and instiate filters class.
 */
use Symfony\Component\HttpFoundation\Request;
use Silex\Component\Security\Core\Encoder\JWTEncoder;
use PROJECT\Exceptions\UnauthorizedException;

$app->before(function (Request $request) use ($app) {
    $route = $app['request']->getUri();
    if (false && strpos($route, '/api/') !== false && strpos($route, $app->configs['security.jwt']['login_route']) === false) {
        try {
            // Get Access token from header
            $tokenHeader = $app['request']->headers->get($app->configs['security.jwt']['header_name']);
            // Remove prefix (eg: Bearer) if needed
            $token = getToken($tokenHeader, $app->configs['security.jwt']['token_prefix']);
            // Decoding the token
            $jwtEncoder = new JWTEncoder(
                $app->configs['security.jwt']['secret_key'],
                $app->configs['security.jwt']['life_time'],
                $app->configs['security.jwt']['algorithm']
            );
            $app['user.jwt.payload'] = $jwtEncoder->decode($token);

            if (empty($token)) {
                throw new UnauthorizedException('Authentication credentials were missing or incorrect.', 401);
            }
        } catch (\UnexpectedValueException $e) {
            throw new UnauthorizedException('Access denied.', 401);
        }
    }
});


function getToken($token, $tokenPrefix)
{
    $prefix = $tokenPrefix;
    if (null === $prefix) {
        return $token;
    }

    if (null === $token) {
        return $token;
    }

    $token = trim(str_replace($prefix, "", $token));

    return $token;
}
