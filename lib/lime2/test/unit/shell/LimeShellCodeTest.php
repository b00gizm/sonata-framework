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

include dirname(__FILE__).'/../../bootstrap/unit.php';

LimeAnnotationSupport::enable();

$t = new LimeTest(3);


// @Test: PHP code can be executed

  // test
  $command = new LimeShellCode(<<<EOF
echo "Test";
file_put_contents("php://stderr", "Errors");
exit(1);
EOF
  );
  $command->execute();
  // assertions
  $t->is($command->getOutput(), 'Test', 'The output is correct');
  $t->is($command->getErrors(), 'Errors', 'The errors are correct');
  $t->is($command->getStatus(), 1, 'The return value is correct');