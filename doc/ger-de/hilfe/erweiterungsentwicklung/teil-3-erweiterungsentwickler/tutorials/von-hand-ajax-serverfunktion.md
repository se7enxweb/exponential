---
title: "Von Hand: eine ezjscore-Serverfunktion"
audience: developer, app-creator, designer
category: extension-development
order: 190
summary: Einen Ajax-Aufruf beantworten - ezjscServer-Einstellungen, eine Klasse mit Serverfunktionen, ein Skript, das sie mit Exp.io aufruft und mit textContent schreibt, eine Richtlinie dafür und das Testen der Adresse.
keywords: Tutorial, von Hand, Ajax, Serverfunktion, Richtlinie, JSON, tutorial, by hand, ajax, ezjscore, server function, ezjscServerFunctions, ezjscServer, Exp.io.call, ezscript_require, exp::io, FunctionList, PermissionPrFunction, RequirePolicy
type: help
---
# Von Hand: eine ezjscore-Serverfunktion

Tutorial 18. Eine Schaltfläche auf der Hello-Seite fragt den Server nach einem Gruß, ohne die Seite neu zu laden. Die
Serverfunktion wurde mit geladenem Kernel ausgeführt, das JavaScript hat `node --check` bestanden, und der JSON-Umschlag
wurde auf einer laufenden Installation mit der kernel-eigenen Funktion `ezjsc::time` geprüft.

## 1. Die Einstellungen

`extension/mysite_hello/settings/ezjscore.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ezjscServer]
FunctionList[]=mysitehello_greet

[ezjscServer_mysitehello]
Class=mysiteHelloServerFunctions
Functions[]=mysitehello
PermissionPrFunction=enabled
RequirePolicy=enabled

*/ ?>
```

Der Server heißt `mysitehello`; um seine Funktion `greet` aufzurufen, braucht es die Richtlinie `ezjscore/call` mit dem
Wert `mysitehello_greet` in der Funktionsliste.

## 2. Die Klasse

`extension/mysite_hello/classes/mysitehelloserverfunctions.php`:

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

Mit `'Ada <b>'` aufgerufen, gab sie `{"greeting":"Hello, Ada &lt;b&gt;!"}` zurück.

## 3. Das Skript

`extension/mysite_hello/design/standard/javascript/mysite_hello.js`:

```javascript
document.addEventListener( 'click', function ( e ) {
    var button = e.target.closest( '[data-mysite-ask]' );
    if ( !button || !window.Exp || !Exp.io )
        return;
    var out = document.querySelector( '[data-mysite-answer]' );
    Exp.io.call( 'mysitehello::greet', [ button.getAttribute( 'data-mysite-ask' ) ], { method: 'GET' } ).then(
        function ( content ) { if ( out ) out.textContent = content.greeting; },
        function ( error ) { if ( out ) out.textContent = error.message; }
    );
} );
```

Der Aufruf liest nur und verwendet daher GET. Eine Serverfunktion, die Daten ändert, darf nur POST annehmen (die
Voreinstellung von `Exp.io`) und ist durch das Formular-Token geschützt.

## 4. Das Template

Ergänzen Sie in `hello/world.tpl` aus Tutorial 12:

```text
{ezscript_require( array( 'exp::core', 'exp::io', 'mysite_hello.js' ) )}
<button type="button" class="mysite-ask" data-mysite-ask="{$name|wash}">{'Ask the server'|i18n( 'extension/mysite_hello' )}</button>
<output data-mysite-answer></output>
```

Kein Inline-Skript, kein `onclick`: So funktioniert es auch unter der Content Security Policy der Administration.

## 5. Ausprobieren

```bash
php -l extension/mysite_hello/classes/mysitehelloserverfunctions.php
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

Gewähren Sie der Rolle, die die Seite ansieht, `ezjscore/call` mit der Funktion `mysitehello_greet`. Öffnen Sie dann
die Adresse direkt und senden Sie dabei den JSON-Header:

```bash
curl -s -H 'Accept: application/json' https://www.example.com/ezjscore/call/mysitehello::greet::Ada
```

Die Antwort ist `{"error_text":"","content":{"greeting":"Hello, Ada!"}}` für einen Benutzer, der die Funktion aufrufen
darf; für andere nennt `error_text` den Grund. Auf der Seite schreibt die Schaltfläche den Gruß in die Ausgabe.

## Verwandte Seiten

- Zurück: [Von Hand: Ihre eigene Tabelle mit eZPersistentObject](von-hand-eigene-tabelle-mit-persistentem-objekt.md)
- Weiter: [Von Hand: ein Custom Tag für Rich Text](von-hand-custom-tag.md)
- [Ajax mit ezjscore-Serverfunktionen](../ajax-mit-ezjscore-serverfunktionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
