<?php
/**
 * File containing the expMigrationCheckpoint class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Where a long migration got to: a few values (an offset, the last id handled) a PHP migration keeps between the
 * batches of its work, so a run that is stopped, fails or times out goes on from there the next time instead of
 * starting again. One file per migration, var/<site>/migration/checkpoints/<migration id>.json, written whole each
 * time (a temporary file renamed over it, so a reader never sees half a file). The runner removes it once the
 * migration is applied; a migration that failed keeps it for the next run (exp:migration:resume, or migrate again).
 *
 * A checkpoint is a file, not a row: it has to survive the rollback of a failed migration's transaction. That is
 * also why expMigrationPhp::batches(), which writes it, needs a migration that is not transactional(): each batch
 * is committed on its own, and the checkpoint is written only after its batch is committed.
 *
 * A dry run never writes one; get() still reads what a real run left, so the plan can say where it would go on.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationCheckpoint
{
    /** @var string */
    protected $file;

    /** @var bool */
    protected $write;

    /** @var array|null */
    protected $values = null;

    /**
     * @param string $migrationId
     * @param string $directory the folder of the checkpoints
     * @param bool $write false: set() keeps the values in memory only (a dry run)
     * @throws InvalidArgumentException for an id that cannot be a file name
     */
    public function __construct( $migrationId, $directory, $write = true )
    {
        if ( !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_.-]{0,199}$/', (string)$migrationId ) || strpos( (string)$migrationId, '..' ) !== false )
            throw new InvalidArgumentException( 'not a migration id: ' . $migrationId );
        $this->file = rtrim( (string)$directory, '/' ) . '/' . $migrationId . '.json';
        $this->write = (bool)$write;
    }

    /**
     * The checkpoint of a migration in a run: in the context's option checkpoint-directory (tests) or the default
     * folder; written only when the run applies.
     *
     * @param string $migrationId
     * @param expMigrationContext $context
     * @return expMigrationCheckpoint
     */
    public static function of( $migrationId, $context )
    {
        $directory = $context->option( 'checkpoint-directory' );
        return new self( $migrationId, $directory !== null && $directory !== '' ? (string)$directory : self::directory(), $context->isApply() );
    }

    /**
     * The folder of the checkpoints, absolute.
     *
     * @return string
     */
    public static function directory()
    {
        $directory = rtrim( (string)eZSys::varDirectory(), '/' ) . '/migration/checkpoints';
        if ( $directory[0] !== '/' )
            $directory = rtrim( (string)eZSys::rootDir(), '/' ) . '/' . $directory;
        return $directory;
    }

    /** @return string */
    public function file()
    {
        return $this->file;
    }

    /**
     * A value, or $default when the checkpoint has none.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get( $name, $default = null )
    {
        $values = $this->values();
        return array_key_exists( (string)$name, $values ) ? $values[(string)$name] : $default;
    }

    /**
     * Every value.
     *
     * @return array
     */
    public function values()
    {
        if ( $this->values === null )
        {
            $data = is_file( $this->file ) ? json_decode( (string)@file_get_contents( $this->file ), true ) : null;
            $this->values = is_array( $data ) && isset( $data['values'] ) && is_array( $data['values'] ) ? $data['values'] : array();
        }
        return $this->values;
    }

    /**
     * Whether a checkpoint is on record.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->values() !== array();
    }

    /**
     * Sets values and writes the checkpoint (JSON-encodable values only).
     *
     * @param array $values name => value, merged into the values there are
     * @return bool false when the file could not be written
     */
    public function set( array $values )
    {
        $this->values = array_merge( $this->values(), $values );
        if ( !$this->write )
            return true;
        $directory = dirname( $this->file );
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        $json = json_encode( array( 'values' => $this->values, 'updated' => time() ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        $temporary = $this->file . '.' . bin2hex( random_bytes( 4 ) ) . '.tmp';
        if ( $json === false || @file_put_contents( $temporary, $json ) === false )
            return false;
        if ( !@rename( $temporary, $this->file ) )
        {
            @unlink( $temporary );
            return false;
        }
        return true;
    }

    /**
     * Removes the checkpoint (the migration is done).
     */
    public function clear()
    {
        $this->values = array();
        if ( $this->write && is_file( $this->file ) )
            @unlink( $this->file );
    }
}
