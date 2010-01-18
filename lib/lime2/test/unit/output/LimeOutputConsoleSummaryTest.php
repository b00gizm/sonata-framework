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

require_once dirname(__FILE__).'/../../bootstrap/unit.php';

LimeAnnotationSupport::enable();

$t = new LimeTest(39);


// @Before

  $printer = $t->mock('LimePrinter', array('strict' => true));
  $output = new LimeOutputConsoleSummary($printer);


// @After

  $printer = null;
  $output = null;


// @Test: When close() is called, the test summary is printed

  // fixtures
  $printer->printText(str_pad('/test/script', 73, '.'));
  $printer->printLine("ok", LimePrinter::OK);
  $printer->replay();
  // test
  $output->focus('/test/script');
  $output->pass('A passed test', '/test/script', 11);
  $output->close();
  // assertions
  $printer->verify();


// @Test: When close() is called and tests failed, the status is "not ok" and the failed tests are displayed

  // fixtures
  $printer->printText(str_pad('/test/script', 73, '.'));
  $printer->printLine("not ok", LimePrinter::NOT_OK);
  $printer->any('printLine')->times(4);
  $printer->printLine('    ... and 1 more');
  $printer->replay();
  // test
  $output->focus('/test/script');
  $output->fail('A failed test', '/test/script', 11);
  $output->fail('A failed test', '/test/script', 11);
  $output->fail('A failed test', '/test/script', 11);
  $output->fail('A failed test', '/test/script', 11);
  $output->close();
  // assertions
  $printer->verify();


// @Test: When close() is called and warnings appeared in the test, the status is warning and the warnings are displayed

  // fixtures
  $printer->printText(str_pad('/test/script', 73, '.'));
  $printer->printLine("warning", LimePrinter::WARNING);
  $printer->any('printLine')->times(4);
  $printer->printLine('    ... and 1 more');
  $printer->replay();
  // test
  $output->focus('/test/script');
  $output->pass('A passed test', '/test/script', 11);
  $output->warning('A warning', '/test/script', 33);
  $output->warning('A warning', '/test/script', 33);
  $output->warning('A warning', '/test/script', 33);
  $output->warning('A warning', '/test/script', 33);
  $output->close();
  // assertions
  $printer->verify();


// @Test: When close() is called and errors appeared in the test, the status is "not ok" and the errors are displayed

  // fixtures
  $printer->printText(str_pad('/test/script', 73, '.'));
  $printer->printLine("not ok", LimePrinter::NOT_OK);
  $printer->any('printLine')->times(4);
  $printer->printLine('    ... and 1 more');
  $printer->replay();
  // test
  $output->focus('/test/script');
  $output->pass('A passed test', '/test/script', 11);
  $output->error(new LimeError('An error', '/test/script', 22));
  $output->error(new LimeError('An error', '/test/script', 22));
  $output->error(new LimeError('An error', '/test/script', 22));
  $output->error(new LimeError('An error', '/test/script', 22));
  $output->close();
  // assertions
  $printer->verify();


// @Test: When close() is called and todos appeared in the test, the status is "ok" but the todos are displayed

  // fixtures
  $printer->printText(str_pad('/test/script', 73, '.'));
  $printer->printLine("ok", LimePrinter::OK);
  $printer->any('printLine')->times(4);
  $printer->printLine('    ... and 1 more');
  $printer->replay();
  // test
  $output->focus('/test/script');
  $output->pass('A passed test', '/test/script', 11);
  $output->todo('A todo', '/test/script', 22);
  $output->todo('A todo', '/test/script', 22);
  $output->todo('A todo', '/test/script', 22);
  $output->todo('A todo', '/test/script', 22);
  $output->close();
  // assertions
  $printer->verify();


// @Test: When close() is called and the too few tests were executed, a message is printed

  // fixtures
  $printer->printText(str_pad('/test/script', 73, '.'));
  $printer->printLine("not ok", LimePrinter::NOT_OK);
  $printer->any('printLine')->once();
  $printer->printLine('    Looks like you planned 2 tests but only ran 1.');
  $printer->replay();
  // test
  $output->focus('/test/script');
  $output->plan(2);
  $output->pass('A passed test', '/test/script', 11);
  $output->close();
  // assertions
  $printer->verify();


