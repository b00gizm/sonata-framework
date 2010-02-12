 <?php

 /**
  * This file is part of the Sonata RESTful PHP framework
  * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
  *
  * @author Pascal Cremer <b00giZm@gmail.com>
  */

// Register all necessary autoloaders
require_once dirname(__FILE__).'/../lib/lime2/lib/LimeAutoloader.php';
LimeAutoloader::register();

require_once dirname(__FILE__).'/../../framework/Autoloader.class.php';
Sonata_Autoloader::register();

// Enable annotation support for Lime2
LimeAnnotationSupport::enable();
