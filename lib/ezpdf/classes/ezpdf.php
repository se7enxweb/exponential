<?php
/**
 * File containing the eZPDF class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \defgroup eZPDF PDF generator library
*/

/*!
  \class eZPDF ezpdf.php
  \ingroup eZPDF
  \brief eZPDF provides template operators for dealing with pdf generation
*/

class eZPDF
{
    /**
     * Initializes the object with the name $name, default is "pdf".
     *
     * @param string $name
     */
    public function __construct( $name = "pdf" )
    {
        $this->Operators = array( $name );
        $this->Config = eZINI::instance( 'pdf.ini' );
    }

    /*!
     Returns the template operators.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    /*!
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return array( 'operation' => array( 'type' => 'string',
                                            'required' => true,
                                            'default' => '' ) );
    }

    /*!
     Display the variable.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        $config = eZINI::instance( 'pdf.ini' );

        switch ( $namedParameters['operation'] )
        {
            case 'toc':
            {
                $operatorValue = '<C:callTOC';

                if ( count( $operatorParameters ) > 1 )
                {
                    $params = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                    $operatorValue .= isset( $params['size'] ) ? ':size:'. implode(',', $params['size'] ) : '';
                    $operatorValue .= isset( $params['dots'] ) ? ':dots:'. $params['dots'] : '';
                    $operatorValue .= isset( $params['contentText'] ) ? ':contentText:'. $params['contentText'] : '';
                    $operatorValue .= isset( $params['indent'] ) ? ':indent:'. implode(',', $params['indent'] ) : '';

                }

                $operatorValue .= '>';
                eZDebug::writeNotice( 'PDF: Generating TOC', __METHOD__ );
            } break;

            case 'set_font':
            {
                $params = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $operatorValue = '<ezCall:callFont';

                foreach ( $params as $key => $value )
                {
                    if ( $key == 'colorCMYK' )
                    {
                        $operatorValue .= ':cmyk:' . implode( ',', $value );
                    }
                    else if ( $key == 'colorRGB' )
                    {
                        $operatorValue .= ':cmyk:' . implode( ',', eZMath::rgbToCMYK2( $value[0]/255,
                                                                                       $value[1]/255,
                                                                                       $value[2]/255 ) );
                    }
                    else
                    {
                        $operatorValue .= ':' . $key . ':' . $value;
                    }
                }
                $operatorValue .= '>';

                eZDebug::writeNotice( 'PDF: Changed font.' );
            } break;

            case 'table':
            {
                $operatorValue = '<ezGroup:callTable';

                if ( count( $operatorParameters > 2 ) )
                {
                    $tableSettings = $tpl->elementValue( $operatorParameters[2], $rootNamespace, $currentNamespace );

                    if ( is_array( $tableSettings ) )
                    {
                        foreach( array_keys( $tableSettings ) as $key )
                        {
                            switch( $key )
                            {
                                case 'headerCMYK':
                                case 'cellCMYK':
                                case 'textCMYK':
                                case 'titleCellCMYK':
                                case 'titleTextCMYK':
                                {
                                    $operatorValue .= ':' . $key . ':' . implode( ',', $tableSettings[$key] );
                                } break;

                                default:
                                {
                                    $operatorValue .= ':' . $key . ':' . $tableSettings[$key];
                                } break;
                            }
                        }
                    }
                }

                $operatorValue .= '>';

                $rows = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $rows = str_replace( array( ' ', "\t", "\r\n", "\n" ),
                                                          '',
                                                          $rows );
                $rows = eZPDF::toOutputCharset( $rows );

                $operatorValue .= urlencode( $rows );

                $operatorValue .= '</ezGroup:callTable><C:callNewLine>';

                eZDebug::writeNotice( 'PDF: Added table to PDF', __METHOD__ );
            } break;

            case 'header':
            {
                $header = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $header['text'] = str_replace( array( ' ', "\t", "\r\n", "\n" ),
                                               '',
                                               $header['text'] );

                $operatorValue = '<ezCall:callHeader:level:'. $header['level'] .':size:'. $header['size'];

                if ( isset( $header['align'] ) )
                {
                    $operatorValue .= ':justification:'. $header['align'];
                }

                if ( isset( $header['font'] ) )
                {
                    $operatorValue .= ':fontName:'. $header['font'];
                }

                $operatorValue .= ':label:'. rawurlencode( $header['text'] );

                $operatorValue .= '><C:callNewLine>'. $header['text'] .'</ezCall:callHeader><C:callNewLine>';

                eZDebug::writeNotice( 'PDF: Added header: '. $header['text'] .', size: '. $header['size'] .
                                      ', align: '. $header['align'] .', level: '. $header['level'],
                                      __METHOD__ );
            } break;

            case 'create':
            {
                $this->createPDF();
            } break;

            case 'new_line':
            case 'newline':  // Deprecated
            {
                $operatorValue = '<C:callNewLine>';
            } break;

            case 'new_page':
            case 'newpage':  // Deprecated
            {
                $operatorValue = '<C:callNewPage><C:callNewLine>';

                eZDebug::writeNotice( 'PDF: New page', __METHOD__ );
            } break;

            case 'image':
            {
                $image = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $width = isset( $image['width'] ) ? $image['width']: 100;
                $height = isset( $image['height'] ) ? $image['height']: 100;

                $operatorValue = '<C:callImage:src:'. rawurlencode( $image['src'] ) .':width:'. $width .':height:'. $height;

                if ( isset( $image['static'] ) )
                {
                    $operatorValue .= ':static:' . $image['static'];
                }

                if ( isset ( $image['x'] ) )
                {
                    $operatorValue .= ':x:' . $image['x'];
                }

                if ( isset( $image['y'] ) )
                {
                    $operatorValue .= ':y:' . $image['y'];
                }

                if ( isset( $image['dpi'] ) )
                {
                    $operatorValue .= ':dpi:' . $image['dpi'];
                }

                if ( isset( $image['align'] ) ) // left, right, center, full
                {
                    $operatorValue .= ':align:' . $image['align'];
                }

                $operatorValue .= '>';

                eZDebug::writeNotice( 'PDF: Added Image '.$image['src'].' to PDF file', __METHOD__ );
            } break;

            case 'anchor':
            {
                $name = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $operatorValue = '<C:callAnchor:'. $name['name'] .':FitH:>';
                eZDebug::writeNotice( 'PDF: Added anchor: '.$name['name'], __METHOD__ );
            } break;

            case 'link': // external link
            {
                $link = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $link['text'] = str_replace( '&quot;',
                                             '"',
                                             $link['text'] );

                $operatorValue = '<c:alink:'. rawurlencode( $link['url'] ) .'>'. $link['text'] .'</c:alink>';
                eZDebug::writeNotice( 'PDF: Added link: '. $link['text'] .', url: '.$link['url'], __METHOD__ );
            } break;

            case 'stream':
            {
                $this->PDF->ezStream();
            } break;

            case 'close':
            {
                $filename = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                eZDir::mkdir( eZDir::dirpath( $filename ), false, true );

                $file = eZClusterFileHandler::instance( $filename );
                $file->storeContents( $this->PDF->ezOutput(), 'viewcache', 'pdf' );

                eZDebug::writeNotice( 'PDF file closed and saved to '. $filename, __METHOD__ );
            } break;

            case 'strike':
            {
                $text = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );
                $operatorValue = '<c:strike>'. $text .'</c:strike>';
                eZDebug::writeNotice( 'Striked text added to PDF: "'. $text .'"', __METHOD__ );
            } break;

            /* usage : execute/add text to pdf file, pdf(execute,<text>) */
            case 'execute':
            {
                $text = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                if ( count ( $operatorParameters ) > 2 )
                {
                    $options = $tpl->elementValue( $operatorParameters[2], $rootNamespace, $currentNamespace );

                    $size = isset( $options['size'] ) ? $options['size'] : $config->variable( 'PDFGeneral', 'Format' );
                    $orientation = isset( $options['orientation'] ) ? $options['orientation'] : $config->variable( 'PDFGeneral', 'Orientation' );

                    $this->createPDF( $size, $orientation );
                }
                else
                {
                    $this->createPDF( $config->variable( 'PDFGeneral', 'Format' ), $config->variable( 'PDFGeneral', 'Orientation' ) );
                }

                $text = str_replace( array( ' ', "\n", "\t" ), '', $text );
                $text = eZPDF::toOutputCharset( $text );

                $this->PDF->ezText( $text );
                eZDebug::writeNotice( 'Execute text in PDF, length: "'. strlen( $text ) .'"', __METHOD__ );
            } break;

