 <?php

/**
 * Middleware to pull out raw filters and instiate filters class.
 */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


// $app->after(function (Request $req, Response $res) use ($app) {
//     $route = $app['request']->getUri();
//     if (strpos($route, '/api/') !== false) {
//         if ($res instanceOf \Exception) {
//             $result = [
//                 'status' => 'error' ,
//                 'code'   => $res->getCode(),
//                 'response' => $res->getMessage()
//             ];
//         } else {
//             $result = [
//                 'status' => 'ok' ,
//                 'code'   => '200',
//                 'response' => $res->getContent()
//             ];
//         }
//         return $app->json($result);
//     }
// });
