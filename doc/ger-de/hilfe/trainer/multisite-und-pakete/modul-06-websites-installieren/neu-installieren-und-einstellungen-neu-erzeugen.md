---
title: "Lektion 6.4: Neu installieren, zurücksetzen und Einstellungen neu erzeugen"
audience: trainer, admin, developer
category: training
order: 64
summary: Die Teilnehmenden wählen zwischen DatabaseAction remove, ignore und skip, sichern vor einer Neuinstallation, setzen eine Schulungswebsite mit derselben kickstart.ini zurück, erzeugen die Einstellungen neu und behalten dabei die Daten und erholen sich von einem fehlgeschlagenen Lauf und seiner Wartungsseite. 45 Minuten.
keywords: multisite und seitenpakete, neuinstallation, einstellungen neu erzeugen, sicherung, wartungsmodus, übungsumgebung zurücksetzen, multisite and site packages, DatabaseAction, remove, ignore, skip, --db-action, --force, reinstall, regenerate settings, backup, sqlite3 .backup, maintenance mode, maintenance.php off, kickstart.ini.before-exp-install, reset a lab
type: help
---
# Lektion 6.4: Neu installieren, zurücksetzen und Einstellungen neu erzeugen

Eine Installation ist nicht immer die erste. Übungsumgebungen werden zurückgesetzt, Testsysteme neu aufgebaut, und
manchmal sollen nur die Einstellungen neu geschrieben werden, während die Inhalte bleiben. Ein Schlüssel entscheidet,
was mit den Daten geschieht: `DatabaseAction` (`--db-action` bei `exp:install`). Diese Lektion vermittelt die drei
Werte, die Sicherung, die vor dem zerstörerischen kommen muss, und wie man aus einem fehlgeschlagenen Lauf herauskommt.

