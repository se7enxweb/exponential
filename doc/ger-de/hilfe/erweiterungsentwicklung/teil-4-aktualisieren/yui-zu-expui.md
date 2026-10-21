---
title: Von YUI zu expui
audience: developer, designer
category: extension-development
order: 170
summary: YUI 2 und 3 sind ab Exponential 6.0.15 nicht mehr vorhanden. Finden Sie den YUI-Code einer alten Erweiterung und stellen Sie ihn auf jQuery und Exponential UI (expui) um - Serveraufrufe, Dialoge, Tabellen, Datumsauswahl und Ereignisse.
keywords: YUI entfernt, Dialog, Datumsauswahl, Upgrade, YUI, YUI 2, YUI 3, YAHOO, expui, Exponential UI, jQuery, Exp.io.call, Exp.dialog, exp::datatable, exp::datepicker, ezjsc::yui3, upgrade
type: help
---
# Von YUI zu expui

Teil 4, Kapitel 17. Jahrelang lief die Administration auf YUI, der Yahoo! User Interface Library, und viele
Erweiterungen haben sie ebenfalls verwendet: für Ajax-Aufrufe, Dialoge, Datentabellen und Datumsauswahl.
Exponential 6.0.15 hat YUI aus dem Kernel, aus jedem Design und aus jeder mitgelieferten Erweiterung entfernt. Code,
der noch nach YUI fragt, bekommt nichts: `YUI` und `YAHOO` sind undefiniert, und das Skript der Seite bricht bei der
ersten Verwendung ab. Dieses Kapitel zeigt, wie Sie eine alte Erweiterung auf **Exponential UI** (die Erweiterung
`expui`, JavaScript-Objekt `Exp`) und jQuery umstellen.

