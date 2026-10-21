---
title: Zwei Wege zum Bauen
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 10
featured: true
summary: Exponential 6.0.15+ baut Seiten auf zwei Wegen, mit Template-Code in einer Design-Erweiterung und mit dem Layout-Editor ohne Code, und beide treffen sich in Blöcken und Template-Blöcken. Was jeder Weg ist, wer ihn verwendet, wann Sie welchen wählen und wie sie sich verbinden, gezeigt auf der echten Site Fit & Healthy.
keywords: zwei Wege zum Bauen, Templates, Design-Erweiterung, Template-Override, Layout-Editor, ohne Code, Blöcke, Zonen, Template-Block, Theme, Site-Bau, wann verwenden, kombinieren, two ways to build, templates, design extension, template override, Layouts editor, no code, blocks, zones, template block, theme, site building, when to use, combine
type: help
---
# Zwei Wege zum Bauen

Exponential 6.0.15+ bietet einer Site zwei Wege, gebaut zu werden, und die meisten echten Sites verwenden beide zugleich.

| | 1. Templates in einer Design-Erweiterung | 2. Der Layout-Editor |
|---|---|---|
| Was Sie anlegen | `.tpl`-Dateien, Stylesheets, Einstellungen, in Ihrer eigenen Erweiterung | Layouts: mit Blöcken gefüllte Zonen, in der Datenbank gespeichert |
| Werkzeug | ein Editor, ein Terminal, git | die Administration: **Layouts** |
| Wer | Designer, Entwickler | Website-Ersteller, Redakteure, Administratoren |
| Änderungen gehen live | nach Deployment und Leeren eines Cache | wenn Sie auf **Layout veröffentlichen** klicken |
| Versioniert | in git mit Ihrer Erweiterung | Entwürfe und veröffentlichte Versionen in der Datenbank; Export und Import als `.zip` (`exp:layouts`) |
| Reichweite | alles: Markup, Daten, Logik | was die Blocktypen und ihre Ansichtstypen bieten |

## Inhalt

