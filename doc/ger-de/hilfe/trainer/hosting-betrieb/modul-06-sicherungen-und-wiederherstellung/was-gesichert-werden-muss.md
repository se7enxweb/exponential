---
title: "Lektion 6.1: Was gesichert wird"
audience: trainer, hosting, admin
category: training
order: 61
summary: Die Teilnehmenden nennen die beiden Hälften einer Exponential-Sicherung, die Schlüsseldateien, die sich nicht neu erzeugen lassen, und was weggelassen wird, und erstellen dann in einem Wartungsfenster eine konsistente Sicherung von Datenbank und Dateien. 40 Minuten.
keywords: hosting und betrieb, sicherung, was gesichert wird, var/storage, settings/override, audit-schlüssel, geheimnis der e-mail-einstellungen, ssl, wartungsmodus, tar, caches ausschließen, git_manager backup, hosting and operations, backup, what to back up, audit keys, maintenance mode, exclude caches
type: help
---
# Lektion 6.1: Was gesichert wird

Eine Website, die aus einer Sicherung ohne eine Schlüsseldatei wiederhergestellt wurde, kann vollständig aussehen und
trotzdem kaputt sein: Audit-Einträge, die sich nicht mehr prüfen lassen, Links in jedem versendeten Newsletter, die
nicht mehr funktionieren, HTTPS, das auf ein Ersatzzertifikat zurückfällt. Diese Lektion vermittelt, was in eine
Sicherung einer Exponential-Website gehört, was gefahrlos weggelassen werden kann und wie man Datenbank und Dateien im
selben Moment sichert.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 6: Sicherungen und Wiederherstellung](README.md) |
| Dauer | 40 Minuten (20 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Entwickler von Sicherungsdiensten |
| Voraussetzungen | [Lektion 3.4](../modul-03-datenbanken/datenbanksicherungen-und-umzuege.md) (Datenbanksicherungen pro Datenbanksystem) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, warum eine Sicherung aus Datenbank und Dateien besteht, die im selben Moment erstellt werden;
- die zu sichernden Verzeichnisse und Dateien aufzählen, einschließlich der Schlüsseldateien, die sich nicht neu
  erzeugen lassen;
- aufzählen, was weggelassen wird, weil die Website es neu aufbaut;
- eine konsistente Sicherung im Wartungsmodus erstellen und sagen, wann der Wartungsmodus nicht nötig ist;
- erklären, warum eine Sicherung, die `settings/override/` enthält, verschlüsselt werden muss.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Sicherungen: was gesichert wird](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md) und
[Kapitel 10, Sicherungen und Wiederherstellung](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung).

1. **Zwei Hälften** (3 Minuten). Die Datenbank enthält Inhalte, Benutzer, Rollen und Versionen; die Dateien enthalten
   Uploads, Einstellungen, eigene Erweiterungen und Designs. Ein Bild, das in der Datenbank steht, aber nicht auf der
   Festplatte liegt, wird als defekt angezeigt.
2. **Die Dateien** (5 Minuten). `var/storage/` und jedes `var/<site>/storage/`; `settings/override/` und
   `settings/siteaccess/`; eigene Erweiterungen und Designs, die nicht in einem Repository liegen; `config.php` und
   zugehörige Dateien, falls vorhanden; das Verzeichnis des Audit-Protokolls; `composer.json` und `composer.lock`, wenn
   Composer die Website verwaltet.
3. **Schlüssel, die sich nicht neu erzeugen lassen** (4 Minuten). Die Signatur- und Pseudonym-Schlüssel des
   Audit-Protokolls in `settings/override/audit.ini.append.php`; das `TokenSecret` der E-Mail-Einstellungen (ein neues
   Geheimnis macht jeden Link in jeder bereits versendeten E-Mail ungültig); der Konfigurationsbaum von Velocity und
   sein Verzeichnis `ssl/`.
4. **Was weggelassen wird** (3 Minuten). Caches, `var/autoload/`, `vendor/` (aus `composer.lock` neu aufgebaut), die
   Logs und PID-Dateien von Velocity, `var/tmp/`. Die Release-Dateien lassen sich erneut herunterladen;
   **Upgrade-Prüfung** listet Dateien auf, die an Ort und Stelle geändert wurden.
5. **Zusammen** (5 Minuten). Der Wartungsmodus macht das Paar konsistent. Bei SQLite und MySQL mit InnoDB ist die
   Datenbanksicherung allein auch ohne ihn konsistent; ein Hoster, der Websites nicht jede Nacht offline nehmen kann,
   nimmt ein kleines Fenster in Kauf, in dem ein neuer Upload nur in einer Hälfte stehen kann.

### Häufige Fragen

**Können Kunden aus der Administration sichern?** Die Erweiterung `git_manager` ergänzt **Setup**, **Backup**, mit
Sicherungen mit Zeitstempel und optionaler GnuPG-Verschlüsselung. Sie braucht `mysqldump` und deckt daher Websites mit
MySQL und MariaDB ab; die eigene Sicherung jedes Datenbanksystems bleibt Aufgabe des Hosters.

**Brauche ich die Logs?** `var/log/` ist optional, nützlich, wenn eine Störung später untersucht werden muss.

