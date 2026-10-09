<?php
/**
 * File containing the eZSOAPClient class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZSOAPClient ezsoapclient.php
  \ingroup eZSOAP
  \brief eZSOAPClient is a class which can be used as a SOAP client

  eZSOAPClient handles communication with a SOAP server.

  \code

// create a new client
$client = new eZSOAPClient( "nextgen.bf.dvh1.ez.no", "/sdk/ezsoap/view/server" );

$namespace = "http://soapinterop.org/";

// create the SOAP request object
$request = new eZSOAPRequest( "addNumbers", "http://calkulator.com/simplecalculator" );

// add parameters to the request
$request->addParameter( "valueA", 42 );
$request->addParameter( "valueB", 17 );

// send the request to the server and fetch the response
$response = $client->send( $request );

// check if the server returned a fault, if not print out the result
if ( $response->isFault() )
{
    print( "SOAP fault: " . $response->faultCode(). " - " . $response->faultString() . "" );
}
else
    print( "Returned SOAP value was: \"" . $response->value() . "\"" );
  \endcode

  \sa eZSOAPServer eZSOAPRequest eZSOAPResponse

*/

class eZSOAPClient
{
    public $errorNumber;
    public $errorString;
    /**
     * @var string
     */
    public $ErrorString;
    /**
     * Creates a new SOAP client.
     *
     * @param string $server The remote server to connect to
     * @param string $path The path to the SOAP service on the remote server
     * @param int $port The port to connect to, 80 by default. You can use 'ssl' as well to specify that you want
     *                  to use port 443 over SSL, but omit the last parameter $useSSL of this method then or set it
     *                  to true. When $port equals 443, SSL will also be used if $useSSL is omitted or set to true.
     * @param bool $useSSL If we need to connect to the remote server with (https://) or without (http://) SSL
     */
    public function __construct( $server, $path = '/', $port = 80, $useSSL = null )
    {
        $this->Login = "";
        $this->Password = "";
        $this->Server = $server;
        $this->Path = $path;
        $this->Port = $port;
        if ( is_numeric( $port ) )
        {
            $this->Port = $port;

            if ( $port == 443 )
            {
                $this->UseSSL = true;
            }
        }
        elseif ( strtolower( $port ) == 'ssl' )
        {
            $this->UseSSL = true;
            $this->Port = 443;
        }
        else
        {
            $this->Port = 80;
        }

        if ( $useSSL === true )
        {
            $this->UseSSL = true;
        }
        else if ( $useSSL === false )
        {
            $this->UseSSL = false;
        }
    }

