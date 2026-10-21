---
title: "Lektion 5.2: Ein barrierefreies Website-Design"
audience: trainer, designer, developer
category: training
order: 52
summary: Die Teilnehmenden machen den Rahmen ihres Themes barrierefrei - Seitensprache, benannte Landmarks, die aktuelle Seite im Menü, ein h1 pro Seite, ein sichtbarer Fokusring, reduzierte Bewegung - und prüfen jede Änderung im Seitenquelltext und mit der Tastatur. 60 Minuten.
keywords: theme-design, barrierefreiheit, lang, landmarks, nav aria-label, aria-current, sprunglink, h1, überschriften, focus-visible, outline, prefers-reduced-motion, kontrast, alternativtext, pagelayout, theme design, accessibility, skip link, headings, contrast, alt text
type: help
---
# Lektion 5.2: Ein barrierefreies Website-Design

Das meiste, was eine öffentliche Website barrierefrei macht, wird ein einziges Mal entschieden, im Theme: die
Seitensprache, die Landmarks, der Sprunglink, die Reihenfolge der Überschriften, der Fokusring, Kontrast und Bewegung.
In dieser Lektion gehen die Teilnehmenden die Liste der Template-Autoren durch und wenden jeden Punkt auf
`training_theme` an.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../../../../../eng-us/help/trainers/theme-designers/README.md) |
| Modul | [Modul 5: Barrierefreiheit und Rechts-nach-links](README.md) |
| Dauer | 60 Minuten (15 Präsentation, 45 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Lektion 5.1](was-exponential-verspricht.md); `training_theme` mit dem Sprunglink und der Brotkrumennavigation aus Lektion 2.2 |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Seitensprache aus dem Siteaccess ausgeben und die Landmarks des Rahmens auszeichnen;
- mehrere Navigations-Landmarks mit `aria-label` benennen und die aktuelle Seite mit `aria-current="page"` kennzeichnen;
- ein `h1` pro Seite und Überschriften in der richtigen Reihenfolge einhalten;
- jedem Link und jedem Bedienelement einen sichtbaren Fokusstil geben und `prefers-reduced-motion` beachten.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Barrierefreiheit für Website-Bauer, was die Template-Autorin oder der Template-Autor bestimmt](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md#what-the-template-author-controls).

1. **Die Liste** (6 Minuten). Gehen Sie die Tabelle durch: Sprache (`<html lang="...">` aus
   `$site.http_equiv.Content-language`), Landmarks (`header`, `nav` mit `aria-label`, wenn es mehrere gibt, `main`,
   `footer`), Sprunglink, ein `h1` pro Seite, aktuelle Seite mit `aria-current="page"`, sichtbarer Fokus (nie
   `outline: none` ohne Ersatz), Kontrast 4.5:1 für Text und 3:1 für großen Text, Bilder mit `alt`, reduzierte
   Bewegung, 200 % Zoom ohne horizontales Scrollen.
2. **Wohin jeder Punkt im Theme gehört** (5 Minuten). `pagelayout.tpl` (Sprache, Landmarks, Sprunglink), der
   Kopfbereich (Menü, aktuelle Seite), die View-Templates (Überschriften), `training.css` (Fokus, Bewegung, Kontrast).
3. **Die Administration als Beispiel** (4 Minuten). admin4 hat einen gemeinsamen Fokusring (eine Umrandung von 3 px in
   seiner Ringfarbe) und eine Regel für `prefers-reduced-motion`; zeigen Sie beides als Vorbild für das öffentliche
   Theme.

### Häufige Fragen

**Welcher Sprachcode gehört in `lang`?** `$site.http_equiv.Content-language` liefert den Code der Inhaltssprache des
Siteaccess, so wie das Standard-Pagelayout ihn verwendet. Das Pagelayout des Assistenten baut ihn aus
`ContentObjectLocale`; beides funktioniert, das Erste folgt den HTTP-Einstellungen des Siteaccess.

**Ist der Name der Website im Kopfbereich ein `h1`?** Nein: Der eigene Name der Seite ist das `h1` (der Titel des
Artikels, der Name des Ordners). Der Name der Website ist ein Link.

**Brauche ich überall ARIA?** Nein. Zuerst native Elemente (`<nav>`, `<main>`, `<button>`); ARIA dort, wo HTML nichts
bietet, etwa `aria-current`.

### Stolperfallen

- **Zwei `h1`-Elemente.** Eines aus dem Pagelayout und eines aus der View. Prüfen Sie es mit der Gliederung in
  Schritt 6.
- **Ein Fokusstil, den ein Reset-Stylesheet entfernt.** Durchsuchen Sie das CSS des Themes nach `outline: none` und
  `outline: 0`.
- **`aria-current` an jedem Eintrag.** Nur am Eintrag der aktuellen Seite (`"page"`) oder des Bereichs, in dem die Seite
  liegt (`"true"`).

## Praktische Übung

Auf Ihrer Schulungsinstallation. `D` steht für `extension/training_theme/design/training_theme`.

1. Ändern Sie in `D/templates/pagelayout.tpl` das Element `<html>` zu
   `<html lang="{$site.http_equiv.Content-language|wash}">`.
2. Geben Sie in `D/templates/page_header.tpl` dem `<nav>` einen Namen:
   `<nav class="training_theme-menu" aria-label="{'Main menu'|i18n( 'design/training_theme' )}">`.
3. Kennzeichnen Sie in der Menüschleife den aktuellen Bereich. Der Pfad der Seite steht in `$module_result.path`; sein
   zweiter Eintrag (Index 1) ist der oberste Eintrag, wenn die Seite unterhalb der obersten Ebene der Website liegt:

   ```
   <li><a href={$training_theme_item.url_alias|ezurl}{if and( is_set( $module_result.path[1].node_id ), $module_result.path[1].node_id|eq( $training_theme_item.node_id ) )} aria-current="{if $module_result.path|count|eq( 2 )}page{else}true{/if}"{/if}>{$training_theme_item.name|wash}</a></li>
   ```

   Das Menü unterscheidet sich jetzt von Seite zu Seite: Wenn Sie es in Lektion 2.4 in einen Cache-Block gesetzt haben,
   ergänzen Sie `$module_result.uri` in dessen Schlüsseln.
4. Ergänzen Sie in `D/stylesheets/training.css` einen Fokusstil und eine Regel für reduzierte Bewegung:

   ```css
   a:focus-visible, button:focus-visible, input:focus-visible { outline: 3px solid #1f4fa8; outline-offset: 2px; }
   .training_theme-menu [aria-current] { font-weight: bold; text-decoration: underline; }
   @media (prefers-reduced-motion: reduce) {
       *, *::before, *::after { transition: none !important; animation: none !important; }
   }
   ```

5. Führen Sie `eztemplatecheck.php` für die beiden Templates aus, dann die Routine aus Lektion 3.3 und ein
   vollständiges Neuladen.
6. Öffnen Sie einen Artikel. Listen Sie im Bereich „Barrierefreiheit“ der Entwicklerwerkzeuge (oder durch Suchen im
   Quelltext) die Überschriften auf: ein `h1`, der Titel des Artikels.
7. Drücken Sie vom Anfang der Seite an die Tabulatortaste: Sprunglink, Name der Website, Menüeinträge, Links der
   Brotkrumennavigation, Links im Inhalt. Prüfen Sie, dass der Fokus an jeder Station sichtbar ist.
8. Öffnen Sie eine Seite innerhalb eines Ordners der obersten Ebene. Prüfen Sie, dass ihr Menüeintrag im Quelltext
   `aria-current="true"` trägt und anders aussieht.
9. Prüfen Sie mit der Farbauswahl der Entwicklerwerkzeuge (sie zeigt das Kontrastverhältnis) den Kontrast Ihrer
   Fokusfarbe und des Menütexts gegenüber ihrem Hintergrund.

## Erwartetes Ergebnis

- Schritt 1: Der Quelltext zeigt `<html lang="en">` oder den Sprachcode des Siteaccess.
- Schritt 6: genau ein `h1` auf der Artikelseite.
- Schritt 7: Jede Fokusstation zeigt einen sichtbaren Ring.
- Schritt 8: Der aktuelle Eintrag der obersten Ebene ist im Quelltext und sichtbar gekennzeichnet.
- Schritt 9: Kontrastverhältnisse von mindestens 4.5:1 für Text und 3:1 für den Fokusring.
- Der Trainer prüft am Projektor die Seite einer Teilnehmerin oder eines Teilnehmers nur mit der Tastatur.

## Quiz

1. Welche Variable liefert die Seitensprache für `<html lang>`?
2. Die Seite hat zwei `<nav>`-Elemente. Was braucht jedes davon?
3. Welches Attribut kennzeichnet die aktuelle Seite in einem Menü?
   A. `aria-selected` B. `aria-current="page"` C. `aria-pressed` D. `class="active"` allein
4. Was ist an `a:focus { outline: none; }` in einem Theme falsch?
5. Was sollte ein Theme tun, wenn das System der Benutzerin oder des Benutzers reduzierte Bewegung anfordert?

<details>
<summary>Antworten</summary>

1. `$site.http_equiv.Content-language`
   ([Barrierefreiheit für Website-Bauer](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md#what-the-template-author-controls)).
2. Einen Namen mit `aria-label`, damit Screenreader-Nutzer sie unterscheiden können.
3. **B.**
4. Es entfernt den sichtbaren Fokus, sodass Tastaturnutzer nicht sehen, wo sie sich befinden; entfernen Sie ihn nie
   ohne Ersatz.
5. Übergänge und Animationen mit einer Regel `@media (prefers-reduced-motion: reduce)` abschalten.

</details>

## Weiterführende Literatur

- [Barrierefreiheit für Website-Bauer](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md): die
  vollständige Liste, Formulare und die Checkliste.
- [Menüs und Navigation](../../../../../eng-us/help/creators/website-creators/menus-and-navigation.md): Menüs, die die aktuelle Seite
  kennzeichnen.
- [Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md): wie die Administration es macht.

## Verwandte Seiten

- [Modul 5: Barrierefreiheit und Rechts-nach-links](README.md)
- [Theme-Design: Kursüberblick](../../../../../eng-us/help/trainers/theme-designers/README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
