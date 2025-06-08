<?php

declare(strict_types=1);

require __DIR__ . "/../../vendor/autoload.php";

use Framework\App;
use App\Config\Paths;
use Dotenv\Dotenv;

use function App\Config\{registerRoutes, registerMiddleware};

$app = new App (Paths::SOURCE . "/App/container-definitions.php");

$dot_env = Dotenv::createImmutable(Paths::ROOT);
$dot_env->load();

registerRoutes($app);
registerMiddleware($app);

return $app;