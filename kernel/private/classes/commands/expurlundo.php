<?php
/**
 * @description Undo a URL map import: every alias, wildcard and redirect it created or changed back as it was
 * @alias exp:url:undo
 *
 * File containing the exp:url:undo command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expurlundo extends ExpurlBase
{
    protected function name()
    {
        return 'exp:url:undo';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Undo a URL map import\n" .
                             "Puts every URL alias, wildcard and redirect the import created or changed back as it was before, the\n" .
                             "last first, in one transaction. An entry changed after the import is kept (the batch is then\n" .
                             "partly-undone), unless --force is given. exp:url:batches lists the imports.\n" .
                             "\n" .
                             "./console exp:url:undo 12\n" .
                             "./console exp:url:undo 12 --force --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[force][format:][login:]', '[batch]', array(
            'force' => 'Undo also the entries that changed after the import (default: they are kept)',
            'format' => 'import: the format of the file (csv, json, xlsx, ods, apache, nginx; default from its name or content); export: the format to write (default from --output, else csv); batches, undo, from-sitemap, from-404, suggest, test: text (default) or json (the machine output exp-url/1)',
            'login' => 'Run as this user (recorded with the import and in the audit)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->outputFormat( $options );
        $batch = isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : '';
        if ( !ctype_digit( $batch ) || (int)$batch < 1 )
            $this->usage( 'name the batch: exp:url:undo <batch> (exp:url:batches lists them)' );
        $userId = $this->login( $options );
        $this->database( $format );
        try
        {
            $result = ( new \expUrlMapImporter( $userId ? array( 'user_id' => $userId ) : array() ) )->undo( (int)$batch, array( 'force' => !empty( $options['force'] ) ) );
        }
        catch ( \RuntimeException $e )
        {
            $this->finish( array( 'mode' => 'undo', 'batch_id' => (int)$batch, 'errors' => array( $e->getMessage() ) ), $format );
            return;
        }
        $report = array( 'mode' => 'undo' ) + $result + array( 'warnings' => array() );
        if ( $result['counts']['kept'] )
            $report['warnings'][] = $result['counts']['kept'] . ' entr' . ( $result['counts']['kept'] === 1 ? 'y' : 'ies' ) . ' changed after the import and were kept (--force undoes them too)';
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        if ( !isset( $report['counts'] ) )
            return array();
        $lines = array( sprintf( 'URL map import %d: %s, %d entries back as before, %d kept', $report['batch_id'], $report['state'], $report['counts']['undone'], $report['counts']['kept'] ) );
        foreach ( array_slice( $report['items'], 0, 50 ) as $item )
            $lines[] = sprintf( '  %-7s %s %s %s', $item['result'], $item['target_table'], $item['old_url'], $item['message'] );
        return $lines;
    }
}

}
