---
title: Exponential Intelligence erweitern
audience: app-creator, developer
category: app-creation
order: 400
summary: expai aus Ihrer eigenen Erweiterung heraus erweitern - eine Feldaktion per Einstellungen, ein Werkzeug, das der Assistent aufrufen kann (expAiTool), ein Anbieter für einen anderen KI-Dienst (expAiProvider), Panel-Abschnitte, JavaScript-Hooks und die Register, die die Übersicht von RAD prüft.
keywords: erweitern, Feldaktion, Werkzeug, Anbieter, Panel, JavaScript-Hooks, Register, Anwendungsentwickler, expai, extend, field action, tool, expAiTool, expAiToolContext, provider, expAiProvider, expAiProviderBase, panel, JavaScript hooks, rad.ini registries, app creator
type: help
---
# Exponential Intelligence erweitern

Diese Seite ist für Anwendungsentwickler, die Exponential Intelligence ergänzen, ohne es zu verändern: Alles wird aus
Ihrer eigenen Erweiterung hinzugefügt, über Einstellungen und kleine Klassen. Quelle: `extension/expai/doc/extending.md`,
deren Beispiele für expai 0.0.1.0 ausgeführt wurden; die Beispielerweiterung liegt in
`extension/expai/doc/examples/expaiexample`.

![Exponential Intelligence, Modelle: die Modelle, die jeder Anbieter bereitstellt, welches der Standard ist und was jedes tun darf](../../../../eng-us/help/images/creators/ai-models.webp)

## Inhalt

1. [Ihre Erweiterung](#ihre-erweiterung)
2. [Eine Feldaktion, nur per Einstellungen](#eine-feldaktion-nur-per-einstellungen)
3. [Ein Werkzeug, das der Assistent aufrufen kann](#ein-werkzeug-das-der-assistent-aufrufen-kann)
4. [Ein Anbieter](#ein-anbieter)
5. [Panel-Abschnitte und JavaScript-Hooks](#panel-abschnitte-und-javascript-hooks)
6. [Ihre Ergänzungen prüfen](#ihre-ergänzungen-prüfen)

## Ihre Erweiterung

```text
extension/myai/
    settings/expai.ini.append.php      what you add to expai's lists
    settings/design.ini.append.php     DesignExtensions[], BackendJavaScriptList[] (optional)
    classes/...                        tools, field action handlers, a provider
    design/admin/templates/...         panel or edit form sections
```

Aktivieren Sie sie dort, wo expai aktiv ist, erzeugen Sie die Autoloads neu, leeren Sie die INI- und Template-Caches
(einschließlich `ezjscore-packer` und `template-block`) und starten Sie Velocity neu.

## Eine Feldaktion, nur per Einstellungen

```ini
# settings/expai.ini.append.php
[EditViewSettings]
FieldActions[]=headline

[FieldAction_headline]
Label=Headline
Handler=expAiPromptFieldAction
Datatypes[]
Datatypes[]=ezstring
Input=none
Instruction=Write a short, catchy headline (at most eight words) for this content, based on its other fields.
```

`Input` ist `none`, `instruction`, `optional_instruction` oder `language`.

## Ein Werkzeug, das der Assistent aufrufen kann

```ini
[ToolSettings]
Tools[]=count_children

[Tool_count_children]
Class=myAiToolCountChildren
```

Die Klasse erweitert `expAiTool` mit `description()`, `inputSchema()`, `execute( array $input, expAiToolContext $context )`,
`stepText()` und `stepArguments()`. Regeln aus der Dokumentation von expai:

- Lesen und schreiben Sie über `$context->reader` und `$context->writer`, die als aktueller Benutzer mit dessen Prüfungen
  laufen.
- Ein schreibendes Werkzeug gibt bei `writes()` `true` zurück, prüft `$context->checkDraftLimit()`, hält Entwürfe mit
  `expAiDraftLog::record()` fest und veröffentlicht nie.
- Werfen Sie `expAiException` für Fehler, die das Modell erfahren soll.
- Neue Werkzeuge werden in neuen Unterhaltungen angeboten.

Ein Lesedienst von expservices, der mit `'ai' => true` markiert ist, wird ohne eigene Klasse zu einem Werkzeug
([Backend-Dienste von ezjscore](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)).

## Ein Anbieter

Ein OpenAI-kompatibler oder Anthropic-kompatibler Server braucht keinen Code: eine Gruppe `[Provider_<id>]` mit
`Type=openai_chat` (oder `anthropic`) und Modellgruppen. Alles andere: eine Klasse, die `expAiProvider` implementiert
(oder `expAiProviderBase` erweitert), mit `providerId()`, `modelName()` und `send( array $request )`, die die interne
Form der Messages-API von expai in den Dienst und zurück übersetzt. Tragen Sie die Modelle in die Tabelle `[Pricing]`
ein, damit die Kosten bekannt sind.

## Panel-Abschnitte und JavaScript-Hooks

Abschnitte für Panel und Bearbeitungsformular sind Templates, die Ihre Erweiterung den Listen von expai hinzufügt;
JavaScript-Hooks sind Skriptdateien in der Backend-Skriptliste Ihres Designs. Folgen Sie den Abschnitten 3 und 4 von
`extension/expai/doc/extending.md` und halten Sie sich an die Admin-Regeln: keine Inline-Skripte oder -Styles
([Content Security Policy für Ihren Code](content-security-policy.md)).

## Ihre Ergänzungen prüfen

expai deklariert Register in `settings/rad.ini.append.php`; **Setup > RAD > Übersicht der Erweiterungspunkte**
(`setup/radsurvey/(show)/registries`) zählt Ihre Anbieter, Werkzeuge und Feldaktionen und zeigt jene, deren Klasse,
Template oder Block fehlt ([Erweiterungsoberfläche von RAD](../../../bc/6.0/rad-erweiterungsoberflaeche.md)).

## Verwandte Seiten

- [Exponential Intelligence (expai) für Anwendungen](exponential-intelligence.md)
- [RAD-Werkzeuge: schneller bauen](rad-werkzeuge.md)
- [expai](../../../funktionen/6.0/erweiterungen/expai.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