1. [Weg 1: Templates in einer Design-Erweiterung](#weg-1-templates-in-einer-design-erweiterung)
2. [Weg 2: der Layout-Editor](#weg-2-der-layout-editor)
3. [Wo sich die beiden treffen](#wo-sich-die-beiden-treffen)
4. [Ein drittes Werkzeug: Einstellungen](#ein-drittes-werkzeug-einstellungen)
5. [Welcher Weg wann](#welcher-weg-wann)
6. [Was es kostet, gemessen](#was-es-kostet-gemessen)

## Weg 1: Templates in einer Design-Erweiterung

Ein Design ist ein Ordner mit Templates unter `extension/<name>/design/<design>/templates/`. Die Site Fit & Healthy ist das
Design `media` der Erweiterung `sevenx_themes_media`. Jeder Teil einer Seite ist dort ein Template:
`pagelayout.tpl` (die HTML-Seite), `pagelayout/header.tpl`, `pagelayout/footer.tpl`, die Inhaltsansichten unter
`content/views/`, die Block-Templates unter `explayouts/block/`.

Ein Template, das eine andere Erweiterung mitbringt, wird geändert, ohne es anzufassen: Legen Sie eine Datei mit demselben
Pfad in Ihre eigene Design-Erweiterung, die in `design.ini [ExtensionSettings] DesignExtensions[]` später aufgeführt ist
(die zuletzt aufgeführte gewinnt), und leeren Sie die Template-Caches. Templates verwenden die Template-Sprache von
Exponential: `{def}`, `{foreach}`, `fetch()`, `ezini()`, `|wash`, `|ezurl`.

Wählen Sie ihn für alles Strukturelle: das HTML der Seite, eine neue Art von Block, eine Ansicht einer Inhaltsklasse, ein
Menü, das seine Einträge holt, Markup, das auf jeder Seite gleich sein muss.

## Weg 2: der Layout-Editor

**Layouts** in der Administration baut Seiten per Ziehen und Ablegen aus Blöcken. Ein **Layout** hat einen Typ (seine Zonen,
zum Beispiel `header`, `post_header`, `main`, `pre_footer`, `footer`) und enthält in seinen Zonen **Blöcke**. Der Editor
bietet die in der `explayouts.ini` definierten Blocktypen an (Title, Text, Rich text, Image, Button, List, Grid, Gallery,
Slider, Hero, Card, Columns und mehr), jeden mit seinen **Ansichtstypen** und Optionen. **Layout-Zuordnungen** entscheiden,
welches Layout eine Seite bekommt (nach Knoten, Teilbaum, Pfad oder Inhaltsklasse).

Wählen Sie ihn für die Zusammenstellung von Seiten: Landingpages, Kampagnenseiten, ein Hero und drei Teaser, eine Liste der
neuesten Artikel, die Blöcke über der Fußzeile, alles, was ein Redakteur ohne Release neu anordnen können soll.

## Wo sich die beiden treffen

Die beiden Wege sind ein System:

- Jeder Block wird von einem **Template** dargestellt: `design:explayouts/block/<block type>.tpl` im Design. Ändern Sie dieses
  Template in Ihrer Erweiterung, und jeder Block dieses Typs ändert sich, überall.
- Ein **Template-Block** ist ein Block, dessen Inhalt ein Template ist, das Sie nennen. So setzt das gemeinsame Layout
  **Header / Footer** Kopf- und Fußzeile des Themes in jedes Layout: Das Layout entscheidet *wo*, das Template entscheidet
  *was* ([Wie die Kopfzeile aufgebaut ist](wie-die-kopfzeile-aufgebaut-ist.md)).
- **Gemeinsame Layouts** mit **verknüpften Zonen** lassen eine Menge von Blöcken auf vielen Seitentypen erscheinen
  ([Gemeinsame Layouts und Zonenverknüpfung](gemeinsame-layouts-und-zonenverknuepfung.md)).
- Die Templates lesen Inhalte mit `fetch()`, die Blöcke lesen sie über **Sammlungen** (handverlesene Einträge oder eine
  Abfrage auf den Baum). Beide zeigen denselben Inhaltsbaum.

## Ein drittes Werkzeug: Einstellungen

Manches ist keins von beidem: Das Kopfmenü dieses Themes ist eine Liste von Knoten-IDs in der `menu.ini`, gelesen vom
Template der Kopfzeile. Einstellungen werden unter **Setup > INI-Einstellungen** oder mit `./console exp:ini` geändert, ohne
Code ([Einen Eintrag zum Kopfmenü hinzufügen](kopfmenue-eintrag-hinzufuegen.md)).

## Welcher Weg wann

Die vollständige Tabelle steht unter [Welcher Weg: die Entscheidungstabelle](entscheidungstabelle.md). Kurz:

| Wenn die Änderung ist | Verwenden Sie |
|---|---|
| Wo Blöcke stehen und welche | den Layout-Editor |
| Wie ein Blocktyp überall aussieht | einen Template-Override von `explayouts/block/<type>.tpl` |
| Der Rahmen der Site (Kopfzeile, Fußzeile, HTML der Seite) | Templates; platzieren Sie ihn mit einem Template-Block |
| Die Einträge des Kopf- oder Fußzeilenmenüs dieses Themes | Einstellungen (`menu.ini`) oder, für die zweite Zeile der Fußzeile, Inhalte |
| Eine neue Art von Block | beides: eine Blockdefinition in der `explayouts.ini` plus ihr Template, dann bietet der Editor sie an |

## Was es kostet, gemessen

Auf einer Wegwerfkopie der Installation (der eingebaute Server von PHP, ein geteilter Host unter Last):

| Aufgabe | Weg | Schritte | Gemessene Zeit |
|---|---|---|---|
| Einen Eintrag zum Kopfmenü hinzufügen | Administration (Einstellungen) | 10 Aktionen | |
| Einen Eintrag zum Kopfmenü hinzufügen | Konsole | 2 Befehle | 1,3 bis 2,1 s + 2,0 bis 2,5 s |
| Einen Fußzeilen-Link hinzufügen | Inhaltsbaum | 9 Aktionen, kein Leeren eines Cache | |
| Erste Seite nach einem vollständigen Leeren von Templates und Inhalten | | | 2,9 bis 3,4 s |
| Eine Seite aus dem Seiten-Cache | | | 22 bis 102 ms |

Wie diese gezählt wurden: [Gemessene Schritte und Zeiten](gemessene-schritte-und-zeiten.md).

## Verwandte Seiten

- [Zwei Wege zum Bauen, alle Seiten](README.md)
- [Welcher Weg: die Entscheidungstabelle](entscheidungstabelle.md)
- [Beispiele zum Lebenszyklus](beispiele/README.md): 206 Aufgaben von der Planung bis zur Migration, jede auf beiden Wegen erledigt
- [Eine Kampagnenseite bauen, von Anfang bis Ende](kampagnenseite-bauen.md)
- [Einen Eintrag zum Kopfmenü hinzufügen](kopfmenue-eintrag-hinzufuegen.md)
- [Der Layout-Editor](../layout-editor.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
