---
title: Muster in extension/exp_oe_tiptap
audience: developer
category: standards
order: 434
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/exp_oe_tiptap mit den Entwurfsmustern.
keywords: extension/exp_oe_tiptap, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/exp_oe_tiptap

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/exp_oe_tiptap` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (14)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expOETiptapAIAnthropicProvider` | class implements expOETiptapAIProvider | [Strategie / Handler](strategie-handler.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaianthropicprovider.php:15` | Die Anthropic Messages API: POST {Endpoint} (Standard https://api.anthropic.com/v1/messages) mit den Headern x-api-key und anthropic-version und { mo… |
| `expOETiptapAIException` | class extends Exception | [Ausnahmehierarchie](ausnahme.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexception.php:18` | Ein Fehler der KI-Hooks, dessen Meldung dem Redakteur angezeigt werden darf (ezjscore sendet Ausnahmemeldungen als error_text an den Browser). |
| `expOETiptapAIExpaiProvider` | class implements expOETiptapAIProvider | [Fabrik und statische Fabrikmethoden](fabrik.md), [Strategie / Handler](strategie-handler.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexpaiprovider.php:32` | Die KI-Befehle über die Extension expai (Exponential Intelligence), [AISettings] Provider=expai: die Anfrage geht an das Modell, mit dem expai eingerichtet is… |
| `expOETiptapAIHttpClient` | class | – | `extension/exp_oe_tiptap/classes/ai/expoetiptapaihttpclient.php:16` | JSON-POST an einen KI-Anbieter mit curl: nur https (einfaches http nur zu einem Loopback-Host, für ein Modell auf dem eigenen Rechner), keine Weiterleitungen, ein Gesamt-Timeout… |
| `expOETiptapAIOpenAIProvider` | class implements expOETiptapAIProvider | [Strategie / Handler](strategie-handler.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiopenaiprovider.php:16` | Jeder Anbieter, der die OpenAI-Chat-Completions-API spricht: POST {Endpoint} mit { model, messages: [ system, user ], max_tokens, temperature }, Antwort ch… |
| `expOETiptapAIPrompts` | class | – | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiprompts.php:28` | Die Prompts der KI-Befehle. |
| `expOETiptapAIProvider` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiprovider.php:16` | Ein Textgenerierungs-Backend der KI-Hooks. |
| `expOETiptapAIRequestContext` | class | – | `extension/exp_oe_tiptap/classes/ai/expoetiptapairequestcontext.php:25` | Das bearbeitete Feld, das eine KI-Anfrage nennt (contentObjectId, version, attributeId, languageCode), und die Prüfung, ob der aktuelle Benutzer daran arbeiten darf, die sa… |
| `expOETiptapAIService` | class | [Fassade / Dienst](fassade.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiservice.php:19` | Die KI-Hooks auf dem Server: Prüfungen, Grenzen, Prompt, Anbieteraufruf, Antwort. |
| `expOETiptapEditor` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/exp_oe_tiptap/classes/expoetiptapeditor.php:23` | Tiptap als Engine der Editor-Registry von ezoe und was das Bearbeitungs-Template darüber wissen muss. |
| `expOETiptapEngine` | class extends expOEEditorEngineBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/exp_oe_tiptap/classes/expoetiptapengine.php:16` | Tiptap als Engine der Editor-Registry von ezoe: registriert durch settings/ezoe.ini.append.php (ezoe.ini [EditorSettings] Engines[tiptap]=expOETiptapEngin… |
| `expOETiptapServerFunctions` | class extends ezjscServerFunctions | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/exp_oe_tiptap/classes/expoetiptapserverfunctions.php:29` | ezjscore-Serverfunktionen des Tiptap-Editors (ezjscore.ini [ezjscServer_expoetiptap]). |
| `expOETiptapXMLInput` | class extends eZOEXMLInput | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/exp_oe_tiptap/classes/expoetiptapxmlinput.php:20` | Der ezxmltext-Eingabe-Handler von exp_oe_tiptap: eZOEXMLInput von ezoe, beibehalten für das, was 0.1.0 eingerichtet hat. |
| `exp_oe_tiptapInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/exp_oe_tiptap/ezinfo.php:7` | – |

## Modulansichten (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/exp_oe_tiptap/switch` | `switch.php` | switch | `extension/exp_oe_tiptap/modules/exp_oe_tiptap/module.php:16` |

## Richtlinienfunktionen (2)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `exp_oe_tiptap/switch` | – | `extension/exp_oe_tiptap/modules/exp_oe_tiptap/module.php:26` |
| `exp_oe_tiptap/ai` | – | `extension/exp_oe_tiptap/modules/exp_oe_tiptap/module.php:28` |

## INI-Plugin-Punkte (3)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ezjscServer_expoetiptap] Class` | `expOETiptapServerFunctions` | `extension/exp_oe_tiptap/settings/ezjscore.ini.append.php:7` |
| `[EditorSettings] Engines[tiptap]` | `expOETiptapEngine` | `extension/exp_oe_tiptap/settings/ezoe.ini.append.php:8` |
| `[InputSettings] AliasClasses[eZSimplifiedXMLInput]` | `expOETiptapXMLInput` | `extension/exp_oe_tiptap/settings/ezxml.ini.append.php:7` |
