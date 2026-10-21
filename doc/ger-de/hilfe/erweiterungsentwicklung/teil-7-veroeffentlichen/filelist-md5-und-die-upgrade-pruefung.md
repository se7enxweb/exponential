---
title: filelist.md5 und die Upgrade-Prüfung
audience: developer
category: extension-development
order: 90
summary: Erweiterungen, die share/filelist.md5 mitliefern, werden von der Prüfung der Dateikonsistenz in der Administration gelesen; jedes Release muss die Prüfsummen geänderter Dateien aktualisieren, sonst listet die Prüfung sie als geändert auf. Wie Sie sie richtig halten.
keywords: filelist.md5, Prüfsummen, Dateikonsistenz, Upgrade-Prüfung, Manifest, Release, checksums, file consistency check, upgrade check, share, manifest, release, cjw_newsletter, md5sum
type: help
---
# filelist.md5 und die Upgrade-Prüfung

Teil 7, Kapitel 9. Manche Erweiterungen liefern eine Liste ihrer Dateien mit einer Prüfsumme für jede mit:
`share/filelist.md5`. Die Upgrade-Prüfung der Administration vergleicht die Dateien einer Installation damit und
listet jede abweichende Datei als „geändert“ auf. Das ist nützlich, um lokale Änderungen zu finden, und irreführend,
wenn die Liste selbst veraltet ist. Dieses Kapitel ist für Maintainer solcher Erweiterungen. Wenn Ihre keine
`share/filelist.md5` hat, können Sie es überspringen.

## Was die Prüfung tut

In der Administration liest die Upgrade-Prüfung (Setup, die Seite „Systemupgrade Überprüfung“, **Überprüfe
Dateikonsistenz**) die `share/filelist.md5` des Kernels und die der Erweiterungen, die eine mitliefern, berechnet die
Prüfsumme jeder aufgeführten Datei und listet die Dateien auf, deren Prüfsumme abweicht. Ein Administrator der Website
liest diese Liste als „Dateien, die jemand auf diesem Server geändert hat“.

## Warum ein Release sie aktualisieren muss

Wenn ein Release Dateien ändert, aber nicht deren Zeilen in `share/filelist.md5`, meldet jede Installation dieses
Releases diese Dateien als geändert, obwohl niemand sie angefasst hat. Die Spezifikation der Extension-Metadaten hält
den Fall von cjw_newsletter fest:

| Release | Was geschah |
|---|---|
| 4.1.4 | 21 Dateien geändert, ohne das Manifest zu aktualisieren; die Prüfung listete alle auf |
| 4.1.5 | das Manifest korrigiert |
| 4.1.14 | dem Manifest fehlten 33 Dateien unter `classes/runnable/` |
| 4.1.15 | das Manifest listet alle 309 versionierten Dateien |

## So halten Sie sie richtig

Die Datei von cjw_newsletter beginnt mit einem kurzen Kopf und hat dann eine Zeile je Datei: die MD5-Prüfsumme, zwei
Leerzeichen, den Pfad relativ zur Erweiterung.

```text
name: cjw_newsletter
version: 4.2.12
files_count: 697

a65c763ef0340e232cfe8c274f56fca0  .github/FUNDING.yml
aa9ea80af84e6cba65e0764a80b56e16  .gitignore
```

Erzeugen Sie vor jedem Release die Zeilen aller versionierten Dateien neu und vergleichen Sie:

```bash
cd extension/myext
git ls-files -z -- . ':!share/filelist.md5' | xargs -0 -r md5sum > ../myext-filelist.new
diff <( grep -E '^[0-9a-f]{32}  ' share/filelist.md5 | sort -k2 ) <( sort -k2 ../myext-filelist.new )
```

(Das `grep -E` behält nur die Zeilen mit Prüfsummen und lässt den Kopf weg.) Jeder Unterschied ist eine Zeile, die Sie
aktualisieren müssen. Schreiben Sie die neue Liste, behalten Sie den Kopf und aktualisieren Sie dessen `version` und
`files_count` mit dem Release ([ezinfo.php und extension.xml anheben](ezinfo-und-extension-xml-anheben.md)). Löschen
Sie die temporäre Datei danach; sie wird außerhalb der Erweiterung geschrieben, damit sie nicht versehentlich
committet werden kann.

Nehmen Sie jede versionierte Datei auf, die die Erweiterung installiert. Lassen Sie Dateien weg, die nicht installiert
werden (Tests, CI-Konfiguration), wenn die `.gitattributes` Ihrer Erweiterung sie aus dem Composer-Archiv ausschließt.

## Das eigene Manifest des Kernels

Der Kernel hält sein Manifest mit jedem Commit aktuell: `make manifest-fix` schreibt die Zeilen geänderter Dateien neu,
und der pre-commit-Hook und die CI prüfen es
([Qualitätsprüfungen](../../../../eng-us/features/6.0/quality-checks.md), auf Englisch). Eine Erweiterung kann dasselbe
mit einer kleinen Prüfung in ihrer eigenen CI tun.

## Verwandte Seiten

- Zurück: [GitHub-Releases und Release Notes](github-releases-und-release-notes.md)
- Weiter: [Pakete (.ezpkg) und der Paket-Ersteller](pakete-ezpkg.md)
- [Spezifikation: Metadaten von Extensions](../../../spezifikationen/6.0/extension-metadaten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
