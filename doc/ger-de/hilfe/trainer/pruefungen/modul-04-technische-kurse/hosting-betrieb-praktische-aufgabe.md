---
title: "Lektion 4.6: Hosting und Betrieb, praktische Aufgabe"
audience: trainer, hosting, admin
category: training
order: 46
summary: Eine praktische Aufgabe von 75 Minuten für das Ende des Kurses Hosting und Betrieb, eine SQLite-Schulungs-Website konsistent sichern, sie in einem zweiten Verzeichnis als sichere Testkopie mit Datei-Transport und ohne Crontab wiederherstellen, sie prüfen, einen Fehler über seine Referenz im Log finden und das einzeilige Wiederherstellungsprotokoll schreiben, bewertet mit einem Bewertungsraster aus sechs Kriterien. 100 Minuten mit Bewertung.
keywords: prüfungen und praktische übungen, hosting und betrieb, praktische aufgabe, bewertungsraster, sicherung, wiederherstellung, wiederherstellungstest, wartung, datei-transport, eigentumsrechte, fehlerreferenz, log, wiederherstellungsprotokoll, assessments and practical exercises, hosting and operations, practical task, rubric, backup, sqlite3 .backup, restore, restore test, maintenance, file transport, crontab, ownership, error reference, restore record
type: help
---
# Lektion 4.6: Hosting und Betrieb, praktische Aufgabe

„Funktionieren Ihre Sicherungen?“ ist die Frage, die jeder Host mit Nachweisen beantworten muss. In dieser Aufgabe
erstellen die Teilnehmenden eine konsistente Sicherung einer Schulungs-Website, stellen sie als Testkopie wieder her,
die niemanden erreichen kann, prüfen sie und schreiben das Protokoll, das die Frage beantwortet.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Verwenden Sie nur Schulungsinstallationen
> auf SQLite, mit Testkonten unter `example.invalid`. Verwenden Sie für eine Prüfung niemals eine Kopie der Website
> eines Kunden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 4: Die technischen Kurse prüfen](README.md) |
| Dauer | 100 Minuten (5 Einweisung, 75 Aufgabe, 20 Bewertung) |
| Niveau | Fortgeschrittene bis Experten |
| Zielgruppe | Teilnehmende am Ende des Kurses Hosting und Betrieb |
| Voraussetzungen | [Lektion 4.5](hosting-betrieb-schriftlicher-test.md); eine Shell auf einem Schulungsrechner mit einer SQLite-Schulungsinstallation pro Teilnehmer |

## Lernziele

Wer diese Aufgabe besteht, hat gezeigt, dass er Folgendes kann:

- Datenbank und Dateien einer laufenden Website in einem Zeitfenster sichern;
- eine Website in ein separates Verzeichnis mit korrekten Eigentumsrechten wiederherstellen;
- eine wiederhergestellte Kopie sicher machen: keine echten Mails, kein Cron, kein öffentlicher Zugriff;
- eine wiederhergestellte Website prüfen und die Zeit messen;
- einen protokollierten Fehler über seine Referenz finden.

## Hinweise für Trainer

### So führen Sie sie durch

1. **Vorher**: Alle Teilnehmenden haben eine SQLite-Schulungsinstallation in einem Verzeichnis, das ihnen gehört,
   über einen lokalen Port ausgeliefert, und ein zweites leeres Verzeichnis für die Kopie. Das Kommandozeilenwerkzeug
   `sqlite3` ist installiert. Erzeugen Sie auf jeder Installation eine Fehlerseite (öffnen Sie zum Beispiel ein Modul,
   das es nicht gibt) und notieren Sie ihre Referenz.
2. **Einweisung**: Teilen Sie die Schritte und das Bewertungsraster aus; 75 Minuten. Die Teilnehmenden arbeiten als
   Benutzer der Installation, nicht als root.
3. **Bewertung**: Lesen Sie die Notizen und Zeitangaben, prüfen Sie die Einstellungen der Kopie, `crontab -l` des
   Benutzers der Teilnehmenden und die Eigentumsrechte am Verzeichnis `var/` der Kopie.

