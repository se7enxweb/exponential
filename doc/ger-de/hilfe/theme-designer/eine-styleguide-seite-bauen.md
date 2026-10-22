---
title: "Eine Styleguide-Seite bauen"
audience: theme-designer
category: theme-design
order: 12
summary: Wie Sie einen lebenden Styleguide in einem Exponential-Theme bauen - eine Seite, die jeden Token und jede Komponente mit den echten Templates und Stylesheets des Themes zeigt, gebaut mit einem Knoten-Override und Template-Teilen und von Navigation und Suche ferngehalten.
keywords: Theme-Designer, Styleguide, lebender Styleguide, Musterbibliothek, Komponentenseite, Override, Remote-ID des Knotens, Tokens, Farbmuster, Design-Review, theme designer, style guide, living style guide, pattern library, component page, override, node remote id, swatches, design review
type: help
---
# Eine Styleguide-Seite bauen

Diese Seite ist für Theme-Designer (mit einem Entwickler, wenn Sie Templates nicht selbst schreiben), die eine Seite
möchten, die jeden Token und jede Komponente des Themes zeigt, gezeichnet von den eigenen Templates und Stylesheets des
Themes. Anders als ein Styleguide im Designtool kann dieser nicht vom Code abdriften: Ändert sich eine Komponente, ändert
sich die Seite. Hier beginnen Reviews, und hier sehen Redakteure, was das Design bietet.

## Inhalt

