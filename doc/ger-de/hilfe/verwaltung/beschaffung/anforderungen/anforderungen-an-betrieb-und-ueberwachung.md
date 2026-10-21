---
title: Anforderungen an Betrieb und Überwachung
audience: government, project-manager, hosting, admin
category: public-sector
order: 100
summary: Formulierungen für den täglichen Betrieb eines öffentlichen CMS - Überwachung, Protokolle, geplante Aufgaben, Caches, Wartungsfenster, Upgrades und Zustandsprüfungen - mit den Werkzeugen und Seiten von Exponential, die die Antwort eines Lieferanten nennen sollte.
keywords: Betrieb, Überwachung, Verfügbarkeit, Zustandsprüfungen, Protokolle, Log-Betrachter, Cronjobs, Caches, Wartungsmodus, Upgrades, Upgrade-Prüfung, Systeminformationen, Runbooks, Ausschreibung, Anforderungen, operations, monitoring, uptime, health checks, logs, log viewer, maintenance mode, upgrade check, system information, runbooks, tender, requirements
type: help
---
# Anforderungen an Betrieb und Überwachung

Diese Seite richtet sich an IT-Leitungen und Beschaffungsteams, die festlegen, wie das CMS nach dem Livegang betrieben
wird. Der Betrieb ist meist eine Leistung eines Lieferanten oder Ihres eigenen IT-Teams; die Software stellt die
Werkzeuge bereit. Die Seite gibt Formulierungen für Anforderungen und nennt die Werkzeuge von Exponential, die eine
Antwort verwenden sollte, aus [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md) und der
[Hilfe zum Betrieb beim Hosting](../../../hosting/betrieb/README.md).

## Inhalt
1. [Was der Betrieb umfasst](#was-der-betrieb-umfasst)
2. [Formulierung der Anforderungen](#formulierung-der-anforderungen)
3. [Werkzeuge, die Exponential bereitstellt](#werkzeuge-die-exponential-bereitstellt)
4. [Berichte, nach denen Sie fragen sollten](#berichte-nach-denen-sie-fragen-sollten)
5. [Fallstricke](#fallstricke)

## Was der Betrieb umfasst

- Überwachen, dass die Website erreichbar und schnell ist.
- Protokolle lesen und auf Fehler reagieren.
- Geplante Aufgaben (Cronjobs) ausführen: Benachrichtigungen, Suchindizierung, Pflege des Audits.
- Caches nach Änderungen leeren und vorwärmen.
- Geplante Wartung und Upgrades, der Redaktion und den Besuchern angekündigt.
- Sicherungen und Wiederherstellungstests (siehe [Anforderungen an Hosting und Datenstandort](anforderungen-an-hosting-und-datenstandort.md)).

## Formulierung der Anforderungen

| Id | Anforderung |
|---|---|
| OPS-01 | Die Website wird alle <Intervall> von außen mit einer Prüfung überwacht, die die Datenbank berührt; Alarme erreichen <Rolle> zu jeder Stunde / in <Zeiten>. |
| OPS-02 | Anwendungsfehler werden mit einer Referenz protokolliert, die ein Besucher nennen kann; der Lieferant kann den Protokolleintrag anhand dieser Referenz finden. |
| OPS-03 | Geplante Aufgaben laufen wie dokumentiert, und ihr Scheitern wird gemeldet. |
| OPS-04 | Geplante Wartung findet in angekündigten Zeitfenstern statt; Besucher sehen eine Wartungsnachricht, Administratoren können sich weiterhin anmelden. |
| OPS-05 | Vor jedem Upgrade prüft der Lieferant die Konsistenz von Dateien und Datenbank und berichtet Abweichungen vom Release. |
| OPS-06 | Der Lieferant liefert Runbooks für Website nicht erreichbar, langsame Website, volle Festplatte, Ablauf eines Zertifikats, Wiederherstellung aus einer Sicherung und kompromittierte Website. |
| OPS-07 | Protokolle werden rotiert und <Zeitraum> aufbewahrt, mit maskierten personenbezogenen Daten. |
| OPS-08 | Der Lieferant liefert einen monatlichen Betriebsbericht (Verfügbarkeit, Störungen, Änderungen, eingespielte Updates). |

## Werkzeuge, die Exponential bereitstellt

| Id | Werkzeug | Seite |
|---|---|---|
| OPS-01 | Zustandsprüfungen unter **Setup > Systeminformationen** (`/setup/info`) | [Systeminformationen](../../../../anleitungen/systeminformationen.md), [Zustandsprüfungen und Verfügbarkeit](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md) |
| OPS-02 | Fehlerreferenzen | [Einen Fehler anhand seiner Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md) |
| OPS-03 | Cronjob-Konsole und Pause | [Cronjobs](../../../../anleitungen/cronjobs.md), [Cronjob-Konsole](../../../../../eng-us/features/6.0/cronjobs-console.md) |
| OPS-04 | **Setup > Wartung**, `php bin/php/maintenance.php on --message=... --until=...` | [Wartungsmodus](../../../../anleitungen/wartungsmodus.md) |
| OPS-05 | **Setup > Upgrade-Prüfung** (`/setup/systemupgrade`) | [Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md), [Prüfung der Dateikonsistenz](../../../../../eng-us/features/6.0/file-consistency-check.md) |
| OPS-06 | Für Hoster geschriebene Runbooks | [Hosting-Runbooks](../../../hosting/runbooks/README.md) |
| OPS-07 | **Setup > Logdateien**, Rotation der Logdateien, Anfrageprotokolle | [Log-Betrachter](../../../../../eng-us/features/6.0/log-viewer.md), [Rotation der Logdateien](../../../../../eng-us/features/6.0/log-rotation.md), [Anfrageprotokolle](../../../../../eng-us/features/6.0/request-logs.md) |
| OPS-08 | Checkliste für den monatlichen Betrieb für Hoster | [Monatlicher Betrieb](../../../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md) |

Caches: **Setup > Cache-Verwaltung** (`/setup/cache`) und `php bin/php/ezcache.php --clear-all`
([Caches](../../../../anleitungen/caches.md)). Upgrades: [Aktualisieren](../../../../anleitungen/aktualisieren.md).

## Berichte, nach denen Sie fragen sollten

- Verfügbarkeit je Monat, gemessen wie im Vertrag festgelegt.
- Störungen mit Ursache, Dauer und Behebung.
- Eingespielte Updates von Exponential, PHP und Betriebssystem, mit Datum.
- Wiederherstellungstests mit Datum und Dauer.
- Ausgelöste Audit-Alarme und wie sie behandelt wurden ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)).

## Fallstricke

- Überwachung festlegen, ohne zu sagen, wer auf den Alarm reagiert.
- „Die Software hat einen Wartungsmodus“ als Betriebskonzept behandeln.
- Vergessen, dass die Pflege des Audits davon abhängt, dass die Cronjob-Gruppe `frequent` läuft.

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Anforderungen an Support und Wartung](anforderungen-an-support-und-wartung.md)
- [Ausschreibungsvorlage: Klausel zu Service-Levels](../ausschreibungsvorlagen/klausel-service-level.md)
- [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
