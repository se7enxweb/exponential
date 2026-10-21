---
title: "Lektion 10.2: Die Upgrade-Prüfung"
audience: trainer, admin
category: training
order: 102
summary: Die Teilnehmenden führen unter Setup > Upgrade-Prüfung die Prüfungen der Datei- und Datenbankkonsistenz aus, lesen jede Art von Befund, ändern absichtlich eine Datei, um sie gemeldet zu sehen, und erhalten dieselbe Antwort in einer Shell mit checkmanifest.php. 35 Minuten.
keywords: website-administration, upgrade-prüfung, setup upgrade-prüfung, systemupgrade, dateikonsistenz prüfen, datenbankkonsistenz prüfen, filelist.md5, geändert, fehlt, checkmanifest.php, schema, site administration, upgrade check, Check file consistency, Check database consistency, Modified, Missing
type: help
---
# Lektion 10.2: Die Upgrade-Prüfung

Vor einem Upgrade muss ein Administrator wissen, ob jemand die Dateien oder die Datenbank der Installation von Hand
geändert hat: Ein Upgrade ersetzt geänderte Dateien, und eine Datenbank, die vom Schema abweicht, kann das Update
scheitern lassen. **Setup > Upgrade-Prüfung** beantwortet beide Fragen mit zwei Prüfungen, die nur lesen. Diese
Lektion behandelt die Seite, ihre Befunde und die Entsprechung auf der Kommandozeile.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 10: Upgrades, Erweiterungen und Pakete](README.md) |
| Dauer | 35 Minuten (10 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Support-Mitarbeiter |
| Voraussetzungen | [Lektion 10.1](ein-upgrade-planen-und-durchfuehren.md); ein Administratorkonto und eine Shell |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- **Überprüfe Dateikonsistenz** und **Überprüfe Datenbankkonsistenz** ausführen und die Ergebniszeile lesen;
- die Probleme (**Geändert**, **Fehlt**, **Nicht lesbar**) von den Hinweisen unterscheiden, die ein Upgrade nicht
  aufhalten;
- erklären, was vor einem Upgrade mit einer geänderten Datei zu tun ist;
- einen Bericht als CSV oder Text herunterladen;
- dieselbe Dateiprüfung mit `php bin/php/checkmanifest.php --all --extensions` ausführen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Die Upgrade-Prüfung: Dateien und Datenbank vor einem Upgrade](../../../../anleitungen/upgrade-pruefung.md). Die
beschriebene Seite ist die des Designs admin4.

1. **Der Weg hinein** (1 Minute). **Setup**, dann **Upgrade-Prüfung** (`/setup/systemupgrade`), Richtlinie
   `setup/setup`. Die Seite erinnert Sie daran, zuerst zu sichern.
2. **Die Übersicht** (2 Minuten). Die Version, die Anzahl der Dateien in `share/filelist.md5`, wann sich das Manifest
   zuletzt geändert hat, die aktiven Erweiterungen mit eigenem Manifest und das Datenbanksystem.
3. **Die Dateiprüfung** (3 Minuten). Die Ergebniszeile: *Überprüfung der Dateikonsistenz ist in Ordnung.* (grün), eine
   Warnung mit der Anzahl geänderter, fehlender und nicht lesbarer Dateien (gelb) oder eine rote Zeile, wenn die
   Prüfung nicht laufen kann. Danach die Zahlen, die gelesenen Manifeste, eine Suche und eine Gruppe pro Zustand mit
   **Was es bedeutet und was zu tun ist**.
4. **Die Datenbankprüfung** (2 Minuten). Befunde nach Tabelle gruppiert, jeweils mit dem SQL, das das Datenbanksystem
   bräuchte. Das SQL ist Text zum Lesen; nichts wird ausgeführt. Abweichungen, für die das Datenbanksystem kein SQL
   schreibt, sind Hinweise zum Datenbanksystem.
5. **Was zu tun ist** (2 Minuten). Eine geänderte Datei wird von einem Upgrade überschrieben: Verlagern Sie die
   Änderung vorher in eine Erweiterung oder ein Override, oder stellen Sie sicher, dass sie Teil des neuen Release ist.

### Häufige Fragen

**Ändert die Prüfung etwas?** Nein. Keine Datei wird geschrieben und kein SQL ausgeführt.

**Warum sehe ich Dateien, die ich absichtlich geändert habe?** Die Prüfung listet jede Datei auf, die vom Release
abweicht. Das ist zu erwarten; der Sinn ist, sie vor dem Upgrade zu kennen.

**Werden Erweiterungen geprüft?** Diejenigen mit eigener `share/filelist.md5` und die im Release mitgelieferten über
das Manifest des Release. Aktive Erweiterungen ohne beides werden als **nicht geprüft** genannt.

