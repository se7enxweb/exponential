---
title: "Lektion 1.5: Eine Prüfungssitzung durchführen"
audience: trainer
category: training
order: 15
summary: Die Prüfung am Tag selbst durchführen, identische Schulungsinstallationen vorbereiten, die Teilnehmenden über die Regeln informieren, die Zeit im Blick behalten, Nachweise sicher sammeln, mit einer defekten Installation umgehen, ohne die Teilnehmenden zu benachteiligen, und danach Testkonten und Daten aufräumen. 40 Minuten.
keywords: prüfungen und praktische übungen, prüfungstag, vorbereitung der übungsumgebung, masterkopie, zurücksetzen, nachweis, zeitnahme, defekte installation, wiederholung, aufräumen, datei-transport, assessments and practical exercises, assessment day, lab preparation, golden copy, reset, evidence, broken installation, retake, cleanup, file transport
type: help
---
# Lektion 1.5: Eine Prüfungssitzung durchführen

Auch ein gut geschriebener Test kann am Tag selbst unfair sein: Die Installation einer Teilnehmerin ist defekt, bei
einem anderen begann die Zeit zu spät, ein Dritter sendet versehentlich eine echte E-Mail. Diese Lektion ist die
Checkliste für die Sitzung selbst.

> **Hinweis**
> Praktische Aufgaben laufen immer auf Schulungsinstallationen, niemals auf einer Live-Website. Stellen Sie vor der
> Sitzung auf jeder Schulungsinstallation den Mail-Transport auf Dateien um ([Mail: der Datei-Transport](../../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand)).

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 1: Prüfungen gestalten](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, die Prüfungen durchführen |
| Voraussetzungen | [Lektion 1.3](bewertungsraster-erstellen.md), [Die Übungsumgebung zurücksetzen](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-uebungsumgebung-zuruecksetzen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- identische Schulungsinstallationen aus einer Masterkopie oder mit einem Installationsbefehl vorbereiten;
- die Teilnehmenden über die Regeln, die Zeit und die erlaubten Hilfsmittel informieren;
- Nachweise für das Bewertungsraster sammeln, ohne personenbezogene Daten zu sammeln;
- fair entscheiden, was geschieht, wenn eine Installation während der Aufgabe ausfällt;
- nach der Sitzung aufräumen.

## Hinweise für Trainer

### So präsentieren Sie es

1. **Am Vortag** (4 Minuten).
   - Bauen Sie die Installation jeder Teilnehmerin und jedes Teilnehmers auf dieselbe Weise auf, mit derselben Version
     und demselben Seitenpaket, wie in
     [Eine Installation pro Teilnehmer](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/eine-installation-pro-teilnehmer.md),
     oder setzen Sie sie aus einer Masterkopie zurück ([Die Übungsumgebung zurücksetzen](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-uebungsumgebung-zuruecksetzen.md)).
   - Setzen Sie `[MailSettings] Transport=file` in `settings/override/site.ini.append.php`; eine E-Mail wird dann nach
     `var/log/mail/` geschrieben, statt gesendet zu werden. Setzen Sie für Newsletter-Aufgaben außerdem jedes
     `TransportMethod*` in `cjw_newsletter.ini` auf `file`; das mitgelieferte `TransportMethodPreview` ist `sendmail`
     ([Newsletter (cjw_newsletter)](../../../../installation/10-nach-der-installation.md#newsletter-cjw_newsletter)).
   - Installieren Sie auf Schulungsrechnern keine Crontab; die Teilnehmenden führen Cronjob-Teile von Hand aus, wenn
     eine Aufgabe es verlangt.
   - Legen Sie die Teilnehmerkonten mit Adressen unter `example.invalid` an.
   - Erledigen Sie die Aufgabe selbst auf einer Installation und stoppen Sie die Zeit.
2. **Die Einweisung** (3 Minuten). Lesen Sie die erste Zeile des Bogens vor („die eigene Prüfung des Trainers, keine
   offizielle Zertifizierung“), die Zeit, was geöffnet sein darf (die Hilfe, Notizen), ob die Teilnehmenden sprechen
   dürfen und wie sie den Trainer rufen.
3. **Währenddessen** (4 Minuten). Schreiben Sie die Startzeit auf den Bogen jeder Teilnehmerin und jedes Teilnehmers.
   Gehen Sie herum, helfen Sie nicht bei der Aufgabe, helfen Sie aber beim Drumherum: ein eingefrorener Browser, ein
   vergessenes Passwort des Schulungskontos.
4. **Wenn eine Installation ausfällt** (2 Minuten). Liegt der Fehler in der Übungsumgebung und nicht in der Arbeit der
   Teilnehmenden, halten Sie deren Zeit an, setzen Sie die Installation zurück, stellen Sie den Fortschritt wenn
   möglich wieder her und geben Sie die verlorene Zeit zurück. Hat die Teilnehmerin sie im Rahmen der Aufgabe selbst
   beschädigt, ist das ein Nachweis für das Bewertungsraster.
5. **Danach** (2 Minuten). Bewerten Sie mit dem Bewertungsraster, solange die Installationen noch existieren. Halten
   Sie dann die Server an, entfernen Sie Teilnehmerkonten auf gemeinsam genutzten Websites und löschen Sie die
   Verzeichnisse der Übungsumgebung
   ([Sicherheit und Datenschutz im Unterricht](../../train-the-trainer/modul-04-durchfuehren/sicherheit-und-datenschutz-im-kurs.md)).

### Häufige Fragen

**Dürfen die Teilnehmenden eigene Laptops verwenden?** Ja, wenn auf jedem dieselbe Installation läuft; sonst verwenden
Sie die Rechner der Übungsumgebung. Unterschiedliche Umgebungen ergeben unterschiedliche Aufgaben.

**Was zählt als Nachweis?** Was die Spalte Nachweis des Bewertungsrasters nennt: die Seite eines Elements, eine Datei
in `var/log/mail/`, die Ausgabe eines Befehls. Machen Sie Bildschirmfotos nur mit Erlaubnis der Teilnehmenden und nur
von der Schulungsinstallation.

**Wie laufen Wiederholungen ab?** Wie in den Regeln festgelegt: meist eine neue praktische Aufgabe derselben Art auf
einer frischen Installation, an einem anderen Tag.

### Stolperfallen

- **Eine Installation für die ganze Gruppe.** Die Teilnehmenden überschreiben gegenseitig ihre Arbeit. Verwenden Sie
  eine Installation pro Teilnehmer oder einen Ordner und ein Konto pro Teilnehmer mit Rollen, die sie voneinander
  trennen.
- **Die Crontab einer kopierten Website.** Eine wiederhergestellte Kopie einer echten Website mit laufendem Cron sendet
  echte E-Mails. Verwenden Sie für eine Prüfung niemals eine Kopie einer Live-Website mit echten Adressen.
- **Tage später bewerten.** Die Installation wurde zurückgesetzt, und die Nachweise sind weg.

## Praktische Übung

Bereiten Sie eine Prüfungssitzung auf Ihrer eigenen Schulungsinstallation vor.

1. Prüfen Sie den Mail-Transport der Installation:

   ```bash
   ./console exp:ini get site.ini/MailSettings/Transport --allow-root-user
   ```

   Ist er nicht `file`, setzen Sie ihn in `settings/override/site.ini.append.php` und leeren Sie den INI-Cache.
2. Legen Sie in der Gruppe, die Ihre Aufgabe braucht, ein Teilnehmerkonto unter `example.invalid` an.
3. Schreiben Sie den Text der Einweisung: Zeile zum Aussteller, Zeit, was geöffnet sein darf, wie man Sie ruft.
4. Erstellen Sie eine Masterkopie des vorbereiteten Zustands, wie in
   [Die Übungsumgebung zurücksetzen](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-uebungsumgebung-zuruecksetzen.md)
   beschrieben.
5. Erledigen Sie die Aufgabe selbst und stoppen Sie die Zeit.
6. Beschädigen Sie die Installation absichtlich (halten Sie zum Beispiel den Server an) und setzen Sie sie aus der
   Masterkopie zurück. Stoppen Sie die Zeit des Zurücksetzens.
7. Schreiben Sie die Regel auf, die Sie anwenden, wenn die Installation einer Teilnehmerin ausfällt.

## Erwartetes Ergebnis

- Der Mail-Transport ist `file`, geprüft mit dem Befehl.
- Ein Text für die Einweisung, eine Masterkopie, die Dauer der Aufgabe und die Dauer eines Zurücksetzens.
- Eine schriftliche Regel für ausgefallene Installationen, die die verlorene Zeit zurückgibt, wenn der Fehler bei der
  Übungsumgebung liegt.
- Der Trainer bittet eine Teilnehmerin, ihre Einweisung vorzulesen.

## Quiz

1. Welche Mail-Einstellung prüfen Sie vor einer Sitzung auf jeder Schulungsinstallation?
2. Warum installieren Sie auf Schulungsrechnern keine Crontab?
3. Die Installation einer Teilnehmerin antwortet nicht mehr, weil auf dem Rechner der Übungsumgebung der Speicherplatz
   ausgegangen ist. Was tun Sie?
4. Wann bewerten Sie, und warum dann?
5. Was entfernen Sie nach der Sitzung?

<details>
<summary>Antworten</summary>

1. `[MailSettings] Transport`, das `file` sein sollte
   ([10.4](../../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand)); bei
   Newsletter-Aufgaben außerdem die Schlüssel `TransportMethod*` in `cjw_newsletter.ini`.
2. Cron führt Benachrichtigungen, Newsletter und Jobs von selbst aus; auf einer Kopie mit echten Adressen sendet er
   echte E-Mails
   ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#ein-wiederherstellungstest-auf-einer-anderen-maschine)).
3. Halten Sie die Zeit der Teilnehmerin an, setzen Sie die Installation zurück, stellen Sie den Fortschritt wenn möglich
   wieder her und geben Sie die verlorene Zeit zurück.
4. Direkt nach der Aufgabe, solange die Installationen und die Nachweise noch existieren.
5. Teilnehmerkonten auf gemeinsam genutzten Websites, Verzeichnisse der Übungsumgebung und laufende Server
   ([Sicherheit und Datenschutz im Unterricht](../../train-the-trainer/modul-04-durchfuehren/sicherheit-und-datenschutz-im-kurs.md)).

</details>

## Weiterführende Literatur

- [Kurzkarte für die Übungsumgebung](../../kurzkarte-fuer-die-uebungsumgebung.md): die Befehle zum Aufbauen, Bereitstellen und Zurücksetzen
  einer Übungsumgebung.
- [Die Übungsumgebung zurücksetzen](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-uebungsumgebung-zuruecksetzen.md):
  Masterkopien und frische Installationen.
- [Fehlerbehebung im Unterricht](../../train-the-trainer/modul-04-durchfuehren/fehlersuche-im-kurs.md): wenn
  während der Sitzung etwas ausfällt.

## Verwandte Seiten

- [Modul 1: Prüfungen gestalten](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
