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

use org\pdepend\reflection\ReflectionClassCache;
use org\pdepend\reflection\parser\Parser;
use org\pdepend\reflection\interfaces\ParserContext;
use org\pdepend\reflection\interfaces\SourceResolver;
use org\pdepend\reflection\interfaces\ReflectionClassFactory;

/**
 * This reflection factory utilizes the PHP tokenizer extension to provide a
 * runtime independent version of reflection classes.
 *
 * @category  PHP
 * @package   org\pdepend\reflection\factories
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class StaticReflectionClassFactory implements ReflectionClassFactory
{
    /**
     * The used parser context.
     *
     * @var \org\pdepend\reflection\interfaces\ParserContext
     */
    private $_context = null;

    /**
     * The used source resolver that returns the source file for a given class
     * or interface name.
     *
     * @var \org\pdepend\reflection\interfaces\SourceResolver
     */
    private $_resolver = null;

    /**
     * A simple cache class which holds already parsed and created reflection
     * class instances.
     *
     * @var \org\pdepend\reflection\factories\ReflectionClassCache
     */
    private $_classCache = null;

    /**
     * Constructs a new static reflection factory.
     *
     * @param \org\pdepend\reflection\interfaces\ParserContext  $context  Current
     *        context the will be used by the static reflection parser to retrieve
     *        additional data during the parsing process.
     * @param \org\pdepend\reflection\interfaces\SourceResolver $resolver Used
     *        source resolver that will return a file name for a given class or
     *        interface name.
     */
    public function __construct( ParserContext $context, SourceResolver $resolver )
    {
        $this->_context  = $context;
        $this->_resolver = $resolver;

        $this->_classCache = new ReflectionClassCache();
    }

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
        if ( $this->_classCache->has( $className ) )
        {
            return true;
        }
        return $this->_resolver->hasPathnameForClass( $className );
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
        if ( $this->_classCache->has( $className ) )
        {
            return $this->_classCache->restore( $className );
        }
        return $this->_createClass( $className );
    }

    /**
     * This method will force a source file parsing and then it returns a
     * reflection class for a class or interface named <b>$className</b>.
     *
     * @param string $className Full qualified name of the searched class.
     *
     * @return boolean
     * @throws \ReflectionException When no class with the given name exists.
     */
    private function _createClass( $className )
    {
        $this->_parseFileForClass( $className );
        if ( $this->_classCache->has( $className ) )
        {
            return $this->_classCache->restore( $className );
        }
        throw new \ReflectionException( 'Class ' . $className . ' does not exist' );
    }

    /**
     * This method parses all classes within the source file where a class or
     * interface that matches <b>$className</b> is declared. All found reflection
     * objects will be stored in the internal cache for later reuse.
     *
     * @param string $className Full qualified name of the searched class.
     *
     * @return void
     */
    private function _parseFileForClass( $className )
    {
        $parser  = new Parser( $this->_context );
        $classes = $parser->parseFile( $this->_resolver->getPathnameForClass( $className ) );
        foreach ( $classes as $class )
        {
            $this->_classCache->store( $class );
        }
    }
}