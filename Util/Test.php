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
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2006 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pear.php.net/package/PHPUnit2
 * @since      File available since Release 3.0.0
 */

require_once 'PHPUnit2/Util/Filter.php';

PHPUnit2_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Test helpers.
 *
 * @category   Testing
 * @package    PHPUnit2
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2006 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PHPUnit2
 * @since      Class available since Release 3.0.0
 */
class PHPUnit2_Util_Test
{
    /**
     * @param  PHPUnit2_Framework_Test $test
     * @param  boolean                 $asString
     * @return mixed
     * @access public
     * @static
     */
    public static function describe(PHPUnit2_Framework_Test $test, $asString = TRUE)
    {
        if ($asString) {
            if ($test instanceof PHPUnit2_Framework_SelfDescribing) {
                return $test->toString();
            } else {
                return get_class($test);
            }
        } else {
            if ($test instanceof PHPUnit2_Framework_TestCase) {
                return array(
                  get_class($test), $test->getName()
                );
            }

            else if ($test instanceof PHPUnit2_Framework_SelfDescribing) {
                return array('', $test->toString());
            }

            else {
                return array('', get_class($test));
            }
        }
    }

    /**
     * @param  PHPUnit2_Framework_Test       $test
     * @param  PHPUnit2_Framework_TestResult $result
     * @return mixed
     * @access public
     * @static
     */
    public static function lookupResult(PHPUnit2_Framework_Test $test, PHPUnit2_Framework_TestResult $result)
    {
        $testName = self::describe($test);

        foreach ($result->errors() as $error) {
            if ($testName == self::describe($error->failedTest())) {
                return $error;
            }
        }

        foreach ($result->failures() as $failure) {
            if ($testName == self::describe($failure->failedTest())) {
                return $failure;
            }
        }

        foreach ($result->notImplemented() as $notImplemented) {
            if ($testName == self::describe($notImplemented->failedTest())) {
                return $notImplemented;
            }
        }

        foreach ($result->skipped() as $skipped) {
            if ($testName == self::describe($skipped->failedTest())) {
                return $skipped;
            }
        }

        return PHPUnit2_Runner_BaseTestRunner::STATUS_PASSED;
    }
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
?>
