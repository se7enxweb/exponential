---
title: Muster in extension/ezoe
audience: developer
category: standards
order: 447
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezoe mit den Entwurfsmustern.
keywords: extension/ezoe, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezoe

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezoe` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (26)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `EnchantSpell` | class extends SpellChecker | – | `extension/ezoe/modules/ezoe/classes/EnchantSpell.php:11` | Diese Klasse wurde von Michel Weimerskirch beigesteuert. |
| `expOEAuditBranch` | class implements expAuditTaxonomyBranch | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezoe/classes/expoeauditbranch.php:11` | Der Audit-Taxonomiezweig des Online-Editors: die Wahl der Editor-Engine. |
| `expOEEditor` | class | [Registry](registry.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezoe/classes/expoeeditor.php:18` | Die Registry der Editor-Engines und die Helfer, die jeder Aufrufer teilt: die Templates, das Modul ezoe, die ezjscore-Funktionen und die expservices-Klasse alle… |
| `expOEEditorEngine` | interface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `extension/ezoe/classes/expoeeditorengine.php:21` | Eine Editor-Engine des Online-Editors: das, was ein ezxmltext-Attribut im Browser zu einem Editor macht. |
| `expOEEditorEngineBase` | abstract class implements expOEEditorEngine | [Strategie / Handler](strategie-handler.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/ezoe/classes/expoeeditorenginebase.php:14` | Bequeme Basis für Editor-Engines: liest den eigenen ezoe.ini-Block der Engine [Engine_] (ToolbarMap[]=, ExternalPlugins[]=, Scripts[], Styles[], Version,… |
| `expOETinyMCE3Engine` | class extends expOEEditorEngineBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/ezoe/classes/expoetinymce3engine.php:10` | Die Standard-Engine: eZ Online Editor auf TinyMCE 3.5, gerendert von ezxmltext_ezoe.tpl selbst. |
| `expOETinyMCE8Engine` | class extends expOEEditorEngineBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/ezoe/classes/expoetinymce8engine.php:10` | Die Opt-in-Engine: eZ Online Editor auf TinyMCE 8 (GPL), gerendert von ezxmltext_ezoe_tinymce8.tpl. |
| `expOEUrlException` | class extends RuntimeException | [Ausnahmehierarchie](ausnahme.md) | `extension/ezoe/classes/expoeurlexception.php:10` | Ein Grund, aus dem der Online-Editor es ablehnt, eine Datei von einer URL abzurufen; die Meldung ist für den Benutzer bestimmt. |
| `expOEUrlFetcher` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezoe/classes/expoeurlfetcher.php:23` | Ruft eine Datei von einer URL ab, für den Upload-Reiter des Online-Editors („Von einer URL“). |
| `Exponential\View\Extension\Ezoe\Ezoe\Dialog` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezoe/classes/runnable/views/ezoe/dialog.php:10` | Der Code von extension/ezoe/modules/ezoe/dialog.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezoe\Ezoe\EmbedView` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezoe/classes/runnable/views/ezoe/embed_view.php:10` | Der Code von extension/ezoe/modules/ezoe/embed_view.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezoe\Ezoe\Load` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezoe/classes/runnable/views/ezoe/load.php:10` | Der Code von extension/ezoe/modules/ezoe/load.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezoe\Ezoe\Relations` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezoe/classes/runnable/views/ezoe/relations.php:10` | Der Code von extension/ezoe/modules/ezoe/relations.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezoe\Ezoe\SpellcheckRpc` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezoe/classes/runnable/views/ezoe/spellcheck_rpc.php:58` | Gibt einen Anfragewert anhand des Namens ohne Magic Quoting zurück. |
| `Exponential\View\Extension\Ezoe\Ezoe\Tags` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezoe/classes/runnable/views/ezoe/tags.php:11` | Der Code von extension/ezoe/modules/ezoe/tags.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezoe\Ezoe\Upload` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezoe/classes/runnable/views/ezoe/upload.php:10` | Der Code von extension/ezoe/modules/ezoe/upload.php, in eine Klasse verschoben (#207 Stufe 1). |
| `eZOEInputParser` | class extends eZXMLInputParser | – | `extension/ezoe/ezxmltext/handlers/input/ezoeinputparser.php:41` | – |
| `ezoeServerFunctions` | class extends ezjscServerFunctions | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezoe/classes/ezoeserverfunctions.php:35` | – |
| `eZOETemplateUtils` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezoe/autoloads/ezoetemplateutils.php:37` | – |
| `eZOEXMLInput` | class extends eZXMLInputHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezoe/ezxmltext/handlers/input/ezoexmlinput.php:40` | – |
| `GoogleSpell` | class extends SpellChecker | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezoe/modules/ezoe/classes/GoogleSpell.php:9` | – |
| `Moxiecode_JSON` | class | – | `extension/ezoe/modules/ezoe/classes/utils/mcejson.php:377` | Diese Klasse verarbeitet JSON. |
| `Moxiecode_JSONReader` | class | – | `extension/ezoe/modules/ezoe/classes/utils/mcejson.php:10` | – |
| `PSpell` | class extends SpellChecker | – | `extension/ezoe/modules/ezoe/classes/PSpell.php:9` | – |
| `PSpellShell` | class extends SpellChecker | – | `extension/ezoe/modules/ezoe/classes/PSpellShell.php:9` | – |
| `SpellChecker` | class | – | `extension/ezoe/modules/ezoe/classes/SpellChecker.php:9` | – |

## Modulansichten (8)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezoe/relations` | `relations.php` | editor | `extension/ezoe/modules/ezoe/module.php:34` |
| `/ezoe/upload` | `upload.php` | editor | `extension/ezoe/modules/ezoe/module.php:41` |
| `/ezoe/tags` | `tags.php` | editor | `extension/ezoe/modules/ezoe/module.php:48` |
| `/ezoe/dialog` | `dialog.php` | editor | `extension/ezoe/modules/ezoe/module.php:55` |
| `/ezoe/embed_view` | `embed_view.php` | editor | `extension/ezoe/modules/ezoe/module.php:62` |
| `/ezoe/load` | `load.php` | editor | `extension/ezoe/modules/ezoe/module.php:68` |
| `/ezoe/spellcheck_rpc` | `spellcheck_rpc.php` | editor | `extension/ezoe/modules/ezoe/module.php:74` |
| `/ezoe/engine` | `engine.php` | editor | `extension/ezoe/modules/ezoe/module.php:146` |

## Richtlinienfunktionen (7)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `ezoe/relations` | Class, Section, Owner, Node, Subtree | `extension/ezoe/modules/ezoe/module.php:123` |
| `ezoe/editor` | Class, Section, Owner, Node, Subtree | `extension/ezoe/modules/ezoe/module.php:129` |
| `ezoe/relations` | – | `extension/ezoe/modules/ezoe/module.php:137` |
| `ezoe/editor` | – | `extension/ezoe/modules/ezoe/module.php:138` |
| `ezoe/search` | – | `extension/ezoe/modules/ezoe/module.php:139` |
| `ezoe/browse` | – | `extension/ezoe/modules/ezoe/module.php:140` |
| `ezoe/disable_editor` | – | `extension/ezoe/modules/ezoe/module.php:141` |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezoe_ini_section | `eZOETemplateUtils` | `extension/ezoe/autoloads/eztemplateautoload.php:13` |

## INI-Plugin-Punkte (7)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[ezoe]` | `expOEAuditBranch` | `extension/ezoe/settings/audit.ini.append.php:6` |
| `[ezjscServer_ezoe] Class` | `ezoeServerFunctions` | `extension/ezoe/settings/ezjscore.ini.append.php:6` |
| `[EditorSettings] Engines[tinymce3]` | `expOETinyMCE3Engine` | `extension/ezoe/settings/ezoe.ini:30` |
| `[EditorSettings] Engines[tinymce8]` | `expOETinyMCE8Engine` | `extension/ezoe/settings/ezoe.ini:31` |
| `[SpellChecker] config[general.engine]` | `GoogleSpell` | `extension/ezoe/settings/ezoe.ini:336` |
| `[InputSettings] AliasClasses[eZSimplifiedXMLInput]` | `eZOEXMLInput` | `extension/ezoe/settings/ezxml.ini.append.php:11` |
| `[Registry_ezoeengines] Entries[Engines]` | `expOEEditorEngine` | `extension/ezoe/settings/rad.ini.append.php:17` |