1. [Was die Seite zeigt](#was-die-seite-zeigt)
2. [Wie sie gebaut wird](#wie-sie-gebaut-wird)
3. [Schritt für Schritt](#schritt-für-schritt)
4. [Tokens ohne Inline-Stile zeigen](#tokens-ohne-inline-stile-zeigen)
5. [Aus dem Weg halten](#aus-dem-weg-halten)
6. [Die Seite nutzen](#die-seite-nutzen)

## Was die Seite zeigt

1. **Tokens:** die Farbpalette mit jedem Rollennamen, die typografische Skala, die Abstandsskala, der Radius.
2. **Typografie:** jede Überschriftenebene, Fließtext, Listen, Zitate und Tabellen, so wie formatierter Text sie
   darstellt.
3. **Komponenten:** jeder Eintrag des [Komponenteninventars](komponenteninventar.md), in jeder Variante und jedem
   Zustand, der sich statisch zeigen lässt.
4. **Ansichten:** einige echte Objekte in jedem Ansichtsmodus (line, card, embed).

## Wie sie gebaut wird

Die Seite ist ein normales Inhaltsobjekt (zum Beispiel ein Ordner namens "Styleguide"), dessen Vollansicht über einen
Override, der die Remote-ID des Knotens prüft, ein eigenes Template erhält. Die Override-Technik ist in
[Einen einzelnen Knoten überschreiben](../designer/vorlagen/einen-einzelnen-knoten-ueberschreiben.md) dokumentiert; eine Remote-ID
hält die Regel funktionsfähig, wenn der Inhalt auf eine andere Installation umzieht.

## Schritt für Schritt

1. Legen Sie auf der Entwicklungsinstallation einen Ordner "Styleguide" an einer Stelle an, die Besucher nicht
   durchblättern (siehe [Aus dem Weg halten](#aus-dem-weg-halten)). Geben Sie ihm eine lesbare Remote-ID, oder bitten
   Sie den Entwickler, ihn mit einer solchen aus dem Inhaltspaket des Themes anzulegen
   ([Demo-Inhalte für ein Theme](../designer/veroeffentlichung/demo-inhalte.md)).
2. Ergänzen Sie den Override in `settings/override.ini.append.php` Ihrer Design-Erweiterung:

   ```ini
   [mytheme_style_guide]
   Source=node/view/full.tpl
   MatchFile=full/style_guide.tpl
   Subdir=templates
   Match[node_remote_id]=mytheme-style-guide
   ```

3. Schreiben Sie `design/mytheme/override/templates/full/style_guide.tpl`. Bauen Sie es aus denselben Template-Teilen,
   die das Theme verwendet, damit die Seite das echte Markup zeigt:

   ```tpl
   {* Style guide: every component, drawn by the theme's own parts *}
   <article class="mytheme-styleguide">
     <h1>{'Style guide'|i18n( 'design/mytheme' )}</h1>

     <section aria-labelledby="sg-buttons">
       <h2 id="sg-buttons">{'Buttons'|i18n( 'design/mytheme' )}</h2>
       {include uri='design:parts/button.tpl' label='Primary' variant='primary'}
       {include uri='design:parts/button.tpl' label='Secondary' variant='secondary'}
     </section>

     <section aria-labelledby="sg-cards">
       <h2 id="sg-cards">{'Cards'|i18n( 'design/mytheme' )}</h2>
       {foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 6 ) ) as $child}
         {node_view_gui content_node=$child view='card'}
       {/foreach}
     </section>
   </article>
   ```

4. Legen Sie einige Objekte der Stressfälle unter den Styleguide-Ordner, damit der Kartenabschnitt echte Extreme zeigt
   ([Mit echten Inhalten gestalten](mit-echten-inhalten-gestalten.md#die-stressfälle)).
5. Leeren Sie den Cache der Template-Overrides, weil die Template-Datei neu ist (eine Aufgabe für den Entwickler:
   [Caching für Designer](../designer/theme-design/caching-fuer-designer.md)), und öffnen Sie die Seite.

## Tokens ohne Inline-Stile zeigen

Auf einer Seite mit Farbmustern schleichen sich Inline-Stile ein (`style="background: #0b5cad"`). Tun Sie das nie: Es
bricht die Regel gegen Inline-CSS und eine strenge Content Security Policy. Geben Sie stattdessen jedem Farbmuster eine
Klasse, in einem kleinen Stylesheet des Styleguides:

```css
/* design/mytheme/stylesheets/styleguide.css */
.mytheme-swatch            { inline-size: 6rem; block-size: 3rem; border: 1px solid var(--site-line);
                             border-radius: var(--site-radius); }
.mytheme-swatch--ink       { background: var(--site-ink); }
.mytheme-swatch--surface   { background: var(--site-surface); }
.mytheme-swatch--accent    { background: var(--site-accent); }
.mytheme-swatch--muted     { background: var(--site-muted); }
```

Weil die Farbmuster die Tokens verwenden, zeigt die Seite im Dunkelmodus ohne weitere Arbeit die dunkle Palette. Laden
Sie `styleguide.css` nur, wo es gebraucht wird, oder nehmen Sie die paar Zeilen in der gepackten Datei hin; hängen Sie
keine Versionsabfrage an seinen Link ([Keine Versionsabfragen an Asset-Links](../designer/theme-design/keine-versionsparameter-an-asset-links.md)).

## Aus dem Weg halten

- Legen Sie den Ordner dorthin, wo Abrufe der Navigation nicht hinreichen, oder in eine Sektion, die das Hauptmenü
  auslässt.
- Verbergen Sie ihn vor Besuchern, wenn die Website öffentlich ist: Üblich ist eine Sektion ohne Leserecht für anonyme
  Besucher, über die der Administrator entscheidet ([Sektionen, Rollen und Richtlinien](../../../eng-us/help/cms-developers/sections-roles-and-policies.md)).
- Halten Sie ihn auf dieselbe Weise aus Suchergebnissen und Sitemaps heraus.

## Die Seite nutzen

- Beginnen Sie jedes Design-Review hier, hell und dunkel, in den drei Breiten
  ([Mit Kunden und Reviews arbeiten](mit-kunden-und-reviews-arbeiten.md)).
- Zeigen Sie Redakteuren, welche Komponenten und Ansichtstypen für Blöcke es gibt.
- Vergleichen Sie einmal pro Release mit dem Inventar; alles, was auf der Seite steht, aber nicht im Inventar, oder
  umgekehrt, ist abgedriftet.

## Verwandte Seiten

- [Komponenteninventar](komponenteninventar.md)
- [Design-Tokens](../designer/theme-design/design-tokens.md)
- [Grundlagen der Overrides](../designer/vorlagen/override-grundlagen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
