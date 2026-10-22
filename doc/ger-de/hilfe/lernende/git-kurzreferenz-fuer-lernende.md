---
title: Git-Kurzreferenz für Lernende
audience: student
category: learning
order: 46
summary: Eine einseitige Referenz der Git- und Composer-Befehle, die Lernende mit Exponential verwenden - Klone einrichten, den Zustand prüfen, Branches anlegen und committen, die Historie lesen, eine Änderung finden, die etwas kaputt gemacht hat, über einen Fork beitragen, auf dem neuesten Stand bleiben und was zu tun ist, wenn Composer sich weigert -, jeweils mit ihrer Quellseite.
keywords: git, spickzettel, kurzreferenz, befehle, klonen, branch, commit, log, blame, bisect, fork, pull request, rebase, composer, status, lernende, cheat sheet, quick reference, commands, clone, learner
type: help
---
# Git-Kurzreferenz für Lernende

Jeder Befehl auf dieser Seite stammt aus [Mit Git entwickeln](../mit-git-entwickeln.md) oder aus `CONTRIBUTING.md`,
sofern keine andere Quelle genannt ist. Drucken Sie sie aus oder legen Sie sie neben Ihr Terminal. Die Erklärungen
finden Sie in [Git als Lernende nutzen](git-als-lernende-nutzen.md).

## Inhalt

1. [Einrichten](#einrichten)
2. [Wo bin ich?](#wo-bin-ich)
3. [Branches anlegen und committen](#branches-anlegen-und-committen)
4. [Die Historie lesen](#die-historie-lesen)
5. [Eine Änderung finden, die etwas kaputt gemacht hat](#eine-änderung-finden-die-etwas-kaputt-gemacht-hat)
6. [Über einen Fork beitragen](#über-einen-fork-beitragen)
7. [Auf dem neuesten Stand bleiben](#auf-dem-neuesten-stand-bleiben)
8. [Wenn Composer sich weigert](#wenn-composer-sich-weigert)

## Einrichten

| Befehl | Bewirkt |
|---|---|
| `composer config --global 'preferred-install.se7enxweb/*' source` | Jedes Paket `se7enxweb/*` als Git-Klon installieren |
| `git clone https://github.com/se7enxweb/exponential.git exponential` | Den Kernel mit seiner vollständigen Historie holen |
| `git switch -c my-site` | Einen eigenen Branch auf `main` beginnen |
| `git switch -c my-site v6.0.14` | Einen eigenen Branch stattdessen auf einem Release-Tag beginnen |
| `git tag -l 'v*' --sort=version:refname` | Die Release-Tags in Versionsreihenfolge auflisten |
| `composer install` | Die Erweiterungen nach `extension/<name>` klonen |

## Wo bin ich?

| Befehl | Bewirkt |
|---|---|
| `git status` | Geänderte und nicht versionierte Dateien |
| `git diff` | Die Änderungen, Zeile für Zeile |
| `composer status -v` | Pakete mit nicht committeten Änderungen, nicht gepushten Commits oder einer anderen Version als in der Lock-Datei |
| `ls -d extension/*/.git \| wc -l` | Die Erweiterungen zählen, die Klone sind |
| `git -C extension/<name> status` | Der Zustand einer Erweiterung („HEAD detached at v...“ ist normal) |

## Branches anlegen und committen

| Befehl | Bewirkt |
|---|---|
| `git switch -c <branch>` | Ein neuer Branch ab der aktuellen Stelle |
| `git add <file>` | Eine Datei vormerken (beim Namen, nicht alles auf einmal) |
| `git commit -m "Fixed: <what>"` | Mit einer Betreffzeile im Stil des Projekts committen |
| `git log --oneline -5` | Ihre letzten fünf Commits |

Betreffzeilen beginnen mit `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder `Renamed: `.

## Die Historie lesen

| Befehl | Bewirkt |
|---|---|
| `git log --oneline -10 -- <file>` | Die letzten zehn Änderungen an einer Datei |
| `git blame <file>` | Wer jede Zeile zuletzt geändert hat |
| `git log -S "<text>" --oneline` | Jeder Commit, der ein Textstück hinzugefügt oder entfernt hat |
| `git -C extension/<name> log --oneline -3` | Die neuesten Commits einer Erweiterung |

## Eine Änderung finden, die etwas kaputt gemacht hat

```bash
git bisect start
git bisect bad
git bisect good v6.0.13
# test, then answer: git bisect good   or   git bisect bad
git bisect reset
```

## Über einen Fork beitragen

```bash
cd extension/<name>
git remote add fork https://github.com/<you>/<name>.git
git switch -c <fix-branch>
git commit -am "Fixed: <what>"
git push -u fork <fix-branch>
# then open the pull request from the link GitHub prints, or: gh pr create
```

Solange der Pull Request offen ist, wenn die Begutachtung es verlangt:

```bash
git fetch origin
git rebase -i origin/main
git push --force-with-lease     # only to your own pull request branch
```

Für den Kernel: einmal `make hooks`, und `make manifest-fix`, um `share/filelist.md5` aktuell zu halten.

## Auf dem neuesten Stand bleiben

| Befehl | Bewirkt |
|---|---|
| `git fetch origin --tags`, dann `git merge origin/main` | Den Kernel auf Ihrem Branch aktualisieren |
| `composer update` | Danach die Erweiterungen passend dazu |
| `composer update se7enxweb/<package>` | Nur eine Erweiterung |

Zuerst Git, dann Composer. Der vollständige Ablauf: [Mit Git aktualisieren](../mit-git-aktualisieren.md).

## Wenn Composer sich weigert

| Composer meldet | Tun Sie |
|---|---|
| `... has uncommitted changes.` | Auf einem Branch committen, oder per Stash beiseitelegen, aktualisieren und wiederherstellen |
| `... has unpushed changes on the current branch` | Den Branch in Ihren Fork pushen, dann aktualisieren |
| `You have version variations ...` | Erwartet, solange Sie an einer Korrektur arbeiten; `composer update <package>` kehrt zum Tag zurück |
| Ein Paket ist noch ein Archiv | `composer reinstall <package>`; niemals von Hand mit `git clone` hineinklonen |

Quelle: [Wenn Composer sich weigert](../mit-git-entwickeln.md#wenn-composer-sich-weigert).

## Verwandte Seiten

- [Git als Lernende nutzen](git-als-lernende-nutzen.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Mit Git aktualisieren](../mit-git-aktualisieren.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
