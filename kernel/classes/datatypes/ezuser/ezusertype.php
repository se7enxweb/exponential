<?php
/**
 * File containing the eZUserType class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZUserType ezusertype.php
  \brief The class eZUserType handles user accounts and association with content objects
  \ingroup eZDatatype

*/

class eZUserType extends eZDataType
{
    const DATA_TYPE_STRING = "ezuser";

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "User account", 'Datatype name' ),
                           array( 'translation_allowed' => false,
                                  'serialize_supported' => true ) );
    }

    /*!
     Delete stored object attribute
    */
    function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
        $db = eZDB::instance();
        $userID = (int)$contentObjectAttribute->attribute( "contentobject_id" );

        $res = $db->arrayQuery( "SELECT COUNT(*) AS version_count FROM ezcontentobject_version WHERE contentobject_id = $userID" );
        $versionCount = isset( $res[0]['version_count'] ) ? (int)$res[0]['version_count'] : 0;

        if ( ( $version == null || $versionCount <= 1 )
                && eZUser::fetch( $userID ) !== null )
        {
            eZUser::removeUser( $userID );
            $db->query( "DELETE FROM ezuser_role WHERE contentobject_id = '$userID'" );
        }
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_user_login_" . $contentObjectAttribute->attribute( "id" ) ) &&
             $http->hasPostVariable( $base . "_data_user_email_" . $contentObjectAttribute->attribute( "id" ) ) &&
             $http->hasPostVariable( $base . "_data_user_password_" . $contentObjectAttribute->attribute( "id" ) ) &&
             $http->hasPostVariable( $base . "_data_user_password_confirm_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $classAttribute = $contentObjectAttribute->contentClassAttribute();
            // A field posted as an array (name[]=) is taken as empty; strip_tags(),
            // trim() and strtolower() stopped the request with a TypeError on it
            $loginName = strip_tags( self::postedString( $http, $base . "_data_user_login_" . $contentObjectAttribute->attribute( "id" ) ) );
            $email = self::postedString( $http, $base . "_data_user_email_" . $contentObjectAttribute->attribute( "id" ) );
            $password = self::postedString( $http, $base . "_data_user_password_" . $contentObjectAttribute->attribute( "id" ) );
            $passwordConfirm = self::postedString( $http, $base . "_data_user_password_confirm_" . $contentObjectAttribute->attribute( "id" ) );
            if ( trim( $loginName ) == '' )
            {
                if ( $contentObjectAttribute->validateIsRequired() || trim( $email ) != '' )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'The username must be specified.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            else
            {
                $existUser = eZUser::fetchByName( $loginName );
                if ( $existUser != null )
                {
                    $userID = $existUser->attribute( 'contentobject_id' );
                    if ( $userID !=  $contentObjectAttribute->attribute( "contentobject_id" ) )
                    {
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                             'The username already exists, please choose another one.' ) );
                        return eZInputValidator::STATE_INVALID;
                    }
                }
                // validate user email. eZMail::validate() anchors with $, which also
                // matches before a final line break; the address goes into the
                // headers of the registration and password mails, so no control
                // character is accepted.
                $isValidate = !preg_match( '/[\x00-\x1F\x7F]/', $email ) && eZMail::validate( $email );
                if ( !$isValidate )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'The email address is not valid.' ) );
                    return eZInputValidator::STATE_INVALID;
                }

                $authenticationMatch = eZUser::authenticationMatch();
                if ( $authenticationMatch & eZUser::AUTHENTICATE_EMAIL )
                {
                    if ( eZUser::requireUniqueEmail() )
                    {
                        $userByEmail = eZUser::fetchByEmail( $email );
                        if ( $userByEmail != null )
                        {
                            $userID = $userByEmail->attribute( 'contentobject_id' );
                            if ( $userID !=  $contentObjectAttribute->attribute( "contentobject_id" ) )
                            {
                                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                                     'A user with this email already exists.' ) );
                                return eZInputValidator::STATE_INVALID;
                            }
                        }
                    }
                }
                // validate user name
                if ( !eZUser::validateLoginName( $loginName, $errorText ) )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         $errorText ) );
                    return eZInputValidator::STATE_INVALID;
                }
                // validate user password
                $ini = eZINI::instance();
                $generatePasswordIfEmpty = $ini->variable( "UserSettings", "GeneratePasswordIfEmpty" ) == 'true';
                if ( !$generatePasswordIfEmpty || ( $password != "" ) )
                {
                    if ( $password == "" )
                    {
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                             'The password cannot be empty.',
                                                                             'eZUserType' ) );
                        return eZInputValidator::STATE_INVALID;
                    }
                    if ( $password !== $passwordConfirm )
                    {
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                             'The passwords do not match.',
                                                                             'eZUserType' ) );
                        return eZInputValidator::STATE_INVALID;
                    }
                    if ( !eZUser::validatePassword( $password ) )
                    {
                        $minPasswordLength = $ini->variable( 'UserSettings', 'MinPasswordLength' );
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                             'The password must be at least %1 characters long.', null, array( $minPasswordLength ) ) );
                        return eZInputValidator::STATE_INVALID;
                    }
                    if ( strtolower( $password ) == 'password' )
                    {
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                             'The password must not be "password".' ) );
                        return eZInputValidator::STATE_INVALID;
                    }
                }

                // validate confirm email
                if ( $ini->variable( 'UserSettings', 'RequireConfirmEmail' ) == 'true' )
                {
                    // A form without the field compares against '' (a mismatch)
                    // instead of logging an undefined post variable error
                    $emailConfirm = self::postedString( $http, $base . "_data_user_email_confirm_" . $contentObjectAttribute->attribute( "id" ) );
                    if ( $email != $emailConfirm )
                    {
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                             'The emails do not match.',
                                                                             'eZUserType' ) );
                        return eZInputValidator::STATE_INVALID;
                    }
                }
            }
        }
        else if ( $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Input required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_user_login_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $login = strip_tags( self::postedString( $http, $base . "_data_user_login_" . $contentObjectAttribute->attribute( "id" ) ) );
            $email = self::postedString( $http, $base . "_data_user_email_" . $contentObjectAttribute->attribute( "id" ) );
            $password = self::postedString( $http, $base . "_data_user_password_" . $contentObjectAttribute->attribute( "id" ) );
            $passwordConfirm = self::postedString( $http, $base . "_data_user_password_confirm_" . $contentObjectAttribute->attribute( "id" ) );

            $contentObjectID = $contentObjectAttribute->attribute( "contentobject_id" );

            $user = $contentObjectAttribute->content();
            if ( !$user instanceof eZUser )
            {
                $user = eZUser::create( $contentObjectID );
            }

            $ini = eZINI::instance();
            $generatePasswordIfEmpty = $ini->variable( "UserSettings", "GeneratePasswordIfEmpty" );
            if ( $password == "" )
            {
                if ( $generatePasswordIfEmpty == 'true' )
                {
                    $passwordLength = $ini->variable( "UserSettings", "GeneratePasswordLength" );
                    $password = $user->createPassword( $passwordLength );
                    $passwordConfirm = $password;
                    $http->setSessionVariable( "GeneratedPassword", $password );
                }
                else
                {
                    $password = null;
                }
            }

            // The passwords themselves are never written to the debug output,
            // which can end up in a log file or the page
            eZDebugSetting::writeDebug( 'kernel-user', ( $password === null || $password === '' ) ? 'empty' : 'given', "password" );
            eZDebugSetting::writeDebug( 'kernel-user', $login, "login" );
            eZDebugSetting::writeDebug( 'kernel-user', $email, "email" );
            eZDebugSetting::writeDebug( 'kernel-user', $contentObjectID, "contentObjectID" );
            // Only a generated password is kept in the session (above), for the
            // registration to tell the user. A password the user typed was put
            // there in plain text on every save as well, where nothing reads it.
            if ( $password == "_ezpassword" )
            {
                $password = false;
                $passwordConfirm = false;
            }

            eZDebugSetting::writeDebug( 'kernel-user', "setInformation run", "ezusertype" );
            $user->setInformation( $contentObjectID, $login, $email, $password, $passwordConfirm );
            $contentObjectAttribute->setContent( $user );
            return true;
        }
        return false;
    }

    function storeObjectAttribute( $contentObjectAttribute )
    {
        /** @var eZContentObjectAttribute $contentObjectAttribute */
        /** @var eZUser $user */
        $user = $contentObjectAttribute->content();
        if ( !( $user instanceof eZUser ) )
        {
            // create a default user account
            $user = eZUser::create( $contentObjectAttribute->attribute( "contentobject_id" ) );
            $userID = $contentObjectAttribute->attribute( "contentobject_id" );
            $isEnabled = 1;
            $userSetting = eZUserSetting::create( $userID, $isEnabled );
            $userSetting->store();

            $user->store();
            $contentObjectAttribute->setContent( $user );
        }
        else
        {
            // No "draft" for version 1 to avoid regression for existing code creating new users.
            if ( $contentObjectAttribute->attribute( 'version' ) == '1' )
            {
                $user->store();
                $contentObjectAttribute->setContent( $user );
            }

            // saving information in the object attribute data_text field to simulate a draft
            // only if the object version is a draft
            $objectVersion = $contentObjectAttribute->attribute( 'object_version' );
            if (
                $user->Login &&
                $objectVersion instanceof eZContentObjectVersion &&
                $objectVersion->attribute( 'status' ) == eZContentObjectVersion::STATUS_DRAFT
            )
            {
                $contentObjectAttribute->setAttribute( 'data_text', $this->serializeDraft( $user ) );
            }
        }
    }

    /**
     * @param $contentObjectAttribute
     * @param eZContentObject $contentObject
     * @param $publishedNodes
     */
    function onPublish( $contentObjectAttribute, $contentObject, $publishedNodes )
    {
        /** @var eZContentObjectAttribute $contentObjectAttribute */
        /** @var eZUser $user */
        $user = $contentObjectAttribute->content();

        // Publishing draft's content
        $serializedDraft = $contentObjectAttribute->attribute( 'data_text' );

        if ( !empty( $serializedDraft ) )
        {
            // The draft belongs to an account; without one (content() found no
            // ezuser row) the typed updateUserDraft() raised a TypeError
            if ( !$user instanceof eZUser )
            {
                $user = eZUser::create( $contentObjectAttribute->attribute( 'contentobject_id' ) );
            }
            $user = $this->updateUserDraft( $user, $serializedDraft );
            $user->store();
            $contentObjectAttribute->setContent( $user );

            // Clear draft info
            $contentObjectAttribute->setAttribute( 'data_text', '' );
            $contentObjectAttribute->store();
        }
    }

    /**
     * Generates a serialized draft of the ezuser content
     *
     * @param eZUser $user
     * @return string
     */
    public static function serializeDraft( eZUser $user )
    {
        return json_encode(
            array(
                 'login' => $user->attribute( 'login' ),
                 'password_hash' => $user->attribute( 'password_hash' ),
                 'email' => $user->attribute( 'email' ),
                 'password_hash_type' => $user->attribute( 'password_hash_type' )
            )
        );
    }

    /**
     * Unserialize draft data generated with serializeDraft()
     *
     * @param $serializedDraft
     * @return mixed
     */
    private function unserializeDraft( $serializedDraft )
    {
        // Only what serializeDraft() writes is a draft: a JSON object with the
        // four fields as strings or numbers. Anything else in data_text (invalid
        // JSON, a number, a list, an object without the fields) is not applied,
        // where it raised warnings or a fatal error on the property access.
        if ( !is_string( $serializedDraft ) || $serializedDraft === '' )
            return null;
        $draft = json_decode( $serializedDraft );
        if ( !$draft instanceof stdClass )
            return null;
        foreach ( array( 'login', 'password_hash', 'email', 'password_hash_type' ) as $field )
        {
            if ( !property_exists( $draft, $field ) ||
                 ( $draft->$field !== null && !is_scalar( $draft->$field ) ) )
                return null;
        }
        return $draft;
    }

    /**
     * Updates ezuser with data generated with getSerializedDraft()
     *
     * @param eZUser $user
     * @param $serializedDraft
     * @return eZUser updated user
     */
    private function updateUserDraft( eZUser $user, $serializedDraft )
    {
        $draft = $this->unserializeDraft( $serializedDraft );

        if ( $draft )
        {
            $user->setAttribute( 'login', $draft->login );
            $user->setAttribute( 'password_hash', $draft->password_hash );
            $user->setAttribute( 'email', $draft->email );
            $user->setAttribute( 'password_hash_type', $draft->password_hash_type );
        }

        return $user;
    }

    /*!
     Returns the object title.
    */
    function title( $contentObjectAttribute, $name = "login" )
    {
        $user = $this->objectAttributeContent( $contentObjectAttribute );
        // An object of a user class without an account yet has no title
        if ( !$user instanceof eZUser )
            return '';

        $value = $user->attribute( $name );

        return $value;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $user = $this->objectAttributeContent( $contentObjectAttribute );
        if ( is_object( $user ) and
             $user->isEnabled() )
            return true;
        return false;
    }

    /*!
     Returns the user object.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $userID = $contentObjectAttribute->attribute( "contentobject_id" );
        if ( empty( $GLOBALS['eZUserObject_' . $userID] ) )
        {
            $GLOBALS['eZUserObject_' . $userID] = eZUser::fetch( $userID );
        }

        $user = eZUser::fetch( $userID );
        eZDebugSetting::writeDebug( 'kernel-user', $user, 'user' );

        //Looking for a "draft" and loading its content
        $serializedDraft = $contentObjectAttribute->attribute( 'data_text' );

        // Without an ezuser row there is no account to apply the draft to;
        // the typed updateUserDraft() raised a TypeError on null
        if ( !empty( $serializedDraft ) && $user instanceof eZUser )
        {
            $user = $this->updateUserDraft( $user, $serializedDraft );
        }

        return $user;
    }

    function isIndexable()
    {
        return true;
    }

    /*!
     We can only remove the user attribute if:
     - The current user, anonymous user and administrator user is not using this class
     - There are more classes with the ezuser datatype
    */
    function classAttributeRemovableInformation( $contentClassAttribute, $includeAll = true )
    {
        $result  = array( 'text' => ezpI18n::tr( 'kernel/classes/datatypes',
                                            "Cannot remove the account:" ),
                          'list' => array() );
        $currentUser = eZUser::currentUser();
        $userObject  = $currentUser->attribute( 'contentobject' );
        $currentClassID = $userObject instanceof eZContentObject ? $userObject->attribute( 'contentclass_id' ) : null;
        $ini         = eZINI::instance();
        $anonID      = (int)$ini->variable( 'UserSettings', 'AnonymousUserID' );
        $classID     = (int)$contentClassAttribute->attribute( 'contentclass_id' );
        $db          = eZDB::instance();

        if ( $currentClassID !== null && $classID == $currentClassID )
        {
            $result['list'][] = array( 'text' => ezpI18n::tr( 'kernel/classes/datatypes',
                                                         "The account owner is currently logged in." ) );
            if ( !$includeAll )
                return $result;
        }

        $sql = "SELECT id FROM ezcontentobject WHERE id = $anonID AND contentclass_id = $classID";
        $rows = $db->arrayQuery( $sql );
        if ( count( $rows ) > 0 )
        {
            $result['list'][] = array( 'text' => ezpI18n::tr( 'kernel/classes/datatypes',
                                                         "The account is currently used by the anonymous user." ) );
            if ( !$includeAll )
                return $result;
        }

        $sql = "SELECT ezco.id FROM ezcontentobject ezco, ezuser
 WHERE ezco.contentclass_id = $classID AND
       ezuser.login = 'admin' AND
       ezco.id = ezuser.contentobject_id ";
        $rows = $db->arrayQuery( $sql );
        if ( count( $rows ) > 0 )
        {
            $result['list'][] = array( 'text' => ezpI18n::tr( 'kernel/classes/datatypes',
                                                         "The account is currently used the administrator user." ) );
            if ( !$includeAll )
                return $result;
        }

        $sql = "SELECT count( ezcc.id ) AS count FROM ezcontentclass ezcc, ezcontentclass_attribute ezcca
 WHERE ezcc.id != $classID AND
       ezcca.data_type_string = 'ezuser' AND
       ezcc.id = ezcca.contentclass_id ";
        $rows = $db->arrayQuery( $sql );
        if ( empty( $rows[0]['count'] ) )
        {
            $result['list'][] = array( 'text' => ezpI18n::tr( 'kernel/classes/datatypes',
                                                         "You cannot remove the last class holding user accounts." ) );
            if ( !$includeAll )
                return $result;
        }

        return $result;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        $metaString = "";
        $user = $contentObjectAttribute->content();

        if ( $user instanceof eZUser )
        {
            // create a default user account
            $metaString .= $user->attribute( 'login' ) . " ";
            $metaString .= $user->attribute( 'email' ) . " ";
        }
        return $metaString;
    }

    /**
     * Returns the string representation of the attribute
     * passed in $contentObjectAttribute.
     *
     * The string definition will looks like this :
     * login|email|password_has|hash_identifier|is_enabled where :
     *
     * - login => user login cf : the login field in the ezuser table.
     *
     * - email => user email cf : the  email table field in the
     *   ezuser table.
     *
     * - password_hash => use password hash, cf password_hash field in the
     *   ezuser table.
     *
     * - hash_identifier => one of the hash name available in
     *   {@link eZUser::passwordHashTypeName()}
     *
     * - is_enabled => whether the user is enabled or not, cf the is_enabled
     *   field in the ezuser_setting table.
     *
     * Example:
     * <code>
     * foo|foo@ez.no|1234|md5_password|0
     * </code>
     *
     * @uses eZUser::isEnabled()
     * @param object $contentObjectAttribute A contentobject attribute of type user_account.
     * @return string The string definition.
     */
    function toString( $contentObjectAttribute )
    {
        $userID = $contentObjectAttribute->attribute( "contentobject_id" );
        if ( empty( $GLOBALS['eZUserObject_' . $userID] ) )
        {
            $GLOBALS['eZUserObject_' . $userID] = eZUser::fetch( $userID );
        }
        $user = $GLOBALS['eZUserObject_' . $userID];
        // An object of a user class without an account exports as empty, which
        // fromString() takes as "nothing to import"
        if ( !$user instanceof eZUser )
            return '';

        $userInfo = array(
            $user->attribute( 'login' ),
            $user->attribute( 'email' ),
            $user->attribute( 'password_hash' ),
            eZUser::passwordHashTypeName( $user->attribute( 'password_hash_type' ) ),
            (int)$user->isEnabled()
        );

        return implode( '|', $userInfo );
    }

    /**
     * Populates the user_account datatype with the correct values
     * based upon the string passed in $string.
     *
     * The string that must be passed looks like the following :
     * login|email|password_hash|hash_identifier|is_enabled
     *
     * Example:
     * <code>
     * foo|foo@ez.no|1234|md5_password|0
     * </code>
     *
     * @param object $contentObjectAttribute A contentobject attribute of type user_account.
     * @param string $string The string as described in the example.
     * @return object The newly created eZUser object
     */
    function fromString( $contentObjectAttribute, $string )
    {
        if ( $string == '' )
            return true;
        $userData = self::parseStringRepresentation( $string );
        if ( $userData === false )
            return false;
        $login = $userData[0];
        $email = $userData[1];

        $userByUsername = eZUser::fetchByName( $login );
        if( $userByUsername && $userByUsername->attribute( 'contentobject_id' ) != $contentObjectAttribute->attribute( 'contentobject_id' ) )
            return false;

        if( eZUser::requireUniqueEmail() )
        {
            $userByEmail = eZUser::fetchByEmail( $email );
            if( $userByEmail && $userByEmail->attribute( 'contentobject_id' ) != $contentObjectAttribute->attribute( 'contentobject_id' ) )
                return false;
        }

        $user = eZUser::create( $contentObjectAttribute->attribute( 'contentobject_id' ) );

        $user->setAttribute( 'login', $login );
        $user->setAttribute( 'email', $email );
        if ( isset( $userData[2] ) )
            $user->setAttribute( 'password_hash', $userData[2] );

        if ( isset( $userData[3] ) )
            $user->setAttribute( 'password_hash_type', eZUser::passwordHashTypeID( $userData[3] ) );

        if( isset( $userData[4] ) )
        {
            // A user imported for the first time has no setting row yet;
            // ->setAttribute() on the null from fetch() was a fatal error
            self::storeIsEnabled( $contentObjectAttribute->attribute( 'contentobject_id' ), (int)(bool)$userData[4] );
        }

        $user->store();
        return $user;
    }

    /*!
     \param package
     \param content attribute

     \return a DOM representation of the content object attribute
    */
    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );
        $userID = $objectAttribute->attribute( "contentobject_id" );
        $user = eZUser::fetch( $userID );
        if ( is_object( $user ) )
        {
            $userNode = $node->ownerDocument->createElement( 'account' );
            $userNode->setAttribute( 'login', $user->attribute( 'login' ) );
            $userNode->setAttribute( 'email', $user->attribute( 'email' ) );
            $userNode->setAttribute( 'password_hash', $user->attribute( 'password_hash' ) );
            $userNode->setAttribute( 'password_hash_type', eZUser::passwordHashTypeName( $user->attribute( 'password_hash_type' ) ) );
            $userNode->setAttribute( 'is_enabled', (int)$user->isEnabled() );
            $node->appendChild( $userNode );
        }

        return $node;
    }

    /*!
     \param package
     \param contentobject attribute object
     \param ezdomnode object
    */
    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $userNode = $attributeNode->getElementsByTagName( 'account' )->item( 0 );
        if ( is_object( $userNode ) )
        {
            $userID = $objectAttribute->attribute( 'contentobject_id' );
            $user = eZUser::fetch( $userID );
            if ( !is_object( $user ) )
            {
                $user = eZUser::create( $userID );
            }
            $user->setAttribute( 'login', $userNode->getAttribute( 'login' ) );
            $user->setAttribute( 'email', $userNode->getAttribute( 'email' ) );
            $user->setAttribute( 'password_hash', $userNode->getAttribute( 'password_hash' ) );
            $user->setAttribute( 'password_hash_type', eZUser::passwordHashTypeID( $userNode->getAttribute( 'password_hash_type' ) ) );
            $user->store();
            // serializeContentObjectAttribute() writes is_enabled; an account
            // exported disabled was installed enabled because it was not read
            if ( $userNode->hasAttribute( 'is_enabled' ) )
            {
                self::storeIsEnabled( $userID, (int)(bool)$userNode->getAttribute( 'is_enabled' ) );
            }
        }
    }

    /*!
     \private
     \return the posted value of \a $name as a string: '' when it was not posted
     or was posted as an array (name[]=).
    */
    static function postedString( $http, $name )
    {
        if ( !$http->hasPostVariable( $name ) )
            return '';
        $value = $http->postVariable( $name );
        if ( is_array( $value ) || is_object( $value ) )
            return '';
        return (string)$value;
    }

    /*!
     \private
     Splits the toString() format login|email|password_hash|hash_type|is_enabled.
     \return the five fields (the last three only when given), or false when
     there is no login and email.

     An address may contain a | in its local part (eZMail::REGEXP allows it),
     and the old explode() then shifted the hash into the email and the hash
     type into the hash. The last three fields never contain one (a hash, a hash
     type name, 0/1), so with more than five fields the extra ones are the email's.
    */
    static function parseStringRepresentation( $string )
    {
        if ( !is_string( $string ) )
            return false;
        $userData = explode( '|', $string );
        if ( count( $userData ) < 2 )
            return false;
        if ( count( $userData ) > 5 )
        {
            $tail = array_slice( $userData, -3 );
            $email = implode( '|', array_slice( $userData, 1, count( $userData ) - 4 ) );
            $userData = array_merge( array( $userData[0], $email ), $tail );
        }
        return $userData;
    }

    /*!
     \private
     Stores the is_enabled flag of \a $userID, creating its setting row when
     there is none.
    */
    static function storeIsEnabled( $userID, $isEnabled )
    {
        $userSetting = eZUserSetting::fetch( $userID );
        if ( !$userSetting instanceof eZUserSetting )
        {
            $userSetting = eZUserSetting::create( $userID, $isEnabled );
        }
        $userSetting->setAttribute( "is_enabled", $isEnabled );
        $userSetting->store();
    }
}

eZDataType::register( eZUserType::DATA_TYPE_STRING, "eZUserType" );

?>