            case 'page_number':
            case 'pageNumber':
            {
                $numberDesc = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                if ( isset( $numberDesc['identifier'] ) )
                {
                    $identifier = $numberDesc['identifier'];
                }
                else
                {
                    $identifier = 'main';
                }

                if ( isset( $numberDesc['start'] ) )
                {
                    $operatorValue = '<C:callStartPageCounter:start:'. $numberDesc['start'] .':identifier:'. $identifier .'>';
                }
                else if ( isset( $numberDesc['stop'] ) )
                {
                    $operatorValue = '<C:callStartPageCounter:stop:1:identifier:'. $identifier .'>';
                }
            } break;

            /* usage {pdf( line, hash( x1, <x>, y1, <y>, x2, <x2>, y2, <y2>, pages, <all|current>, thickness, <1..100>,  ) )} */
            case 'line':
            {
                $lineDesc = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                if ( isset( $lineDesc['pages']) and
                     $lineDesc['pages'] == 'all' )
                {
                    $operatorValue = '<ezGroup:callLine';
                }
                else
                {
                    $operatorValue = '<C:callDrawLine';
                }

                $operatorValue .= ':x1:' . $lineDesc['x1'];
                $operatorValue .= ':x2:' . $lineDesc['x2'];
                $operatorValue .= ':y1:' . $lineDesc['y1'];
                $operatorValue .= ':y2:' . $lineDesc['y2'];

                $operatorValue .= ':thickness:' . ( isset( $lineDesc['thickness'] ) ? $lineDesc['thickness'] : '1' );

                $operatorValue .= '>';

                if ( $lineDesc['pages'] == 'all' )
                {
                    $operatorValue .= '___</ezGroup:callLine>';
                }

                return $operatorValue;
            } break;

