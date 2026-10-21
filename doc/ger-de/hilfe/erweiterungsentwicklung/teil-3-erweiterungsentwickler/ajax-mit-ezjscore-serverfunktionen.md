---
title: Ajax mit ezjscore-Serverfunktionen
audience: developer, app-creator, designer
category: extension-development
order: 240
summary: Ajax-Aufrufe mit einer ezjscore-Serverfunktion beantworten - Einstellungen, eine Klasse mit statischen Funktionen, die Aufrufadresse und ihr JSON-Umschlag, Exp.io, Richtlinien, POST und das Formular-Token.
keywords: Ajax, Serverfunktion, Richtlinie, Formular-Token, ajax, ezjscore, server function, ezjscServer, ezjscServerFunctions, ezjscore/call, Exp.io, Exp.io.call, $.ez, json, error_text, FunctionList, PermissionPrFunction, RequirePolicy, form token, POST
type: help
---
# Ajax mit ezjscore-Serverfunktionen

Teil 3, Kapitel 24. Wenn eine Seite Daten braucht, ohne neu zu laden (eine Bewertung, eine Live-Suche, eine Liste,
die mehr nachlädt), ruft der Browser eine **ezjscore-Serverfunktion** auf: eine statische Methode Ihrer Klasse,
erreichbar unter `/ezjscore/call/<server>::<function>::<arg1>::<arg2>`. Dieses Kapitel baut eine solche Funktion; die
Methode wurde mit geladenem Kernel ausgeführt, und das Adressformat sowie der JSON-Umschlag wurden auf einer laufenden
Installation mit der kernel-eigenen Funktion `ezjsc::time` geprüft.

## Einstellungen

`extension/mysite_hello/settings/ezjscore.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ezjscServer_mysitehello]
Class=mysiteHelloServerFunctions

*/ ?>
```

Der Blockname nach `ezjscServer_` ist der **Servername**, der in Aufrufen verwendet wird. `Class` nennt Ihre Klasse
(per Autoload geladen); `File` kann ihre Datei nennen, wenn sie nicht per Autoload geladen wird. Diese Schlüssel und
die Zugriffsschlüssel weiter unten sind in `extension/ezjscore/settings/ezjscore.ini` dokumentiert.

## Die Klasse

```php
<?php
/**
 * ezjscore server functions of mysite_hello:
 *   GET  <root>/ezjscore/call/mysitehello::greet::Ada   -> {"error_text":"","content":{"greeting":"Hello, Ada!"}}
 *   JavaScript: Exp.io.call( 'mysitehello::greet', [ 'Ada' ] ).then( function ( content ) { ... } )
 */
class mysiteHelloServerFunctions extends ezjscServerFunctions
{
    public static function greet( $args )
    {
        $name = isset( $args[0] ) && is_string( $args[0] ) && $args[0] !== '' ? $args[0] : 'world';
        // the browser inserts this as text, but escape anyway: a server function may be called from anywhere
        return array( 'greeting' => 'Hello, ' . htmlspecialchars( $name, ENT_QUOTES, 'UTF-8' ) . '!' );
    }
}
```

Jede öffentliche statische Methode ist eine Funktion; `$args` enthält die Teile der Adresse nach dem Funktionsnamen.
Mit `'Ada <b>'` aufgerufen, gab sie `{"greeting":"Hello, Ada &lt;b&gt;!"}` zurück.

## Die Adresse und die Antwort

```text
GET /ezjscore/call/mysitehello::greet::Ada
Accept: application/json
```

antwortet mit einem Umschlag, wie es die kernel-eigene Funktion `ezjsc::time` auf einer laufenden Installation tut:

```json
{"error_text":"","content":{"greeting":"Hello, Ada!"}}
```

`content` ist, was Ihre Methode zurückgegeben hat; `error_text` ist gefüllt, wenn der Aufruf fehlgeschlagen ist oder
abgelehnt wurde. Ohne den JSON-Header `Accept` antwortet die View mit dem reinen Inhalt.

## Aufruf aus JavaScript

