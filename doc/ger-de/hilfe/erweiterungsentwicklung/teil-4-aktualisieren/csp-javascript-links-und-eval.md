---
title: "CSP: javascript:-Links, eval und String-Timer"
audience: developer, designer
category: extension-development
order: 150
summary: Die übrigen Arten von Inline-Code, die eine Content Security Policy blockiert - javascript:-Links, eval(), new Function(), String-Timer, als HTML eingefügte Skripte und JSONP - und wie Sie jede davon in einer alten Erweiterung ersetzen.
keywords: javascript:-Link, String-Timer, als HTML eingefügtes Skript, Upgrade, CSP, javascript: link, href javascript, eval, new Function, setTimeout string, unsafe-eval, innerHTML script, JSONP, upgrade
type: help
---
# CSP: javascript:-Links, eval und String-Timer

Teil 4, Kapitel 15. Nach Skriptblöcken und Event-Handler-Attributen bleiben einige kleinere Arten von Inline-Code
übrig. Jede wird von einer Policy ohne `'unsafe-inline'` oder `'unsafe-eval'` blockiert, und jede ist leicht zu
ersetzen, sobald Sie wissen, dass es sie gibt.

## Sie finden

```bash
cd extension/myoldext
grep -rnE "href\s*=\s*[\"']\s*javascript:|action\s*=\s*[\"']\s*javascript:" --include='*.tpl' design/
grep -rnE "\beval\s*\(|new\s+Function\s*\(|set(Timeout|Interval)\s*\(\s*[\"']" --include='*.js' --include='*.tpl' design/
grep -rnE "\.(html|append|prepend|after|before)\(\s*[^)]*<script|globalEval|callback=\?" --include='*.js' design/
```

## javascript:-Links

```text
<a href="javascript:void(0);" onclick="myOldExtToggle( 'details' );">Details</a>
<a href="javascript:history.back();">Back</a>
<a href="javascript:window.print();">Print</a>
```

Eine `javascript:`-URL ist Inline-Code. Ersetzen Sie jede danach, was sie tut:

| Alter Link | Ersatz |
|---|---|
| `href="javascript:void(0)"` mit einem `onclick` | ein `<button type="button">` mit einem data-Attribut, verarbeitet vom Click-Listener Ihrer Datei |
| `href="javascript:history.back()"` | ein echter Link zur vorherigen Seite, wenn Sie sie kennen (`{$node.parent.url_alias\|ezurl}`), oder eine Schaltfläche mit `data-<ext>-back` |
| `href="javascript:window.print()"` | eine Schaltfläche mit `data-<ext>-print` |
| `href="javascript:document.forms.x.submit()"` | eine echte Absenden-Schaltfläche im Formular |

Eine Schaltfläche ist das richtige Element für „auf dieser Seite etwas tun“; ein Link ist das richtige Element für
„irgendwohin gehen“. Das richtige zu verwenden, korrigiert auch das Verhalten bei Tastatur und Screenreader. Ein
`<button>` in einem Formular sendet das Formular ab, sofern es nicht `type="button"` hat.

```text
<button type="button" class="button" data-myoldext-toggle="details" aria-expanded="false" aria-controls="details">{'Details'|i18n( 'extension/myoldext' )}</button>
<div id="details" hidden>...</div>
```

```javascript
document.addEventListener( 'click', function( e )
{
    var button = e.target && e.target.closest ? e.target.closest( '[data-myoldext-toggle]' ) : null;
    if ( !button )
        return;
    var panel = document.getElementById( button.getAttribute( 'data-myoldext-toggle' ) );
    if ( panel )
    {
        panel.hidden = !panel.hidden;
        button.setAttribute( 'aria-expanded', panel.hidden ? 'false' : 'true' );
    }
} );
```

## eval() und new Function()

`eval()` und `new Function( '...' )` führen einen String als Code aus. Eine Policy blockiert sie, sofern sie nicht
`'unsafe-eval'` enthält, was die mitgelieferten Direktiven von Exponential nicht tun. Alter Code hat sie aus zwei
Gründen verwendet:

| Verwendung | Ersatz |
|---|---|
| JSON parsen: `eval( '(' + text + ')' )` | `JSON.parse( text )` |
| Eine Funktion über ihren Namen aufrufen: `eval( name + '()' )` | eine Zuordnung von Funktionen: `var actions = { save: save, reset: reset }; actions[name]();` |
| Einen Wert über einen Pfad lesen: `eval( 'config.' + key )` | `config[key]` |

Der JSON-Fall ist zugleich eine Sicherheitskorrektur: `eval()` führt aus, was immer der Server geschickt hat, und
`JSON.parse()` liest nur Daten.

## String-Timer

```javascript
// before: a string is code, like eval()
setTimeout( "myOldExtRefresh('" + id + "')", 1000 );

// after: a function
setTimeout( function() { myOldExtRefresh( id ); }, 1000 );
```

`setInterval()` funktioniert auf dieselbe Weise.

## Als HTML eingefügte Skripte

Alter Ajax-Code hat oft einen HTML-Block mit einem `<script>` darin geladen und eingefügt, im Vertrauen darauf, dass
jQuery das Skript ausführt:

```javascript
$( '#panel' ).html( response );   // response contains <script>...</script>
```

Unter der Policy wird die Ausführung dieses Inline-Skripts durch jQuery blockiert. Ändern Sie die Serverseite so,
dass sie nur Daten (JSON) oder nur Markup zurückgibt, und halten Sie den Code in Ihrer Datei:

```javascript
jQuery.getJSON( url, function( data )
{
    var panel = document.getElementById( 'panel' );
    panel.textContent = data.message;
} );
```

Mit ezjscore ist die Serverseite eine Server-Funktion, und der Client ruft sie über die JavaScript-API des Kernels
auf; siehe [Ajax mit Server-Funktionen von ezjscore](../teil-3-erweiterungsentwickler/ajax-mit-ezjscore-serverfunktionen.md).

## JSONP

JSONP (`callback=?` in einer jQuery-URL oder ein von Hand gebautes `<script src="...?callback=...">`) lädt ein Skript
von einem anderen Host. Unter `script-src 'self'` wird es blockiert, sofern dieser Host nicht erlaubt ist. Die meisten
Dienste, die JSONP angeboten haben, bieten heute JSON mit CORS an; verwenden Sie `fetch()` oder `jQuery.getJSON()`
ohne den Callback und fügen Sie den Host stattdessen zu `connect-src` hinzu.

## Erlauben, was wirklich erlaubt werden muss

Manches lässt sich nicht in Ihre eigenen Dateien verschieben, zum Beispiel das Skript eines Kartendienstes. Fügen Sie
dafür den genauen Host zur Policy der Siteaccesses hinzu, die ihn brauchen, und zwar in den Einstellungen, nicht indem
Sie die Policy für alle lockern. Wie die Einstellungen funktionieren, einschließlich zusätzlicher Quellen pro
Direktive, steht in [Content Security Policy](../../../funktionen/6.0/content-security-policy.md).

## Verwandte Seiten

- Zurück: [CSP: von Event-Handler-Attributen zu data-Attributen](csp-event-handler-zu-data-attributen.md)
- Weiter: [Von Inline-CSS zu Stylesheets](inline-css-in-stylesheets.md)
- [Content Security Policy für Erweiterungen](../teil-3-erweiterungsentwickler/content-security-policy-fuer-erweiterungen.md)
- [Content Security Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/CSP) auf MDN
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
