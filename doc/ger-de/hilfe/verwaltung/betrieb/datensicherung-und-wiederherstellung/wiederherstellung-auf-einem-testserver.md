---
title: Wiederherstellung auf einem Testserver
audience: government, hosting, admin
category: public-sector
order: 90
summary: Für Betriebsteams in öffentlichen Stellen, die einen Wiederherstellungstest durchführen oder eine Kopie für Abnahmetests vorbereiten. Die schrittweise Wiederherstellung einer Exponential-Website auf einer separaten Maschine, mit angehaltenem Mailversand, ohne Cronjobs, mit Netzwerkbeschränkung und mit den Prüfungen am Ende.
keywords: Wiederherstellung auf einem Testserver, Testwiederherstellung, Staging-Kopie, Abnahmeumgebung, Datei-Transport, DebugSending, keine Crontab, Wartungsmodus, Velocity anhalten, ezcache, Autoloads, Prüfungen, restore to test server, test restore, staging copy, acceptance environment, file transport, no crontab, maintenance mode, Velocity stop, autoloads, checks
type: help
---
# Wiederherstellung auf einem Testserver

Diese Seite richtet sich an Betriebsteams in öffentlichen Stellen, die eine Datensicherung auf einer separaten
Maschine wiederherstellen: als Wiederherstellungstest oder um Testerinnen und Testern eine realistische Kopie zu
geben. Sie nennt das Verfahren und die Schritte, die verhindern, dass sich die Kopie wie die Live-Website verhält. Sie
folgt [Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md) und
[10.9 Datensicherung und Wiederherstellung](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Kopie stilllegen](#die-kopie-stilllegen)
3. [Die Wiederherstellung Schritt für Schritt](#die-wiederherstellung-schritt-für-schritt)
4. [Prüfungen](#prüfungen)
5. [Aufräumen](#aufräumen)
6. [Checkliste](#checkliste)

## Bevor Sie beginnen

- **Dasselbe Release.** Checken Sie dasselbe Exponential-Release und dieselbe `composer.lock` aus, mit denen die
  Datensicherung erstellt wurde. Eine Datenbank aus einem älteren Release braucht danach die
  Aktualisierungsschritte.
- **Ein eigener Hostname**, nur aus Ihrem Netzwerk erreichbar.
- **Freigabe** für das Kopieren von Produktionsdaten, einschließlich personenbezogener Daten, auf diese Maschine, wie
  es Ihre Datenschutzregeln verlangen
  ([Umgebungen für Entwicklung, Test und Produktion](../hosting/umgebungen-entwicklung-test-produktion.md)).
- **Die Datensicherung**: Datenbank und Dateien vom selben Zeitpunkt sowie die Schlüsseldateien.

## Die Kopie stilllegen

Erledigen Sie dies, bevor die Website ausliefert:

1. **E-Mail.** Schreiben Sie jede E-Mail in Dateien, statt sie zu versenden:

   ```ini
   # settings/override/site.ini.test.append.php, with EXP_ENV=test on this machine
   [MailSettings]
   Transport=file
   ```

   E-Mails gehen dann nach `var/log/mail/`. Oder behalten Sie den Transport und setzen `DebugSending=enabled` mit
   `DebugReceiverEmail=<test address>`.
2. **Cronjobs.** Installieren Sie die Crontab nicht, sonst laufen Benachrichtigungen, Newsletter und Inhaltsjobs gegen
   echte Adressen.
3. **Netzwerk.** Beschränken Sie den Host auf Ihr Netzwerk, wie Sie es bei der Administration tun würden
   ([Die Administration von der öffentlichen Website trennen](../hosting/administrations-und-oeffentliche-siteaccesses-trennen.md)).
4. **Umgebungskennzeichen.** Mit `EXP_ENV=test` kann admin4 die Umgebung anzeigen, damit niemand die Kopie mit der
   Produktion verwechselt.

## Die Wiederherstellung Schritt für Schritt

1. Wartungsmodus ein, und unter Velocity den Server anhalten, damit kein Worker die Datenbank festhält:

   ```bash
   php bin/php/maintenance.php on --message="Restoring" --allow-root-user
   php bin/php/console exp:velocity stop --allow-root-user
   ```

2. Stellen Sie die Datenbank mit dem Werkzeug ihrer Engine wieder her
   ([Datensicherung je Datenbank-Engine](datensicherungen-je-datenbank-engine.md)) und entpacken Sie die Dateien
   (`var/storage`, Speicher je Website, das Audit-Verzeichnis, `settings/override`, `settings/siteaccess`, Ihre
   Extensions).
3. Passen Sie Einstellungen an, die die Live-Maschine benennen: Datenbankhost, `SiteURL` jedes Siteaccess,
   Host-Zuordnung, Hostnamen von Velocity
   ([Eine Website zwischen Hosts umziehen](../../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)).
4. Übergeben Sie die Dateien dem Benutzer der Website und bauen Sie dann Caches und Autoloads neu auf:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

5. Starten Sie Velocity, leeren Sie seinen Antwort-Cache und beenden Sie den Wartungsmodus:

   ```bash
   php bin/php/console exp:velocity start --allow-root-user
   php bin/php/console exp:velocity cache clear --allow-root-user
   php bin/php/maintenance.php off --allow-root-user
   ```

   Unter PHP-FPM laden Sie stattdessen das PHP-FPM neu, das die Testwebsite ausliefert.

Wenn die Datensicherung bei eingeschaltetem Wartungsmodus erstellt wurde, hält die wiederhergestellte
`var/maintenance.json` die Kopie im Wartungsmodus; `php bin/php/maintenance.php off` beendet ihn.

## Prüfungen

Führen Sie die Prüfungen aus [Wiederherstellung testen](wiederherstellungstests.md) aus: Die Website und die Administration
antworten, die Version stimmt, `cache.php status` endet mit `PASS`, `exp:audit verify` endet mit 0, aktuelle Inhalte
und Bilder lassen sich öffnen, und `error.log` zeigt nichts Unerklärtes. Halten Sie die Dauer fest.

## Aufräumen

Eine Kopie für einen Wiederherstellungstest enthält personenbezogene Daten. Wenn der Test abgeschlossen und berichtet
ist, entfernen Sie die Kopie oder bewahren Sie sie nur so lange auf, wie Ihre Regeln es zulassen. Vermerken Sie im
Bericht, wann sie entfernt wurde.

## Checkliste

- [ ] Dasselbe Release und dieselbe `composer.lock` wie bei der Datensicherung.
- [ ] E-Mails gehen in Dateien; keine Crontab; der Host ist auf Ihr Netzwerk beschränkt.
- [ ] Einstellungen, die die Live-Maschine benennen, wurden angepasst.
- [ ] Eigentümer korrigiert, Caches und Autoloads neu aufgebaut.
- [ ] Die Prüfungen sind bestanden, die Dauer ist festgehalten.
- [ ] Die Kopie ist entfernt oder wird nach Ihren Datenregeln aufbewahrt.

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- Runbook [Aus einer Datensicherung wiederherstellen](../../../hosting/runbooks/aus-einem-backup-wiederherstellen.md) für einen echten Vorfall
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
