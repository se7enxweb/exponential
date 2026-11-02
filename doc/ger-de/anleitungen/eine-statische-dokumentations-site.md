# Eine statische Dokumentations-Site

Ein Tutorial für Teams, die Produkt- oder Projektdokumentation in Exponential schreiben und sie öffentlich, schnell und
kostenlos haben wollen: eine statische Kopie auf GitHub Pages mit Suche, stabilen Adressen, einer Sitemap und
Weiterleitungen von alten Adressen.

[Anleitungen](README.md) · Verwandt: [SEO auf einer statischen Site](../hilfe/statisches-veroeffentlichen/seo-auf-einer-statischen-site.md),
[Statisches Veröffentlichen: CI und GitHub Actions](statisches-veroeffentlichen-ci-und-github-actions.md)

## Was Sie am Ende haben

Eine Dokumentations-Site, die Suchmaschinen gut erfassen, die im Browser sucht, alte Links funktionsfähig hält und
neben dem Code Ihres Projekts auf GitHub veröffentlicht wird.

## 1. Wo die Dokumentation liegt

Zwei übliche Wege:

- **Ein eigenes Repository**, `example/docs`, mit der Kopie im Branch `gh-pages`. Am einfachsten.
- **Das Repository des Projekts**, Branch `main`, Ordner `docs`. GitHub Pages liefert die Wurzel oder `/docs` aus;
  setzen Sie `Folder=docs` beim Ziel, und der Build wird nach `<ordner>/docs` geschrieben. Der Branch ist dann der des
  Projekts, und der Code darin wird von Ihren Entwicklern committet, nicht von Exponential; nehmen Sie also lieber einen
  eigenen Branch (`Branch=gh-pages`), wenn nichts dagegen spricht.

## 2. Das Ziel anlegen

Unter Setup > Statisches Veröffentlichen > **Neues Ziel**: der Siteaccess der Dokumentation, Repository und Branch,
relative Links und die **öffentliche Adresse**, die Sitemap, `robots.txt` und Canonical-Links brauchen.

```ini
[Target_docs]
Name=Documentation
SiteAccess=docs
Links=relative
BasePath=/docs/
PublicURL=https://example.github.io/docs/
Remote=https://github.com/example/docs.git
Branch=gh-pages
Search=enabled
Extras[]
Extras[]=nojekyll
Extras[]=404
Extras[]=robots
Extras[]=sitemap
Extras[]=redirects
Extras[]=manifest
```

![Der Einrichtungsassistent eines neuen Ziels](../hilfe/bilder/statisches-veroeffentlichen/wizard-light.webp)

## 3. Suche

`Search=enabled` lässt die Suchformulare der Site auf `search.html` zeigen, die in einem je Sprache geführten Index im
Browser sucht. `[StaticPublishSettings] SearchMaxBytes` (standardmäßig 2 MB) begrenzt den Index einer Sprache; darüber
behält der Index weniger Wörter je Seite, dann keine Anrisstexte, dann weniger Seiten.

## 4. Alte Adressen

Dokumentation wird umgebaut. Mit `Extras[]=redirects` schreibt der Build an jede alte Adresse (die früheren Adressen
umbenannter oder verschobener Knoten, eigene Aliasse und URL-Wildcards ohne `*`) eine kleine Seite, die den Besucher zur
neuen schickt, sodass Links in Issues, Foren und Suchergebnissen weiter funktionieren.

## 5. Bauen, Links prüfen, veröffentlichen

```bash
./console exp:static:build docs --dry-run
./console exp:static:build docs
./console exp:static:diff docs --check-links --mount=/docs/
./console exp:static:publish docs
```

Die Linkprüfung folgt jedem Link und jeder Datei jeder Seite unterhalb von `/docs/` wie ein Browser und listet auf, was
sie nicht findet; beheben Sie das vor dem Veröffentlichen. Dann im Repository unter **Settings > Pages** aus `gh-pages`
ausliefern.

![Die Vorschau eines Builds mit Änderungen, Bericht und Größe](../hilfe/bilder/statisches-veroeffentlichen/preview-light.webp)

## 6. Mit jedem Release veröffentlichen

Veröffentlichen Sie die Dokumentation, wenn das Projekt ein Release macht: aus dem Release-Job Ihrer CI
([CI und GitHub Actions](statisches-veroeffentlichen-ci-und-github-actions.md)), oder mit **Wenn Inhalte veröffentlicht
werden** in Schritt 8, sodass jede Änderung wenige Minuten später online ist. Jede Veröffentlichung wird als
`static/docs/<n>` getaggt, die Dokumentation eines Release ist also immer auffindbar, und man kann zu ihr zurückkehren.

## Nächster Schritt

[Von statischem HTML zu Exponential und zurück](von-statischem-html-zu-exponential-und-zurueck.md).
