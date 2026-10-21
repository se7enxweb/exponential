---
title: "Lektion 4.5: Hosting und Betrieb, schriftlicher Test"
audience: trainer, hosting, admin
category: training
order: 45
summary: Ein schriftlicher Test mit vierzehn Fragen für das Ende des Kurses Hosting und Betrieb, zu beschreibbaren Verzeichnissen und Benutzern, Sicherungen und Wiederherstellungen, dem Deployment einer PHP-Änderung, Wartung und Überwachung, Velocity-Health-Checks, Vorfällen mit voller Festplatte und Mail auf Testkopien, mit einem Lösungsschlüssel, der die Hosting-Seiten zitiert. 30 Minuten für die Teilnehmenden.
keywords: prüfungen und praktische übungen, hosting und betrieb, schriftlicher test, beschreibbare verzeichnisse, dateiberechtigungen, sicherung, wiederherstellung, wiederherstellungstest, deployment, wartung, health-check, watchdog, festplatte voll, assessments and practical exercises, hosting and operations, written test, writable directories, file permissions, backup, restore, restore test, deploy, exp:velocity deploy, maintenance, 503, health check, disk full, file transport
type: help
---
# Lektion 4.5: Hosting und Betrieb, schriftlicher Test

Dieser Test schließt den Kurs Hosting und Betrieb ab. Er prüft das Urteilsvermögen, das gehostete Websites sicher
hält: welcher Benutzer welche Dateien schreibt, was eine Sicherung enthalten muss, die Reihenfolge eines Deployments,
wie man Wartung von einem Ausfall unterscheidet und wie man verhindert, dass eine wiederhergestellte Kopie echten
Menschen Mails schickt.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 4: Die technischen Kurse prüfen](README.md) |
| Dauer | 45 Minuten (5 Einweisung, 30 Test, 10 Besprechung der Antworten) |
| Niveau | Fortgeschrittene bis Experten |
| Zielgruppe | Teilnehmende am Ende des Kurses Hosting und Betrieb |
| Voraussetzungen | Der Kurs Hosting und Betrieb; [Lektion 1.2](../modul-01-pruefungen-gestalten/schriftliche-tests-schreiben.md) für den Trainer |

## Lernziele

Wer diesen Test besteht, hat gezeigt, dass er Folgendes kann:

- sagen, welche Verzeichnisse beschreibbar sein müssen und für wen;
- aufzählen, was eine Sicherung enthält und wie Datenbank und Dateien zueinander passen;
- die Reihenfolge eines Deployments erklären und warum sie wichtig ist;
- eine Website während der Wartung und mit Velocity richtig überwachen;
- sicher handeln, wenn eine Festplatte voll ist oder eine Sicherung für einen Test wiederhergestellt wird.

## Hinweise für Trainer

### So führen Sie ihn durch

1. Teilen Sie die Fragen ohne den Schlüssel aus; 30 Minuten.
2. Gehen Sie die Antworten mit den Hosting-Seiten auf dem Projektor durch.

### Häufige Fragen

**Unsere Hosts betreiben kein Velocity.** Lassen Sie die Fragen 9 und 10 weg und senken Sie die Bestehensgrenze um
zwei Punkte.

### Stolperfallen

- **Teilnehmende, die zu `rm -rf var/` greifen.** Frage 12 steht aus gutem Grund im Test; besprechen Sie sie.

## Der Test

1. Welches Verzeichnis muss immer beschreibbar bleiben, und warum?
2. Welche Einrichtung der Eigentumsrechte wird für eine Installation empfohlen?
3. Warum macht ein Leeren des Caches als root eine Website später kaputt?
4. Eine Sicherung besteht aus zwei Hälften. Nennen Sie sie und die Bedingung, die sie erfüllen müssen.
5. Welches Verzeichnis einer Sicherung ist so schutzbedürftig wie der Server selbst, und warum?
6. Wie sichern Sie eine SQLite-Datenbank einer laufenden Website?
7. Eine PHP-Klasse wurde geändert. Welcher einzelne Befehl bringt sie in Betrieb, und wie sehen Sie vorher, was er tun
   würde?
8. Warum werden die Caches für gerenderte Ausgaben erst geleert, nachdem PHP-FPM und Velocity den neuen Code
   ausführen?
9. Welche Adresse beantwortet einen Health-Check eines Velocity-Servers, und was beweist sie nicht?
10. Was tut der Velocity-Watchdog, und was tut er niemals?
11. Ihre Überwachung meldet während eines geplanten Wartungsfensters einen 503. Wie sollte sich die Prüfung verhalten?
12. Die Festplatte ist voll. Nennen Sie eine Sache, die Sie entfernen dürfen, und eine, die Sie niemals entfernen
    dürfen.
13. Sie stellen eine Sicherung auf einem Testrechner wieder her. Nennen Sie zwei Dinge, die Sie tun, damit sie sich
    nicht wie die Live-Website verhalten kann.
