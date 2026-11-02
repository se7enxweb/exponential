# Statische Websites auf GitHub Pages hosten mit Exponential 6.0.15+

Diese Anleitung zeigt, wie Sie eine kostenlose, schnelle, sichere öffentliche Kopie einer Exponential-Website auf
GitHub Pages bringen, während Redakteure weiter in Exponential arbeiten. Die Kopie ist statisch: einfache HTML-Seiten
und Dateien, kein PHP und keine Datenbank dahinter, also gibt es auf der öffentlichen Seite nichts zu aktualisieren,
zu patchen oder anzugreifen. Sie bauen und veröffentlichen sie neu, wenn sich der Inhalt ändert.

Sie ist für Website-Betreiber und Administratoren, die eine kostenlose öffentliche Website möchten, und für
Entwickler, die Websites von der Kommandozeile oder aus CI bauen und veröffentlichen. Diese erste Fassung behandelt
die Kommandozeile: ein Ziel in den Einstellungen, ein Build, eine lokale Vorschau, eine Prüfung und das
Veröffentlichen mit `exp:static:publish`. Dieselben Schritte haben Seiten in der Administration, unter
Setup > Statisches Veröffentlichen ([Statisches Veröffentlichen](../hilfe/statisches-veroeffentlichen/README.md)). Das
automatische Veröffentlichen bei GitHub kommt mit einem späteren Teil des statischen Veröffentlichens; die
Funktionsseite führt auf, was jeder Teil hinzufügt.

[Anleitungen](README.md) · Verwandt: [Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md),
[Das Build-Format](../spezifikationen/6.0/statisches-veroeffentlichen-build-format.md),
[Generator für den statischen Cache](../funktionen/6.0/generator-fuer-den-statischen-cache.md),
[Überblick für Administratoren](statisches-veroeffentlichen.md), [CI und GitHub Actions](statisches-veroeffentlichen-ci-und-github-actions.md),
[Fehlerbehebung](statisches-veroeffentlichen-fehlerbehebung.md)

## Was Sie bekommen

- Eine Kopie eines Front-Siteaccess in einem Ordner: jede Seite als `<pfad>/index.html`, jede Datei, die die Seiten
  brauchen, eine `404.html` aus der eigenen 404-Seite Ihrer Website, `robots.txt`, `sitemap.xml` und `.nojekyll`.
- Links, die überall funktionieren, wo der Ordner ausgeliefert wird: unter `https://<owner>.github.io/<repository>/`,
  an der Wurzel Ihrer eigenen Domain oder aus einer lokalen Vorschau.
- Eine Kopie, die nur enthält, was ein anonymer Besucher sieht: keine Debug-Ausgabe, keine Formular-Token, keine
  Sitzungskennungen. Eine Seite, die sich nicht bereinigen lässt, hält den Build an.
- Links auf das, was eine statische Kopie nicht kann (Suche, Anmeldung, Formulare), zeigen auf Ihre Live-Website, sodass
  sie dort, wo es eine gibt, weiter funktionieren.

## Was funktioniert und was sich ändert

| Auf der Live-Website | In der statischen Kopie |
|---|---|
| Seiten, Bilder, Downloads, Stylesheets, Skripte, Schriften | Dasselbe, als Dateien |
| Menüs, Layouts, Blöcke, Galerien | Dasselbe, so wie sie beim Build gerendert wurden |
| Suche, Anmeldung, Formulare, Kommentare, Shop | Links zur Live-Website (`LiveURL`) |
| Eine Seite mit Query-String (`?page=2`) | Ein Link zur Live-Website |
| Links auf Seiten, die die Kopie auslässt, Links in die Administration (`/admin/...`) | Links zur Live-Website, oder nur ihr Text mit `OutsideLinks=unlink` und `AdminLinks=unlink` (Schritt 7 des Assistenten) |
| Eine Änderung, die ein Redakteur veröffentlicht | Sichtbar nach dem nächsten Build und Push |
| Seiten, die nur angemeldete Benutzer sehen | Nie in der Kopie |

## Bevor Sie anfangen

