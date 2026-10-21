---
title: Selbst betreiben im eigenen Haus
audience: government, admin, project-manager
category: public-sector
order: 30
summary: Was eine öffentliche Stelle braucht, um Exponential auf eigenen Servern zu betreiben - die Anforderungen, die Installationswege, die Auslieferung mit Velocity oder einem klassischen Webserver, die Datenbank, der laufende Betrieb und die Kompetenzen, die Sie einplanen sollten.
keywords: Selbstbetrieb, eigenes Haus, eigenes Rechenzentrum, Installation, Anforderungen, PHP, Velocity, Apache, Datenbank, Betrieb, Datensicherungen, Souveränität, self-hosting, on premise, own data center, install, requirements, operations, backups, sovereignty
type: help
---
# Selbst betreiben im eigenen Haus

Diese Seite richtet sich an Administratoren und IT-Verantwortliche, die Exponential im eigenen Rechenzentrum betreiben
wollen. Exponential ist eine PHP-Anwendung mit einer Datenbank und einem beschreibbaren Dateibereich; es braucht für
den Betrieb weder einen Lizenzserver noch eine Verbindung zu einem Hersteller. Die Einzelheiten stehen im
Installationshandbuch und in der Hilfe zum Hosting, die unten verlinkt sind.

## Inhalt

1. [Was Sie brauchen](#was-sie-brauchen)
2. [Den Code beziehen](#den-code-beziehen)
3. [Die Website ausliefern](#die-website-ausliefern)
4. [Die Datenbank](#die-datenbank)
5. [Betriebsaufgaben, die Sie übernehmen](#betriebsaufgaben-die-sie-übernehmen)
6. [Kompetenzen, die Sie einplanen sollten](#kompetenzen-die-sie-einplanen-sollten)

## Was Sie brauchen

| Teil | Wo die Anforderungen stehen |
|---|---|
| Betriebssystem | [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md) |
| PHP-Version und Erweiterungen | [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md), [2. Anforderungen](../../../installation/02-voraussetzungen.md) |
| Datenbank | [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Speicherplatz für Inhaltsdateien und Caches | [Arbeitsspeicher, CPU und Festplatte bemessen](../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md) |
| Beschreibbare Verzeichnisse | [Beschreibbare Verzeichnisse und Rechte](../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md) |

## Den Code beziehen

Der Code ist öffentlich; die Installationswege sind in [Eine Installationsmethode wählen](../../../installation/04-installationsweg-waehlen.md)
und [Den Code beziehen](../../../installation/03-den-code-beschaffen.md) beschrieben. Für einen Server ohne Internetzugang
bereiten Sie die Installation mit Composer auf einem Rechner vor, der Zugang hat, und kopieren das Ergebnis; prüfen
Sie im Kapitel der gewählten Methode, was sie herunterlädt.

## Die Website ausliefern

Zwei Wege, beide dokumentiert:

- **Velocity**, der PHP-Anwendungsserver des Projekts (MIT-lizenziert), der Exponential selbst ausliefert
  ([Velocity-Funktionen](../../../funktionen/6.0/velocity-pakete-und-binaerdateien.md)).
- **Ein klassischer Webserver** mit PHP-FPM, zum Beispiel Apache mit den mitgelieferten Rewrite-Regeln
  ([8. Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)).

## Die Datenbank

SQLite 3 ist die Voreinstellung und braucht keinen Datenbankserver; PostgreSQL ist der empfohlene Datenbankserver.
Alle sechs unterstützten Datenbanken können auf Ihren eigenen Rechnern laufen
([Datenbankunterstützung](../../datenbankunterstuetzung.md)).

## Betriebsaufgaben, die Sie übernehmen

| Aufgabe | Dokumentation |
|---|---|
| Cronjobs (Workflows, Benachrichtigungen, Audit, Suchindex) | [Cronjobs](../../../anleitungen/cronjobs.md) |
| Datensicherungen und Wiederherstellungstests | [Datensicherungen: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md), [Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md) |
| Updates und Aktualisierungen | [Aktualisieren](../../../anleitungen/aktualisieren.md), [Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md) |
| Sicherheitshärtung | [13. Sicherheitshärtung](../../../installation/13-sicherheitshaertung.md), [Sicherheitshärtung](../../sicherheitshaertung.md) |
| Protokolle und Überwachung | [Überwachung im Überblick](../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md) |
| E-Mail-Zustellung | [E-Mail-Zustellung](../../hosting/betrieb/mailzustellung.md) |

## Kompetenzen, die Sie einplanen sollten

Mindestens zwei Personen, die Folgendes können: Linux-Server und PHP betreiben, die Datenbank betreiben und
wiederherstellen, Updates mit Composer oder über die Seite **Updates and packages** einspielen und die Protokolle
lesen. Schulungen sind im [Kurskatalog](../../trainer/kurskatalog.md) aufgeführt. Wenn Sie den Betrieb
vergeben, behalten Sie eine Person im Haus, die den Dienstleister steuern kann (siehe „Das Wissen im Haus halten“).

## Verwandte Seiten

- [Eine Website betreiben](../../../anleitungen/betrieb-einer-website.md)
- [Hosting-Unternehmen: hier beginnen](../../hosting/hosting-anbieter-hier-beginnen.md)
- [Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
