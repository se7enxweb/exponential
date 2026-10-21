---
title: Template-Debugging
audience: designer, developer
category: templates
order: 740
summary: Werkzeuge, um Template-Probleme zu finden - die Debug-Ausgabe und ihre Template-Liste, Template-Warnungen mit Datei und Zeile, dump und attribute, um in Variablen zu sehen, die Debug-Funktionen, die Syntaxprüfung für Templates und das Fehlerlog.
keywords: Template-Debugging, Debug-Ausgabe, DebugOutput, ShowUsedTemplates, Template-Warnungen, dump, Operator attribute, debug-log, debug-timing-point, eztemplatecheck, Fehlerlog, unbekannter Operator, template debugging, debug output, template warnings, attribute operator, error log, unknown operator
type: help
---
# Template-Debugging

Diese Seite ist für Designer, deren Template nicht tut, was sie erwarten. Sie listet die Werkzeuge vom schnellsten zum
tiefsten auf. Bei „meine Änderung erscheint nicht“ beginnen Sie mit [Ein Design debuggen](../theme-design/ein-design-debuggen.md)
(Caches); bei „welches Template ist das“ mit [Template-Pfadkommentare](template-pfad-kommentare.md).

## Inhalt

1. [Die Debug-Ausgabe](#die-debug-ausgabe)
2. [In eine Variable sehen](#in-eine-variable-sehen)
3. [Debug-Funktionen](#debug-funktionen)
4. [Syntaxprüfung](#syntaxprüfung)
5. [Typische Meldungen](#typische-meldungen)

## Die Debug-Ausgabe

Auf einem Test-Siteaccess, nur für Ihre Adresse:

```ini
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]=203.0.113.10

[TemplateSettings]
ShowUsedTemplates=enabled
```

Unter der Seite erscheinen die Warnungen und Fehler der Anfrage, Zeitmessungen, Datenbankabfragen und mit `ShowUsedTemplates`
eine Tabelle jedes verwendeten Templates mit der Datei, aus der es kam. Template-Warnungen nennen Template und Zeile
(Compiler-Warnungen tragen seit der Arbeit an den Pfadkommentaren im August 2026 die Fundstelle).

## In eine Variable sehen

```tpl
{$node|attribute( show, 1 )}           {* the attributes of $node and their values, one level deep *}
{$module_result.content_info|dump( show, 2 )}
```

Entfernen Sie diese vor dem Commit: Sie geben interne Daten auf der Seite aus.

## Debug-Funktionen

Aus `lib/eztemplate/classes/eztemplatedebugfunction.php`, angezeigt in der Debug-Ausgabe, nicht auf der Seite:

```tpl
{debug-log msg='teaser part reached'}
{debug-log var=$items msg='items of the list'}
{debug-timing-point id='menu'} ... {/debug-timing-point}
{debug-accumulator} ... {/debug-accumulator}
```

`debug-timing-point` misst, wie lange ein Teil des Templates braucht.

## Syntaxprüfung

```bash
./console exp:eztemplatecheck extension/mytheme/design/mytheme/templates/
./console exp:eztemplatecheck -s mysite
```

meldet jede Datei als gültig oder mit ihrem Parse-Fehler. Es führt Templates nicht aus, kann also keine unbekannten Operatoren
oder fehlenden Variablen finden.

## Typische Meldungen

| Meldung (Debug-Ausgabe oder `var/log/error.log`) | Bedeutung |
|---|---|
| Operator '...' is not registered | Den Operator gibt es nicht, oder seine Erweiterung ist hier nicht aktiv |
| Variable '...' is already defined | ein `{def}` einer vorhandenen Variablen ([Variablen und def](variablen-und-def.md)) |
| Variable '...' is not defined with {def} | `{undef}` einer Variablen, die nicht definiert war |
| Custom match file: path '...' not found in any resource | das `MatchFile` eines Overrides existiert nicht |
| Image '...' does not exist in any design | eine Datei für `ezimage` fehlt in der Kaskade |
| Could not find: ... | ein dem Packer übergebenes Stylesheet oder Skript fehlt |

## Verwandte Seiten

- [Template-Pfadkommentare](template-pfad-kommentare.md)
- [Häufige Template-Fehler](haeufige-template-fehler.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
