---
title: Landmarks und Überschriften
audience: designer
category: theme-design
order: 460
summary: Die Seitenstruktur, in der Screenreader-Benutzer navigieren: Landmark-Elemente und -Rollen im Pagelayout, ein einziger Hauptbereich, beschriftete Navigation, ein h1 pro Seite und Überschriftenebenen in View-Templates und Layout-Blöcken.
keywords: Landmarks, Überschriften, h1, Reihenfolge der Überschriften, main, nav, header, footer, aside, role, aria-label, Seitenstruktur, Navigation mit dem Screenreader, Dokumentgliederung, landmarks, headings, heading order, page structure, screen reader navigation, document outline
type: help
---
# Landmarks und Überschriften

Diese Seite ist für Designer, die das Pagelayout und View-Templates schreiben. Screenreader-Benutzer springen zwischen Landmarks und
Überschriften, so wie sehende Benutzer eine Seite überfliegen. Die Seite beantwortet: welche Landmarks eine Seite braucht und wie
Überschriften über Pagelayout, Ansichten und Layout-Blöcke hinweg in Ordnung bleiben.

## Inhalt

1. [Landmarks im Pagelayout](#landmarks-im-pagelayout)
2. [Wiederholte Landmarks beschriften](#wiederholte-landmarks-beschriften)
3. [Überschriften](#überschriften)
4. [Überschriften in Layouts](#überschriften-in-layouts)
5. [Prüfungen](#prüfungen)

## Landmarks im Pagelayout

```tpl
<body>
<a class="skip-link" href="#main">{'Skip to main content'|i18n( 'design/mytheme' )}</a>
<header class="site-header">
    <a class="site-name" href={'/'|ezurl}>{$site.title|wash}</a>
    <nav aria-label="{'Main navigation'|i18n( 'design/mytheme' )}">...</nav>
</header>
<main id="main" tabindex="-1">
    {$module_result.content}
</main>
<footer class="site-footer">...</footer>
</body>
```

- Genau ein `<main>`. `tabindex="-1"` erlaubt dem Skip-Link, den Fokus hineinzusetzen.
- `<header>` und `<footer>` direkt im `<body>` sind Banner und Inhaltsinformation der Seite.
- Seitliche Inhalte gehören in `<aside>`.

admin4 verwendet dieselbe Struktur mit Rollen an `div`-Elementen: `role="banner"`, `role="main"` (mit `tabindex="-1"`),
`role="navigation"` mit `aria-label` für den Brotkrumenpfad, `role="complementary"` für die Werkzeugspalte, `role="contentinfo"`
(`design/admin4/templates/pagelayout.tpl`).

## Wiederholte Landmarks beschriften

Wenn eine Seite mehr als ein `<nav>` hat (Hauptmenü, Brotkrumenpfad, Links im Fußbereich, Blättern), geben Sie jedem ein übersetztes
`aria-label`, sodass die Liste der Landmarks „Hauptnavigation, Brotkrumenpfad, Fußbereich“ lautet.

## Überschriften

- **Ein `<h1>` pro Seite**: der Titel des Inhalts. In einer Inhaltsansicht gibt ihn das Vollansichts-Template aus; das Pagelayout nicht
  (der Name der Website im Kopfbereich ist auf Inhaltsseiten kein `<h1>`).
- **Keine übersprungenen Ebenen**: `h1`, dann `h2`, dann `h3`. Wählen Sie das Element nach der Struktur und gestalten Sie es mit Klassen,
  nicht umgekehrt.
- **line-Ansichten und Teaser** in einer Liste unter einem `h2` verwenden `h3` für ihre Titel.
- **Rich Text**: Die Überschriften der Redakteure beginnen auf der Ebene, die ihnen das Datentyp-Template gibt; prüfen Sie, wie Ihr
  `ezxmltags/header.tpl` die Ebenen abbildet, wenn Sie es überschreiben.

## Überschriften in Layouts

Mit dem Layout-Editor gebaute Seiten bestehen aus Blöcken; jedes Block-Template entscheidet über sein eigenes Überschriftenelement. Das
`block/title.tpl` der Layouts-Erweiterung nimmt das Element aus dem Block (`tag` oder `h` plus `level`), sodass der Redakteur es wählt.
In Ihren Block-Templates:

- Geben Sie Listen- und Rasterblöcken eine einstellbare Überschriftenebene oder verwenden Sie `h2` für Blocktitel und `h3` für Einträge;
- stellen Sie sicher, dass die Seite weiterhin genau ein `h1` hat, meist aus einem Titel- oder Vollansichtsblock.

## Prüfungen

- [ ] Eine Liste der Landmarks (Browsererweiterung oder Rotor des Screenreaders) zeigt Banner, Navigation(en), Hauptinhalt,
  Inhaltsinformation.
- [ ] Jede Navigation hat einen eigenen Namen.
- [ ] Die Liste der Überschriften beginnt mit einem `h1` und hat keine Sprünge.
- [ ] Auch Fehlerseiten und Suchseiten haben ein `h1`.

## Verwandte Seiten

- [Skip-Links](sprunglinks.md)
- [Das Pagelayout](das-pagelayout.md)
- [Block-Ansichtstemplates](block-ansichtstemplates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
