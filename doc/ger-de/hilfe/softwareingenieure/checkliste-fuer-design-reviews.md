---
title: Checkliste für Design-Reviews
audience: software-engineer
category: software-engineering
order: 550
summary: Eine Checkliste und Vorlage für die Prüfung des technischen Entwurfs eines Systems auf Exponential - Anforderungen und Eignung, Produkt und Stack, Inhaltsmodell, Daten und Datenbank, Laufzeit und Caching, Skalierung und Verfügbarkeit, Sicherheit, Integration, Betrieb, Qualität, Aktualisierungen und Verantwortung - mit der Seite, die jede Frage beantwortet.
keywords: Design-Review, Architektur-Review, Checkliste, Vorlage, Entscheidungsprotokoll, Systementwurf, Anforderungen, Risiken, nicht-funktionale Anforderungen, Softwareingenieur, design review, architecture review, checklist, template, decision record, system design, requirements, risks, non-functional requirements
type: help
---
# Checkliste für Design-Reviews

Diese Seite ist eine Checkliste und eine Vorlage für die Prüfung des Entwurfs eines Systems auf Exponential: einer neuen
Website, einer Anwendung, einer Migration oder einer großen Erweiterung. Kopieren Sie die Abschnitte in Ihr
Entscheidungsprotokoll und beantworten Sie jeden Punkt mit einer Entscheidung und ihrer Quelle.

## Inhalt

