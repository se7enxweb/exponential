---
title: Headless mit der REST API
audience: businesses, app-creator, project-manager
category: business-solutions
order: 30
summary: Was es für ein Unternehmen bedeutet, Exponential als Backend für die Inhalte einer App oder eines eigenen Frontends zu verwenden - was in Exponential bleibt, was Ihre Entwickler bauen, Fähigkeiten und Grenzen der REST API, Vorschau, Caching und die Punkte für die Beauftragung einer Agentur.
keywords: headless, REST API, mobile App, Frontend, Single Page Application, Backend für Inhalte, ezprestapi, Vorschau, Caching, Berechtigungen, Agenturbriefing, Unternehmen, mobile app, front end, single page application, content back end, preview, caching, permissions, agency brief, business
type: help
---
# Headless mit der REST API

Diese Seite ist für Führungskräfte und Produktverantwortliche, die eine App oder ein eigenes Frontend planen, das in
Exponential verwaltete Inhalte zeigt. „Headless“ bedeutet: Exponential behält die Inhalte, die Bearbeitung und die
Berechtigungen, während ein anderes Programm die Darstellung übernimmt. Diese Seite erklärt, was diese Aufteilung für Ihr
Team und Ihr Budget bedeutet, was die API heute kann und was nicht, und was in ein Briefing für Entwickler gehört. Die
technische Anleitung ist [Headless Exponential](../../ersteller/anwendungsentwickler/headless-exponential.md).

## Inhalt

