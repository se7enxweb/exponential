---
title: Upgrade-Prüfung vorher und nachher
audience: government, admin, project-manager
category: public-sector
order: 110
summary: Setup > Upgrade-Prüfung und ihr Gegenstück auf der Kommandozeile als Nachweis rund um eine Änderung nutzen - die Prüfung der Dateikonsistenz gegen das Manifest des Releases, die Prüfung der Datenbankkonsistenz, die herunterladbaren Berichte und wie Sie jeden Befund lesen.
keywords: Upgrade-Prüfung, Dateikonsistenz, Datenbankkonsistenz, checkmanifest, filelist.md5, Schemaprüfung, Nachweis, vorher und nachher, geänderte Dateien, Prüfung, Änderungsdatensatz, upgrade check, file consistency, database consistency, schema check, evidence, before and after, modified files, audit, change record
type: help
---
# Upgrade-Prüfung vorher und nachher

Diese Seite richtet sich an Administratoren, die vor und nach einer Änderung nachweisen müssen, dass Code und
Datenbank so sind, wie sie sein sollen. **Setup > Upgrade-Prüfung** (`/setup/systemupgrade`, Seitentitel
„Systemupgrade Überprüfung“) hat zwei Prüfungen, beschrieben unter [Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md).

## Inhalt
1. [Die zwei Prüfungen](#die-zwei-prüfungen)
2. [Vor einer Änderung](#vor-einer-änderung)
3. [Nach einer Änderung](#nach-einer-änderung)
4. [Die Befunde lesen](#die-befunde-lesen)
5. [Aus der Shell](#aus-der-shell)
6. [Verwandte Seiten](#verwandte-seiten)

## Die zwei Prüfungen

| Prüfung | Vergleicht | Schaltfläche | Download |
|---|---|---|---|
| Dateikonsistenz | die Dateien auf der Festplatte mit dem Manifest des Releases `share/filelist.md5` (und den Manifesten aktiver Erweiterungen, die eines haben) | **Überprüfe Dateikonsistenz** | **CSV herunterladen**, **Text herunterladen** |
| Datenbankkonsistenz | das Datenbankschema mit `share/db_schema.dba` und denen aktiver Erweiterungen | **Überprüfe Datenbankkonsistenz** | **SQL herunterladen** |

Beide lesen nur; die Seite hat keine Schaltfläche, die schreibt. Jeder Lauf und jeder Download wird im Prüfprotokoll als
`system.upgrade.run` aufgezeichnet.

## Vor einer Änderung

1. Führen Sie beide Prüfungen aus; laden Sie die Berichte herunter.
2. Als geändert gemeldete Dateien, die Sie absichtlich geändert haben: Führen Sie sie auf, denn eine Aktualisierung
   ersetzt sie.
3. Unterschiede in der Datenbank: Verstehen Sie sie jetzt, nicht während des Wartungsfensters.
4. Hängen Sie die Berichte an den Änderungsdatensatz an.

## Nach einer Änderung

1. Führen Sie beide Prüfungen erneut aus.
2. Erwartet: Die aufgeführten Dateien sind weiterhin die einzigen geänderten (oder es gibt keine, wenn Sie Ihre
   Änderungen in eine Erweiterung verlagert haben, was der bessere Ort ist); die Datenbankprüfung zeigt keine fehlende
   Tabelle.
3. Hängen Sie die Berichte an; vergleichen Sie sie mit „vorher“.

## Die Befunde lesen

| Befund | Was zu tun ist |
|---|---|
| Unerwartet geänderte Datei | herausfinden, wer sie geändert hat und warum; bei unbefugter Änderung aus dem Release wiederherstellen ([Verunstaltung](../vorfaelle/verunstaltung-der-website.md)) |
| Fehlende Datei | aus dem Release wiederherstellen |
| Fehlende Tabelle | nach einer Datensicherung ihre CREATE-Anweisung ausführen |
| Geänderte Tabelle | jede Anweisung lesen; die mit **Entfernt etwas** markierten können Daten entfernen |
| Tabelle nicht im Schema | meist eine Tabelle einer eigenen Erweiterung; lassen Sie sie |
| Tabelle einer nicht aktiven Erweiterung | dafür wird kein SQL geschrieben; aktivieren Sie die Erweiterung oder lassen Sie sie |
| „File share/filelist.md5 does not exist.“ | das Manifest des Releases kopieren, das Sie betreiben |

## Aus der Shell

```bash
php bin/php/checkmanifest.php --all                 # exit 0 when nothing is modified or missing
php bin/php/checkmanifest.php --all --extensions    # also every extension with its own manifest
php bin/php/checkmanifest.php --all --extensions --csv > report.csv
```

Der Befehl braucht keinen Kernel, keine Einstellungen und keine Datenbank, daher läuft er auch auf einer defekten
Website. Ein Exit-Code ungleich null macht ihn für die Überwachung nutzbar
([Upgrade-Prüfung als Überwachung](../ueberwachung/upgrade-pruefung-als-ueberwachung.md)).

## Verwandte Seiten
- [Konsistenz nach der Aktualisierung prüfen](../../aufgaben/betrieb/die-konsistenz-nach-einem-upgrade-pruefen.md)
- [Das Datenbankschema prüfen](../../aufgaben/betrieb/das-datenbankschema-pruefen.md)
- [Prüfung der Dateikonsistenz](../../../../funktionen/6.0/dateikonsistenzpruefung.md)
- [Überblick über Aktualisierungen für den öffentlichen Sektor](aktualisierungen-im-oeffentlichen-sektor-im-ueberblick.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