1. [Eignung und Umfang](#eignung-und-umfang)
2. [Inhaltsmodell und Daten](#inhaltsmodell-und-daten)
3. [Laufzeit, Caching und Skalierung](#laufzeit-caching-und-skalierung)
4. [Sicherheit](#sicherheit)
5. [Integration](#integration)
6. [Betrieb](#betrieb)
7. [Qualität, Aktualisierungen und Verantwortung](#qualität-aktualisierungen-und-verantwortung)
8. [Eine Vorlage für ein Entscheidungsprotokoll](#eine-vorlage-für-ein-entscheidungsprotokoll)

## Eignung und Umfang

- [ ] Welches Produkt der Familie, und enthält es, was wir brauchen? ([Was Exponential ist](../fuehrung/was-exponential-ist.md))
- [ ] Welche Bedürfnisse decken vorhandene Funktionen oder Erweiterungen, und welche brauchen neuen Code?
      ([Exponential bewerten](exponential-bewerten.md))
- [ ] Ist die Lizenz (GNU GPL, Version 2 oder später) mit der Art vereinbar, wie wir unseren Code verbreiten?

## Inhaltsmodell und Daten

- [ ] Inhaltsklassen, Attribute und Datentypen; welche brauchen eigene Datentypen?
      ([Das Inhaltsmodell für Ingenieure](inhaltsmodell-fuer-ingenieure.md))
- [ ] Die Form des Inhaltsbaums, Sektionen und Objektzustände und wie die Berechtigungen ihnen folgen.
- [ ] Sprachen: wie viele Inhaltssprachen (höchstens 62), welche Rückfälle, Bedarf an Rechts-nach-links
      ([Internationalisierung und Rechts-nach-links](i18n-und-rechts-nach-links.md)).
- [ ] Eigene Tabellen: Persistenzklassen, Schemadateien für jeden Treiber
      ([Schemaänderungen und Migrationen](schemaaenderungen-und-migrationen.md)).
- [ ] Welche Datenbank, und wird sie in der CI getestet? ([Datenbanken: eine wählen und betreiben](datenbanken-und-die-wahl-einer-datenbank.md))

## Laufzeit, Caching und Skalierung

- [ ] Welcher Anwendungsserver ([Anwendungsserver](anwendungsserver.md)), und ist unser Code in persistenten Workern
      sicher?
- [ ] Welche Caches sind an, wonach sie geschlüsselt sind, wie sie invalidiert werden
      ([Caching-Ebenen für Ingenieure](caching-ebenen-fuer-ingenieure.md)).
- [ ] Ein Rechner oder mehrere Knoten; was geteilt wird ([Skalierung und Clustering](skalierung-und-clustering.md)).
- [ ] Erwarteter Verkehr, gemessene Seitenkosten und die Reserve
      ([Leistungsmessung und Benchmarks](leistungsmessung-und-benchmarks.md)).
- [ ] Eine Installation oder mehrere Websites ([Installationen mit mehreren Websites](installationen-mit-mehreren-websites.md)).

## Sicherheit

- [ ] Rollen und Richtlinien für jede Art von Benutzer, schriftlich festgehalten.
- [ ] Zwei-Faktor-Anmeldung für das Personal; Sperre; Session-Handler
      ([Authentifizierung, Sessions und Zwei-Faktor-Anmeldung](authentifizierung-sessions-und-2fa.md)).
- [ ] Vertrauenswürdige Proxys, Sicherheits-Header, ein Plan für CSP ([HTTP-Sicherheit](http-sicherheit-csp-und-proxys.md)).
- [ ] `ManagedByDeployment` in der Produktion.
- [ ] Wo Geheimnisse liegen und wer sie wechselt.

## Integration

- [ ] Welche APIs: REST, ezjscore-Endpunkte, entfernte Dienste; Authentifizierung für jede
      ([API-Design: REST, ezjscore und Headless](api-design-rest-ezjscore-headless.md)).
- [ ] Hintergrundarbeit: Cronjob-Teile, Inhaltsjobs, expqueue
      ([Konsole, Cronjobs und Hintergrundarbeit](konsole-cron-und-hintergrundarbeit.md)).
- [ ] E-Mail: Transport, Voreinstellungen und Einwilligung.

## Betrieb

- [ ] Bereitstellungsverfahren und seine Reihenfolge ([Eine Änderung bereitstellen](eine-aenderung-bereitstellen.md)).
- [ ] Überwachung, Logs, Audit-Trail, Empfänger von Warnungen ([Beobachtbarkeit](beobachtbarkeit-logs-debug-und-audit.md)).
- [ ] Sicherungen und eine getestete Wiederherstellung.
- [ ] Störungsverfahren und Runbooks ([Umgang mit Störungen](umgang-mit-stoerungen.md)).

## Qualität, Aktualisierungen und Verantwortung

- [ ] Tests und CI für unseren eigenen Code ([Teststrategie](teststrategie.md)).
- [ ] Wie wir Releases von Kernel und Erweiterungen verfolgen und wer die Upgrade-Hinweise liest
      ([Richtlinie zur Rückwärtskompatibilität](richtlinie-zur-rueckwaertskompatibilitaet.md)).
- [ ] Composer-Bedingungen und wie wir neu festlegen ([Abhängigkeitsverwaltung mit Composer](abhaengigkeitsverwaltung-mit-composer.md)).
- [ ] Was wir zurückgeben und ob Mitwirkende die CLA unterzeichnet haben ([Upstream beitragen](upstream-beitragen.md)).
- [ ] Bekannte Risiken und Schulden, die wir akzeptieren ([Mit technischen Schulden umgehen](technische-schulden.md)).

## Eine Vorlage für ein Entscheidungsprotokoll

| Feld | Inhalt |
|---|---|
| Titel | die Entscheidung in einer Zeile |
| Datum und Status | das Datum; vorgeschlagen, angenommen oder durch ein anderes Protokoll ersetzt |
| Kontext | die Anforderung und die Einschränkung |
| Optionen | zwei oder drei, jede mit ihrer Quellseite |
| Entscheidung | die gewählte Option |
| Folgen | was leichter wird, was schwerer wird, worauf zu achten ist |
| Prüfdatum | wann sie erneut angesehen wird, zum Beispiel nach dem nächsten Release 6.0.x |

## Verwandte Seiten

- [Checkliste für Code-Reviews](checkliste-fuer-code-reviews.md)
- [Ein Exponential-Projekt planen](../fuehrung/ein-exponential-projekt-planen.md)
- [Risikomanagement](../fuehrung/risikomanagement.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