1. [Was in Exponential bleibt und was Sie bauen](#was-in-exponential-bleibt-und-was-sie-bauen)
2. [Welche API für welche Aufgabe](#welche-api-für-welche-aufgabe)
3. [Was die REST API tut](#was-die-rest-api-tut)
4. [Dinge, die früh zu planen sind](#dinge-die-früh-zu-planen-sind)
5. [Was es (noch) nicht gibt](#was-es-noch-nicht-gibt)
6. [Entwickler oder eine Agentur beauftragen](#entwickler-oder-eine-agentur-beauftragen)

## Was in Exponential bleibt und was Sie bauen

Aus [Headless Exponential](../../ersteller/anwendungsentwickler/headless-exponential.md#was-sie-behalten-und-was-sie-ersetzen):

| Bleibt in Exponential | Baut Ihr Frontend |
|---|---|
| Inhaltsklassen, Bearbeitung, Versionen, Übersetzungen, Workflows | Seiten-Templates, Menüs, der Seitenrahmen |
| Rollen und Richtlinien, bei jedem API-Aufruf geprüft | Routing und Behandlung der URLs |
| Die Administration und der Layout-Editor für Redakteure | Das Rendern von Layouts |
| Suche, Medienspeicher, Bildaliase | Zustand und Caching im Client |

Die Arbeit Ihrer Redakteure ändert sich nicht: Sie veröffentlichen wie bisher in der Administration. Was Sie
hinzufügen, ist ein zweites Programm, das entworfen, gebaut, gehostet und gepflegt werden muss.

## Welche API für welche Aufgabe

| API | Für | Lesen |
|---|---|---|
| REST v1 und v2 (`ezprestapi`) | Knoten, Unterelemente und Felder lesen; Inhalte anlegen und entfernen; OAuth für Apps | [REST API v1 und v2 für Unternehmen](rest-api-v1-und-v2-fuer-unternehmen.md) |
| expservices | Die Funktionen der Administration als Dienste, mit Discovery und Tokens | [Entfernte Dienste](entfernte-dienste.md) |
| Serverfunktionen von ezjscore | Kleine JSON-Endpunkte, die Ihre eigenen Entwickler schreiben | [JSON-Endpunkte mit ezjscore](../../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md) |

Für eine App, die Ihre Inhalte zeigt, ist die REST API die übliche Wahl.

## Was die REST API tut

Aus [REST API v1 und v2 für Anwendungen](../../ersteller/anwendungsentwickler/rest-api.md):

- **Liest** einen Knoten, seine Unterelemente (seitenweise, mit Offset und Limit), die Zahl der Unterelemente und Felder,
  nach Knoten- oder Objekt-ID, in v1 und v2.
- **Legt Inhalte an und entfernt sie** nur in v2.
- Liefert den Wert eines Rich-Text-Felds als reinen Text. `OutputFormat=xhtml` ergänzt den von Templates gerenderten
  Knoten, der erst Markup liefert, wenn ein Entwickler die `rest`-View-Templates dafür geschrieben hat.
- Liefert ein Bildfeld nur als Pfad seiner Originaldatei, ohne die verkleinerten Fassungen (Aliase). Die App fragt
  die Adressen der Aliase über die entfernten Dienste ab (`expimage::url`, `expimage::urls`); definieren Sie Aliase
  für die Größen, die Ihre App braucht.
- **Prüft bei jedem Lese- und Schreibzugriff die Richtlinien des Benutzers**, welche Zugangsdaten auch immer verwendet
  wurden.
- **Verlangt standardmäßig eine Authentifizierung**; öffentliche Frontends verwenden „a read-only integration user“
  (einen Integrationsbenutzer nur mit Lesezugriff), dessen Schlüssel auf Ihrem Server bleibt.

## Dinge, die früh zu planen sind

| Thema | Warum es wichtig ist | Quelle |
|---|---|---|
| **Vorschau** | Redakteure sehen die Vorschau mit den Templates der Website; ein Headless-Frontend „needs its own preview“ (braucht eine eigene Vorschau), eine geschützte Route, die Entwürfe liest. „Plan this early; it is the part teams forget.“ (Planen Sie das früh; das ist der Teil, den Teams vergessen.) | [Vorschau und Entwürfe](../../ersteller/anwendungsentwickler/headless-exponential.md#vorschau-und-entwürfe) |
| **Caching und Leeren** | Das REST-Framework hat einen Antwortcache; auch das Frontend oder ein CDN cacht und muss erfahren, wenn Inhalte veröffentlicht werden, zum Beispiel über ein Workflow-Ereignis, das Ihren Purge-Hook aufruft | [Caching und Leistung](../../ersteller/anwendungsentwickler/headless-exponential.md#caching-und-leistung) |
| **Links in Inhalten** | Links in Rich Text zeigen auf Adressen der Website; das Frontend bildet sie auf seine eigenen Routen ab | [Rich Text, Bilder und Links](../../ersteller/anwendungsentwickler/headless-exponential.md#rich-text-bilder-und-links) |
| **Zugangsdaten** | Welchen Schlüssel oder welches Token die App verwendet und wer es widerrufen kann | [Apps und API-Schlüssel](apps-und-api-schluessel.md) |
| **SEO** | Inhalte innerhalb einer App werden nicht indiziert; eine Website trägt weiterhin die Sichtbarkeit in Suchmaschinen | [Mobile Strategie](mobile-strategie.md#was-eine-app-ihnen-an-arbeit-hinzufügt) |

> **Tipp**
> „Start hybrid unless there is a reason not to“ (beginnen Sie hybrid, wenn nichts dagegen spricht): Behalten Sie die
> Website in den Templates von Exponential und fügen Sie die App über die API hinzu. Vorschau, Layouts und SEO bleiben
> für die Website einfach
> ([Hybrid: Templates und API zusammen](../../ersteller/anwendungsentwickler/headless-exponential.md#hybrid-templates-und-api-zusammen)).

## Was es (noch) nicht gibt

Ehrlich gesagt, aus den Quellen:

- Die REST-Route `listAtom` antwortet in v1 und v2 mit `500`; verwenden Sie stattdessen die Route `list` oder den
  RSS-Export der Website ([8. Known issue: listAtom](../../../anleitungen/rest-api-v1-und-v2.md#8-bekanntes-problem-listatom)).
- Der Kernel hat keine GraphQL-API. (Die README beschreibt REST- und GraphQL-APIs für Exponential Platform DXP, eine
  andere Produktlinie.)
- Die Dokumentation beschreibt keinen Dienst für Push-Benachrichtigungen; Benachrichtigungen an ein Telefon sind das
  eigene Backend Ihrer App.
- Layouts werden von den Templates der Website gerendert; ein Headless-Frontend rendert sie selbst.

## Entwickler oder eine Agentur beauftragen

Nehmen Sie in das Briefing auf:

- [ ] Welche Inhalte die App liest (Klassen, Ordner, Sprachen) und ob sie etwas schreibt.
- [ ] Welche Zugangsdaten sie verwendet, wo sie aufbewahrt und wie sie rotiert werden
      ([Apps und API-Schlüssel](apps-und-api-schluessel.md)).
- [ ] Die Rolle des Integrationsbenutzers: nur Lesen, beschränkt auf die Ordner, die die App braucht.
- [ ] Wie Redakteure Inhalte vor dem Veröffentlichen in der App in der Vorschau sehen.
- [ ] Wie die App erfährt, dass sich Inhalte geändert haben (Abfragen mit Grenzen oder ein Purge-Hook beim
      Veröffentlichen).
- [ ] Bildgrößen, die die App braucht, damit passende Aliase definiert werden, und wie die App ihre Adressen abruft.
- [ ] Was die App zeigt, wenn die API nicht antwortet ([Zuverlässigkeit des App-Backends](zuverlaessigkeit-des-app-backends.md)).
- [ ] Dass jede Anfrage über HTTPS geht (`rest.ini [Authentication] RequireHTTPS` kann reines HTTP ablehnen).

## Verwandte Seiten

- [REST API v1 und v2 für Unternehmen](rest-api-v1-und-v2-fuer-unternehmen.md)
- [Apps und API-Schlüssel](apps-und-api-schluessel.md)
- [Headless Exponential](../../ersteller/anwendungsentwickler/headless-exponential.md)
- [REST API v1 und v2 für Anwendungen](../../ersteller/anwendungsentwickler/rest-api.md)
- [Exponential als Anwendungsplattform](../../ersteller/anwendungsentwickler/exponential-als-anwendungsplattform.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
