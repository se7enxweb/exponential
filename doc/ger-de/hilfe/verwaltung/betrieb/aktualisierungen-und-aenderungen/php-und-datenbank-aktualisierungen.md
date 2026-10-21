---
title: Aktualisierungen von PHP und Datenbank
audience: government, admin, hosting
category: public-sector
order: 90
summary: Die Plattform unter Exponential aktualisieren - unterstützte PHP-Versionen, Versionen des Datenbankservers, die Reihenfolge der Schritte, Staging und was ein internes IT-Team oder ein Anbieter einer Behörden-Cloud abstimmen muss.
keywords: PHP-Aktualisierung, PHP 8.5, Datenbankaktualisierung, MySQL, MariaDB, PostgreSQL, SQLite, Plattformaktualisierung, Ende der Unterstützung, Aktualisierung des Betriebssystems, veraltete Funktionen, deprecated.log, Abstimmung, Cloud-Anbieter, PHP upgrade, database upgrade, platform upgrade, end of life, OS upgrade, deprecations, coordination, cloud provider
type: help
---
# Aktualisierungen von PHP und Datenbank

Diese Seite richtet sich an interne IT-Teams und Verantwortliche für Dienste, die PHP und den Datenbankserver in einem
unterstützten Zustand halten müssen, oft nach einem Zeitplan, den das Betriebssystem oder der Cloud-Anbieter vorgibt.
Das Hosting-Runbook [PHP-Aktualisierung](../../../hosting/runbooks/php-upgrade.md) enthält die technischen Schritte;
diese Seite ergänzt Reihenfolge und Abstimmung.

## Inhalt
1. [Was unterstützt wird](#was-unterstützt-wird)
2. [Die Reihenfolge der Aktualisierungen](#die-reihenfolge-der-aktualisierungen)
3. [Vorbereitung mit dem Log der veralteten Funktionen](#vorbereitung-mit-dem-log-der-veralteten-funktionen)
4. [Aktualisierungen des Datenbankservers](#aktualisierungen-des-datenbankservers)
5. [Abstimmung](#abstimmung)
6. [Verwandte Seiten](#verwandte-seiten)

## Was unterstützt wird

| Komponente | Unterstützung | Quelle |
|---|---|---|
| PHP 8.0 | die älteste unterstützte; Velocity läuft darauf ab seinem Release 0.0.4.54 | [Anforderungen](../../../../installation/02-voraussetzungen.md) |
| PHP 8.1 bis 8.5 | unterstützt, Velocity eingeschlossen | dieselbe |
| PHP 7.4 | eine Website, die darauf bleiben muss, bleibt auf 6.0.7 | [Aktualisieren, Teil B](../../../../anleitungen/aktualisieren.md#teil-b-von-54-oder-590-auf-exponential-600) |
| Datenbanken | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, mit praktischen Mindestversionen | [Datenbanken in der offiziellen Reihenfolge](../../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md) |

## Die Reihenfolge der Aktualisierungen

1. Aktualisieren Sie zuerst Exponential auf das neueste 6.0.x, damit Korrekturen für neuere PHP-Versionen vorhanden sind.
2. Aktualisieren Sie PHP auf dem Staging; installieren Sie die PHP-Erweiterungen, die die Website braucht
   (`./console exp:velocity ext check --allow-root-user` listet auf, was fehlt, und gibt den Installationsbefehl aus).
3. Testen Sie auf dem Staging ([Staging und Freigabe](staging-und-freigabe.md)).
4. Aktualisieren Sie die Produktion in einem Wartungsfenster; laden Sie das PHP-FPM neu, das die Website ausliefert
   (auf Servern mit mehreren PHP-Versionen heißt der Dienst womöglich nicht `php-fpm`; `exp:velocity deploy` findet
   ihn mit `[DeploySettings] PhpFpmService=auto`), oder starten Sie Velocity neu.
5. Aktualisieren Sie niemals PHP, den Datenbankserver und Exponential im selben Wartungsfenster: Eine Änderung nach
   der anderen hält die Ursache eines Problems offensichtlich.

## Vorbereitung mit dem Log der veralteten Funktionen

PHP 8 meldet Hinweise auf veraltete Funktionen für Code, den eine spätere PHP-Version ablehnen wird. Exponential
schreibt sie nach `var/log/deprecated.log`, jede unterschiedliche Meldung einmal pro Anfrage
([Eine Website betreiben, Abschnitt 8](../../../../anleitungen/betrieb-einer-website.md#php-deprecations-und-größe-der-logdateien)):

```bash
sort var/log/deprecated.log | uniq -c | sort -rn | head
```

Um sie auch in der Produktion zu protokollieren, fügen Sie `AlwaysLog[]=deprecated` unter `[DebugSettings]` hinzu.
Die Liste ist die Arbeitsliste Ihrer Entwicklerinnen und Entwickler vor der nächsten PHP-Aktualisierung; Ihre eigenen
Erweiterungen sind die übliche Quelle.

## Aktualisierungen des Datenbankservers

- Folgen Sie dem Ablauf des Herstellers; Exponential aktualisiert keine Datenbankserver.
- Sichern Sie zuerst mit dem Werkzeug der Engine ([Datensicherungen je Datenbank-Engine](../datensicherung-und-wiederherstellung/datensicherungen-je-datenbank-engine.md)).
- Führen Sie danach **Setup > Upgrade-Prüfung**, **Überprüfe Datenbankkonsistenz** aus: Sie vergleicht das Schema mit
  dem, was Exponential erwartet.
- Zeichensätze sind wichtig: `utf8mb4` für MySQL und MariaDB, `UTF8` für PostgreSQL
  ([Datenbanken in der offiziellen Reihenfolge](../../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md#nebeneinander)).

## Abstimmung

| Beteiligte | Vereinbaren |
|---|---|
| Betriebssystem- oder Cloud-Anbieter | die Daten zum Ende der Unterstützung von PHP und der Datenbank sowie das Wartungsfenster |
| Entwicklerinnen und Entwickler Ihrer eigenen Erweiterungen | Korrekturen für die gefundenen veralteten Funktionen |
| Änderungsbeirat | eine Änderung pro Komponente |
| Verantwortliche für den Dienst | das Wartungsfenster und den öffentlichen Hinweis |

## Verwandte Seiten
- [Überblick über Aktualisierungen für den öffentlichen Sektor](aktualisierungen-im-oeffentlichen-sektor-im-ueberblick.md)
- [PHP-Erweiterungen vor einer Aktualisierung prüfen](../../../../../eng-us/help/government/tasks/operations/check-php-extensions-before-an-upgrade.md)
- [PHP-Versionen und -Erweiterungen (Hosting)](../../../hosting/grundlagen/php-versionen-und-erweiterungen.md)
- [Datenbankwahl in der offiziellen Reihenfolge](../hosting/datenbankwahl-in-der-offiziellen-reihenfolge.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
