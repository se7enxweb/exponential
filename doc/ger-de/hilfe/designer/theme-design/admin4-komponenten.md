---
title: admin4-Komponenten
audience: designer, developer
category: theme-design
order: 550
summary: Die gemeinsamen Komponentenklassen .a4-* der Administration (Seitenbereich, Karten, Knöpfe, Werkzeugleiste, Filterleiste, Formulare, Tabellen, Tags und Badges, Hinweise, Aufklappen, Schritte, Symbole, Hilfsklassen), mit einem kleinen Beispiel, und warum Sie sie verwenden, bevor Sie CSS schreiben.
keywords: admin4-Komponenten, a4-Klassen, a4-card, a4-btn, a4-table, a4-toolbar, a4-filterbar, a4-tag, a4-badge, a4-notice, a4-sr-only, a4-page, UI-Baukasten der Administration, Komponentenbibliothek, admin4 components, a4 classes, admin ui kit, component library
type: help
---
# admin4-Komponenten

Diese Seite ist für Designer und Entwickler, die eine Seite der Administration bauen, im Kernel oder in einer Erweiterung. Die
Administration hat eine kleine Komponentenbibliothek: Klassen mit dem Präfix `.a4-` in `design/admin4/stylesheets/components.css`
(identische Kopie in `design/admin`). Die vollständige Referenz ist der Abschnitt „Components“ von
[Gestaltung der Administration](../../../../eng-us/bc/6.0/admin4-styling.md); diese Seite ist der Überblick.

## Inhalt

1. [Verwenden Sie sie zuerst](#verwenden-sie-sie-zuerst)
2. [Die wichtigsten Komponenten](#die-wichtigsten-komponenten)
3. [Eine Seite daraus gebaut](#eine-seite-daraus-gebaut)
4. [Zustände und Gewicht](#zustände-und-gewicht)

## Verwenden Sie sie zuerst

Die Regel für jedes Admin-Template: Verwenden Sie die gemeinsamen Klassen wieder, bevor Sie CSS schreiben. Sie sind bereits hell
und dunkel, beachten rechts nach links, sind mit der Tastatur bedienbar und auf jeder Admin-Seite geladen. Eigenes neues CSS ist
die Ausnahme ([admin4-Modul-Stylesheets](admin4-modul-stylesheets.md)).

## Die wichtigsten Komponenten

| Bedarf | Klassen |
|---|---|
| Ein Rahmen für eine Modulseite | `.a4-page` (Reset, Schrift, Links, Fokusring) |
| Seitentitel und Aktionen | `.a4-pagehead` mit `.a4-pagehead-title`, `.a4-pagehead-actions` |
| Kästen | `.a4-card` (`.a4-card-head`, `.a4-card-title`, `.a4-card-body`, `.a4-card-foot`), `.a4-cards` |
| Knöpfe | `.a4-btn`, `.a4-btn-primary`, `.a4-btn-danger`, `.a4-btn-ghost`, `.a4-btn-small`, `.a4-btn-icon`; `.a4-actions` für eine Reihe |
| Aktionsleisten | `.a4-toolbar` (`.is-bottom`, `.is-sticky`) |
| Filter über einer Liste | `.a4-filterbar` |
| Formulare | `.a4-form`, `.a4-field`, `.a4-label`, `.a4-input`, `.a4-field-error`, `.a4-error-summary`, `.a4-check`, `.a4-radios` |
| Tabellen | `.a4-table-wrap` um `table.a4-table` |
| Kennzeichnungen | `.a4-tag`, `.a4-badge` (`.is-ok`, `.is-warn`, `.is-bad`, `.is-info`), `.a4-status` |
| Meldungen | `.a4-notice` (`.is-ok`, `.is-warn`, `.is-bad`, `.is-info`), `.a4-empty` für eine leere Liste |
| Aufklappen | `details.a4-fold`, `details.a4-confirm` |
| Blättern, Reiter | `.a4-pager`, `.a4-tabs` |
| Symbole | `.a4-icon` mit `.a4-bi-<name>` |
| Hilfsklassen | `.a4-sr-only`, `.a4-muted`, `.a4-meta`, `.a4-num`, `.a4-nowrap`, `.a4-code` |

## Eine Seite daraus gebaut

```tpl
<div class="a4-page">
    <div class="a4-pagehead">
        <div class="a4-pagehead-main"><h1 class="a4-pagehead-title">{'Reports'|i18n( 'design/admin/myext' )}</h1></div>
        <div class="a4-pagehead-actions"><a class="a4-btn a4-btn-primary" href={'/myext/new'|ezurl}>{'New report'|i18n( 'design/admin/myext' )}</a></div>
    </div>
    <div class="a4-card">
        <div class="a4-card-head"><h2 class="a4-card-title">{'Last runs'|i18n( 'design/admin/myext' )}</h2></div>
        <div class="a4-card-body">
            {if $runs|count}
            <div class="a4-table-wrap"><table class="a4-table">...</table></div>
            {else}
            <p class="a4-empty">{'No runs yet.'|i18n( 'design/admin/myext' )}</p>
            {/if}
        </div>
    </div>
</div>
```

![Setup, Sektionen aus den gemeinsamen Komponenten gebaut: Zählerkarten, eine Filterleiste (umrandet) und eine Karte pro Sektion](../../../../eng-us/help/images/designers/components-page.webp)

![Dieselbe Seite im dunklen Modus, ohne zusätzliches CSS: Die Komponenten übernehmen die dunklen Token-Werte](../../../../eng-us/help/images/designers/components-page-dark.webp)

Kein `<style>`, kein `style=""`, jede Farbe kommt über die Klassen aus Token.

## Zustände und Gewicht

Zustände sind `.is-*`-Klassen an der Komponente (`.a4-card.is-warn`). Jede Komponente ist eine Klasse plus höchstens ein Element
oder ein Zustand, sodass eine Modulregel mit einer Klasse mehr immer gewinnt und Komponenten über die alten Basisregeln für
Elemente gewinnen.

## Verwandte Seiten

- [admin4-Token](admin4-tokens.md)
- [admin4-Modul-Stylesheets](admin4-modul-stylesheets.md)
- [Admin-Designs im Überblick](admin-designs-ueberblick.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
