---
title: "Statisches Veröffentlichen im Vergleich mit Static-Site-Generatoren"
audience: web-framework-developer, developer
category: web-frameworks
order: 531
summary: Ein ehrlicher Vergleich des statischen Veröffentlichens in Exponential mit Jekyll, Hugo, Eleventy, Astro und Headless-Lösungen - wo ein CMS, das statische Kopien veröffentlicht, gewinnt, und wo ein Generator das bessere Werkzeug ist.
keywords: Static-Site-Generator, SSG, Vergleich, Headless CMS, Jamstack, Statisches Veröffentlichen, static site generator, Jekyll, Hugo, Eleventy, Astro, Gatsby, Next.js export, headless CMS, comparison, Jekyll alternative, Netlify, static site, GitHub Pages, gh-pages, free hosting, free website, CNAME, custom domain, deploy, static publishing
type: help
---
# Statisches Veröffentlichen im Vergleich mit Static-Site-Generatoren

Wenn Sie Jekyll, Hugo, Eleventy oder Astro kennen, wird Ihnen das Ergebnis des statischen Veröffentlichens vertraut
vorkommen und der Weg dorthin anders. Diese Seite vergleicht beides ehrlich.

## Was Sie am Ende haben

Ein klares Bild, wann Sie das statische Veröffentlichen von Exponential nutzen, wann einen Generator und wann beides.

## Der grundlegende Unterschied

| | Static-Site-Generator | Statisches Veröffentlichen in Exponential |
|---|---|---|
| Inhalte liegen in | Markdown-Dateien in Git oder einem Headless CMS | dem Inhalts-Repository von Exponential, bearbeitet in seinem Editor |
| Wer bearbeitet | Entwickler, oder Redakteure über eine Git-basierte Oberfläche | Redakteure in einem vollständigen CMS, mit Versionen, Workflows, Rollen und Übersetzungen |
| Wie die Website entsteht | Templates erzeugen beim Build Dateien | die echte Website wird gerendert, dann als anonymer Besucher gecrawlt und umgeschrieben |
| Vorschau | ein lokaler Entwicklungsserver | die dynamische Website selbst, dazu eine Vorschau jedes Builds |
| Verlauf | Git-Commits der Quellen | Versionen in Exponential, dazu ein Commit mit Tag pro Veröffentlichung |
| Build-Geschwindigkeit | sehr schnell | ein Crawl; Minuten für Hunderte Seiten, Teil-Builds für einzelne Seiten |

## Wo statisches Veröffentlichen gewinnt

- **Redakteure, die keine Entwickler sind.** Sie bekommen einen echten Editor, Bilder, Beziehungen, Zeitsteuerung und Rollen.
- **Eine bestehende Exponential-Website.** Dieselben Templates erzeugen die dynamische und die statische Website; nichts wird doppelt geschrieben.
- **Gemischte Websites.** Die Suche läuft im Browser, Formulare können an die Live-Site senden, Mitgliederbereiche bleiben dynamisch.
- **Sicherheit.** Veröffentlicht wird nur, was ein anonymer Besucher sieht; Entwürfe und private Inhalte erreichen den Build nie.

## Wo ein Generator gewinnt

- **Docs-as-Code** mit Pull-Request-Prüfung von Markdown, wo ohnehin alle in Git bearbeiten.
- **Sehr große Websites, die sehr oft neu gebaut werden**, wo ein Crawl langsamer ist als das Rendern aus Dateien.
- **Gar kein Server**, nicht einmal zum Bearbeiten. Exponential braucht irgendwo PHP und eine Datenbank, und sei es auf
  einem Laptop oder in CI.

## Beides nutzen

Das Ergebnis sind einfache Dateien in einem Ordner, also liefert alles, was einen Ordner ausliefert, es aus: GitHub
Pages, GitLab Pages, Netlify, Cloudflare Pages oder rsync auf einen beliebigen Webserver. Sie können auch einen
Generator für Ihre Dokumentation behalten und die Marketing-Website aus Exponential veröffentlichen.

Details zum Ergebnis: [Das Build-Format](../../spezifikationen/6.0/statisches-veroeffentlichen-build-format.md).
Kommandozeile und CI: [Statisches Veröffentlichen auf der Kommandozeile und in CI](../php-entwickler/statisches-veroeffentlichen-auf-der-kommandozeile-und-in-ci.md).

## Jetzt Ihre eigene erstellen

Beurteilen Sie es an einer echten Website: [In 15 Minuten eine kostenlose Website](../statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md).
