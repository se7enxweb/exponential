---
title: "Das Hauptlayout: pagelayout.tpl und ein Stylesheet"
audience: builder, designer
category: books
order: 110
summary: Den Seitenrahmen der Clubwebsite Schritt für Schritt bauen: ein nacktes Pagelayout, ein Stylesheet über die Designeinstellungen laden, dann Kopfbereich, Menü, Inhaltsbereich und Fußbereich.
keywords: Pagelayout, Template, Stylesheet, Menü, Fußbereich, Seitenrahmen, pagelayout, template, ezdesign, ezcss_load, page_head, design.ini, FrontendCSSFileList, style sheet, menu, footer, module_result, Building book
type: help
---
# Das Hauptlayout: pagelayout.tpl und ein Stylesheet

Das Layout der TSCM-Website ist einfach: oben ein Banner und ein Menü, in der Mitte der wechselnde Inhalt, unten ein
kleiner Fußbereich. Der Seitenrahmen ist ein einziges Template, `pagelayout.tpl`. Das System sucht es immer zuerst in Ihrem
Design; gibt es dort keines, nimmt es das Standard-Template. [Template-Grundsätze](../buch-lernen/grundsaetze-der-templates.md)
erklärt die Idee; diese Seite setzt sie um.

## Ein Pagelayout auf das Nötigste reduziert

`extension/exp_tscm_design/design/tscm/templates/pagelayout.tpl`:

```
{*?template charset=utf-8?*}
<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}">
<head>
{ezcss_load( array(
    'core.css',
    ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ),
    ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' )
) )}
{include uri='design:page_head.tpl'}
</head>
<body>
Hello world!
</body>
</html>
```

Leeren Sie den Template-Override-Cache und laden Sie die Website: „Hello world!“. Sehen Sie sich den Quelltext der Seite an:
`page_head.tpl` hat den Titel, die Meta-Tags und die Link-Tags geschrieben. Es muss immer eingebunden werden. Was die Zeilen
bedeuten (das Buch erklärt jede):

- `{*?template charset=utf-8?*}` teilt der Engine den Zeichensatz der Datei mit. Schreiben Sie Templates in UTF-8.
- `{include uri='design:page_head.tpl'}` fügt die Standard-Kopfdatei ein (sie setzt den Seitentitel, die Meta-Informationen und mehr).
- `ezcss_load( ... )` schreibt den Link zu den Stylesheets. Das Buch schrieb ein `<link>` mit dem Operator `ezdesign`, der
  den Pfad des aktuellen Designs vor einen Dateinamen setzt; `ezcss_load` tut dasselbe für jede Datei der Liste, und der
  ezjscore-Packer bündelt sie ([CSS und JavaScript in einer Erweiterung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/css-und-javascript-in-einer-erweiterung.md)).
  Das `charset=latin1` des Buches und sein XHTML-1.0-Doctype sind verschwunden: Verwenden Sie `<!DOCTYPE html>` und UTF-8.

## Ein Stylesheet

Legen Sie den Stil des Clubs in `design/tscm/stylesheets/tscm.css` ab und tragen Sie ihn in
`extension/exp_tscm_design/settings/design.ini.append.php` ein:

```ini
[StylesheetSettings]
FrontendCSSFileList[]
FrontendCSSFileList[]=tscm.css
```

(Auch das Buch legte seine Regeln in `tscm.css` ab und lud sie über einen Link im Template.) Das CSS des Buches setzt einen
Seitenhintergrund, eine 732 Pixel breite, zentrierte Spalte `.main`, dunkle Streifen `.border` und `.menu` und einen
`.footer`. Schreiben Sie Ihr eigenes mit denselben Ansatzpunkten, in einem Design, in dem es wenige Farben gibt, die einmal
gewählt werden. Schreiben Sie nie `style=""` oder einen `<style>`-Block in ein Template
([Content Security Policy für Erweiterungsautoren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/content-security-policy-fuer-erweiterungen.md)).
Ein Anfang:

```css
body { margin: 0; background: #fff; color: #222; font: 100%/1.5 sans-serif; }
.main { max-width: 46rem; margin: 0 auto; }
.menu { background: #474747; text-align: center; padding: .25rem; }
.menu a { color: #ccc; margin: 0 .5rem; }
.content { padding: 2rem; }
.footer { background: #333; color: #bbb; padding: .25rem; text-align: center; font-size: .8rem; }
```

## Der Rahmen

Erweitern Sie den Body auf fünf Teile: Kopfbereich, Menü, Inhalt und Fußbereich.

```
<body>
<div class="main">
    <header><h1><a href={'/'|ezurl}>{$site.title|wash}</a></h1></header>
    {* The menu: the folders just below the root, fetched, not typed. *}
    {def $top=fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                              'class_filter_type', 'include',
                                              'class_filter_array', array( 'folder' ) ) )}
    <nav class="menu">
        {foreach $top as $item}
            <a href={$item.url_alias|ezurl}>{$item.name|wash}</a>
        {/foreach}
    </nav>
    <main class="content">
        {$module_result.content}
    </main>
    <footer class="footer">The Scandinavian Check Mates</footer>
</div>
</body>
```

An der Stelle von `{$module_result.content}` steht die Inhaltsansicht ([Inhalt und Darstellung](../buch-lernen/inhalt-und-darstellung.md)).
Das Buch schrieb sein Menü als statische Links; dieses Menü holt die Ordner, ein neuer Ordner ist also ein neuer
Menüeintrag, ohne dass das Template angefasst wird ([Inhalte holen](../buch-lernen/inhalte-in-templates-abrufen.md);
weitere Menüs: [häufige Template-Aufgaben](../buch-lernen/haeufige-aufgaben-mit-templates.md)). Cachen Sie das Menü mit
`{cache-block}` ([Caching in Templates](../buch-lernen/caching-in-templates.md)), wenn die Website wächst.

Weiter: [Sektionen und die Startseite](sektionen-und-die-startseite.md).

## Quelle und Lizenz

Bearbeitet nach dem Abschnitt über das Anlegen einer neuen Website und das Hauptlayout des Tutorials *Building an eZ publish site*
(geschrieben für eZ publish 3.2, 2003), ursprünglich von ez.no, 2024 von 7x erneut unter der GNU Free Documentation License
veröffentlicht, [Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x und Graham Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren
Version, ohne unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: für Exponential 6 neu geschrieben: Erweiterungen statt umbenannter Ordner, aktuelle Einstellungen und Operatoren, keine Inline-Stile und ein moderner Seitenrahmen.
