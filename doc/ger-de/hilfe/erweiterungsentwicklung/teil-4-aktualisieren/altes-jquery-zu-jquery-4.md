---
title: Vom alten jQuery zu jQuery 4
audience: developer, designer
category: extension-development
order: 180
summary: Laden Sie das jQuery 4, das ezjscore bereitstellt, statt einer mitgelieferten alten Kopie, und ersetzen Sie die jQuery-Aufrufe, die die Versionen 3 und 4 entfernt haben, mit der Liste, die der Layout-Editor durchgegangen ist, und jQuery Migrate als Sicherheitsnetz.
keywords: entfernte APIs, Event-Kurzformen, Upgrade, jQuery 4, jQuery Migrate, jQuery UI 1.14, ezjsc::jquery, noConflict, removed APIs, .live, .size, .bind, event shorthands, jQuery.trim, upgrade
type: help
---
# Vom alten jQuery zu jQuery 4

Teil 4, Kapitel 18. Die Administration von Exponential läuft auf jQuery 4.0.0, mit jQuery Migrate 4.0.2 und
jQuery UI 1.14.2, alle geladen über den Packer-Schlüssel `ezjsc::jquery` von ezjscore (und `ezjsc::jqueryUI`). Alte
Erweiterungen bringen oft ihr eigenes jQuery 1.x oder 2.x mit oder rufen Funktionen auf, die jQuery 3 und 4 entfernt
haben. Dieses Kapitel behandelt beides. Die Zusammenfassung der Änderung im Kernel ist
[jQuery 4 in der Administration, und YUI ist weg](../../../../eng-us/features/6.0/jquery4-and-yui-removal.md).

## Schritt 1: das jQuery der Seite verwenden, nicht Ihre eigene Kopie

Eine Erweiterung, die `jquery-1.7.2.min.js` mitliefert und neben dem des Kernels lädt, bringt zwei jQuery-Versionen
auf die Seite. Die zweite ersetzt die globalen `jQuery` und `$`, und die auf der ersten registrierten Plugins
verschwinden, was je nach Reihenfolge die Skripte des Kernels oder Ihre eigenen bricht.

Kopien finden:

```bash
grep -rlE 'jQuery JavaScript Library v[0-9]|jQuery v[0-9]' extension/myoldext/design/
```

Löschen Sie die Kopie (nachdem Sie geprüft haben, dass ihre Lizenzdatei dort bleibt, wo eine verbleibende Bibliothek
sie braucht), und laden Sie die gemeinsame vor Ihrer Datei:

```text
{ezscript_require( array( 'ezjsc::jquery', 'myoldext.js' ) )}
```

Entfernen Sie außerdem `jQuery.noConflict( true )` aus Ihrem Code. Mit Ihrer eigenen Kopie war es harmlos; mit dem
jQuery der Seite entfernt es `jQuery` und `$` der Seite. se7enxweb/explayouts_ui_api hat in Commit 8273a03 genau das
für den Layout-Editor getan: „seine eigenen Kopien sind weg, jQuery.noConflict(true) ist weg (es hätte das jQuery der
Seite entfernt)“.

## Schritt 2: entfernte Aufrufe ersetzen

jQuery 1.9, 3 und 4 haben eine Reihe von Funktionen entfernt, die jahrelang als veraltet galten, und andere als
veraltet markiert (darunter `.bind()`, `jQuery.proxy()` und die Event-Kurzformen). Eine veraltete Funktion läuft noch,
aber jQuery Migrate meldet sie, und eine spätere Version wird sie entfernen, ersetzen Sie also beide Arten. Die, die
alte Erweiterungen am häufigsten verwenden:

| Alt | Ersatz |
|---|---|
| `.live( 'click', fn )`, `.die()` | `$( document ).on( 'click', selector, fn )`, `.off()` |
| `.bind( 'click', fn )`, `.unbind()`, `.delegate()`, `.undelegate()` | `.on()`, `.off()` |
| `.size()` | `.length` |
| `.andSelf()` | `.addBack()` |
| `jQuery.browser` | Feature-Erkennung |
| `jQuery.trim( s )` | `s.trim()` (`String.prototype.trim`) |
| `jQuery.isArray( a )` | `Array.isArray( a )` |
| `jQuery.parseJSON( s )` | `JSON.parse( s )` |
| `jQuery.camelCase( s )` | Ihre eigene einzeilige Funktion |
| `jQuery.proxy( fn, ctx )` | `fn.bind( ctx )` |
| `jQuery.isFunction( f )` | `typeof f === 'function'` |
| `jqXHR.success( fn )`, `.error( fn )`, `.complete( fn )` | `.done( fn )`, `.fail( fn )`, `.always( fn )` |
| Event-Kurzformen `.click( fn )`, `.change( fn )`, `.submit( fn )` | `.on( 'click', fn )` und so weiter; `.trigger( 'click' )` zum Auslösen |
| `.attr( 'checked', true )`, `.attr( 'disabled', false )` | `.prop( 'checked', true )`, `.prop( 'disabled', false )` |
| `$( '<div>' ).load( url, fn )` für Skripte | `jQuery.ajax()` mit JSON; keine Skripte in Antworten ([CSP: javascript:-Links, eval und String-Timer](csp-javascript-links-und-eval.md)) |

