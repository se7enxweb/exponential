<?php
/**
 * Converts between two engines that have no converter of their own (stpl -> twig, twig -> stpl) through Exponential
 * templates, in memory and in one run: the first converter writes the .tpl, the second converts it to the target
 * engine. The notes of both are kept. expTemplateDesignConverter uses it for every direction without a
 * ConverterList entry whose two halves, <from>-tpl and tpl-<to>, have one.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTemplateChainConverter
{
    /** @var object[] the converters, in order */
    private $converters;

    /** @var string[] */
    private $warnings = array();

    /**
     * @param object ...$converters each with convert( $text, $name ) and warnings()
     */
    public function __construct( ...$converters )
    {
        $this->converters = $converters;
    }

    public function convert( $text, $name = 'template' )
    {
        $this->warnings = array();
        foreach ( $this->converters as $converter )
        {
            $text = $converter->convert( $text, $name );
            foreach ( $converter->warnings() as $warning )
                $this->warnings[] = $warning;
        }
        return $text;
    }

    public function warnings()
    {
        return $this->warnings;
    }
}
