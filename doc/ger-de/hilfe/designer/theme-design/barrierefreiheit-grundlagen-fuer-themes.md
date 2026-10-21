---
title: Barrierefreiheit: Grundlagen für Themes
audience: designer
category: theme-design
order: 430
summary: Die Grundlagen der Barrierefreiheit, die jedes Exponential-Design braucht: Sprache und Schreibrichtung am html-Element, Landmarks und Überschriften, ein Skip-Link, sichtbarer Fokus, Kontrast, Alternativtexte, Formulare, Bewegung, Zoom und wie das Projekt testet (axe ohne ernste Probleme, Tastatur, hell und dunkel).
keywords: Barrierefreiheit, a11y, WCAG, Screenreader, Tastatur, Fokus, Kontrast, Alternativtext, Landmarks, Skip-Link, axe, barrierefreies Theme, inklusives Design, accessibility, keyboard, focus, contrast, alt text, skip link, accessible theme, inclusive design
featured: true
type: help
---
# Barrierefreiheit: Grundlagen für Themes

Diese Seite ist für Designer, die möchten, dass jeder Besucher ihr Design nutzen kann, auch Menschen, die eine Tastatur, einen
Screenreader, starke Vergrößerung oder hohen Kontrast verwenden. Sie listet, wofür ein Design verantwortlich ist, und verweist
für jedes Thema auf eine eigene Seite.

## Inhalt

1. [Wofür das Design zuständig ist](#wofür-das-design-zuständig-ist)
2. [Die zehn Grundlagen](#die-zehn-grundlagen)
3. [Wofür Redakteure zuständig sind](#wofür-redakteure-zuständig-sind)
4. [Wie das Projekt testet](#wie-das-projekt-testet)

## Wofür das Design zuständig ist

Inhalte, Templates und CSS teilen sich die Arbeit. Das Design bestimmt die Seitenstruktur, die Navigation, die Fokusstile, die
Farben, das Markup der Formulare und das Verhalten der Skripte. Ein Design, das diese Dinge richtig macht, macht jede Seite von
vornherein barrierefrei; eines, das sie falsch macht, können Redakteure nicht reparieren.

## Die zehn Grundlagen

1. **Sprache und Schreibrichtung.** `<html lang="{$site.http_equiv.Content-language|wash}">` und `dir="rtl"` für
   Sprachen, die von rechts nach links geschrieben werden ([Das Pagelayout](das-pagelayout.md)).
2. **Landmarks und ein einziges `<h1>`.** `<header>`, `<nav>`, `<main id="main">`, `<footer>`; ein `<h1>` pro Seite,
   Überschriften in der richtigen Reihenfolge ([Landmarks und Überschriften](landmarks-und-ueberschriften.md)).
3. **Ein Skip-Link** als erstes fokussierbares Element ([Skip-Links](sprunglinks.md)).
4. **Sichtbarer Fokus** auf jedem interaktiven Element, nie `outline: none` ohne Ersatz
   ([Tastatur und Fokus](tastatur-und-fokus.md)).
5. **Kontrast** von mindestens 4,5:1 für Text, 3:1 für großen Text, Symbole und Fokusringe, im hellen und im dunklen Modus
   ([Farbkontrast](farbkontrast.md)).
6. **Alternativtexte**, ausgegeben aus dem Bildfeld (`|wash`), `alt=""` für Dekoration ([Bilder in einem Design](bilder-in-einem-design.md)).
7. **Formulare mit Beschriftungen und Fehlermeldungen**, die mit ihren Feldern verknüpft sind ([Barrierefreie Formulare](barrierefreie-formulare.md)).
8. **Bewegung nur, wenn gewünscht**: Beachten Sie `prefers-reduced-motion` ([Reduzierte Bewegung](reduzierte-bewegung.md)).
9. **Zoom und Umbruch**: funktioniert bei 200 % und in einem 320 px breiten Viewport ([Testen bei Zoomstufen](testen-bei-zoomstufen.md)).
10. **Skripte, die ihren Zustand in ARIA halten**: Ein Menüknopf meldet `aria-expanded`, ein Dialog hält den Fokus fest und
    gibt ihn danach zurück.

## Wofür Redakteure zuständig sind

Alternativtexte von Bildern, aussagekräftige Linktexte, die Reihenfolge der Überschriften im Rich Text, Untertitel von Videos.
Das Design hilft, indem es ausgibt, was Redakteure eingeben (Alternativtexte, Bildunterschriften), und den Überschriften im
Rich Text sinnvolle Stile gibt.

## Wie das Projekt testet

Die Seiten der Administration werden mit diesen Zielen geprüft, die auch für öffentliche Designs passen:

- axe (ein automatischer Prüfer für Barrierefreiheit, im Browser oder per Skript ausgeführt) findet **keine ernsten oder
  kritischen Probleme**;
- nur Tastatur: Jedes Bedienelement ist mit Tab erreichbar, mit Enter und Leertaste bedienbar, Escape schließt Überlagerungen;
- heller und dunkler Modus, bei 1440 px, bei 960 px mit Skalierung 2 und bei 375 px;
- erzwungene Farben (Windows-Kontrastmodus): Die Stylesheets von admin4 haben Dutzende Regeln `@media (forced-colors: active)`,
  damit Rahmen und Fokus sichtbar bleiben.

Gehen Sie die [Checkliste zur Barrierefreiheit](checkliste-barrierefreiheit.md) vor jeder Veröffentlichung eines Designs durch.

## Verwandte Seiten

- [Checkliste: Barrierefreiheit](checkliste-barrierefreiheit.md)
- [Farbkontrast](farbkontrast.md)
- [Tastatur und Fokus](tastatur-und-fokus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
