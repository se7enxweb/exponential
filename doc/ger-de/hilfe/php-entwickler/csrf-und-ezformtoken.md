---
title: CSRF-Schutz und ezformtoken
audience: php-developer, developer
category: php-development
order: 350
summary: Wie Cross-Site Request Forgery in Exponential verhindert wird und was PHP-Code tun muss - die ezformtoken-Listener auf request/input und response/output, welche Anfragen geprüft werden, das Formularfeld und der Header X-CSRF-Token, die 403-Ablehnung und ihre Log-Zeile, das Geheimnis in HTMLForms und POSTs aus JavaScript, Skripten und APIs.
keywords: CSRF, Cross-Site Request Forgery, Formular-Token, Ablehnung, Formular abgelaufen, cross-site request forgery, ezformtoken, ezxFormToken, form token, ezxform_token, X-CSRF-Token, getToken, request/input, response/output, HTMLForms Secret, RefusalLogCollapseSeconds, ezpFormTokenException, POST, 403
type: help
---
# CSRF-Schutz und ezformtoken

Diese Seite richtet sich an PHP-Entwickler, deren Code POST-Anfragen empfängt. Cross-Site Request Forgery bringt den
Browser eines angemeldeten Benutzers dazu, von einer feindlichen Seite aus eine Anfrage zu senden, die der Benutzer
nicht beabsichtigt hat. Exponential blockiert das mit der Erweiterung `ezformtoken`, die Teil der Kernel-Distribution
ist (`extension/ezformtoken`). Der meiste Code ist geschützt, ohne etwas zu tun; diese Seite erklärt, wann das gilt und
was zu tun ist, wenn nicht.

## Inhalt

