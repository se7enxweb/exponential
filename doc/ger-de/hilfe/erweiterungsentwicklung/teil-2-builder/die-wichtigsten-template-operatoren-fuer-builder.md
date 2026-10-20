---
title: Die Template-Operatoren, die Builder am häufigsten nutzen
audience: developer, designer, app-creator
category: extension-development
order: 140
summary: Ein Arbeitssatz von etwa fünfzig Template-Operatoren - Escaping, Zeichenketten, Arrays, Logik, Zahlen, Datumsangaben, Übersetzung, URLs und Einstellungen - jeder mit einem Beispiel und der Ausgabe, die die Template-Engine erzeugt hat.
keywords: Template-Operatoren, Escaping, Zeichenketten, Arrays, Datum, Übersetzung, URLs, template operators, wash, shorten, concat, upcase, count, implode, explode, contains, choose, cond, first_set, is_set, l10n, datetime, i18n, ezurl, ezimage, ezroot, ezdesign, ezini, sum, div
type: help
---
# Die Template-Operatoren, die Builder am häufigsten nutzen

Teil 2, Kapitel 14. Die Template-Sprache hat Hunderte von Operatoren, aber einige Dutzend erledigen fast die ganze
Arbeit. Dieses Kapitel ordnet sie nach Aufgaben. **Jede Ausgabezeile unten wurde von der Template-Engine des Kernels**
aus der gezeigten Eingabe erzeugt, mit diesen definierten Variablen:

```text
{def $text = '  Hello <b>World</b>  '
     $list = array( 3, 1, 2 )
     $when = 1760745600}
```

Die vollständige Liste, mit der Erweiterung, aus der jeder Operator stammt, ist [Eingebaute Template-Operatoren](../teil-8-referenz/referenz-der-template-operatoren.md).

## Escaping: immer

| Eingabe | Ausgabe |
|---|---|
| `{$text\|wash}` | `  Hello &lt;b&gt;World&lt;/b&gt;  ` |
| `{$text\|wash( 'xhtml' )}` | dasselbe (`xhtml` ist der Standard) |
| `{'x"y'\|wash( 'javascript' )}` | `x\042y` |

`wash` wandelt `<`, `>`, `&` und Anführungszeichen in Entities um, sodass Text von Menschen nicht zu HTML werden kann.
Verwenden Sie `wash( 'javascript' )` für einen Wert, der in eine JavaScript-Zeichenkette in einem Data-Attribut oder
einem JSON-Block geschrieben wird.

## Zeichenketten

| Eingabe | Ausgabe |
|---|---|
| `{$text\|trim\|upcase\|wash}` | `HELLO &lt;B&gt;WORLD&lt;/B&gt;` |
| `{'Some Title'\|downcase}` | `some title` |
| `{'some title'\|upfirst}` | `Some title` |
| `{'some title'\|upword}` | `Some Title` |
| `{'a long sentence that goes on'\|shorten( 12 )}` | `a long se...` |
| `{concat( 'a', '-', 'b' )}` | `a-b` |
| `{'x'\|append( 'y' )}` | `xy` |
| `{'a,b,c'\|explode( ',' )\|count}` | `3` |

`shorten( <length> )` kürzt auf so viele Zeichen, einschließlich der `...`.

## Arrays

| Eingabe | Ausgabe |
|---|---|
| `{$list\|count}` | `3` |
| `{$list\|implode( ',' )}` | `3,1,2` |
| `{$list\|sort\|implode( ',' )}` | `1,2,3` |
| `{array( 'x', 'y' )\|reverse\|implode( '' )}` | `yx` |
| `{array( 1, 2 )\|merge( array( 3 ) )\|implode( ',' )}` | `1,2,3` |
| `{array( 1, 2, 3 )\|extract( 1 )\|implode( ',' )}` | `2,3` |
| `{$list\|contains( 2 )\|choose( 'no', 'yes' )}` | `yes` |

`choose( <for false>, <for true> )` wählt nach einem Wahrheitswert, das erste Argument für falsch.
`cond( <test>, <value>, ... )` ist die andere Art zu wählen, mit dem Test zuerst.

## Logik und Vergleich

