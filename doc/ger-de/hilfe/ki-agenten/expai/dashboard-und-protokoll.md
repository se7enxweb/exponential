---
title: Das Dashboard und das Protokoll
audience: ai-agents, editor, admin
category: ai-agents
order: 36
summary: Wo Exponential Intelligence zeigt, was es getan hat und was es gekostet hat - Dashboard > Exponential Intelligence mit Kosten, Tokens, Anfragen, Entwürfen, Grenzen und Start writing; EI: Log mit jeder Anfrage und den Versionen, die es geschrieben hat; die Menüeinträge und die Richtlinien, die sie öffnen.
keywords: Dashboard, EI: Log, expai/dashboard, expai/log, Nutzung, Kosten, Tokens, Anfragen, Entwürfe, Grenzen, Start writing, Menü, EI: Vector, EI: Chains, EI: Budgets, EI: Models, EI: Summary, dashboard, usage, cost, requests, drafts, limits, menu
type: help
---
# Das Dashboard und das Protokoll

Diese Seite richtet sich an Redakteure, die ihre eigene Nutzung des Assistenten sehen möchten, und an Administratoren, die die ganze
Website beobachten. Alles, was der Assistent tut, ist an zwei Orten sichtbar: auf der Dashboard-Seite und im Protokoll
([Dokumentation der Erweiterung: Dashboard](https://github.com/se7enxweb/expai/blob/main/doc/dashboard.md)).

## Inhalt

1. [Die Dashboard-Seite](#die-dashboard-seite)
2. [Start writing](#start-writing)
3. [EI: Log](#ei-log)
4. [Die Menüeinträge](#die-menüeinträge)

## Die Dashboard-Seite

**Dashboard > Exponential Intelligence** (`expai/dashboard`, Richtlinie `expai/write`) zeigt:

- die geschätzten Kosten, Tokens, Anfragen und Entwürfe, mit Trends und einem Diagramm je Tag;
- die Grenzen des gewählten Zeitraums und wie viel von jeder verbraucht ist;
- jüngste Entwürfe und die Versionen, die auf Ihre Freigabe warten;
- mit `expai/log` die ganze Website und die Benutzer mit der höchsten Nutzung statt nur Ihrer eigenen Nutzung;
- den Status, zum Beispiel "Switched off", wenn der Hauptschalter aus ist.

## Start writing

Die Karte **Start writing** des Dashboards wählt einen Ort vor (den Standardknoten des Dashboards oder die Inhaltswurzel) und sagt,
dass der Assistent dort und überall darunter arbeitet, innerhalb Ihrer eigenen Berechtigungen. **Write** öffnet den Reiter
Intelligence dieses Knotens mit Ihrer ausgefüllten Anfrage; Sie drücken Send. **change** lässt Sie zuerst einen anderen Ort wählen.

## EI: Log

**EI: Log** (`expai/log`) listet Anfragen und die Versionen, die der Assistent geschrieben hat:

- mit `expai/write` Ihre eigenen Anfragen und Versionen; mit `expai/log` die aller, mit einem Filter nach Benutzer;
- je Anfrage: Modell, Anbieter, Region, Tokens, geschätzte Kosten, Zeit, die verwendeten Werkzeuge, Fehler und Ablehnungen;
- Durchgänge mit ihren Summen; das Öffnen eines Durchgangs zeigt seine Anfragen.

Die Texte von Anfragen und Antworten werden nicht im Protokoll gespeichert
([Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md#was-die-installation-behält)).

## Die Menüeinträge

| Menü | Eintrag | Öffnet | Richtlinien |
|---|---|---|---|
| Dashboard | Exponential Intelligence | `expai/dashboard` | `expai/write` |
| Dashboard | EI: Vector | `expai/vector` | `expai/write`, `expai/vector` |
| Dashboard | EI: Chains | `expai/chain` | `expai/write`, `expai/chain` |
| Dashboard | EI: Log | `expai/log` | `expai/write` |
| Dashboard | EI: Help | die Dokumentation der Erweiterung im Hilfe-Center | `exphelp/read` |
| Design | Missing alt text | `expai/alttext` | `expai/write` |
| Setup | EI: Summary | `expai/summary` | `expai/summary` |
| Setup | EI: Models | `expai/models` | `expai/model_manage` |
| Setup | EI: Budgets | `expai/budgets` | `expai/log` |

Einträge werden nur Benutzern mit ihren Richtlinien gezeigt. Jede expai-Seite hat außerdem einen Link "?" zu ihrer Seite im
Hilfe-Center.

## Verwandte Seiten

- [Kostenkontrolle](../steuerung/kostenkontrolle.md)
- [Berechtigungen für KI-Funktionen](berechtigungen-fuer-ki-funktionen.md)
- [Der Audit-Trail für KI](../steuerung/audit-trail-fuer-ki.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