> **Hinweis**
> `DatabaseAction=remove` zerstört jede Zeile in der Datenbank, die es nennt, und nichts fragt nach: `--force` ist die
> einzige Bestätigung. Üben Sie nur auf den Installationen der Lektionen 6.2 und 6.3.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 6: Websites mit dem Kickstarter und exp:install installieren](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren, Entwickler und Hoster |
| Voraussetzungen | [Lektion 6.3](der-kickstarter-und-kickstart-ini.md): eine mit dem Kickstarter angelegte Installation und ihre beiseitegelegte `kickstart.ini` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `remove`, `ignore` und `skip` erklären und den richtigen Wert wählen;
- eine SQLite-Datenbank vor einer Neuinstallation konsistent sichern, außerhalb des Wurzelverzeichnisses der
  Installation;
- eine Website in ihren installierten Zustand zurücksetzen, indem dieselbe `kickstart.ini` erneut ausgeführt wird;
- die Einstellungen neu erzeugen, ohne die Daten anzufassen, und von Hand vorgenommene Änderungen wieder einarbeiten;
- den Wartungsmodus eines fehlgeschlagenen Laufs beenden, nachdem seine Ursache behoben ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [6.5 DatabaseAction](../../../../installation/06-kickstarter.md#65-databaseaction-lesen-sie-dies-bevor-sie-run-ausführen),
[6.10 Erneut ausführen und fortsetzen](../../../../installation/06-kickstarter.md#610-erneut-ausführen-und-fortsetzen) und
[7.9 Beispiele](../../../../installation/07-installation-ueber-die-konsole.md#über-eine-vorhandene-installation-neu-installieren).

1. **Die drei Werte** (5 Minuten):

   | Wert | Datenbank | Einstellungen | Verwenden für |
   |---|---|---|---|
   | `remove` | vorhandene Tabellen gelöscht (SQLite: Datei geleert), dann installiert | geschrieben | eine saubere Installation, eine Neuinstallation |
   | `ignore` | zu Vorhandenem hinzugefügt, vorhandene Tabellen übersprungen | geschrieben | eine neue, leere Datenbank (die Voreinstellung von `ini --yes`) |
   | `skip` | nichts eingefügt, der Administrator unverändert | geschrieben | `settings/siteaccess/` und `settings/override/` neu erzeugen |

   Ein fehlender oder falsch geschriebener Wert verhält sich wie `ignore`, das bei einer Datenbank, die nicht leer
   ist, fehlschlägt. Schreiben Sie ihn aus. `exp:install` hat als Voreinstellung `--db-action=remove`.
2. **Die Sicherung** (4 Minuten). In ein Verzeichnis außerhalb des Wurzelverzeichnisses der Installation: Ein Dump im
   Document Root kann von jedem heruntergeladen werden, der seinen Namen errät. Für SQLite liefert
   `sqlite3 <file> ".backup '<target>'"` eine konsistente Kopie, selbst während die Website schreibt. Prüfen Sie vor
   dem Lauf, dass die Sicherung nicht leer ist.
3. **Einstellungen neu erzeugen** (3 Minuten). `skip` schreibt die Einstellungen aus der Datei oder den Optionen neu.
   Von Hand vorgenommene Änderungen in `settings/override/` und `settings/siteaccess/` werden ersetzt, kopieren Sie sie
   deshalb vorher beiseite und arbeiten Sie sie wieder ein. Stellen Sie nach einer Neuinstallation nie alte
   Einstellungen im Ganzen wieder her: Knoten-IDs darin passen möglicherweise nicht mehr.
4. **Ein fehlgeschlagener Lauf** (3 Minuten). Die Website bleibt im Wartungsmodus ("The site stays in maintenance
   mode. After fixing the cause: run the kickstarter again, or php bin/php/maintenance.php off"). Ein vor dem Lauf
   geöffnetes Wartungsfenster bleibt offen. Nach einem `exp:install`, das vor seinem Aufräumen abgebrochen wurde,
   liegt die frühere `kickstart.ini` in `var/log/kickstart.ini.before-exp-install-<date>`.

### Häufige Fragen

**Kann ich einen fehlgeschlagenen Lauf beim fehlgeschlagenen Schritt fortsetzen?** Nein; führen Sie nach dem Beheben
der Ursache die ganze Abfolge erneut aus. Mit `remove` ersetzt ein vollständiger zweiter Lauf alles, was der
fehlgeschlagene hinterlassen hat.

**Wie setze ich eine Übungsumgebung am schnellsten zurück?** Dieselbe `kickstart.ini` mit `DatabaseAction=remove` und
`run --force` oder derselbe Befehl `exp:install --force`. Lektion 2.6 von Train the Trainer behandelt das
Zurücksetzen von Übungsumgebungen.

**Ich habe die Datenbank-Engine gewechselt, und die Website spricht noch mit der alten.** Einstellungen in
`settings/override/` gewinnen; setzen Sie dort zuerst `[DatabaseSettings] DatabaseImplementation` auf den neuen
Treiber oder entfernen Sie den alten Wert.

**Behält `skip` meine Inhalte und Benutzer?** Ja: Nichts wird eingefügt, und der Administrator wird nicht angefasst.

### Stolperfallen

- **Die falsche Datenbank.** `--from-installed` oder eine kopierte Datei kann auf eine Live-Datenbank zeigen. Lesen
  Sie `Database` zweimal.
- **Eine Sicherung in `var/`.** Das liegt innerhalb der Installation; verwenden Sie ein Verzeichnis außerhalb.
- **Blind eingearbeitete Einstellungen.** Arbeiten Sie nach `skip` nur die von Hand vorgenommenen Änderungen wieder
  ein, Block für Block.

## Praktische Übung

Verwenden Sie die Installation aus Lektion 6.3 (`kickstart-course`) und ihre `kickstart.ini`, die Sie beiseitegelegt
haben.

1. Ermitteln Sie die Datenbankdatei: `php bin/php/console exp:ini get site.ini/DatabaseSettings/Database`, dann
   `ls -l var/storage/sqlite3/`.
2. Legen Sie in der Administration dieser Website einen Ordner `Before reset` an und veröffentlichen Sie ihn.
3. Sichern Sie die Datenbank außerhalb der Installation (ersetzen Sie die Pfade):

   ```bash
   mkdir -p ~/course-backups
   sqlite3 var/storage/sqlite3/exponential.db ".backup '$HOME/course-backups/before-reset.sqlite'"
   ls -l ~/course-backups/
   ```

4. Legen Sie `kickstart.ini` zurück in das Wurzelverzeichnis der Installation und setzen Sie in `[site_details]`
   `DatabaseAction=remove`.
5. Führen Sie `php bin/php/console exp:kickstarter run --dry-run` aus, dann `run --force`.
6. Prüfen Sie den Inhaltsbaum: Der Ordner *Before reset* ist verschwunden; die Website ist wie frisch installiert.
   Notieren Sie das neue Administratorpasswort aus der Zusammenfassung.
7. Fügen Sie eine von Hand vorgenommene Einstellung hinzu:
   `php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Hand-made name" global`.
8. Erzeugen Sie die Einstellungen neu und behalten Sie die Daten: Setzen Sie `DatabaseAction=skip` und führen Sie
   erneut `run --force` aus.
9. Führen Sie `exp:ini where site.ini/SiteSettings/SiteName` aus: Die von Hand geschriebene Zeile ist aus dem globalen
   Override verschwunden. Suchen Sie die Sicherung des Overrides aus Schritt 7 in `var/backup/ini/` und setzen Sie den
   Wert erneut von Hand.
10. Verschieben Sie `kickstart.ini` wieder aus dem Wurzelverzeichnis der Installation.

## Erwartetes Ergebnis

- Schritt 3: eine Sicherungsdatei von mehr als ein paar Bytes außerhalb der Installation.
- Schritt 6: Der Inhaltsbaum ist der einer frischen Installation, ohne *Before reset*; ein neues Passwort wurde
  erzeugt.
- Schritt 9: Der von Hand gesetzte `SiteName` ist verschwunden, bis er erneut gesetzt wird; die Inhalte wurden vom
  Lauf mit `skip` nicht angefasst.
- Der Trainer prüft Schritt 3 (Größe der Sicherung), bevor jemand Schritt 5 ausführt.

## Quiz

1. Welche `DatabaseAction` leert zuerst die Datenbank?
   A. `ignore` B. `skip` C. `remove` D. keine
2. Was ist die Voreinstellung von `--db-action` bei `exp:install`?
3. Warum darf eine Sicherung nicht im Document Root der Installation liegen?
4. Welcher Wert schreibt nur die Einstellungen?
5. Ein Lauf ist fehlgeschlagen, und die Website zeigt die Wartungsseite. Welche zwei Auswege gibt es, nachdem die
   Ursache behoben ist?

<details>
<summary>Antworten</summary>

1. **C.**
2. `remove`.
3. Jeder, der ihren Namen errät, könnte sie herunterladen
   ([6.5](../../../../installation/06-kickstarter.md#65-databaseaction-lesen-sie-dies-bevor-sie-run-ausführen)).
4. `skip`.
5. Den Kickstarter (oder `exp:install`) erneut ausführen oder `php bin/php/maintenance.php off`.

</details>

## Weiterführende Informationen

- [6. Der Kickstarter](../../../../installation/06-kickstarter.md): `DatabaseAction`, erneutes Ausführen, Logs.
- [7. Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md#711-was-schiefgehen-kann).
- [Wartungsmodus](../../../../../eng-us/features/6.0/maintenance-mode.md).
- [Sicherungen: was gesichert werden muss](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md).
- Train the Trainer: [Die Übungsumgebung zwischen den Sitzungen zurücksetzen](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/resetting-the-lab.md).

## Verwandte Seiten

- [Modul 6: Websites mit dem Kickstarter und exp:install installieren](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