| Eingabe | Ausgabe |
|---|---|
| `{'a'\|eq( 'a' )}` | `1` |
| `{and( true(), false() )}` | (leer: falsch gibt nichts aus) |
| `{or( false(), true() )}` | `1` |
| `{not( true() )}` | (leer) |
| `{is_set( $nothing )\|choose( 'unset', 'set' )}` | `unset` |
| `{first_set( $nothing, 'fallback' )}` | `fallback` |

`first_set` liefert das erste Argument, das definiert ist: der saubere Weg, einen Standardwert anzugeben.

## Zahlen

| Eingabe | Ausgabe |
|---|---|
| `{sum( 1, 2, 3 )}` | `6` |
| `{mul( 2, 3 )}` | `6` |
| `{div( 10, 4 )}` | `2.5` |
| `{mod( 10, 4 )}` | `2` |
| `{1234.5\|l10n( 'number' )}` | `1,234.50` (eng-US) |

Es gibt auch `sub`, `inc`, `dec`, `min`, `max`, `round`, `floor` und `ceil`.

## Datumsangaben

| Eingabe | Ausgabe (eng-US) |
|---|---|
| `{$when\|l10n( 'shortdate' )}` | `10/17/2025` |
| `{$when\|datetime( 'custom', '%Y-%m-%d' )}` | `2025-10-17` |

`l10n` formatiert für das Gebietsschema des Siteaccess (`shortdate`, `date`, `shorttime`, `time`, `shortdatetime`,
`datetime`, `number`, `currency`); `datetime( 'custom', <format> )` nimmt ein ausdrückliches Format. `currentdate()` ist
jetzt.

## Übersetzung

```text
{'Read more'|i18n( 'design/mysite/full/article' )}
```

gibt den Text in der Sprache des Benutzers aus, wenn es für diesen Kontext eine Übersetzung in einer `.ts`-Datei gibt,
und sonst den englischen Text. Jeder Text, den ein Template ausgibt, sollte durch `i18n` laufen; siehe
[Übersetzungen in einer Erweiterung](../teil-3-erweiterungsentwickler/uebersetzungen-in-einer-erweiterung.md).

## URLs und Dateien

| Eingabe | Ausgabe |
|---|---|
| `{'/content/view/full/2'\|ezurl( 'no' )}` | `/content/view/full/2` (plus Siteaccess-Pfad oder -Host, wenn die Website einen verwendet) |
| `{'/content/view/full/2'\|ezurl}` | `"/content/view/full/2"`, mit Anführungszeichen, für ein Attribut |
| `{'stylesheets/site.css'\|ezdesign}` | die Adresse der ersten `stylesheets/site.css` in der Design-Kette |
| `{'logo.png'\|ezimage}` | die Adresse von `images/logo.png` in der Design-Kette |
| `{'/'\|ezroot( 'no' )}` | `/`, das Wurzelverzeichnis der Installation ohne den Siteaccess |

`ezimage` sucht im Ordner `images/` der Design-Basen; eine fehlende Datei wird in der Debug-Ausgabe mit jedem Pfad
gemeldet, den der Operator probiert hat.

## Einstellungen

| Eingabe | Ausgabe |
|---|---|
| `{ezini( 'SiteSettings', 'SiteName', 'site.ini' )\|wash}` | der Name der Website, escaped |
| `{ezini( 'DebugSettings', 'DebugOutput' )}` | `enabled` oder `disabled` (`site.ini` ist die Standarddatei) |
| `{ezini_hasvariable( 'MySettings', 'Limit', 'mysite.ini' )}` | ob eine Einstellung existiert |

## Debugging

| Eingabe | Zeigt |
|---|---|
| `{$node\|attribute( 'show', 1 )}` | die Attribute einer Variablen, eine Ebene tief, als Tabelle |
| `{debug-log var=$node.name msg='name'}` | den Wert in der Debug-Ausgabe, nicht in der Seite |

Siehe [Templates debuggen](templates-debuggen.md).

## Verwandte Seiten

- Zurück: [Inhalte in Templates abrufen](fetch-fuer-builder.md)
- Weiter: [Templates debuggen](templates-debuggen.md)
- [Eingebaute Template-Operatoren](../teil-8-referenz/referenz-der-template-operatoren.md)
- [Template-Operatoren für Zeichenketten](../../../../eng-us/features/6.0/string-template-operators.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
