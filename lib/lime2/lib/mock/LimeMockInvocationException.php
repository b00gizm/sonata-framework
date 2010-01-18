<?php

/*
 * This file is part of the Lime test framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Bernhard Schussek <bernhard.schussek@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Thrown when a method invocation should not have been made.
 *
 * This exception is usually wrapped inside a LimeMockInvocation and should not
 * bubble up.
 *
 * @package    Lime
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 * @version    SVN: $Id: LimeMockInvocationException.php 23701 2009-11-08 21:23:40Z bschussek $
 */
class LimeMockInvocationException extends Exception
{
  private
    $invocation   = null;

  /**
   * Constructor.
   *
   * @param  LimeMockInvocation $invocation   The erroneous method invocation
   * @param  string $message                  The message describing why the
   *                                          invocation should not have been
   *                                          made. The message is appended
   *                                          at the method name.
   */
  public function __construct(LimeMockInvocation $invocation, $message)
  {
    parent::__construct($invocation.' '.$message);

    $this->invocation = $invocation;
  }

  /**
   * Returns the invocation that caused this exception.
   *
   * @return LimeMockInvocation
   */
  public function getInvocation()
  {
    return $this->invocation;
  }
}