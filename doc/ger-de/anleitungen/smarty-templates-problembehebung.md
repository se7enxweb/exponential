# Smarty-Templates: Problembehebung und FAQ

Diese Seite ist für Template-Entwickler und Betreiber, deren `.stpl`-Seite nicht wie erwartet aussieht. Sie listet die
Fehlermeldungen, die die Brücke und Smarty erzeugen, jede mit ihrer Ursache und ihrer Behebung, die Probleme, die keine
Meldung geben, und eine FAQ. Jede Meldung hier trat beim Portieren des Media-Designs oder beim Bau der Brücke auf.

## Zuerst: die Meldung sehen

Ein `.stpl`, das scheitert, macht die Seite nicht kaputt. Seine Stelle bleibt leer, und:

- die Debug-Ausgabe bekommt einen Fehler mit der Bezeichnung `smarty` (und das Fehlerlog, `var/log/error.log`, eine
  Zeile);
- bei eingeschalteter Debug-Ausgabe bekommt die Seite einen HTML-Kommentar `<!-- smarty: <message> -->` dort, wo das
  Template gewesen wäre.

Also: Schalten Sie die Debug-Ausgabe für Ihre eigene Adresse ein ([die Exp-Debug-Leiste](../funktionen/6.0/exp-debug-leiste.md)),
laden Sie neu und sehen Sie im Reiter Messages der Leiste nach oder suchen Sie im Seitenquelltext nach `<!-- smarty:`.
Ohne Debug-Ausgabe sehen Sie im Fehlerlog nach:

```bash
grep -h 'smarty\|rendering .* failed' var/log/error.log | tail -20
```

(`var/<site>/log/` in einer Multisite-Installation, die ihre Logs getrennt hält.)

## Meldungen der Brücke

