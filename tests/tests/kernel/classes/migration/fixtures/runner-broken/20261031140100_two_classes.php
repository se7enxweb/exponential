<?php
// description: A PHP migration file with two migration classes
namespace ExpTests\Migrations\Broken;

final class FirstOfTwo extends \expMigrationPhp
{
    public function up( $context )
    {
        return array();
    }
}

final class SecondOfTwo extends \expMigrationPhp
{
    public function up( $context )
    {
        return array();
    }
}
