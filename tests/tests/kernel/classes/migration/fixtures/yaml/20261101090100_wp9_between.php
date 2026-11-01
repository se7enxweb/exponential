<?php
// description: A PHP migration between two YAML migrations
// requires: 20261101090000_wp9_first
namespace ExpTests\Migrations\Yaml;

/**
 * Reads the reference the YAML migration before it set and sets one for the YAML migration after it.
 */
final class Between extends \expMigrationPhp
{
    public function up( $context )
    {
        $context->setReference( 'from_php', 'php saw ' . $context->reference( 'first_name' ) );
        return array( 'passed the reference on' );
    }
}
