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

PHPUnit2_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Constraint which checks if a value is of a specified type.
 *
 * The expected value is passed in the constructor.
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
class PHPUnit2_Framework_Constraint_IsType implements PHPUnit2_Framework_Constraint
{
    private $type;

    public function __construct($type)
    {
        switch ($type) {
            case 'integer':
            case 'int':
            case 'float':
            case 'string':
            case 'boolean':
            case 'bool':
            case 'null':
            case 'array':
            case 'object': {
              break;
            }

            default: {
              throw new InvalidArgumentException(
                sprintf(
                  'Type specified for PHPUnit2_Framework_Constraint_IsType <%s> is not a valid type.',

                  $type
                )
              );
            }
        }

        $this->type = $type;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @parameter mixed $other Value or object to evaluate.
     * @return bool
     */
    public function evaluate($other)
    {
        switch ($this->type) {
            case 'integer':
            case 'int': {
              return is_integer($other);
            }

            case 'float': {
              return is_float($other);
            }

            case 'string': {
              return is_string($other);
            }

            case 'boolean':
            case 'bool': {
              return is_bool($other);
            }

            case 'null': {
              return is_null($other);
            }

            case 'array': {
              return is_array($other);
            }

            case 'object': {
              return is_object($other);
            }
        }
    }

    /**
     * @param   mixed   $other The value passed to evaluate() which failed the
     *                         constraint check.
     * @param   string  $description A string with extra description of what was
     *                               going on while the evaluation failed.
     * @throws  PHPUnit2_Framework_ExpectationFailedException
     */
    public function fail($other, $description)
    {
        switch ($this->type) {
            case 'integer':
            case 'int': {
              $expected = 1;
            }
            break;

            case 'float': {
              $expected = 1.1;
            }
            break;

            case 'string': {
              $expected = 'str';
            }
            break;

            case 'boolean':
            case 'bool': {
              $expected = TRUE;
            }
            break;

            case 'null': {
              $expected = NULL;
            }
            break;

            case 'array': {
              $expected = array(1);
            }
            break;

            case 'object': {
              $expected = new Exception();
            }
            break;
        }

        throw new PHPUnit2_Framework_ExpectationFailedException(
          $description,
          new PHPUnit2_Framework_ComparisonFailure_Type($expected, $other)
        );
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     * @access public
     */
    public function toString()
    {
        return sprintf(
          'is type <%s>',

          $this->type
        );
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
