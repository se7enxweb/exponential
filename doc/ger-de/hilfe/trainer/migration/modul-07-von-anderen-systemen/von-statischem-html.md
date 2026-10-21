---
title: "Lektion 7.3: Von einer statischen HTML-Website"
audience: trainer, admin, developer
category: training
order: 73
summary: Die Teilnehmenden migrieren eine kleine statische HTML-Website - die Dateien lesen oder crawlen, Seiten nach Template gruppieren, den Inhaltsbereich herauslösen und auf die vereinfachte Eingabe bereinigen, Bilder und Dokumente importieren, den Baum aus den Verzeichnissen aufbauen und die .html-Adressen mit Aliasen, Wildcards oder Webserver-Regeln erhalten. 60 Minuten.
keywords: migration zu exponential, statisches html, generator für statische websites, inhaltsextraktion, vereinfachte eingabe, zip-import, ordnerbaum, html-adressen, weiterleitungen, url-wildcard, migrating to exponential, static HTML, crawl, static site generator, content extraction, simplified input, XmlText, ZIP import, folder tree, .html addresses, redirects, url wildcard
type: help
---
# Lektion 7.3: Von einer statischen HTML-Website

Eine statische Website ist die einfachste Quelle: Jede Seite ist eine Datei, die Sie lesen können. Die Arbeit liegt
darin, die Inhalte vom ringsum wiederholten Layout zu trennen und die Adressen zu erhalten. Diese Lektion ist die
praktischste des Moduls: Die Teilnehmenden führen drei kleine Seiten durch die ganze Methode.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 7: Von anderen Systemen](README.md) |
| Dauer | 60 Minuten (15 Präsentation, 45 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren, Entwickler und Website-Ersteller |
| Voraussetzungen | [Lektion 2.3](../modul-02-inhalte-zuordnen/urls-weiterleitungen-und-seo.md); ein Redakteurs- oder Administratorkonto auf einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- zwischen dem Lesen der Dateien, dem Lesen der Quellen des Generators und dem Crawlen wählen;
- den Inhaltsbereich eines Seiten-Templates finden und ihn auf die vereinfachte Eingabe reduzieren;
- eine Verzeichnisstruktur in einen Ordnerbaum mit Remote-IDs aus den alten Pfaden umwandeln;
- `.html`-Adressen mit einer Regel oder einem Alias pro Seite erhalten.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Statische HTML-Websites und Exponential](../../../../installation/migration/statisches-html.md).

1. **Lesen oder crawlen** (3 Minuten). Die Dateien einer handgemachten Website; die Markdown-Quellen eines Generators
   für statische Websites, nicht das erzeugte HTML; ein Crawl (Sitemap, Links, Server-Log), wenn nur die Live-Website
   existiert.
2. **Herauslösen** (5 Minuten). Gruppieren Sie Seiten nach Template; suchen Sie pro Template den Inhaltsbereich
   (`<main>`, `<article>`, ein Inhalts-`div`) sowie Titel, Datum, Autor und Beschreibung. Lassen Sie Navigation,
   Skripte, Inline-Styles und Layout-Tabellen weg; behalten Sie Überschriften, Absätze, Listen, Tabellen, Links und
   Bilder: die vereinfachte Eingabe, die der Kernel in XmlText umwandelt.
3. **Baum und Medien** (3 Minuten). Die Verzeichnisse sind meist die Navigation. Bilder und verlinkte Dokumente werden
   zu Bild- und Dateiobjekten; ein Ordner davon kann über den ZIP-Importer der Upload-Seite hereinkommen, wobei seine
   Ordnerstruktur erhalten bleibt.
4. **Adressen** (4 Minuten). Alte Adressen enden auf `.html` oder sind Verzeichnisse mit `index.html`. Ein
   weiterleitender Alias pro Seite oder, wenn sie einem Muster folgen, eine URL-Wildcard oder eine Webserver-Regel,
   die `.html` entfernt.

### Häufige Fragen

**Kann Exponential auch eine statische Kopie erstellen?** Ja: `exp:makestaticcache` schreibt statische Seiten
öffentlicher Siteaccesses (Anleitung, Abschnitt 5); siehe Lektion 8.1.

**Was ist mit Seiten, die mit Tabellen für das Layout gebaut wurden?** Lassen Sie die Layout-Tabellen weg und behalten
Sie die Inhalte; echte Datentabellen bleiben Tabellen.

**Ab wie vielen Seiten lohnt sich ein Skript?** Die Anleitung nennt keine Zahl. Bei einer Handvoll können Redakteure
die bereinigten Inhalte einfügen; darüber hinaus das Skriptgerüst aus Lektion 3.3 mit JSON-Zeilen.

### Stolperfallen

- **Ganze Seiten einfügen.** Navigation und Fußzeilen landen dann in jedem Artikel.
- **Bilder auf dem alten Server lassen.** Verweise brechen, wenn die alte Website abgeschaltet wird; importieren Sie
  sie.
