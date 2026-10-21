---
title: Farbe und Kontrast in Ihrem Theme
audience: government, designer, developer
category: public-sector
order: 220
summary: Für das Design, das ein öffentliches Design baut oder mit der Marke versieht - die Kontrastverhältnisse, die WCAG 2.2 AA verlangt, wie admin4 sie mit geprüften Farb-Tokens im hellen und dunklen Modus einhält und wie Sie dieselbe Disziplin auf Ihr eigenes Theme und Ihre Markenfarben anwenden.
keywords: Farbe, Kontrast, Kontrastverhältnis, Tokens, CSS-Custom-Properties, Dunkelmodus, erzwungene Farben, Markenfarben, Corporate Design, Theme, Design, color, contrast, contrast ratio, 4.5:1, 3:1, tokens, CSS custom properties, dark mode, forced colors, brand colors, theme, WCAG 1.4.1, 1.4.3, 1.4.11, admin4, designers
type: help
---
# Farbe und Kontrast in Ihrem Theme

Diese Seite richtet sich an Designerinnen und Designer sowie Frontend-Entwicklerinnen und -Entwickler, die das
öffentliche Design einer Website bauen, und an Kommunikationsteams, die ihnen Markenfarben vorgeben. Öffentliche
Stellen haben oft Markenpaletten, die für den Druck gewählt wurden; auf dem Bildschirm verfehlen mehrere dieser Farben
den Kontrast, den WCAG 2.2 AA verlangt. Beheben Sie das einmal im Theme, und jede Seite profitiert davon.

## Inhalt

1. [Was WCAG verlangt](#was-wcag-verlangt)
2. [Wie admin4 es macht](#wie-admin4-es-macht)
3. [Dasselbe in Ihrem Theme tun](#dasselbe-in-ihrem-theme-tun)
4. [Markenfarben, die durchfallen](#markenfarben-die-durchfallen)
5. [Verwandte Seiten](#verwandte-seiten)

## Was WCAG verlangt

| Kriterium | Anforderung |
|---|---|
| 1.4.3 Kontrast (Minimum) | Text 4,5:1 gegenüber seinem Hintergrund; großer Text 3:1 |
| 1.4.11 Nicht-Text-Kontrast | Begrenzungen von Bedienelementen, Fokusindikatoren und bedeutungstragende Grafiken 3:1 |
| 1.4.1 Benutzung von Farbe | Farbe ist nie das einzige Mittel, Informationen zu vermitteln |

Quelle: <https://www.w3.org/TR/WCAG22/>.

## Wie admin4 es macht

Die Administration admin4 definiert alle ihre Farben als Tokens (Custom Properties `--a4-*` in
`design/admin4/stylesheets/components.css`) mit einem hellen und einem dunklen Wert, und der Test
`tests/tests/design/AdminTokenContrastTest.php` berechnet den Kontrast nach WCAG 2 für die Paare, die zusammen
verwendet werden: Text 4,5:1 auf jeder Fläche, Fokusrahmen und Feldränder 3:1, Diagrammfarben 3:1, in beiden Modi
([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#wcag-22)). Die Designregeln verbieten fest
codierte Farben in Templates und verlangen für jedes neue Token einen dunklen Wert
([Styling von admin4](../../../bc/6.0/admin4-gestaltung.md)). Außerdem beachtet sie `@media (forced-colors: active)` und
`prefers-color-scheme`.

## Dasselbe in Ihrem Theme tun

1. **Definieren Sie Tokens.** Legen Sie jede Farbe des Designs an einer Stelle ab (CSS-Custom-Properties), benannt nach
   ihrer Rolle: Text, gedämpfter Text, Link, Fläche, Rand, Fokus, Fehler, Erfolg.
2. **Prüfen Sie jedes Paar**, das zusammen vorkommt, mit einem Kontrastrechner nach der Formel von WCAG 2, in jedem
   Farbschema, das Ihr Design anbietet (hell, dunkel, hoher Kontrast).
3. **Keine Inline-Farben.** Templates verwenden Klassen; Inhalte setzen keine Farben. Farben, die die Redaktion im Rich
   Text wählt, entgehen Ihren Prüfungen.
4. **Fokus**: ein Fokusindikator von mindestens 3:1 gegenüber dem Bedienelement und seinem Hintergrund; entfernen Sie
   den Umriss nie ohne Ersatz.
5. **Links** im Fließtext: Unterstreichen Sie sie oder lassen Sie sie sich um 3:1 vom umgebenden Text unterscheiden und
   zeigen Sie bei Hover und Fokus einen Hinweis, der nicht auf Farbe beruht.
6. **Erzwungene Farben**: Testen Sie mit dem Hochkontrastmodus des Betriebssystems; verwenden Sie Media Queries mit
   `forced-colors`, wo Ränder oder Symbole verschwinden.
7. **Zustände**: Fehler, Warnungen und Erfolgsmeldungen tragen zusätzlich zur Farbe ein Symbol oder ein Wort (die
   Meldungen von admin4 tun das, `AdminNoticesTest.php`).

## Markenfarben, die durchfallen

- Behalten Sie die Markenfarbe für große Elemente (Kopfbereiche, große Überschriften, Hintergründe hinter dunklem Text)
  und leiten Sie für kleinen Text und Links einen dunkleren Ton ab.
- Schreiben Sie die abgeleiteten Töne in Ihre Markenrichtlinien, damit Kommunikationsteams anderswo dieselben Werte
  verwenden.
- Halten Sie die Entscheidung in Ihrer Designdokumentation fest; Prüferinnen und Prüfer fragen danach.

## Verwandte Seiten

- [Ihre Website mit Ihrer Marke versehen](../../../../eng-us/help/creators/website-creators/branding-your-site.md)
- [Stylesheets und Skripte](../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md)
- [Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
