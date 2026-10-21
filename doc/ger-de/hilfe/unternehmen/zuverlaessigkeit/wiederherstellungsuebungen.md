---
title: Wiederherstellungsübungen
audience: businesses, admin, hosting, project-manager
category: business-solutions
order: 80
summary: Wie Sie nachweisen, dass Ihre Backups funktionieren, indem Sie regelmäßig eines auf einem anderen Rechner wiederherstellen, was Sie prüfen, wie Sie verhindern, dass die Kopie echten Kunden Mails schickt, und wie Sie das Ergebnis als Nachweis festhalten.
keywords: Wiederherstellungsübung, Wiederherstellungstest, Wiederherstellungszeit, Backup-Test, Notfallwiederherstellung, Probe, Nachweis, Testrechner, Mail-Transport Datei, restore drill, restore test, recovery time, backup test, disaster recovery, rehearsal, evidence, test machine, mail transport file
type: help
---
# Wiederherstellungsübungen

Diese Seite richtet sich an Führungskräfte, die einen Nachweis brauchen, dass sich das Unternehmen vom Verlust eines
Servers erholen kann, und an die Person im Betrieb, die die Übung durchführt. Eine Wiederherstellungsübung ist eine
geplante Wiederherstellung eines echten Backups auf einem anderen Rechner, mit Zeitmessung und Prüfung. Aus „Wir haben
Backups“ wird so „Wir haben letzten Monat eines in so vielen Minuten wiederhergestellt“. Das technische Vorgehen steht
unter [Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md).

## Inhalt

