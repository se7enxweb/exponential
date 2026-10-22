---
title: Lesepfad für Softwareingenieure
audience: software-engineer
category: software-engineering
order: 20
summary: Die Seiten für Softwareingenieure in einer sinnvollen Reihenfolge, mit vier kürzeren Pfaden für die Bewertung, die Architektur, den Betrieb und für Prüfer und Beitragende, und den vertiefenden Dokumenten, die Sie nach jedem Schritt lesen.
keywords: Lesepfad, Lernpfad, Einarbeitung, Reihenfolge, Bewerter, Architekt, Betreiber, Prüfer, Beitragender, Softwareingenieur, reading path, learning path, onboarding, evaluator, architect, operator, reviewer, contributor
type: help
---
# Lesepfad für Softwareingenieure

Diese Seite bringt die Seiten für Softwareingenieure in eine Reihenfolge. Lesen Sie den Kernpfad einmal; folgen Sie dann
dem Pfad für die Rolle, die Sie in Ihrem Projekt haben. Jeder Schritt nennt das vertiefende Dokument, das Sie lesen, wenn
die Hilfeseite nicht reicht.

## Inhalt

1. [Der Kernpfad](#der-kernpfad)
2. [Der Pfad für die Bewertung](#der-pfad-für-die-bewertung)
3. [Der Pfad für die Architektur](#der-pfad-für-die-architektur)
4. [Der Pfad für den Betrieb](#der-pfad-für-den-betrieb)
5. [Der Pfad für Prüfer und Beitragende](#der-pfad-für-prüfer-und-beitragende)

## Der Kernpfad

| Schritt | Seite | Vertiefendes Dokument |
|---|---|---|
| 1 | [Exponential für Softwareingenieure](exponential-fuer-softwareingenieure.md) | [Was Exponential ist](../fuehrung/was-exponential-ist.md) |
| 2 | [Architekturüberblick](architekturueberblick.md) | [Kernel-Architektur](../../standards/architektur/kernel-architektur.md) |
| 3 | [Der Lebenszyklus einer Anfrage](lebenszyklus-einer-anfrage.md) | [Kernel-Architektur](../../standards/architektur/kernel-architektur.md) |
| 4 | [Das Inhaltsmodell für Ingenieure](inhaltsmodell-fuer-ingenieure.md) | [Inhaltsmodell und Bearbeitung](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| 5 | [Siteaccesses und Konfiguration](siteaccesses-und-konfiguration.md) | [Ablageorte von INI-Overrides](../../spezifikationen/6.0/ini-override-platzierungen.md) |
| 6 | [Caching-Ebenen für Ingenieure](caching-ebenen-fuer-ingenieure.md) | [Caches](../../anleitungen/caches.md) |
| 7 | [Anwendungsserver](anwendungsserver.md) | [Velocity-Engines](../../bc/6.0/velocity-engines.md) |
| 8 | [Teststrategie](teststrategie.md) | [Qualitätsprüfungen](../../funktionen/6.0/qualitaetspruefungen.md) |
| 9 | [Release Engineering](release-engineering.md) | [Quellcodeverwaltung und Releases](../../standards/versionsverwaltung-und-releases.md) |
| 10 | [Sicherheitstechnik im Überblick](sicherheitstechnik-im-ueberblick.md) | [Sicherheitsstandards](../../standards/sicherheit.md) |

## Der Pfad für die Bewertung

Sie entscheiden, ob Exponential zu einem Projekt passt.

1. [Exponential bewerten](exponential-bewerten.md): die zu beantwortenden Fragen und wie Sie sie beantworten.
2. [Datenbanken: eine wählen und betreiben](datenbanken-und-die-wahl-einer-datenbank.md) und [Anwendungsserver](anwendungsserver.md).
3. [Skalierung und Clustering](skalierung-und-clustering.md) und [Leistungsmessung und Benchmarks](leistungsmessung-und-benchmarks.md).
4. [Sicherheitstechnik im Überblick](sicherheitstechnik-im-ueberblick.md).
5. [Richtlinie zur Rückwärtskompatibilität](richtlinie-zur-rueckwaertskompatibilitaet.md) und [Abhängigkeitsverwaltung mit Composer](abhaengigkeitsverwaltung-mit-composer.md).
6. [Checkliste für Design-Reviews](checkliste-fuer-design-reviews.md), um das Ergebnis festzuhalten.

## Der Pfad für die Architektur

Sie entwerfen ein System mit Exponential im Zentrum.

1. [Kernel, lib und Erweiterungen: die Schichtung](kernel-lib-und-erweiterungen.md).
2. [Module, Views und Zugriffskontrolle](module-views-und-zugriffskontrolle.md).
3. [Persistenz und die Datenbankschicht](persistenz-und-die-datenbankschicht.md) und [Schemaänderungen und Migrationen](schemaaenderungen-und-migrationen.md).
4. [Events, Erweiterungspunkte und Plugins](events-erweiterungspunkte-und-plugins.md).
5. [API-Design: REST, ezjscore und Headless](api-design-rest-ezjscore-headless.md).
6. [Installationen mit mehreren Websites](installationen-mit-mehreren-websites.md) und [Internationalisierung und Rechts-nach-links](i18n-und-rechts-nach-links.md).
7. [Konsole, Cronjobs und Hintergrundarbeit](konsole-cron-und-hintergrundarbeit.md).

## Der Pfad für den Betrieb

Sie betreiben Exponential in der Produktion.

1. [Eine Änderung bereitstellen](eine-aenderung-bereitstellen.md).
2. [Persistente Worker von Velocity](persistente-worker-von-velocity.md).
3. [Cache-Invalidierung und Leeren](cache-invalidierung-und-leeren.md).
4. [Beobachtbarkeit: Logs, Debug-Leiste und Audit-Trail](beobachtbarkeit-logs-debug-und-audit.md).
5. [Umgang mit Störungen](umgang-mit-stoerungen.md).
6. [HTTP-Sicherheit: Header, CSP, Anfrageregeln und Proxys](http-sicherheit-csp-und-proxys.md).
7. Die Betriebsanleitungen für Hoster: [Hosting-Unternehmen: hier beginnen](../hosting/hosting-anbieter-hier-beginnen.md).

## Der Pfad für Prüfer und Beitragende

Sie prüfen Änderungen oder schicken sie an das Projekt.

1. [Git-Arbeitsablauf](git-arbeitsablauf.md).
2. [Statische Analyse und Codestil](statische-analyse-und-codestil.md) und [Tests ohne Datenbank und Testsuiten](tests-ohne-datenbank-und-testsuiten.md).
3. [CI-Gates](ci-gates.md).
4. [Checkliste für Code-Reviews](checkliste-fuer-code-reviews.md) und [Checkliste für Sicherheitsprüfungen](checkliste-fuer-sicherheitspruefungen.md).
5. [Changelogs und Upgrade-Hinweise](changelogs-und-bc-hinweise.md).
6. [Beiträge an das Projekt](upstream-beitragen.md) und [Dokumentation als Code](dokumentation-als-code.md).
7. [Mit technischen Schulden umgehen](technische-schulden.md).

## Verwandte Seiten

- [Softwareingenieure: hier beginnen](softwareingenieure-hier-beginnen.md)
- [Glossar für Softwareingenieure](glossar-fuer-softwareingenieure.md)
- [Lesepfade des Kurses zur Erweiterungsentwicklung](../erweiterungsentwicklung/lesepfade.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
