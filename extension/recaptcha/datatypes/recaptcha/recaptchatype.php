<?php
/**
 * reCAPTCHA extension for eZ Publish
 * Written by Bruce Morrison <bruce@stuffandcontent.com>
 * Copyright (C) 2008. Bruce Morrison.  All rights reserved.
 * http://www.stuffandcontent.com
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

// Include the super class file
//include_once( "kernel/classes/ezdatatype.php" );
// Include reCAPTCHA lib
include_once( "extension/recaptcha/classes/recaptchalib.php" );

// Define the name of datatype string
//define( "EZ_DATATYPESTRING_RECAPTCHA", "recaptcha" );


class recaptchaType extends eZDataType
{
  const DATA_TYPE_STRING = 'recaptcha';
  /*!
   Construction of the class, note that the second parameter in eZDataType 
   is the actual name showed in the datatype dropdown list.
  */
  function __construct()
  {
   parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', 'reCAPTCHA', 'Datatype name' ),
                           array( 'serialize_supported' => false,
                                  'translation_allowed' => false ) );
  }

  /*!
    Validates the input and returns true if the input was
    valid for this datatype.
  */
  function validateObjectAttributeHTTPInput( $http, $base, 
                                               $objectAttribute )
  {
    $classAttribute = $objectAttribute->contentClassAttribute();

    $ini = eZINI::instance( 'recaptcha.ini' );
    $newOjbectsOnly = $ini->variable( 'PublishSettings', 'NewObjectsOnly' ) == 'true';

    if ( $newOjbectsOnly && $objectAttribute->attribute( 'object' )->attribute( 'status' ) )
       return eZInputValidator::STATE_ACCEPTED;

    if ( $classAttribute->attribute( 'is_information_collector' ) or $this->reCAPTCHAValidate($http) )
      return eZInputValidator::STATE_ACCEPTED;
    $objectAttribute->setValidationError('ReCaptcha - Input incorrect. Please try again.');
    return eZInputValidator::STATE_INVALID;
  }

  function validateCollectionAttributeHTTPInput( $http, $base, $objectAttribute )
  {
    if ($this->reCAPTCHAValidate($http))
      return eZInputValidator::STATE_ACCEPTED;
    $objectAttribute->setValidationError('ReCaptcha - Input incorrect. Please try again.');
    return eZInputValidator::STATE_INVALID;
  }

  function isIndexable()
  {
    return false;
  }

  function isInformationCollector()
  {
    return true;
  }

  function hasObjectAttributeContent( $contentObjectAttribute )
  {
    return false;
  }

  static function reCAPTCHAValidate( $http )
  {
    // check if the current user is able to bypass filling in the captcha and
    // return true without checking if so
    $currentUser = eZUser::currentUser();
    $accessAllowed = $currentUser->hasAccessTo( 'recaptcha', 'bypass_captcha' );
    if ($accessAllowed["accessWord"] == 'yes')
      return true;

    $ini = eZINI::instance( 'recaptcha.ini' );
    // If PrivateKey is an array try and find a match for the current host
    $privatekey = $ini->variable( 'Keys', 'PrivateKey' );
    if ( is_array($privatekey) )
    {
      $hostname = eZSys::hostname();
      if (isset($privatekey[$hostname]))
        $privatekey = $privatekey[$hostname];
      else
        // try our luck with the first entry
        $privatekey = array_shift($privatekey);
    }

    $recaptcha_challenge_field = $http->postVariable('recaptcha_challenge_field');
    $recaptcha_response_field = $http->postVariable('g-recaptcha-response');

    $recaptcha_response = recaptcha_check_answer( $privatekey, $_SERVER['REMOTE_ADDR'], $recaptcha_challenge_field, $_POST['g-recaptcha-response'] );

    return $recaptcha_response->is_valid;
  }

}
eZDataType::register( recaptchaType::DATA_TYPE_STRING, "recaptchaType" );