    /*!
      Sends a SOAP message and returns the response object.
    */
    function send( $request )
    {
        // Over HTTPS only with cURL: without it the call used to go to the TLS port as plain text
        if ( $this->UseSSL && !function_exists( 'curl_init' ) )
        {
            $this->ErrorString = '<b>Error:</b> eZSOAPClient::send() : a call over HTTPS needs the PHP extension curl.';
            eZDebug::writeError( "No SOAP call to {$this->Server}: HTTPS needs the PHP extension curl, which is not loaded", __METHOD__ );
            return 0;
        }
        if ( !$this->UseSSL )
        {
            if ( $this->Timeout != 0 )
            {
                $fp = fsockopen( $this->Server,
                                 $this->Port,
                                 $this->errorNumber,
                                 $this->errorString,
                                 $this->Timeout );
            }
            else
            {
                $fp = fsockopen( $this->Server,
                                 $this->Port,
                                 $this->errorNumber,
                                 $this->errorString );
            }

            if ( $fp == 0 )
            {
                $this->ErrorString = '<b>Error:</b> eZSOAPClient::send() : Unable to open connection to ' . $this->Server . '.';
                return 0;
            }

            $payload = $request->payload();

            $authentification = "";
            if ( ( $this->login() != "" ) )
            {
                $authentification = "Authorization: Basic " . base64_encode( $this->login() . ":" . $this->password() ) . "\r\n" ;
            }

            $HTTPRequest = "POST " . $this->Path . " HTTP/1.0\r\n" .
                "User-Agent: eZ soap client\r\n" .
                "Host: " . $this->Server . ":" . $this->Port . "\r\n" .
                $authentification .
                "Content-Type: text/xml\r\n" .
                "SOAPAction: \"" . $request->ns() . '/' . $request->name() . "\"\r\n" .
                "Content-Length: " . strlen( $payload ) . "\r\n\r\n" .
                $payload;
            if ( !fputs( $fp, $HTTPRequest, strlen( $HTTPRequest ) ) )
            {
                $this->ErrorString = "<b>Error:</b> could not send the SOAP request. Could not write to the socket.";
                $response = 0;
                return $response;
            }

            $rawResponse = "";
            // fetch the SOAP response
            while ( $data = fread( $fp, 32768 ) )
            {
                $rawResponse .= $data;
            }

            // close the socket
            fclose( $fp );
        }
        else //SOAP With SSL
        {
            if ( $request instanceof eZSOAPRequest )
            {
                $URL = "https://" . $this->Server . ":" . $this->Port . $this->Path;
                $ch = curl_init ( $URL );
                if ( $this->Timeout != 0 )
                {
                    curl_setopt( $ch, CURLOPT_TIMEOUT, $this->Timeout );
                }
                $payload = $request->payload();

                if ( $ch != 0 )
                {
                    $headers = [
                        "User-Agent: eZ soap client",
                        "Host: " . $this->Server . ":" . $this->Port,
                        "Content-Type: text/xml",
                        "SOAPAction: \"" . $request->ns() . '/' . $request->name() . "\"",
                        "Content-Length: " . strlen( $payload ),
                    ];
                    if ( $this->login() != '' )
                    {
                        $headers[] = "Authorization: Basic " . base64_encode( $this->login() . ":" . $this->password() );
                    }
                    if ( !curl_setopt_array( $ch, $this->curlOptions( $URL, $payload, $headers ) ) )
                    {
                        $this->ErrorString = '<b>Error:</b> could not set the cURL options of the XML-SOAP with SSL call: ' . curl_error( $ch );
                        return 0;
                    }
                    unset( $rawResponse );

                    $rawResponse = curl_exec( $ch );

                    if ( $rawResponse === false )
                    {
                        $this->ErrorString = "<b>Error:</b> could not send the XML-SOAP with SSL call. Could not write to the socket. cURL failed: " . curl_error($ch) . " (errno " . curl_errno($ch) . ")";
                        if ( PHP_VERSION_ID < 80000 ) curl_close( $ch ); // no effect since PHP 8.0, deprecated in 8.5
                        $response = 0;
                        return $response;
                    }
                }

                if ( PHP_VERSION_ID < 80000 ) curl_close( $ch ); // no effect since PHP 8.0, deprecated in 8.5
            }
        }

	$response = new eZSOAPResponse();
        $response->decodeStream( $request, $rawResponse );

        return $response;
    }