// @Test: When close() is called and the too many tests were executed, a message is printed

  // fixtures
  $printer->printText(str_pad('/test/script', 73, '.'));
  $printer->printLine("not ok", LimePrinter::NOT_OK);
  $printer->any('printLine')->once();
  $printer->printLine('    Looks like you only planned 1 tests but ran 2.');
  $printer->replay();
  // test
  $output->focus('/test/script');
  $output->plan(1);
  $output->pass('A passed test', '/test/script', 11);
  $output->pass('A passed test', '/test/script', 11);
  $output->close();
  // assertions
  $printer->verify();


// @Test: flush() prints a summary of all files if failures occured

  // fixtures
  $printer = $t->mock('LimePrinter'); // non-strict
  $printer->any('printText')->atLeastOnce();
  $printer->any('printLine')->atLeastOnce();
  $printer->printBox(' Failed 2/5 test scripts, 60.00% okay. 1/5 subtests failed, 80.00% okay.', LimePrinter::NOT_OK);
  $printer->replay();
  $output = new LimeOutputConsoleSummary($printer);
  // test
  $output->focus('/test/script1');
  $output->pass('A passed test', '/test/script', 11);
  $output->close();
  $output->focus('/test/script2');
  $output->pass('A passed test', '/test/script2', 11);
  $output->warning('A warning', '/test/script2', 11);
  $output->close();
  $output->focus('/test/script3');
  $output->fail('A failed test', '/test/script3', 11);
  $output->close();
  $output->focus('/test/script4');
  $output->pass('A passed test', '/test/script', 11);
  $output->error(new LimeError('An error', '/test/script', 11));
  $output->close();
  $output->focus('/test/script5');
  $output->pass('A passed test', '/test/script', 11);
  $output->close();
  $output->flush();
  // assertions
  $printer->verify();


// @Test: flush() prints a success message if everything went fine

  // fixtures
  $printer = $t->mock('LimePrinter'); // non-strict
  $printer->any('printText')->atLeastOnce();
  $printer->any('printLine')->atLeastOnce();
  $printer->printBox(' All tests successful.', LimePrinter::HAPPY);
  $printer->printBox(' Files=2, Tests=3, Time=00:01, Processes=3', LimePrinter::HAPPY);
  $printer->replay();
  $output = new LimeOutputConsoleSummary($printer, array('processes' => 3));
  // test
  $output->focus('/test/script1');
  $output->pass('A passed test', '/test/script1', 11);
  $output->close();
  $output->focus('/test/script2');
  $output->pass('A passed test', '/test/script2', 11);
  $output->pass('A passed test', '/test/script2', 11);
  $output->close();
  $output->flush();
  // assertions
  $printer->verify();


// @Test: If the base dir is set, the test files are truncated

  // fixtures
  $output = new LimeOutputConsoleSummary($printer, array('base_dir' => '/test'));
  $printer->reset();
  $printer->printText(str_pad('/script', 73, '.'));
  $printer->printLine("ok", LimePrinter::OK);
  $printer->replay();
  // test
  $output->focus('/test/script');
  $output->pass('A passed test', '/test/script', 11);
  $output->close();
  // assertions
  $printer->verify();


// @Test: File extensions are omitted in the output

  // fixtures
  $printer->printText(str_pad('/test/script', 73, '.'));
  $printer->printLine("ok", LimePrinter::OK);
  $printer->replay();
  // test
  $output->focus('/test/script.php');
  $output->pass('A passed test', '/test/script.php', 11);
  $output->close();
  // assertions
  $printer->verify();


// @Test: Too long file names are truncated

  // fixtures
  $printer->reset();
  $printer->printText(str_repeat('x', 59).'/test/script..');
  $printer->printLine("ok", LimePrinter::OK);
  $printer->replay();
  // test
  $output->focus(str_repeat('x', 80).'/test/script');
  $output->pass('A passed test', '/test/script', 11);
  $output->close();
  // assertions
  $printer->verify();
