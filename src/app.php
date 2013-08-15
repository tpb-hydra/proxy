<?php
require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Asphyxia\Coil\Coil as Coil;

$app = new Silex\Application();
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/../config/settings.yml'));
$app->register(new Asphyxia\Hydra\StaticAssets(__DIR__ . '/../web/static/'));

$app['debug'] = $app['config']['debug'];
$rule_set = $app['config']['content']['rule_set'];
$app->register(new Asphyxia\Hydra\ContentFixer($app['config']['content'][$rule_set]));

$app->get('/', function (Silex\Application $app, Request $request) {
    return $app['static']->fetch($app['config']['static']['index']);
});

$app->get('/{page}', function(Silex\Application $app, Request $request) {
    $page = $request->get('page');

    if (in_array($page, array_keys($app['config']['static']))) {
        return $app['static']->fetch($app['config']['static'][$page]);

    }else{
        return $app['fixer']->fix(
                Coil::get($app['config']['backend'][1] . '/' . $page)
        );

    }

})->value('page', 'browse');

$app->get('/browse/{id}/{page}/{order}/{other}', function(Silex\Application $app, Request $request) {
    $id = $request->get('id');
    $page = $request->get('page');
    $order = $request->get('order');
    $other = $request->get('other');

    return $app['fixer']->fix(
                Coil::get($app['config']['backend'][1] . '/browse/' . $id . '/' . $page . '/' . $order . '/' . $other)
    );

})->value('page', 0)->assert('page', '\d+')
    ->value('order', 3)->assert('order', '\d+')
    ->value('other', 0)->assert('other', '\d+');

$app->get('/recent/{page}', function(Silex\Application $app, Request $request) {

    $page = $request->get('page');

    return $app['fixer']->fix(
        Coil::get($app['config']['backend'][1].'/recent/'.$page)
    );

})->value('page', 0)
    ->assert('page', '\d+');

$app->get('/tv/{id}/{season}', function(Silex\Application $app, Request $request) {
    $id = $request->get('id');
  $season = $request->get('season');

    return $app['fixer']->fix(
                Coil::get($app['config']['backend'][1] . '/tv/' . $id . '/' . $season)
    );

})->value('season', '')
    ->assert('id', '(\d+|all|latest)');

$app->get('/top/{id}', function(Silex\Application $app, Request $request) {
    $id = $request->get('id');

    return $app['fixer']->fix(
                Coil::get($app['config']['backend'][1] . '/top/' . $id)
    );

})->value('id', '');

$app->get('/music/{section}/{name}', function(Silex\Application $app, Request $request) {
    $name = $request->get('name');
    $section = $request->get('section');

    return $app['fixer']->fix(
                Coil::get($app['config']['backend'][1] . '/music/'.$section.'/' . $name)
    );

});

$app->get('/torrent/{id}/{name}', function(Silex\Application $app, Request $request) {
    $id = $request->get('id');

    return $app['fixer']->fix(
                Coil::get($app['config']['backend'][1] . '/torrent/' . $id)
    );

})->value('name', '')
    ->assert('id', '\d+');

$app->error(function (Exception $e) use ($app) {
  if ( $e instanceof NotFoundHttpException){
    return $app['static']->fetch('404.html');
  }
  $code = ($e instanceof HttpException) ? $e->getStatusCode() : 500;
  return $app['static']->fetch('500.html');
});

$app->run();
