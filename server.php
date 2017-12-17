<?php

chdir(__DIR__);
require_once 'vendor/autoload.php';

use React\Http\Response;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Server;

$cmd = new Commando\Command();

$cmd->option('p')
  ->aka('port')
  ->default(8080)
  ->describedAs('Listen port');

$cmd->option('h')
  ->aka('host')
  ->default('0.0.0.0')
  ->describedAs('Listen address');

$cmd->option('f')
  ->aka('file')
  ->default('SxGeoCity.dat')
  ->describedAs('Database file');

echo "Loading database\n";

require 'SxGeo.php';

$loop = React\EventLoop\Factory::create();

if (file_exists($cmd['file']) === false || is_readable($cmd['file']) === false) {
  echo "File not found: \"{$cmd['file']}\"\n";
  exit(1);
}

try {
  $SxGeo = new SxGeo($cmd['file'], SXGEO_BATCH | SXGEO_MEMORY);
} catch (Exception $e) {
  echo "Error: " . $e->getMessage() . "\n";
  exit(1);
}

$server = new \React\Http\Server(function (ServerRequestInterface $request) use ($SxGeo) {

  switch ($request->getUri()->getPath()) {

    case '/status':

      return new Response(
        200,
        array('Content-Type' => 'application/json'),
        json_encode(['success' => true])
      );

    case '/query':

      $startTime = microtime(true);
      $queryParams = $request->getQueryParams();
      $result = [];

      $ip = isset($queryParams['ip']) ? (string)$queryParams['ip'] : false;

      if ($ip !== false) {
        try {

          $result = $SxGeo->getCityFull($ip);
          $result['execution'] = number_format(microtime(true) - $startTime, 12);
          $result['success'] = true;


        } catch (\Exception $e) {
          $result = [
            'success' => false,
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'code' => $e->getCode(),
            'execution' => number_format(microtime(true) - $startTime, 12)
          ];
        }
      } else {
        $result = [
          'success' => false,
          'message' => 'Bad arguments',
        ];
      }

      return new Response(
        200,
        array('Content-Type' => 'application/json'),
        json_encode($result)
      );
    default:
      return new Response(404);
  }
});

$socket = new \React\Socket\Server($cmd['host'].':'.$cmd['port'], $loop);
$server->listen($socket);

echo "Starting server {$cmd['host']}:{$cmd['port']} \n";

$loop->run();

