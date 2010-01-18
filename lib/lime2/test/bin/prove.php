<?php

/*
 * This file is part of the Lime framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Bernhard Schussek <bernhard.schussek@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once(dirname(__FILE__).'/../../lib/LimeAutoloader.php');

LimeAutoloader::register();

$baseDir = realpath(dirname(__FILE__).'/../..');

$s = new LimeTestSuite(array(
  'force_colors' => isset($argv) && in_array('--color', $argv),
  'verbose'      => isset($argv) && in_array('--verbose', $argv),
  'base_dir'     => $baseDir,
));

$s->registerGlob($baseDir.'/test/unit/*Test.php');
$s->registerGlob($baseDir.'/test/unit/*/*Test.php');

exit($s->run() ? 0 : 1);