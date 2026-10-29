---
title: Templates zwischen Template-Engines umwandeln
audience: developer, designer, app-creator
category: templates
order: 57
summary: Der Befehlsraum exp:template:convert wandelt Templates zwischen Exponential-Templates (.tpl), Smarty-Templates (.stpl) und Twig-Templates (.twig) in jede Richtung um - die sechs benannten Befehle, der allgemeine Befehl, der Diff, die Markierung, die von Hand geänderte Dateien schützt, NOTE-Zeilen, Exit-Codes, Override-Regeln für einen Siteaccess und ein Beispiel durch alle drei Engines.
keywords: umwandeln, Konverter, exp:template:convert, tpl-to-twig, twig-to-tpl, tpl-to-stpl, stpl-to-tpl, stpl-to-twig, twig-to-stpl, exp:template:convert:diff, exp:template:engines, Markierung, NOTE, override-ini, Hin- und Rückweg, convert, converter
type: help
---
# Templates zwischen Template-Engines umwandeln

Exponential rendert drei Arten von Templates: Exponential-Templates (`.tpl`), Smarty-Templates (`.stpl`) und
Twig-Templates (`.twig`, `.html.twig`). Ein Befehlsraum wandelt zwischen ihnen in jede Richtung um, ein einzelnes
Template oder ein ganzes Design. Dieses Kapitel ist für Entwickler und Designer, die Templates von einer Engine in
eine andere bringen; die Ausgabe unten haben die Befehle auf einer Entwicklungsinstallation ausgegeben.

## Die Engines

```bash
./console exp:template:engines
```

```
AVAILABLE    tpl (Exponential templates) Exponential 6.0.15stable
AVAILABLE    stpl (Smarty templates) Smarty 5.8.5
AVAILABLE    twig (Twig templates)  Twig 3.30.0
PASS 3 engines available
```

Eine Engine heißt `tpl` (auch `exp`, `exponential`), `stpl` (auch `smarty`) oder `twig`.

## Die Befehle

| Befehl | Wandelt um |
|---|---|
| `exp:template:convert:tpl-to-stpl`, `exp:template:convert:stpl-to-tpl` | `.tpl` und `.stpl` |
| `exp:template:convert:tpl-to-twig`, `exp:template:convert:twig-to-tpl` | `.tpl` und `.twig` |
| `exp:template:convert:stpl-to-twig`, `exp:template:convert:twig-to-stpl` | `.stpl` und `.twig`, über `.tpl` in einem Lauf |
| `exp:template:convert --from-engine=<e> --to-engine=<e>` | jede Richtung |
| `exp:template:convert:diff --file=<Datei> --to-engine=<e> [--against=<Datei>]` | gibt eine Umwandlung aus, oder ihren Unterschied zu einer Datei; schreibt nichts |

Die Optionen jeder Richtung:

| Option | Bedeutung |
|---|---|
| `--from=<Verz>` | ein Design-Verzeichnis: jedes Template der Quell-Engine darunter (`templates/` und `override/templates/`) |
| `--to=<Verz>` | wohin die umgewandelten Dateien kommen, an dieselbe Stelle darunter (Standard: neben die Quellen) |
| `--file=<Datei>` | nur ein Template |
| `--dry-run` | sagen, was geschrieben würde, nichts schreiben |
| `--force` | ein Ziel ersetzen, das von Hand geändert oder geschrieben wurde |
| `-s <Siteaccess> --override-ini=<Datei>` | die Override-Regeln dieses Siteaccess für die umgewandelten Dateien schreiben |
| `--twig-suffix=.html.twig` | die Endung geschriebener Twig-Templates (Standard `.twig`) |

Exit-Codes: 0 PASS; 1 FAIL (ein Template konnte nicht gelesen, umgewandelt, geprüft oder geschrieben werden); 2 falsche
Optionen; 3 die Umwandlung ist nicht verfügbar (die Bibliothek einer Engine fehlt).

## Durch alle drei Engines

Eine `.tpl`, umgewandelt in `.stpl`:

```bash
./console exp:template:convert:tpl-to-stpl --from=<design> --to=<design>_stpl
```

```
PASS tpl->stpl 2 templates: 2 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

```smarty
{$items = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 3])}
<ul>{foreach $items as $child}
    <li>{$child.name|wash}</li>{/foreach}
</ul>{if ($items|exp_count)|gt:2}
{'More'|i18n:'design/standard/content'}{/if}
```

und die `.stpl` weiter in `.twig` (über `.tpl`, in einem Lauf):

```bash
./console exp:template:convert:stpl-to-twig --from=<design>_stpl --to=<design>_twig
```

```
PASS stpl->twig 2 templates: 2 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

Die `.twig` ist Zeile für Zeile derselbe Text, den `tpl-to-twig` aus der `.tpl` schreibt: jede Richtung behält, was
das Template ausgibt, deshalb lassen sich die Engines verketten.

## Von Hand geänderte Dateien

Jede geschriebene Datei endet mit einem Markierungskommentar, der die Richtung, die md5 des umgewandelten Texts und die
Quelle nennt:

```
{# exp:template:convert stpl->twig md5:543e672301da17d7166e2c1ac83c7696 from templates/list.stpl -- generated; a file changed by hand is not replaced without --force #}
```

Ein zweiter Lauf schreibt nur, was sich geändert hat (sonst `unchanged`), und lässt ein Ziel ohne Markierung oder eines,
das seit dem Schreiben geändert wurde, in Ruhe (`SKIP ... --force replaces it`). Mit `--override-ini` zeigt die Regel
eines übersprungenen Templates weiterhin auf es.

## NOTE-Zeilen

Ein Konstrukt ohne genaue Entsprechung in der Ziel-Engine wird so nah wie möglich geschrieben und in einer
`NOTE`-Zeile der Fehlerausgabe genannt; was sich gar nicht schreiben lässt, bleibt als Kommentar erhalten:

```
{* TWIG (not converted: the Twig node MacroDeclarationNode has no template form) line 1 *}{$user_name|upcase}
NOTE macro.twig: {% macros %} have no template form
```

## Das Ergebnis prüfen

Jedes umgewandelte Template wird geprüft (von seiner Engine kompiliert), bevor es geschrieben wird. Prüfen Sie nach
einem ganzen Design mit `./console exp:template:check --design-dir=<Verz> --level=strict` und vergleichen Sie Seiten,
die aus dem alten und dem neuen Design gerendert werden.

## Wie es weitergeht

- [Von Exponential-Templates (.tpl) zu Twig-Templates (.twig) umziehen](von-tpl-zu-twig-umziehen.md),
  [Von Exponential-Templates (.tpl) zu Exponential-Smarty-Templates (.stpl) umziehen](von-tpl-zu-stpl-umziehen.md)
- [Templates zwischen Engines konvertieren](../../anleitungen/template-konvertierung.md) (Anleitung)
- [The three template engines compared](../../../eng-us/guides/template-engines.md) (Englisch)
