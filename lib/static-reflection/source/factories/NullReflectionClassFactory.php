<?php
/**
 * This file is part of the static reflection component.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 *   * Neither the name of Manuel Pichler nor the names of his
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
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  PHP
 * @package   org\pdepend\reflection\factories
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

namespace org\pdepend\reflection\factories;

use org\pdepend\reflection\api\NullReflectionClass;
use org\pdepend\reflection\interfaces\ReflectionClassFactory;

/**
 * This reflection factory implementation will return so called <b>NULL</b>
 * reflection class instances. These objects are used as placeholders for
 * unknown source fragments within an analyzed project.
 *
 * @category  PHP
 * @package   org\pdepend\reflection\factories
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class NullReflectionClassFactory implements ReflectionClassFactory
{
    /**
     * Internal cache with already created reflection class instances.
     *
     * @var array(\org\pdepend\reflection\api\NullReflectionClass)
     */
    private $_reflectionClasses = array();

    /**
     * This method will return <b>true</b> when the concrete reflection factory
     * knows a class or interface for the given name.
     *
     * @param string $className Full qualified name of the searched class.
     *
     * @return boolean
     */
    public function hasClass( $className )
    {
        return true;
    }

    /**
     * This method creates a <b>\ReflectionClass</b> instance for a class or
     * interfact that matches the given name. It will throw an exception when
     * no matching class or interface exists.
     *
     * @param string $className Full qualified name of the searched class.
     *
     * @return boolean
     * @throws \ReflectionException When no class with the given name exists.
     */
    public function createClass( $className )
    {
        if ( isset( $this->_reflectionClasses[$className] ) === false )
        {
            $this->_reflectionClasses[$className] = new NullReflectionClass( $className );
        }
        return $this->_reflectionClasses[$className];
    }
}