**Wie viele Kopien, wie oft?** Das ist die Sicherungsrichtlinie aus Lektion 6.2.

### Stolperfallen

- **Eine aktive SQLite-Datei kopieren** statt `.backup` zu verwenden. Wiederholen Sie die Regel aus Lektion 3.4.
- **Sicherungen, die jeder lesen kann.** `settings/override/` enthält das Datenbankpasswort; beschränken und
  verschlüsseln Sie.
- **Den Speicher pro Website vergessen.** Bei einer Installation mit mehreren var-Verzeichnissen listen Sie alle auf.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, als Benutzer der Website, und bewahren Sie die Sicherung nur für die
Übung in `var/training-backup/` auf.

1. Listen Sie auf, was in Ihrer Installation vorhanden ist:
   `ls -d var/storage var/*/storage settings/override settings/siteaccess`.
2. Prüfen Sie, ob die Schlüsseldateien vorhanden sind: `ls settings/override/ | grep -E 'audit|mailpreferences'`
   (notieren Sie, welche es auf Ihrer Installation gibt).
3. Prüfen Sie die Größe dessen, was Sie weglassen: `du -sh var/cache var/autoload vendor 2>/dev/null`.
4. Schalten Sie den Wartungsmodus ein: `php bin/php/maintenance.php on --message="Backup practice" --until=15m`.
5. Sichern Sie die Datenbank mit dem Werkzeug ihres Datenbanksystems (SQLite: der Befehl `.backup` aus Lektion 3.4) in
   `var/training-backup/`.
6. Packen Sie die Dateien:

   ```bash
   tar czf var/training-backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
   ```

7. Schalten Sie den Wartungsmodus aus: `php bin/php/maintenance.php off`.
8. Listen Sie den Inhalt des Archivs auf und prüfen Sie, dass die Schlüsseldateien aus Schritt 2 darin sind:
   `tar tzf var/training-backup/files-<date>.tar.gz | grep -E 'audit|mailpreferences'`.
9. Beschränken Sie die Sicherung: `chmod 600 var/training-backup/*` und prüfen Sie mit `ls -l`.
10. Schreiben Sie eine Sicherungsliste für eine Kunden-Website in einem Absatz: was enthalten ist, was ausgeschlossen
    ist und warum.

## Erwartetes Ergebnis

- Das Sicherungsverzeichnis enthält eine Datenbankkopie und ein Dateiarchiv, die in einem Fenster erstellt wurden.
- Schritt 8 listet die Schlüsseldateien auf, die es auf der Installation gibt.
- Schritt 9: Nur der Eigentümer kann die Sicherungen lesen.
- Die Liste aus Schritt 10 nennt `var/storage`, `settings/override`, `settings/siteaccess`, die Schlüsseldateien und die
  ausgeschlossenen Caches.
- Der Trainer prüft die Archivliste einer Person am Beamer, ohne Dateiinhalte zu zeigen.

![Sicherungen in der Administration](../../../../../eng-us/help/images/trainers/ho-backups.webp)

## Quiz

1. Warum müssen Datenbank und Dateien im selben Moment gesichert werden?
2. Welche Datei macht, wenn sie verloren geht, jeden Link in jeder bereits versendeten E-Mail ungültig?
   A. `site.ini.append.php` B. das `TokenSecret` der E-Mail-Einstellungen C. `var/autoload` D. eine Cache-Datei
3. Nennen Sie drei Dinge, die in einer Sicherung weggelassen werden.
4. Warum muss eine Sicherung, die `settings/override/` enthält, verschlüsselt und beschränkt werden?
5. Wann ist der Wartungsmodus für eine konsistente Datenbanksicherung nicht nötig?

<details>
<summary>Antworten</summary>

1. Ein Bild, das in der Datenbank steht, aber nicht auf der Festplatte liegt, wird als defekt angezeigt; eine Datei
   ohne ihre Datenbankzeile ist Ballast.
2. **B.** Es steht in `settings/override/mailpreferences.ini.append.php`
   ([Sicherungen: was gesichert wird](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md#schlüssel-und-geheimnisse-die-sich-nicht-neu-erzeugen-lassen)).
3. Drei beliebige von: Caches, `var/autoload/`, `vendor/`, die Logs und PID-Dateien von Velocity, `var/tmp/`.
4. Sie enthält das Datenbankpasswort und andere Zugangsdaten; die Sicherung ist so schützenswert wie der Server.
5. Bei SQLite und MySQL mit InnoDB ist die Datenbanksicherung allein konsistent; der Wartungsmodus sorgt dafür, dass
   Datenbank und Dateien übereinstimmen.

</details>

## Weiterführende Informationen

- [Sicherungen: was gesichert wird](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)
- [Datenbanksicherungen pro Datenbanksystem](../../../hosting/betrieb/datenbank-backups-je-engine.md)
- [git_manager](../../../../../eng-us/features/6.0/extensions/git_manager.md): Sicherungen aus der Administration.
- [Speicheraufbau](../../../hosting/betrieb/aufbau-des-speichers.md): wo jede Website ihre Dateien ablegt.

## Verwandte Seiten

- [Modul 6: Sicherungen und Wiederherstellung](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
