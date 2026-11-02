<?php
/**
 * File containing the expMigrationPhp class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The base of a PHP migration: a file YYYYMMDDHHMMSS_name.php with its header (expMigrationDefinition) and one
 * namespaced class that extends this one. load() reads the file and makes the object; the class name is free, the
 * namespace is the author's (the site's or the extension's), so two migrations never clash on a name.
 *
 *   <?php
 *   // description: A table for news notes and the News folder
 *   // requires: 20261030090000_news_class
 *   namespace MySite\Migrations;
 *
 *   final class AddNewsNotes extends \expMigrationPhp
 *   {
 *       public function plan( $context )
 *       {
 *           return array( 'create the table news_note', 'create the folder News below the root' );
 *       }
 *
 *       public function up( $context )
 *       {
 *           ...
 *           return array( 'created news_note', 'created the folder News' );
 *       }
 *   }
 *
 * The shape is the one of the kernel's own migrations (expWebsiteSidebarMigration): plan() lists what up() would do
 * without changing anything (the dry run; build it from pure functions where possible, so it can be tested without a
 * database), up() does it and lists what it did, down() undoes it when the class has one (reversible() is true then),
 * detect() may ask the installation whether the change is there already. Everything goes through the context
 * ($context->db(), $context->reference() ...), never through a connection of its own, so the runner's transaction and
 * a test's database are the ones used.
 *
 * The state is record-based: done once a run recorded it, failed when the last run failed, pending otherwise. A class
 * whose detect() answers (not null) is detect-based instead.
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expMigrationPhp implements expMigration
{
    /** @var expMigrationDefinition|null */
    protected $definition;

    /**
     * Reads a PHP migration file and makes its migration.
     *
     * @param expMigrationDefinition $definition a definition of type php
     * @return expMigrationPhp
     * @throws RuntimeException when the file has no class that extends expMigrationPhp, or more than one
     */
    final public static function load( expMigrationDefinition $definition )
    {
        if ( $definition->type() !== 'php' )
            throw new RuntimeException( $definition->id() . ' is not a PHP migration (.' . $definition->type() . ')' );
        $file = $definition->file();
        if ( $file === null || !is_file( $file ) )
            throw new RuntimeException( $definition->id() . ': the file is missing' );
        $code = (string)file_get_contents( $file );
        $classes = self::classesIn( $code );
        // A method PHP would refuse (a helper named definition() or file(), a hook with another signature) is a fatal
        // error while the file loads, which nothing can catch: it is found in the code before the file is loaded.
        $clashes = self::methodClashes( $code );
        if ( $clashes )
            throw new RuntimeException( "$file: " . implode( '; ', $clashes ) );
        foreach ( $classes as $class )
            if ( class_exists( $class, false ) && ( new ReflectionClass( $class ) )->getFileName() !== realpath( $file ) )
                throw new RuntimeException( "$file: the class $class is defined in another file already ("
                                            . ( new ReflectionClass( $class ) )->getFileName() . ')' );
        self::requireFile( $file );
        $found = array();
        foreach ( $classes as $class )
            if ( class_exists( $class, false ) && is_subclass_of( $class, __CLASS__ ) && !( new ReflectionClass( $class ) )->isAbstract() )
                $found[] = $class;
        if ( count( $found ) !== 1 )
            throw new RuntimeException( "$file: " . ( $found ? 'more than one class extends expMigrationPhp (' . implode( ', ', $found ) . ')'
                                                             : 'no class extends expMigrationPhp' ) );
        $class = $found[0];
        $migration = new $class();
        $migration->setDefinition( $definition );
        return $migration;
    }

    /**
     * The classes a PHP file declares, with their namespace. Pure.
     *
     * @param string $code
     * @return string[] fully qualified, without the leading backslash
     */
    final public static function classesIn( $code )
    {
        $tokens = token_get_all( (string)$code );
        $nameTokens = array( T_STRING, T_NS_SEPARATOR );
        foreach ( array( 'T_NAME_QUALIFIED', 'T_NAME_FULLY_QUALIFIED' ) as $name )
            if ( defined( $name ) )
                $nameTokens[] = constant( $name );
        $namespace = '';
        $classes = array();
        $count = count( $tokens );
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( !is_array( $tokens[$i] ) )
                continue;
            if ( $tokens[$i][0] === T_NAMESPACE )
            {
                $name = '';
                for ( $j = $i + 1; $j < $count; $j++ )
                {
                    if ( is_array( $tokens[$j] ) && in_array( $tokens[$j][0], $nameTokens, true ) )
                        $name .= $tokens[$j][1];
                    else if ( is_array( $tokens[$j] ) && $tokens[$j][0] === T_WHITESPACE )
                        continue;
                    else
                        break;
                }
                $namespace = trim( $name, '\\' );
                $i = $j;
            }
            else if ( $tokens[$i][0] === T_CLASS )
            {
                // not ::class, not new class (an anonymous class)
                $prev = $i - 1;
                while ( $prev >= 0 && is_array( $tokens[$prev] ) && $tokens[$prev][0] === T_WHITESPACE )
                    $prev--;
                if ( $prev >= 0 && ( ( is_array( $tokens[$prev] ) && in_array( $tokens[$prev][0], array( T_DOUBLE_COLON, T_NEW ), true ) ) ) )
                    continue;
                for ( $j = $i + 1; $j < $count; $j++ )
                {
                    if ( is_array( $tokens[$j] ) && $tokens[$j][0] === T_WHITESPACE )
                        continue;
                    if ( is_array( $tokens[$j] ) && $tokens[$j][0] === T_STRING )
                        $classes[] = ( $namespace !== '' ? $namespace . '\\' : '' ) . $tokens[$j][1];
                    break;
                }
            }
        }
        return $classes;
    }

    /**
     * The methods of the classes in $code that extend expMigrationPhp and that PHP would refuse when the file loads (a
     * fatal error nothing can catch), each as a message. Pure: the code is tokenized, never run.
     *
     * - a method of the base's own (final: definition(), file(), id(), source(), checksum(), state() ...) declared
     *   again, typically a helper that happens to have the name;
     * - a hook (plan(), up(), down(), detect(), description(), transactional(), reversible()) declared private,
     *   protected or static, with fewer parameters than the base, with more required ones, or with a type on a
     *   parameter the base leaves untyped;
     * - a class that is not abstract and has no up().
     *
     * @param string $code
     * @return string[] empty when nothing clashes
     */
    final public static function methodClashes( $code )
    {
        $base = array();
        foreach ( ( new ReflectionClass( __CLASS__ ) )->getMethods() as $method )
            if ( !$method->isPrivate() )
                $base[strtolower( $method->getName() )] = $method;
        $tokens = token_get_all( (string)$code );
        $count = count( $tokens );
        $skip = array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT );
        $classModifiers = array( T_ABSTRACT, T_FINAL );
        if ( defined( 'T_READONLY' ) )
            $classModifiers[] = constant( 'T_READONLY' );
        $problems = array();
        $depth = 0;
        $classDepth = null;
        $pending = null;
        $class = null;
        for ( $i = 0; $i < $count; $i++ )
        {
            $token = $tokens[$i];
            if ( !is_array( $token ) )
            {
                if ( $token === '{' )
                {
                    $depth++;
                    if ( $pending !== null )
                    {
                        $class = $pending;
                        $classDepth = $depth;
                        $pending = null;
                    }
                }
                else if ( $token === '}' )
                {
                    if ( $classDepth !== null && $depth === $classDepth )
                    {
                        if ( !$class['abstract'] && !$class['up'] )
                            $problems[] = "the class {$class['name']} has no method up()";
                        $classDepth = null;
                        $class = null;
                    }
                    $depth--;
                }
                continue;
            }
            if ( $token[0] === T_CURLY_OPEN || $token[0] === T_DOLLAR_OPEN_CURLY_BRACES )
            {
                $depth++;
                continue;
            }
            if ( $token[0] === T_CLASS && $classDepth === null )
            {
                $prev = self::previousToken( $tokens, $i, $skip );
                if ( $prev !== null && is_array( $tokens[$prev] ) && in_array( $tokens[$prev][0], array( T_DOUBLE_COLON, T_NEW ), true ) )
                    continue;
                $abstract = false;
                for ( $p = $prev; $p !== null && is_array( $tokens[$p] ) && in_array( $tokens[$p][0], $classModifiers, true ); $p = self::previousToken( $tokens, $p, $skip ) )
                    if ( $tokens[$p][0] === T_ABSTRACT )
                        $abstract = true;
                $name = '';
                $extends = false;
                for ( $j = $i + 1; $j < $count && $tokens[$j] !== '{'; $j++ )
                {
                    if ( !is_array( $tokens[$j] ) || in_array( $tokens[$j][0], $skip, true ) )
                        continue;
                    if ( $name === '' && $tokens[$j][0] === T_STRING )
                        $name = $tokens[$j][1];
                    else if ( $tokens[$j][0] === T_EXTENDS )
                    {
                        $n = self::nextToken( $tokens, $j, $skip );
                        $extends = $n !== null && is_array( $tokens[$n] ) && strtolower( ltrim( $tokens[$n][1], '\\' ) ) === 'expmigrationphp';
                    }
                }
                if ( $extends )
                    $pending = array( 'name' => $name, 'abstract' => $abstract, 'up' => false );
                continue;
            }
            if ( $token[0] !== T_FUNCTION || $classDepth === null || $depth !== $classDepth )
                continue;

            $n = self::nextToken( $tokens, $i, $skip );
            if ( $n !== null && $tokens[$n] === '&' )
                $n = self::nextToken( $tokens, $n, $skip );
            if ( $n === null || !is_array( $tokens[$n] ) )
                continue;
            $method = $tokens[$n][1];
            $lower = strtolower( $method );
            if ( $lower === 'up' )
                $class['up'] = true;
            $modifiers = array();
            for ( $p = self::previousToken( $tokens, $i, $skip ); $p !== null && is_array( $tokens[$p] ) && in_array( $tokens[$p][0], array( T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_ABSTRACT, T_FINAL ), true ); $p = self::previousToken( $tokens, $p, $skip ) )
                $modifiers[] = strtolower( $tokens[$p][1] );
            if ( !isset( $base[$lower] ) )
                continue;
            $own = $base[$lower];
            $where = "{$class['name']}::$method()";
            if ( $own->isFinal() )
            {
                $problems[] = "$where has the name of a method of expMigrationPhp that a migration cannot declare: give it another name";
                continue;
            }
            if ( in_array( 'private', $modifiers, true ) || in_array( 'protected', $modifiers, true ) )
                $problems[] = "$where must be public, as in expMigrationPhp";
            if ( in_array( 'static', $modifiers, true ) !== $own->isStatic() )
                $problems[] = "$where must " . ( $own->isStatic() ? '' : 'not ' ) . 'be static, as in expMigrationPhp';
            $params = self::parametersAfter( $tokens, $n, $skip );
            $wanted = $own->getNumberOfParameters();
            if ( count( $params ) < $wanted )
                $problems[] = "$where must take $wanted parameter(s), as in expMigrationPhp";
            $required = 0;
            foreach ( $params as $k => $param )
            {
                if ( !$param['optional'] )
                    $required = $k + 1;
                if ( $k < $wanted && $param['type'] !== '' && strtolower( $param['type'] ) !== 'mixed' )
                    $problems[] = "$where: the parameter {$param['name']} has the type {$param['type']}, which expMigrationPhp does not have: leave it untyped";
            }
            if ( $required > $wanted )
                $problems[] = "$where requires $required parameter(s), expMigrationPhp passes $wanted";
        }
        return $problems;
    }

    /**
     * The parameters of the function whose name is the token $nameIndex: name, type (as written, '' for none) and
     * whether it is optional (a default or variadic).
     *
     * @param array $tokens
     * @param int $nameIndex
     * @param int[] $skip
     * @return array[]
     */
    private static function parametersAfter( array $tokens, $nameIndex, array $skip )
    {
        $count = count( $tokens );
        $i = $nameIndex + 1;
        while ( $i < $count && $tokens[$i] !== '(' )
            $i++;
        $params = array();
        $current = array( 'name' => '', 'type' => '', 'optional' => false );
        $level = 0;
        $afterName = false;
        $attribute = defined( 'T_ATTRIBUTE' ) ? constant( 'T_ATTRIBUTE' ) : null;
        for ( $i++; $i < $count; $i++ )
        {
            $t = $tokens[$i];
            if ( $t === '(' || $t === '[' || ( is_array( $t ) && $t[0] === $attribute ) )
                $level++;
            else if ( $t === ']' )
                $level--;
            else if ( $t === ')' )
            {
                if ( $level === 0 )
                    break;
                $level--;
            }
            if ( $level > 0 )
                continue;
            if ( $t === ',' )
            {
                if ( $current['name'] !== '' )
                    $params[] = $current;
                $current = array( 'name' => '', 'type' => '', 'optional' => false );
                $afterName = false;
                continue;
            }
            if ( $afterName )
            {
                if ( $t === '=' )
                    $current['optional'] = true;
                continue;
            }
            if ( is_array( $t ) && $t[0] === T_VARIABLE )
            {
                $current['name'] = $t[1];
                $afterName = true;
            }
            else if ( is_array( $t ) && $t[0] === T_ELLIPSIS )
                $current['optional'] = true;
            else if ( is_array( $t ) && in_array( $t[0], $skip, true ) )
                continue;
            else if ( $t !== '&' )
                $current['type'] .= is_array( $t ) ? $t[1] : $t;
        }
        if ( $current['name'] !== '' )
            $params[] = $current;
        return $params;
    }

    /** @return int|null the index of the token before $i that is not in $skip */
    private static function previousToken( array $tokens, $i, array $skip )
    {
        for ( $p = $i - 1; $p >= 0; $p-- )
            if ( !is_array( $tokens[$p] ) || !in_array( $tokens[$p][0], $skip, true ) )
                return $p;
        return null;
    }

    /** @return int|null the index of the token after $i that is not in $skip */
    private static function nextToken( array $tokens, $i, array $skip )
    {
        $count = count( $tokens );
        for ( $n = $i + 1; $n < $count; $n++ )
            if ( !is_array( $tokens[$n] ) || !in_array( $tokens[$n][0], $skip, true ) )
                return $n;
        return null;
    }

    /**
     * Loads a migration file with nothing of the caller in its scope.
     *
     * @param string $file
     */
    final protected static function requireFile( $file )
    {
        require_once $file;
    }

    /**
     * @param expMigrationDefinition $definition
     */
    final public function setDefinition( expMigrationDefinition $definition )
    {
        $this->definition = $definition;
    }

    /** @return expMigrationDefinition|null */
    final public function definition()
    {
        return $this->definition;
    }

    /** @return string */
    final public function id()
    {
        return $this->definition ? $this->definition->id() : '';
    }

    /** @return string */
    final public function source()
    {
        return $this->definition ? $this->definition->source() : 'site';
    }

    /** @return string */
    public function description()
    {
        return $this->definition ? $this->definition->description() : '';
    }

    /** @return string[] */
    final public function environments()
    {
        return $this->definition ? $this->definition->environments() : array();
    }

    /** @return string[] */
    final public function requires()
    {
        return $this->definition ? $this->definition->requires() : array();
    }

    /** @return bool */
    final public function maintenance()
    {
        return $this->definition ? $this->definition->maintenance() : false;
    }

    /** @return string[] */
    final public function engines()
    {
        return $this->definition ? $this->definition->engines() : array( '*' );
    }

    /** @return string|null the checksum of the file (expMigrationDefinition::checksumFor()) */
    final public function checksum()
    {
        return $this->definition ? $this->definition->checksumFor() : null;
    }

    /** @return string|null the file */
    final public function file()
    {
        return $this->definition ? $this->definition->file() : null;
    }

    /**
     * Whether up() runs in a transaction where the engine has one. A migration that commits itself (a long batch)
     * returns false.
     *
     * @return bool
     */
    public function transactional()
    {
        return true;
    }

    /**
     * Whether the change is in place without asking the record: expMigration::DONE, PENDING or NOT_NEEDED, or null
     * (the default) to go by the record.
     *
     * @param expMigrationContext $context
     * @return string|null
     */
    public function detect( $context )
    {
        return null;
    }

    /**
     * @param expMigrationContext $context
     * @return string
     */
    final public function state( $context )
    {
        $detected = $this->detect( $context );
        if ( $detected !== null )
            return (string)$detected;
        $record = $context->record( $this->source(), $this->id() );
        if ( !$record )
            return expMigration::PENDING;
        if ( $record['state'] === expMigration::DONE )
            return expMigration::DONE;
        if ( $record['state'] === expMigration::FAILED )
            return expMigration::FAILED;
        return expMigration::PENDING;
    }

    /**
     * What up() would do. The default names the migration; a class lists its changes.
     *
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( $context )
    {
        return array( $this->description() !== '' ? $this->description() : 'run ' . $this->id() );
    }

    /**
     * @param expMigrationContext $context
     * @return string[] what was done
     */
    abstract public function up( $context );

    /**
     * Whether the class has a down() of its own.
     *
     * @return bool
     */
    public function reversible()
    {
        return ( new ReflectionMethod( $this, 'down' ) )->getDeclaringClass()->getName() !== __CLASS__;
    }

    /**
     * @param expMigrationContext $context
     * @return string[]
     * @throws RuntimeException always: the migration has no down()
     */
    public function down( $context )
    {
        throw new RuntimeException( $this->id() . ' cannot be undone by itself (no down()); restore the caption taken before it' );
    }

    /**
     * Where this migration got to in an earlier run that did not finish (expMigrationCheckpoint): values it keeps
     * between batches of its own work. The runner removes it once the migration is applied.
     *
     * @param expMigrationContext $context
     * @return expMigrationCheckpoint
     */
    final protected function checkpoint( $context )
    {
        return expMigrationCheckpoint::of( $this->id(), $context );
    }

    /**
     * Works through many items a batch at a time, each batch committed on its own, and goes on where the last run
     * stopped: the number of items handled is kept in the checkpoint after every batch, so a run that fails or is
     * stopped half-way does not do the first batches again. Between batches it pauses [RunSettings] Throttle seconds
     * (or the run's --throttle), so a long run does not hold the server.
     *
     *   public function transactional() { return false; }
     *
     *   public function up( $context )
     *   {
     *       $db = $context->db();
     *       $count = $this->batches( $context,
     *           function ( $offset, $limit ) use ( $db ) {
     *               return $db->arrayQuery( 'SELECT id FROM ezcontentobject ORDER BY id', array( 'offset' => $offset, 'limit' => $limit ) );
     *           },
     *           function ( $row ) use ( $db ) { ... one item ... } );
     *       return array( "updated $count objects" );
     *   }
     *
     * $fetch gets the offset (the items handled in every run so far) and the batch size, and returns the next items in
     * a stable order; fewer than the batch size (or none) ends the work. A handler that removes the items it handles
     * would make an offset skip items: its $fetch ignores the offset and asks for what is still left instead.
     *
     * The migration must not be transactional(): a batch is a transaction of its own, and its checkpoint is written
     * only after it is committed. A dry run handles nothing and returns 0.
     *
     * @param expMigrationContext $context
     * @param callable $fetch function( int $offset, int $limit ): array
     * @param callable $handle function( mixed $item ): void, for each item of a batch
     * @param int $size items per batch (at least 1)
     * @param string $key the name of the offset in the checkpoint (another name for a second piece of work)
     * @return int the items handled in this run
     * @throws LogicException when the migration is transactional()
     */
    final protected function batches( $context, $fetch, $handle, $size = 100, $key = 'offset' )
    {
        if ( $this->transactional() )
            throw new LogicException( $this->id() . ': batches() commits every batch on its own; the migration\'s transactional() must return false' );
        if ( !$context->isApply() )
            return 0;
        $size = max( 1, (int)$size );
        $checkpoint = $this->checkpoint( $context );
        $offset = max( 0, (int)$checkpoint->get( $key, 0 ) );
        if ( $offset > 0 )
            $context->log( 'info', $this->id() . ": goes on after $offset items handled in an earlier run" );
        $pause = expMigrationRunner::throttleSeconds( $context->option( 'throttle' ) );
        $sleep = $context->option( 'sleep' );
        $handled = 0;
        while ( true )
        {
            $items = array_values( (array)call_user_func( $fetch, $offset, $size ) );
            if ( !$items )
                break;
            $context->begin();
            try
            {
                foreach ( $items as $item )
                    call_user_func( $handle, $item );
                $context->commit();
            }
            catch ( Throwable $e )
            {
                $context->rollback();
                throw $e;
            }
            $offset += count( $items );
            $handled += count( $items );
            if ( !$checkpoint->set( array( $key => $offset ) ) )
                $context->log( 'warning', $this->id() . ': the checkpoint could not be written: ' . $checkpoint->file() );
            if ( count( $items ) < $size )
                break;
            if ( $pause > 0 )
                is_callable( $sleep ) ? call_user_func( $sleep, $pause ) : usleep( (int)round( $pause * 1000000 ) );
        }
        return $handled;
    }
}
