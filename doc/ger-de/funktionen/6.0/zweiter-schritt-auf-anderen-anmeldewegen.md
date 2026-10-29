# Zweiter Schritt auf den anderen Anmeldewegen

Die zweistufige Anmeldung (die Extension `sevenx_authentication_2fa`) fragt ihren zweiten Schritt im
Anmeldeformular der Website ab. Vier andere Wege prüfen ein Passwort selbst: die REST-Schnittstelle mit
Basic-Authentifizierung, WebDAV, SOAP und die Anmeldung der entfernten Dienste (expservices). Ein Konto, für das die
Website einen zweiten Schritt verlangt, meldet sich dort allein mit dem Passwort an. Das bleibt der Standard: Die
Clients dieser Wege können keinen zweiten Schritt beantworten. Seit 6.0.15 fragt der Kernel auf diesen Wegen die
Extension, und eine Website kann **jeden Weg einzeln einbeziehen**. Auf einem einbezogenen Weg wird für ein solches
Konto ein Passwort allein abgewiesen; stattdessen meldet es sich mit einem **App-Passwort** oder mit dem **aktuellen
Code seiner Authenticator-App** an.

## Kurz gesagt

- **Standardmäßig ändert sich nichts.** `sevenxauthentication2fa.ini [OtherSignInPaths] Guard[]` ist leer: REST
  (Basic-Authentifizierung, API-Schlüssel, OAuth), SOAP, WebDAV und die expservices-Anmeldung melden ein Konto mit
  `Enforce2FA` allein mit dem Passwort an, genau wie vor 6.0.15, und die Einrichtungsseite zeigt keine App-Passwörter.
- Eine Website bezieht einen Weg mit je einer Zeile ein: `Guard[]=expservices`, `Guard[]=webdav`, `Guard[]=rest`,
  `Guard[]=soap`. Dann wird für ein Konto, für das `Enforce2FA` gilt (zum Beispiel die Gruppen Administrator users und
  Editors), ein Passwort allein auf diesem Weg abgewiesen.
- Ein **App-Passwort**, das der Benutzer auf der Seite der zweistufigen Anmeldung (`user2fa/setup`) erstellt, meldet
  über die einbezogenen unter REST, WebDAV und SOAP an. Es wird einmal angezeigt, nur als gesalzener Hash
  gespeichert, gilt nur auf den Wegen, für die es erstellt wurde, und der Benutzer kann es jederzeit widerrufen. Die
  Seite bietet nur einbezogene Wege an.
- Die **expservices-Anmeldung** erzeugt eine Sitzung, die auch die Website annimmt. App-Passwörter gelten dort nie.
  Ist sie einbezogen, sendet das Konto mit dem Passwort den aktuellen Code seiner Authenticator-App im POST-Feld `otp`
  (oder im Header `X-Exp-OTP`).
- Auf einer einbezogenen REST-Schnittstelle können Clients auch einen **persönlichen API-Schlüssel** oder ein
  **OAuth-Token** verwenden. Beides ist kein Passwort allein: Den Autorisierungscode von OAuth gibt es erst nach der
  Anmeldung im Web, mit ihrem zweiten Schritt.
- Konten, für die `Enforce2FA` nicht gilt, bleiben unverändert. Ausgenommene Konten (`ExemptGroups[]`,
  `ExemptEmailDomains[]`) ebenfalls. Konsolenbefehle fragen nie.
- Jede Abweisung steht im Prüfprotokoll (`access.user.second_factor.refused`). Ebenso das Erstellen, Widerrufen und
  Verwenden eines App-Passworts.
- Es funktioniert unter Apache mit PHP-FPM ebenso wie unter Exponential Velocity.

## Was jeder einbezogene Weg antwortet

Auf einem Weg, der nicht in `Guard[]` steht, meldet ein Passwort allein wie bisher an; die Tabelle gilt, sobald ein
Weg aufgeführt ist.

