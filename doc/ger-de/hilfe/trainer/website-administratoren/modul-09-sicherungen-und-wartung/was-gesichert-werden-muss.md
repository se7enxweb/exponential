---
title: "Lektion 9.1: Was gesichert werden muss"
audience: trainer, admin
category: training
order: 91
summary: Die Teilnehmenden zählen die zwei Hälften einer Sicherung auf, finden die Dateien mit Schlüsseln, die keine Wiederherstellung neu erzeugen kann, entscheiden, was weggelassen werden kann, und schreiben den Sicherungsplan ihrer Schulungsinstallation. 40 Minuten.
keywords: website-administration, sicherung, was sichern, datenbank, var/storage, settings/override, schlüssel, audit-schlüssel, geheimnis der e-mail-einstellungen, git_manager, setup sicherung, sicherungsplan, site administration, backup, what to back up, database, keys, audit keys, mail preferences secret, backup plan
type: help
---
# Lektion 9.1: Was gesichert werden muss

Eine Sicherung, der eine einzige kleine Datei fehlt, kann wertlos sein: Stellen Sie den Inhalt ohne die
Audit-Schlüssel wieder her, lässt sich das Audit-Protokoll nicht mehr verifizieren; stellen Sie ihn ohne das Geheimnis
der E-Mail-Einstellungen wieder her, funktioniert kein bereits verschickter Abmeldelink mehr. Diese Lektion zeigt, was
eine Sicherung von Exponential enthalten muss, was sie weglassen kann und wie die beiden Hälften gemeinsam erstellt
werden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 9: Sicherungen und Wartung](README.md) |
| Dauer | 40 Minuten (20 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Support-Mitarbeiter |
| Voraussetzungen | Die Module 6 bis 8 dieses Kurses helfen; eine Schulungsinstallation mit Zugang zur Shell |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, warum eine Sicherung aus der Datenbank und den Dateien besteht, die im selben Moment erstellt werden;
- die Verzeichnisse aufzählen, die eine Sicherung von Exponential enthalten muss;
- die Schlüsseldateien nennen, die sich nicht neu erzeugen lassen, und was ohne jede von ihnen kaputtgeht;
- aufzählen, was weggelassen werden kann, weil es neu erzeugt wird;
- einen Sicherungsplan für eine Installation schreiben: was, wann, wohin, wie viele Generationen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Sicherungen: was gesichert werden muss](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md) und
[Nach der Installation, 10.9 Sicherung und Wiederherstellung](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung).

1. **Zwei Hälften** (3 Minuten). Die Datenbank enthält Inhalte, Benutzer, Rollen und Versionen; die Dateien enthalten
   Uploads, Einstellungen und eigene Erweiterungen. Eine Bildzeile ohne ihre Datei ist ein kaputtes Bild; eine Datei
   ohne ihre Zeile ist Ballast. Erstellen Sie beide im selben Moment.
2. **Die Dateien** (5 Minuten). `var/storage/` (und jedes `var/<site>/storage/`), `var/storage/sqlite3/` bei SQLite
   (mit der Online-Sicherung von SQLite, nie als einfache Kopie einer Datei, in die gerade geschrieben wird),
   `settings/override/`, `settings/siteaccess/`, eigene Erweiterungen und Designs, `config.php` und verwandte Dateien,
   sofern vorhanden, das Audit-Verzeichnis `var/<site>/log/audit/`.
3. **Schlüssel** (4 Minuten). `settings/override/audit.ini.append.php` (Signatur- und Pseudonym-Schlüssel des Audits),
   `settings/override/mailpreferences.ini.append.php` (das Website-Geheimnis der E-Mail-Einstellungen) und der
   Konfigurationsbaum von Velocity mit den Schlüsseln seiner Zertifikate. Klein, unverzichtbar, vertraulich.
4. **Weglassen** (2 Minuten). Caches, `var/autoload/`, `vendor/` (wird aus `composer.lock` neu aufgebaut), die Logs
   und PID-Dateien von Velocity, `var/log/mail/`, `var/tmp/`.
5. **Wohin sie gehört** (3 Minuten). Weg vom Server, mehrere Generationen, verschlüsselt: `settings/override/` enthält
   das Datenbankpasswort. Eine Sicherung ist so vertraulich wie der Server selbst.
6. **Aus der Administration** (3 Minuten). Wo die Erweiterung `git_manager` installiert ist, erstellt
   **Setup > Sicherung** Sicherungen der Datenbank und der Dateien mit Zeitstempel, auf Wunsch verschlüsselt. Sie
   braucht `mysqldump` und deckt daher nur Websites mit MySQL und MariaDB ab.

### Häufige Fragen

**Brauchen wir für jede Sicherung den Wartungsmodus?** Nicht für die Datenbank allein bei SQLite oder MySQL mit
InnoDB; er sorgt dafür, dass Datenbank und Dateien zueinander passen. Eine Website, die nicht jede Nacht offline gehen
kann, sichert die Datenbank im laufenden Betrieb und die Dateien direkt danach und nimmt ein kleines Zeitfenster in
Kauf.

**Gehört der Kernel in die Sicherung?** Er kommt mit dem Release und lässt sich in derselben Version erneut holen.
Sichern Sie ihn nur, wenn jemand Dateien direkt geändert hat; **Setup > Upgrade-Prüfung** listet Dateien auf, die vom
Release abweichen.

