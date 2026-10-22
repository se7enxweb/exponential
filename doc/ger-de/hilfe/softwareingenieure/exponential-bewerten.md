---
title: Exponential bewerten
audience: software-engineer
category: software-engineering
order: 30
summary: Eine strukturierte technische Bewertung von Exponential für ein Projekt - die zu beantwortenden Fragen zu Eignung, Laufzeit, Daten, Leistung, Sicherheit, Qualität, Wartung und Lizenz, wie Sie jede davon aus dem Code und der Dokumentation beantworten und wie Sie dafür eine Testinstallation aufsetzen.
keywords: Bewertung, Due Diligence, technische Bewertung, Eignung, Machbarkeitsnachweis, Auswahl, Kriterien, Risiko, Lizenz, GPL, Wartung, Softwareingenieur, evaluation, technical assessment, fit, proof of concept, selection, criteria, risk, license, maintenance
type: help
---
# Exponential bewerten

Diese Seite richtet sich an Ingenieure, denen die Frage "Sollen wir auf Exponential aufbauen?" gestellt wird. Sie zerlegt
diese Frage in kleinere, die das Repository und die Dokumentation beantworten können, sodass das Ergebnis auf Belegen statt
auf Eindrücken beruht. Schreiben Sie die Antworten mit ihren Quellen auf; die
[Checkliste für Design-Reviews](checkliste-fuer-design-reviews.md) ist eine Vorlage für das Ergebnis.

## Inhalt

1. [Eine Testinstallation aufsetzen](#eine-testinstallation-aufsetzen)
2. [Die Fragen, nach Bereich](#die-fragen-nach-bereich)
3. [Die eigenen Standangaben des Projekts lesen](#die-eigenen-standangaben-des-projekts-lesen)
4. [Abzuwägende Risiken](#abzuwägende-risiken)

## Eine Testinstallation aufsetzen

Bewerten Sie an einer echten Installation, nicht an Screenshots. Am schnellsten geht es mit SQLite, das keinen
Datenbankserver braucht:

```bash
composer install
./console exp:install --help
```

`exp:install` verwendet SQLite, wenn kein `--db` angegeben ist ([Datenbankunterstützung](../datenbankunterstuetzung.md)). Das
Installationsbuch beschreibt jede Installationsmethode ([Eine Installationsmethode wählen](../../installation/04-installationsweg-waehlen.md)).
Liefern Sie sie mit Velocity aus, um die empfohlene Laufzeitumgebung zu sehen ([Anwendungsserver](anwendungsserver.md)).

## Die Fragen, nach Bereich

| Bereich | Frage | Wo Sie die Antwort finden |
|---|---|---|
| Eignung | Bildet das Inhaltsmodell unsere Daten ab (typisierte Attribute, Versionen, Übersetzungen, Beziehungen, Platzierungen im Baum)? | [Das Inhaltsmodell für Ingenieure](inhaltsmodell-fuer-ingenieure.md) |
| Eignung | Welches Produkt der Familie brauchen wir, und enthält es die Teile, die wir brauchen? | [Was Exponential ist](../fuehrung/was-exponential-ist.md) |
| Laufzeit | Welche PHP-Versionen und -Erweiterungen haben unsere Server? | `composer.json`, [Anforderungen](../../installation/02-voraussetzungen.md) |
| Laufzeit | Welcher Server wird es ausliefern? | [Anwendungsserver](anwendungsserver.md) |
| Daten | Welche Datenbank, und wird sie in der CI getestet? | [Datenbanken: eine wählen und betreiben](datenbanken-und-die-wahl-einer-datenbank.md) |
| Leistung | Was kostet eine gecachte und eine ungecachte Seite auf unserer Hardware? | [Leistungsmessung und Benchmarks](leistungsmessung-und-benchmarks.md) |
| Skalierung | Brauchen wir mehrere Webknoten, und was muss geteilt werden? | [Skalierung und Clustering](skalierung-und-clustering.md) |
| Sicherheit | Was sind die Standards, und wie werden Schwachstellen gemeldet und behoben? | [Sicherheitstechnik im Überblick](sicherheitstechnik-im-ueberblick.md) |
| Integration | Welche APIs brauchen unsere anderen Systeme? | [API-Design: REST, ezjscore und Headless](api-design-rest-ezjscore-headless.md) |
| Qualität | Was prüfen die CI und die lokalen Gates? | [CI-Gates](ci-gates.md) |
| Wartung | Was zählt als brechende Änderung, und wie wird sie angekündigt? | [Richtlinie zur Rückwärtskompatibilität](richtlinie-zur-rueckwaertskompatibilitaet.md) |
| Releases | Wie oft erscheinen Releases, und wie werden sie nummeriert? | [Release Engineering](release-engineering.md), `doc/changelogs/` |
| Lizenz | Ist die GNU GPL, Version 2 oder später, damit vereinbar, wie wir unseren Code verbreiten? | `LICENSE`, [Beiträge an das Projekt](upstream-beitragen.md) |

## Die eigenen Standangaben des Projekts lesen

Die Standardseiten in `doc/eng-us/standards/` stufen das Projekt gegenüber öffentlichen Standards als **Complies**,
**Partly complies**, **Does not comply** oder **Unverified** ein und listen bekannte Lücken. Sie sind die ehrlichste
einzelne Quelle für eine Bewertung, weil sie benennen, was fehlt:

- [Sicherheitsstandards](../../standards/sicherheit.md): OWASP Top 10, ASVS, CSP.
- [Quellcodeverwaltung und Releases](../../standards/versionsverwaltung-und-releases.md): Branch-Schutz und signierte Commits
  sind als Lücken aufgeführt.
- [Standards für Datenbanken und SQL](../../standards/datenbanken-und-sql.md) und [Tests und CI](../../standards/tests-und-ci.md).
- [Standards zur Internationalisierung](../../standards/internationalisierung.md) und [Barrierefreiheit](../../standards/barrierefreiheit.md).

## Abzuwägende Risiken

- **Geerbter Code.** Der Kernel ist alt und groß; PHPStan läuft auf Stufe 1 mit einer Baseline der Befunde, die bei der
  Einführung der Prüfung bestanden ([Mit technischen Schulden umgehen](technische-schulden.md)).
- **Manuelle Datenbank-Updates.** Es gibt keinen Migrationsläufer, der festhält, welche Anweisungen ausgeführt wurden
  ([Schemaänderungen und Migrationen](schemaaenderungen-und-migrationen.md)).
- **Richtlinie, nicht Durchsetzung.** Einige Repository-Regeln (kein Force-Push, Merge-Stil) sind Richtlinien ohne
  Branch-Schutz ([Quellcodeverwaltung und Releases](../../standards/versionsverwaltung-und-releases.md)).
- **Ein kleineres Ökosystem** als die größten PHP-Frameworks. Prüfen Sie den Erweiterungskatalog auf das, was Sie
  brauchen, bevor Sie planen, es zu schreiben
  ([Der se7enxweb-Erweiterungskatalog](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/der-se7enxweb-erweiterungskatalog.md)).

## Verwandte Seiten

- [Exponential für Softwareingenieure](exponential-fuer-softwareingenieure.md)
- [Checkliste für Design-Reviews](checkliste-fuer-design-reviews.md)
- [Häufige Fragen von Softwareingenieuren](faq-fuer-softwareingenieure.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