| Weg | Passwort allein, Konto braucht einen zweiten Schritt | Was anmeldet |
|---|---|---|
| REST, `AuthenticationStyle=ezpRestBasicAuthStyle` | `401` mit `WWW-Authenticate: Basic realm="Exponential REST"` und `{"error":"second_factor_required","error_description":"..."}`; `403` mit `second_factor_setup_required`, solange das Konto noch keinen zweiten Schritt hat | ein App-Passwort (für `rest`) als Passwort; ein persönlicher API-Schlüssel (`Authorization: Bearer expk_...`); ein OAuth-Token |
| WebDAV (`index_webdav.php`, früher `webdav.php`) | `401` mit dem `WWW-Authenticate` des Servers und dem Grund in `X-Exp-Sign-In-Refused` | ein App-Passwort (für `webdav`) als Passwort |
| SOAP (`soap.php`) | `401` (`403` bei `second_factor_setup_required`) mit `WWW-Authenticate: Basic realm="Exponential SOAP"`, `X-Exp-Sign-In-Refused` und einem SOAP-Fault (`faultcode` `SOAP-ENV:Client`, `<detail><reason>`) | ein App-Passwort (für `soap`) als Passwort |
| expservices `expsession::login` | `{"ok":false,"error":{"code":401,"message":"...","reason":"second_factor_required"}}` | Benutzername, Passwort und `otp` (der Code der App) |

Die Gründe:

- `second_factor_required`: Das Passwort stimmte, ein zweiter Faktor fehlt.
- `second_factor_invalid`: Ein Code wurde gesendet, ist aber falsch, wurde schon verwendet, es kamen zu viele falsche
  Codes (der Code ist dann für `CodeLockSeconds` gesperrt), oder der Weg nimmt keine Codes an.
- `second_factor_setup_required`: Das Konto hat noch keinen zweiten Schritt. Es meldet sich auf der Website an,
  richtet ihn ein und erstellt danach ein App-Passwort.

Der Token-Endpunkt von OAuth in der REST-Schnittstelle nimmt nur Autorisierungscodes und Refresh-Tokens an. Einen
Password-Grant, der den zweiten Schritt umgehen könnte, gibt es nicht.

## App-Passwörter

Sobald eine Website REST, WebDAV oder SOAP einbezogen hat, findet ein angemeldeter Benutzer mit einem eingerichteten
zweiten Schritt auf `user2fa/setup` den Abschnitt **App-Passwörter** (ist keiner davon einbezogen, wird er nicht
angezeigt):

1. **App-Passwort erstellen**: einen Namen geben (der Client, für den es ist), ankreuzen, wo es gelten darf (die
   einbezogenen unter REST-Schnittstelle, WebDAV und SOAP), und mit dem aktuellen Code der Authenticator-App bestätigen. Wer Codes per
   E-Mail erhält, braucht keinen Code.
2. Das Passwort wird einmal angezeigt, in Vierergruppen (`abcd-efgh-jkmn-pqrs-tuvw-xyz2`). Im Client wird es statt
   des Passworts des Kontos eingetragen, mit dem üblichen Benutzernamen. Groß- und Kleinschreibung und die
   Bindestriche spielen keine Rolle.
3. Die Liste zeigt jedes App-Passwort mit seinen Wegen, wann es erstellt und zuletzt verwendet wurde und wann es
   abläuft. **Widerrufen** beendet es sofort.

Weitere Regeln:

- Ein App-Passwort gehört zum zweiten Schritt. Wer den zweiten Schritt entfernt oder zurücksetzt, widerruft alle.
  Ein Wechsel von der Authenticator-App zu E-Mail-Codes oder umgekehrt behält sie.
- Es meldet nie über das Anmeldeformular oder die expservices-Anmeldung an und erzeugt nie ein Sitzungs-Cookie. Jede
  Anfrage wird für sich angemeldet.
- Ein deaktiviertes oder wegen fehlgeschlagener Anmeldungen gesperrtes Konto meldet es nicht an.
- Es wird vor dem Passwort geprüft. Ein Client mit App-Passwort zählt daher nie als fehlgeschlagene Anmeldung.
- Gespeichert wird nur sein gesalzener HMAC-SHA-256, im Zwei-Faktor-Feld des Kontos. Es kommt also auf keiner
  Datenbank eine Tabelle hinzu. Es hat etwa 119 zufällige Bits.

## Der Code der Authenticator-App

Auf den einbezogenen Wegen in `CodeChannels[]` (standardmäßig nur die expservices-Anmeldung, sobald
`Guard[]=expservices` gesetzt ist) zählt der aktuelle Code als zweiter
Schritt:

```bash
curl -s -c jar -d 'username=<user>' --data-urlencode 'password=<password>' -d 'otp=<code>' \
  'https://<site>/ezjscore/call/expsession::login?ContentType=json'
```

Jeder Code gilt einmal, hier wie im Anmeldeformular. Nach `[Security] MaxAttempts` falschen Codes ist der Code für
`CodeLockSeconds` (900 Sekunden) gesperrt. Der Zähler der fehlgeschlagenen Anmeldungen des Kontos kann das hier nicht
leisten, weil jedes richtige Passwort vor einem Code ihn zurücksetzt. REST, WebDAV und SOAP senden die Anmeldedaten
mit jeder Anfrage; ein Code, der nur einmal gilt, passt dort schlecht. Eine Website kann sie trotzdem in
`CodeChannels[]` aufnehmen; der Header ist dann `X-Exp-OTP` (`site.ini [SignInSecondFactorSettings] CodeHeader`).

## Einstellungen

`sevenxauthentication2fa.ini`, in `settings/override` oder einem Siteaccess:

```ini
[OtherSignInPaths]
# the default: no path guarded, every path signs in with the password alone as before
Guard[]
# opt in, one line each:
#Guard[]=expservices
#Guard[]=webdav
#Guard[]=rest
#Guard[]=soap
# also accounts that set up a second step themselves (Enforce2FA does not apply to them)
GuardOptedIn=disabled
CodeChannels[]
CodeChannels[]=expservices
CodeLockSeconds=900

[AppPasswords]
AppPasswords=enabled
# only rest, webdav and soap are possible, and only those also in Guard[] are offered
Channels[]
Channels[]=rest
Channels[]=webdav
Channels[]=soap
MaxPerUser=10
# 0: until revoked
ExpiryDays=365
LastUsedInterval=900
```

Die Seite des Kernels ist `site.ini [SignInSecondFactorSettings]`. `Handlers[]` nennt die Klassen, die der Kernel
fragt: Die Extension trägt `sevenxAuthentication2faSignIn` in ihrer eigenen `site.ini.append.php` ein. `CodeHeader`
nennt den Header eines Codes. Ohne Handler, oder mit dem Handler und leerem `Guard[]`, ändert sich nichts.

## Für Entwickler

- `expSignInSecondFactor` (`kernel/classes/user/expsigninsecondfactor.php`) rufen die Wege auf: `check()` nach einem
  richtigen Passwort, `appPasswordUser()` davor, `presentedCode()`, und die Antworten (`wwwAuthenticate()`,
  `soapFault()`, `sendRefusalHeaders()`; REST: `expRestSecondFactorRequiredStatus`).
- Ein Handler implementiert `expSignInSecondFactorHandler`: `checkSignIn( eZUser $user, $channel, $code )` gibt
  `null` oder `array( 'reason' => ..., 'message' => ... )` zurück, `appPasswordUser( $login, $secret, $channel )` das
  Konto oder `null`.
- Die Wege sind dieselben wie bei [Passwort bei der nächsten Anmeldung ändern](passwort-bei-naechster-anmeldung-aendern.md).
  Der zweite Schritt wird zuerst geprüft.

## Prüfprotokoll

| Ereignis | Wann |
|---|---|
| `access.user.second_factor.refused` | ein Passwort allein wird abgewiesen (Kanal, Grund, ob ein Code gesendet wurde) |
| `access.user.app_password.create` | ein App-Passwort wird erstellt (ID, Name, Wege, Ablauf) |
| `access.user.app_password.revoke` | es wird widerrufen (Grund `owner`, `second_step_removed`, `second_step_reset`) |
| `access.user.app_password.use` | es meldet an (höchstens einmal je `LastUsedInterval`) |

Passwörter, Codes, Hashes und Salts werden nie aufgezeichnet. Die Extension schreibt außerdem eigene Zeilen in
`var/log/auth.log`.

Siehe auch: [Passwort bei der nächsten Anmeldung ändern](passwort-bei-naechster-anmeldung-aendern.md),
[API-Schlüssel](../../anleitungen/api-schluessel.md), [Prüfprotokoll](pruefprotokoll.md), sowie `README.md` und
`INSTALL.md` der Extension.