| Meldung | Ursache | Behebung |
|---|---|---|
| `rendering <file> failed: <reason>` | Smarty konnte das Template nicht kompilieren oder ausführen; der Grund ist Smartys Meldung oder eine der Meldungen unten | der Grund sagt es; siehe die Tabellen unten |
| `template not found: <file>` | der Stub nennt eine Datei, die fehlt: das `.stpl` wurde umbenannt, verschoben oder gelöscht, und ein kompilierter Stub zeigt noch darauf | die Template-Caches leeren (`php bin/php/ezcache.php --clear-tag=template --allow-root-user`) |
| `the Smarty library is not available (install smarty/smarty with Composer, or see [SmartySettings] LibraryPath in smarty.ini)` | Smarty 5 ist nicht im Autoload von Composer, und `LibraryPath` nennt keinen Checkout | installieren Sie es oder setzen Sie `LibraryPath` auf das Verzeichnis, das `src/Smarty.php` enthält; wird außerdem einmal als `Smarty 5 not found: ...` protokolliert |
| `not a Smarty template: <file>` | `exp_smarty_render` wurde mit einer Datei aufgerufen, die nicht auf `.stpl` endet | mit einem `.stpl` aufrufen |
| `invalid parameter name for <function>: <name>` | eine Template-Funktion wurde mit einem Parameternamen aufgerufen, der nicht aus Buchstaben, Ziffern und Unterstrichen besteht und mit einem Buchstaben beginnt | den Parameter umbenennen |
| `expinclude: missing parameter 'uri'` | `{expinclude}` ohne `uri` (oder mit einer leeren: ein `concat()`, das `''` ergab) | die URI angeben; den Wert prüfen, aus dem Sie sie bilden |
| `expfetch: missing parameter 'assign'`, `'module'`, `'function'` | `{expfetch}` ohne einen davon | ergänzen |
| `expset: missing parameter 'name'` | `{expset}` ohne `name` | ergänzen |
| `expsetblock: missing parameter 'variable'` | `{expsetblock}` oder `{expappendblock}` ohne `variable` | ergänzen |
| `<!-- smarty: unsupported characters in the template file name -->` | Name oder Pfad der `.stpl`-Datei enthält `'`, `\`, `{`, `}` oder einen Zeilenumbruch | die Datei umbenennen |

## Meldungen von Smarty

Smartys Kompilierfehler lauten:

```
Syntax error in template "file:/<path>/full/folder.stpl"  on line 12 "{foreach ($validation.attributes) as $item}" missing 'from' attribute
```

Die Datei, die Zeile und die Quellzeile werden angegeben; der Grund steht am Ende.

| Grund | Ursache | Behebung |
|---|---|---|
| `missing 'from' attribute` | ein `{foreach}` über einen Ausdruck (`{foreach fetch(...) as $x}`, `{foreach ($list\|append:'x') as $x}`): Smarty liest eine geklammerte Quelle als die Attributform `{foreach from=...}` | zuerst zuweisen: `{$__loop1 = fetch(...)}{foreach $__loop1 as $x}`. Der Konverter tut das für Sie |
| `unexpected closing tag` | ein schließendes Tag ohne öffnendes Tag: ein `.tpl`-Tag, das in einer Portierung von Hand stehen blieb (`{/section}`, `{/let}`, `{/default}`, `{/cache-block}`, `{/set-block}`), ein schließendes Tag, dessen öffnendes Tag Smarty als Text las (eine Klammer gefolgt von einem Leerzeichen, `{ if ...}`), oder ein öffnendes Tag, das entfernt wurde | das öffnende Tag finden; das `.tpl`-Tag in seine Smarty-Form umwandeln ([die Zuordnung](smarty-templates-konverter.md#2-was-er-schreibt)) |
| `unclosed {if} tag`, `unclosed '{if}' tag` | ein öffnendes Tag, das nie geschlossen wird, oder Tags, die verkehrt herum verschachtelt sind (`{if}{foreach}{/if}{/foreach}`) | in der richtigen Reihenfolge schließen |
| `unknown modifier 'x'` | kein Smarty-Modifier und kein Operator `x` in diesem Siteaccess: Die Erweiterung, die ihn registriert, ist hier nicht aktiv, oder ein Tippfehler | die Erweiterung im Siteaccess aktivieren oder den Namen korrigieren; nach dem Aktivieren PHP-FPM neu laden und Velocity neu starten (Namen werden einmal je Prozess nachgeschlagen) |
| `unknown tag 'x'` | keine Template-Funktion `x` in diesem Siteaccess, oder ein `.tpl`-Tag, das in der Datei stehen blieb (`{def}`, `{set}`, `{undef}`, `{let}`, `{set-block}`, `{cache-block}`, `{include uri=...}`) | die Smarty-Form verwenden (`{$x = ...}`, `{capture}`, `{expcacheblock}`, `{expinclude}`) |
| ` - Unexpected "("` und Ähnliches | Smarty-Syntax: oft eine `.tpl`-Gewohnheit, Modifier-Parameter in Klammern (`$x\|i18n('ctx')`) | `$x\|i18n:'ctx'` |
| ` - Unexpected ...` innerhalb von CSS oder JavaScript | eine `{`, auf die etwas anderes als Leerraum folgt, beginnt ein Tag: `body {margin:0}`, `function(){return 1;}` | `{ldelim}`, ein Leerzeichen nach der Klammer oder `{literal}...{/literal}` um den Code |
| `Unable to load 'file:<name>'` | Smartys eigenes `{include file=...}` fand die Datei nicht in `override/templates/` und `templates/` der Designs | den Pfad prüfen; für ein `.tpl` oder um Override-Regeln anzuwenden, `{expinclude uri='design:...'}` verwenden |

## PHP-Fehler innerhalb eines Renderns

Diese kommen aus dem Code, den ein Operator oder eine Funktion ausführt; die Meldung ist `rendering <file> failed:`,
gefolgt von der von PHP.

| Meldung | Ursache | Behebung |
|---|---|---|
| `in_array(): Argument #2 ($haystack) must be of type array, null given` | `contains` (oder ein anderer Array-Operator) lief mit einer `null`-Eingabe und einem Array-Parameter: `$list\|contains:$x`, wo `$list` nicht gesetzt ist, oder `contains(null, [...])`. Es war auch das Symptom einer Brücke vor Oktober 2026, die die Eingabe von `contains`, `implode`, `i18n` ... als Parameter übergab. | sicherstellen, dass die Eingabe ein Array ist (`{$list = []}` vor der Schleife, die es füllt); mit der aktuellen Brücke das Template erneut konvertieren, wenn es für diese noch die alten Formen `op(null, ...)` enthält |
| `Array to string conversion` (eine Warnung, die Seite zeigt `Array`) | ein Array wurde mit `{$x}` gedruckt oder dort übergeben, wo Text erwartet wird. Meist lief ein Smarty-Modifier statt des gleichnamigen Operators: Smartys `explode` nimmt das Trennzeichen zuerst, `implode` ist veraltet, `count` zählt eine Zeichenkette als 1 | `exp_explode`, `exp_implode`, `exp_count` verwenden; ein Element drucken (`$x.0`) oder das Array mit `exp_implode` verbinden |
| `Call to undefined method ...` oder `Undefined ... setKeyValue` auf jeder Seite, `.tpl`-Seiten eingeschlossen | eine Kopie einer Kernel-Klasse in einem Erweiterungsverzeichnis hat den Platz des Kernels eingenommen (siehe „Überschreibungen von Kernel-Klassen“ unten) | die Kopie entfernen, die Autoloads neu erzeugen |
| `Operator '<name>' is not registered` (eine Template-Warnung, kein Scheitern des Renderns) | der Operator existiert in dem Siteaccess, für den das Template kompiliert wurde, aber nicht in diesem | die Erweiterung hier aktivieren oder das Template aus diesem Siteaccess heraushalten |

## Probleme ohne Meldung

**Die ganze Seite ist leer oder weiß.** Das Pagelayout selbst ist gescheitert. Bei ausgeschalteter Debug-Ausgabe druckt
es überhaupt nichts. Schalten Sie die Debug-Ausgabe ein oder lesen Sie das Fehlerlog. Häufige Ursachen: Das `.stpl`
des Pagelayouts hat nach einer Handänderung einen Syntaxfehler; Smarty ist nicht installiert; das Kompilierverzeichnis
ist nicht beschreibbar (nächster Punkt).

**Eine Seite funktioniert unter Apache und scheitert unter Velocity, oder umgekehrt.** Velocity läuft als `root`,
PHP-FPM als Benutzer der Website, und beide schreiben `<cache directory>/smarty/compile/` und
`<cache directory>/smarty/functions/`. Ein Verzeichnis, das der eine angelegt hat, ist für den anderen womöglich nicht
beschreibbar: Das Rendern scheitert mit einer Meldung über eine Datei, die nicht geschrieben werden kann. Leeren Sie den
Smarty-Cache als Benutzer der Website (`php bin/php/ezcache.php --clear-id=smarty`) oder übergeben Sie die
Verzeichnisse diesem Benutzer.

**Eine `.stpl`-Seite ist langsam.** Messen Sie zuerst, unter gleichen Bedingungen (dieselben Caches, derselbe
Debug-Zustand, ein Server, der nicht überlastet ist), mit der Skriptzeit der Debug-Leiste und dem Median von zehn
Anfragen. Prüfen Sie dann: Wird `{expinclude}` mit `name=` verwendet, wo es nicht nötig ist (das Include läuft dann über
das Template-System, statt als Subtemplate zu laufen); ist das Pagelayout gecacht (`{expcacheblock}`,
`[TemplateSettings] TemplateCache=enabled`); wird die Website von PHP-FPM mit kaltem Opcode-Cache ausgeliefert. Alles
dazu: [Smarty-Templates: Leistung (Geschwindigkeit)](smarty-templates-leistung.md).

**Eine Änderung an einem `.stpl` zeigt sich nicht.** Der Reihe nach: Der Inhalts-View-Cache liefert die Seite (leeren
Sie `--clear-id=content` oder schalten Sie `ViewCaching` ab, während Sie arbeiten); ein `{expcacheblock}` darum ist
gecacht (leeren Sie `--clear-tag=template`); der Ganzseiten-HTTP-Cache beantwortet anonyme Besucher
(`X-Exp-Cache: HIT`; sehen Sie mit eingeschalteter Debug-Ausgabe nach, die ihn umgeht);
`[SmartySettings] CompileCheck=disabled` (leeren Sie `--clear-id=smarty`); die Override-Regel wählt Ihre Datei nicht
(der Template-Baum der Debug-Leiste nennt die verwendete Datei).

**Ein neues `.stpl` wird nicht verwendet.** Eine neue Template-Datei oder Regel erfordert, dass die Template-Caches
geleert werden: Der Override-Cache hält die Liste der Dateien. Zeigt die Regel auf eine Datei in `templates/` statt in
`override/templates/`, braucht `MatchFile` davor `../../templates/` (es wird unterhalb von `override/templates/`
nachgeschlagen).

**Eine Variable ist in `node_view_gui`, `attribute_view_gui` ... leer.** Eine Template-Funktion läuft in einem eigenen
Namensraum, der nur die Parameter enthält, die Sie übergeben. Übergeben Sie den Wert als Parameter: `{node_view_gui
view='line' content_node=$child highlight=$search_text}` macht `$highlight` zu einer Variablen des Line-Templates.

**Eine in einem eingebundenen `.stpl` gesetzte Variable ist danach nicht sichtbar.** Sie ist nach `{expinclude}` ohne
`name=` sichtbar. Sie ist es nicht nach `{expinclude ... name='x'}` (ein eigener Namensraum) oder nach Smartys
`{include file=...}` (Smarty behält die Variablen der eingebundenen Datei für sich).

**Ein Wert ist immer der Standardwert.** Ein konvertiertes `.tpl`-`$x|first_set('d')` ist `first_set('d')`, weil das
`.tpl` `$x` übergangen hat; siehe [was nach dem Konvertieren zu prüfen ist](smarty-templates-konverter.md#3-seine-hinweise-und-was-nach-dem-konvertieren-zu-prüfen-ist).

**Der Text `null` erscheint.** Ein nacktes Wort in einem `.tpl` ist eine Zeichenkette, und der Konverter behält das bei
(`'null'`).

**Ein Cache-Block zeigt den Inhalt einer anderen Seite.** Ihm fehlen die Schlüssel, die ihn unterscheiden, oder zwei
`{expcacheblock}` in einer Datei haben dieselbe `id`. Siehe [Caching und Leistung](smarty-templates-caching.md#expcacheblock-im-detail).

**Ein Siteaccess verlangt von jedem Besucher eine Anmeldung.** Die Rolle Anonymous hat keine `user/login`-Richtlinie für
ihn: Fügen Sie den Siteaccess der SiteAccess-Einschränkung der Richtlinie hinzu.

**Überschreibungen von Kernel-Klassen aus inaktiven Erweiterungen.** Der Autoload-Generator
(`php bin/php/ezpgenerateautoloads.php`) sieht sich jedes Verzeichnis unterhalb von `extension/` an, aktiv oder nicht,
und jede Datei dort, die eine Kernel-Klasse deklariert, wird in `var/autoload/ezp_override.php` zu einer Überschreibung
dieser Klasse, für jeden Siteaccess. Eine Erweiterung, die etwa eine Kopie von `eZTemplateDesignResource` mitbringt,
ersetzt die Klasse des Kernels, auch wenn kein Siteaccess sie aktiviert, und eine veraltete Kopie kann beim nächsten
Bereitstellen die ganze Website lahmlegen. Die Smarty-Unterstützung braucht keine solche Kopie: Sie steckt im Kernel.
Enthält ein Erweiterungsverzeichnis Kopien von Kernel-Klassen, die Sie nicht wollen, entfernen Sie es (oder tragen Sie
es in `.autoloadignore` ein), erzeugen Sie die Autoloads neu und prüfen Sie, dass `var/autoload/ezp_override.php` nur
die Überschreibungen auflistet, die Sie beabsichtigen.

## FAQ

**Muss ich eine Erweiterung aktivieren?** Nein. Die `.stpl`-Unterstützung steckt im Kernel (`lib/smarty`) und
funktioniert in jedem Siteaccess.

**Welche Smarty-Version?** Smarty 5, unter PHP 8. Smarty 3 und 4 funktionieren nicht: Die Brücke ist eine
Smarty-5-Erweiterung.

**Kann ein `.tpl` ein `.stpl` einbinden, und umgekehrt?** Ja, in beide Richtungen: `{include uri='design:x.stpl'}` in
einem `.tpl`, `{expinclude uri='design:x.tpl'}` in einem `.stpl`. Variablen fließen wie bei jedem Include.

**Funktioniert es in der Administration, in Mail-Templates, in PDF-Exporten?** Überall, wo das Template-System ein
Template lädt: ein `.stpl`, das von einer Override-Regel gewählt oder in einem Include genannt wird. Mail-Templates
funktionieren, weil die Variablen, die ein `.stpl` zuweist (`$subject`, `$email_receiver` ...), zurückgeschrieben
werden, damit das Modul sie liest.

**Kann ich Smartys Template-Vererbung (`{extends}`, `{block}`) verwenden?** Smarty findet andere `.stpl`-Dateien in den
Verzeichnissen `override/templates/` und `templates/` der Designs, kann es also im Prinzip; sie wurde in der
Media-Portierung nicht verwendet und ist von den Tests nicht abgedeckt. Der Design-Fallback und Override-Regeln sind
der Weg von Exponential, ein Template zu variieren.

**Wird die Ausgabe automatisch maskiert?** Nein, wie in einem `.tpl`: Maskieren Sie mit `|wash`. Siehe
[Sicherheit und Maskierung](smarty-templates-sicherheit.md).

**Kann ich eigene Smarty-Plugins registrieren?** Nicht über eine Einstellung. Schreiben Sie stattdessen einen
Template-Operator oder eine Template-Funktion: Sie funktionieren in `.tpl` und `.stpl` gleichermaßen. Siehe
[Operatoren und Funktionen für .stpl schreiben](smarty-templates-plugins.md).

**Muss ich nach dem Bearbeiten eines `.stpl` etwas neu starten?** Nein, mit `CompileCheck=enabled`. Nach dem Ändern
einer INI-Einstellung, einer Operator-Klasse oder der Liste aktiver Erweiterungen: Leeren Sie den INI-Cache, laden Sie
PHP-FPM neu und starten Sie Velocity neu (`./console exp:velocity deploy --allow-root-user` erledigt alles).

**Lohnt sich `CompileCheck=disabled`?** Es erspart Smarty eine Dateiprüfung je Template und Anfrage. Verwenden Sie es
in der Produktion, wenn Templates sich nur mit einem Deployment ändern, und leeren Sie `--clear-id=smarty` nach jeder
Template-Änderung.

**Wo sind die kompilierten Dateien?** `<cache directory>/smarty/compile/` (die von Smarty) und
`<cache directory>/smarty/functions/` (die erzeugten Aufrufe von Template-Funktionen), zum Beispiel
`var/site/cache/smarty/`. Sie werden mit den Template-Caches geleert.

**Warum ist `exp_path` nicht der Operator `path`?** Ein Operator, dessen eigener Name mit `exp_` beginnt, wird unter
diesem Namen aufgerufen. Nur bei einem Namen, der nicht registriert ist, wird das `exp_` entfernt.

**Ändert der Konverter meine `.tpl`-Dateien?** Nein. Er schreibt `.stpl`-Dateien unterhalb von `--to` (standardmäßig
neben das `.tpl`) und berührt nie eine Quelle. Ein Ziel, das er nicht selbst geschrieben hat oder das bearbeitet wurde,
nachdem er es geschrieben hat, wird übersprungen, sofern nicht `--force` angegeben ist.

**Kann ich von `.stpl` zu `.tpl` zurück?** Ja: `./console exp:template:convert:stpl-to-tpl`
([Teil 6 der Anleitung zum Konverter](smarty-templates-konverter.md#6-smarty-templates-zurück-in-tpl-konvertieren)).

**Die Konvertierung endet mit `FAIL`. Was sagt der Exit-Code?** Die Befehle von `exp:template:convert` enden mit
`PASS` und Exit-Code 0, wenn jedes Template konvertiert, unverändert gelassen oder übersprungen wurde (`SKIP`-Zeilen und
`NOTE`-Zeilen lassen einen Lauf nicht scheitern). Andernfalls:

| Exit-Code | Letzte Zeile, zum Beispiel | Ursache und Behebung |
|---|---|---|
| 1 | `FAIL tpl->stpl 516 templates: ... 1 errors; ...`, nach einer Zeile `ERROR <template>: ...` | ein Template konnte nicht gelesen oder konvertiert werden, oder das Ergebnis kompiliert nicht mit Smarty (tpl zu stpl) bzw. lässt sich nicht parsen (stpl zu tpl); dafür wurde nichts geschrieben. Korrigieren Sie die Quelle, die die `ERROR`-Zeile nennt, und konvertieren Sie erneut |
| 2 | `FAIL --from must be a design directory: extension/no-such-design (or give --file)`, `FAIL --from-engine is needed: tpl, stpl` | falsche Optionen: Prüfen Sie die Pfade und geben Sie bei `exp:template:convert` `--from-engine` und `--to-engine` an |
| 3 | `FAIL <from>-><to>: converting ... is not available yet` | die Installation hat keinen Konverter für diese Richtung (`site.ini [TemplateBridgeSettings] ConverterList`) |

Jede Zeile und jede Option ist in [Templates zwischen Template-Engines konvertieren](template-konvertierung.md#3-ausgabe-und-exit-codes)
erklärt.

## Verwandte Seiten

- [Smarty-Templates: .stpl-Templates schreiben und ausführen](smarty-templates.md#11-fehlersuche)
- [Operator-Referenz](smarty-templates-operator-referenz.md), [Template-Funktionen und Blöcke](smarty-templates-funktionen.md)
- [Ein Design migrieren](smarty-templates-migrieren.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