1. [Warum üben](#warum-üben)
2. [Wie oft](#wie-oft)
3. [Vor der Übung](#vor-der-übung)
4. [Die Übung, Schritt für Schritt](#die-übung-schritt-für-schritt)
5. [Prüfungen, die die Wiederherstellung belegen](#prüfungen-die-die-wiederherstellung-belegen)
6. [Die Kopie still und vertraulich halten](#die-kopie-still-und-vertraulich-halten)
7. [Das Ergebnis festhalten](#das-ergebnis-festhalten)
8. [Was eine fehlgeschlagene Übung lehrt](#was-eine-fehlgeschlagene-übung-lehrt)

## Warum üben

Die Anleitungen sind deutlich: „Ein Backup, das Sie nie wiederhergestellt haben, ist eine Hoffnung“
([Betrieb einer Site](../../../anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen)). Eine Übung belegt drei Dinge auf
einmal:

- **Das Backup ist vollständig**: Datenbank und Dateien sind beide da, vom selben Zeitpunkt.
- **Das Vorgehen funktioniert**: Die diensthabenden Personen können ihm folgen, und die Anleitung ist aktuell.
- **Die Wiederherstellungszeit ist bekannt**: „Schreiben Sie auf, wie lange es gedauert hat; das ist Ihre
  Wiederherstellungszeit“
  ([Backups und Wiederherstellungen](../../../../eng-us/help/creators/website-creators/backups-and-restores.md#the-restore-test)).

Ohne den letzten Punkt ist jedes Versprechen, wie schnell Sie wiederherstellen, eine Vermutung.

## Wie oft

Die Quellen setzen ein Minimum: wiederherstellen „auf einem anderen Rechner mindestens einmal und nach jeder größeren
Aktualisierung“
([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#ein-wiederherstellungstest-auf-einer-anderen-maschine)),
und die wöchentliche Checkliste verlangt, dass ein aktuelles Backup „mindestens einmal auf einem anderen Rechner
wiederhergestellt wurde“
([Betrieb einer Site, eine wöchentliche Checkliste](../../../anleitungen/betrieb-einer-website.md#10-eine-wöchentliche-checkliste)). Die
monatliche Checkliste des Hosting-Bereichs stellt jeden Monat das Backup einer Website wieder her, im Wechsel
([Checkliste für den monatlichen Betrieb](../../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md#backups)).

Wählen Sie oberhalb dieses Minimums Ihren eigenen Rhythmus und tragen Sie ihn mit einer benannten verantwortlichen
Person in den Kalender ein.

## Vor der Übung

- [ ] Ein Testrechner (oder ein vorübergehender Server) steht bereit, der aus dem öffentlichen Internet **nicht**
      erreichbar ist.
- [ ] Er läuft mit demselben Exponential-Release und demselben `composer.lock` wie das Backup
      ([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#bevor-sie-wiederherstellen)).
- [ ] Die Übung führt nicht immer dieselbe Person durch: Wechseln Sie ab, damit das Wissen geteilt wird.
- [ ] Eine Stoppuhr oder eine Notiz von Start- und Endzeit.
- [ ] Das Wiederherstellungsvorgehen ist ausgedruckt oder geöffnet, und Sie folgen ihm genau so, wie es geschrieben ist.

## Die Übung, Schritt für Schritt

Die Schritte entsprechen dem Wiederherstellungsvorgehen des Installationsbuchs
([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#die-wiederherstellung-schritt-für-schritt)):

1. Notieren Sie die Startzeit.
2. Holen Sie das Backup von seinem Speicherort, so wie Sie es nach dem Verlust des Servers tun würden.
3. Schalten Sie für die Testkopie die Wartung ein (`php bin/php/maintenance.php on --message="Restoring" --allow-root-user`);
   unter Velocity stoppen Sie auch den Server.
4. Stellen Sie die Datenbank mit dem Werkzeug ihrer Engine wieder her und entpacken Sie die Dateien.
5. Korrigieren Sie Eigentümer und Rechte der entpackten Dateien.
6. Leeren Sie jeden Cache und erzeugen Sie die Autoloads neu:
   `php bin/php/ezcache.php --clear-all --allow-root-user` und `php bin/php/ezpgenerateautoloads.php -e`.
7. Unter Velocity starten Sie den Server und leeren seinen Antwortcache; schalten Sie die Wartung aus.
8. Führen Sie die Prüfungen unten aus. Notieren Sie die Endzeit.

## Prüfungen, die die Wiederherstellung belegen

Aus [Wiederherstellung und Wiederherstellungstests, Prüfungen nach einer Wiederherstellung](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#prüfungen-nach-einer-wiederherstellung)
und [Backups und Wiederherstellungen](../../../../eng-us/help/creators/website-creators/backups-and-restores.md#the-restore-test):

| Prüfung | Gutes Ergebnis |
|---|---|
| Die Startseite, eine tiefe Unterseite, ein Bild, ein Download | Sie laden, Bilder eingeschlossen |
| Anmeldung an der Administration | Das Dashboard öffnet sich |
| **Setup > Systeminformationen** | Das erwartete Release; keine fehlgeschlagene Zustandsprüfung |
| `php bin/php/cache.php status --allow-root-user` | Endet mit `PASS` |
| `php bin/php/audit.php verify --allow-root-user` | Exit-Status 0 |
| Ein Testelement veröffentlichen | Es erscheint auf der Test-Website |
| Ein Formular (Kontakt, Anmeldung) | Es lässt sich absenden; die Mail geht an den Datei-Transport, nicht an eine echte Adresse |
| Die neuesten Inhalte, die Sie erwarten | Sie sind da; das sagt Ihnen den tatsächlichen Wiederherstellungspunkt |
| `tail -n 50 var/log/error.log` | Nichts, was Sie nicht erklären können |

Bei einem Shop zusätzlich: Die neueste erwartete Bestellung steht in der Bestellliste
([Online-Shops und kleine Geschäfte](../shops/README.md)).

## Die Kopie still und vertraulich halten

Eine wiederhergestellte Kopie ist eine vollständige Website mit echten Konten und echten E-Mail-Adressen. Bevor Sie sie
starten
([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#ein-wiederherstellungstest-auf-einer-anderen-maschine)):

1. **Stoppen Sie Mails**: Setzen Sie `[MailSettings] Transport=file` in `settings/override/site.ini.append.php` des
   Testrechners, sodass jede Mail in eine Datei geschrieben statt versendet wird.
2. **Installieren Sie keine Crontab** auf dem Testrechner, sonst laufen Benachrichtigungen, Newsletter und Inhaltsjobs
   gegen echte Adressen.
3. **Beschränken Sie den Zugang**: Die Kopie enthält personenbezogene Daten; halten Sie sie vom öffentlichen Internet
   fern.
4. **Entfernen Sie die Kopie**, wenn die Übung beendet ist, einschließlich ihrer Datenbank und ihrer Dateien.

> **Warnung**
> Eine vergessene Testkopie, die einen Newsletter an alle Mitglieder schickt oder mit echten Kundendaten online bleibt,
> macht aus einer Übung eine Störung. Die Schritte 1 bis 4 sind nicht optional.

## Das Ergebnis festhalten

„Halten Sie jeden Test in einer Zeile fest: Datum, Website, verwendetes Backup, Dauer der Wiederherstellung, Ergebnis,
wer ihn durchgeführt hat“
([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#ein-wiederherstellungstest-auf-einer-anderen-maschine)).
Eine Tabelle wie diese genügt:

| Datum | Verwendetes Backup | Wer | Minuten bis zur Wiederherstellung | Neueste gefundene Inhalte | Ergebnis | Nacharbeit |
|---|---|---|---|---|---|---|
| (ausfüllen) | | | | | | |

Diese Zeile ist der Nachweis, nach dem eine Führungskraft, ein Prüfer oder ein Kunde fragt. Bewahren Sie die Historie
auf; eine steigende Wiederherstellungszeit ist eine frühe Warnung, dass die Website gewachsen ist und der Plan sich
ändern muss.

## Was eine fehlgeschlagene Übung lehrt

Eine fehlgeschlagene Übung ist ein Erfolg: Sie haben das Problem an einem ruhigen Tag gefunden. Häufige Erkenntnisse
([Wiederherstellung und Wiederherstellungstests, was schiefgeht](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#was-schiefgeht)):

| Erkenntnis | Übliche Ursache |
|---|---|
| Bilder fehlen, Seiten sind in Ordnung | Die Dateihälfte wurde nicht gesichert oder stammt von einem anderen Zeitpunkt |
| Fehler zu fehlenden Tabellen oder Spalten | Das Backup stammt von einem anderen Release; führen Sie die Upgrade-Schritte aus |
| Die Website zeigt alte Inhalte | Die Caches wurden nach der Wiederherstellung nicht geleert |
| `Class ... not found` | Autoloads oder `vendor/` passen nicht |
| Der Webserver kann `var/` nicht schreiben | Eigentümer oder Rechte nach dem Entpacken falsch |
| Die Wiederherstellung dauert viel länger als erwartet | Die Website ist gewachsen; überprüfen Sie Ihr Versprechen zur Wiederherstellungszeit |

Beheben Sie die Ursache, aktualisieren Sie das schriftliche Vorgehen und führen Sie die Übung erneut durch.

## Verwandte Seiten

- [Backups, die sich wiederherstellen lassen](backups-die-sich-wiederherstellen-lassen.md)
- [Störungsbehandlung für kleine Teams](stoerungsbehandlung-fuer-kleine-teams.md)
- [Runbook: aus einem Backup wiederherstellen](../../hosting/runbooks/aus-einem-backup-wiederherstellen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
