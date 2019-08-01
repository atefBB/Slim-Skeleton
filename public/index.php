<?php
require '../vendor/autoload.php';

// Prepare app
$app = new \Slim\Slim(array(
    'templates.path' => '../templates',
));

// Create monolog logger and store logger in container as singleton
// (Singleton resources retrieve the same log resource definition each time)
$app->container->singleton('log', function () {
    $log = new \Monolog\Logger('slim-skeleton');
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../logs/app.log', \Monolog\Logger::DEBUG));
    return $log;
});

// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset'          => 'utf-8',
    'cache'            => realpath('../templates/cache'),
    'auto_reload'      => true,
    'strict_variables' => false,
    'autoescape'       => true,
);

$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

// Define routes
$app->get('/', function () use ($app) {
    // Sample log message
    $app->log->info("Slim-Skeleton '/' route");
    // Render index view
    $app->render('index.html');
});

$app->get("/job", function () use ($app) {
    try {
        // Required if redis is located elsewhere
        Resque::setBackend('172.17.0.1:6379');

        $token = Resque::enqueue(
            'default', Queue\Jobs\MyJob::class,
            array(
                'name' => 'Chris',
            )
        );

        echo "Job #{$token} added with success !";
    } catch (Exception $e) {
        $app->log->info(
            sprintf("Error while adding job to queue, with message: %s", $e->getMessage())
        );
    }
});

// Run app
$app->run();
