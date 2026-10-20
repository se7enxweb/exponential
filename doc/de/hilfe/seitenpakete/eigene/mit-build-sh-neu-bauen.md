---
title: Mit build.sh neu bauen
audience: developer, partner
category: install-upgrade
order: 89
summary: Wie die Paketarchive von 7x aus dem Quell-Repository gebaut werden - packages/build.sh und bin/pack.sh Zeile für Zeile, was sie weglassen, wo die Dateien .ezpkg landen - und wie Sie ein Archiv prüfen, bevor es veröffentlicht wird.
keywords: build.sh, pack.sh, ezpkg, tar, gzip, neu bauen, Archiv, ausschließen, sevenx-ezpackage, cjw_multisite-ezpackage, tar -tzf, ezpm validate, Release, rebuild, archive, exclude, release
type: help
---
# Mit build.sh neu bauen

Diese Seite ist für Entwickler, die einen Paketordner geändert haben und das Archiv `.ezpkg` brauchen, das der Installer herunterlädt. Sie
erklärt die zwei Packskripte der Quell-Repositorys von 7x, was in einem Archiv landet und wie Sie das Ergebnis prüfen, bevor es jemand
anderes bekommt.

7x und die Exponential Foundation ermutigen Sie ausdrücklich, die Archive, die Sie bauen, per Pull Request an
se7enxweb/exponential.packages.exponential.earth einzureichen: siehe [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Was ein Archiv .ezpkg ist](#was-ein-archiv-ezpkg-ist)
2. [packages/build.sh](#packagesbuildsh)
3. [bin/pack.sh](#binpacksh)
4. [Das Archiv prüfen](#das-archiv-prüfen)
5. [Mit dem vorigen Release vergleichen](#mit-dem-vorigen-release-vergleichen)
6. [Vom Archiv zum Paketserver](#vom-archiv-zum-paketserver)

## Was ein Archiv .ezpkg ist

Ein mit gzip komprimiertes tar-Archiv eines Paketordners, mit `package.xml` oben (nicht in einem nach dem Paket benannten Ordner). Der Kernel
prüft beim Import genau das: Die Datei beginnt mit der gzip-Signatur, jeder Eintrag ist eine einfache Datei oder ein Verzeichnis mit einem
relativen Pfad, und `package.xml` liegt oben (`kernel/classes/ezpackageuploadinspector.php`). Mehr auf [Die Datei .ezpkg](../grundlagen/die-datei-ezpkg.md).

## packages/build.sh

Das Skript von [se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage) und seit dem 19.10.2026 dasselbe Skript in
[se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage), aus `packages/` heraus ausgeführt:

```bash
cd packages
bash build.sh
ls *.ezpkg
```

Was es tut, der Reihe nach:

1. Es geht jeden Eintrag des aktuellen Verzeichnisses durch (`for folder in *`).
2. Es überspringt alles, was kein Verzeichnis ist, sodass die Dateien `.ezpkg` eines früheren Laufs und `build.sh` selbst in Ruhe gelassen
   werden.
3. Es wechselt in den Ordner und führt `tar -zcf ../<folder>.ezpkg <excludes> *` aus: Das Archiv wird nach dem Ordner benannt und neben ihn
   geschrieben, und `*` setzt den Inhalt des Ordners oben in das Archiv.

Die Ausschlüsse, wie das Skript sie aufführt:

| Weggelassen | Warum |
|---|---|
| `.git`, `.gitignore`, `.gitattributes`, `.gitmodules`, `.svn`, `.hg` | Dateien der Versionsverwaltung mitgelieferter Arbeitskopien von Erweiterungen |
| `.DS_Store`, `.idea`, `.vscode` | Ordner von Betriebssystem und Editor |
| `*~`, `*.swp` | Backups und Auslagerungsdateien von Editoren |
| `*.bak`, `*.bak.*` | Sicherungskopien, die beim Bearbeiten eines Paketordners entstanden (am 19.10.2026 hinzugefügt, nachdem 113 solcher Kopien in `sevenx_multisite_democontent` ausgeliefert worden waren) |
| `node_modules` | Build-Abhängigkeiten |

tar wendet jeden Namen in jeder Tiefe an. Alles andere im Ordner geht hinein, auch Dateien, die ein Skript oder ein Editor neben den
XML-Dateien hinterlassen hat. Sehen Sie sich `git status` und den Ordner an, bevor Sie bauen.

> **Hinweis**
> `*` trifft keine Namen, die oben im Ordner mit einem Punkt beginnen, sodass eine Punktdatei auf oberster Ebene nie gepackt wird; die
> Ausschlüsse decken die tiefer liegenden ab.

## bin/pack.sh

Das ältere Skript, in beiden Repositorys. Führen Sie es aus dem Wurzelverzeichnis des Repositorys aus:

```bash
bash bin/pack.sh --output-dir="$PWD/build"
```

Das Skript nutzt das Schlüsselwort `function`, das bash versteht und manche Shells `/bin/sh` nicht; rufen Sie es also mit `bash` auf. Es
wechselt vor dem Packen nach `packages/`; geben Sie `--output-dir` deshalb als absoluten Pfad an.

Es geht `packages/` durch, überspringt einen Ordner namens `package_set` und schreibt `<output-dir>/<folder>.ezpkg` mit denselben
Ausschlüssen plus `.cache` und `pack_and_publish.bat`. Ohne `--output-dir` schreibt es in einen festen Standardordner unter `/tmp`; übergeben
Sie die Option immer, damit die Archive dort landen, wo Sie sie erwarten.

`ant/createzpkg.sh <source> <destination>` packt einen einzelnen Ordner (ohne `.svn`, `.cache`, `pack_and_publish.bat`), und
`bin/repack_extension.sh` baut ein Erweiterungspaket mit `ezpm` neu ([Pakete mit ezpm erstellen](pakete-mit-ezpm-erstellen.md)).

## Das Archiv prüfen

```bash
# the top of the archive: package.xml first-level, no wrapping folder
tar -tzf acme_site.ezpkg | head

# the gzip signature the importer checks (1f 8b)
head -c 2 acme_site.ezpkg | od -An -tx1

# the definition is well formed
tar -xzOf acme_site.ezpkg package.xml | xmllint --noout -

# the kernel's own check, without importing (in an installation)
php ezpm.php validate /path/to/acme_site.ezpkg
```

`ezpm validate` nimmt ein Archiv, ein Paketverzeichnis oder einen Paketnamen und prüft jedes Dokument, die Definition, wie der Kernel sie
liest, die Dokumente, die die Elemente nennen, und bei JSON-Paketen das Schema und die Prüfsummen. Die Grenzen, unter denen ein Hochladen
bleiben muss (`package.ini [UploadSettings]`: standardmäßig 256 MiB pro Archiv, 2 GiB entpackt, 50000 Einträge), stehen auf
[Ein Paket vor der Veröffentlichung prüfen](../anforderungen/ein-paket-vor-der-veroeffentlichung-pruefen.md).

## Mit dem vorigen Release vergleichen

Ein Neubau eines unveränderten Ordners ist nicht Byte für Byte gleich (tar hält Zeiten fest); vergleichen Sie also die Listen, nicht die
Dateien:

```bash
diff <(tar -tzf old/acme_site.ezpkg | sort) <(tar -tzf acme_site.ezpkg | sort)
```

Jede Zeile des Unterschieds sollte durch einen Commit seit dem letzten Release erklärt sein.

## Vom Archiv zum Paketserver

Der Release-Ablauf von 7x, wie er für jeden Paketserver gilt:

1. Ändern Sie das Paket im Quell-Repository und committen Sie.
2. Führen Sie `packages/build.sh` aus.
3. Vergleichen Sie die neuen Archive mit den veröffentlichten.
4. Kopieren Sie jede `.ezpkg` in das Repository des Paketservers unter `exponential/6.0/<version>/`.
5. Aktualisieren Sie die `index.xml` dieses Ordners (und `thumbnails/` für ein neues Seitenpaket).
6. Committen und pushen Sie; der Server veröffentlicht neu ([Wie ein Release den Paketserver erreicht](../paketserver/wie-ein-release-den-paketserver-erreicht.md)).

Für Ihren eigenen Server siehe [Einen eigenen Paketserver betreiben](einen-eigenen-paketserver-betreiben.md).

## Verwandte Seiten

- [Ein eigenes Seitenpaket erstellen](README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Die Datei .ezpkg](../grundlagen/die-datei-ezpkg.md)
- [Ein Paket vor der Veröffentlichung prüfen](../anforderungen/ein-paket-vor-der-veroeffentlichung-pruefen.md)
- [Die Quell-Repositorys](../paketserver/die-quell-repositorys.md)
- [Ein Paket prüfen](../../../../en/us/help/designers/extensions/validating-a-package.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