Die vollständige Referenz ist [YUI aus Exponential entfernt](../../../bc/6.0/yui-entfernung.md). Der eigene
Leitfaden von Exponential UI, mit einem Beispiel für jedes YUI-Modul, ist `doc/CONVERTING_YUI_to_EXPUI.md` im
[Repository von expui](https://github.com/se7enxweb/expui).

## YUI in Ihrer Erweiterung finden

Die Dokumentation des Kernels gibt diese Suche an:

```bash
grep -rnE "ezjsc::yui|YUI\(|YAHOO\.|YUI3_config|YUILoader|yui3-js-enabled" extension/myoldext/
```

Und auf einer Seite in den Entwicklerwerkzeugen des Browsers: Eine Anfrage, deren URL `yui` enthält, bedeutet, dass
noch etwas danach fragt, und ein Fehler wie „YUI is not defined“ zeigt auf die Zeile.

## Laden: was Sie stattdessen schreiben

Alte Templates laden YUI mit den Packer-Schlüsseln von ezjscore:

```text
{ezscript_require( array( 'ezjsc::yui3', 'ezjsc::yui3io', 'myoldext.js' ) )}
```

Diese Schlüssel laden nichts mehr. Ersetzen Sie sie durch jQuery und die Module von Exponential UI, die Sie brauchen:

```text
{ezscript_require( array( 'ezjsc::jquery', 'exp::core::shared', 'exp::io', 'myoldext.js' ) )}
```

In der Administration ist der Kern von Exponential UI bereits auf jeder Seite geladen (expui fügt seine Dateien der
Backend-Liste hinzu), daher braucht eine Administrationsseite meist nur ihre eigene Datei und die Module, die sie
verwendet. Ein Frontend-Design lädt ihn selbst; die Dokumentation des Kernels zeigt das:

```text
{exp_config()}
{ezscript_require( array( 'ezjsc::jquery', 'exp::core::shared', 'exp::datepicker' ) )}
{ezcss_require( array( 'exp/core.css', 'exp/datepicker.css' ) )}
```

## Der Ersatz

| YUI | Stattdessen verwenden |
|---|---|
| `Y.io.ez( 'fn::args', ... )` (`ezjsc::yui3io`) | `Exp.io.call( 'fn', [args] )` (`exp::io`) oder `$.ez( 'fn::args', post, callback )` von jQuery (`ezjsc::jqueryio`) |
| `Y.one`, `Y.all`, `Y.on`, `Y.Node` | jQuery `$( ... )` |
| DataTable und Paginator von YUI 2 | `exp::datatable` (`$.fn.expDataTable`) |
| Calendar von YUI 2, `ezdatepicker.js` | `exp::datepicker` |
| `ezmodalwindow`, YUI Dialog | `exp::dialog` (`Exp.dialog`) |
| `ezajaxuploader` | `exp::upload` |
| `ezcollapsiblemenu` | `exp::collapse` (`Exp.collapse`) |
| `fixed_toolbar.js` | `exp::sticky` |
| Drag and Drop von YUI | native Drag-Ereignisse oder Pointer-Ereignisse |
| `.yui3-js-enabled` auf `<html>` | `.exp-js` |

## Beispiel: ein Serveraufruf

Der häufigste YUI-Code in Erweiterungen ist ein Aufruf einer Server-Funktion von ezjscore. Die Server-Funktion bleibt
genau, wie sie ist; nur der Aufruf ändert sich. Aus dem Umstellungsleitfaden von Exponential UI:

```javascript
// before: YUI 3 io-ez
Y.io.ez( 'mysite::latest::' + nodeId, {
    on: {
        success: function( id, r ) {
            var data = r.responseJSON;
            if ( data.error_text ) { alert( data.error_text ); return; }
            Y.one( '#latest' ).setHTML( data.content );
        },
        failure: function() { alert( 'failed' ); }
    }
});

// after: Exponential UI
Exp.io.call( 'mysite::latest', [nodeId] ).then( function( content ) {
    $( '#latest' ).html( content );
}, function( error ) {
    alert( error.message );
} );
```

`Exp.io.call()` liefert ein Promise. Der Fehlerfall erhält ein Objekt, dessen `kind` Ihnen sagt, was passiert ist (der
Leitfaden führt `signedout`, `refused`, `server`, `network`, `timeout` und `invalid` auf), sodass Sie einen
abgemeldeten Benutzer von einem defekten Server unterscheiden können. POST-Daten gehören ins dritte Argument:
`Exp.io.call( 'mysite::save', [], { data: { title: title } } )`.

## Beispiel: ein Dialog

```javascript
// before: the admin's YUI modal window
var win = new Y.eZ.ModalWindow( { window: '#my-window', width: 650 } );
win.setTitle( 'Rename' ); win.setContent( html ); win.open();

// after
var d = Exp.dialog.create( { title: 'Rename', width: 650 } );
d.setContent( html );
d.open().then( function( value ) { /* the value it was closed with, or null */ } );
```

Eine Bestätigung wird zu einer Zeile: `Exp.dialog.confirm( 'Remove?' ).then( function( ok ) { ... } );`. Die Dialoge
von Exponential UI verwenden das eigene `<dialog>`-Element des Browsers, daher braucht das Template kein Markup für
ein Fenster.

## Beispiel: Ereignisse und Elemente

| YUI | jQuery |
|---|---|
| `Y.one( '#menu' )` | `$( '#menu' )` |
| `node.ancestor( 'form' )` | `$el.closest( 'form' )` |
| `node.on( 'click', fn )` | `$el.on( 'click', fn )` |
| `Y.delegate( 'click', fn, '#list', 'li' )` | `$( '#list' ).on( 'click', 'li', fn )` |
| `e.halt()` | `e.preventDefault(); e.stopPropagation();` |
| `YAHOO.util.Dom.get( 'menu' )` | `document.getElementById( 'menu' )` |
| `YAHOO.util.Dom.getRegion( el )` | `el.getBoundingClientRect()` |

## Zurückgebliebene Einstellungen und Stylesheets

- Overrides von `ezjscore.ini`, die `ExternalScripts[yui*]`, `LocalScripts[yui*]`, `LocalScriptBasePath[yui*]` oder
  einen Abschnitt `[YUI3]` setzen, können aus den Einstellungen Ihrer Erweiterung gelöscht werden.
- CSS, das für die Klassennamen von YUI (`.yui-dt-*`, `.yui3-*`) geschrieben wurde, gestaltet jetzt nichts mehr.
  Entfernen Sie es und gestalten Sie den Ersatz mit den Komponenten der Administration
  ([admin4-Styling für Erweiterungsseiten](admin4-styling-fuer-erweiterungsseiten.md)).
- Template-Overrides von Kernel-Templates, die beim Entfernen umbenannt wurden, müssen ebenfalls umbenannt werden;
  die Referenz listet sie auf.

## Gleichzeitig ältere Releases unterstützen

Eine Erweiterung, die für mehrere Kernel-Versionen veröffentlicht wird, kann für alte Installationen einen Weg über
YUI behalten und Exponential UI verwenden, wenn es vorhanden ist. Prüfen Sie auf das Objekt, nicht auf eine
Versionsnummer:

```javascript
if ( window.Exp && Exp.io ) {
    Exp.io.call( 'mysite::latest', [nodeId] ).then( show );
} else if ( window.YUI ) {
    // the old code path, for installations before 6.0.15
}
```

Der Leitfaden von Exponential UI hat dazu einen Abschnitt („Keep the YUI version as the fallback“). Entfernen Sie den
alten Weg, wenn Sie diese Releases nicht mehr unterstützen.

## Ihre Arbeit prüfen

- Die Suche oben auf dieser Seite findet nichts.
- Auf jeder Seite der Erweiterung: keine Anfrage, die `yui` enthält, und in der Konsole ist `typeof YUI`
  `"undefined"`, während alles weiterhin funktioniert.

## Verwandte Seiten

- Zurück: [Von Inline-CSS zu Stylesheets](inline-css-in-stylesheets.md)
- Weiter: [Vom alten jQuery zu jQuery 4](altes-jquery-zu-jquery-4.md)
- [YUI aus Exponential entfernt](../../../bc/6.0/yui-entfernung.md)
- [jQuery 4 in der Administration, und YUI ist weg](../../../funktionen/6.0/jquery4-und-entfernung-von-yui.md)
- [Ajax mit Server-Funktionen von ezjscore](../teil-3-erweiterungsentwickler/ajax-mit-ezjscore-serverfunktionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