            case 'footer_block':
            case 'header_block':
            {
                $frameDesc = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $operatorValue = '<ezGroup:callBlockFrame';
                $operatorValue .= ':location:'. $namedParameters['operation'];
                $operatorValue .= '>';

                if ( isset( $frameDesc['block_code'] ) )
                {
                    $frameDesc['block_code'] = eZPDF::toOutputCharset( $frameDesc['block_code'] );
                    $operatorValue .= urlencode( $frameDesc['block_code'] );
                }

                $operatorValue .= '</ezGroup:callBlockFrame>';

                eZDebug::writeNotice( 'PDF: Added Block '.$namedParameters['operation'] .': '.$operatorValue, __METHOD__ );
                return $operatorValue;

            } break;

            /* deprecated */
            case 'footer':
            case 'frame_header':
            {
                $frameDesc = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $operatorValue  = '<ezGroup:callFrame';
                $operatorValue .= ':location:'. $namedParameters['operation'];

                if ( $namedParameters['operation'] == 'footer' )
                {
                    $frameType = 'Footer';
                }
                else if( $namedParameters['operation'] == 'frame_header' )
                {
                    $frameType = 'Header';
                }

                if ( isset( $frameDesc['align'] ) )
                {
                    $operatorValue .= ':justification:'. $frameDesc['align'];
                }

                if ( isset( $frameDesc['page'] ) )
                {
                    $operatorValue .= ':page:'. $frameDesc['page'];
                }
                else
                {
                    $operatorValue .= ':page:all';
                }

                $operatorValue .= ':newline:' . ( isset( $frameDesc['newline'] ) ? $frameDesc['newline'] : 0 );

                $operatorValue .= ':pageOffset:';
                if ( isset( $frameDesc['pageOffset'] ) )
                {
                    $operatorValue .= $frameDesc['pageOffset'];
                }
                else
                {
                    $operatorValue .= $this->Config->variable( $frameType, 'PageOffset' );
                }

                if ( isset( $frameDesc['size'] ) )
                {
                    $operatorValue .= ':size:'. $frameDesc['size'];
                }

                if ( isset( $frameDesc['font'] ) )
                {
                    $operatorValue .= ':font:'. $frameDesc['font'];
                }

                $operatorValue .= '>';

                if ( isset( $frameDesc['text'] ) )
                {
                    $frameDesc['text'] = eZPDF::toOutputCharset( $frameDesc['text'] );
                    $operatorValue .= urlencode( $frameDesc['text'] );
                }

                $operatorValue .= '</ezGroup:callFrame>';

                if ( isset( $frameDesc['margin'] ) )
                {
                    $operatorValue .= '<C:callFrameMargins';

                    $operatorValue .= ':identifier:'. $namedParameters['operation'];

                    $operatorValue .= ':topMargin:';
                    if ( isset( $frameDesc['margin']['top'] ) )
                    {
                        $operatorValue .= $frameDesc['margin']['top'];
                    }
                    else
                    {
                        $operatorValue .= $this->Config->variable( $frameType, 'TopMargin' );
                    }

                    $operatorValue .= ':bottomMargin:';
                    if ( isset( $frameDesc['margin']['bottom'] ) )
                    {
                        $operatorValue .= $frameDesc['margin']['bottom'];
                    }
                    else
                    {
                        $operatorValue .= $this->Config->variable( $frameType, 'BottomMargin' );
                    }

                    $operatorValue .= ':leftMargin:';
                    if ( isset( $frameDesc['margin']['left'] ) )
                    {
                        $operatorValue .= $frameDesc['margin']['left'];
                    }
                    else
                    {
                        $operatorValue .= $this->Config->variable( $frameType, 'LeftMargin' );
                    }

                    $operatorValue .= ':rightMargin:';
                    if ( isset( $frameDesc['margin']['right'] ) )
                    {
                        $operatorValue .= $frameDesc['margin']['right'];
                    }
                    else
                    {
                        $operatorValue .= $this->Config->variable( $frameType, 'RightMargin' );
                    }

                    $operatorValue .= ':height:';
                    if ( isset( $frameDesc['margin']['height'] ) )
                    {
                        $operatorValue .= $frameDesc['margin']['height'];
                    }
                    else
                    {
                        $operatorValue .= $this->Config->variable( $frameType, 'Height' );
                    }

                    $operatorValue .= '>';
                }

                if ( isset( $frameDesc['line'] ) )
                {
                    $operatorValue .= '<C:callFrameLine';
                    $operatorValue .= ':location:'. $namedParameters['operation'];

                    $operatorValue .= ':margin:';
                    if( isset( $frameDesc['line']['margin'] ) )
                    {
                        $operatorValue .= $frameDesc['line']['margin'];
                    }
                    else
                    {
                        $operatorValue .= $this->Config->variable( $frameType, 'LineMargin' );
                    }

                    if ( isset( $frameDesc['line']['leftMargin'] ) )
                    {
                        $operatorValue .= ':leftMargin:'. $frameDesc['line']['leftMargin'];
                    }
                    if ( isset( $frameDesc['line']['rightMargin'] ) )
                    {
                        $operatorValue .= ':rightMargin:'. $frameDesc['line']['rightMargin'];
                    }

                    $operatorValue .= ':pageOffset:';
                    if ( isset( $frameDesc['line']['pageOffset'] ) )
                    {
                        $operatorValue .= $frameDesc['line']['pageOffset'];
                    }
                    else
                    {
                        $operatorValue .= $this->Config->variable( $frameType, 'PageOffset' );
                    }

                    $operatorValue .= ':page:';
                    if ( isset( $frameDesc['line']['page'] ) )
                    {
                        $operatorValue .= $frameDesc['line']['page'];
                    }
                    else
                    {
                        $operatorValue .= $this->Config->variable( $frameType, 'Page' );
                    }

                    $operatorValue .= ':thickness:';
                    if ( isset( $frameDesc['line']['thickness'] ) )
                    {
                        $operatorValue .= $frameDesc['line']['thickness'];
                    }
                    else
                    {
                        $operatorValue .= $this->Config->variable( $frameType, 'LineThickness' );
                    }
                    $operatorValue .= '>';
                }

                eZDebug::writeNotice( 'PDF: Added frame '.$frameType .': '.$operatorValue, __METHOD__ );
            } break;