- **Nicht prüfen, was die Alias-Bereinigung aus `.html` macht.** Testen Sie eine Adresse, bevor Sie hundert
  schreiben.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation. Die „alte Website“ sind drei Seiten, die der Trainer austeilt (oder
die die Teilnehmenden zuerst in einem Texteditor schreiben): `about/index.html`, `about/team.html` und
`products/widgets.html`, jede mit einem Kopfbereich, einem Menü, einem `<main>` mit einer Überschrift, zwei Absätzen
und einem Bild sowie einer Fußzeile.

1. Öffnen Sie jede Seite in einem Texteditor und markieren Sie den Inhaltsbereich und die Teile, die erhalten bleiben
   (Titel, Beschreibung).
2. Schreiben Sie den bereinigten Inhalt von `about/team.html`: nur Überschriften, Absätze, Listen, Links und der
   Bildverweis.
3. Schreiben Sie den Baum auf, den Sie aufbauen werden: welche Ordner, welche Seiten und die Remote-ID jeder Seite,
   zum Beispiel `html-/about/team.html`.
4. Legen Sie in der Administration unterhalb Ihres Kursordners die Ordner **About** und **Products** an (klicken Sie
   auf **Neu erstellen** und wählen Sie **Folder**) und veröffentlichen Sie jeden.
5. Legen Sie die Seite **Team** unterhalb von **About** als Artikel an (oder mit der Klasse, die Ihre
   Schulungswebsite für Seiten verwendet) und fügen Sie den bereinigten Inhalt ein. Veröffentlichen Sie.
6. Öffnen Sie die URL-Alias-Seite des Knotens **Team** (`/admin/content/urlalias/<node id>`). Geben Sie unter **Neuer
   Alias** `about/team.html` als **URL alias name** ein, lassen Sie **Der Alias soll zum Ziel weiterleiten** angehakt
   und klicken Sie auf **Erstellen**. Wenn die Seite meldet, dass Zeichen geändert wurden, notieren Sie, was sie aus
   dem Punkt gemacht hat.
7. Fragen Sie die alte Adresse ab und prüfen Sie Status und Ziel (ersetzen Sie den Host durch die Adresse Ihrer
   Schulungswebsite):

   ```bash
   curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' http://localhost/about/team.html
   ```

8. Schreiben Sie in Ihre Notizen die URL-Wildcard oder die Webserver-Regel, die alle `.html`-Seiten einer großen
   statischen Website auf einmal abdecken würde, und das Risiko dabei.
9. Entfernen Sie den in Schritt 6 angelegten Alias (wählen Sie ihn aus und klicken Sie auf **Ausgewählte entfernen**),
   damit die Schulungswebsite sauber bleibt.

## Erwartetes Ergebnis

- Schritt 2: Inhalte ohne Menü, Fußzeile, Skripte oder Inline-Styles.
- Schritte 4 und 5: Die Ordner und die Seite sind unterhalb des Kursordners veröffentlicht.
- Schritte 6 und 7: Die alte Adresse antwortet mit einer Weiterleitung (301) auf die Adresse des neuen Knotens, oder
  die Person hat notiert, wie der Alias bereinigt wurde und was das für die alte Adresse bedeutet.
- Schritt 8: eine Wildcard oder Webserver-Regel, die `.html` entfernt, mit dem Risiko, dass sie Adressen trifft, die
  sich nicht ändern sollen.
- Der Trainer prüft Schritt 7 auf zwei Bildschirmen.

## Quiz

1. Eine Website wurde mit einem Generator für statische Websites gebaut. Was lesen Sie: das erzeugte HTML oder die
   Quellen?
2. Welche Elemente behält die vereinfachte Eingabe?
   A. Skripte und Styles B. Überschriften, Absätze, Listen, Tabellen, Links und Bilder C. Navigationsmenüs
   D. Layout-Tabellen
3. Woher kommt der Ordnerbaum einer statischen Website meist?
4. Nennen Sie zwei Wege, hundert `.html`-Adressen zu erhalten, die einem Muster folgen.
5. Welches Werkzeug bringt einen Ordner mit Bildern herein und erhält dabei seine Ordnerstruktur?

<details>
<summary>Antworten</summary>

1. Die Quellen (Markdown mit Front Matter)
   ([Anleitung für statisches HTML, Abschnitt 1](../../../../installation/migration/statisches-html.md#1-die-dateien-lesen-oder-crawlen)).
2. **B** ([Abschnitt 2](../../../../installation/migration/statisches-html.md#2-die-inhalte-extrahieren)).
3. Aus der Verzeichnisstruktur (Abschnitt 2).
4. Eine URL-Wildcard oder eine Webserver-Regel, die `.html` entfernt
   ([Abschnitt 4](../../../../installation/migration/statisches-html.md#4-adressen)).
5. Der ZIP-Importer der Upload-Seite ([Abschnitt 3](../../../../installation/migration/statisches-html.md#3-import)).

</details>

## Weiterführende Informationen

- [Statische HTML-Websites und Exponential](../../../../installation/migration/statisches-html.md): die Methode vollständig.
- [JavaScript- und Node.js-Anwendungen](../../../../installation/migration/javascript-anwendungen.md): Markdown- und MDX-Quellen
  von Generatoren für statische Websites.
- [Medien und Uploads](../../../medien-und-uploads.md): viele Dateien auf einmal hochladen.

## Verwandte Seiten

- [Modul 7: Von anderen Systemen](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
