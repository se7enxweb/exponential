---
title: ezformtoken und CSRF-Schutz
audience: developer
category: extension-development
order: 240
summary: Wie ezformtoken jeden POST eines angemeldeten Benutzers vor gefälschten Anfragen schützt - das Token in Formularen und im Seitenkopf, der Header X-CSRF-Token, die Ablehnung mit 403 - und was Ihre Erweiterung tut.
keywords: CSRF, Cross-Site Request Forgery, Formular-Token, Formularschutz, Anfragefälschung, ezformtoken, ezxFormToken, cross-site request forgery, ezxform_token, X-CSRF-Token, csrf-token meta, form token, POST, 403
type: help
---
# ezformtoken und CSRF-Schutz

Teil 8, Referenz 20. Cross-Site Request Forgery (CSRF) bedeutet, dass eine andere Website den Browser eines
angemeldeten Benutzers dazu bringt, einen POST an Ihre Website zu senden. Die mit dem Kernel ausgelieferte Erweiterung
`ezformtoken` verhindert das mit einem geheimen Token je Session. Meist funktioniert das, ohne dass Ihre Erweiterung
etwas tun muss; diese Seite sagt, wann nicht, und was dann zu tun ist.

## Wie es funktioniert

Die Erweiterung registriert drei Listener (in ihrer `settings/site.ini.append.php`):

| Ereignis | Methode | Tut |
|---|---|---|
| `response/output` | `ezxFormToken::output()` | fügt jedem `<form>` einer HTML-Seite ein verstecktes Feld `ezxform_token` hinzu und dem `<head>` die Meta-Tags `csrf-param` und `csrf-token` (mit der ID `ezxform_token_js`) |
| `request/input` | `ezxFormToken::input()` | lehnt einen POST ohne das richtige Token ab |
| `session/regenerate` | `ezxFormToken::reset()` | ein neues Token mit einer neuen Session |

Geschützt sind nur angemeldete Benutzer mit einer Session (bei anonymen Besuchern gibt es nichts zu stehlen), und
geprüft werden nur POST-Requests.

## Wo das Token gesucht wird

Bei einem POST nimmt `input()` das Token in dieser Reihenfolge aus:

1. dem POST-Feld (`ezxform_token` oder dem konfigurierten Feldnamen);
2. dem HTTP-Header `X-CSRF-Token`, für Ajax-Requests, die JSON oder XML senden.

Ein fehlendes oder falsches Token beendet den Request mit einer Ablehnung 403 (mit einem JSON-Body für Ajax- und
JSON-Requests).

## Was Ihre Erweiterung tut

| Situation | Vorgehen |
|---|---|
| Ein `<form method="post">` in einem Template | nichts: Der Ausgabefilter fügt das Feld hinzu |
| Ein Formular, das JavaScript aufbaut | ein verstecktes Feld `ezxform_token` mit dem Wert des Meta-Tags `csrf-token` hinzufügen |
| Ein Ajax-POST mit Formulardaten | `ezxform_token` mitsenden; `Exp.io.call()` und `$.ez()` von ezjscore erledigen das für Sie |
| Ein Ajax-POST mit einem JSON-Body | den Header `X-CSRF-Token` mit dem Token senden |
| Eine View, die bei GET Daten ändert | auf POST umstellen: GET wird nie geprüft |
| Eine JSON-Seite, die Ihre View ausliefert | das Token wird nur HTML-Seiten hinzugefügt; lesen Sie es aus der Seite, die den Aufruf macht |

In PHP gibt `ezxFormToken::getToken()` das Token der aktuellen Session zurück, für Templates, die Sie außerhalb der
normalen Ausgabe rendern (zum Beispiel einen per Ajax geladenen Dialog):

```php
<?php
$tpl = eZTemplate::factory();
$tpl->setVariable( 'ezxform_token', ezxFormToken::getToken() );
```

## Das Token in JavaScript lesen

```javascript
var meta = document.querySelector( 'meta[name="csrf-token"]' );
var token = meta ? meta.getAttribute( 'content' ) : '';
fetch( '/myext/save', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
    body: JSON.stringify( { name: 'Ada' } ),
    credentials: 'same-origin'
} );
```

## Schalten Sie ihn nie ab

`ezxFormToken::setIsEnabled( false )` gibt es für Sonderfälle innerhalb des Kernels. Eine Erweiterung, die den Schutz
abschaltet, damit ihr eigenes Formular funktioniert, öffnet jeden POST der Website für Fälschungen. Reparieren Sie
stattdessen das Formular.

## Verwandte Seiten

- Zurück: [Referenz der ezjscore-Serverfunktionen](ezjscore-serverfunktionen-referenz.md)
- Weiter: [E-Mails mit eZMail versenden](ezmail.md)
- [Sicherheit in Erweiterungen](../../../../eng-us/help/extension-development/part-3-extension-developer/security-in-extensions.md)
- [Cross-Site Request Forgery](https://owasp.org/www-community/attacks/csrf) bei OWASP
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
