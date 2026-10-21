---
title: Der Paketserver auf GitHub
audience: developer, admin, hosting
category: install-upgrade
order: 36
summary: Das GitHub-Repository hinter dem Paketserver, se7enxweb/exponential.packages.exponential.earth, und wie daraus eine Website wird - die Einstellungen von GitHub Pages, die Workflow-Datei Zeile für Zeile, die Action für die Verzeichnisauflistung, CNAME, FUNDING.yml und crossdomain.xml.
keywords: GitHub, GitHub Pages, Repository, workflow.yml, GitHub Actions, Verzeichnisauflistung, jayanta525, github-pages-directory-listing, CNAME, FUNDING.yml, crossdomain.xml, deploy-pages, upload-pages-artifact, concurrency, Branch master, repository, directory listing, master branch
type: help
---
# Der Paketserver auf GitHub

Diese Seite ist für Entwickler, die den Paketserver verstehen, nachbauen oder zu ihm beitragen wollen. Sie erklärt das Repository
[se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth), wie GitHub daraus
<https://exponential.packages.exponential.earth/> macht und wofür jede Datei außerhalb der Release-Ordner da ist. Die Fakten zum Repository
wurden am 18.10.2026 mit `gh api` (nur lesend) gelesen.

## Inhalt

1. [Das Repository](#das-repository)
2. [Vom Push zur Website](#vom-push-zur-website)
3. [Die Workflow-Datei](#die-workflow-datei)
4. [Die Action für die Verzeichnisauflistung](#die-action-für-die-verzeichnisauflistung)
5. [CNAME](#cname)
6. [FUNDING.yml](#fundingyml)
7. [crossdomain.xml und README.md](#crossdomainxml-und-readmemd)
8. [Die Einrichtung für einen eigenen Server nachbauen](#die-einrichtung-für-einen-eigenen-server-nachbauen)
9. [Verwandte Seiten](#verwandte-seiten)

## Das Repository

| Eigenschaft | Wert |
|---|---|
| Name | `se7enxweb/exponential.packages.exponential.earth` |
| Sichtbarkeit | öffentlich |
| Standard-Branch | `master` |
| Homepage | <https://exponential.packages.exponential.earth/> |
| Fork von | `ezpublishlegacy/packages.ezpublishlegacy.com`, dem Spiegel von 2014 ([Geschichte](geschichte-des-paketservers.md)) |
| Größe | etwa 2,5 GB mit seiner Geschichte (die GitHub-API meldet 2.520.299 KB) |
| Issues | eingeschaltet |
| Commits | 341 (bis 16.10.2026) |

Der Baum:

```text
.github/FUNDING.yml
.github/workflows/workflow.yml
CNAME
README.md
crossdomain.xml
exponential/6.0/<version>/index.xml
exponential/6.0/<version>/<name>.ezpkg
exponential/6.0/<version>/thumbnails/<name>.png
```

> **Tipp**
> Das Repository ist groß, weil jede `.ezpkg` jedes Release in seiner Geschichte aufbewahrt wird. Zum Lesen klonen Sie es ohne Blobs:
> `git clone --filter=blob:none https://github.com/se7enxweb/exponential.packages.exponential.earth.git`, dann nutzen Sie `git log` und
> `git ls-tree -r -l HEAD`; Git holt den Inhalt einer Datei erst, wenn Sie sie öffnen.

## Vom Push zur Website

| Schritt | Was geschieht |
|---|---|
| 1 | Ein Commit wird nach `master` gepusht (direkt oder durch Zusammenführen eines Pull Requests) |
| 2 | Der Workflow `main` startet (`on: [push]`) |
| 3 | Der Job `pages-directory-listing` checkt `master` aus, schreibt in jeden Ordner eine `index.html` und lädt den ganzen Baum als Artefakt für Pages hoch |
| 4 | Der Job `deploy` veröffentlicht dieses Artefakt in der Umgebung `github-pages` |
| 5 | GitHub Pages liefert es unter der eigenen Domain aus `CNAME` aus, mit erzwungenem HTTPS |

Die Einstellungen von Pages, wie die API sie meldet: Build-Typ `workflow` (kein Branch), Quell-Branch `master`, eigene Domain
`exponential.packages.exponential.earth`, HTTPS erzwungen. Der Workflow ist 150 Mal gelaufen; die Läufe stehen unter
[Actions](https://github.com/se7enxweb/exponential.packages.exponential.earth/actions).

> **Hinweis**
> Während ein Ausrollen läuft, kann der Server einige Minuten lang mit Fehlern antworten. Deshalb wiederholt der Setup-Assistent einen
> gescheiterten Download ([Wenn der Paketserver nicht erreichbar ist](wenn-der-paketserver-nicht-erreichbar-ist.md)).

## Die Workflow-Datei

`.github/workflows/workflow.yml`, auf ihre arbeitenden Zeilen gekürzt:

```yaml
name: main
on: [push]

concurrency:
  group: pages
  cancel-in-progress: false

jobs:
  pages-directory-listing:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4.2.2
        with:
          ref: master
      - uses: jayanta525/github-pages-directory-listing@main
        with:
          FOLDER: .
      - uses: actions/upload-pages-artifact@v3
        with:
          path: '.'

  deploy:
    needs: pages-directory-listing
    permissions:
      pages: write
      id-token: write
    environment:
      name: github-pages
    runs-on: ubuntu-latest
    steps:
      - id: deployment
        uses: actions/deploy-pages@v4
```

| Teil | Warum |
|---|---|
| `concurrency: group: pages`, `cancel-in-progress: false` | Ein Ausrollen nach dem anderen. Sein Kommentar erklärt: Ein Push, der während eines laufenden Ausrollens ankommt, wartet darauf, und nur der neueste wartende Push rollt aus; jedes Ausrollen veröffentlicht den ganzen Baum zum Stand seines Commits, sodass nichts verloren geht, wenn ein älterer wartender übersprungen wird. Hinzugefügt am 12.10.2026, als nacheinander gepushte Pakete die Ausrollvorgänge der anderen scheitern ließen |
| `ref: master` | Rollt immer den Branch aus, gleich welcher Ref gepusht wurde |
| `FOLDER: .` | Seiten der Auflistung für den ganzen Baum, von oben |
| `path: '.'` | Der ganze Checkout ist die Website |
| `permissions` | Der Job zum Ausrollen darf nach Pages schreiben und ein OIDC-Token anfordern, das `deploy-pages` braucht |

Die echte Datei hat außerdem Schritte, die `ls -al` und `du -sh .` ausgeben, um die Größe des Artefakts im Log zu sehen.

## Die Action für die Verzeichnisauflistung

[jayanta525/github-pages-directory-listing](https://github.com/jayanta525/github-pages-directory-listing) erzeugt für jeden Ordner die
Seite „Index of /...“. Wie die Seiten aussehen, ist in [Den Paketserver im Browser durchsuchen](den-paketserver-im-browser-durchsuchen.md)
beschrieben. Vor dem 24.12.2023 nutzte der Server einen JavaScript-Dateibrowser in einer von Hand erstellten `index.html`; er wurde durch
diese Action ersetzt.

## CNAME

`CNAME` enthält eine Zeile, den Hostnamen, auf den GitHub Pages antwortet:

```text
exponential.packages.exponential.earth
```

Sie hat diesen Wert seit dem 13.08.2025. Ihn zu ändern verlegt die Website auf einen anderen Hostnamen; jede Installation, deren
`RemotePackagesIndexURL` den alten nennt, hinge dann von einer Umleitung ab. Ändern Sie ihn nicht in einem Pull Request.

## FUNDING.yml

`.github/FUNDING.yml` lässt GitHub im Repository eine Schaltfläche **Sponsor** zeigen. Sie nennt GitHub Sponsors für die Organisation
`se7enxweb`, Patreon und eigene Links zu PayPal, Venmo, Cash App und <https://se7enx.com>. Sie wurde am 24.12.2023 angelegt. Auf die Website
hat sie keine Wirkung. Siehe [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md), wie das Projekt finanziert
wird.

## crossdomain.xml und README.md

- `crossdomain.xml` ist eine Cross-Domain-Richtlinie, die den Zugriff von jeder Domain erlaubt (`<allow-access-from domain="*"/>`). Sie kam
  mit dem Spiegel von 2014; nichts in Exponential liest sie.
- `README.md` nennt den Server (Paketserver für Exponential 6), das Datum seiner letzten Aktualisierung und Links zum Projekt, zum
  Paketserver, zur Dokumentation, zur Community und zum Hersteller. Sie wird als Datei der Website ausgeliefert und auf der Startseite des
  Repositorys gezeigt.

## Die Einrichtung für einen eigenen Server nachbauen

Dieselben drei Dinge machen jedes Repository zu einem Paketserver: Release-Ordner mit einer `index.xml` und den Paketen, ein Workflow für
Pages wie der oben und, wenn Sie einen eigenen Hostnamen wollen, eine Datei `CNAME` und ein DNS-Eintrag dafür. Siehe
[Einen eigenen Paketserver betreiben](../eigene/einen-eigenen-paketserver-betreiben.md). Für ein Paket, das alle bekommen sollen, ist ein Pull
Request an dieses Repository der bessere Weg: [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Geschichte des Paketservers](geschichte-des-paketservers.md)
- [Wie ein Release den Paketserver erreicht](wie-ein-release-den-paketserver-erreicht.md)
- [Die Quell-Repositorys](die-quell-repositorys.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