Die Administration und Designs mit expui haben `Exp.io` (Erweiterung `expui`):

```javascript
Exp.io.call( 'mysitehello::greet', [ 'Ada' ] ).then( function ( content ) {
    document.querySelector( '[data-mysite-greeting]' ).textContent = content.greeting;
}, function ( error ) {
    // error.kind: signedout, refused, server, network, timeout or invalid
} );
```

Laden Sie es mit `{ezscript_require( array( 'exp::core', 'exp::io', 'mysite_hello.js' ) )}`. `Exp.io.call()` fügt das
Formular-Token hinzu, lehnt bei `error_text` ab und liefert `content`. Älterer Code, der `$.ez()` verwendet,
funktioniert über die Kompatibilitätsschicht von expui (`exp/compat.js`) weiter. Fügen Sie Ergebnisse mit
`textContent` ein, nicht mit `innerHTML`, es sei denn, Sie haben das HTML sicher aufgebaut.

## Wer sie aufrufen darf

Ohne weitere Einstellungen kann jeder Besucher, der die View `ezjscore/call` verwenden darf, Ihre Funktion aufrufen.
So schränken Sie sie ein:

```ini
[ezjscServer]
FunctionList[]=mysitehello_greet

[ezjscServer_mysitehello]
Class=mysiteHelloServerFunctions
Functions[]=mysitehello
PermissionPrFunction=enabled
RequirePolicy=enabled
```

| Schlüssel | Bedeutung |
|---|---|
| `[ezjscServer] FunctionList[]` | Namen, die im Rolleneditor als Einschränkungswerte der Richtlinie `ezjscore/call` angeboten werden |
| `Functions[]` | die Namen, die ein Benutzer braucht |
| `PermissionPrFunction=enabled` | der benötigte Name ist pro Funktion `<name>_<function>` (`mysitehello_greet`) |
| `RequirePolicy=enabled` | `Functions[]` prüfen und mit 403 antworten, wenn jeder Aufruf einer Anfrage abgelehnt wird |

Die Erweiterung `ezstarrating` ist ein echtes Beispiel (`FunctionList[]=ezstarrating_rate`,
`PermissionPrFunction=enabled`). Erteilen Sie dann den Rollen, die sie brauchen, `ezjscore/call` mit dieser Funktion.

## Daten ändern: POST und das Formular-Token

Eine Funktion, die etwas ändert, muss GET ablehnen: Andernfalls würde ein `<img src=".../ezjscore/call/...">` auf
einer beliebigen Seite sie im Namen eines Besuchers aufrufen. `rate()` von `ezstarrating` kehrt sofort zurück, wenn die
Anfrage kein POST ist, und diesen Fall deckt dann die Prüfung des Formular-Tokens ab. `Exp.io.call()` sendet
standardmäßig ein POST mit dem Formular-Token (übergeben Sie `{ method: 'GET' }` für einen rein lesenden Aufruf), und
`Exp.io.form()` sendet ein Formular mit seinem Token.

## Template-Funktionen statt PHP

Ein Serverblock kann `TemplateFunction=true` setzen, um statt mit einer Methode mit einem Template
`templates/<class>/<function>.tpl` zu antworten (`ezjscore.ini` dokumentiert das). Das ist nützlich, um gerenderte
HTML-Fragmente zurückzugeben.

## RAD

**Handler extension: ajax function** (`/setup/handlerextension/ajaxfunction`) schreibt die Klasse und die
Einstellungen; siehe
[Von Hand: eine ezjscore-Serverfunktion](tutorials/von-hand-ajax-serverfunktion.md).

## Verwandte Seiten

- Zurück: [CSS und JavaScript in einer Erweiterung](css-und-javascript-in-einer-erweiterung.md)
- Weiter: [Übersetzungen in einer Erweiterung](uebersetzungen-in-einer-erweiterung.md)
- [Referenz der ezjscore-Serverfunktionen](../teil-8-referenz/ezjscore-serverfunktionen-referenz.md)
- [ezjscore-Dienste im Backend](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