### Stolperfallen

- **Ein Entwicklungs-Checkout.** Eine Schulungsinstallation, die aus einem Zweig zwischen zwei Releases gebaut wurde,
  kann viele geänderte Dateien zeigen. Erklären Sie das, statt sie zu „reparieren“.
- **Vergessen, die Übung rückgängig zu machen.** Schritt 5 ändert eine Datei; Schritt 8 stellt sie wieder her.
- **Die Shell-Prüfung im falschen Verzeichnis ausführen.** Die Pfade in der Liste sind relativ zum Dokumentenstamm.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, im Browser und in einem Terminal im Wurzelverzeichnis der
Installation.

1. Öffnen Sie **Setup > Upgrade-Prüfung**. Lesen Sie **Übersicht**: Version, Dateien im Manifest, Datenbanksystem.
2. Klicken Sie auf **Überprüfe Dateikonsistenz**. Lesen Sie die Ergebniszeile und die Zahlen.
3. Öffnen Sie **Was die Dateiprüfung liest** und stellen Sie fest, welche Erweiterungen geprüft werden und welche
   **nicht geprüft** sind.
4. Klicken Sie auf **Überprüfe Datenbankkonsistenz**. Lesen Sie das Ergebnis und eventuelle Hinweise zum
   Datenbanksystem.
5. Ändern Sie absichtlich eine aufgeführte Datei: Kopieren Sie `index.php` nach `index.php.orig` und hängen Sie dann
   eine Leerzeile an das Ende von `index.php` an (`echo >> index.php`).
6. Klicken Sie erneut auf **Überprüfe Dateikonsistenz**. Suchen Sie `index.php` unter **Geändert** und lesen Sie
   **Was es bedeutet und was zu tun ist**.
7. Führen Sie dieselbe Prüfung in der Shell aus:

   ```bash
   php bin/php/checkmanifest.php --all --extensions
   ```

8. Stellen Sie die Datei wieder her: `mv index.php.orig index.php`, und führen Sie die Prüfung noch einmal aus.
9. Klicken Sie auf **Text herunterladen** und öffnen Sie den Bericht.

## Erwartetes Ergebnis

- Schritt 2 zeigt eine Ergebniszeile und Zahlen; auf einer Installation aus einem Release idealerweise
  *Überprüfung der Dateikonsistenz ist in Ordnung.*
- Schritt 6 listet `index.php` als **Geändert** auf, mit den ersten Zeichen der aufgeführten und der aktuellen
  Prüfsumme.
- Schritt 7 meldet dieselbe Datei.
- Nach Schritt 8 wird die Datei nicht mehr aufgeführt.
- Der Trainer prüft, dass bei allen Teilnehmenden `index.php` wiederhergestellt wurde (Schritt 8) und die Website
  noch antwortet.

![Die Upgrade-Prüfung: Datei- und Datenbankkonsistenz](../../../../../eng-us/help/images/trainers/sa-upgrade-check.webp)

## Quiz

1. Welcher Befund verhindert ein sicheres Upgrade?
   A. **Nicht aufgeführt** B. **Fehlerhafte Zeilen** C. **Geändert** D. **Nicht in Reihenfolge**
2. Führt **Überprüfe Datenbankkonsistenz** das SQL aus, das sie anzeigt?
3. Was geschieht bei einem Upgrade mit einer Datei, die Sie direkt geändert haben?
4. Welche Datei enthält die Prüfsummen eines Release?
5. Welcher Befehl liefert die Befunde zu den Dateien in einer Shell?

<details>
<summary>Antworten</summary>

1. **C.** Geänderte, fehlende und nicht lesbare Dateien sind Probleme; die anderen sind Hinweise
   ([Die Upgrade-Prüfung, Kurz gesagt](../../../../anleitungen/upgrade-pruefung.md#kurz-gesagt)).
2. Nein. Beide Prüfungen lesen nur; das SQL ist Text, den Sie lesen.
3. Das Upgrade ersetzt sie, und Ihre Änderung geht verloren, sofern Sie sie nicht vorher in eine Erweiterung oder ein
   Override verlagert haben.
4. `share/filelist.md5` (und die eigene `share/filelist.md5` einer Erweiterung).
5. `php bin/php/checkmanifest.php --all --extensions`.

</details>

## Weiterführende Literatur

- [Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md): jeder Teil der Seite und jeder Befund.
- [Prüfung der Dateikonsistenz und die Dateiliste des Release](../../../../../eng-us/features/6.0/file-consistency-check.md).
- [Qualitätsprüfungen](../../../../../eng-us/features/6.0/quality-checks.md).

## Verwandte Seiten

- [Modul 10: Upgrades, Erweiterungen und Pakete](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