### Häufige Fragen

**Wie liefern wir die Kopie aus?** Über einen anderen lokalen Port, wie in
[Die Websites der Übungsumgebung ausliefern](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-websites-der-uebungsumgebung-bereitstellen.md);
die Kopie wird niemals über den Rechner hinaus erreichbar gemacht.

**Die Kopie kommt im Wartungsmodus zurück.** Wurde `var/` kopiert, während die Wartung eingeschaltet war, enthält es
`var/maintenance.json`; `php bin/php/maintenance.php off` beendet sie. Wer das selbst herausfindet, bekommt den Punkt
für das Prüfen.

### Stolperfallen

- **Die Datenbankdatei kopieren, während sie benutzt wird.** Wird unter **Konsistente Sicherung** als Fehler bewertet.
- **Als root arbeiten.** Dateien im Verzeichnis `var/` der Kopie, die root gehören, machen sie später kaputt; bewertet
  unter **Wiederherstellung**.

## Praktische Übung

Die Aufgabe, wie sie an die Teilnehmenden ausgeteilt wird. Arbeiten Sie als Benutzer Ihrer Installation; notieren Sie
jeden Befehl und die Zeit, die jeder Teil gedauert hat.

1. Schalten Sie für Ihre Schulungs-Website die Wartung mit einer Meldung ein:
   `php bin/php/maintenance.php on --message="Backup" --until=30m --allow-admin`.
2. Sichern Sie die Datenbank mit der Online-Sicherung von SQLite in ein eigenes Sicherungsverzeichnis:

   ```bash
   sqlite3 <path to the database file> ".backup '<backup dir>/site.db'"
   ```

   (bei einer Standardinstallation mit SQLite liegt die Datenbank in `var/storage/sqlite3/`).
3. Archivieren Sie im selben Zeitfenster die Dateien: `var/storage/`, `settings/override/`, `settings/siteaccess/` und
   jede eigene Erweiterung oder jedes eigene Design. Notieren Sie, was Sie absichtlich weggelassen haben und warum.
4. Schalten Sie die Wartung aus und prüfen Sie die Website.
5. Bereiten Sie die Kopie vor: Legen Sie dasselbe Release des Codes in das zweite Verzeichnis (wie im Kurs), entpacken
   Sie dort Ihr Dateiarchiv und stellen Sie die Datenbankdatei in deren `var/storage/sqlite3/` wieder her.
6. Machen Sie die Kopie sicher, bevor sie läuft: Setzen Sie `[MailSettings] Transport=file` in der Datei
   `settings/override/site.ini.append.php` der Kopie, vergewissern Sie sich, dass keine Crontab-Zeile die Kopie
   ausführt (`crontab -l`), und liefern Sie sie nur über einen lokalen Port aus.
7. Korrigieren Sie die Eigentumsrechte an den beschreibbaren Verzeichnissen der Kopie, leeren Sie jeden Cache und
   erzeugen Sie in der Kopie die Autoloads neu.
8. Starten Sie die Kopie und prüfen Sie sie: die Startseite, die Anmeldung an der Administration mit einem Testkonto,
   ein Bild und das neueste Element, von dem Sie wissen, dass es im Original existiert.
9. Finden Sie auf der Original-Website den Fehler, den Ihr Trainer erzeugt hat: Entnehmen Sie seine Referenz der
   Fehlerseite und suchen Sie die passenden Zeilen in `var/log/error.log` (oder unter **Setup > Logdateien**).
   Kopieren Sie die Zeilen ohne personenbezogene Daten in Ihre Notizen.
10. Schreiben Sie das einzeilige Wiederherstellungsprotokoll: Datum, Website, verwendete Sicherung, Dauer der
    Wiederherstellung, Ergebnis, wer sie durchgeführt hat.
11. Stoppen Sie den Server der Kopie. Lassen Sie die Kopie für die Bewertung durch den Trainer stehen; sie wird nach
    der Bewertung entfernt.

