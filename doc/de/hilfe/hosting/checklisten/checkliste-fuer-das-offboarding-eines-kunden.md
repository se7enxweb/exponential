---
title: Checkliste für das Offboarding eines Kunden
audience: hosting
category: hosting
order: 60
summary: Was ein Hoster tut, wenn ein Kunde geht oder seine Exponential-Website anderswohin umzieht - ein vollständiger Export zur Übergabe, Zugangsdaten, DNS, das letzte Backup, das Löschen von Daten nach Vertrag und Gesetz und das Abschließen der Unterlagen.
keywords: Offboarding, Kunde geht, Kündigung, Übergabe, Export, Rückgabe von Daten, Backup, Löschung, Aufbewahrung, DNS, Zugangsdaten, DSGVO, Vertragsende, offboarding, customer leaves, cancellation, handover, data return, deletion, retention, credentials, GDPR, contract end
type: help
---
# Checkliste für das Offboarding eines Kunden

Diese Checkliste ist für die Mitarbeitenden in Support und Systemadministration eines Hosters, wenn ein Kunde seinen
Vertrag beendet oder seine Website anderswohin umzieht. Exponential ist freie Software; ein Kunde kann die Website also
überallhin mitnehmen. Eine saubere Übergabe gehört zu einem guten Hosting-Dienst und zu Ihrem Ruf.

```text
Customer: <customer>       Site(s): <domains>       Contract end: <date>       Handled by: <name>
```

## Den Plan vereinbaren

- [ ] Datum des letzten Exports und der Abschaltung schriftlich vereinbart.
- [ ] Was der Kunde erhält und wie (sichere Übertragung, keine E-Mail-Anhänge).
- [ ] Wie lange Sie Backups nach dem Ende aufbewahren, nach dem Vertrag und dem geltenden Recht
      ([Aufbewahrung von Logs und Daten](../sicherheit/aufbewahrung-von-logs-und-daten.md)).

## Der Export zur Übergabe

Eine vollständige Website besteht aus der Datenbank **und** den Dateien ([Was gesichert werden muss](../betrieb/backups-was-gesichert-werden-muss.md)):

- [ ] Datenbank-Dump mit dem Werkzeug der Engine ([Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)).
- [ ] `var/storage` (und `var/<site>/storage`, wo getrennt).
- [ ] `settings/override` und `settings/siteaccess`; sie enthalten das Datenbankpasswort, übertragen Sie sie also
      vertraulich und sagen Sie dem Kunden, dass er dieses Passwort beim neuen Hoster ändern soll.
- [ ] Eigene Erweiterungen und Designs oder die Adressen ihrer Git-Repositories.
- [ ] Eine Notiz der Versionen: Exponential, PHP, Datenbank-Engine und Version, Engine (Velocity, FrankenPHP, Apache oder
      nginx), Erweiterungen.
- [ ] Erstellen Sie den Export mit eingeschaltetem Wartungsmodus, damit Datenbank und Dateien zusammenpassen:
      `php bin/php/maintenance.php on --message="..." --allow-root-user`.

Der neue Hoster des Kunden kann [Eine Website zwischen Hostern umziehen](../migrationen/eine-website-zwischen-hostern-umziehen.md) folgen.

## Zugang und DNS

- [ ] DNS: Wenn Sie es verwalten, übertragen Sie es oder ändern Sie die Einträge am vereinbarten Datum.
- [ ] Zertifikate, die Sie ausgestellt haben, werden nicht übertragen; der neue Hoster stellt eigene aus.
- [ ] Die Konten Ihrer Mitarbeitenden in der Administration des Kunden werden entfernt, wenn die Website aus Ihrem Export
      anderswo weiterläuft; sagen Sie dem Kunden, welche Konten Ihre waren.
- [ ] Der Zugang des Kunden zu Ihrem Panel und zu SFTP wird am Tag der Abschaltung geschlossen.

## Abschaltung

- [ ] Website am vereinbarten Datum im Wartungsmodus oder abgeschaltet.
- [ ] Cronjobs der Website entfernt ([Cronjobs](../betrieb/cronjobs.md)).
- [ ] Velocity-Instanz oder PHP-FPM-Pool der Website entfernt; ihre Ports freigegeben.
- [ ] Überwachungsprüfungen der Website entfernt.
- [ ] Mailversand für die Domain der Website eingestellt.

## Löschung

- [ ] Dateien, Datenbank und Datenbankbenutzer der Website am vereinbarten Datum gelöscht.
- [ ] Backups am Ende der vereinbarten Aufbewahrung gelöscht, nicht früher, es sei denn, der Kunde bittet darum und das
      Gesetz erlaubt es.
- [ ] Logs mit personenbezogenen Daten (Zugriffslogs, Anfrage-Logs) gemäß Ihrer Aufbewahrungsrichtlinie behandelt.
- [ ] Löschung dem Kunden schriftlich bestätigt, mit Datum.

## Unterlagen

- [ ] Der Datensatz des Kunden abgeschlossen, mit Exportdatum, Löschdatum und wer es getan hat.
- [ ] Erkenntnisse festgehalten, falls der Kunde wegen eines Problems gegangen ist.

## Verwandte Seiten

- [Eine Website zwischen Hostern umziehen](../migrationen/eine-website-zwischen-hostern-umziehen.md)
- [Compliance und DSGVO](../sicherheit/compliance-und-dsgvo.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
