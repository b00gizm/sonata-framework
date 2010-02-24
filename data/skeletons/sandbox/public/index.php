<?php

require_once dirname(__FILE__).'/../config/bootstrap.php';
require_once dirname(__FILE__).'/../lib/SandboxApp.class.php';

$app = new SandboxApp('prod', false);
$app->run();
