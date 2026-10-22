---
title: Die Template-Engine und die Design-Kette
audience: software-engineer, developer, designer
category: software-engineering
order: 170
summary: Wie Exponential HTML ausgibt - die Engine eZTemplate, die .tpl-Dateien zu PHP kompiliert, aus Einstellungen registrierte Operatoren und Funktionen, Fetch-Funktionen als einziger Datenzugriff, die Design-Kette und Override-Regeln, die ein Template wählen, Cache-Blöcke, Maskierung und die Prüfregeln für Templates.
keywords: Template-Engine, eZTemplate, tpl, kompilierte Templates, Operatoren, Template-Funktionen, fetch, Design-Kette, override.ini, Priority, cache-block, wash, Maskierung, Pagelayout, Softwareingenieur, template engine, compiled templates, operators, template functions, design chain, escaping
type: help
---
# Die Template-Engine und die Design-Kette

Diese Seite richtet sich an Ingenieure, die verstehen müssen, wie eine Seite gerendert wird, wie ein Template gewählt
wird und was dabei schiefgehen kann. Die Template-Sprache ist die eigene von Exponential; sie ist weder Twig noch Smarty,
auch wenn ihre Syntax mit geschweiften Klammern ähnlich aussieht. Die maßgeblichen Regeln stehen in
[Kernel-Architektur](../../standards/architektur/kernel-architektur.md#die-template-engine-operatoren-und-funktionen).

## Inhalt

1. [Die Engine](#die-engine)
2. [Datenzugriff aus Templates](#datenzugriff-aus-templates)
3. [Die Design-Kette](#die-design-kette)
4. [Overrides](#overrides)
5. [Caching innerhalb von Templates](#caching-innerhalb-von-templates)
6. [Fehlersuche beim Rendern](#fehlersuche-beim-rendern)
7. [Prüfregeln](#prüfregeln)

## Die Engine

`eZTemplate` (`lib/eztemplate/classes/eztemplate.php`) kompiliert `.tpl`-Dateien zu PHP und führt die kompilierten
Dateien aus (mitgeliefert `site.ini [TemplateSettings] TemplateCompile=enabled`). Operatoren (`|wash`, `ezurl`, `i18n`)
und Funktionen (`{def}`, `{foreach}`, `{include}`, `{cache-block}`) sind PHP-Klassen, die in `eztemplateautoload.php`-
Dateien als Einträge von `$eZTemplateOperatorArray` und `$eZTemplateFunctionArray` registriert sind. Die Engine lädt sie
aus `[TemplateSettings] AutoloadPathList[]` und aus den in `ExtensionAutoloadPath[]` aufgeführten Erweiterungen.

Die Sprachreferenz steht in der Hilfe: [Template-Sprache](../template-sprache/README.md) und
[Template-Operatoren](../template-operatoren/README.md).

## Datenzugriff aus Templates

Templates fragen die Datenbank nicht ab: Die Sprache hat kein SQL-Konstrukt. Daten kommen aus:

- **den Variablen der View**, die die Modul-View vor dem Rendern setzt;
- **Fetch-Funktionen**: `fetch( 'content', 'list', hash( ... ) )`, zugeordnet in
  `kernel/<module>/function_definition.php` und von `eZFunctionHandler` ausgeführt. Ein Fetch, der Inhalte zurückgibt,
  wendet die Berechtigungen des Benutzers an;
- **Template-Operatoren**, die PHP sind und alles tun können; sie werden daher wie Views geprüft.

## Die Design-Kette

Ein Design ist ein Verzeichnis mit Templates, Stylesheets und Bildern. Designs werden gestapelt, sodass eine Website nur
enthält, was sich von der Basis unterscheidet: `site.ini [DesignSettings] SiteDesign`, `AdditionalSiteDesignList[]` und
`StandardDesign` (`standard`). Erweiterungen fügen Design-Verzeichnisse über `design.ini [ExtensionSettings]
DesignExtensions[]` hinzu. `eZTemplateDesignResource` baut die Kette: zuerst die Design-Verzeichnisse der Erweiterungen,
dann `design/<design>/`.

## Overrides

Blöcke in `override.ini` wählen für eine View ein anderes Template, wenn Bedingungen passen: `Source`, `MatchFile`,
`Subdir` und `Match[...]` (zum Beispiel der Klassenbezeichner oder der Knoten). Seit Juli 2026 werden Overrides nach
`Priority` geordnet, niedrigere zuerst ([Reihenfolge der Template-Overrides](../../funktionen/6.0/reihenfolge-der-template-ueberschreibungen.md)).
Die Administrationsseite Design > Templates schreibt nur `settings/siteaccess/<sa>/override.ini.append.php`.

Eine Website oder Erweiterung überschreibt ein Kernel-Template über `override.ini` oder ihr eigenes Design-Verzeichnis;
sie darf keine Dateien in `design/standard/` oder `design/admin*/` ändern.

## Caching innerhalb von Templates

| Cache | Was | Geleert durch |
|---|---|---|
| Kompilierte Templates | das aus jedem `.tpl` kompilierte PHP | `php bin/php/ezcache.php --clear-tag=template --allow-root-user` |
| Template-Override-Cache | die aufgelösten Override-Regeln | `--clear-id=template-override`; ohne das wird eine neue Template-Datei nicht gefunden |
| Cache-Blöcke | die Ausgabe von `{cache-block}`-Abschnitten (`[TemplateSettings] TemplateCache=enabled`) | Ablauf, Schlüssel, `--clear-id=template-block` |
| View-Cache für Inhalte | die ganze gerenderte Inhaltsansicht | Inhaltsänderungen; `--clear-tag=content` |

Siehe [Cache-Invalidierung und Leeren](cache-invalidierung-und-leeren.md).

## Fehlersuche beim Rendern

- **Kommentare mit Template-Pfaden** umschließen jedes gerenderte Template mit HTML-Kommentaren, die seine Datei nennen
  ([Kommentare mit Template-Pfaden](../../funktionen/6.0/template-pfad-kommentare.md)).
- Die **Debug-Leiste** listet die Templates, die eine Seite verwendet ([Die Exp-Debug-Leiste](../../funktionen/6.0/exp-debug-leiste.md)).
- `./console exp:eztemplatecheck` (`bin/php/eztemplatecheck.php`) prüft Templates auf Syntaxfehler.

## Prüfregeln

- Jede Variable aus Inhalten oder Eingaben wird mit `|wash` ausgegeben oder über einen Operator, der maskiert.
- Ein Operator, der HTML in PHP baut, maskiert es dort.
- Kein SQL in Templates, und kein rohes Datenbank-Handle wird an eines übergeben.
- In den Administrationsdesigns: kein `<style>`-Block und kein `style=""`-Attribut und kein Inline-Skriptcode; das
  prüfen Tests in `tests/tests/design/` ([Gestaltung der Administration](../../bc/6.0/admin4-gestaltung.md)).
- Skripte, die inline bleiben müssen, tragen die Nonce der Content Security Policy: `<script nonce="{csp_nonce()}">`.

## Verwandte Seiten

- [Templates und Design](../../anleitungen/templates-und-design.md)
- [View-Ergebnisse und das Pagelayout](../ersteller/anwendungsentwickler/ergebnisse-von-ansichten-und-das-pagelayout.md)
- [Die Design-Kette](../erweiterungsentwicklung/teil-2-builder/die-design-kette.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