## Erwartetes Ergebnis

- Ein Sicherungsverzeichnis mit `site.db` und einem Dateiarchiv, erstellt, während die Wartung eingeschaltet war.
- Eine Kopie, die auf ihrem eigenen lokalen Port antwortet, mit `Transport=file`, ohne Crontab-Zeile, im Besitz des
  Benutzers der Teilnehmenden und mit dem neuesten Element.
- Notizen mit den Log-Zeilen des Fehlers, den Zeitangaben und dem Wiederherstellungsprotokoll.
- Der Trainer prüft die Einstellungen der Kopie, `crontab -l` und `ls -l var/` der Kopie.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Konsistente Sicherung | Datenbankdatei während der Benutzung kopiert | Online-Sicherung, Dateien fehlen | Beides, nicht in einem Zeitfenster | Online-Sicherung und Dateien in einem Wartungsfenster | Notizen, das Sicherungsverzeichnis |
| Wiederherstellung | Kopie läuft nicht | Läuft, Eigentumsrechte falsch (Dateien von root in `var/`) | Läuft, Caches oder Autoloads ausgelassen | Läuft, Eigentumsrechte, Caches, Autoloads korrekt | `ls -l var/`, die Kopie |
| Sichere Kopie (Pflicht) | Mail-Transport unverändert oder Cron führt sie aus | Entweder Mail oder Cron behandelt | Beides behandelt, über localhost hinaus ausgeliefert | Datei-Transport, kein Cron, nur lokaler Port | Einstellungen der Kopie, `crontab -l` |
| Prüfungen | Keine | Nur Startseite | Drei von vier Prüfungen | Startseite, Anmeldung, Bild, neuestes Element | Notizen |
| Fehler über die Referenz | Nicht gefunden | Falsche Zeilen | Richtige Zeilen, personenbezogene Daten kopiert | Richtige Zeilen, nichts Personenbezogenes | Notizen, das Log |
| Protokoll und Zeitmessung | Keins | Zeitangaben ohne Protokoll | Protokoll unvollständig | Vollständiges einzeiliges Protokoll mit Zeiten | Notizen |

**Bestanden**: 12 von 18 Punkten oder mehr und **Sichere Kopie** mit 2 oder 3.

## Quiz

Fragen für die Nachbesprechung nach der Bewertung.

1. Warum war die Wartung während der Schritte 2 und 3 eingeschaltet?
2. Was würde geschehen, wenn die Crontab der Kopie mit dem Standard-Mail-Transport liefe?
3. Welchen Teil Ihrer Sicherung würden Sie verschlüsseln, und warum?
4. Was beweist Ihr Wiederherstellungsprotokoll einem Kunden oder einer Prüferin?

<details>
<summary>Antworten</summary>

1. Damit die Datenbank und die Dateien denselben Zeitpunkt beschreiben
   ([Sicherungen: was gesichert wird](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md#ein-backup-hat-zwei-hälften)).
2. Benachrichtigungen, Newsletter und Jobs würden an echte Adressen laufen
   ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#ein-wiederherstellungstest-auf-einer-anderen-maschine)).
3. Das Archiv mit `settings/override/`, das Zugangsdaten enthält
   ([Sicherungen: was gesichert wird](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md#die-zu-sichernden-dateien)).
4. Dass die Sicherungen funktionieren, mit Nachweis, wann, wie lange und durch wen
   ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#ein-wiederherstellungstest-auf-einer-anderen-maschine)).

</details>

## Weiterführende Literatur

- [Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md): das
  vollständige Verfahren.
- [Datenbanksicherungen je Datenbanksystem](../../../hosting/betrieb/datenbank-backups-je-engine.md): Befehle für
  jedes Datenbanksystem.
- [Einen Fehler über seine Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md).

## Verwandte Seiten

- [Lektion 4.5: Hosting und Betrieb, schriftlicher Test](hosting-betrieb-schriftlicher-test.md)
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
