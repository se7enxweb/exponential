---
title: Checkliste für den Start einer Kunden-Website
audience: hosting, admin
category: hosting
order: 30
summary: Die Checkliste, die ein Hoster abarbeitet, bevor die Exponential-Website eines Kunden öffentlich wird - Administratorpasswort und Konten, Einstellungen, Caches, HTTPS und Header, Cron, Mail, Suche, Backups mit einem Wiederherstellungstest, Überwachung, Logs und die Übergabe an den Kunden.
keywords: Go-Live, Start, Checkliste, Kunden-Website, Produktion, Administratorpasswort, Debug aus, Caches, HTTPS, CSP, Cron, Mail, Suchindex, Backup, Wiederherstellungstest, Überwachung, Übergabe, DNS, go-live, launch, checklist, customer site, production, administrator password, debug off, search index, restore test, monitoring, handover
featured: true
type: help
---
# Checkliste für den Start einer Kunden-Website

Diese Checkliste ist für den Systemadministrator des Hosters und den Website-Betreiber des Kunden gemeinsam, in den Tagen,
bevor eine Website auf Ihrer Plattform öffentlich wird. Sie setzt voraus, dass der Server die
[Checkliste für einen neuen Server](checkliste-fuer-einen-neuen-server.md) bestanden hat.

```text
Site: <domain>        Customer: <customer>        Go-live: <date>        Checked by: <names>
```

## Konten und Passwörter

- [ ] Das bei der Installation gesetzte Administratorpasswort ist kein Standardpasswort und nur den Personen bekannt, die
      es brauchen ([Das Standard-Administratorpasswort](../../standard-administratorpasswort.md),
      [Das Administratorpasswort ändern](../../administratorpasswort-aendern.md)).
- [ ] Testkonten, die während der Entwicklung angelegt wurden, sind entfernt oder deaktiviert.
- [ ] Jeder Redakteur hat ein persönliches Konto; niemand teilt sich das Administratorkonto.
- [ ] Über die zweistufige Anmeldung für Administratoren entschieden ([Zweistufige Anmeldung](../sicherheit/zweistufige-anmeldung.md)).

## Einstellungen

- [ ] Die Debug-Ausgabe ist für die öffentlichen Siteaccesses aus (`[DebugSettings] DebugOutput=disabled` in den
      Einstellungen der Website).
- [ ] Die Adressen der Website und die Zuordnung der Siteaccesses nennen die Produktionsdomains.
- [ ] Mailtransport und Absender sind die der Produktion ([Mailzustellung](../betrieb/mailzustellung.md)).
- [ ] Der in Seiten angezeigte Name der Installation ist der der Produktion, wo er verwendet wird.
- [ ] `settings/override` ist nur für den Systembenutzer der Website und ihre Engine oder ihren PHP-FPM-Pool lesbar.

## Caches und Leistung

- [ ] Der View-Cache und der Template-Cache sind für die öffentlichen Siteaccesses an ([Cache-Schichten](../leistung/cache-schichten.md)).
- [ ] Mit Velocity: Der Antwort-Cache ist an und nach dem letzten Ausrollen geleert
      (`php bin/php/console exp:velocity cache clear --allow-root-user`).
- [ ] Die Startseite und die wichtigsten Seiten der Bereiche antworten aus einem warmen Cache innerhalb des Ziels Ihres
      Tarifs; gemessen mit dem Benchmark-Befehl ([Benchmark einer gehosteten Website](../leistung/benchmark-einer-gehosteten-website.md)).

## HTTPS und Header

- [ ] Das Zertifikat deckt jede Produktionsdomain ab und wird automatisch erneuert ([TLS und HTTPS](../sicherheit/tls-und-https.md)).
- [ ] HTTP leitet auf HTTPS um.
- [ ] Die Content Security Policy erzeugt auf den wichtigsten Seiten keine Meldungen ([Content Security Policy](../sicherheit/content-security-policy.md)).
- [ ] Die Administration ist wie vereinbart eingeschränkt ([Die Administration einschränken](../sicherheit/die-administration-einschraenken.md)).

## Cron, Suche, Mail

- [ ] Die Crontab der Website führt `runcronjobs.php` mit seinen Teilen aus ([Cronjobs](../betrieb/cronjobs.md)); die
      Cronjob-Konsole zeigt die letzten Läufe ([Cronjob-Konsole](../betrieb/die-cronjob-konsole.md)).
- [ ] Der Suchindex ist vollständig (`php bin/php/updatesearchindex.php` lief nach dem letzten Import)
      ([Such-Backends](../grundlagen/such-backends.md)).
- [ ] Eine Testmail der Website (zum Beispiel ein Zurücksetzen des Passworts für ein Testkonto) kommt an und wird nicht
      als Spam markiert.

## Backups und Wiederherstellung

- [ ] Der Backup-Job für diese Website läuft: Datenbank plus `var/storage`, `settings/override`, `settings/siteaccess`
      ([Was gesichert werden muss](../betrieb/backups-was-gesichert-werden-muss.md)).
- [ ] Ein Backup dieser Website wurde auf einer anderen Maschine **wiederhergestellt**, und die wiederhergestellte Website
      öffnet sich ([Wiederherstellung und Wiederherstellungstests](../betrieb/wiederherstellung-und-wiederherstellungstests.md)).

## Überwachung und Logs

- [ ] Eine externe Prüfung einer Seite, die die Datenbank berührt ([Zustandsprüfungen und Verfügbarkeit](../betrieb/zustandspruefungen-und-verfuegbarkeit.md)).
- [ ] Der Log-Viewer zeigt keine unerklärten Fehler in `error.log` aus den letzten Testtagen
      ([Log-Viewer und Log-Rotation](../betrieb/log-viewer-und-log-rotation.md)).
- [ ] `security.log` wird beobachtet ([Sicherheitslog](../betrieb/sicherheitslog.md)).

## DNS und Umschaltung

- [ ] Die Time-to-Live im DNS vorab gesenkt; die neuen Einträge bereit.
- [ ] Die Website vor der DNS-Änderung über einen Eintrag in der hosts-Datei auf der Produktionsmaschine getestet.
- [ ] Rückfall vereinbart: wie, bis wann ([Kunden zu Ihrem Hosting migrieren](../migrationen/kunden-zu-ihrem-hosting-migrieren.md#rückfall)).

## Übergabe

- [ ] Der Kunde weiß, wie er ein Problem meldet und was er angibt (Seite, Uhrzeit, Fehlerreferenz)
      ([Support-Stufen](../geschaeft/support-stufen-fuer-exponential-hosting.md)).
- [ ] Die Redakteure des Kunden wissen, wo die Hilfe ist: das Hilfe-Center ihrer Administration.
- [ ] Das SLA und die Aufbewahrung der Backups sind schriftlich bestätigt.
- [ ] Der Datensatz der Website ist vollständig: Versionen, Datenbank-Engine, Engine, Erweiterungen, Kontakte.

**Demomodus.**

- [ ] Demomodus aus: `EXP_DEMO_MODE` ist für keinen Prozess der Website gesetzt (Pool, virtueller Host, `.htaccess`, Unit, Crontab); kein Banner **Demo-Modus** in der Administration; keine Zeile `demo/mode` in `var/log/security.log` ([Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md)).

## Verwandte Seiten

- [Checkliste für einen neuen Server](checkliste-fuer-einen-neuen-server.md)
- [Checkliste für die Sicherheitsprüfung](checkliste-fuer-die-sicherheitspruefung.md)
- [Kapitel 10: Nach der Installation](../../../installation/10-nach-der-installation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
