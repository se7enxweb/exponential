---
title: Einen Fork mit seiner Quelle synchron halten
audience: developer
category: extension-development
order: 40
summary: Holen Sie die neuen Commits des Originals mit git fetch und merge oder rebase in Ihren Fork, lösen Sie Konflikte und halten Sie Ihre eigenen Änderungen klein, damit das Synchronisieren einfach bleibt - mit den Befehlen und einer Routine.
keywords: Fork, synchronisieren, upstream, git fetch, git merge, git rebase, Konflikt, Abweichung, Routine, fork, sync, conflict, divergence, routine
type: help
---
# Einen Fork mit seiner Quelle synchron halten

Teil 5, Kapitel 4. Ein Fork ist am Anfang mit seinem Original identisch und entfernt sich mit jedem Commit auf einer
der beiden Seiten weiter davon. Je weiter er sich entfernt, desto schwerer wird es, die Korrekturen des Originals zu
übernehmen. Dieses Kapitel zeigt, wie Sie die Änderungen des Originals in Ihren Fork holen und wie Sie das einfach
halten.

## Die Befehle

Mit den Remotes aus [Auf GitHub forken, Schritt für Schritt](auf-github-forken.md) (`origin` Ihr Fork, `upstream` das
Original):

```bash
git fetch upstream
git switch main
git merge --ff-only upstream/main
git push origin main
```

`--ff-only` gelingt nur, wenn Ihr `main` keine eigenen Commits hat, und so sollte es sein, wenn Sie auf Branches
arbeiten. Wenn der Befehl ablehnt, haben Sie versehentlich auf `main` committet: Verschieben Sie diese Commits zuerst
auf einen Branch.

GitHub bietet dasselbe als Schaltfläche an: **Sync fork** auf der Seite Ihres Forks.

## Das Original in einen Branch holen

Ihr Arbeits-Branch braucht die neuen Commits des Originals ebenfalls. Zwei Wege:

```bash
# merge: keeps your branch's history as it is, adds a merge commit
git switch fix-menu-entry-csp
git merge upstream/main

# rebase: replays your commits on top of the original's; history stays linear
git switch fix-menu-entry-csp
git rebase upstream/main
git push --force-with-lease origin fix-menu-entry-csp
```

| Verwenden | Wann |
|---|---|
| merge | der Branch wird mit anderen geteilt oder wurde schon geprüft |
| rebase | der Branch gehört nur Ihnen und wurde noch nicht geprüft; viele Projekte bevorzugen Pull Requests, die auf ihren aktuellen main rebast sind |

Rebasen Sie nie einen Branch, auf dem andere ihre Arbeit aufgebaut haben, und pushen Sie `main` nie mit Gewalt (force).

## Konflikte

Ein Konflikt bedeutet, dass das Original und Sie dieselben Zeilen geändert haben. Git hält an und markiert sie:

```text
<<<<<<< HEAD
<a id="menu-class-change" href="#" data-menu-form="menu-form-class-change">
=======
<a id="menu-class-change" href="#" class="menu-item" onclick="...">
>>>>>>> upstream/main
```

1. Lesen Sie beide Seiten und entscheiden Sie, wie die Zeile lauten muss. Oft ist es eine Kombination.
2. Entfernen Sie die Markierungen, speichern Sie und führen Sie `git add` für die Datei aus.
3. `git merge --continue` (oder `git rebase --continue`).
4. Führen Sie die Tests der Erweiterung aus und öffnen Sie die betroffenen Seiten, bevor Sie pushen.

## Eine Routine für einen dauerhaften Fork

- **Synchronisieren Sie oft.** Wöchentlich oder bei jedem Release des Originals. Zehn kleine Merges sind einfacher
  als ein großer.
- **Halten Sie Ihre Änderungen wenige und getrennt.** Ein Commit je Änderung, mit einer klaren Nachricht, damit Sie
  in `git log` sehen, was von Ihnen stammt.
- **Schicken Sie Ihre Änderungen zurück.** Jede Änderung, die das Original annimmt, ist eine Änderung weniger, die
  Sie tragen ([Mit Pull Requests zurückgeben](mit-pull-requests-zurueckgeben.md)).
- **Beobachten Sie das Original.** Auf GitHub sagt Ihnen **Watch > Custom > Releases**, wann Sie synchronisieren
  sollten.
- **Schreiben Sie auf, was sich unterscheidet.** Ein kurzer Abschnitt in Ihrer README, „Unterschiede zum Original“,
  erspart der nächsten Person, die ihn pflegt, Stunden.

## Sehen, wie weit Sie sich entfernt haben

```bash
git fetch upstream
git log --oneline upstream/main..main    # your commits the original does not have
git log --oneline main..upstream/main    # the original's commits you do not have
```

Zwei kurze Listen bedeuten einen gesunden Fork. Eine lange erste Liste bedeutet, dass Sie viel tragen: Zeit, einiges
davon zurückzuschicken.

## Verwandte Seiten

- Zurück: [Auf GitHub forken, Schritt für Schritt](auf-github-forken.md)
- Weiter: [Ethisches Rebranding](ethisches-rebranding.md)
- [Mit Git aktualisieren](../../mit-git-aktualisieren.md)
- [Syncing a fork](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/working-with-forks/syncing-a-fork) in der GitHub-Dokumentation (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
