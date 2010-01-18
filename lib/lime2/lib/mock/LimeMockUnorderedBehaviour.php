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
 * A behaviour that allows methods to be invoked in the any order.
 *
 * @package    Lime
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 * @version    SVN: $Id: LimeMockUnorderedBehaviour.php 23701 2009-11-08 21:23:40Z bschussek $
 * @see        LimeMockBehaviourInterface
 */
class LimeMockUnorderedBehaviour extends LimeMockBehaviour
{
  /**
   * (non-PHPdoc)
   * @see mock/LimeMockBehaviour#invoke($invocation)
   */
  public function invoke(LimeMockInvocation $invocation)
  {
    $exception = null;

    foreach ($this->invocations as $invocationExpectation)
    {
      try
      {
        if ($invocationExpectation->matches($invocation))
        {
          return $invocationExpectation->invoke($invocation);
        }
      }
      catch (LimeMockInvocationException $e)
      {
        // see whether any other expectation matches before rethrowing
        $exception = $e;
      }
    }

    if (!is_null($exception))
    {
      throw $exception;
    }

    parent::invoke($invocation);
  }
}