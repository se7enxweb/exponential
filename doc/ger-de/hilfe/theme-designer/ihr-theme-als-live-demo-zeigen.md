---
title: "Ihr Theme als statische Live-Demo zeigen"
audience: theme-designer, designer
category: theme-design
order: 44
summary: Zeigen Sie Ihr Exponential-Theme Kunden und Nutzern als Live-Demo auf GitHub Pages, kostenlos und immer erreichbar, mit Beispielinhalten und einem Link zum Repository des Themes.
keywords: Theme-Designer, Designer, Theme-Demo, Live-Demo, Schaufenster, Designsystem, kostenlose Website, kostenloses Hosting, statische Website, Statisches Veröffentlichen, theme designer, theme demo, live demo, showcase, free website, free hosting, static site, GitHub Pages, gh-pages, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing
type: help
---
# Ihr Theme als statische Live-Demo zeigen

Bildschirmfotos verkaufen ein Theme schlechter als eine Website, durch die man klicken kann. Mit statischem
Veröffentlichen installieren Sie Ihr Theme auf einer Exponential-Site mit Beispielinhalten und veröffentlichen eine
statische Kopie davon auf GitHub Pages: eine Live-Demo, die nichts kostet und nie ein Server-Update braucht.

## Was Sie am Ende haben

- Eine Live-Demo unter `https://<sie>.github.io/<theme>-demo/`, verlinkt aus der README Ihres Themes und seiner Paketseite.
- Jede Fassung der Demo mit einem Tag, damit Sie zeigen können, wie sich das Theme zwischen Releases verändert hat.

## Einrichten

1. Legen Sie eine Site mit Ihrem Theme und realistischen Beispielinhalten an: Artikel mit Bildern, eine Ordnerliste,
   eine lange Seite, eine Seite in einer zweiten Sprache, wenn das Theme das kann.
2. Legen Sie ein Ziel für ihren Siteaccess an. Verwenden Sie relative Links, damit die Demo unter `/<repository>/`
   funktioniert. Siehe [Links und Basispfad](../statisches-veroeffentlichen/links-und-basispfad.md).
3. Bauen Sie, öffnen Sie die Vorschau und prüfen Sie jeden Seitentyp in Telefon- und Desktop-Breite.
4. Veröffentlichen Sie. Tragen Sie die Adresse in die README Ihres Themes ein.

## Design-Prüfungen, die der Build Ihnen liefert

- **Fehlende Dateien:** Die Linkprüfung jedes Builds listet Stylesheets, Schriften oder Bilder, die Ihre Templates
  nennen, die es aber nicht gibt.
- **Absolute Links:** Ein Template, das `/design/...` von Hand schreibt, funktioniert trotzdem, weil der Build es
  umschreibt, aber `ezurl` und `ezdesign` sind der saubere Weg.
- **Größe:** Der Bericht zeigt, wie schwer Ihre Seiten und Bilder sind.

## Was in einer statischen Kopie anders aussieht

Formulare, Kommentare, die Anmeldung und der Warenkorb haben keinen Server dahinter. Sie werden ausgeblendet oder durch
einen kurzen Text mit einem Link zur Live-Site ersetzt. Gestalten Sie diese Zustände bewusst; siehe
[Suche, Formulare und Kommentare auf einer statischen Site](../statisches-veroeffentlichen/suche-formulare-und-kommentare-auf-einer-statischen-site.md).

Die Suche funktioniert weiter: Der Build schreibt eine Suchseite, die im Browser sucht. Gestalten Sie sie wie Ihre Suchergebnisse.

## Ein Beispiel ansehen

Die Demo-Website: [https://se7enxweb.github.io/exponential-demo-site-static/](https://se7enxweb.github.io/exponential-demo-site-static/) (in Kürze).

## Jetzt Ihre eigene erstellen

Folgen Sie [In 15 Minuten eine kostenlose Website](../statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md)
mit der Demo-Site Ihres Themes.
