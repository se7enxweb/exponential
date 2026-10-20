---
title: Checkliste für den monatlichen Betrieb
audience: hosting, admin
category: hosting
order: 40
summary: Was ein Hoster einmal im Monat für jede Exponential-Website prüft, die er betreibt - Updates, Backups und eine Stichprobe der Wiederherstellung, Festplatte und Logs, Sicherheitslog, Cron-Läufe, Caches, Zertifikate, Konten und ein kurzer Bericht an den Kunden.
keywords: monatlich, Betrieb, Checkliste, Wartung, Updates, Sicherheitskorrekturen, Backups, Wiederherstellungstest, Festplattennutzung, Logs, Log-Rotation, Sicherheitslog, Cron, Zertifikate, Konten, Bericht, monthly, operations, checklist, maintenance, security fixes, restore test, disk usage, log rotation, security log, certificates, accounts, report
type: help
---
# Checkliste für den monatlichen Betrieb

Diese Checkliste ist für die Systemadministratoren und Support-Mitarbeitenden, die Exponential-Websites betreiben. Gehen
Sie sie einmal im Monat pro Website durch (oder pro Server für die Punkte zum Server), halten Sie das Ergebnis fest und
eröffnen Sie für jeden Punkt, der scheitert, ein Ticket.

```text
Site/server: <name>        Month: <yyyy-mm>        Checked by: <name>
```

## Updates

- [ ] Neue Exponential-Releases seit dem letzten Monat sind bekannt; Sicherheitskorrekturen darunter sind eingespielt oder
      geplant ([Mit dem Projekt und 7x zusammenarbeiten](../geschaeft/mit-dem-projekt-und-7x-zusammenarbeiten.md#releases-verfolgen),
      [Kunden-Websites aktualisieren](../betrieb/kunden-websites-aktualisieren.md)).
- [ ] Updates von Erweiterungen geprüft, wo Sie sie verwalten ([Automatische Updates mit ezupdate](../betrieb/automatische-updates-mit-ezupdate.md)).
- [ ] Sicherheitsupdates von Betriebssystem und PHP eingespielt.
- [ ] Die PHP-Version wird noch von ihren Betreuern und von Exponential unterstützt (8.0 bis 8.5).

## Backups

- [ ] Jedes Backup des Monats ist durchgelaufen; Fehlschläge wurden verfolgt.
- [ ] Das Backup einer Website, reihum gewählt, wurde in diesem Monat auf einer anderen Maschine wiederhergestellt
      ([Wiederherstellung und Wiederherstellungstests](../betrieb/wiederherstellung-und-wiederherstellungstests.md)).
- [ ] Alte Backups gemäß der Aufbewahrungsrichtlinie entfernt ([Backup-Zeitpläne und Aufbewahrung](../betrieb/backup-zeitplaene-und-aufbewahrung.md)).

## Festplatte und Logs

- [ ] Festplattennutzung von `var/storage`, `var/cache`, `var/log` und der Datenbank innerhalb des Tarifs ([Aufbau des Speichers](../betrieb/aufbau-des-speichers.md)).
- [ ] Logs werden rotiert; Archive bereinigt (`./console exp:logs --archives`, `./console exp:logs --prune --dry-run`)
      ([Log-Viewer und Log-Rotation](../betrieb/log-viewer-und-log-rotation.md)).
- [ ] Kein wachsendes Fehlermuster in `error.log` (`./console exp:logs --search=... --file=var/log/error.log --rotations`).

## Sicherheit

- [ ] `security.log` durchgesehen: Abtastversuche sind im Internet normal; achten Sie auf Häufungen und auf Angriffe, die
      ein 200 erhalten haben ([Sicherheitslog](../betrieb/sicherheitslog.md)).
- [ ] Keine unbekannten Administratorkonten; Konten ausgeschiedener Personen entfernt.
- [ ] Das Prüfprotokoll zeigt keine unerwarteten Änderungen an Rollen und Richtlinien ([Prüfprotokoll](../sicherheit/pruefprotokoll.md)).
- [ ] Zertifikate länger gültig als der Puffer für die Erneuerung (mit Velocity: `php bin/php/console exp:velocity ssl show --allow-root-user`)
      ([TLS und HTTPS](../sicherheit/tls-und-https.md)).

## Jobs und Caches

- [ ] Cronjobs liefen nach Zeitplan; keiner scheitert wiederholt ([Cronjob-Konsole](../betrieb/die-cronjob-konsole.md)).
- [ ] Mailwarteschlange und Bounces normal ([Einwilligung und Bounces bei Mails](../betrieb/einwilligung-und-bounces-bei-mails.md)).
- [ ] Mit Velocity: `php bin/php/console exp:velocity status --allow-root-user` meldet, dass es läuft; die
      Cache-Statistik sieht normal aus (`exp:velocity cache stats`).

## Bericht an den Kunden

- [ ] Verfügbarkeit des Monats aus Ihrer externen Prüfung.
- [ ] Vorfälle und was getan wurde.
- [ ] Eingespielte und geplante Updates.
- [ ] Ergebnis des Wiederherstellungstests, wenn diese Website die Stichprobe war.
- [ ] Wachstum von Festplatte und Datenbank.

## Verwandte Seiten

- [Überblick über die Überwachung](../betrieb/ueberblick-ueber-die-ueberwachung.md)
- [Checkliste für die Sicherheitsprüfung](checkliste-fuer-die-sicherheitspruefung.md)
- [SLA-Optionen](../geschaeft/sla-optionen-fuer-exponential-hosting.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
