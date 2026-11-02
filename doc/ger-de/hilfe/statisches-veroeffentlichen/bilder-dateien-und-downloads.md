---
title: "Bilder, Dateien und Downloads"
audience: admin, website-creator, editor, hosting
category: administration
order: 15
summary: Welche Bilder, Stylesheets, Skripte, Schriften und Downloads in eine statische Kopie kommen, wie Speicherbilder veröffentlichter Inhalte kopiert werden, was nie kopiert wird und wie ein Build in den Größengrenzen des Hosts bleibt.
keywords: Statisches Veröffentlichen, Bilder, Bildvarianten, Dateien, Downloads, Speicherbilder, Dateigröße, große Dateien, Größengrenze, static publishing, images, image aliases, files, downloads, assets, Assets=referenced, referenced-and-storage, storage images, file size, 100 MB, 1 GB, size limit, GitHub Pages, static site
type: help
---
# Bilder, Dateien und Downloads

## Was Sie am Ende haben

Eine Kopie mit jedem Bild und Download, den sie braucht, und nichts, was nicht öffentlich sein sollte.

## Was kopiert wird

- Jedes **Stylesheet, Skript, jede Schrift und jedes Bild**, das die Seiten nennen, einschließlich der gepackten
  Stylesheets und Skripte und der Dateien, die diese Stylesheets ihrerseits nennen.
- Jede **Bildvariante**, die eine Seite verwendet (ein Vorschaubild, eine große Fassung). Eine Variante, die noch nicht
  erzeugt wurde, wird unterwegs erzeugt, wie beim ersten Besucher.
- Jeder **Download**, auf den eine Seite verlinkt, wenn die Website ihn einem nicht angemeldeten Besucher ausliefert.
- Mit **Dateien: jedes Bild veröffentlichter Inhalte** (`Assets=referenced-and-storage`, Standard) außerdem jedes Bild
  im Speicher der Website, das zur veröffentlichten Version eines Inhalts gehört, den ein anonymer Besucher lesen darf,
  ob eine Seite darauf verlinkt oder nicht. Galerien und Bildseiten, die Bilder nachladen, funktionieren dann auch.

**Nur was die Seiten nennen** (`Assets=referenced`) kopiert die ersten drei und sonst nichts; die Kopie bleibt
kleiner.

## Was nie kopiert wird

- Bilder von **Entwürfen** und älteren Versionen.
- Die **Originale** hochgeladener Dateien, es sei denn, eine Seite verlinkt den Download und die Website liefert ihn an
  jeden aus.
- Alles von Inhalten, die ein nicht angemeldeter Besucher nicht lesen darf.
- Dateien ausgelassener Seiten.

## Größe

GitHub Pages erlaubt 1 GB je Website und 100 MB je Datei
([Grenzen und Bedingungen von GitHub Pages](grenzen-und-bedingungen-von-github-pages.md)). Die Vorschau zeigt vor dem
Veröffentlichen die Größe gegenüber der Grenze und die größte Datei; ein Build über den Grenzen wird nicht gepusht. So
wird er kleiner:

1. **Dateien** auf **Nur was die Seiten nennen** umstellen.
2. Sektionen mit großen Medien auslassen (Schritt 7).
3. Videos und große Downloads auf eine Videoplattform oder einen Download-Host legen und von der Seite verlinken.

## Nächster Schritt

[Build, Vorschau und Veröffentlichen](build-vorschau-und-veroeffentlichen.md).