            case 'frontpage':
            {
                $pageDesc = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $align = isset( $pageDesc['align'] ) ? $pageDesc['align'] : 'center';
                $text = isset( $pageDesc['text'] ) ? $pageDesc['text'] : '';
                $top_margin = isset( $pageDesc['top_margin'] ) ? $pageDesc['top_margin'] : 100;

                $operatorValue = '<ezGroup:callFrontpage:justification:'. $align .':top_margin:'. $top_margin;

                if ( isset( $pageDesc['size'] ) )
                {
                    $operatorValue .= ':size:'. $pageDesc['size'];
                }

                $text = str_replace( array( ' ', "\t", "\r\n", "\n" ),
                                     '',
                                     $text );
                $text = eZPDF::toOutputCharset( $text );

                $operatorValue .= '>'. urlencode( $text ) .'</ezGroup:callFrontpage>';

                eZDebug::writeNotice( 'Added content to frontpage: '. $operatorValue, __METHOD__ );
            } break;

            /* usage: pdf(set_margin( hash( left, <left_margin>,
                                            right, <right_margin>,
                                            x, <x offset>,
                                            y, <y offset> )))
            */
            case 'set_margin':
            {
                $operatorValue = '<C:callSetMargin';
                $options = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                foreach( array_keys( $options ) as $key )
                {
                    $operatorValue .= ':' . $key . ':' . $options[$key];
                }

                $operatorValue .= '>';

                eZDebug::writeNotice( 'Added new margin/offset setup: ' . $operatorValue );

                return $operatorValue;
            } break;

