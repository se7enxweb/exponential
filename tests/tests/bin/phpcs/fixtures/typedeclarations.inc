<?php
declare( strict_types=1 );

class TypeDeclarationsFixture
{
    const PLAIN = 1;
    const string TYPED = 'a';

    public $plain;
    public string $typed;
    protected ?array $nullable = null;
    static $plainStatic;

    function plain( $a, &$b, $c = array() )
    {
        $local = function( $x ) { return $x; };
        $typedClosure = function( int $x ): int { return $x; };
        $arrow = fn( $y ) => $y;
        $typedArrow = fn( string $y ): string => $y;
        return $local;
    }

    function typed( int $a, ?eZINI $b, array|string $c ): array
    {
        return array();
    }

    function __construct( private int $promoted, $loose )
    {
    }

    function returnsVoid(): void
    {
    }
}
