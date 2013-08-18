<?php
require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Asphyxia\Coil\Coil as Coil;
use Hydra\Component\StaticAssets;
use Hydra\Component\ContentFixer;

$app = new Silex\Application();
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/../config/settings.yml'));
$app->register(new StaticAssets\StaticAssets(__DIR__ . '/../web/static/'));

$app['debug']   = $app['config']['debug'];
$app['backend'] = $app['config']['backend'][0];
$ruleSet       = $app['config']['content']['rule_set'];
$app->register(new ContentFixer\ContentFixer($app['config']['content'][$ruleSet]));

$app->get('/', function (Silex\Application $app, Request $request) {
    return $app['static']->fetch($app['config']['static']['index']);
});

$app->get('/{page}', function(Silex\Application $app, Request $request) {
    $page = $request->get('page');

    if (in_array($page, array_keys($app['config']['static']))) {
        return $app['static']->fetch($app['config']['static'][$page]);
    } else {
        return Coil::get($app['backend'] . '/' . $page);
    }

})->value('page', 'browse');

$app->get('/browse/{id}/{page}/{order}/{other}', function(Silex\Application $app, Request $request) {
    $id     = $request->get('id');
    $page   = $request->get('page');
    $order  = $request->get('order');
    $other  = $request->get('other');

    return Coil::get($app['backend'] . '/browse/' . $id . '/' . $page . '/' . $order . '/' . $other);

})->value('page', 0)->assert('page', '\d+')
  ->value('order', 3)->assert('order', '\d+')
  ->value('other', 0)->assert('other', '\d+');

$app->get('/recent/{page}', function(Silex\Application $app, Request $request) {
    $page = $request->get('page');

    return Coil::get($app['backend'] . '/recent/'.$page);

})->value('page', 0)
  ->assert('page', '\d+');

$app->get('/tv/{id}/{season}', function(Silex\Application $app, Request $request) {
    $id     = $request->get('id');
    $season = $request->get('season');

    return Coil::get($app['backend'] . '/tv/' . $id . '/' . $season);

})->value('season', '')
  ->assert('id', '(\d+|all|latest)');

$app->get('/top/{id}', function(Silex\Application $app, Request $request) {
    $id = $request->get('id');

    return Coil::get($app['backend'] . '/top/' . $id);

})->value('id', '');

$app->get('/music/{section}/{name}', function(Silex\Application $app, Request $request) {
    $name    = $request->get('name');
    $section = $request->get('section');

    return Coil::get($app['backend'] . '/music/'.$section.'/' . $name);

});

$app->get('/torrent/{id}/{name}', function(Silex\Application $app, Request $request) {
    $id = $request->get('id');

    return Coil::get($app['backend'] . '/torrent/' . $id);

})->value('name', '')
  ->assert('id', '\d+');

$app->error(function (Exception $e) use ($app) {
  if ($e instanceof NotFoundHttpException) {
    return $app['static']->fetch('404.html');
  }

  return $app['static']->fetch('500.html');

});

$app->after(function (Request $request, Response $response) use ($app) {
    return $response->setContent(
        $app['fixer']->fix($response->getContent())
    );
});

$app->run();
