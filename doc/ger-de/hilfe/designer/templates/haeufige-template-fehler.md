---
title: Häufige Template-Fehler
audience: designer, developer
category: templates
order: 350
summary: Die Template-Fehler, die die meisten Bugs, Sicherheitslücken und langsamen Seiten verursachen, jeweils mit Anzeichen und Korrektur - nicht maskierte Ausgabe, Fetch in Schleifen, fehlende Limits, Cache-Schlüssel, Benutzerdaten in gecachten Ansichten, Anführungszeichen um ezurl, Variablenkollisionen, fehlendes has_content, bearbeitete mitgelieferte Templates, neue Templates, die nicht erscheinen.
keywords: häufige Fehler, Template-Fehler, Fallstricke, nicht maskierte Ausgabe, Fetch in Schleife, Cache-Schlüssel, Anführungszeichen bei ezurl, Variablenkollision, has_content, Cache der Template-Overrides, Fehlersuche in Templates, common mistakes, template bugs, pitfalls, unwashed output, fetch in loop, cache key, ezurl quotes, variable clash, template override cache, troubleshooting templates
type: help
---
# Häufige Template-Fehler

Diese Seite ist für Designer, die die üblichen Fallen vermeiden wollen. Jeder Fehler hat ein Anzeichen, sodass Sie die
Seite auch nutzen können, um die Ursache eines Problems zu finden.

## Inhalt

1. [Ausgabe und Sicherheit](#ausgabe-und-sicherheit)
2. [Daten und Geschwindigkeit](#daten-und-geschwindigkeit)
3. [Caching](#caching)
4. [Syntax](#syntax)
5. [Dateien und Caches](#dateien-und-caches)

## Ausgabe und Sicherheit

| Fehler | Anzeichen | Korrektur |
|---|---|---|
| einen Wert ohne `\|wash` ausgeben | HTML aus Inhalten zerstört die Seite; Einschleusen von Skripten möglich | bei der Ausgabe maskieren ([Wash und Maskierung](../../../../eng-us/help/designers/templates/wash-and-escaping.md)) |
| die Ausgabe von Rich Text maskieren | Tags als Text angezeigt | die Ausgabe von `attribute_view_gui` nicht maskieren |
| doppelt maskieren | `&amp;amp;` auf der Seite | einmal maskieren, am Ende |
| Inline-`<script>` oder `onclick` | unter einer Content Security Policy blockiert | Skriptdateien ([Kein Inline-JavaScript](../../../../eng-us/help/designers/theme-design/no-inline-javascript.md)) |

## Daten und Geschwindigkeit

| Fehler | Anzeichen | Korrektur |
|---|---|---|
| ein Fetch innerhalb eines `foreach` | langsame Seiten, viele Abfragen in der Debug-Ausgabe | ein Fetch vor der Schleife ([Fetch-Leistung](fetch-leistung.md)) |
| ein Listen-Fetch ohne `limit` | sehr langsame Seiten bei großen Ordnern | immer ein `limit`, blättern |
| `$node.children` für ein Menü lesen | alle Kinder mit allen Daten geladen | ein Fetch mit `limit` und `class_filter_array` |
| ein leeres Feld zeichnen | leere Wrapper, verirrte Beschriftungen | zuerst `has_content` prüfen |

## Caching

| Fehler | Anzeichen | Korrektur |
|---|---|---|
| Benutzerdaten in einer Vollansicht im View-Cache | ein Besucher sieht den Namen eines anderen | in das Pagelayout verschieben oder den Cache variieren ([Caching für Designer](../../../../eng-us/help/designers/theme-design/caching-for-designers.md)) |
| ein Cache-Block ohne die aktuelle Seite in seinen Schlüsseln | das Menü markiert die falsche Seite | `$module_result.uri` oder die Knoten-ID zu den Schlüsseln hinzufügen |
| `csp_nonce()` in gecachter Ausgabe | Skripte bei späteren Besuchen blockiert | Skriptdateien; Nonce nur außerhalb von Caches |
| `currentdate()` oder `rand()` in gecachter Ausgabe | Datum oder Zufallswert eingefroren | außerhalb gecachter Teile berechnen |

## Syntax

| Fehler | Anzeichen | Korrektur |
|---|---|---|
| Anführungszeichen um `ezurl`: `href="{$x\|ezurl}"` | `href=""/path""`, fehlerhaftes Attribut | `href={$x\|ezurl}` schreiben oder `ezurl( 'no' )` innerhalb Ihrer Anführungszeichen ([URL-Operatoren](../../../../eng-us/help/designers/templates/url-operators.md)) |
| `$a == $b` | Parse-Fehler | `eq( $a, $b )` |
| `{def}` einer vorhandenen Variablen | Warnung, Wert des Einbindenden überschrieben | Präfixe und `{undef}` ([Variablen und def](../../../../eng-us/help/designers/templates/variables-and-def.md)) |
| Zeichenketten mit `gt` vergleichen | unerwartete Ergebnisse | `gt` vergleicht bei nicht numerischen Zeichenketten die Längen ([Logik- und Vergleichsoperatoren](logik-und-vergleichsoperatoren.md)) |
| ein Operator aus einer inaktiven Erweiterung | „Operator '...' is not registered“ in der Debug-Ausgabe, nichts ausgegeben | die Erweiterung aktivieren oder einen anderen Operator verwenden |

## Dateien und Caches

| Fehler | Anzeichen | Korrektur |
|---|---|---|
| ein Template in `design/standard` oder einem mitgelieferten Theme bearbeiten | Änderung beim Upgrade verloren | es in Ihr Design kopieren ([Die Design-Kaskade](../../../../eng-us/help/designers/theme-design/the-design-cascade.md)) |
| ein neues Template oder ein neuer Override erscheint nicht | altes Template weiter verwendet oder leerer Inhaltsbereich | `template-override` leeren ([Ein Design debuggen](../../../../eng-us/help/designers/theme-design/debugging-a-design.md)) |
| ein Override mit einem Tippfehler in `MatchFile` | Regel ignoriert, Eintrag im Fehlerlog | den Pfad unter `override/templates/` prüfen |
| zwei Overrides für dieselbe Quelle | der falsche gewinnt | `Priority` ([Override-Reihenfolge](../../../../eng-us/help/designers/templates/override-ordering.md)) |

## Verwandte Seiten

- [Template-Styleguide](../../../../eng-us/help/designers/templates/template-style-guide.md)
- [Template-Debugging](../../../../eng-us/help/designers/templates/template-debugging.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
