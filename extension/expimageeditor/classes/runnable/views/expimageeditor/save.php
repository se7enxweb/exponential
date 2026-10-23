<?php
/**
 * The code of extension/expimageeditor/modules/expimageeditor/save.php.
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expimageeditor
 */

namespace Exponential\View\Extension\Expimageeditor\Expimageeditor
{

/**
 * expimageeditor/save/<attribute id>/<version>: takes the edited image (POST, field "image", with the form token in
 * the X-CSRF-Token header or the ezxform_token field), stores it as the image of the draft's attribute and answers
 * with JSON: { "html": the attribute's edit template rendered anew } or { "error": a translated message } with the
 * status of the refusal. Nothing is stored unless every check of expImageEditorStore passes.
 */
class Save extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = isset( $scope['Params'] ) ? $scope['Params'] : array();
        $attributeId = isset( $Params['attribute_id'] ) ? (string)$Params['attribute_id'] : '';
        $version = isset( $Params['version'] ) ? (string)$Params['version'] : '';

        $upload = null;
        $refusal = null;
        try
        {
            if ( !isset( $_SERVER['REQUEST_METHOD'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' )
                throw new \expImageEditorException( 405, 'The image must be sent with POST.' );
            // a page of another site cannot send this header without CORS; its value is the form token of the session
            $token = isset( $_SERVER['HTTP_X_CSRF_TOKEN'] ) ? (string)$_SERVER['HTTP_X_CSRF_TOKEN'] : '';
            if ( !self::tokenMatches( $token ) )
                throw new \expImageEditorException( 403, 'The request did not come from the edit form.' );
            if ( !ctype_digit( $attributeId ) || !ctype_digit( $version ) )
                throw new \expImageEditorException( 400, 'The image does not exist.' );

            $attribute = \expImageEditorStore::editableAttribute( (int)$attributeId, (int)$version );
            $file = isset( $_FILES[\expImageEditorStore::FILE_FIELD] ) ? $_FILES[\expImageEditorStore::FILE_FIELD] : null;
            $upload = \expImageEditorStore::checkUpload( $file, $attribute );
            \expImageEditorStore::store( $attribute, $upload );
        }
        catch ( \expImageEditorException $e )
        {
            \eZDebug::writeWarning( 'expimageeditor/save: ' . $e->status() . ' ' . $e->getMessage(), __METHOD__ );
            $refusal = $e;
        }
        finally
        {
            // the re-encoded copy, stored or not
            \expImageEditorStore::discard( $upload );
        }
        // answered outside the try: eZExecution::cleanExit() ends a Velocity request with an exception of its own
        if ( $refusal !== null )
            self::send( $refusal->status(), array( 'error' => \ezpI18n::tr( 'extension/expimageeditor', $refusal->getMessage() ) ) );

        // the attribute as it is now stored, drawn by the same template as the edit form
        $attribute = \eZContentObjectAttribute::fetch( (int)$attributeId, (int)$version );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'attribute', $attribute );
        $tpl->setVariable( 'expimageeditor_response', true );
        self::send( 200, array( 'html' => self::withoutStyleAttributes( $tpl->fetch( 'design:content/datatype/edit/ezimage.tpl' ) ) ) );

        return $this->viewResult( null, null );
    }

    /**
     * Whether $token, the X-CSRF-Token header, is the session's form token. With ezformtoken active its value is
     * compared (ezformtoken's own input check compares it too, before the view runs); without it, a header that a page
     * of another site cannot send without CORS is all there is, and any value that is not empty is taken.
     *
     * @param string $token
     * @return bool
     */
    public static function tokenMatches( $token )
    {
        if ( !is_string( $token ) || $token === '' )
            return false;
        if ( class_exists( 'ezxFormToken' ) && in_array( 'ezformtoken', \eZExtension::activeExtensions(), true ) && \ezxFormToken::isEnabled() )
            return hash_equals( (string)\ezxFormToken::getToken(), $token );
        return true;
    }

    /**
     * The fragment without style="" attributes. The standard image view writes style="border: ..." (an entry of the
     * kernel's CSP allowlist); an enforced style-src 'self' drops such an attribute, and the browser reports it as soon
     * as the editor parses the fragment. The values are washed by the templates, so a double-quoted value ends at the
     * next double quote.
     *
     * @param string $html
     * @return string
     */
    public static function withoutStyleAttributes( $html )
    {
        return preg_replace( '/\s+style\s*=\s*(?:"[^"]*"|\'[^\']*\')/i', '', (string)$html );
    }

    /**
     * Ends the request with a JSON document.
     *
     * @param int $status
     * @param array $data
     */
    private static function send( $status, array $data )
    {
        if ( $status !== 200 && function_exists( 'http_response_code' ) )
            http_response_code( $status );
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Cache-Control: no-store' );
        echo json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP );
        \eZExecution::cleanExit();
    }
}

}
