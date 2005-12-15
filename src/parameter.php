<?php
/**
 * File containing the ezcConsoleParameter class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class for handling console parameters.
 * This class allows the complete handling of parameters submitted
 * to a console based application.
 *
 * <code>
 * $inputHandler = new ezcConsoleParameter();
 * 
 * // Register simple parameter -h/--help
 * $inputHandler->registerOption( new ezcConsoleOption( 'h', 'help' ) );
 * 
 * // Register complex parameter -f/--file
 * $file = new ezcConsoleOption(
 *  'f',
 *  'file',
 *  ezcConsoleParameter::TYPE_STRING,
 *  null,
 *  false,
 *  'Process a file.',
 *  'Processes a single file.'
 * );
 * $inputHandler->registerOption( $file );
 * 
 * // Manipulate parameter -f/--file after registration
 * $file->multiple = true;
 * 
 * // Register another complex parameter that depends on -f and excludes -h
 * $dir = new ezcConsoleOption(
 *  'd',
 *  'dir',
 *  ezcConsoleParameter::TYPE_STRING,
 *  null,
 *  true,
 *  'Process a directory.',
 *  'Processes a complete directory.',
 *  array( new ezcConsoleOptionRule( $inputHandler->getOption( 'f' ) ) ),
 *  array( new ezcConsoleOptionRule( $inputHandler->getOption( 'h' ) ) )
 * );
 * $inputHandler->registerOption( $dir );
 * 
 * // Register an alias for this parameter
 * $inputHandler->registerAlias( 'e', 'extended-dir', $dir );
 * 
 * // Process registered parameters and handle errors
 * try
 * {
 *      $inputHandler->process();
 * }
 * catch ( ezcConsoleParameterException $e )
 * {
 *      if ( $e->code === ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET )
 *      {
 *          $consoleOut->outputText(
 *              'Parameter ' . isset( $e->param ) ? $e->param->name : 'unknown' . " may not occur here.\n", 'error'
 *          );
 *      }
 *      exit( 1 );
 * }
 * 
 * // Process a single parameter
 * $file = $inputHandler->getOption( 'f' );
 * if ( $file->value === false )
 * {
 *      echo "Option -{$file->short}/--{$file->long} was not submitted.\n";
 * }
 * elseif ( $file->value === true )
 * {
 *      echo "Option -{$file->short}/--{$file->long} was submitted without value.\n";
 * }
 * else
 * {
 *      echo "Option -{$file->short}/--{$file->long} was submitted with value <".var_export($file->value, true).">.\n";
 * }
 * 
 * // Process all parameters at once:
 * foreach ( $inputHandler->getValues() as $paramShort => $val )
 * {
 *      switch (true)
 *      {
 *          case $val === false:
 *              echo "Option $paramShort was not submitted.\n";
 *              break;
 *          case $val === true:
 *              echo "Option $paramShort was submitted without a value.\n";
 *              break;
 *          case is_array($val):
 *              echo "Option $paramShort was submitted multiple times with value: <".implode(', ', $val).">.\n";
 *              break;
 *          default:
 *              echo "Option $paramShort was submitted with value: <$val>.\n";
 *              break;
 *      }
 * }
 * </code>
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleParameter
{
    /**
     * Option does not cary a value.
     */
    const TYPE_NONE     = 1;

    /**
     * Option takes an integer value.
     */
    const TYPE_INT      = 2;

    /**
     * Option takes a string value. 
     */
    const TYPE_STRING   = 3;

    /**
     * Array of option definitions, indexed by number.
     * This array stores the ezcConsoleOption objects representing
     * the options.
     *
     * For lookup of a option after it's short or long values the attributes
     * @link ezcConsoleParameter::$optionShort
     * @link ezcConsoleParameter::$optionLong
     * are used.
     * 
     * @var array(int => array)
     */
    private $options = array();

    /**
     * Short option names. Each references a key in 
     * {@link ezcConsoleParameter::$options}.
     * 
     * @var array(string => int)
     */
    private $optionShort = array();

    /**
     * Long option names. Each references a key in 
     * {@link ezcConsoleParameter::$options}.
     * 
     * @var array(string => int)
     */
    private $optionLong = array();

    /**
     * Arguments, if submitted, are stored here. 
     * 
     * @var array
     */
    private $arguments = array();

    /**
     * Create parameter handler
     */
    public function __construct()
    {
    }

    /**
     * Register a new option.
     * This method adds a new option to your option collection. If allready a
     * option with the assigned short or long value exists, an exception will
     * be thrown.
     *
     * @see ezcConsoleParameter::unregisterOption()
     *
     * @param ezcConsoleOption $option The option to register.
     *
     */
    public function registerOption( ezcConsoleOption $option )
    {
        foreach ( $this->optionShort as $short => $ref )
        {
            if ( $short === $option->short ) 
            {
                throw new ezcConsoleParameterException( 
                    "A parameter with the short name <{$short}> is already registered.",
                    ezcConsoleParameterException::PARAMETER_ALREADY_REGISTERED,
                    $option
                );
            }
        }
        foreach ( $this->optionLong as $long => $ref )
        {
            if ( $long === $option->long ) 
            {
                throw new ezcConsoleParameterException( 
                    "A parameter with the long name <{$long}> is already registered.",
                    ezcConsoleParameterException::PARAMETER_ALREADY_REGISTERED,
                    $option
                );
            }
        }
        $this->options[] = $option;
        $this->optionLong[$option->long] = $option;
        $this->optionShort[$option->short] = $option;
    }

    /**
     * Register an alias to a option.
     * Registers a new alias for an existing option. Aliases may
     * then be used as if they were real option.
     *
     * @see ezcConsoleParameter::unregisterAlias()
     *
     * @param string $short                    Shortcut of the alias
     * @param string $long                     Long version of the alias
     * @param ezcConsoleOption $option Reference to an existing option
     *
     *
     * @throws ezcConsoleParameterException
     *         If the referenced option does not exist
     *         {@link ezcConsoleParameterException::PARAMETER_NOT_EXISTS}.
     * @throws ezcConsoleParameterException
     *         If another option/alias has taken the provided short or long name
     *         {@link ezcConsoleParameterException::PARAMETER_ALREADY_REGISTERED}.
     */
    public function registerAlias( $short, $long, $option )
    {
        $short = ezcConsoleOption::sanitizeParameterName($short);
        $long = ezcConsoleOption::sanitizeParameterName($long);
        if ( !isset( $this->optionShort[$option->short] ) || !isset( $this->optionLong[$option->long] ) )
        {
            throw new ezcConsoleParameterException( 
                "The referenced parameter <{$option->short}>/<{$option->long}> is not registered so <{$short}>/<{$long}> cannot be made an alias.",
                ezcConsoleParameterException::PARAMETER_NOT_EXISTS,
                $option
            );
        }
        if ( isset( $this->optionShort[$short] ) || isset( $optionLong[$long] ) )
        {
            throw new ezcConsoleParameterException( 
                "The parameter <{$short}>/<{$long}> does already exist.",
                ezcConsoleParameterException::PARAMETER_ALREADY_REGISTERED,
                isset( $this->optionShort[$short] ) ? $this->optionShort[$short] : $this->optionLong[$long]
            );
        }
        $this->shortParam[$short] = $option;
        $this->longParam[$long] = $option;
    }

    /**
     * Registeres options according to a string specification.
     * Accepts a string like used in eZ publis 3.x to define parameters and
     * registeres all parameters as options accordingly. String definitions look like
     * this:
     *
     * <code>
     * [s:|size:][u:|user:][a:|all:]
     * </code>
     *
     * This string will result in 3 parameters:
     * -s / --size
     * -u / --user
     * -a / --all
     *
     * @param string $paramDef Parameter definition string.
     * 
     * @throws ezcConsoleParameterException 
     *         If string is not wellformed
     *         {@link ezcConsoleParameterException::PARAMETER_STRING_NOT_WELLFORMED}.
     */
    public function fromString( $optionDef ) 
    {
        $regex = '/\[([a-z0-9-]+)([:?*+])?([^|]*)\|([a-z0-9-]+)([:?*+])?\]/';
        if ( preg_match_all( $regex, $optionDef, $matches ) )
        {
            foreach ( $matches[1] as $id => $short )
            {
                $option = null;
                if ( empty( $matches[4][$id] )  ) 
                {
                    throw new ezcConsoleParameterException( 
                        "Missing long parameter name for short parameter <-{$short}>",
                        ezcConsoleParameterException::PARAMETER_STRING_NOT_WELLFORMED 
                    );
                }
                $option = new ezcConsoleOption($short, $matches[4][$id]);
                if ( !empty( $matches[2][$id] ) || !empty( $matches[5][$id] ) )
                {
                    switch ( !empty( $matches[2][$id] ) ? $matches[2][$id] : $matches[5][$id] )
                    {
                        case '*':
                            // Allows 0 or more occurances
                            $option->multiple = true;
                            break;
                        case '+':
                            // Allows 1 or more occurances
                            $option->multiple = true;
                            $option->type = self::TYPE_STRING;
                            break;
                        case '?':
                            $option->type = self::TYPE_STRING;
                            $option->default = '';
                            break;
                        default:
                            break;
                    }
                }
                if ( !empty( $matches[3][$id] ) )
                {
                    $option->default = $matches[3][$id];
                }
                $this->registerOption( $option );
            }
        }

    }

    /**
     * Remove a option to be no more supported.
     * Using this function you will remove a option. All dependencies to that 
     * specific option are removed completly from every other registered 
     * option.
     *
     * @see ezcConsoleParameter::registerOption()
     *
     * @param ezcConsoleOption $option The option object to unregister.
     *
     * @throws ezcConsoleParameterException 
     *         If requesting a nonexistant option
     *         {@link ezcConsoleParameterException::PARAMETER_NOT_EXISTS}.
     */
    public function unregisterOption( $option )
    {
        $found = false;
        foreach ( $this->options as $id => $existParam )
        {
            if ( $existParam === $option )
            {
                $found = true;
                unset($this->options[$id]);
                continue;
            }
            $existParam->removeAllExclusions($option);
            $existParam->removeAllDependencies($option);
        }
        if ( $found === false )
        {
            throw new ezcConsoleParameterException( 
                "The referenced parameter <{$option->short}>/<{$option->long}> is not registered.",
                ezcConsoleParameterException::PARAMETER_NOT_EXISTS,
                $option
            );
        }
        foreach ( $this->optionLong as $name => $existParam )
        {
            if ( $existParam === $option )
            {
                unset($this->optionLong[$name]);
            }
        }
        foreach ( $this->optionShort as $name => $existParam )
        {
            if ( $existParam === $option )
            {
                unset($this->optionShort[$name]);
            }
        }
    }
    
    /**
     * Remove a alias to be no more supported.
     * Using this function you will remove an alias.
     *
     * @see ezcConsoleParameter::registerAlias()
     * 
     * @throws ezcConsoleParameterException
     *      If the requested short/long name belongs to a real parameter instead
     *      of an alias {@link ezcConsoleParameterException::PARAMETER_IS_NO_ALIAS}. 
     *
     * @param mixed $short 
     * @param mixed $long 
     */
    public function unregisterAlias( $short, $long )
    {
        $short = ezcConsoleOption::sanitizeParameterName($short);
        $long = ezcConsoleOption::sanitizeParameterName($long);
        foreach ( $this->options as $id => $option )
        {
            if ( $option->short === $short )
            {
                throw new ezcConsoleParameterException( 
                    "The short name <{$short}> refers to a real parameter, not to an alias.",
                    ezcConsoleParameterException::PARAMETER_IS_NO_ALIAS,
                    $option
                );
            }
            if ( $option->long === $long )
            {
                throw new ezcConsoleParameterException( 
                    "The long name <{$long}> refers to a real parameter, not to an alias.",
                    ezcConsoleParameterException::PARAMETER_IS_NO_ALIAS,
                    $option
                );
            }
        }
        if ( isset( $this->optionShort[$short] ) )
        {
            unset($this->optionShort[$short]);
        }
        if ( isset( $this->optionLong[$short] ) )
        {
            unset($this->optionLong[$long]);
        }
    }

    /**
     * Returns the definition object for a specific option.
     * This method receives the long or short name of a option and
     * returns the ezcConsoleOption object.
     * 
     * @param string $name Short or long name of the option - or --).
     * @return ezcConsoleOption The requested option.
     *
     * @throws ezcConsoleParameterException 
     *         If requesting a nonexistant parameter
     *         {@link ezcConsoleParameterException::PARAMETER_NOT_EXISTS}.
     */
    public function getOption( $name )
    {
        $name = ezcConsoleOption::sanitizeParameterName($name);
        if ( isset( $this->optionShort[$name] ) )
        {
            return $this->optionShort[$name];
        }
        if ( isset( $this->optionLong[$name] ) )
        {
            return $this->optionLong[$name];
        }
        throw new ezcConsoleParameterException( 
            "<{$name}> is not a valid parameter long or short name.", 
            ezcConsoleParameterException::PARAMETER_NOT_EXISTS,
            null
        );
    }

    /**
     * Process the input parameters.
     * Actually process the input options and arguments according to the actual 
     * settings.
     * 
     * Per default this method uses $argc and $argv for processing. You can 
     * override this setting with your own input, if necessary, using the
     * parameters of this method. (Attention, first argument is always the pro
     * gram name itself!)
     *
     * All exceptions thrown by this method contain an additional attribute "option"
     * which specifies the parameter on which the error occured.
     * 
     * @param array(int -> string) $args The arguments
     *
     * @throws ezcConsoleParameterException 
     *         If dependencies are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If exclusion rules are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If type rules are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_TYPE_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If a parameter used does not exist
     *         {@link ezcConsoleParameterException::PARAMETER_NOT_EXISTS}.
     * @throws ezcConsoleParameterException 
     *         If arguments are passed although a parameter dissallowed them
     *         {@link ezcConsoleParameterException::ARGUMENTS_NOT_ALLOWED}.
     * 
     * @see ezcConsoleParameterException
     */ 
    public function process( $args = null )
    {
        if ( !isset( $args ) )
        {
            $args = isset( $argv ) ? $argv : isset( $_SERVER['argv'] ) ? $_SERVER['argv'] : array();
        }
        $i = 1;
        while ( $i < count( $args ) )
        {
            // Equalize parameter handling (long params with =)
            if ( substr( $args[$i], 0, 2 ) == '--' )
            {
                $this->preprocessLongOption( $args, $i );
            }
            // Check for parameter
            if ( substr( $args[$i], 0, 1) === '-' && $this->hasOption( $args[$i] ) !== false )
            {
                $this->processOptions( $args, $i );
            }
            // Looks like parameter, but is not available??
            elseif ( substr( $args[$i], 0, 1) === '-' && trim( $args[$i] ) !== '--' )
            {
                throw new ezcConsoleParameterException(
                    "Unknown parameter <{$args[$i]}>.",
                    ezcConsoleParameterException::PARAMETER_NOT_EXISTS,
                    null
                );
            }
            // Must be the arguments
            else
            {
                $args[$i] == '--' ? ++$i : $i;
                $this->processArguments( $args, $i );
                break;
            }
        }
        $this->checkRules();
    }

    /**
     * Returns if an option with the given name exists.
     * Checks if an option with the given name is registered.
     * 
     * @param string $name Short or long name of the option.
     * @return bool True if option exists, otherwise false.
     */
    public function hasOption( $name )
    {
        try
        {
            $param = $this->getOption( $name );
        }
        catch ( ezcConsoleParameterException $e )
        {
            return false;
        }
        return true;
    }

    /**
     * Returns an array of all registered options.
     * Returns an array of all registered options in the following format:
     * <code>
     * array( 
     *      0 => object(ezcConsoleOption),
     *      1 => object(ezcConsoleOption),
     *      2 => object(ezcConsoleOption),
     *      ...
     * );
     * </code>
     *
     * @return array(string=>object(ezcConsoleOption)) Registered options.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns all values submitted.
     * Returns an array of all values submitted to the options. The array is 
     * indexed by the parameters short name (excluding the '-' prefix).
     * 
     * @return array(string => mixed)
     */
    public function getValues()
    {
        $res = array();
        foreach ( $this->options as $param )
        {
            $res[$param->short] = $param->value;
        }
        return $res;
    }

    /**
     * Returns arguments provided to the program.
     * This method returns all arguments provided to a program in an
     * integer indexed array. Arguments are sorted in the way
     * they are submitted to the program. You can disable arguments
     * through the 'arguments' flag of a parameter, if you want
     * to disallow arguments.
     *
     * Arguments are either the last part of the program call (if the
     * last parameter is not a 'multiple' one) or divided via the '--'
     * method which is commonly used on Unix (if the last parameter
     * accepts multiple values this is required).
     *
     * @return array(int => string) Arguments.
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Get help information for your options.
     * This method returns an array of help information for your options,
     * indexed by integer. Each helo info has 2 fields:
     *
     * 0 => The options names ("<short> / <long>")
     * 1 => The help text (depending on the $long parameter)
     *
     * The $long options determines if you want to get the short- or longhelp
     * texts. The array returned can be used by {@link ezcConsoleTable}.
     *
     * If using the second options, you can filter the options shown in the
     * help output (e.g. to show short help for related options). Provide
     * as simple number indexed array of short and/or long values to set a filter.
     * 
     * @param bool $long Set this to true for getting the long help version.
     * @param array $params Set of options to generate help for, default is all.
     */
    public function getHelp( $long = false, $params = array() )
    {
        $help = array();
        foreach ( $this->options as $id => $param )
        {
            if ( count($params) === 0 || in_array( $param->short, $params ) || in_array( $param->long, $params ) )
            {
                $help[] = array( 
                    '-' . $param->short . ' / ' . '--' . $param->long,
                    $long == false ? $param->shorthelp : $param->longhelp,
                );
            }
        }
        return $help;
    }

    /**
     * Process an option.
     * This method does the processing of a single option. 
     * 
     * @param array $args The arguments array.
     * @param int $i      The current position in the arguments array.
     * @param int The current index in the $args array.
     */
    private function processOptions( $args, &$i )
    {
        $option = $this->getOption( $args[$i++] );
        // No value expected
        if ( $option->type === ezcConsoleParameter::TYPE_NONE )
        {
            // No value expected
            if ( isset( $args[$i] ) && substr( $args[$i], 0, 1 ) !== '-' )
            {
                // But one found
                throw new Exception( 
                    "Parameter with long name <{$option->long}> does not expect a value but <{$args[$i]}> was submitted.",
                    ezcConsoleParameterException::PARAMETER_TYPE_RULE_NOT_MET,
                    $option
                );
            }
            // Multiple occurance possible
            if ( $option->multiple === true )
            {
                $option->value[] = true;
            }
            else
            {
                $option->value = true;
            }
            // Everything fine, nothing to do
            return $i;
        }
        // Value expected, check for it
        if ( isset( $args[$i] ) && substr( $args[$i], 0, 1 ) !== '-' )
        {
            // Type check
            if ( $this->correctType( $option, $args[$i] ) === false )
            {
                throw new ezcConsoleParameterException( 
                    "Parameter with long name <{$option->long}> of incorrect type.",
                    ezcConsoleParameterException::PARAMETER_TYPE_RULE_NOT_MET,
                    $option
                );
            }
            // Multiple values possible
            if ( $option->multiple === true )
            {
                $option->value[] = $args[$i];
            }
            // Only single value expected, check for multiple
            elseif ( isset( $option->value ) && $option->value !== false )
            {
                throw new ezcConsoleParameterException( 
                    "Parameter with long name <{$option->long}> expects only 1 value but multiple have been submitted.",
                    ezcConsoleParameterException::TOO_MANY_PARAMETER_VALUES,
                    $option
                );
            }
            else
            {
                $option->value = $args[$i];
            }
            $i++;
        }
        // Value found? If not, use default, if available
        if ( !isset( $option->value ) || $option->value === false || ( is_array( $option->value ) && count( $option->value ) === 0) ) 
        {
            if ( isset( $option->default ) ) 
            {
                $option->value = $option->multiple === true ? array( $option->default ) : $option->default;
            }
            else
            {
                throw new ezcConsoleParameterException( 
                    "Parameter value missing for parameter with long name <{$option->long}>.",
                    ezcConsoleParameterException::MISSING_PARAMETER_VALUE,
                    $option
                );
            }
        }
        return $i;
    }

    /**
     * Process arguments given to the program. 
     * 
     * @todo FIXME: Add test for this!
     * @param array $args The arguments array.
     * @param int $i Current index in arguments array.
     */
    private function processArguments( $args, &$i )
    {
        while ( $i < count( $args ) )
        {
            $this->arguments[] = $args[$i++];
        }
    }

    /**
     * Check the rules that may be associated with an option.
     * Optionss are allowed to have rules associated for
     * dependencies to other options and exclusion of other options or
     * arguments. This method processes the checks.
     * 
     *
     * @throws ezcConsoleParameterException 
     *         If dependencies are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If exclusion rules are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If arguments are passed although a parameter dissallowed them
     *         {@link ezcConsoleParameterException::ARGUMENTS_NOT_ALLOWED}.
     */
    private function checkRules()
    {
        $values = $this->getValues();
        foreach ( $this->options as $id => $option )
        {
            if ( $option->value === false || is_array( $option->value ) && count( $option->value ) === 0 )
            {
                // Parameter was not set so ignore it's rules.
                continue;
            }
            // Dependencies
            foreach ( $option->getDependencies() as $dep )
            {
                if ( !isset( $values[$dep->option->short] ) || $values[$dep->option->short] === false )
                {
                    throw new ezcConsoleParameterException( 
                        "Parameter with long name <{$option->long}> depends on parameter with long name <{$dep->option->long}> which was not submitted.",
                        ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET,
                        $option
                    );
                }
                $depVals = $dep->values;
                if ( count( $depVals ) > 0 )
                {
                    if ( !in_array( $values[$dep->option->short], $depVals ) )
                    {
                        throw new ezcConsoleParameterException( 
                            "Parameter with long name <{$option->long}> depends on parameter with long name <{$dep->option->long}> to be in a specific value range, but isn't.",
                            ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET,
                            $option
                        );
                    }
                }
            }
            // Exclusions
            foreach ( $option->getExclusions() as $exc )
            {
                if ( isset( $values[$exc->option->short] ) && $values[$exc->option->short] !== false )
                {
                    throw new ezcConsoleParameterException( 
                        "Parameter with long name <{$option->long}> excludes the parameter with long name <{$exc->option->long}> which was submitted.",
                        ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET,
                        $option
                    );
                }
                $excVals = $exc->values;
                if ( count( $excVals ) > 0 )
                {
                    if ( in_array( $values[$exc->option->short], $excVals ) )
                    {
                        throw new ezcConsoleParameterException( 
                            "Parameter with long name <{$option->long}> excludes parameter with long name <{$exc->option->long}> to be in a specific value range, but it is.",
                            ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET,
                            $option
                        );
                    }
                }
            }
            // Arguments
            if ( $option->arguments === false && is_array( $this->arguments ) && count( $this->arguments ) > 0 )
            {
                throw new ezcConsoleParameterException( 
                    "Parameter with long name <{$option->long}> excludes the usage of arguments, but arguments have been passed.",
                    ezcConsoleParameterException::ARGUMENTS_NOT_ALLOWED,
                    $option
                );
            }
        }
    }

    /**
     * Checks if a value is of a given type. Converts the value to the
     * correct PHP type on success.
     *  
     * @param int $option The option.
     * @param string $val The value to check.
     * @return bool True on succesful check, otherwise false.
     */
    private function correctType( $option, &$val )
    {
        $res = false;
        switch ( $option->type )
        {
            case ezcConsoleParameter::TYPE_STRING:
                $res = true;
                $val = preg_replace( '/^(["\'])(.*)\1$/', '\2', $val );
                break;
            case ezcConsoleParameter::TYPE_INT:
                $res = preg_match( '/^[0-9]+$/', $val ) ? true : false;
                if ( $res )
                {
                    $val = (int)$val;
                }
                break;
        }
        return $res;
    }

    /**
     * Split parameter and value for long option names. This method checks 
     * for long options, if the value is passed using =. If this is the case
     * parameter and value get split and replaced in the arguments array.
     * 
     * @param array $args The arguments array
     * @param int $i Current arguments array position
     */
    private function preprocessLongOption( &$args, $i )
    {
        // Value given?
        if ( preg_match( '/^--\w+\=[^ ]/i', $args[$i] ) )
        {
            // Split param and value and replace current param
            $parts = explode( '=', $args[$i], 2 );
            array_splice( $args, $i, 1, $parts );
        }
    }
}
?>