            /* add keyword to pdf document */
            case 'keyword':
            {
                $text = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $text = str_replace( array( ' ', "\n", "\t" ), '', $text );
                $text = eZPDF::toOutputCharset( $text );

                $operatorValue = '<C:callKeyword:'. rawurlencode( $text ) .'>';
            } break;

            /* add Keyword index to pdf document */
            case 'createIndex':
            case 'create_index':
            {
                $operatorValue = '<C:callIndex>';

                eZDebug::writeNotice( 'Adding Keyword index to PDF', __METHOD__ );
            } break;

            case 'ul':
            {
                $text = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );
                if ( count( $operatorParameters ) > 2 )
                {
                    $params = $tpl->elementValue( $operatorParameters[2], $rootNamespace, $currentNamespace );
                }
                else
                {
                    $params = array();
                }

                if ( isset( $params['rgb'] ) )
                {
                    $params['rgb'] = eZMath::normalizeColorArray( $params['rgb'] );
                    $params['cmyk'] = eZMath::rgbToCMYK2( $params['rgb'][0]/255,
                                                          $params['rgb'][1]/255,
                                                          $params['rgb'][2]/255 );
                }

                if ( !isset( $params['cmyk'] ) )
                {
                    $params['cmyk'] = eZMath::rgbToCMYK2( 0, 0, 0 );
                }
                if ( !isset( $params['radius'] ) )
                {
                    $params['radius'] = 2;
                }
                if ( !isset ( $params['pre_indent'] ) )
                {
                    $params['pre_indent'] = 0;
                }
                if ( !isset ( $params['indent'] ) )
                {
                    $params['indent'] = 2;
                }
                if ( !isset ( $params['yOffset'] ) )
                {
                    $params['yOffset'] = -1;
                }

                $operatorValue = '<C:callCircle' .
                     ':pages:current' .
                     ':x:-1' .
                     ':yOffset:' . $params['yOffset'] .
                     ':y:-1' .
                     ':indent:' . $params['indent'] .
                     ':pre_indent:' . $params['pre_indent'] .
                     ':radius:' . $params['radius'] .
                     ':cmyk:' . implode( ',', $params['cmyk'] ) .
                     '>';

                $operatorValue .= '<C:callSetMargin' .
                     ':delta_left:' . ( $params['indent'] + $params['radius'] * 2 + $params['pre_indent'] ) .
                     '>';

                $operatorValue .= $text;

