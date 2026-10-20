---
title: Wie ein Release den Paketserver erreicht
audience: developer, partner
category: install-upgrade
order: 38
summary: Der Weg eines Pakets von einem Commit in seinem Quell-Repository zu einer Datei auf dem Paketserver - bauen, vergleichen, kopieren, index.xml aktualisieren, committen und pushen -, für eine Korrektur an einem vorhandenen Paket und für einen neuen Release-Ordner, mit den Prüfungen für jeden Schritt.
keywords: Release, veröffentlichen, build.sh, ezpkg, Paketserver, index.xml, Commit, Push, GitHub Pages, Ausrollen, Release-Ordner, RemotePackagesIndexURL, Korrektur, sevenx-ezpackage, release, publish, package server, deploy, release folder, fix
type: help
---
# Wie ein Release den Paketserver erreicht

Diese Seite ist für Entwickler, die Pakete veröffentlichen, und für alle, die wissen wollen, wie eine Änderung an einem Seitenpaket neue
Installationen erreicht. Sie beschreibt den Ablauf, dem 7x folgt, Schritt für Schritt, und die Prüfungen, die zu jedem Schritt gehören.
Derselbe Ablauf funktioniert für einen eigenen Paketserver.

## Inhalt

1. [Der Ablauf in einer Tabelle](#der-ablauf-in-einer-tabelle)
2. [Schritt 1: die Quelle ändern und committen](#schritt-1-die-quelle-ändern-und-committen)
3. [Schritt 2: die Archive bauen](#schritt-2-die-archive-bauen)
4. [Schritt 3: vergleichen](#schritt-3-vergleichen)
5. [Schritt 4: in den Release-Ordner kopieren](#schritt-4-in-den-release-ordner-kopieren)
6. [Schritt 5: index.xml aktualisieren](#schritt-5-indexxml-aktualisieren)
7. [Schritt 6: committen und pushen](#schritt-6-committen-und-pushen)
8. [Ein neuer Release-Ordner](#ein-neuer-release-ordner)
9. [Ein Beispiel aus der Geschichte](#ein-beispiel-aus-der-geschichte)
10. [Verwandte Seiten](#verwandte-seiten)

## Der Ablauf in einer Tabelle

| Schritt | Wo | Ergebnis |
|---|---|---|
| 1. Ändern und committen | das Quell-Repository ([Die Quell-Repositorys](die-quell-repositorys.md)) | die Änderung ist im Ordner des Pakets festgehalten |
| 2. Bauen | `packages/build.sh` in diesem Repository | eine `<name>.ezpkg` pro Ordner |
| 3. Vergleichen | Ihre Arbeitskopie | Sie wissen genau, was sich im Archiv geändert hat |
| 4. Kopieren | das Repository des Paketservers, `exponential/6.0/<version>/` | die neue `.ezpkg` ersetzt die alte |
| 5. Index | `exponential/6.0/<version>/index.xml` | nur geändert, wenn ein Paket neu ist, umbenannt wird oder eine neue Version oder Zusammenfassung bekommt |
| 6. Committen und pushen | das Repository des Paketservers, Branch `master` | GitHub Pages veröffentlicht die Website neu |

## Schritt 1: die Quelle ändern und committen

Nehmen Sie die Änderung im entpackten Paketordner vor, zum Beispiel `packages/sevenx_multisite/settings/` von `sevenx-ezpackage`. Committen
Sie sie zuerst dort, damit Quelle und Archiv nie voneinander abweichen. Nutzen Sie eine Nachricht, die das Paket nennt und sagt, was sich für
eine Installation ändert:

```text
Updated: The installer of the sevenx_site package activates ezownerchange, so changing the owner of content is available in a new installation
```

Wenn die Änderung `package.xml` berührt, prüfen Sie es zuerst: `xmllint --noout packages/<name>/package.xml`
([Ein Paket vor der Veröffentlichung prüfen](../anforderungen/ein-paket-vor-der-veroeffentlichung-pruefen.md)).

## Schritt 2: die Archive bauen

```bash
cd packages
./build.sh
```

`build.sh` packt jeden Ordner von `packages/` mit `tar -zcf ../<folder>.ezpkg` und lässt dabei Ordner der Versionsverwaltung, Reste von
Editoren und Betriebssystemen und `node_modules` weg (die Liste steht im Skript und in
[Die Quell-Repositorys](die-quell-repositorys.md#die-skripte)). Die Dateien `.ezpkg` werden neben die Ordner geschrieben. Repositorys
ohne `build.sh` nutzen `bin/pack.sh --output-dir=<dir>`. Siehe [Mit build.sh neu bauen](../eigene/mit-build-sh-neu-bauen.md).

## Schritt 3: vergleichen

Bevor irgendetwas kopiert wird, vergleichen Sie das neue Archiv mit dem auf dem Server:

```bash
# file lists, old and new
curl -s https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/sevenx_site.ezpkg | tar -tzf - | sort > old.txt
tar -tzf sevenx_site.ezpkg | sort > new.txt
diff old.txt new.txt

# a single file, old and new
curl -s https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/sevenx_site.ezpkg | tar -xzOf - package.xml > old-package.xml
tar -xzOf sevenx_site.ezpkg package.xml | diff old-package.xml -
```

Prüfen Sie, dass:

- [ ] sich nur die Dateien unterscheiden, die Sie ändern wollten;
- [ ] kein `.git`, `.DS_Store`, Backup eines Editors oder eine andere verirrte Datei in der Liste steht;
- [ ] `package.xml` oben im Archiv liegt, nicht in einem Ordner;
- [ ] die Datei weiterhin mit der gzip-Signatur beginnt (`file sevenx_site.ezpkg` sagt `gzip compressed data`).

Schreiben Sie die Vergleichsdateien in einen eigenen Arbeitsordner, nicht in einen geteilten temporären Ordner.

## Schritt 4: in den Release-Ordner kopieren

Kopieren Sie die `.ezpkg` in das Repository des Paketservers, in den Ordner des Release, zu dem sie gehört:

```bash
cp sevenx_site.ezpkg <package-server-clone>/exponential/6.0/6.0.15/
```

Eine Korrektur an einem veröffentlichten Paket **ersetzt die Datei an Ort und Stelle**: derselbe Name, derselbe Ordner, dieselbe URL. Jede
Installation, die diesen Ordner liest, bekommt von da an die korrigierte Datei. Die frühere Datei bleibt nur in der Git-Geschichte.

## Schritt 5: index.xml aktualisieren

Der Index ändert sich nur, wenn:

- ein Paket hinzugefügt wird (ein neuer Eintrag `<package>` und bei einem Seitenpaket ein Vorschaubild in `thumbnails/`);
- ein Paket umbenannt oder entfernt wird;
- `version`, `summary` oder `description` eines Seitenpakets im Setup-Assistenten anders lauten sollen.

Eine Korrektur an einem vorhandenen Paket braucht keine Änderung am Index. Prüfen Sie die Datei nach einer Bearbeitung mit
`xmllint --noout` und prüfen Sie jede `url` ([Das Format index.xml](das-format-index-xml.md#einen-index-prüfen)).

## Schritt 6: committen und pushen

Committen Sie im Repository des Paketservers mit derselben Art von Nachricht, die das Paket nennt:

```text
Updated: The sevenx_site package's installer activates ezownerchange
```

Pushen Sie nach `master`. Der Workflow baut die Verzeichnisauflistungen und rollt die Website aus
([Der Paketserver auf GitHub](der-paketserver-auf-github.md#vom-push-zur-website)). Dann:

- [ ] warten Sie, bis der Lauf unter **Actions** fertig ist;
- [ ] holen Sie die Datei vom Server und vergleichen Sie ihre Prüfsumme mit Ihrer (`sha256sum`);
- [ ] denken Sie daran, dass der Server `Cache-Control: max-age=600` sendet: Ein Cache kann die alte Datei bis zu zehn Minuten lang
      ausliefern;
- [ ] installieren Sie einmal vom Server in eine Sandbox ([In einer Sandbox testen](../eigene/in-einer-sandbox-testen.md)).

> **Hinweis**
> Seit dem 12.10.2026 laufen Ausrollvorgänge nacheinander; schnell aufeinanderfolgende Pushes warten aufeinander, statt zu scheitern. Einen
> Commit pro Paket zu pushen ist in Ordnung.

## Ein neuer Release-Ordner

Für ein neues Release von Exponential zeigt die Geschichte dieses Muster (6.0.14 am 05.06.2026, 6.0.15 am 07.08.2026):

1. Legen Sie `exponential/6.0/<new version>/` mit allen Paketen, `index.xml` und `thumbnails/` an.
2. Stellen Sie sicher, dass jede `url` und `thumbnail_url` in der neuen `index.xml` den neuen Ordner nennt. Direkt nach dem Import von
   6.0.15 korrigierte ein Commit mit einer Fehlerkorrektur für die neuen Paket-URLs die Einträge von `sevenx_multisite` und
   `sevenx_multisite_clean`.
3. Pushen Sie und prüfen Sie den Ordner in einem Browser.
4. Richten Sie im Kernel `settings/package.ini [RepositorySettings] RemotePackagesIndexURL` auf den neuen Ordner, in dem Release, das ihn
   nutzen soll ([Einstellungen des Paketservers](einstellungen-des-paketservers.md)).

Ältere Ordner bleiben, sodass Installationen älterer Releases weiter funktionieren.

## Ein Beispiel aus der Geschichte

Am 15.10.2026 lief eine Änderung durch beide Repositorys:

| Repository | Commit |
|---|---|
| `sevenx-ezpackage` | Der Installer des Pakets sevenx_site aktiviert ezownerchange, sodass das Ändern des Eigentümers von Inhalten in einer neuen Installation verfügbar ist (eine PHP-Datei in `packages/sevenx_site/settings/`, drei Zeilen) |
| `exponential.packages.exponential.earth` | Der Installer des Pakets sevenx_site aktiviert ezownerchange (`exponential/6.0/6.0.15/sevenx_site.ezpkg`, von 79.228 auf 79.291 Bytes) |

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Die Quell-Repositorys](die-quell-repositorys.md)
- [Der Paketserver auf GitHub](der-paketserver-auf-github.md)
- [Eigene Pakete versionieren](../eigene/eigene-pakete-versionieren.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