Der Commit des Layout-Editors listet die auf, auf die er gestoßen ist: „jqXHR.success, jQuery.camelCase,
jQuery.proxy, die Event-Kurzformen, attr(name, false) wie bisher als removeAttr beibehalten“.

Sie finden:

```bash
grep -rnE '\.(live|die|bind|unbind|delegate|undelegate|size|andSelf)\(|jQuery\.(browser|trim|isArray|parseJSON|camelCase|proxy|isFunction)\b|\$\.(trim|isArray|parseJSON|camelCase|proxy|isFunction)\b|\.(success|error|complete)\(' --include='*.js' --include='*.tpl' extension/myoldext/design/
```

Manche Treffer sind falsch: `.error(` kann eine Methode Ihres eigenen Objekts sein, `.bind(` kann JavaScripts eigenes
`Function.prototype.bind` sein. Lesen Sie jede Zeile.

## Schritt 3: jQuery Migrate als Sicherheitsnetz, nicht als Lösung

jQuery Migrate stellt viele entfernte Funktionen wieder her und schreibt in seinem meldenden Build für jede
Verwendung eine Warnung in die Konsole. ezjscore lädt standardmäßig den stillen Build, „damit älterer Code
weiterläuft, ohne die Konsole zu füllen; der meldende Build ist nur eine Einstellung entfernt“ (siehe die oben
verlinkte Seite des Kernels).

Verwenden Sie es so:

1. Schalten Sie auf Ihrer Entwicklungskopie auf den meldenden Build um (die Einstellung
   `LocalScripts[jqueryMigrate]` in `ezjscore.ini` von ezjscore nennt die Datei).
2. Verwenden Sie jede Seite und jede Funktion Ihrer Erweiterung bei geöffneter Konsole.
3. Beheben Sie jede „JQMIGRATE“-Warnung, die auf Ihre Datei zeigt.
4. Prüfen Sie mit ausgeschaltetem Migrate: Wenn `LocalScripts[jqueryMigrate]` leer bleibt, wird jQuery 4 allein
   geladen.

Wenn Ihre Erweiterung ohne Migrate funktioniert, funktioniert sie auch weiter, wenn eine Installation es weglässt.

## Schritt 4: jQuery UI

jQuery UI 1.14 läuft auf jQuery 4. Die Widgets haben ihre Namen behalten, aber einige Optionen und die alten Klassen
des CSS-Frameworks haben sich zwischen 1.8 oder 1.10 und 1.14 geändert. Laden Sie es über `ezjsc::jqueryUI`, nach
`ezjsc::jquery`, und testen Sie jedes Widget, das Ihre Erweiterung verwendet. Für Dialoge und Datumsauswahl ziehen Sie
stattdessen `exp::dialog` und `exp::datepicker` von Exponential UI in Betracht, die die Administration selbst
verwendet ([Von YUI zu expui](yui-zu-expui.md)).

## Schritt 5: Plugins

Ein mitgeliefertes Plugin (eine Lightbox, eine Tooltip-Bibliothek), das für jQuery 1.x geschrieben wurde, kann intern
die entfernten Aufrufe verwenden. Suchen Sie zuerst nach einem neueren Release des Plugins. Wenn es keines gibt und
die Lizenz es erlaubt, korrigieren Sie es an Ort und Stelle und halten Sie oben in der Datei und in Ihrem Changelog
fest, was Sie geändert haben.

## Ein echtes Beispiel, von Anfang bis Ende

Der Layout-Editor ist eine Single-Page-Anwendung, die jQuery 2.2.4 und jQuery UI 1.10.4 in ihrem Bündel mitbrachte.
Seine Umstellung auf das jQuery 4 der Seite wird in
[Fallstudie: der Layout-Editor auf jQuery 4](fallstudie-der-layout-editor-auf-jquery-4.md) erzählt. Der Commit hält
fest, wie sie überprüft wurde: „durch Verwendung auf Exponential Velocity und PHP-FPM in den Designs admin, admin2 und
admin3: ein Layout öffnen, die Menüs, Duplizieren, Löschen über das Modal, Sortieren per Drag, Blöcke aus der Palette
hinzufügen, Bearbeiten im Modal, der Code-Editor, die Tabs der Seitenleiste, Inline-Bearbeitung und Verwerfen, ohne
Fehler, ohne fehlgeschlagene Anfrage und ohne Warnung von jQuery Migrate.“ Diese Liste ist ein gutes Vorbild für Ihren
eigenen Test.

## Verwandte Seiten

- Zurück: [Von YUI zu expui](yui-zu-expui.md)
- Weiter: [admin4-Styling für Erweiterungsseiten](admin4-styling-fuer-erweiterungsseiten.md)
- [jQuery 4 in der Administration, und YUI ist weg](../../../../eng-us/features/6.0/jquery4-and-yui-removal.md)
- [Upgrade-Leitfaden für jQuery 4.0](https://jquery.com/upgrade-guide/4.0/) und [Upgrade-Leitfaden für jQuery 3.0](https://jquery.com/upgrade-guide/3.0/) auf jquery.com
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
