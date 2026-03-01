<?php
/**
 * File containing the parser class.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class Parser
{

    var $handlerMap = array();
    var $exportableDatatypes;
    var $contentClassCollectorAttributes;
    var $exportCreationDate = false;
    var $exportModificationDate = false;

    function Parser( $objectID = false )
    {
        $ini = eZINI::instance( "export.ini" );
        $this->exportableDatatypes = $ini->variable( "General", "ExportableDatatypes" );

        foreach ( $this->exportableDatatypes as $typename )
        {
            $classname = $ini->variable( $typename, 'HandlerClass' );
            $handler = new $classname;
            $this->handlerMap[$typename] = array( "handler" => $handler, "exportable" => true );
        }

        /*
            if ( $objectID )
            {
                $this->getContentClassCollectorAttributes( $objectID );
            }
        */
    }

    function getExportableDatatypes()
    {
        return $this->exportableDatatypes;
    }

    function exportAttributeHeader( &$attribute, $seperationChar )
    {
        /* Older alternate */
        /*
            $contentClassAttribute = $attribute->contentClassAttribute();
            return $contentClassAttribute->Identifier;
        */

        return $attribute->attribute( 'name' );
    }

    function exportAttribute( &$attribute, $seperationChar )
    {
        $ret = false;
        $datatypeName = eZContentClassAttribute::dataTypeByID( $attribute->ContentClassAttributeID );

        if ( array_key_exists( $datatypeName, $this->handlerMap ) )
        {
            $handler = $this->handlerMap[$datatypeName]['handler'];
        } else {
            $handler = new BaseHandler();
        }

        /*
        BC: Error Debug Comment Test Case Output

                echo ( '<hr />' );
                print_r( $objectAttribute->DataTypeString );
                echo ( '<hr />' );
                print_r( $objectAttribute );
                echo ( '<hr />' );
                print_r( $this->handlerMap );
                echo ( '<hr />' );
        */

        if ( $attribute && $seperationChar && is_object( $handler ) )
        {
            $ret = $handler->exportAttribute( $attribute, $seperationChar );
        }

        if ( is_null( $ret ) )
        {
            return false;
        }
        else
        {
            return $ret;
        }
    }

    /*
     * Returns all collection attributes from the ContentClass of contentObject $objectID
     * @param $objectID int
     * @return set $this->contentClassCollectorAttributes
     */
    function getContentClassCollectorAttributes( $objectID )
    {
        $formObject = eZContentObject::fetch( $objectID );
        $formObjectClass = $formObject->contentClass();
        $contentClassAttributes = $formObjectClass->fetchAttributes();
        $this->contentClassCollectorAttributes = array();

        foreach ( $contentClassAttributes as $contentClassAttribute )
        {
            if ( $contentClassAttribute->attribute( 'is_information_collector' ) )
            {
                array_push(
                    $this->contentClassCollectorAttributes,
                    $contentClassAttribute->attribute( 'id' )
                );
            }
        }
    }

    function exportCollectionObjectHeaderNew(
        &$collection,
        &$attributes_to_export,
        $seperationChar
    )
    {
        $resultstring = array();
        array_push( $resultstring, "ID" );

        /* Older alternate */
        /*
        $attributes2=$collection->informationCollectionAttributes();
        foreach ( $attributes2 as $currentattribute2 ) {
            array_push( $resultstring,$this->exportAttributeHeader( $currentattribute2, $seperationChar ) );
        }
        */

        foreach ( $attributes_to_export as $classAttributeID )
        {
            $contentClassAttribute = eZContentClassAttribute::fetch( $classAttributeID );

            if ( $contentClassAttribute instanceof eZContentClassAttribute )
            {
                array_push(
                    $resultstring,
                    $this->exportAttributeHeader( $contentClassAttribute, $seperationChar )
                );
            }
        }

        if ( $this->getCreationDate() === true )
        {
            array_push(
                $resultstring,
                ezpI18n::tr( "design/bccie/export", "Created" )
            );
        }
        if ( $this->getModificationDate() === true )
        {
            array_push(
                $resultstring,
                ezpI18n::tr( "design/bccie/export", "Modified" )
            );
        }

        return $resultstring;
    }

    function exportCollectionObject( &$collection, &$attributes_to_export, $seperationChar )
    {
        $resultstring = array();
        $emptyAttributeExport = eZINI::instance( "cie.ini" )->variable( "CieSettings", "ExportZeroToEmptyString" ) === 'enabled';

        foreach ( $attributes_to_export as $attributeid )
        {
            if ( $attributeid == "contentobjectid" )
            {
                array_push( $resultstring, $collection->ID );
            }
            else if ( $attributeid == -1 )
            {
                array_push( $resultstring, "" );
            }
            else if ( $attributeid != -2 )
            {
                $attributes = $collection->informationCollectionAttributes();
                $exportedAttribute = false;

                foreach ( $attributes as $currentattribute )
                {
                    if ( ( (int)$attributeid ) == ( (int)$currentattribute->ContentClassAttributeID ) )
                    {
                        /* array_push( $resultstring, $this->exportAttribute( $currentattribute, $seperationChar ) ); */
                        $exportedAttribute = $this->exportAttribute(
                                                  $currentattribute,
                                                      $seperationChar
                        );
                    }
                }

                if ( ( !$emptyAttributeExport && $exportedAttribute !== false ) || ( $emptyAttributeExport && $exportedAttribute ) )
                {
                    array_push( $resultstring, utf8_encode( $exportedAttribute ) );
                }
                else
                {
                    array_push( $resultstring, "" );
                }
            }
        }

        if ( $this->getCreationDate() === true )
        {
            array_push( $resultstring, date( 'c', $collection->attribute( 'created' ) ) );
        }

        if ( $this->getModificationDate() === true )
        {
            array_push( $resultstring, date( 'c', $collection->attribute( 'modified' ) ) );
        }

        return $resultstring;
    }

    function exportCollectionObjectHeader( &$attributes_to_export )
    {
        $resultstring = array();
        foreach ( $attributes_to_export as $attributeid )
        {
            if ( $attributeid == "contentobjectid" )
            {
                array_push( $resultstring, "ID" );
            }
            else if ( $attributeid == -1 )
            {
                array_push( $resultstring, "" );
            }
            else if ( $attributeid != -2 )
            {
                $attribute = & eZContentClassAttribute::fetch( $attributeid );
                $attribute_name = $attribute->name();
                $attribute_name_escaped = preg_replace( "(\r\n|\n|\r)", " ", $attribute_name );
                $attribute_name_escaped = utf8_decode( $attribute_name_escaped );
                array_push( $resultstring, $attribute_name_escaped );

                // works for 3.8 only
                // array_push( $resultstring, $attribute->Name );
            }
        }

        return $resultstring;
    }

    function exportInformationCollection(
        $collections,
        $attributes_to_export,
        $seperationChar,
        $export_type = 'csv',
        $days = false,
        $creation_date = false,
        $modification_date = false
    )
    {
        // eZDebug::writeDebug( $attributes_to_export );
        if ( $creation_date !== false )
        {
            $this->setCreationDate( true );
        }

        if ( $modification_date !== false )
        {
            $this->setModificationDate( true );
        }

        switch ( $export_type )
        {
            case "csv" :
                $returnstring = array();

                // TODO: Refactor foreach into method
                array_push(
                    $returnstring,
                    $this->exportCollectionObjectHeaderNew(
                         $collections[0],
                             $attributes_to_export,
                             $seperationChar
                    )
                );

                foreach ( $collections as $collection )
                {
                    if ( $days != false )
                    {
                        $current_datestamp = strtotime( "now" );
                        $ci_created = $collection->Created;
                        $range = mktime( 0, 0, 0, date( "m" ), date( "d" ) - $days, date( "Y" ) );

                        /*
                           print_r( $collection );
                           print_r( "\n##################################" );
                                       print_r( "\nDate: $current_datestamp". strtotime("now") );
                                       print_r( "\nCreated: $ci_created" );
                                       print_r( "\nDays: $days | $range" );
                           print_r( "\n##################################" );
                           // die();
                        */

                        if ( $ci_created < $current_datestamp && $ci_created >= $range )
                        {
                            // print_r( "\nCI Date is lt current date and CI Date is gt eq range \n" );
                            array_push(
                                $returnstring,
                                $this->exportCollectionObject(
                                     $collection,
                                         $attributes_to_export,
                                         $seperationChar
                                )
                            );
                        }
                    }
                    else
                    {
                        array_push(
                            $returnstring,
                            $this->exportCollectionObject(
                                 $collection,
                                     $attributes_to_export,
                                     $seperationChar
                            )
                        );
                    }
                    // array_push( $returnstring, $this->exportCollectionObject( $collection, $attributes_to_export, $seperationChar ) );
                }

                return $this->csv( $returnstring, $seperationChar );
                break;
            case "sylk":
                $returnstring = array();
                // array_push( $returnstring, $this->exportCollectionObjectHeader( $attributes_to_export ) );
                // TODO: Refactor foreach into method

                foreach ( $collections as $collection )
                {
                    if ( $days != false )
                    {
                        $current_datestamp = strtotime( "now" );
                        $ci_created = $collection->Created;
                        $range = mktime( 0, 0, 0, date( "m" ), date( "d" ) - $days, date( "Y" ) );
                        /*
                                         print_r( $collection );
                                         print_r( "\n##################################" );
                                         print_r( "\nDate: $current_datestamp". strtotime("now") );
                                         print_r( "\nCreated: $ci_created" );
                                         print_r( "\nDays: $days | $range" );
                                         print_r( "\n##################################" );
                                         // die();
                        */

                        if ( $ci_created < $current_datestamp && $ci_created >= $range )
                        {
                            // print_r( "\nCI Date is lt current date and CI Date is gt eq range \n" );
                            array_push(
                                $returnstring,
                                $this->exportCollectionObject(
                                     $collection,
                                         $attributes_to_export,
                                         $seperationChar
                                )
                            );
                        }
                    }
                    else
                    {
                        array_push(
                            $returnstring,
                            $this->exportCollectionObject(
                                 $collection,
                                     $attributes_to_export,
                                     $seperationChar
                            )
                        );
                    }
                }

                return $this->sylk( $returnstring );
                break;
            default:
                $export_type = 'csv';
                $returnstring = array();

                // TODO: Refactor foreach into method
                foreach ( $collections as $collection )
                {
                    if ( $days != false )
                    {
                        $current_datestamp = strtotime( "now" );
                        $ci_created = $collection->Created;
                        $range = mktime( 0, 0, 0, date( "m" ), date( "d" ) - $days, date( "Y" ) );
                        /*
                                       print_r( $collection );
                                       print_r( "\n##################################" );
                                       print_r( "\nDate: $current_datestamp". strtotime("now") );
                                       print_r( "\nCreated: $ci_created" );
                                       print_r( "\nDays: $days | $range" );
                                       print_r( "\n##################################" );
                                       // die();
                        */

                        if ( $ci_created < $current_datestamp && $ci_created >= $range )
                        {
                            // print_r( "\nCI Date is lt current date and CI Date is gt eq range \n" );
                            array_push(
                                $returnstring,
                                $this->exportCollectionObject(
                                     $collection,
                                         $attributes_to_export,
                                         $seperationChar
                                )
                            );
                        }
                    }
                    else
                    {
                        array_push(
                            $returnstring,
                            $this->exportCollectionObject(
                                 $collection,
                                     $attributes_to_export,
                                     $seperationChar
                            )
                        );
                    }
                }

                return $this->csv( $returnstring, $seperationChar );
                break;
        }
    }

    /*
     * SYLK EXPORT
     */
    function sylk( $tableau )
    {
        if ( !defined( 'FORMAT_REEL' ) )
        {
            define( "FORMAT_REEL", 1 );
        } // #,##0.00

        if ( !defined( 'FORMAT_ENTIER' ) )
        {
            define( "FORMAT_ENTIER", 2 );
        } // #,##0

        if ( !defined( 'FORMAT_TEXTE' ) )
        {
            define( "FORMAT_TEXTE", 3 );
        } // @

        $cfg_formats[FORMAT_ENTIER] = "FF0";
        $cfg_formats[FORMAT_REEL] = "FF2";
        $cfg_formats[FORMAT_TEXTE] = "FG0";

        if ( $tableau )
        {
            // en-t? du fichier SYLK
            // $sylkcontent = "ID;Atchoum Production\n"; // ID;Pappli
            $sylkcontent = "ID;Pcie\n"; // ID;Pappli
            $sylkcontent = $sylkcontent . "\n";
            // formats
            $sylkcontent = $sylkcontent . "P;PGeneral\n";
            $sylkcontent = $sylkcontent . "P;P#,##0.00\n"; // P;Pformat_1 (reels)
            $sylkcontent = $sylkcontent . "P;P#,##0\n"; // P;Pformat_2 (entiers)
            $sylkcontent = $sylkcontent . "P;P@\n"; // P;Pformat_3 (textes)
            $sylkcontent = $sylkcontent . "\n";
            // polices
            $sylkcontent = $sylkcontent . "P;EArial;M200\n";
            $sylkcontent = $sylkcontent . "P;EArial;M200\n";
            $sylkcontent = $sylkcontent . "P;EArial;M200\n";
            $sylkcontent = $sylkcontent . "P;FArial;M200;SB\n";
            $sylkcontent = $sylkcontent . "\n";
            // nb lignes * nb colonnes :  B;Yligmax;Xcolmax
            $sylkcontent = $sylkcontent . "B;Y" . ( count( $tableau ) );
            // detection du nb de colonnes

            for ( $i = 0; $i < count( $tableau ); $i++ )
            {
                $tmp[$i] = count( $tableau[$i] );
            }
            $nbcol = max( $tmp );
            $sylkcontent = $sylkcontent . ";X" . $nbcol . "\n";
            $sylkcontent = $sylkcontent . "\n";
            // r?p?tion des infos de formatage des colonnes
            for ( $cpt = 0; $cpt < $nbcol; $cpt++ )
            {
                if ( isset( $tableau[1][$cpt] ) )
                {
                    switch ( gettype( $tableau[1][$cpt] ) )
                    {
                        case "integer":
                            $num_format[$cpt] = FORMAT_ENTIER;
                            $format[$cpt] = $cfg_formats[$num_format[$cpt]] . "R";
                            break;
                        case "double":
                            $num_format[$cpt] = FORMAT_REEL;
                            $format[$cpt] = $cfg_formats[$num_format[$cpt]] . "R";
                            break;
                        default:
                            $num_format[$cpt] = FORMAT_TEXTE;
                            $format[$cpt] = $cfg_formats[$num_format[$cpt]] . "L";
                            break;
                    }
                }
            }
            // largeurs des colonnes
            for ( $cpt = 1; $cpt <= $nbcol; $cpt++ )
            {
                for ( $t = 0; $t < count( $tableau ); $t++ )
                {
                    // $tmpo[$t]= strlen( $tableau[$t][$cpt-1] );
                    if ( isset( $tableau[$t] ) )
                    {
                        if ( isset( $tableau[$t][$cpt - 1] ) )
                        {
                            $tmpo[$t] = strlen( $tableau[$t][$cpt - 1] );
                        }
                    }
                }

                /*
                if( !isset( $tableau[$t] ) )
                {
                  $xyz = $cpt-1;
                  print_r( "TTD: $t | " . $xyz ."\n" );
                  // print_r( $tableau[$t] );
                }
                */
                // print_r( $tableau[$t][$cpt-1] );
                /*
                print_r( $tmpo );
                print_r( max($tmpo) );
                die( $tmpo );
                */

                $taille = max( $tmpo );
                if ( $taille == 0 )
                {
                    $taille = 1;
                }
                // F;Wcoldeb colfin largeur
                if ( strlen( $tableau[0][$cpt - 1] ) > $taille )
                {
                    $taille = strlen( $tableau[0][$cpt - 1] );
                }
                if ( $taille > 50 )
                {
                    $taille = 50;
                }
                $sylkcontent = $sylkcontent . "F;W" . $cpt . " " . $cpt . " " . $taille . "\n";
            }
            $sylkcontent = $sylkcontent . "F;W" . $cpt . " 256 8\n"; // F;Wcoldeb colfin largeur
            $sylkcontent = $sylkcontent . "\n";
            // en-t? des colonnes (en gras --> SDM4)
            for ( $cpt = 1; $cpt <= $nbcol; $cpt++ )
            {
                $sylkcontent = $sylkcontent . "F;SDM4;FG0C;" . ( $cpt == 1 ? "Y1;" : "" ) . "X" . $cpt . "\n";
                $sylkcontent = $sylkcontent . "C;N;K\"" . $tableau[0][$cpt - 1] . "\"\n";
            }
            $sylkcontent = $sylkcontent . "\n";
            // donn? utiles
            $ligne = 2;
            for ( $i = 1; $i < count( $tableau ); $i++ )
            {
                // parcours des champs
                for ( $cpt = 0; $cpt < $nbcol; $cpt++ )
                {
                    // print_r( $num_format[$cpt] );
                    // print_r( $format[$cpt] );

                    if ( isset( $format[$cpt] ) && isset( $format[$cpt] ) )
                    {
                        // format
                        $sylkcontent = $sylkcontent . "F;P" . $num_format[$cpt] . ";" . $format[$cpt];
                        $sylkcontent = $sylkcontent . ( $cpt == 0 ? ";Y" . $ligne : "" ) . ";X" . ( $cpt + 1 ) . "\n";

                        // valeur
                        if ( $num_format[$cpt] == FORMAT_TEXTE )
                        {
                            // $sylkcontent = $sylkcontent."C;N;K\"".str_replace(';', ';;', $tableau[$i][$cpt])."\"\n";
                            // if( isset( $tableau[$t] ) )
                            if ( isset( $tableau[$i] ) )
                            {
                                if ( isset( $tableau[$i][$cpt] ) )
                                {
                                    $sylkcontent = $sylkcontent . "C;N;K\"" . str_replace(
                                            ';',
                                            ';;',
                                            $tableau[$i][$cpt]
                                        ) . "\"\n";
                                }
                            }
                            // $sylkcontent = $sylkcontent."C;N;K\"".str_replace( ';', ';;', $tableau[$i][$cpt] )."\"\n";
                        }
                        else
                        {
                            $sylkcontent = $sylkcontent . "C;N;K" . $tableau[$i][$cpt] . "\n";
                        }
                    }
                }
                $sylkcontent = $sylkcontent . "\n";
                $ligne++;
            }
            // fin du fichier
            $sylkcontent = $sylkcontent . "E\n";

            return $sylkcontent;
        }
        else
        {
            return false;
        }
    }

    /*
     * CSV EXPORT
     */
    function csv( $tableau, $seperator )
    {
        if ( $tableau )
        {
            $line = "truc";
            for ( $i = 0; $i < count( $tableau ); $i++ )
            {
                $tmp[$i] = count( $tableau[$i] );
            }
            $nbcol = max( $tmp );

            $line = "";

            for ( $i = 0; $i < count( $tableau ); $i++ )
            {
                // parcours des champs
                for ( $cpt = 0; $cpt < $nbcol; $cpt++ )
                {
                    if ( isset( $tableau[$i][$cpt] ) )
                    {
                        $line .= '"' . trim( $tableau[$i][$cpt] ) . '"' . $seperator;
                    }
                }
                $line .= "\n";
            }

            return $line;
        }
    }

    public function setModificationDate( $date )
    {
        $this->exportModificationDate = $date;
    }

    public function setCreationDate( $date )
    {
        $this->exportCreationDate = $date;
    }

    public function getCreationDate()
    {
        return $this->exportCreationDate;
    }
    public function getModificationDate()
    {
        return $this->exportModificationDate;
    }
}

?>