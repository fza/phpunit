<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PHPUnit
 *
 * Copyright (c) 2002-2006, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRIC
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Testing
 * @package    PHPUnit2
 * @author     Jan Borsodi <jb@ez.no>
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2006 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pear.php.net/package/PHPUnit2
 * @since      File available since Release 3.0.0
 */

require_once 'PHPUnit2/Framework.php';
require_once 'PHPUnit2/Util/Filter.php';
require_once 'PHPUnit2/Extensions/MockObject/Matcher/Invocation.php';
require_once 'PHPUnit2/Extensions/MockObject/Invocation.php';

PHPUnit2_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Main matcher which defines a full expectation using method, parameter and invocation matchers.
 *
 * This matcher encapsulates all the other matchers and allows the builder to set
 * the specific matchers when the appropriate methods are called (once(), where()
 * etc.).
 *
 * All properties are public so that they can easily be accessed by the builder.
 *
 * @category   Testing
 * @package    PHPUnit2
 * @author     Jan Borsodi <jb@ez.no>
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2006 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PHPUnit2
 * @since      Class available since Release 3.0.0
 */
class PHPUnit2_Extensions_MockObject_Matcher implements PHPUnit2_Extensions_MockObject_Matcher_Invocation
{
    public $invocationMatcher;

    public $afterMatchBuilderId = NULL;

    public $afterMatchBuilderIsInvoked = FALSE;

    public $methodNameMatcher = NULL;

    public $parametersMatcher = NULL;

    public $stub = NULL;

    public function __construct(PHPUnit2_Extensions_MockObject_Matcher_Invocation $invocationMatcher)
    {
        $this->invocationMatcher = $invocationMatcher;
    }

    public function toString()
    {
        $list = array();

        if ($this->invocationMatcher !== NULL) {
            $list[] = $this->invocationMatcher->toString();
        }

        if ($this->methodNameMatcher !== NULL) {
            $list[] = 'where ' . $this->methodNameMatcher->toString();
        }

        if ($this->parametersMatcher !== NULL) {
            $list[] = 'and ' . $this->parametersMatcher->toString();
        }

        if ($this->afterMatchBuilderId !== NULL) {
            $list[] = 'after ' . $this->afterMatchBuilderId;
        }

        if ($this->stub !== NULL) {
            $list[] = 'will ' . $this->stub->toString();
        }

        return join(' ', $list);
    }

    public function invoked(PHPUnit2_Extensions_MockObject_Invocation $invocation)
    {
        if ($this->invocationMatcher === NULL) {
            throw new RuntimeException('No invocation matcher is set');
        }

        if ($this->methodNameMatcher === NULL) {
            throw new RuntimeException('No method matcher is set');
        }

        if ($this->afterMatchBuilderId !== NULL) {
            $builder = $invocation->object->getInvocationMocker()->lookupId($this->afterMatchBuilderId);

            if (!$builder) {
                throw new RuntimeException(
                  sprintf(
                    'No builder found for match builder identification <%s>',

                    $this->afterMatchBuilderId
                  )
                );
            }

            $matcher = $builder->getMatcher();

            if ($matcher && $matcher->invocationMatcher->hasBeenInvoked()) {
                $this->afterMatchBuilderIsInvoked = TRUE;
            }
        }

        $this->invocationMatcher->invoked($invocation);

        try {
            if ( $this->parametersMatcher !== NULL &&
                !$this->parametersMatcher->matches($invocation)) {
                $this->parametersMatcher->verify();
            }
        }

        catch (PHPUnit2_Framework_ExpectationFailedException $e) {
            throw new PHPUnit2_Framework_ExpectationFailedException(
              sprintf(
                "Expectation failed for %s when %s\n%s",

                $this->methodNameMatcher->toString(),
                $this->invocationMatcher->toString(),
                $e->getDescription()
              ),
              $e->getComparisonFailure()
            );
        }

        if ($this->stub) {
            return $this->stub->invoke($invocation);
        }

        return NULL;
    }

    public function matches(PHPUnit2_Extensions_MockObject_Invocation $invocation)
    {
        if ($this->afterMatchBuilderId !== NULL) {
            $builder = $invocation->object->getInvocationMocker()->lookupId($this->afterMatchBuilderId);

            if (!$builder) {
                throw new RuntimeException(
                  sprintf(
                    'No builder found for match builder identification <%s>',

                    $this->afterMatchBuilderId
                  )
                );
            }

            $matcher = $builder->getMatcher();

            if (!$matcher) {
                return FALSE;
            }

            if (!$matcher->invocationMatcher->hasBeenInvoked()) {
                return FALSE;
            }
        }

        if ($this->invocationMatcher === NULL) {
            throw new RuntimeException('No invocation matcher is set');
        }

        if ($this->methodNameMatcher === NULL) {
            throw new RuntimeException('No method matcher is set');
        }

        if (!$this->invocationMatcher->matches($invocation)) {
            return FALSE;
        }

        try {
            if (!$this->methodNameMatcher->matches($invocation)) {
                return FALSE;
            }
        }

        catch (PHPUnit2_Framework_ExpectationFailedException $e) {
            throw new PHPUnit2_Framework_ExpectationFailedException(
              sprintf(
                "Expectation failed for %s when %s\n%s",

                $this->methodNameMatcher->toString(),
                $this->invocationMatcher->toString(),
                $e->getDescription()
              ),
              $e->getComparisonFailure()
            );
        }

        return TRUE;
    }

    public function verify()
    {
        if ($this->stub !== NULL) {
            return;
        }

        if ($this->invocationMatcher === NULL) {
            throw new RuntimeException('No invocation matcher is set');
        }

        if ($this->methodNameMatcher === NULL) {
            throw new RuntimeException('No method matcher is set');
        }

        $this->invocationMatcher->verify();

        try {
            if ($this->parametersMatcher !== NULL) {
                $this->parametersMatcher->verify();
            }
        }

        catch (PHPUnit2_Framework_ExpectationFailedException $e) {
            throw new PHPUnit2_Framework_ExpectationFailedException(
              sprintf(
                "Expectation failed for %s when %s\n%s",

                $this->methodNameMatcher->toString(),
                $this->invocationMatcher->toString(),
                $e->getDescription()
              ),
              $e->getComparisonFailure()
            );
        }
    }
}

require_once 'PHPUnit2/Extensions/MockObject/Matcher/AnyParameters.php';
require_once 'PHPUnit2/Extensions/MockObject/Matcher/InvokedAtIndex.php';
require_once 'PHPUnit2/Extensions/MockObject/Matcher/InvokedAtLeastOnce.php';
require_once 'PHPUnit2/Extensions/MockObject/Matcher/InvokedCount.php';
require_once 'PHPUnit2/Extensions/MockObject/Matcher/InvokedRecorder.php';
require_once 'PHPUnit2/Extensions/MockObject/Matcher/MethodName.php';
require_once 'PHPUnit2/Extensions/MockObject/Matcher/Parameters.php';
require_once 'PHPUnit2/Extensions/MockObject/Matcher/StatelessInvocation.php';

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
?>
