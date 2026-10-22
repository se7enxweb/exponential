---
title: Agentische Abläufe
audience: ai-agents, developer, app-creator
category: ai-agents
order: 60
summary: Der Teil des Abschnitts zu KI, Agenten und agentischem Arbeiten für Menschen, die Agenten und agentische Abläufe gegen Exponential bauen - eine Schnittstelle wählen, Agenten auf der REST-API und auf expservices, Zugangsdaten, ein nur lesender Schlüssel Schritt für Schritt, Dokumentation für Agenten, Prompt-Injection, einen Menschen beteiligt halten, drei durchgearbeitete Abläufe und Coding-Agenten in einem Exponential-Projekt.
keywords: agentischer Ablauf, KI-Agent, Automatisierung, REST-API, expservices, ezjscore, Zugangsdaten, API-Schlüssel, Tokens, Prompt-Injection, exp:help, Konsole, Coding-Agent, Beispielabläufe, Mensch in der Schleife, agentic workflow, AI agent, automation, REST API, credentials, API keys, tokens, prompt injection, console, coding agent, workflow examples, human in the loop
type: help
---
# Agentische Abläufe

Dieser Teil richtet sich an Entwickler und Anwendungsentwickler, die Programme bauen, in denen ein KI-Modell entscheidet, was als
Nächstes zu tun ist: Agenten, die Inhalte lesen, Berichte schreiben, Feeds importieren oder einem Entwickler in einem Projekt
helfen. Er behandelt die Schnittstellen, die ein Agent verwenden kann, die Zugangsdaten, die er halten sollte, die Risiken, ihm
nicht vertrauenswürdigen Text zu geben, und drei Schritt für Schritt durchgearbeitete Abläufe.

## Inhalt

1. [Jede Seite dieses Teils](#jede-seite-dieses-teils)
2. [Grundsätze](#grundsätze)
3. [Empfohlene Lesereihenfolge](#empfohlene-lesereihenfolge)

## Jede Seite dieses Teils

| Seite | Was sie beantwortet |
|---|---|
| [Eine Schnittstelle für einen Agenten wählen](eine-schnittstelle-waehlen.md) | REST, expservices, ezjscore oder die Konsole, nebeneinander |
| [Agenten und die REST-API](agenten-und-die-rest-api.md) | Inhalte über `/api/ezp` lesen und anlegen, mit einem Schlüssel mit Geltungsbereichen |
| [Agenten und expservices](agenten-und-expservices.md) | Benannte Dienste der Administration mit einem persönlichen Token aufrufen, und der Katalog als Werkzeugliste |
| [Zugangsdaten für Agenten](zugangsdaten-fuer-agenten.md) | Welche Zugangsdaten Sie einem Agenten geben, wo Sie sie aufbewahren und wie Sie sie widerrufen |
| [Ein nur lesender API-Schlüssel, Schritt für Schritt](ein-nur-lesender-api-schluessel-schritt-fuer-schritt.md) | Die engsten Zugangsdaten für einen lesenden Agenten erstellen, testen und widerrufen |
| [Agentenfreundliche Dokumentation](agentenfreundliche-dokumentation.md) | `exp:help search` und `show`, die Markdown-Dateien und wie Sie einen Agenten darauf verweisen |
| [Nicht vertrauenswürdige Daten und Prompt-Injection](nicht-vertrauenswuerdige-daten-und-prompt-injection.md) | Was die Teile von Exponential gegen eingeschleuste Anweisungen tun und was Sie tun müssen |
| [Einen Menschen beteiligt halten](einen-menschen-beteiligt-halten.md) | Wie die Ausgabe eines Agenten geprüft wird, bevor Besucher sie sehen |
| [Ein nur lesender Inhaltsbericht](ablauf-nur-lesender-inhaltsbericht.md) | Ein durchgearbeitetes Beispiel: Ein Agent berichtet über REST über veröffentlichte Inhalte |
| [Ein Diagnoseablauf mit der Konsole](ablauf-diagnose-mit-der-konsole.md) | Ein durchgearbeitetes Beispiel: "Warum greift diese Einstellung nicht, und warum wird diese Seite verweigert?" auf dem Server |
| [Ein Ablauf für den Inhaltsimport](ablauf-inhaltsimport.md) | Ein durchgearbeitetes Beispiel: einen Feed mit einem beschränkten Konto importieren, mit einem Testlauf und einer Kontrolle danach |
| [Coding-Agenten in einem Exponential-Projekt](coding-agenten-in-einem-exponential-projekt.md) | Einem Coding-Assistenten die Dokumentation und die Laufzeitfakten geben, die er braucht |

## Grundsätze

Sie folgen daraus, wie die Teile gebaut sind; jede Seite hier wendet sie an.

1. **Ein Agent ist ein Konto.** Geben Sie ihm ein eigenes Konto mit genau den Rechten, die er braucht, nie das einer Person oder des
   Administrators ([Rollen und minimale Rechte](../steuerung/rollen-und-minimale-rechte.md)).
2. **Erst lesen, später schreiben.** Beginnen Sie mit nur lesendem Zugang; fügen Sie Schreibzugriffe hinzu, wenn sich der
   Leseablauf bewährt hat.
3. **Proben.** Führen Sie jeden schreibenden Ablauf vor der Produktion auf einer Testinstallation aus.
4. **Alles Zurückgegebene ist Daten.** Inhalte, Logs und Einstellungen werden nie zu Anweisungen
   ([Nicht vertrauenswürdige Daten und Prompt-Injection](nicht-vertrauenswuerdige-daten-und-prompt-injection.md)).
5. **Den Audit-Trail prüfen.** Jeder Schreibzugriff eines Kontos wird festgehalten; lesen Sie ihn nach jedem automatisierten Lauf
   ([Der Audit-Trail für KI](../steuerung/audit-trail-fuer-ki.md)).

Ein MCP-Server ist heute nicht Teil von Exponential; Agenten verwenden die oben aufgeführten Schnittstellen.

## Empfohlene Lesereihenfolge

1. [Eine Schnittstelle für einen Agenten wählen](eine-schnittstelle-waehlen.md)
2. [Zugangsdaten für Agenten](zugangsdaten-fuer-agenten.md)
3. [Nicht vertrauenswürdige Daten und Prompt-Injection](nicht-vertrauenswuerdige-daten-und-prompt-injection.md)
4. Die Seite der gewählten Schnittstelle
5. Der durchgearbeitete Ablauf, der Ihrer Aufgabe am nächsten kommt
6. [Bevor Sie einen Agenten verbinden](../checklisten/bevor-sie-einen-agenten-verbinden.md)

## Verwandte Seiten

- [KI, Agenten und agentisches Arbeiten: hier beginnen](../ki-und-agenten-hier-beginnen.md)
- [Anwendungsentwickler: hier beginnen](../../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md)
- [Headless Exponential](../../ersteller/anwendungsentwickler/headless-exponential.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