- Exponential 6.0.15 oder neuer und eine Konsole auf dem Server (oder eine Kopie der Website, auf der Sie bauen).
- Ein Front-Siteaccess, den anonyme Besucher lesen können.
- Ein GitHub-Konto und ein Repository für die Kopie, im kostenlosen Tarif öffentlich. GitHub Pages erlaubt eine
  Website von 1 GB und Dateien von 100 MB; der Build warnt, bevor er eines davon erreicht.

## 1. Ein Ziel einrichten

Ein Ziel sagt, welcher Siteaccess veröffentlicht wird und wie. Tragen Sie es in
`settings/override/staticcache.ini.append.php` ein:

```ini
<?php /* #?ini charset="utf-8"?

[StaticPublishSettings]
Enabled=enabled
TargetList[]
TargetList[]=github-public

[Target_github-public]
Name=Öffentliche Website auf GitHub Pages
SiteAccess=site
Links=relative
BasePath=/meine-site/
PublicURL=https://owner.github.io/meine-site/
Remote=https://github.com/owner/meine-site.git
Branch=gh-pages

*/ ?>
```

- `BasePath` ist `/<repository>/` für eine Projekt-Website (`owner.github.io/meine-site/`) und leer für eine
  Benutzer-Website (`owner.github.io`) oder Ihre eigene Domain.
- `PublicURL` ist, wo die Kopie liegen wird. Die Sitemap, `robots.txt` und die Canonical- und Social-Card-Links
  nutzen es.
- Lassen Sie `Links=relative`, solange Sie keinen Grund haben: Relative Links funktionieren unter jedem Pfad, also kann
  derselbe Build an der Wurzel als Vorschau dienen und unterhalb von `/meine-site/` veröffentlicht werden.
- Erreicht der Server seine eigene öffentliche Adresse nicht, setzen Sie `CrawlURL` auf eine Adresse, die er erreicht
  (`http://127.0.0.1:8080/`). Links auf diese Adresse werden wie Links auf die öffentliche umgeschrieben.

Prüfen Sie es:

```bash
./console exp:static:targets
```

```text
Static publishing: switched on, 1 target(s)

  github-public        Öffentliche Website auf GitHub Pages
    siteaccess site, github-pages, links relative
    public at https://owner.github.io/meine-site/
    folder var/site/static-publish/github-public, not built yet
PASS
```

Ein Fehler (ein Siteaccess, den es nicht gibt oder der ein Administrations-Siteaccess ist, ein Token in `Remote`)
steht unter dem Ziel, und der Befehl endet mit `FAIL (exit 1)`.

## 2. Bauen

Versuchen Sie zuerst einen Probelauf. Er baut alles in einen Staging-Ordner, meldet, was sich ändern würde, und lässt
den Ordner des Ziels in Ruhe:

```bash
./console exp:static:build github-public --dry-run
```

Dann bauen Sie:

```bash
./console exp:static:build github-public
```

Der Build listet jede Seite, sobald sie gespeichert ist, dann eine Zusammenfassung. Das ist der erste Build der
Demo-Website, 560 Seiten, auf einer Kopie ihrer Installation (1. November 2026):

```text
Static publishing: build of github-public (site, full), run 20261101-105351-160345 in 183.2s
  Pages:  560 exported, 0 refused, 0 skipped, 39 excluded
  Files:  499 named by the pages, 2484 of the storage, 4 extras, 15 could not be collected
  Links:  26567 rewritten, 7062 to the live site, 5471 left as they are
  Size:   484.8M in 3547 files
  Taken out: 560 debug
  Diff:   3547 added, 0 changed, 0 removed, 0 unchanged
  Link check under /meine-site/: 18534 links in 562 files, 0 broken
  Written to var/site/static-publish/github-public
WARN 15 file(s) named by the pages could not be collected; their links go to the live site (see "files_failed").
PASS
```

Den größten Teil der Größe machen die Speicherbilder aus (`Assets=referenced-and-storage`); mit `Assets=referenced`
werden nur die Dateien kopiert, die die Seiten nennen. Die 39 ausgelassenen Seiten sind die Registrierung, der
Warenkorb und die Tag-Ansichten: Sie starten eine Sitzung für anonyme Besucher, der erste Build hat sie abgelehnt, und
das Ziel lässt sie aus:

```ini
ExcludePaths[]
ExcludePaths[]=/user/*
ExcludePaths[]=/shop/*
ExcludePaths[]=/tags/*
```

- **refused** (abgelehnt): Eine Seite trug etwas, das eine öffentliche Kopie nicht tragen darf (ein Sitzungs-Cookie,
  Debug-Ausgabe oder ein Formular-Token, das sich nicht herausnehmen ließ). Der Build schlägt fehl und schreibt
  nichts; die Seite wird mit dem Grund genannt. Korrigieren Sie die Seite oder lassen Sie sie mit `ExcludePaths[]` aus.
- **skipped** (übersprungen): Seiten, die es nur mit einem Query-String gibt, oder deren Pfad nicht auf jedem System
  ein Dateiname sein kann.
- **could not be collected** (nicht gesammelt): Dateien, die eine Seite nennt und die die Website einem anonymen
  Besucher nicht gibt (ein fehlendes Bild, ein geschützter Download). Ihre Links gehen zur Live-Website;
  `--format=json` führt sie unter `files_failed` auf.
- **to the live site** (zur Live-Website): Links auf die Suche, die Anmeldung, Formulare und die anderen Seiten, die
  die Kopie nicht enthält.

Um nach einer Änderung einige Seiten neu zu bauen, nennen Sie sie; der Rest des Builds bleibt:

```bash
./console exp:static:build github-public --paths=/about-us,/news
./console exp:static:build github-public --nodes=123
```

## 3. Vorschau

```bash
./console exp:static:serve github-public
```

```text
Serving /var/www/example/var/site/static-publish/github-public
at http://127.0.0.1:8090/meine-site/  (Ctrl+C stops it)
```

Öffnen Sie die Adresse im Browser. Die Vorschau liefert den Ordner unterhalb des Basispfads aus, wie es GitHub Pages
tun wird: Ein Ordner antwortet mit seiner `index.html`, eine fehlende Seite mit Ihrer `404.html`. `--base-path=/`
liefert ihn an der Wurzel aus, wie es eine eigene Domain täte. Die Vorschau lauscht nur auf diesem Rechner; `--host`
und `--port` ändern das.

## 4. Prüfen

```bash
./console exp:static:diff github-public --verify --check-links --mount=/ --mount=/meine-site/
```

`--verify` stellt sicher, dass der Ordner noch genau das enthält, was der Build geschrieben hat. `--check-links` folgt
jedem Link und jeder Datei jeder Seite und jedes Stylesheets, wie ein Browser es täte, unterhalb jedes `--mount`, und
listet, was es nicht findet. `404.html` wird nur unterhalb des Basispfads geprüft, für den sie gebaut wurde.

Ohne Optionen listet `exp:static:diff`, was der letzte Build gegenüber dem davor hinzugefügt, geändert und entfernt
hat.

## 5. Mit Git veröffentlichen

Der Ordner `var/site/static-publish/github-public` ist die Website und ihr eigenes Git-Repository. Legen Sie ein Token
für das Repository unter Setup > Geheimnisse ab (`github_pages_token`) und setzen Sie beim Ziel
`Credential=token:github_pages_token`, dann:

```bash
./console exp:static:publish github-public
```

Der Befehl committet nur die Dateien des Builds auf den Branch `gh-pages`, taggt den Commit `static/github-public/1`
und pusht beides, nie erzwungen. [Repositorys und Zugangsdaten](statisches-veroeffentlichen-repositorys-und-zugangsdaten.md)
erklärt die Anmeldung (Token, SSH-Deploy-Key oder die Zugangsdaten des Hosts) und was geschieht, wenn ein Push
scheitert.

Öffnen Sie dann auf GitHub die **Settings > Pages** des Repositorys, wählen Sie **Deploy from a branch**, den Branch
`gh-pages` und den Ordner `/ (root)` und speichern Sie. Nach ein, zwei Minuten liegt die Website unter
`https://owner.github.io/meine-site/`.

Danach baut und veröffentlicht ein Befehl:

```bash
./console exp:static:publish github-public --build
```

Schreiben Sie nie ein Token in `Remote` oder in die Adresse des Remotes; `exp:static:targets` lehnt das ab.

## Aus CI

Die Befehle geben mit `--format=json` ein JSON-Dokument aus (Schema `exp-static/1`) und enden mit Exit 0, wenn der
Build funktioniert hat, 1, wenn er fehlschlug, 2 bei einer falschen Kommandozeile und 3, wenn die Datenbank nicht
erreichbar ist. Ein CI-Job kann bauen, prüfen und pushen:

```bash
./console exp:static:build github-public --format=json --report=var/log/static-build.json
./console exp:static:diff github-public --check-links --format=json
GH_PAGES_TOKEN=... ./console exp:static:publish github-public --format=json     # Credential=token:env:GH_PAGES_TOKEN
```

## Mit dem Vorlagen-Repository beginnen

Für eine Site, die vollständig bei GitHub gebaut wird, ohne eigenen Server, beginnen Sie mit dem Vorlagen-Repository
[exponential-static-site-starter](https://github.com/se7enxweb/exponential-static-site-starter). Klicken Sie auf **Use this template**, stellen Sie
**Settings > Pages > Source** auf **GitHub Actions** und pushen Sie. Sein Workflow:

1. installiert Exponential mit Composer;
2. holt Ihre Site: eine SQLite-Datenbank und den Storage-Ordner aus `data/` und dazu Ihre Einstellungen, Erweiterungen
   und Designs aus `site/`, oder, wenn dort keine Datenbank liegt, ein Site-Paket, das `exp:install` installiert;
3. stellt die Site mit dem eingebauten Webserver von PHP auf `127.0.0.1` bereit und führt `exp:static:build` und
   `exp:static:diff --check-links` darauf aus;
4. übergibt die Kopie an die Pages-Actions von GitHub, die sie unter `https://<owner>.github.io/<repository>/`
   veröffentlichen.

Ein Token ist nicht nötig: Das Deployment nutzt die Rechte, die GitHub dem Workflow gibt. Das README der Vorlage
führt ihre Einstellungen und die Grenzen von GitHub Pages auf; dasselbe Skript baut die Kopie auch auf Ihrem eigenen
Rechner.

## Ansehen: die Demo-Site

Die Demo-Site von Exponential wird auf diesem Weg als statische Kopie unter [https://se7enxweb.github.io/exponential-demo-site-static/](https://se7enxweb.github.io/exponential-demo-site-static/)
veröffentlicht (in Kürze), aus dem Repository
[se7enxweb/exponential-demo-site-static](https://github.com/se7enxweb/exponential-demo-site-static). Ihre Seiten zeigen,
was eine statische Kopie behält (Layouts, Menüs, Galerien, Bilder und Downloads) und was sich ändert (die Suche läuft
über einen vorab gebauten Index im Browser; Formulare, Anmeldung und Shop entfallen).

## Fehlerbehebung

| Symptom | Ursache und Abhilfe |
|---|---|
| `Static publishing is switched off` | `[StaticPublishSettings] Enabled=enabled` setzen |
| `TargetList[] names no target` | Die Kennung zu `TargetList[]` hinzufügen; der Block allein reicht nicht |
| `No page was exported` | Der Build konnte die Website nicht abrufen; `CrawlURL` auf eine Adresse setzen, die der Server erreicht |
| Eine Seite ist `refused: the response set the session cookie` | Etwas auf dieser Seite startet eine Sitzung für anonyme Besucher (ein Formular, ein Warenkorb); die Seite auslassen oder ändern |
| Eine Seite ist `refused: debug output` | Debug-Ausgabe, die nicht der Standardbericht ist; die Debug-Ausgabe für die Adresse des Builds abschalten |
| Stylesheets fehlen unterhalb von `/meine-site/` | `Links=base-path` mit falschem `BasePath`; `Links=relative` nutzen oder `BasePath` korrigieren |
| Eine 404 auf `github.io` | GitHub Pages ist nicht auf den Branch eingestellt, oder der Push ist noch nicht veröffentlicht |