    /**
     * The cURL options of a call over HTTPS to $URL with $payload and $headers. The server certificate is verified,
     * also against the CA file of setCAFile() when one is set; setVerifyPeer( false ) turns the check off for this
     * client and logs a warning on every call.
     *
     * @param string $URL
     * @param string $payload
     * @param string[] $headers
     * @return array
     */
    protected function curlOptions( $URL, $payload, array $headers )
    {
        $options = array( CURLOPT_URL => $URL,
                          CURLOPT_HEADER => 1,
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_POST => true,
                          CURLOPT_POSTFIELDS => $payload,
                          CURLOPT_HTTPHEADER => $headers,
                          CURLOPT_SSL_VERIFYPEER => $this->VerifyPeer,
                          CURLOPT_SSL_VERIFYHOST => $this->VerifyPeer ? 2 : 0 );
        if ( $this->CAFile !== null )
        {
            $options[CURLOPT_CAINFO] = $this->CAFile;
            // cURL reads the file past open_basedir, where is_readable() says false for a file it can read; it then
            // reports a missing file itself, in the error of the call
            if ( (string)ini_get( 'open_basedir' ) === '' && !is_readable( $this->CAFile ) )
            {
                eZDebug::writeError( "The CA file {$this->CAFile} for {$this->Server} cannot be read: the call fails the certificate check", __METHOD__ );
            }
        }
        if ( !$this->VerifyPeer )
        {
            eZDebug::writeWarning( "The certificate of {$this->Server} is not verified (eZSOAPClient::setVerifyPeer( false )): " .
                                   'anybody between this server and it can read and change the call. Set the CA file of its ' .
                                   'certificate with setCAFile() instead.', __METHOD__ );
        }
        return $options;
    }

    /**
     * Trusts the CA certificates in the file $path (PEM, an absolute path) for the certificate of an HTTPS server, for
     * a server whose certificate an internal CA issued. cURL uses them in place of its CA bundle file; a CA directory
     * compiled into cURL (such as /etc/ssl/certs) is still read. Anything that is not a non-empty string (null, false
     * from a missing setting, '') sets no CA file, nor does a name with a NUL byte (reported).
     *
     * @param string|null|false $path
     * @return void
     */
    function setCAFile( $path )
    {
        $this->CAFile = is_string( $path ) && trim( $path ) !== '' ? trim( $path ) : null;
        // No file name has a NUL byte, and cURL would throw a ValueError for it in send()
        if ( $this->CAFile !== null && strpos( $this->CAFile, "\0" ) !== false )
        {
            $this->CAFile = null;
            eZDebug::writeError( "The CA file name for {$this->Server} contains a NUL byte: no CA file is set, the certificate is checked against the CA bundle", __METHOD__ );
        }
    }

    /**
     * The CA file set with setCAFile(), or null.
     *
     * @return string|null
     */
    function caFile()
    {
        return $this->CAFile;
    }

    /**
     * Whether the certificate of an HTTPS server is verified (true by default). false turns the check off for this
     * client only, and every call logs a warning: a server whose certificate cannot be checked can then be anyone in
     * between. A CA file (setCAFile()) is the way to trust an internal certificate.
     *
     * @param bool $verify
     * @return void
     */
    function setVerifyPeer( $verify )
    {
        $this->VerifyPeer = (bool)$verify;
    }

    /**
     * Whether the certificate of an HTTPS server is verified.
     *
     * @return bool
     */
    function verifyPeer()
    {
        return $this->VerifyPeer;
    }

    /*!
     Set timeout value

     \param timeout value in seconds. Set to 0 for unlimited.
    */
    function setTimeout( $timeout )
    {
        $this->Timeout = $timeout;
    }

    /*!
     Sets the HTTP login
    */
    function setLogin( $login  )
    {
        $this->Login = $login;
    }

    /*!
      Returns the login, used for HTTP authentification
    */
    function login()
    {
        return $this->Login;
    }

    /*!
     Sets the HTTP password
    */
    function setPassword( $password  )
    {
        $this->Password = $password;
    }

    /*!
      Returns the password, used for HTTP authentification
    */
    function password()
    {
        return $this->Password;
    }

    /// The name or IP of the server to communicate with
    public $Server;
    /// The path to the SOAP server
    public $Path;
    /// The port of the server to communicate with.
    public $Port;
    /// How long to wait for the call.
    public $Timeout = 0;
    /// HTTP login for HTTP authentification
    public $Login;
    /// HTTP password for HTTP authentification
    public $Password;
    /// CA certificates (PEM file) to verify an HTTPS server against, or null for the system bundle
    protected $CAFile = null;
    /// Whether the certificate of an HTTPS server is verified
    protected $VerifyPeer = true;
    private $UseSSL;
}

?>
