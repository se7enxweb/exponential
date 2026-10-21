---
title: "Lektion 1.3: Welches Template hat dieses Markup geschrieben?"
audience: trainer, designer, developer
category: training
order: 13
summary: Die Teilnehmenden schalten Template-Pfadkommentare für einen Siteaccess ein, finden im Seitenquelltext das Template hinter einem Teil der Seite, lesen den Override-Hinweis in Klammern und schalten die Kommentare wieder aus. 45 Minuten.
keywords: theme-design, template-pfadkommentare, ShowTemplatePathComments, START, STOP, quelltext anzeigen, debug-leiste, verwendete templates, template-cache, exp:ini set, theme design, template path comments, view source, debug bar, used templates, template cache
type: help
---
# Lektion 1.3: Welches Template hat dieses Markup geschrieben?

Am schnellsten lernt man ein Design kennen, indem man eine Seite fragt, aus welchen Dateien sie entstanden ist. Mit
eingeschalteten Template-Pfadkommentaren umschließt Exponential die Ausgabe jedes Templates mit zwei HTML-Kommentaren,
die die Datei nennen. Diese Lektion macht daraus den ersten Reflex der Teilnehmenden, wann immer eine Seite nicht so
aussieht wie erwartet.

> **Hinweis** Template-Pfadkommentare sind nur für die Entwicklung gedacht. Sie fügen jedem Template Markup hinzu und
> zerstören Feeds, JSON und XML. Lassen Sie sie für einen Live-Siteaccess nie eingeschaltet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 1: Designs und die Fallback-Kette](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Lektion 1.2](die-kette-eines-siteaccess-lesen.md); ein Browser, der den Seitenquelltext anzeigen kann |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Template-Pfadkommentare für einen Siteaccess mit `exp:ini set` ein- und ausschalten;
- den Template-Cache leeren, damit die Änderung wirkt;
- das Template hinter einem Stück Markup mit den Kommentaren `START` und `STOP` finden;
- den Namen in Klammern lesen als „diese Datei hat die Anfrage nach jenem Template beantwortet“.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Template-Pfadkommentare](../../../../../eng-us/features/6.0/template-path-comments.md) und
[Templates und Design, Teil 3](../../../../anleitungen/templates-und-design.md#3-sehen-welches-template-welches-markup-geschrieben-hat).

1. **Die Einstellung** (3 Minuten). `site.ini [TemplateSettings] ShowTemplatePathComments`, Standardwert `disabled`.
   Jeder andere Wert als `enabled` bedeutet aus. Setzen Sie sie nur für einen Siteaccess:

   ```bash
   php bin/php/console exp:ini set site/TemplateSettings/ShowTemplatePathComments enabled siteaccess:<site>
   php bin/php/ezcache.php --clear-tag=template
   ```

   Das Leeren des Caches ist nötig, weil kompilierte Templates die Prüfung enthalten.
2. **Die Kommentare lesen** (5 Minuten). Öffnen Sie eine Seite des Siteaccess, dann den Seitenquelltext, und suchen
   Sie nach `START`. Jedes Stück Ausgabe ist umschlossen:

   ```html
   <!-- START design/plain/templates/pagelayout.tpl -->
   ...
   <!-- STOP design/plain/templates/pagelayout.tpl -->
   ```

   Verschachtelte Kommentare zeigen die Include-Struktur: das Pagelayout, darin die Teile, die es einbindet, im
   Inhaltsbereich das View-Template.
3. **Die Klammer** (3 Minuten). Wenn ein Override geantwortet hat, folgt der angefragte Name in Klammern:
   `<!-- START .../full/article.tpl (node/view/full.tpl) -->`. Lesen Sie es laut vor: „Die Datei `article.tpl` hat die
   Anfrage `node/view/full.tpl` beantwortet.“ In Lektion 2.3 wird ein solcher Override geschrieben.
4. **Andere Werkzeuge** (4 Minuten). Derselbe Schalter steckt in der Exp Debug bar (Einstellungsgruppe Templates). In
   der rechten Spalte der Administration enthält **Kurzeinstellungen** die Schalter **Template-Debug** und **Liste der
   verwendeten Templates**, die die Templates einer Seite in der Debug-Ausgabe auflisten. Zeigen Sie, wo sie sind;
   dieser Kurs verwendet die Kommentare, weil sie in jedem Siteaccess funktionieren.

### Häufige Fragen

**Die Kommentare erscheinen nicht.** Der Template-Cache enthält noch kompilierte Templates von vorher; leeren Sie ihn
erneut. Prüfen Sie mit `exp:ini where site/TemplateSettings/ShowTemplatePathComments <site>`, dass der Wert für den
richtigen Siteaccess wirksam ist.

**Können Besucher die Kommentare sehen?** Ja, alle, die sich den Quelltext ansehen. Ein Grund mehr, sie nur auf
Schulungs- und Entwicklungs-Siteaccesses einzuschalten.

**Machen sie die Website langsamer?** Ein wenig, und die Ausgabe wird größer. Auf einer Schulungs-Website ohne Belang;
ein weiterer Grund, sie danach wieder auszuschalten.

### Stolperfallen

- **Einschalten in `settings/override/`.** Jeder Siteaccess bekommt die Kommentare, auch Feeds und die
  Administration. Verwenden Sie `siteaccess:<site>`.
- **Das Ausschalten vergessen.** Beenden Sie die Übung mit dem Ausschalten und prüfen Sie es, wie unten beschrieben.
- **Die gerenderte Seite statt des Quelltexts durchsuchen.** Kommentare sind auf der Seite unsichtbar; verwenden Sie
  „Quelltext anzeigen“, nicht den Element-Inspektor des Browsers, der Kommentare je nach Einstellung ausblenden kann.

## Praktische Übung

Auf Ihrer Schulungsinstallation, mit dem öffentlichen Siteaccess `<site>` aus Lektion 1.2 im Browser erreichbar (zum
Beispiel mit `php bin/php/console exp:velocity start`, siehe
[Lektion 2.4 von Train the Trainer](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-websites-der-uebungsumgebung-bereitstellen.md)).

1. Schalten Sie die Kommentare für Ihren Siteaccess ein:

   ```bash
   php bin/php/console exp:ini set site/TemplateSettings/ShowTemplatePathComments enabled siteaccess:<site>
   ```

2. Bestätigen Sie es: `php bin/php/console exp:ini where site/TemplateSettings/ShowTemplatePathComments <site>`. Die
   letzte Zeile nach `In effect:` lautet `ShowTemplatePathComments=enabled`.
3. Leeren Sie den Template-Cache: `php bin/php/ezcache.php --clear-tag=template`.
4. Öffnen Sie die Startseite des Siteaccess im Browser und öffnen Sie den Seitenquelltext.
5. Suchen Sie das erste `START`. Notieren Sie die Datei: Es ist das Pagelayout des Website-Designs.
6. Suchen Sie im Quelltext die Fußzeile der Seite und das nächste `START` darüber. Notieren Sie die Datei.
7. Öffnen Sie eine beliebige Inhaltsseite (einen Artikel oder einen Ordner) und suchen Sie das Template des
   Hauptinhaltsbereichs. Steht ein Name in Klammern?
8. Vergleichen Sie die Dateien aus den Schritten 5 bis 7 mit der Kette, die Sie in Lektion 1.2 gezeichnet haben: Aus
   welchem Design stammt jede?
9. Schalten Sie die Kommentare wieder aus:

   ```bash
   php bin/php/console exp:ini set site/TemplateSettings/ShowTemplatePathComments disabled siteaccess:<site>
   php bin/php/ezcache.php --clear-tag=template
   ```

10. Laden Sie den Seitenquelltext neu und prüfen Sie, dass kein Kommentar `START` übrig ist.

## Erwartetes Ergebnis

- Schritte 5 bis 7: Alle Teilnehmenden nennen drei Template-Dateien mit vollem Pfad, zum Beispiel das Pagelayout
  ihres Website-Designs und ein View-Template.
- Schritt 8: Jede Datei liegt in einem Design der Kette aus Lektion 1.2, und die Teilnehmenden können sagen, warum
  dieses Design gewonnen hat.
- Schritt 10: Der Quelltext enthält keine Kommentare `START`.
- Der Trainer prüft das, indem er zwei Teilnehmende bittet, einen ihrer Kommentare laut vorzulesen und auf die Datei
  auf der Festplatte zu zeigen.

## Quiz

1. Welche Einstellung umschließt die Ausgabe jedes Templates mit den Kommentaren `START` und `STOP`?
2. Warum wird der Template-Cache nach dem Umschalten der Einstellung geleert?
3. Was sagt Ihnen `<!-- START design/media/templates/full/article.tpl (node/view/full.tpl) -->`?
   A. Zwei Templates wurden beide gerendert B. `article.tpl` hat eine Anfrage nach `node/view/full.tpl` beantwortet
   C. Der Override ist fehlgeschlagen D. `node/view/full.tpl` bindet `article.tpl` ein
4. Nennen Sie zwei Arten von Ausgabe, die bei eingeschalteten Kommentaren nicht mehr funktionieren.
5. In welchem Bereich schalten Sie die Kommentare für einen Schulungs-Siteaccess ein, und warum?

<details>
<summary>Antworten</summary>

1. `site.ini [TemplateSettings] ShowTemplatePathComments=enabled`
   ([Template-Pfadkommentare](../../../../../eng-us/features/6.0/template-path-comments.md)).
2. Kompilierte Templates enthalten die Prüfung; ohne das Leeren laufen sie weiter ohne Kommentare.
3. **B.** Die Klammer nennt die `Source` der passenden Override-Regel.
4. Zwei beliebige aus: RSS und andere XML-Feeds, JSON-Ausgabe, alles, was das HTML streng parst.
5. `siteaccess:<site>`, damit nur dieser Siteaccess sie bekommt, nicht Feeds oder die Administration.

</details>

## Weiterführende Literatur

- [Template-Pfadkommentare](../../../../../eng-us/features/6.0/template-path-comments.md): Einzelheiten, Grenzen und der
  Schalter in der Debug-Leiste.
- [Exp Debug bar](../../../../../eng-us/features/6.0/exp-debug-bar.md): die Reiter für Templates, Zeitmessung und SQL.
- [Templates und Design, Teil 7: Fehlersuche in Templates](../../../../anleitungen/templates-und-design.md#7-templates-debuggen):
  die Checkliste, wenn eine Seite falsch aussieht.

## Verwandte Seiten

- [Modul 1: Designs und die Fallback-Kette](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
