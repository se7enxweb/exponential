---
title: Exponential als Anwendungsplattform
audience: app-creator, developer, project-manager
category: app-creation
order: 30
summary: Was Exponential einer Anwendung von Haus aus bietet - Inhaltsmodell, Rechte, Versionen, Sprachen, Module, Templates, Workflows, Cron, REST, Warteschlangen, KI - und wie Sie entscheiden, was Sie darauf bauen und was Sie außerhalb halten.
keywords: Anwendungsplattform, Framework, Anwendungen bauen, Inhaltsmodell, Rechte, Module, Workflows, Cronjobs, Warteschlangen, Anwendungsentwickler, application platform, CMF, framework, build applications, content model, permissions, modules, REST, headless, workflows, cronjobs, queues, Exponential Intelligence, app creator
type: help
---
# Exponential als Anwendungsplattform

Diese Seite ist für Anwendungsentwickler, die überlegen, eine Anwendung auf Exponential zu bauen: ein Intranet-Werkzeug,
ein Portal, einen Kundenbereich, eine datengetriebene Website, ein Headless-Backend. Exponential ist "ein
professionelles PHP-Anwendungs-Framework mit fortgeschrittener CMS-Funktionalität" (laut README); diese Seite führt
auf, was Ihnen dieses Framework bietet und wie Sie entscheiden, was Sie darauf bauen.

## Inhalt

1. [Was Sie bekommen, ohne Code zu schreiben](#was-sie-bekommen-ohne-code-zu-schreiben)
2. [Was Sie schreiben](#was-sie-schreiben)
3. [Arten von Anwendungen](#arten-von-anwendungen)
4. [Wann Sie darauf bauen sollten und wann nicht](#wann-sie-darauf-bauen-sollten-und-wann-nicht)
5. [Wie es weitergeht](#wie-es-weitergeht)

## Was Sie bekommen, ohne Code zu schreiben

| Bedarf einer Anwendung | Exponential bietet | Lesen Sie |
|---|---|---|
| Ein Datenmodell | Inhaltsklassen mit typisierten Attributen, Versionen, Übersetzungen, einen Baum | [Das Inhaltsmodell](../../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| Benutzer und Rechte | Benutzer, Gruppen, Rollen, Richtlinien mit Einschränkungen | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |
| Eine Verwaltungsoberfläche | die Administration für jede Inhaltsklasse, ohne Zusatzaufwand | [Rundgang durch die Administration](../../rundgang-durch-die-administration.md) |
| Geschäftsprozesse | Workflows an Triggern, Objektzustände | [Workflows](../../../anleitungen/workflows.md), [Objektzustände](../../../anleitungen/objektzustaende.md) |
| Geplante Arbeit | Cronjob-Teile | [Cronjobs](../../../anleitungen/cronjobs.md) |
| Hintergrundjobs | die Job-Warteschlange `expqueue` und Inhalts-Jobs | [Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md) |
| Eine API | REST v1/v2 mit OAuth2 und API-Schlüsseln; über tausend benannte Dienste in `expservices` | [REST-API v1 und v2](rest-api.md), [Entfernte Dienste](entfernte-dienste-expservices.md) |
| Suche | eingebaut oder Solr | [Suche auf der Website](../website-ersteller/suche-auf-der-website.md) |
| Audit | ein Audit-Trail der Änderungen | [Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) |
| KI-Unterstützung | Exponential Intelligence (`expai`) | [Exponential Intelligence für Anwendungen](exponential-intelligence.md) |
| Auslieferung | Velocity, Apache, nginx, FrankenPHP | [Die Website ausliefern](../../../installation/08-die-website-ausliefern.md) |

## Was Sie schreiben

Alles, was spezifisch für Ihre Anwendung ist, lebt in einer **Erweiterung**: einem Verzeichnis unter `extension/` mit
Modulen (Adressen und Ansichten), Klassen, Templates, Einstellungen, Datentypen, Workflow-Ereignissen,
Cronjob-Skripten und Konsolenbefehlen. Setup > RAD führt 68 Erweiterungspunkte auf und schreibt für die meisten davon
lauffähige Gerüste ([RAD-Werkzeuge](rad-werkzeuge.md)).

## Arten von Anwendungen

| Art | Gebaut aus |
|---|---|
| Eine Datenanwendung mit Formularen und Listen | Inhaltsklassen + eigene Ansichten für besondere Bildschirme |
| Ein Portal oder Mitgliederbereich | Sektionen, Rollen, Registrierung, eigene Module |
| Ein Headless-Backend für Apps | Inhaltsklassen + REST oder expservices ([Headless Exponential](headless-exponential.md)) |
| Ein Prozesswerkzeug (Freigaben, Anträge) | Objektzustände, Workflows, Benachrichtigungen |
| Eine Integrationsdrehscheibe | Cronjobs, Warteschlangen, REST-Clients in PHP-Klassen |

## Wann Sie darauf bauen sollten und wann nicht

Bauen Sie darauf, wenn die Daten inhaltsartig sind (Menschen bearbeiten sie, sie haben Versionen, Rechte und Sprachen),
wenn Ihnen die eingebaute Administration eine eigene Verwaltungsoberfläche erspart und wenn Sie einen Ort für Website und
Anwendung wollen.

Halten Sie es außerhalb, wenn der Kern aus transaktionalen Massendaten ohne redaktionelles Leben besteht (ein
Kontobuch, Telemetrie); speichern Sie diese in eigenen Tabellen ([Persistente Objekte und eigene Tabellen](persistente-objekte-und-eigene-tabellen.md))
oder in einem separaten Dienst und binden Sie ihn an.

## Wie es weitergeht

- [Architektur für Anwendungsentwickler](architektur-fuer-anwendungsentwickler.md)
- [Eine Entwicklungsumgebung einrichten](entwicklungsumgebung.md)
- [Ihre erste Erweiterung](ihre-erste-erweiterung.md)
- Das Buch für Erweiterungsentwickler unter `doc/eng-us/help/extension-development/`, sofern es in Ihrer Installation
  vorhanden ist.

## Verwandte Seiten

- [Anwendungsentwickler: hier beginnen](anwendungsentwickler-hier-beginnen.md)
- [Was Exponential ist: die Produktfamilie](../../fuehrung/was-exponential-ist.md)
- [Standards](../../../../eng-us/standards/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
