---
title: Den Assistenten erweitern
audience: ai-agents, developer, app-creator
category: ai-agents
order: 37
summary: Ein Überblick darüber, was eine Erweiterung Exponential Intelligence hinzufügen kann, ohne es zu ändern - Feldaktionen allein über Einstellungen, Werkzeuge, die das Modell aufrufen kann, freigeschaltete Lesedienste, Anbieter, Abschnitte für Panel und Bearbeitungsformular und JavaScript-Hooks - mit den Regeln, die jede Ergänzung einhalten muss (als der Benutzer laufen, nur Entwürfe schreiben), und wie Sie Ergänzungen unter Setup > RAD prüfen.
keywords: expai erweitern, Feldaktion, FieldActions, Werkzeug, expAiTool, Anbieter, expAiProvider, Dienstwerkzeug, Panel-Abschnitt, JavaScript-Hook, RAD-Übersicht, Erweiterungspunkte, extending expai, field action, tool, provider, service tool, panel section, JavaScript hook, RAD survey, extension points
type: help
---
# Den Assistenten erweitern

Diese Seite richtet sich an Entwickler, die möchten, dass Exponential Intelligence etwas Bestimmtes für ihre Website tut. Alles wird
aus Ihrer eigenen Erweiterung hinzugefügt, über Einstellungen und kleine Klassen; der Assistent selbst bleibt unverändert. Die
Aufgabenanleitung mit Code ist [Exponential Intelligence erweitern](../../ersteller/anwendungsentwickler/exponential-intelligence-erweitern.md);
diese Seite ist der Überblick.

## Inhalt

1. [Was Sie hinzufügen können](#was-sie-hinzufügen-können)
2. [Regeln, die jede Ergänzung einhält](#regeln-die-jede-ergänzung-einhält)
3. [Die kleinste Ergänzung wählen](#die-kleinste-ergänzung-wählen)
4. [Ihre Ergänzungen prüfen](#ihre-ergänzungen-prüfen)

## Was Sie hinzufügen können

| Ergänzung | Wie | Beispiel |
|---|---|---|
| Eine Feldaktion | Nur Einstellungen: `[EditViewSettings] FieldActions[]` und eine Gruppe `[FieldAction_<id>]` mit Bezeichnung, Datentypen, Eingabe und Anweisung | Eine Schaltfläche "Headline" an Textzeilenfeldern |
| Ein Werkzeug, das das Modell aufrufen kann | Eine Klasse, die `expAiTool` erweitert, aufgeführt in `[ToolSettings] Tools[]` | Die Kinder eines Knotens zählen |
| Ein Lesedienst als Werkzeug | `'ai' => true` in einer expservices-Deklaration, keine Klasse | [Dienstwerkzeuge](dienstwerkzeuge.md) |
| Ein Anbieter | Eine Gruppe `[Provider_<id>]` für einen OpenAI- oder Anthropic-kompatiblen Server oder eine Klasse, die `expAiProvider` implementiert | Das eigene Modell-Gateway eines Unternehmens |
| Abschnitte für Panel und Bearbeitungsformular | Templates, die Ihre Erweiterung den Listen von expai hinzufügt | Ein Abschnitt mit Hinweisen zum Hausstil |
| JavaScript-Hooks | Skriptdateien in der Backend-Skriptliste Ihres Designs, ohne Inline-Skripte | Reagieren, wenn ein Entwurf geschrieben wird |

## Regeln, die jede Ergänzung einhält

Aus der Dokumentation der Erweiterung expai:

- Lesen und schreiben Sie über den Leser und Schreiber des Kontexts, die **als der aktuelle Benutzer** mit den Prüfungen des Kernels
  laufen.
- Ein schreibendes Werkzeug hält seine Entwürfe fest und **veröffentlicht nie**.
- Fehler, die das Modell hören soll, werden als eigener Ausnahmetyp des Assistenten geworfen, damit das Modell seinen Aufruf
  korrigieren kann.
- Neue Werkzeuge werden in neuen Gesprächen angeboten.
- Admin-Templates und -Skripte halten die Admin-Regeln ein: keine Inline-Skripte oder -Styles, damit die Content Security Policy hält.

## Die kleinste Ergänzung wählen

1. Kann eine **Feldaktion** über Einstellungen es leisten? Dann braucht es keinen Code.
2. Gibt ein **Dienst** bereits zurück, was das Modell braucht? Schalten Sie ihn frei.
3. Erst dann schreiben Sie eine **Werkzeug**-Klasse.
4. Nur für eine inkompatible API schreiben Sie eine **Anbieter**-Klasse; kompatible Server brauchen nur Einstellungen.

Je kleiner die Ergänzung, desto weniger gibt es zu prüfen und desto weniger Wege gibt es, wie ein Modell sie missbrauchen kann.

## Ihre Ergänzungen prüfen

expai deklariert seine Register für die RAD-Werkzeuge: **Setup > RAD > Extension point survey** zählt Ihre Anbieter, Werkzeuge und
Feldaktionen und zeigt alle, deren Klasse, Template oder Block fehlt
([RAD-Erweiterungsoberfläche](../../../bc/6.0/rad-erweiterungsoberflaeche.md)). Aktivieren Sie dann die Erweiterung, erzeugen Sie die
Autoloads neu, leeren Sie die Caches und probieren Sie die Ergänzung mit einem Testredakteur aus.

## Verwandte Seiten

- [Exponential Intelligence erweitern](../../ersteller/anwendungsentwickler/exponential-intelligence-erweitern.md)
- [Exponential Intelligence (expai) für Anwendungen](../../ersteller/anwendungsentwickler/exponential-intelligence.md)
- [Das Schreibtor und die Probe](das-schreibtor-und-die-probe.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
