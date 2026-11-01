# Statisches Veröffentlichen: Verlauf und Zurücknehmen

Diese Anleitung ist für Administratoren einer Exponential-6.0.15+-Website, die mit `exp:static:publish` als statische
Kopie veröffentlicht wird. Sie zeigt, wie Sie den Verlauf der Veröffentlichungen lesen und eine frühere
Veröffentlichung wieder online stellen, wenn eine Veröffentlichung schiefging, ohne etwas zu verlieren: Zurücknehmen
ist eine neue Veröffentlichung, nie ein Umschreiben des Verlaufs.

[Anleitungen](README.md) · Verwandt: [Repositorys und Zugangsdaten](statisches-veroeffentlichen-repositorys-und-zugangsdaten.md),
[Statische Websites auf GitHub Pages hosten](statische-websites-auf-github-pages-hosten.md),
[Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md)

## Was Sie am Ende haben

- Die Liste der Veröffentlichungen eines Ziels mit ihren Tags, Push-Zuständen und wer sie ausgeführt hat.
- Die öffentliche Website wieder auf einer früheren Veröffentlichung, gepusht als neuer Commit mit eigenem Tag.
- Ein Zurücknehmen, das Sie wiederum zurücknehmen können, denn nichts wurde gelöscht.

## Wie der Verlauf geführt wird

Jede Veröffentlichung, die etwas ändert, ist ein Commit auf dem Branch des Ziels mit einem annotierten Tag
`static/<ziel>/<n>`, gezählt ab 1. Jedes Zurücknehmen ist ebenfalls eine Veröffentlichung. Tags werden nie verschoben
oder ersetzt, und der Push wird nie erzwungen, daher ist der Verlauf beim Git-Host derselbe wie im Ordner des Ziels und
wächst nur.

Die Tabelle `exp_static_run` führt eine Zeile pro Veröffentlichung und Zurücknehmen: die Lauf-ID, die Art (`publish`,
`rollback`), den Zustand (`done`, `unchanged`, `failed`), Seiten, Dateien und Bytes, die hinzugefügten, geänderten und
entfernten Dateien, den Commit, das Tag, den Push-Zustand, wer ihn ausgeführt hat und wann.

## 1. Den Verlauf lesen

```bash
./console exp:static:status github-public --limit=20
```

```text
  Run                     Kind      State      Tag                          Push       By
  20261103-091500-77d0e1  publish   done       static/github-public/4       pushed     cli alpha
  20261102-170210-1c9b3a  publish   done       static/github-public/3       pushed     cli alpha
  20261102-101145-a3e872  publish   unchanged  -                            none       cli alpha
  20261101-160000-0b81d2  publish   done       static/github-public/2       pushed     cli alpha
  20261101-120000-4f2a9c  publish   done       static/github-public/1       pushed     cli alpha
```

Die Tags stehen auch im Repository beim Git-Host (GitHub: **Releases > Tags**) und im Ordner:

```bash
git -C var/site/static-publish/github-public log --oneline --decorate
```

## 2. Sehen, was ein Zurücknehmen ändern würde

Nennen Sie die Veröffentlichung, zu der Sie zurückwollen, mit ihrer Nummer oder ihrem Tag:

```bash
./console exp:static:rollback github-public 3
```

```text
Static publishing: rollback of github-public, branch gh-pages in var/site/static-publish/github-public
  rolling back to static/github-public/3 would add 1, change 4 and remove 0 file(s); run again with --apply
    A	news/item-1/index.html
    M	.static-publish/manifest.json
    M	index.html
    M	news/index.html
    M	sitemap.xml
PASS
```

Ohne `--apply` ändert sich nichts: kein Commit, kein Tag, keine Zeile in `exp_static_run`.

## 3. Zurücknehmen

```bash
./console exp:static:rollback github-public 3 --apply
```

```text
  committed 9a7c55e012: the files of static/github-public/3 (1 added, 4 changed, 0 removed)
  tagged static/github-public/5
  pushed gh-pages and the tags static/github-public/* to https://github.com/owner/my-site.git
  Commit: 9a7c55e0124b, tag static/github-public/5
  Push:   pushed (https://github.com/owner/my-site.git, credential token:github_pages_token)
PASS
```

Was geschehen ist:

- Ein neuer Commit oben auf dem Branch enthält genau die Dateien von Veröffentlichung 3, das Manifest eingeschlossen.
  Die Commits von Veröffentlichung 4 bleiben im Verlauf.
- Er bekommt die nächste Nummer, 5. Die Tags 1 bis 4 bleiben unverändert.
- Er wird wie jede Veröffentlichung gepusht, als Fast-Forward. Scheitert der Push, wartet er wie jede Veröffentlichung
  auf `exp:static:push`.
- Die Seiten in `exp_static_page` sind die von Veröffentlichung 3, und `exp_static_run` hat eine Zeile der Art
  `rollback`.
- Das Audit zeichnet `system.staticpublish.rollback` mit dem Tag auf, zu dem zurückgekehrt wurde.

`--message` setzt die Commit-Nachricht (ohne: `Roll back github-public to static/github-public/3`); `--no-push`
committet und taggt, ohne zu pushen.

Um das Zurücknehmen rückgängig zu machen, nehmen Sie zu 4 zurück.

## Nach dem Zurücknehmen

Der Inhalt der Website hat sich nicht geändert: Das nächste `exp:static:publish --build` baut wieder den aktuellen
Inhalt und veröffentlicht ihn als 6. Korrigieren Sie zuerst den Inhalt (oder nehmen Sie das Ziel aus dem automatischen
Veröffentlichen), wenn eine Seite der Grund für das Zurücknehmen war.

Ein Zurücknehmen startet nicht, solange der Ordner einen Build enthält, der nicht veröffentlicht wurde (`has changes
that are not committed`): Veröffentlichen Sie ihn oder bauen Sie zuerst neu.

## Wer es darf

Von der Konsole aus jeder, der Befehle als Systembenutzer der Website ausführen darf. Die Bildschirme des statischen
Veröffentlichens in der Administration, die folgen, werden die Richtlinie `staticpublish/publish` und „Bestätigen Sie,
dass Sie es sind“ verlangen.

## Nächster Schritt

[SSH-Deploy-Keys](statisches-veroeffentlichen-deploy-keys.md): sich mit einem eigenen Schlüssel an genau einem
Repository anmelden.
