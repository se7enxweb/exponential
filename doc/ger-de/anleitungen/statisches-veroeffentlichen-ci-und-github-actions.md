# Statisches Veröffentlichen: CI und GitHub Actions

Diese Anleitung ist für Entwickler, die eine statische Kopie aus einem CI-Job bauen und veröffentlichen, und für
Administratoren, die GitHub Actions ausliefern lassen, was Exponential pusht. Sie erklärt die maschinenlesbare Ausgabe
der Befehle, die Workflow-Datei, die der Build für `Delivery=actions` schreibt, Zeile für Zeile, und die
Startvorlage, die eine ganze Site auf GitHub baut.

[Anleitungen](README.md) · Verwandt: [Branch oder GitHub Actions wählen](../hilfe/statisches-veroeffentlichen/branch-oder-github-actions-waehlen.md),
[Statische Websites auf GitHub Pages hosten mit Exponential 6.0.15+](statische-websites-auf-github-pages-hosten.md#aus-ci),
[Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md#befehle)

## Was Sie am Ende haben

- Einen CI-Job, der ein Ziel baut, prüft und veröffentlicht und fehlschlägt, wenn etwas nicht stimmt.
- Den Actions-Workflow eines Ziels verstanden, sodass Sie wissen, was GitHub ausführt und warum.
- Eine Wahl zwischen dem Push von Ihrem Server und dem Bauen ganz auf GitHub.

## Zwei Wege der Auslieferung

| | Branch (`Delivery=branch`) | GitHub Actions (`Delivery=actions`) |
|---|---|---|
| Wer baut | Exponential, auf Ihrem Server oder in Ihrer CI | Exponential, auf Ihrem Server oder in Ihrer CI |
| Was GitHub tut | liefert den Branch aus, wie er ist | führt den Workflow unten aus, der die gepushten Dateien hochlädt und ausliefert |
| Pages-Einstellung | **Deploy from a branch** | **GitHub Actions** |
| Das Token braucht | Contents: write | Contents: write und Workflows (die Workflow-Datei wird mitgepusht) |

In beiden Fällen wird auf GitHub nichts gebaut: Die Dateien werden veröffentlicht, wie Exponential sie geschrieben hat.

## Befehle in einem CI-Job

Jeder `exp:static:*`-Befehl nimmt `--format=json` und gibt ein JSON-Dokument aus (Schema `exp-static/1`). Die
Exit-Codes sind 0 erledigt, 1 fehlgeschlagen, 2 Aufruf falsch, 3 Datenbank nicht erreichbar, 4 interner Fehler.

```bash
./console exp:static:targets --format=json
./console exp:static:build github-public --format=json --report=var/log/static-build.json
./console exp:static:diff github-public --verify --check-links --mount=/ --mount=/my-site/ --format=json
GH_PAGES_TOKEN=$TOKEN ./console exp:static:publish github-public --format=json
```

- Halten Sie das Token aus dem Ziel heraus: `Credential=token:env:GH_PAGES_TOKEN` liest es aus der Umgebung des Jobs.
  Es erreicht Git über `GIT_ASKPASS` und erscheint nie auf einer Befehlszeile oder in einem Log.
- Erreicht der Build-Server die öffentliche Adresse der Site nicht, setzen Sie `CrawlURL` (oder übergeben
  `--crawl-url`).
- Ist `SiteURL` `localhost` oder eine Loopback-Adresse, wie in CI, bleiben Links auf Seiten, die die Kopie nicht
  enthält, wie sie sind, und der Build warnt; setzen Sie `LiveURL` auf Ihre Live-Site, damit sie dorthin führen.
- Ein fehlgeschlagener Push bleibt `pending`; `exp:static:push` sendet ihn erneut.

## Die Workflow-Datei, Zeile für Zeile

Mit `Kind=github-pages` und `Delivery=actions` (oder `Extras[]=workflow`) schreibt jeder Build
`.github/workflows/static-publish-pages.yml` in die Kopie. Sie wird nur geschrieben, wenn der Build an der Wurzel des
Branches liegt (`Folder` leer), weil GitHub Workflows von dort liest. Für `Branch=gh-pages` lautet sie:

```yaml
# Written by Exponential static publishing: deploys the static copy pushed to the branch
# gh-pages to GitHub Pages. It builds nothing; the files are published as they are.
# Each publish rewrites this file: change the target's settings instead.
name: Deploy the static site to GitHub Pages
on:
  push:
    branches: ["gh-pages"]
  workflow_dispatch:
permissions:
  contents: read
  pages: write
  id-token: write
concurrency:
  group: pages
  cancel-in-progress: false
jobs:
  deploy:
    environment:
      name: github-pages
      url: ${{ steps.deployment.outputs.page_url }}
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/configure-pages@v5
      - uses: actions/upload-pages-artifact@v3
        with:
          path: .
      - id: deployment
        uses: actions/deploy-pages@v4
```

| Zeilen | Bedeutung |
|---|---|
| Die Kommentare | Wer die Datei geschrieben hat. Bearbeiten Sie sie nicht: Jede Veröffentlichung schreibt sie aus den Einstellungen des Ziels neu |
| `name` | Der Name im Reiter **Actions** des Repositorys |
| `on: push: branches` | Läuft bei jedem Push auf den `Branch` des Ziels, also bei jeder Veröffentlichung und jedem Zurücknehmen |
| `workflow_dispatch` | Erlaubt den Start von Hand im Reiter Actions, etwa nach einer Änderung der Pages-Einstellungen |
| `permissions` | Das Wenigste, was die Auslieferung braucht: Dateien lesen, nach Pages schreiben, und ein OIDC-Token, das den Lauf gegenüber Pages ausweist |
| `concurrency` | Eine Auslieferung zur Zeit; eine neuere wartet, statt die laufende abzubrechen |
| `environment: github-pages` | Die Auslieferungsumgebung, die GitHub für Pages anlegt; `url` zeigt die veröffentlichte Adresse am Lauf |
| `runs-on: ubuntu-latest` | Ein Runner von GitHub; nichts von Ihrer Installation läuft dort |
| `actions/checkout` | Holt den gepushten Branch: die Kopie, wie Exponential sie gebaut hat |
| `actions/configure-pages` | Liest die Pages-Einstellungen des Repositorys |
| `actions/upload-pages-artifact`, `path: .` | Packt den ganzen Checkout als Site |
| `actions/deploy-pages` | Veröffentlicht die gepackte Site; seine Schritt-Id `deployment` liefert die URL oben |

`exp:static:github status` und der Kasten **Auf GitHub** im Verlauf zeigen den letzten Lauf dieses Workflows.

## Auf GitHub bauen: die Startvorlage

Für eine Site, die ganz auf GitHub gebaut wird, ohne eigenen Server, beginnen Sie mit dem Vorlagen-Repository
[exponential-static-site-starter](https://github.com/se7enxweb/exponential-static-site-starter): **Use this template**,
**Settings > Pages > Source** auf **GitHub Actions** stellen und pushen. Sein eigener Workflow installiert Exponential
mit Composer, bringt Ihre Site herein (eine SQLite-Datenbank und den Speicher aus `data/` und Einstellungen,
Erweiterungen und Designs aus `site/`, oder ein Seitenpaket, installiert mit `exp:install`), liefert sie auf
`127.0.0.1` mit dem eingebauten Webserver von PHP aus, führt `exp:static:build` und `exp:static:diff --check-links`
aus und übergibt die Kopie den Pages-Actions von GitHub. Ein Token ist nicht nötig; die README der Vorlage listet ihre
Einstellungen.

## Nächster Schritt

[Öffentlich entwickeln, kostenlos veröffentlichen](oeffentlich-entwickeln-kostenlos-veroeffentlichen.md), ein Tutorial,
das die Startvorlage mit dem Bearbeiten in Exponential verbindet.