1. [Wie es funktioniert](#wie-es-funktioniert)
2. [Welche Anfragen geprüft werden](#welche-anfragen-geprüft-werden)
3. [Was Ihr Code tun muss](#was-ihr-code-tun-muss)
4. [POSTs aus JavaScript](#posts-aus-javascript)
5. [Skripte und APIs](#skripte-und-apis)
6. [Die Ablehnung](#die-ablehnung)
7. [Einstellungen](#einstellungen)
8. [Fehler, die Sie vermeiden sollten](#fehler-die-sie-vermeiden-sollten)

## Wie es funktioniert

`extension/ezformtoken/settings/site.ini.append.php` registriert drei Listener am Ereignis-Hub des Kernels:

```ini
[Event]
Listeners[]=request/input@ezxFormToken::input
Listeners[]=response/output@ezxFormToken::output
Listeners[]=session/regenerate@ezxFormToken::reset
```

| Listener | Tut |
|---|---|
| `ezxFormToken::output()` | fügt jedem `method="post"`-Formular der Seite ein verstecktes Feld `ezxform_token` mit dem Token des Benutzers hinzu |
| `ezxFormToken::input()` | prüft jeden POST, bevor irgendein Modul läuft; ein fehlendes oder falsches Token beendet die Anfrage |
| `ezxFormToken::reset()` | gibt der Sitzung ein neues Token, wenn sich die Sitzungs-ID ändert (Anmeldung) |

`ezxFormToken::getToken()` gibt es zurück: einen SHA-1-Hash aus dem Geheimnis, der Absicht und der Sitzungs-ID.

## Welche Anfragen geprüft werden

`ezxFormToken::input()` prüft eine Anfrage, wenn all das zutrifft:

- sie ist ein POST (oder trägt POST-Daten);
- die Sitzung wurde gestartet und der Benutzer ist **angemeldet** (anonyme POSTs werden nicht geprüft: Ein anonymer
  Besucher hat keine Sitzung, die sich missbrauchen ließe);
- sie ist kein Bericht der Content Security Policy.

Das Token wird aus dem POST-Feld gelesen (`ezxform_token` oder das mit `setFormField()` gesetzte Feld) oder aus dem
Header `X-CSRF-Token`, mit dem JavaScript JSON- oder XML-Bodys senden kann.

## Was Ihr Code tun muss

1. **Ändern Sie Daten nur per POST.** Ein GET, der Daten ändert, lässt sich nicht schützen; ein Link oder ein Bild kann
   ihn auslösen.
2. **Rendern Sie Formulare über Templates** mit `method="post"`; das Token wird für Sie hinzugefügt.
3. **Verwenden Sie die Aktionen des Moduls** (`$Module->isCurrentAction()`) oder `eZHTTPTool`, um den POST zu lesen;
   wenn Ihr Code läuft, wurde das Token bereits geprüft.
4. **Schalten Sie die Prüfung für Ihr Modul nicht ab.** `ezxFormToken::setIsEnabled( false )` gibt es für Sonderfälle
   im Kernel; es ist keine Lösung für ein fehlschlagendes Formular.

## POSTs aus JavaScript

JavaScript, das mit `fetch()` postet, sendet das Token, das es aus der Seite liest:

```javascript
// the token is in every form the page has; or render it into a data attribute of your own
var token = document.querySelector( 'input[name="ezxform_token"]' ).value;
fetch( url, { method: 'POST', credentials: 'same-origin',
              headers: { 'X-CSRF-Token': token, 'Content-Type': 'application/json' },
              body: JSON.stringify( data ) } );
```

Wenn die Seite kein Formular hat, rendern Sie das Token in Ihrem Template in ein `data-*`-Attribut; in PHP gibt
`ezxFormToken::getToken()` es zurück. Geben Sie es nicht in ein Inline-Skript aus
([Ausgaben aus PHP maskieren](ausgaben-aus-php-maskieren.md#attribute-urls-und-daten-für-skripte)).

## Skripte und APIs

Programme sind keine Browser und verwenden keine Formular-Tokens:

- Die REST-API authentifiziert mit OAuth2-Tokens oder API-Schlüsseln ([REST und entfernte Dienste aus PHP](rest-und-entfernte-dienste-aus-php.md)).
- expservices akzeptiert ein persönliches API-Token in `Authorization: Bearer` oder `X-Exp-Token`; eine Anfrage, die es
  trägt, braucht für Schreibvorgänge POST, aber kein Formular-Token, weil eine feindliche Seite diesen Header nicht
  setzen kann. Eine Anfrage ohne den Token-Header fällt auf das Cookie und das Formular-Token zurück
  ([Backend-Dienste über ezjscore](../../bc/6.0/backend-dienste-ueber-ezjscore.md)).
- Kommandozeilenskripte durchlaufen den Web-Kernel überhaupt nicht.

## Die Ablehnung

Ein abgelehnter POST endet mit `ezpFormTokenException`: Der Kernel antwortet mit einer 403-Seite (oder einem JSON-Body
für XHR- und JSON-Anfragen) und schreibt eine Zeile in `var/log/warning.log`. Besucher sehen eine Seite „Formular
abgelaufen“, die sagt, dass nichts gespeichert wurde, und sie bittet, das Formular neu zu laden und erneut zu senden
([Seite „Formular abgelaufen“](../../funktionen/6.0/seite-abgelaufenes-formular.md)).

## Einstellungen

`site.ini [HTMLForms]`:

| Schlüssel | Bedeutung |
|---|---|
| `Secret` | das Geheimnis, das in die Tokens eingemischt wird; setzen Sie je Installation einen eigenen Wert (der mitgelieferte Wert sagt, dass er zu ändern ist) |
| `RefusalLogCollapseSeconds` | dieselbe Ablehnung innerhalb dieser Anzahl Sekunden wird gezählt statt erneut protokolliert (Standard 60; 0 protokolliert jede) |

## Fehler, die Sie vermeiden sollten

| Fehler | Wirkung | Lösung |
|---|---|---|
| ein in PHP gebautes und roh ausgegebenes Formular, außerhalb des Ausgabefilters der Seite | kein Token, jeder POST wird abgelehnt | rendern Sie es über ein Template |
| ein GET-Link, der löscht | fälschbar | ein POST-Formular mit einer Schaltfläche |
| `fetch()` ohne das Token | 403 für angemeldete Benutzer | senden Sie `X-CSRF-Token` |
| die Ablehnung abfangen, „damit das Formular funktioniert“ | Schutz weg | finden Sie heraus, warum das Token fehlt |
| das Token in einer URL | gelangt in Logs und Referrer | nur im POST-Body oder Header |

## Verwandte Seiten

- [Formulare, CSRF-Schutz und HTTP-Eingaben](../ersteller/anwendungsentwickler/formulare-csrf-und-http-eingaben.md)
- [Referenz zu ezformtoken und CSRF](../erweiterungsentwicklung/teil-8-referenz/ezformtoken-und-csrf.md)
- [Sicherheit in PHP-Code](sicherheit-in-php-code.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