**Wie lange sollen Sicherungen aufbewahrt werden?** Das entscheidet die Organisation; siehe
[Sicherungspläne und Aufbewahrung](../../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md).

### Stolperfallen

- **Eine SQLite-Datei kopieren, in die gerade geschrieben wird.** Verwenden Sie die Online-Sicherung; eine einfache
  Kopie während eines Schreibvorgangs oder ohne ihre `-wal`-Datei kann inkonsistent sein.
- **Auf dieselbe Festplatte sichern.** Eine volle Festplatte oder ein verlorener Server nimmt die Sicherung mit.
- **Einem Kunden eine Sicherung ungeprüft übergeben.** Sie enthält Zugangsdaten.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, mit einem Terminal im Wurzelverzeichnis der Installation. In dieser
Übung wird nichts geändert.

1. Finden Sie Ihre Datenbank: `ls -l var/storage/sqlite3/` (bei SQLite), oder lesen Sie im Datenbankabschnitt von
   `settings/override/site.ini.append.php` nach, welche Datenbank verwendet wird (zeigen Sie das Passwort niemandem).
2. Messen Sie den Speicher: `du -sh var/storage var/*/storage 2>/dev/null`.
3. Prüfen Sie, welche Schlüsseldateien existieren:

   ```bash
   ls -l settings/override/audit.ini.append.php settings/override/mailpreferences.ini.append.php
   ```

4. Finden Sie das Audit-Verzeichnis: `ls -d var/*/log/audit var/log/audit 2>/dev/null`.
5. Messen Sie, was Sie weglassen werden: `du -sh var/cache var/*/cache var/autoload vendor 2>/dev/null`.
6. Läuft Ihre Installation auf Velocity, suchen Sie seinen Konfigurationsbaum, wie Ihr Trainer es zeigt.
7. Öffnen Sie in der Administration **Setup** und prüfen Sie, ob **Sicherung** in der linken Spalte steht (der Eintrag
   erscheint nur, wo `git_manager` installiert ist). Wenn ja, öffnen Sie die Seite und lesen Sie
   **Eine Sicherung erstellen** und **Vorhandene Sicherungen**; legen Sie nichts an.
8. Schreiben Sie Ihren Sicherungsplan in fünf Zeilen: Befehl für die Datenbank, Dateiliste, Schlüsseldateien, Ziel
   außerhalb des Rechners und Anzahl der aufbewahrten Generationen.
9. Vergleichen Sie Ihren Plan mit der Checkliste in
   [Sicherungen: was gesichert werden muss](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md#checkliste).

## Erwartetes Ergebnis

- Alle Teilnehmenden haben einen schriftlichen Plan, der `var/storage`, `settings/override`, `settings/siteaccess`, die
  zwei Schlüsseldateien und das Audit-Verzeichnis nennt und Caches, `var/autoload` und `vendor` weglässt.
- Der Plan speichert die Sicherung außerhalb des Schulungsrechners und bewahrt mehr als eine Generation auf.
- Der Trainer vergleicht zwei Pläne am Projektor mit der Checkliste.

## Quiz

1. Warum müssen die Datenbank und die Dateien im selben Moment gesichert werden?
2. Welche Datei enthält die Schlüssel, die das Audit-Protokoll verifizierbar machen?
   A. `settings/site.ini` B. `settings/override/audit.ini.append.php` C. `var/log/audit.log` D. `config.php`
3. Was geht kaputt, wenn `settings/override/mailpreferences.ini.append.php` verloren geht?
4. Nennen Sie drei Dinge, die eine Sicherung weglassen kann.
5. Warum muss eine Sicherung verschlüsselt und zugriffsbeschränkt sein?

<details>
<summary>Antworten</summary>

1. Damit jede Zeile ihre Datei hat und jede Datei ihre Zeile; sonst gehen Bilder kaputt oder Dateien verwaisen.
2. **B.** ([Sicherungen: was gesichert werden muss](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md#schlüssel-und-geheimnisse-die-sich-nicht-neu-erzeugen-lassen)).
3. Ein neues Geheimnis wird erzeugt: Jeder Link in bereits verschickten E-Mails funktioniert nicht mehr, und die
   Sperrliste passt nicht mehr.
4. Drei beliebige von: Caches, `var/autoload/`, `vendor/`, die Logs und PID-Dateien von Velocity, `var/log/mail/`,
   `var/tmp/`.
5. Sie enthält `settings/override/` mit dem Datenbankpasswort und weiteren Zugangsdaten.

</details>

## Weiterführende Literatur

- [Sicherungen: was gesichert werden muss](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md): die vollständige
  Liste und Checkliste.
- [Datenbanksicherungen je Datenbanksystem](../../../hosting/betrieb/datenbank-backups-je-engine.md): die Befehle
  für jede Datenbank.
- [git_manager](../../../../funktionen/6.0/erweiterungen/git_manager.md): Sicherungen aus der Administration.
- [Speicheraufbau](../../../hosting/betrieb/aufbau-des-speichers.md): wo Exponential seine Dateien ablegt.

## Verwandte Seiten

- [Modul 9: Sicherungen und Wartung](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
