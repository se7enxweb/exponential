---
title: Eine Ausstiegsübung durchführen
audience: government, admin, project-manager
category: public-sector
order: 200
summary: Schritt für Schritt einmal im Jahr testen, dass eine Behörde wirklich gehen kann - die ganze Site mit einem Archiv von xrowextract exportieren, den Speicher kopieren, einen Dump der Datenbank erstellen, das Archiv gegen die Zahlen der Administration prüfen und das Ergebnis festhalten - auf einer Kopie, nicht in der Produktion.
keywords: Ausstiegsübung, Test des Exports, xrowextract, Archiv der Site, Manifest, Datenbank-Dump, Speicher, Prüfung, Anbieterbindung, Aufgabe zur Beschaffung, exit drill, export test, site archive, manifest, database dump, storage, verification, lock-in, procurement task
type: help
---
# Eine Ausstiegsübung durchführen

**Wer:** der Site-Administrator, beobachtet vom Vertragsverantwortlichen. **Wann:** einmal im Jahr und vor einer
Vertragsverlängerung. Die Befehle stammen aus
[Von Exponential weg migrieren](../../../../installation/migration/exponential-verlassen.md) und der Lektion für Trainer
[Exponential verlassen](../../../../../eng-us/help/trainers/migration/module-08-leaving-and-capstone/leaving-exponential.md).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Nachweis](#nachweis)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Eine Kopie der Site (Staging oder eine wiederhergestellte Sicherung), nicht die Produktion.
- Shell-Zugang und ein Arbeitsverzeichnis, zum Beispiel `var/tmp/export/`.
- Die Zahlen der Objekte der Site je Klasse aus der Administration (**Setup > Klassen**).

## Schritte

1. Legen Sie das Arbeitsverzeichnis an:

   ```bash
   mkdir -p var/tmp/export
   ```

2. Erstellen Sie ein Archiv der Site mit jeder Klasse, als ZIP:

   ```bash
   php bin/php/console ext:xrowextract:archive --set=everything --format=zip --output=var/tmp/export/
   ```

3. Öffnen Sie `manifest.json` und `README.txt` des Archivs; wählen Sie Klassen und vergleichen Sie die Zahl der Zeilen
   mit den Zahlen der Administration (eine Zeile je Objekt und Sprache).
4. Prüfen Sie, dass kein Passwort-Hash im Archiv ist; das Archiv enthält sie bauartbedingt nie.
5. Kopieren Sie das Speicherverzeichnis `var/<site>/storage/` neben das Archiv.
6. Erstellen Sie einen Dump der Datenbank mit dem Werkzeug der Engine
   ([Datenbanksicherungen je Engine](../../../hosting/betrieb/datenbank-backups-je-engine.md)).
7. Wenn die Site Layouts verwendet:

   ```bash
   php bin/php/console exp:layouts export --all --output=var/tmp/export/layouts.zip
   ```

8. Exportieren Sie Formulareinsendungen, die Sie aufbewahren müssen, und die Einwilligungsnachweise als CSV.
9. Öffnen Sie eine Stichprobe von Dateien und Rich Text aus dem Archiv mit gewöhnlichen Werkzeugen (einer
   Tabellenkalkulation, einem Texteditor).
10. Entfernen Sie den Export, wenn Sie fertig sind, oder bewahren Sie ihn nach Ihren Regeln für Schriftgut und
    Datenschutz auf; er enthält personenbezogene Daten.

## Nachweis

```text
Exit drill - <site> - <date> - <names>
Archive: <file>, classes <n>, counts matched: yes/no (differences: <...>)
Storage copied: <size>     Database dump: <engine, size>     Layouts: <yes/no/none>
Forms / consent exported: <...>     Time taken: <...>
Problems and fixes: <...>     Exit plan updated: yes/no
```

## Ergebnis

Der Nachweis, dass Ihre Daten in offenen Formaten gehen können, und eine gemessene Zeit für den Ausstiegsplan.

## Fallstricke

- Sie während der Bürozeiten in der Produktion durchführen.
- Das Speicherverzeichnis vergessen: Das Archiv verweist auf Dateien.
- Den Export herumliegen lassen: Er enthält personenbezogene Daten.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Einen Ausstiegsplan schreiben](einen-ausstiegsplan-schreiben.md)
- [Formate für den Datenexport](../../../../../eng-us/help/government/procurement/exit-plans/data-export-formats.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
