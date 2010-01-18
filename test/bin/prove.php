<?php

/*
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../../lib/lime2/lib/LimeAutoloader.php';
LimeAutoloader::register();

LimeAnnotationSupport::enable();

$h = new LimeTestSuite(array(
  'force_colors' => isset($argv) && in_array('--color', $argv),
  'verbose'      => isset($argv) && in_array('--verbose', $argv),
));
$h->base_dir = realpath(dirname(__FILE__).'/..');

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__FILE__).'/../unit'), RecursiveIteratorIterator::LEAVES_ONLY) as $file)
{
  if (preg_match('/Test\.php$/', $file))
  {
    $h->register($file->getRealPath());
  }
}

exit($h->run() ? 0 : 1);
