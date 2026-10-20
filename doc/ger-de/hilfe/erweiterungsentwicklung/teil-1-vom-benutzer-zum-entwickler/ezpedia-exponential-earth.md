---
title: eZpedia (ezpedia.exponential.earth)
audience: user, developer, designer, app-creator
category: extension-development
order: 130
summary: eZpedia, die freie Community-Enzyklopädie unter der GNU FDL - die Artikel, die für Erweiterungsentwickler zählen, wie alt sie sind und wie Sie sie mit Quellenangabe wiederverwenden.
keywords: ezpedia, Wiki, Enzyklopädie, GFDL, Lernen, Lösung, Snippet, Erweiterungen, Template-Override, Debugging, Datentyp, Workflow, Kernel-Override, View-Parameter, wiki, encyclopedia, learning, solution, snippet, extensions, template override, debugging, datatype, workflow, kernel override, view parameters
type: help
---
# eZpedia (ezpedia.exponential.earth)

Teil 1, Kapitel 13. [eZpedia](https://ezpedia.exponential.earth/) ist die freie Enzyklopädie dieser Codebasis,
geschrieben von ihrer Community seit 2006. Sie enthält Hunderte von Artikeln (722 englische Artikel, als diese Seite im
Oktober 2026 geschrieben wurde) in Namensräumen für Lernen, Lösungen, Codeschnipsel, Projekte und Personen. Dieses
Kapitel wählt die Artikel aus, die für Erweiterungsentwickler zählen, und sagt, wie Sie sie nutzen.

## Ihre Lizenz

Die Website erklärt: „All text is available under the terms of the GNU Free Documentation License“. Das ist dieselbe
Lizenzfamilie wie bei dieser Hilfe (GNU FDL 1.3, siehe [Freie Dokumentation](../../freie-dokumentation.md)); Sie dürfen
Texte aus eZpedia also zitieren und bearbeiten, solange Sie sie unter der GNU FDL halten und **den Artikel** mit
seinem Titel und einem Link **nennen**. In diesem Buch verlinken wir trotzdem lieber: Die Artikel lohnen sich, als
Ganzes und in ihrem eigenen Zusammenhang gelesen zu werden.

## Die Namensräume

| Namensraum | Adresse | Was darin steht |
|---|---|---|
| Learning | [/en/learning](https://ezpedia.exponential.earth/en/learning) | Konzepte und Anleitungen: Erweiterungen, Templates, Designs, Debugging, Datentypen, Workflows |
| Solution | [/en/solution](https://ezpedia.exponential.earth/en/solution) | Konkrete Probleme und ihre Lösungen, viele mit Code |
| Snippet | [/en/snippet](https://ezpedia.exponential.earth/en/snippet) | Kurze Stücke Code und Template |
| Project | [/en/project](https://ezpedia.exponential.earth/en/project) | Notizen zu Erweiterungen und Werkzeugen |

## Artikel für die Leser dieses Buchs

Die Titel werden so zitiert, wie die Artikel selbst heißen; sie verwenden den Produktnamen ihrer Zeit.

| Thema | Artikel | Lesen Sie dazu |
|---|---|---|
| Erweiterungen | ["Extensions"](https://ezpedia.exponential.earth/en/learning/extensions) | [Was eine Erweiterung ist](was-eine-erweiterung-ist.md) |
| In einer Erweiterung bauen | ["Learning: How to develop eZ publish solutions within an extension"](https://ezpedia.exponential.earth/en/learning/learning_how_to_develop_ez_publish_solutions_within_an_extension) | [Teil 3](../teil-3-erweiterungsentwickler/README.md) |
| Fehlersuche | ["HOWTO Troubleshoot eZ Publish Extensions"](https://ezpedia.exponential.earth/en/learning/howto_troubleshoot_ez_publish_extensions) | [Erweiterungen debuggen](../teil-3-erweiterungsentwickler/erweiterungen-debuggen.md) |
| Design-Erweiterungen | ["Learning: How to create a design extension"](https://ezpedia.exponential.earth/en/learning/learning_how_to_create_a_design_extension) | [Teil 6](../teil-6-theme-entwickler/README.md) |
| Override-System | ["Template Override System"](https://ezpedia.exponential.earth/en/learning/template_override_system) | [Template-Overrides: die Grundlagen](../teil-2-builder/grundlagen-der-template-overrides.md) |
| Templates debuggen | ["Learning: Template Debugging"](https://ezpedia.exponential.earth/en/learning/learning_template_debugging) | [Templates debuggen](../teil-2-builder/templates-debuggen.md) |
| Debugging | ["Debugging"](https://ezpedia.exponential.earth/en/learning/debugging) | [Debug-Ausgabe und Logs für Entwickler](debug-ausgabe-und-logs-fuer-entwickler.md) |
| Pagelayout | ["Pagelayout.tpl"](https://ezpedia.exponential.earth/en/learning/pagelayout_tpl) | [View-Ergebnisse und das Pagelayout](../teil-3-erweiterungsentwickler/view-ergebnisse-und-das-pagelayout.md) |
| Knoten-Templates | ["Custom Node Templates"](https://ezpedia.exponential.earth/en/learning/custom_node_templates) | [Override-Bedingungen](../teil-2-builder/override-bedingungen.md) |
| Datentypen | ["Datatype"](https://ezpedia.exponential.earth/en/learning/datatype) | [Datentypen](../teil-3-erweiterungsentwickler/datentypen.md) |
| Workflows | ["Workflow"](https://ezpedia.exponential.earth/en/learning/workflow) | [Workflow-Ereignistypen](../teil-3-erweiterungsentwickler/workflow-ereignistypen.md) |
| Einstellungen | ["Configuration Files"](https://ezpedia.exponential.earth/en/learning/configuration_files) | [Wie INI-Einstellungen funktionieren](../teil-2-builder/wie-ini-einstellungen-funktionieren.md) |
| Kernel-Overrides | ["Overriding kernel classes within an extension"](https://ezpedia.exponential.earth/en/solution/overriding_kernel_classes_within_an_extension) | [Kernel-Klassen-Overrides: das letzte Mittel](../teil-3-erweiterungsentwickler/kernel-overrides-der-letzte-ausweg.md) |
| View-Parameter | ["How to add view parameter support to your custom module view"](https://ezpedia.exponential.earth/en/solution/how_to_add_view_parameter_support_to_your_custom_module_view) | [View-Parameter](../teil-3-erweiterungsentwickler/view-parameter.md) |
| Bildaliase aus PHP | ["Creating image alias image variations in PHP"](https://ezpedia.exponential.earth/en/solution/creating_image_alias_image_variations_in_php) | [Referenz in Teil 8](../teil-8-referenz/README.md) |
| Rich Text aus PHP | ["Manipulating ezxml document contents"](https://ezpedia.exponential.earth/en/solution/manipulating_ezxml_document_contents) | [Inhalte aus PHP anlegen, veröffentlichen und ändern](../teil-8-referenz/inhaltsfunktionen-aus-php.md) |
| Relationen aus PHP | ["Adding related object programmatic"](https://ezpedia.exponential.earth/en/solution/adding_related_object_programmatic) | dasselbe |
| Beispiele für die PHP-API | ["PHP eZ Publish API source code examples"](https://ezpedia.exponential.earth/en/snippet/php_ez_publish_api_source_code_examples) | [Teil 8](../teil-8-referenz/README.md) |
| Bearbeitungs- und Aktions-Handler | ["Content edit handlers"](https://ezpedia.exponential.earth/en/ez/content_edit_handlers), ["Content action handlers"](https://ezpedia.exponential.earth/en/ez/content_action_handlers) | [Handler, die über Einstellungen benannt werden](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) |

## Wie alt ist das?

Viele Artikel wurden zuletzt zwischen 2006 und 2012 geändert. Die Konzepte (Einstellungen, Overrides, die Design-Kette,
Module, Datentypen, Workflow-Ereignisse) haben sich seitdem kaum verändert. Was sich verändert hat:

| Damals | In Exponential 6 |
|---|---|
| Code für PHP 4 und PHP 5 (Konstruktoren `function ClassName()`, `each()`, `create_function()`) | PHP 8.0 bis 8.5; siehe [Teil 4](../teil-4-aktualisieren/README.md) |
| Zeilen `include_once( 'kernel/classes/...' )` am Anfang von Dateien | Autoloading; neu erzeugen mit `php bin/php/ezpgenerateautoloads.php -e` |
| Inline-`<script>` und `onclick=""` in Templates | In der Administration durch ihre Content Security Policy verboten |
| YUI und älteres jQuery | jQuery 4 und der Namensraum `Exp` von expui |
| Caches leeren durch Löschen von Ordnern | `php bin/php/ezcache.php`, `./console exp:cache` |

Also: Vertrauen Sie den Konzepten, prüfen Sie den Code gegen den Kernel, den Sie haben (suchen Sie mit grep nach jeder
Klasse und Methode, die er verwendet), und rechnen Sie damit, ihn modernisieren zu müssen.

## Verbessern Sie sie

eZpedia ist ein Wiki; jeder darf bearbeiten. Wenn Ihnen ein Artikel geholfen hat, sein Code aber veraltet ist, ist das
Freundlichste, was Sie tun können, eine Notiz mit dem Weg in Exponential 6 und einen Link auf die Seite dieses Buchs
hinzuzufügen, die ihn erklärt.

## Verwandte Seiten

- Zurück: [Exponential Projects (projects.exponential.earth)](projects-exponential-earth.md)
- Weiter: [Aus den freien Büchern lernen](aus-den-buechern-lernen.md)
- [Verlinken oder kopieren: Quellen fair zitieren](verlinken-oder-kopieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
