---
title: Geschichte der Template-Sprache
audience: developer, designer, app-creator
category: development
order: 20
summary: Wie die Template-Sprache gewachsen ist, nur aus dem erzählt, was das Repository festhält - die Git-Geschichte ab 2002, die Changelogs ab 2.9, der Compiler von 2003, die Syntax mit def/if/foreach von 2005 (Version 3.6), die let und section ersetzte, und was vom älteren Stil noch übrig ist.
keywords: Geschichte, Template-Sprache, eZ TPL, ezpl, section, let, foreach, def, Compiler, Process-Cache, 3.6, verbesserte Template-Syntax, veraltet, history, template language, compiler, process cache, improved template syntax, deprecated
type: help
---
# Geschichte der Template-Sprache

Die Template-Sprache ist älter als der meiste Code um sie herum. Diese Seite erzählt ihre Geschichte aus dem, was das Repository selbst
festhält: die Git-Geschichte, die Changelogs in `doc/changelogs/`, die Hinweise zu Funktionen in `doc/eng-us/features/` und Kommentare im
Quellcode. Nichts hier stammt aus dem Gedächtnis oder aus fremden Quellen. Zitierte Betreffzeilen von Commits und Notizen sind ins Deutsche
übertragen.

## Inhalt

1. [Woher sie kam](#woher-sie-kam)
2. [Die Aufzeichnungen im Repository](#die-aufzeichnungen-im-repository)
3. [Vom Process-Cache zum Compiler (2003)](#vom-process-cache-zum-compiler-2003)
4. [Die verbesserte Syntax (2005, Version 3.6)](#die-verbesserte-syntax-2005-version-36)
5. [Wie der ältere Stil heute aussieht](#wie-der-ältere-stil-heute-aussieht)
6. [Exponential führt sie weiter](#exponential-führt-sie-weiter)
7. [Verwandte Seiten](#verwandte-seiten)

## Woher sie kam

Exponential setzt die Codebasis fort, die viele Jahre lang als eZ Publish veröffentlicht wurde. Dieses Produkt wurde von seinem Hersteller
nach fast 18 Jahren eingestellt; der Copyright-Hinweis nennt noch beide Eigentümer des Codes, `Copyright (C) 1998 - 2026 7x & Exponential
Foundation` und `Copyright (C) eZ Systems AS`, die Zeile oben in `lib/eztemplate/classes/eztemplate.php`. Die Template-Bibliothek ist während
ihrer ganzen aufgezeichneten Geschichte Teil der Codebasis gewesen, und 7x hat sie weitergeführt, seit es das Projekt 2023 übernommen hat
([Wer ist 7x?](../wer-ist-7x.md)).

## Die Aufzeichnungen im Repository

| Quelle | Was sie abdeckt | Wo |
|---|---|---|
| Git-Geschichte | ab dem ersten Commit am 2. September 2002 (alle Dateien der Wurzel nach trunk verschoben) | `git log -- lib/eztemplate` |
| Changelogs | von Version 2.9 bis 5.2, dann 6.0.x | [doc/changelogs](../../../changelogs/README.md) |
| Hinweise zu Funktionen | ab Version 3.5 | `doc/eng-us/features/<version>/` |
| Kommentare im Quellcode | aus Gründen der Kompatibilität behaltene Operatoren | `lib/eztemplate/classes/eztemplateautoload.php` |

## Vom Process-Cache zum Compiler (2003)

Der Template-Compiler begann unter einem anderen Namen. Der Commit vom 14. Mai 2003 (`88c1f9460c`) benannte das System des Process-Cache in
Template-Kompilierung um, als besseren Namen, und der Changelog von 3.1 vermerkt, dass das System zur Template-Kompilierung fertig sei und
nun PHP-Template-Dateien erzeuge, die sich richtig ausführen lassen. Zwei Tage später (`4fe8ef4f1c`) wurde die Unterstützung für die
Transformation von Knoten für `let`, `default` und `section` hinzugefügt, der Haken, der eine Funktion bis heute ihren eigenen PHP-Code
schreiben lässt (`templateNodeTransformation()`).

Der Changelog von 3.4 meldet dann, dass die Template-Kompilierung für fast alle Template-Operatoren und -Funktionen umgesetzt sei, fügte
`eztc.php` (dem Befehl, der Templates vorab kompiliert) Parameter hinzu und die Option `subtree_expiry` von `{cache-block}`. Die
Einstellungen, die all das steuern, stehen noch in `settings/site.ini` [TemplateSettings]: `TemplateCompile`, `TemplateOptimization`,
`TemplateCache`, `DevelopmentMode`.

## Die verbesserte Syntax (2005, Version 3.6)

Die größte Änderung an der Sprache selbst kam mit dem Commit `7460c738b2` vom 18. März 2005: die erste Umsetzung der neuen Template-Funktionen
{def}, {undef}, {if}, {elseif}, {else}, {while}, {do}, {for}, {foreach}, {break}, {continue} und {skip}. Seine Entwurfsnotiz liegt noch im
Repository, [doc/eng-us/features/3.6/improved_template_syntax.txt](../../../eng-us/features/3.6/improved_template_syntax.txt). Ihr erklärter Anlass:
die Template-Syntax klarer und der Syntax verbreiteter prozeduraler Programmiersprachen ähnlicher zu machen.

Was sie änderte:

- **Das Dollarzeichen überall**, auch bei Zuweisungen: `{set $i=10}`. Zuweisungen im alten Stil ohne „$“ sollten weiter funktionieren.
- **def und undef** statt Blöcken `{let}...{/let}`, weil ein Block die Datei .tpl schwerer lesbar macht und es erschwert, Code darin zu
  verschieben. Eine mit `{def}` angelegte Variable lebt bis zum Ende ihrer Datei.
- **if, while, do, for und foreach** als für Entwickler viel einfacher zu verstehen und zu nutzen als die allmächtige Funktion {section}.
- **break, continue, skip und delimiter** für jede Schleife.

Die Notiz hält auch fest, was `foreach` bewusst nicht tut: Es läuft nicht über die Zeichen einer Zeichenkette oder einen Zahlenbereich
(nutzen Sie `for`), und es hat keinen Proxy-Iterator (`$item.index`); `section` behält diese. Der Changelog von 3.6 enthält die ersten
Fehlermeldungen gegen die neue Template-Syntax (#6629) und die neue Template-Schleife (die Korrektur von `{delimiter}`), und der Changelog von
3.8 die Korrektur, dass `{undef}` Template-Variablen zerstören konnte, die nicht mit {def} angelegt waren.

## Wie der ältere Stil heute aussieht

Beide Stile laufen weiterhin nebeneinander. Dieselbe Liste, auf die alte und die neue Weise geschrieben, auf einer Entwicklungsinstallation gerendert:

```tpl
{* the older style: let ... /let and section *}
{let items=array( 'red', 'green', 'blue' )}
old: {section name=Colors loop=$items}{$Colors:item}{delimiter}, {/delimiter}{/section}
{/let}
{* the style introduced with def and foreach *}
{def $items = array( 'red', 'green', 'blue' )}
new: {foreach $items as $item}{$item}{delimiter}, {/delimiter}{/foreach}
{undef $items}
```

Ausgabe:

```text
old: red, green, bluenew: red, green, blue
```

Andere Spuren der Geschichte im heutigen Code:

- `lib/eztemplate/classes/eztemplateautoload.php` registriert noch `array_prepend`, `array_append` und `array_merge`, markiert mit
  `// DEPRECATED/OBSOLETE`, neben ihren Ersetzungen `prepend`, `append` und `merge`.
- `let` und `default` sind weiterhin registriert (`eZTemplateSetFunction`), ebenso `section` mit seinen Kindern `section-else`,
  `section-exclude` und `section-include` ([Sections und foreach](sections-und-foreach.md)).
- Der Parser akzeptiert weiterhin Parameter von `set`, `let` und `default` ohne Dollarzeichen und behandelt `name` und `scope` ohne
  Dollarzeichen als Namensraum und Gültigkeitsbereich der Funktion (`parseSetFunction()` in `lib/eztemplate/classes/eztemplatemultipassparser.php`).

## Exponential führt sie weiter

Unter 7x wird die Sprache als Teil des Kernels gepflegt und dort erweitert, wo es heutigen Websites hilft, wobei die Templates vorhandener
Designs immer funktionsfähig bleiben. Beispiele dafür, was die Linie 6.0 in der Template-Engine und um sie herum hinzugefügt hat: die
Einstellung `ShowTemplatePathComments`, die jedes gerenderte Template in HTML-Kommentare mit dem Namen seiner Datei einschließt
(`eZTemplate::showTemplatePathComments()`), eine Warnung, die einmal pro Operator und Parameter für einen fehlenden Standardwert geschrieben
wird (`eZTemplate`), und die Operatoren `csp_nonce` und `exp_environment` des Kernels. Der Changelog jedes Release führt die Änderungen auf:
[doc/changelogs](../../../changelogs/README.md).

## Verwandte Seiten

- Zurück: [Was die Template-Sprache ist](was-die-template-sprache-ist.md)
- Weiter: [Smarty und die Template-Sprache nebeneinander](smarty-nebeneinander.md)
- [Sections und foreach](sections-und-foreach.md)
- [Section (veraltet)](../designer/vorlagen/section-legacy.md) zum Umstellen alter Templates
- [Wer ist 7x?](../wer-ist-7x.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