14. Warum testen Sie eine Wiederherstellung auf einem anderen Rechner, und wie oft?

<details>
<summary>Antworten</summary>

1. `var/` und alles darunter: Caches, kompilierte Templates, Logs, Uploads, Bildvarianten, Autoloads, die
   SQLite-Datenbank ([Beschreibbare Verzeichnisse und Berechtigungen](../../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md#was-beschreibbar-sein-muss)).
2. Alles läuft als Benutzer der Website: PHP-FPM-Pool, Velocity-Worker, Cron, Konsole und Composer
   ([Beschreibbare Verzeichnisse und Berechtigungen](../../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md#einrichtung-1-ein-benutzer)).
3. Dateien, die root geschrieben hat, kann der Benutzer der Website danach nicht ersetzen
   ([Beschreibbare Verzeichnisse und Berechtigungen](../../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md#einrichtung-1-ein-benutzer)).
4. Die Datenbank und die Dateien, zum selben Zeitpunkt erstellt
   ([Sicherungen: was gesichert wird](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md#ein-backup-hat-zwei-hälften)).
5. `settings/override/`: Es enthält das Datenbankpasswort und weitere Zugangsdaten
   ([Sicherungen: was gesichert wird](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md#die-zu-sichernden-dateien)).
6. Mit der Online-Sicherung von SQLite, `sqlite3 <db file> ".backup '<target>'"`, niemals durch Kopieren einer Datei,
   die gerade benutzt wird
   ([Datenbanksicherungen je Datenbanksystem](../../../hosting/betrieb/datenbank-backups-je-engine.md#auf-einen-blick)).
7. `./console exp:velocity deploy --allow-root-user`; vorher mit `--dry-run`
   ([Änderungen in Betrieb nehmen](../../../hosting/betrieb/aenderungen-ausrollen.md#ein-befehl-für-eine-php-änderung)).
8. Sonst werden Seiten, die in der Zwischenzeit angefragt werden, vom alten Code gerendert und erneut im Cache
   gespeichert ([Änderungen in Betrieb nehmen](../../../hosting/betrieb/aenderungen-ausrollen.md#die-schritte-und-ihre-reihenfolge)).
9. `/Q/health` (zum Beispiel `http://127.0.0.1:8088/Q/health`); sie zeigt, dass der Serverprozess antwortet, nicht,
   dass eine Seite der Website gerendert wird ([Health-Checks und Verfügbarkeit](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md#velocity-zustandsendpunkt-und-watchdog)).
10. Er startet die Standard-Engine neu, wenn sie nicht läuft und niemand das beabsichtigt hat; einen laufenden Server
    rührt er niemals an
    ([Health-Checks und Verfügbarkeit](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md#velocity-zustandsendpunkt-und-watchdog)).
11. Setzen Sie die Prüfung für das Fenster aus oder akzeptieren Sie einen 503 mit `Retry-After`, solange die Wartung
    läuft; schlagen Sie Alarm, wenn das Fenster länger dauert als angekündigt
    ([Health-Checks und Verfügbarkeit](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md#geplante-wartung-ist-kein-ausfall)).
12. Erlaubt: Caches über den Cache-Befehl, rotierte Log-Archive über `exp:logs --prune`. Niemals: `var/storage`,
    `settings/` oder die SQLite-Dateien von Hand ([Festplatte voll](../../../hosting/runbooks/festplatte-voll.md#was-entfernt-werden-darf-und-was-nie)).
13. Zwei davon: den Datei-Transport (oder `DebugSending`) einstellen, die Crontab nicht installieren, den Zugriff
    beschränken, die Kopie danach entfernen ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#ein-wiederherstellungstest-auf-einer-anderen-maschine)).
14. Er beweist die Sicherung, das Verfahren und die Dauer; mindestens einmal und nach jedem größeren Upgrade sowie
    nach Ihrem eigenen Zeitplan ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#ein-wiederherstellungstest-auf-einer-anderen-maschine)).

</details>

## Bewertung

- Ein Punkt pro Frage; die Fragen 4, 12 und 13 brauchen beide Teile.
- Bestanden: 10 von 14 Punkten oder mehr, mit den Fragen 5 und 13 richtig: Beide schützen die Daten anderer Menschen.

## Weiterführende Literatur

- [Hosting-Unternehmen: hier beginnen](../../../hosting/hosting-anbieter-hier-beginnen.md)
- [Sicherungen: was gesichert wird](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md) und
  [Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)
- [Änderungen in Betrieb nehmen](../../../hosting/betrieb/aenderungen-ausrollen.md)

## Verwandte Seiten

- [Lektion 4.6: Hosting und Betrieb, praktische Aufgabe](hosting-betrieb-praktische-aufgabe.md)
- [Modul 4: Die technischen Kurse prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
