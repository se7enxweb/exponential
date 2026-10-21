---
title: "Datei-Prüfsummen (share/filelist.md5)"
audience: designer, developer
category: theme-distribution
order: 230
summary: Liefert ein Theme share/filelist.md5 mit, vergleicht die Dateikonsistenzprüfung der Administration die installierten Dateien damit; wie Sie die Prüfsummen im selben Release aktualisieren und was passiert, wenn Sie es vergessen.
keywords: filelist.md5, Prüfsummen, Dateikonsistenzprüfung, Upgrade-Prüfung, geänderte Dateien, md5, share-Ordner, checksums, file consistency check, upgrade check, modified files, share folder
type: help
---
# Datei-Prüfsummen (share/filelist.md5)

Diese Seite ist für Designer, deren Theme eine `share/filelist.md5` mitliefert. Die meisten Themes tun das nicht; dann
erklärt diese Seite nur, warum Sie eine hinzufügen könnten.

## Wofür sie da ist

Die Upgrade-Prüfung der Administration (**Setup > Upgrade-Prüfung > Überprüfe Dateikonsistenz**) vergleicht
installierte Dateien mit ihren erfassten Prüfsummen und listet jede abweichende Datei als geändert auf. Bei einer
Erweiterung, die `share/filelist.md5` mitliefert, gehören deren Zeilen zu diesem Vergleich. So kann ein Administrator
sehen, ob jemand die Dateien des Themes auf dem Server geändert hat.

Die Prüfung ist in [Prüfung der Dateikonsistenz](../../../funktionen/6.0/dateikonsistenzpruefung.md) beschrieben.

## Die Regel

**Aktualisieren Sie die Prüfsummen im selben Release, in dem sich die Dateien ändern.** Ändern Sie 21 Dateien und nicht
ihre Prüfsummen, listet die Prüfung auf jeder Installation dieses Releases alle 21 als geändert, und Administratoren
können Ihre Änderungen nicht von Manipulation unterscheiden. Das Changelog einer Newsletter-Erweiterung hat genau
diesen Fehler und seine Korrektur im nächsten Release festgehalten.

## Schritt für Schritt in einem Release

1. Nehmen Sie Ihre Änderungen vor und committen Sie sie.
2. Erzeugen Sie die Prüfsummen der geänderten Dateien (oder aller Dateien) mit demselben Werkzeug und Format neu, das
   die Datei schon verwendet. Jede Zeile lautet `<md5>  <path>`; die Pfade stehen so, wie die Datei sie schon schreibt.
3. Setzen Sie den Versionskopf der Datei, falls sie einen hat, auf die Release-Nummer.
4. Committen Sie zusammen mit der Versionserhöhung ([Die Version vor dem Tag erhöhen](version-vor-dem-tag-erhoehen.md)).
5. Führen Sie auf einer Testinstallation die Konsistenzprüfung aus: Die Dateien Ihres Themes werden nicht als geändert
   aufgeführt.

Das Wartungsskript der Installation `bin/shell/generatefilelist.sh` kann Erweiterungen aktualisieren, die schon eine
Liste mitliefern (lesen Sie zuerst `bash bin/shell/generatefilelist.sh --help` und verwenden Sie `--dry-run`).

## Sollte Ihr Theme eine mitliefern?

| Liefern Sie eine mit, wenn | Lassen Sie sie weg, wenn |
|---|---|
| Den Administratoren Ihrer Nutzer die Erkennung von Manipulation wichtig ist | Sie oft veröffentlichen und niemand die Prüfung nutzt |
| Sie sie in jedem Release zuverlässig aktualisieren können | Eine vergessene Aktualisierung mehr Alarm auslösen würde, als sie verhindert |

## Verwandte Seiten

- [Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
