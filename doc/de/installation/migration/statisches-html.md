---
title: Statische HTML-Sites und Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 49
summary: Eine handgemachte oder generierte statische HTML-Site in Exponential 6 übernehmen und zurück – die Dateien lesen oder die Site crawlen, den Inhaltsbereich extrahieren, HTML zu XmlText, Bilder und Dokumente, .html-Adressen erhalten und aus Exponential eine statische Kopie erzeugen.
keywords: statisches HTML, Static-Site-Generator, Jekyll, Hugo, Crawlen, HTML-Extraktion, XmlText, .html-Adressen, Weiterleitungen, makestaticcache, statische Kopie, Migration, static HTML, static site generator, crawl, HTML extraction, redirects, static copy, migration
type: install
---
# Statische HTML-Sites und Exponential

Eine statische Site ist die einfachste Quelle: Jede Seite ist eine Datei, die Sie lesen können. Die Arbeit besteht
darin, die Inhalte vom wiederkehrenden Layout drumherum zu trennen und die Adressen zu erhalten.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Zuordnung](inhaltsmodell-zuordnen.md) · [Werkzeuge](exponential-tools.md)

## 1. Die Dateien lesen oder crawlen

| Sie haben | Lesen Sie |
|---|---|
| Die Dateien (FTP, Git) einer handgemachten Site | die `.html`-Dateien direkt |
| Einen Static-Site-Generator (Jekyll, Hugo, Eleventy ...) | die **Quellen** (Markdown mit Front Matter), nicht das generierte HTML; siehe [JavaScript- und Node.js-Anwendungen, 2](javascript-anwendungen.md#2-inhalte-nach-exponential) zu Markdown |
| Nur die laufende Site | einen Crawl: Sitemap, Links, Serverprotokoll; speichern Sie jede Seite |

## 2. Die Inhalte extrahieren

1. **Gruppieren Sie die Seiten nach Template** (Seiten, die dasselbe Layout teilen).
2. **Bestimmen Sie pro Template den Inhaltsbereich** (ein `<main>`, ein `<article>`, ein `<div id="content">`) und die Teile, die Sie behalten:
   Titel (`<h1>` oder `<title>`), Datum, Autor, Meta-Beschreibung.
3. **Bereinigen Sie das HTML**: Entfernen Sie Navigation, Skripte, Inline-Styles und Layouttabellen; behalten Sie Überschriften, Absätze, Listen,
   Tabellen, Links und Bilder. Reduzieren Sie es auf die vereinfachte Eingabe von Exponential (`<p>`, `<strong>`, `<em>`, `<a href>`,
   `<h1>`-`<h6>`, `<ul>`/`<ol>`/`<li>`, `<table>`/`<tr>`/`<td>`), die der Kernel in XmlText umwandelt
   ([Werkzeuge, Abschnitt 4](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst)).
4. **Bilder und Dokumente**: Sammeln Sie jedes `<img src>` und jede verlinkte PDF- oder Office-Datei, importieren Sie sie als Bild- und Dateiobjekte
   und ersetzen Sie die Verweise durch Einbettungen oder Links auf die neuen Objekte.
5. **Der Baum**: Die Verzeichnisstruktur ist in der Regel die Navigation: Aus `/products/widgets/index.html` werden ein Ordner
   *Products*, ein Ordner *Widgets* und so weiter.

## 3. Import

Schreiben Sie JSON-Zeilen (Remote-ID aus dem alten Pfad, zum Beispiel `html-/products/widgets.html`) und importieren Sie dann mit dem
[Skript-Grundgerüst](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst) oder dem Importer von xrowextract. Ein Ordner mit
Bildern und Dokumenten kann über den ZIP-Import der Upload-Seite übernommen werden, wobei seine Ordnerstruktur erhalten bleibt.

## 4. Adressen

Statische Adressen enden auf `.html` oder sind Verzeichnisse mit `index.html`. Die Aliase von Exponential sind saubere Pfade
(`products/widgets`). Legen Sie für jede alte Adresse einen weiterleitenden eigenen URL-Alias am neuen Knoten an und prüfen Sie, was die
Alias-Bereinigung aus dem Punkt und der Endung macht; wenn die alten Adressen einem Muster folgen, deckt ein URL-Platzhalter oder eine
Webserver-Regel (`.html`, `index.html` entfernen) sie alle ab ([Checkliste, 4](checkliste.md#4-urls-und-weiterleitungen)).

## 5. Von Exponential zu einer statischen Kopie

Exponential kann statische Seiten seiner öffentlichen Siteaccesses schreiben:
`./console exp:makestaticcache` crawlt sie und erzeugt statische Seiten (`--site`, `--max-pages`, `--max-depth`). Für
eine Archivkopie einer Site, die Sie abschalten, funktioniert auch ein Crawl der laufenden Site mit einem Spiegelungswerkzeug; behalten Sie
das Storage-Verzeichnis für die Originale der Bilder.

## 6. Checkliste

- [ ] Adressliste vollständig (Dateien, Sitemap, Protokoll); Seiten nach Template gruppiert.
- [ ] Extraktor pro Template getestet; HTML auf die vereinfachte Eingabe reduziert.
- [ ] Bilder und Dokumente importiert; Verweise umgeschrieben.
- [ ] Baum aus der Verzeichnisstruktur aufgebaut; Remote-IDs aus den alten Pfaden.
- [ ] `.html`-Adressen weitergeleitet und getestet.

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../eng-us/README-LICENSE.md)).