                $operatorValue .= '<C:callSetMargin' .
                     ':delta_left:' . -1 * ( $params['indent'] + $params['radius'] * 2 + $params['pre_indent'] ) .
                     '>';
            } break;

            case 'filled_circle':
            {
                $operatorValue = '';
                $options = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                if ( !isset( $options['pages'] ) )
                {
                    $options['pages'] = 'current';
                }

                if ( !isset( $options['x'] ) )
                {
                    $options['x'] = -1;
                }

                if ( !isset( $options['y'] ) )
                {
                    $options['y'] = -1;
                }

                if ( isset( $options['rgb'] ) )
                {
                    $options['rgb'] = eZMath::normalizeColorArray( $options['rgb'] );
                    $options['cmyk'] = eZMath::rgbToCMYK2( $options['rgb'][0]/255,
                                                           $options['rgb'][1]/255,
                                                           $options['rgb'][2]/255 );
                }

                $operatorValue = '<C:callCircle' .
                     ':pages:' . $options['pages'] .
                     ':x:' . $options['x'] .
                     ':y:' . $options['y'] .
                     ':radius:' . $options['radius'];

                if ( isset( $options['cmyk'] ) )
                {
                    $operatorValue .= ':cmyk:' . implode( ',', $options['cmyk'] );
                }

                $operatorValue .= '>';

                eZDebug::writeNotice( 'PDF Added circle: ' . $operatorValue );

                return $operatorValue;
            } break;

            case 'rectangle':
            {
                $operatorValue = '';
                $options = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                if ( !isset( $options['pages'] ) )
                {
                    $options['pages'] = 'current';
                }
                if ( !isset( $options['line_width'] ) )
                {
                    $options['line_width'] = 1;
                }
                if ( !isset( $options['round_corner'] ) )
                {
                    $options['round_corner'] = false;
                }

                $operatorValue = '<C:callRectangle';
                foreach ( $options as $key => $value )
                {
                    if ( $key == 'rgb' )
                    {
                        $options['rgb'] = eZMath::normalizeColorArray( $options['rgb'] );
                        $operatorValue .= ':cmyk:' . implode( ',',  eZMath::rgbToCMYK2( $options['rgb'][0]/255,
                                                                                        $options['rgb'][1]/255,
                                                                                        $options['rgb'][2]/255 ) );
                    }
                    else if ( $key == 'cmyk' )
                    {
                        $operatorValue .= ':cmyk:' . implode( ',', $value );
                    }
                    else
                    {
                        $operatorValue .= ':' . $key . ':' . $value;
                    }
                }
                $operatorValue .= '>';

                eZDebug::writeNotice( 'PDF Added rectangle: ' . $operatorValue );

                return $operatorValue;
            } break;

            /* usage: pdf( filled_rectangle, hash( 'x', <x offset>, 'y' => <y offset>, 'width' => <width>, 'height' => <height>,
                                                    'pages', <'all'|'current'|odd|even>, (supported, current)
                                                    'rgb', array( <r>, <g>, <b> ),
                                                    'cmyk', array( <c>, <m>, <y>, <k> ),
                                                    'rgbTop', array( <r>, <b>, <g> ),
                                                    'rgbBottom', array( <r>, <b>, <g> ),
                                                    'cmykTop', array( <c>, <m>, <y>, <k> ),
                                                    'cmykBottom', array( <c>, <m>, <y>, <k> ) ) ) */
            case 'filled_rectangle':
            {
                $operatorValue = '';
                $options = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                if ( !isset( $options['pages'] ) )
                {
                    $options['pages'] = 'current';
                }

                if ( isset( $options['rgb'] ) )
                {
                    $options['rgb'] = eZMath::normalizeColorArray( $options['rgb'] );
                    $options['cmyk'] = eZMath::rgbToCMYK2( $options['rgb'][0]/255,
                                                           $options['rgb'][1]/255,
                                                           $options['rgb'][2]/255 );
                }

                if ( isset( $options['cmyk'] ) )
                {
                    $options['cmykTop'] = $options['cmyk'];
                    $options['cmykBottom'] = $options['cmyk'];
                }

                if ( !isset( $options['cmykTop'] ) )
                {
                    if ( isset( $options['rgbTop'] ) )
                    {
                        $options['rgbTop'] = eZMath::normalizeColorArray( $options['rgbTop'] );
                        $options['cmykTop'] = eZMath::rgbToCMYK2( $options['rgbTop'][0]/255,
                                                                  $options['rgbTop'][1]/255,
                                                                  $options['rgbTop'][2]/255 );
                    }
                    else
                    {
                        $options['cmykTop'] = eZMath::rgbToCMYK2( 0, 0, 0 );
                    }
                }

                if ( !isset( $options['cmykBottom'] ) )
                {
                    if ( isset( $options['rgbBottom'] ) )
                    {
                        $options['rgbBottom'] = eZMath::normalizeColorArray( $options['rgbBottom'] );
                        $options['cmykBottom'] = eZMath::rgbToCMYK2( $options['rgbBottom'][0]/255,
                                                                     $options['rgbBottom'][1]/255,
                                                                     $options['rgbBottom'][2]/255 );
                    }
                    else
                    {
                        $options['cmykBottom'] = eZMath::rgbToCMYK2( 0, 0, 0 );
                    }
                }

                if ( !isset( $options['pages'] ) )
                {
                    $options['pages'] = 'current';
                }

                $operatorValue = '<C:callFilledRectangle' .
                     ':pages:' . $options['pages'] .
                     ':x:' . $options['x'] .
                     ':y:' . $options['y'] .
                     ':width:' . $options['width'] .
                     ':height:' . $options['height'] .
                     ':cmykTop:' . implode( ',', $options['cmykTop'] ) .
                     ':cmykBottom:' . implode( ',', $options['cmykBottom'] ) .
                     '>';

                eZDebug::writeNotice( 'Added rectangle: ' . $operatorValue );
            } break;

            /* usage : pdf(text, <text>, array( 'font' => <fontname>, 'size' => <fontsize> )) */
            case 'text':
            {
                $text = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $operatorValue = '';
                $changeFont = false;

                if ( count( $operatorParameters ) >= 3)
                {
                    $textSettings = $tpl->elementValue( $operatorParameters[2], $rootNamespace, $currentNamespace );

                    if ( isset( $textSettings ) )
                    {
                        $operatorValue .= '<ezCall:callText';
                        $changeFont = true;

                        if ( isset( $textSettings['font'] ) )
                        {
                            $operatorValue .= ':font:'. $textSettings['font'];
                        }

                        if ( isset( $textSettings['size'] ) )
                        {
                            $operatorValue .= ':size:'. $textSettings['size'];
                        }

                        if ( isset( $textSettings['align'] ) )
                        {
                            $operatorValue .= ':justification:'. $textSettings['align'];
                        }

                        if ( isset( $textSettings['rgb'] ) )
                        {
                            $textSettings['cmyk'] = eZMath::rgbToCMYK2( $textSettings['rgb'][0]/255,
                                                                        $textSettings['rgb'][1]/255,
                                                                        $textSettings['rgb'][2]/255 );
                        }

                        if ( isset( $textSettings['cmyk'] ) )
                        {
                            $operatorValue .= ':cmyk:' . implode( ',', $textSettings['cmyk'] );
                        }

                        $operatorValue .= '>';
                    }
                }

                $operatorValue .= $text;
                if ( $changeFont )
                {
                    $operatorValue .= '</ezCall:callText>';
                }

            } break;

            case 'text_box':
            {
                $parameters = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $operatorValue = '<ezGroup:callTextBox';

                foreach( array_keys( $parameters ) as $key )
                {
                    if ( $key != 'text' )
                    {
                        $operatorValue .= ':' . $key . ':' . urlencode( $parameters[$key] );
                    }
                }

                $parameters['text'] = eZPDF::toOutputCharset( $parameters['text'] );

                $operatorValue .= '>';
                $operatorValue .= urlencode( $parameters['text'] );
                $operatorValue .= '</ezGroup:callTextBox>';

                return $operatorValue;
            } break;

            case 'text_frame':
            {
                $text = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $operatorValue = '';
                $changeFont = false;

                if ( count( $operatorParameters ) >= 3)
                {
                    $textSettings = $tpl->elementValue( $operatorParameters[2], $rootNamespace, $currentNamespace );

                    if ( isset( $textSettings ) )
                    {
                        $operatorValue .= '<ezGroup:callTextFrame';
                        $changeFont = true;

                        foreach ( array_keys( $textSettings ) as $key ) //settings, padding (left, right, top, bottom), textcmyk, framecmyk
                        {
                            if ( $key == 'frameCMYK' )
                            {
                                $operatorValue .= ':frameCMYK:' . implode( ',', $textSettings['frameCMYK'] );
                            }
                            else if ( $key == 'frameRGB' )
                            {
                                $operatorValue .= ':frameCMYK:' . implode( ',', eZMath::rgbToCMYK2( $textSettings['frameRGB'][0]/255,
                                                                                                    $textSettings['frameRGB'][1]/255,
                                                                                                    $textSettings['frameRGB'][2]/255 ) );
                            }
                            else if ( $key == 'textCMYK' )
                            {
                                $operatorValue .= ':textCMYK:' . implode( ',', $textSettings['textCMYK'] );
                            }
                            else if ( $key == 'textRGB' )
                            {
                                $operatorValue .= ':textCMYK:' . implode( ',', eZMath::rgbToCMYK2( $textSettings['textRGB'][0]/255,
                                                                                                   $textSettings['textRGB'][1]/255,
                                                                                                   $textSettings['textRGB'][2]/255 ) );
                            }
                            else
                            {
                                $operatorValue .= ':' . $key . ':' . $textSettings[$key];
                            }
                        }

                        $text = eZPDF::toOutputCharset( $text );

                        $operatorValue .= '>' . urlencode( $text ) . '</ezGroup::callTextFrame>';

                    }
                }

                eZDebug::writeNotice( 'Added TextFrame: ' . $operatorValue );
            } break;

            default:
            {
                eZDebug::writeError( 'PDF operation "'. $namedParameters['operation'] .'" undefined', __METHOD__ );
            }

        }

    }

    /**
     * Whether the configured font can hold the whole of unicode.
     *
     * A truetype file can, because it is embedded as a composite font; the
     * bundled afm faces cannot, because they are simple fonts with 256 slots.
     *
     * @return bool
     */
    public static function isUnicodeFont()
    {
        $ini = eZINI::instance( 'pdf.ini' );
        $font = $ini->hasVariable( 'PDFGeneral', 'Font' )
              ? trim( (string)$ini->variable( 'PDFGeneral', 'Font' ) ) : '';

        return substr( strtolower( $font ), -4 ) === '.ttf';
    }

    /**
     * Converts text from the site's charset to the one the pdf is written in.
     *
     * Was six copies of the same four lines, each defaulting to iso-8859-1.
     * That default was the wrong one: the fonts are emitted with
     * /Encoding /WinAnsiEncoding, and WinAnsi and iso-8859-1 disagree over
     * 0x80-0x9F - which is where the em dash, the euro sign, the bullet and
     * the curly quotes live. The font could draw them; the conversion threw
     * them away first and wrote a question mark. windows-1252 is what
     * WinAnsiEncoding actually is, so that is the default now.
     *
     * Transliteration, on unless it is turned off, degrades whatever is still
     * unmappable to its closest ascii rather than to a question mark, so a
     * Polish or Czech name comes out readable instead of eaten.
     *
     * A multi byte charset is refused. The fonts this library writes are
     * simple fonts with a 256 entry encoding, so utf-8 text would be drawn one
     * byte at a time and read as mojibake - cafÃ© rather than café. Rendering
     * utf-8 properly needs a composite font with an embedded truetype face,
     * which this library does not produce.
     *
     * @param string $text
     * @return string
     */
    public static function toOutputCharset( $text )
    {
        $ini = eZINI::instance( 'pdf.ini' );

        // A truetype font is embedded as a composite font, which addresses its
        // glyphs by number and so has room for the whole of unicode. There is
        // nothing to convert to and nothing to lose: the text goes through as
        // the utf-8 it already is.
        if ( self::isUnicodeFont() )
            return $text;

        $charset = $ini->hasVariable( 'PDFGeneral', 'OutputCharset' )
                 ? trim( (string)$ini->variable( 'PDFGeneral', 'OutputCharset' ) ) : '';
        if ( $charset === '' )
            $charset = 'windows-1252';

        $normalised = strtolower( str_replace( '_', '-', $charset ) );
        if ( in_array( $normalised, array( 'utf-8', 'utf8', 'utf-16', 'utf-16le', 'utf-16be', 'utf-32' ), true ) )
        {
            eZDebug::writeError( "pdf.ini [PDFGeneral] OutputCharset is '$charset'. The fonts written by this library "
                               . 'use a 256 entry encoding, so multi byte text is drawn one byte at a time and comes '
                               . 'out unreadable. Falling back to windows-1252.', __METHOD__ );
            $charset = 'windows-1252';
        }

        $internal = eZTextCodec::internalCharset();
        if ( strcasecmp( $internal, $charset ) === 0 )
            return $text;

        $transliterate = !$ini->hasVariable( 'PDFGeneral', 'Transliterate' )
                      || $ini->variable( 'PDFGeneral', 'Transliterate' ) !== 'disabled';

        if ( $transliterate && function_exists( 'iconv' ) )
        {
            $converted = @iconv( $internal, $charset . '//TRANSLIT//IGNORE', (string)$text );
            if ( $converted !== false )
                return $converted;
        }

        $codec = eZTextCodec::instance( $internal, $charset );
        return $codec ? $codec->convertString( $text ) : $text;
    }

    /*
     \private
     Create PDF object
    */
    function createPDF( $paper = 'a4', $orientation = 'portrait' )
    {
        $ini = eZINI::instance( 'pdf.ini' );

        $font = $ini->hasVariable( 'PDFGeneral', 'Font' )
              ? trim( (string)$ini->variable( 'PDFGeneral', 'Font' ) ) : '';
        if ( $font === '' )
            $font = 'lib/ezpdf/classes/fonts/Helvetica';

        // Which of the font's 256 slots hold which glyph. It has to agree with
        // OutputCharset or the accents land on the wrong characters:
        // WinAnsiEncoding is windows-1252, MacRomanEncoding is mac-roman, and
        // the fonts' own StandardEncoding is neither.
        $encoding = $ini->hasVariable( 'PDFGeneral', 'FontEncoding' )
                  ? trim( (string)$ini->variable( 'PDFGeneral', 'FontEncoding' ) ) : '';
        if ( $encoding === '' )
            $encoding = 'WinAnsiEncoding';

        $this->PDF = new eZPDFTable( $paper, $orientation );

        if ( self::isUnicodeFont() )
        {
            // A composite font has no 256 slot table for an encoding to
            // describe, so none is passed: the text carries glyph numbers.
            $this->PDF->selectFont( $font );
            eZDebug::writeNotice( 'PDF: File created using ' . $font . ' as a unicode font' );
        }
        else
        {
            $this->PDF->selectFont( $font, array( 'encoding' => $encoding ) );
            eZDebug::writeNotice( 'PDF: File created using ' . $font . ' with ' . $encoding );
        }
    }

    /// The array of operators, used for registering operators
    public $Operators;
    public $PDF;
    public $Config;
}


?>
