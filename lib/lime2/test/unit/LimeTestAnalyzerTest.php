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

require_once dirname(__FILE__).'/../bootstrap/unit.php';

LimeAnnotationSupport::enable();

$t = new LimeTest(7);


// @Before

  $file = tempnam(sys_get_temp_dir(), 'lime');
  $output = $t->mock('LimeOutputInterface');
  $analyzer = new LimeTestAnalyzer($output);


// @After

  $file = null;
  $output = null;
  $analyzer = null;


// @Test: The file is called with the argument --output=raw

  // fixtures
  $output->plan(2);
  $output->replay();
  file_put_contents($file, <<<EOF
<?php
if (in_array('--output=raw', \$GLOBALS['argv']))
{
  echo "1..2\n";
}
EOF
  );
  // test
  $analyzer->connect($file);
  while (!$analyzer->done()) $analyzer->proceed();
  // assertions
  $output->verify();


// @Test: If the output cannot be unserialized, an error is reported

  // fixtures
  file_put_contents($file, '<?php echo "\0raw\0Some Error occurred";');
  $output->warning("Could not parse test output. Make sure you don't echo any additional data.", $file, 1);
  $output->replay();
  // test
  $analyzer->connect($file);
  while (!$analyzer->done()) $analyzer->proceed();
  // assertions
  $output->verify();


// @Test: Data sent to the error stream is passed to warning() line by line

  // fixtures
  file_put_contents($file, '<?php file_put_contents("php://stderr", "Error 1\nError 2");');
  $output->warning('Error 1', $file, 0);
  $output->warning('Error 2', $file, 0);
  $output->replay();
  // test
  $analyzer->connect($file);
  while (!$analyzer->done()) $analyzer->proceed();
  // assertions
  $output->verify();


// @Test: PHP errors/warnings are passed to error()/warning()

  // @Test: Case 1 - Invalid identifier

  // fixtures
  file_put_contents($file, '<?php $1invalidname;');
  $output->error(new LimeError("syntax error, unexpected T_LNUMBER, expecting T_VARIABLE or '$'", $file, 1, 'Parse error'));
  $output->replay();
  // test
  $analyzer->connect($file);
  while (!$analyzer->done()) $analyzer->proceed();
  // assertions
  $output->verify();


  // @Test: Case 2 - Failed require

  // fixtures
  file_put_contents($file, '<?php require "foobar.php";');
  $output->warning("Warning: require(foobar.php): failed to open stream: No such file or directory", $file, 1);
  $output->error(new LimeError("require(): Failed opening required 'foobar.php' (include_path='".get_include_path()."')", $file, 1, 'Fatal error'));
  $output->replay();
  // test
  $analyzer->connect($file);
  while (!$analyzer->done()) $analyzer->proceed();
  // assertions
  $output->verify();


