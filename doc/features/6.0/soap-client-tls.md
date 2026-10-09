# SOAP calls over HTTPS: certificates of an internal CA

Read this page if your code calls a SOAP service over HTTPS with `eZSOAPClient`, and the service has a certificate
that the system's CA bundle does not know, for example one an internal CA of the company issued.

## In short

- `eZSOAPClient` verifies the certificate of the server (the certificate chain and the host name), as it always did.
- `$client->setCAFile( '/etc/ssl/internal-ca.pem' )` trusts the CA certificates in that file as well. This is the
  way to trust an internal certificate. cURL uses the file in place of its CA bundle file; a CA directory compiled
  into cURL (on Debian and Ubuntu `/etc/ssl/certs`) is still read, so the public CAs stay trusted too. Give an
  absolute path; a file that cannot be read fails every call and is reported in the error log. `null`, `false` (a
  missing setting) and `''` set no CA file.
- `$client->setVerifyPeer( false )` turns the check off for that one client. Every call then logs a warning. Use it
  only to find out whether the certificate is the problem, never on a live server.

## Example

```php
$client = new eZSOAPClient( 'soap.example.internal', '/soap', 443 );
$client->setCAFile( eZINI::instance( 'myextension.ini' )->variable( 'SOAPSettings', 'CAFile' ) );
$response = $client->send( $request );
```

The CA file is a PEM file with the certificate of the CA that issued the server's certificate (and of any
intermediate CA). Ask the operators of the service for it, or read it from the chain the server sends:

```bash
openssl s_client -connect soap.example.internal:443 -showcerts </dev/null
```

## Why not just turn the check off

A call whose certificate is not checked can be answered by anybody between the two servers: the request, with its
login and password in the Authorization header, and the answer can be read and changed on the way. A CA file keeps
the check and only names whom to trust.

## How it works

`eZSOAPClient::send()` makes calls over HTTPS with cURL; `curlOptions()` gives the options, with
`CURLOPT_SSL_VERIFYPEER` and `CURLOPT_SSL_VERIFYHOST` (2) on, and `CURLOPT_CAINFO` when a CA file is set. Without the
PHP extension curl a call over HTTPS is refused with an error; it used to go to the TLS port as plain text, which no
server answers. Calls over plain HTTP are unchanged.

A class that extends `eZSOAPClient` and declares its own `$CAFile`, `$VerifyPeer`, `curlOptions()`, `caFile()` or
`verifyPeer()` has to drop or rename them.

## Tests

`eZSOAPClientTlsOptionsTest` (no server): the options by default, with a CA file and with the values that set none,
with the check turned off for one client, and that `send()` uses them. Checked by hand against a TLS server with a
certificate of a throw-away CA: refused by default (certificate problem), accepted with that CA file.
