# SOAP-Aufrufe über HTTPS: Zertifikate einer internen CA

Lesen Sie diese Seite, wenn Ihr Code mit `eZSOAPClient` einen SOAP-Dienst über HTTPS aufruft und der Dienst ein
Zertifikat hat, das das CA-Bundle des Systems nicht kennt, zum Beispiel eines, das eine interne CA des Unternehmens
ausgestellt hat.

## Kurz gesagt

- `eZSOAPClient` prüft das Zertifikat des Servers (die Zertifikatskette und den Hostnamen), wie es das immer getan hat.
- `$client->setCAFile( '/etc/ssl/internal-ca.pem' )` vertraut zusätzlich den CA-Zertifikaten in dieser Datei. So
  vertrauen Sie einem internen Zertifikat. cURL verwendet die Datei anstelle seiner CA-Bundle-Datei; ein in cURL
  einkompiliertes CA-Verzeichnis (auf Debian und Ubuntu `/etc/ssl/certs`) wird weiterhin gelesen, sodass auch den
  öffentlichen CAs weiter vertraut wird. Wo in cURL kein solches Verzeichnis einkompiliert ist, vertraut dieser Client
  nur den CAs in der Datei, sodass die Datei jede CA enthalten muss, die sein Server braucht. Geben Sie einen absoluten
  Pfad an; eine Datei, die sich nicht lesen lässt, lässt jeden Aufruf scheitern (der cURL-Fehler steht in
  `ErrorString`) und wird im Fehlerprotokoll gemeldet. Unter `open_basedir` darf die Datei außerhalb liegen, da cURL sie
  selbst liest; PHP kann sie dann nicht prüfen, sodass nur der cURL-Fehler es verrät. `null`, `false` (eine fehlende
  Einstellung), `''` und ein Name mit einem NUL-Byte (gemeldet) setzen keine CA-Datei.
- `$client->setVerifyPeer( false )` schaltet die Prüfung für diesen einen Client ab. Jeder Aufruf protokolliert dann
  eine Warnung. Verwenden Sie es nur, um herauszufinden, ob das Zertifikat das Problem ist, nie auf einem Live-Server.

## Beispiel

```php
$client = new eZSOAPClient( 'soap.example.internal', '/soap', 443 );
$client->setCAFile( eZINI::instance( 'myextension.ini' )->variable( 'SOAPSettings', 'CAFile' ) );
$response = $client->send( $request );
```

Die CA-Datei ist eine PEM-Datei mit dem Zertifikat der CA, die das Zertifikat des Servers ausgestellt hat (und jeder
Zwischen-CA). Fragen Sie die Betreiber des Dienstes danach, oder lesen Sie es aus der Kette, die der Server sendet:

```bash
openssl s_client -connect soap.example.internal:443 -showcerts </dev/null
```

## Warum nicht einfach die Prüfung abschalten

Ein Aufruf, dessen Zertifikat nicht geprüft wird, kann von jedem zwischen den beiden Servern beantwortet werden: Die
Anfrage mit Anmeldename und Passwort im Header Authorization und die Antwort lassen sich unterwegs lesen und ändern.
Eine CA-Datei behält die Prüfung bei und nennt nur, wem vertraut wird.

## Wie es funktioniert

`eZSOAPClient::send()` macht Aufrufe über HTTPS mit cURL; `curlOptions()` liefert die Optionen, mit
`CURLOPT_SSL_VERIFYPEER` und `CURLOPT_SSL_VERIFYHOST` (2) eingeschaltet und `CURLOPT_CAINFO`, wenn eine CA-Datei
gesetzt ist. Ohne die PHP-Erweiterung curl wird ein Aufruf über HTTPS mit einem Fehler abgelehnt; früher ging er als
Klartext an den TLS-Port, worauf kein Server antwortet. Das ist eine Verhaltensänderung: `send()` gibt `0` zurück,
`ErrorString` sagt, dass HTTPS die PHP-Erweiterung curl braucht, und das Fehlerprotokoll enthält „No SOAP call to
<server>: HTTPS needs the PHP extension curl, which is not loaded“ (siehe
[Verhaltensänderungen vom 1. bis 2. Oktober 2026](../../bc/6.0/verhaltensaenderungen-2026-10.md)). Aufrufe über einfaches
HTTP sind unverändert.

Ohne CA-Datei und ohne `setVerifyPeer()` sendet ein Aufruf, was er vorher sendete: `CURLOPT_SSL_VERIFYPEER` true und
`CURLOPT_SSL_VERIFYHOST` 2 sind die eigenen Standards von cURL, und `CURLOPT_CAINFO` wird nicht gesetzt, sodass
`curl.cainfo` aus der php.ini und das CA-Bundle von cURL wie bisher gelten. Die Einstellungen gehören zu einem
Client-Objekt; nichts wird zwischen Clients oder Anfragen aufbewahrt, auch nicht in einem dauerhaften Worker
(Velocity).

Eine Klasse, die `eZSOAPClient` erweitert und eigene `$CAFile`, `$VerifyPeer`, `curlOptions()`, `caFile()` oder
`verifyPeer()` deklariert, muss diese entfernen oder umbenennen.

## Tests

`eZSOAPClientTlsOptionsTest` (ohne Server): die Optionen standardmäßig, mit einer CA-Datei und mit den Werten, die
keine setzen, mit der für einen Client abgeschalteten Prüfung, und dass `send()` sie verwendet; in einem eigenen PHP
(`fixtures/soap_client_tls_subprocess.php`), dass ein Aufruf über HTTPS ohne curl abgelehnt wird, während einer über
HTTP weiterhin gemacht wird, und dass eine CA-Datei außerhalb von `open_basedir` nicht als unlesbar gemeldet wird. Von
Hand geprüft gegen einen TLS-Server mit einem Zertifikat einer Wegwerf-CA: standardmäßig abgelehnt
(Zertifikatsproblem), mit dieser CA-Datei angenommen.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
