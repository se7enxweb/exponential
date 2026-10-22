---
title: "Farbpaletten und Farbabstufungen"
audience: theme-designer
category: theme-design
order: 15
summary: Wie Theme-Designer eine Farbpalette für ein Exponential-Theme aufbauen - Markenfarben, Abstufungen aus Aufhellungen und Abdunklungen, Rollen-Tokens aus den Abstufungen, Werte für den Dunkelmodus, Statusfarben und wie Sie den Kontrast prüfen, bevor etwas programmiert wird.
keywords: Theme-Designer, Farbpalette, Farbabstufung, Aufhellungen, Abdunklungen, Rollen, semantische Farben, Markenfarben, Dunkelmodus, Statusfarben, Kontrast, Tokens, theme designer, color palette, color ramp, tints, shades, roles, semantic colors, brand colors, dark mode, status colors, contrast
type: help
---
# Farbpaletten und Farbabstufungen

Diese Seite ist für Theme-Designer, die die Farben eines Themes aufbauen. Eine gute Palette hat zwei Ebenen:
**Abstufungen** (eine Markenfarbe in etwa zehn Helligkeitsstufen), die nur Designer sehen, und **Rollen-Tokens** (Text,
Fläche, Linie, Akzent, Fokus), die die Stylesheets verwenden. Die Rollen-Tokens und ihre Regeln sind in
[Design-Tokens](../designer/theme-design/design-tokens.md) dokumentiert, Kontrastziele in
[Farbkontrast](../designer/theme-design/farbkontrast.md), der Dunkelmodus in
[Überblick über den Dunkelmodus](../designer/theme-design/dunkelmodus-ueberblick.md). Diese Seite verbindet sie.

## Inhalt

1. [Zwei Ebenen](#zwei-ebenen)
2. [Eine Abstufung aufbauen](#eine-abstufung-aufbauen)
3. [Rollen aus den Abstufungen wählen](#rollen-aus-den-abstufungen-wählen)
4. [Werte für den Dunkelmodus](#werte-für-den-dunkelmodus)
5. [Statusfarben](#statusfarben)
6. [Ein Akzent](#ein-akzent)
7. [Vor dem Programmieren prüfen](#vor-dem-programmieren-prüfen)
8. [Ein Palettenblatt](#ein-palettenblatt)

## Zwei Ebenen

| Ebene | Beispiel | Wer sie verwendet |
|---|---|---|
| Abstufung | `blue-50` ... `blue-900`, `gray-50` ... `gray-900` | Designer, zum Auswählen |
| Rolle | `--site-ink`, `--site-surface`, `--site-accent` | Stylesheets, über Tokens |

Stylesheets verwenden nie die Namen der Abstufungen. Ändert sich das Markenblau, wählen Sie neue Stufen für die Rollen,
und das CSS ändert sich nicht. Die Abstufung kann nur in Ihrem Designtool existieren oder als kommentierter Block am
Anfang der Token-Datei.

## Eine Abstufung aufbauen

1. Beginnen Sie bei jeder Markenfarbe und einem neutralen Ton (Grau, oft leicht zur Marke hin getönt).
2. Bilden Sie etwa zehn Stufen von sehr hell bis sehr dunkel. Arbeiten Sie in einem wahrnehmungsgerechten Farbraum (zum
   Beispiel OKLCH in Werkzeugen, die ihn anbieten), damit gleiche Stufen gleich aussehen; einfache HSL-Stufen wirken
   ungleichmäßig.
3. Halten Sie Farbton und Buntheit entlang der Abstufung stabil, außer wo eine Stufe trüb aussieht; sehr helle und sehr
   dunkle Stufen brauchen oft weniger Buntheit.
4. Schreiben Sie neben jede Stufe ihren Kontrast gegen Weiß und gegen Ihre dunkelste Fläche.

## Rollen aus den Abstufungen wählen

| Rolle | Wahl im hellen Theme | Anforderung |
|---|---|---|
| Ink (Fließtext) | ein sehr dunkler neutraler Ton | 4,5:1 oder mehr auf Fläche und Karte |
| Gedämpfter Text | ein mitteldunkler neutraler Ton | 4,5:1 auf Fläche und Karte |
| Fläche, Karte | Weiß und ein sehr heller neutraler Ton | Karte von der Fläche unterscheidbar |
| Linie | ein heller neutraler Ton | sichtbar; 3:1, wo die Linie die einzige Begrenzung eines Bedienelements ist |
| Akzent (Links, Schaltflächen) | eine mitteldunkle Markenstufe | 4,5:1 auf der Fläche; Text auf Akzent 4,5:1 |
| Fokus | Akzent oder eine eigene Farbe | 3:1 gegenüber dem, was daneben liegt |

Die Ziele stammen aus [Farbkontrast](../designer/theme-design/farbkontrast.md#die-ziele).

## Werte für den Dunkelmodus

Jede Farbrolle erhält ihren dunklen Wert zur selben Zeit wie ihren hellen; das ist eine Projektregel für jeden Token
([Design-Tokens](../designer/theme-design/design-tokens.md#regeln-die-token-nützlich-halten)). In dunklen Themes:

- kommen Flächen vom dunklen Ende der neutralen Abstufung, aber nicht reines Schwarz;
- ist Ink ein heller neutraler Ton, nicht reines Weiß, um Blendung zu verringern;
- rückt der Akzent meist mehrere Stufen heller, weil die mittlere Stufe, die auf Weiß funktionierte, auf Dunkel
  versagt;
- sind Karten etwas heller als die Fläche, damit sich Ebenen ohne Schatten lesen lassen.

Zeichnen Sie jeden wichtigen Bildschirm in beiden. Eine weiße Karte auf einer dunklen Seite ist der häufigste Fehler im
Dunkelmodus ([Checkliste: Dunkelmodus](../designer/theme-design/checkliste-dunkelmodus.md)).

## Statusfarben

Erfolg, Warnung, Fehler und Information brauchen eigene Rollen (`--site-danger`, `--site-success` ...), jeweils mit
einer Variante für Hintergrund, Text und Rahmen, wenn Hinweise getönte Kästen verwenden, und jeweils mit einem dunklen
Wert. Lassen Sie Farbe nie allein eine Bedeutung tragen: Ein Fehler hat auch ein Symbol oder das Wort "Fehler"
([Formulare gestalten](formulare-gestalten.md)).

## Ein Akzent

Die Token-Regeln des Projekts erlauben einen Akzent und einen Radius für Komponenten. Ein zweiter Akzent "nur für diese
Seite" macht das Theme schwerer an eine Marke anzupassen und schwerer barrierefrei zu halten. Bei Websites mit mehreren
Marken wechselt der Akzent pro Marke, nicht pro Seite
([Für mehrere Marken im Multisite-Betrieb gestalten](fuer-mehrere-marken-in-multisite-gestalten.md)).

## Vor dem Programmieren prüfen

- Prüfen Sie jedes Rollenpaar (Text auf Fläche, Text auf Karte, Text auf Akzent, Fokus auf Fläche) in beiden Themes mit
  einem Kontrastprüfer und schreiben Sie das Verhältnis in das Palettenblatt.
- Sehen Sie sich die Palette mit einer Simulation von Farbsehschwächen an, die die Entwicklerwerkzeuge der meisten
  Browser und viele Designtools anbieten.
- Setzen Sie die Palette auf die [Styleguide-Seite](eine-styleguide-seite-bauen.md), sobald sie programmiert ist, damit
  die echten Werte noch einmal geprüft werden.

## Ein Palettenblatt

| Token | Hell | Verhältnis (hell) | Dunkel | Verhältnis (dunkel) | Verwendet für |
|---|---|---|---|---|---|
| `--site-ink` | gray-900 | auf Fläche | gray-100 | auf Fläche | Fließtext |
| `--site-accent` | blue-700 | auf Fläche | blue-300 | auf Fläche | Links, primäre Schaltflächen |

Tragen Sie die Werte und gemessenen Verhältnisse Ihrer Palette ein. Dieses Blatt übergeben Sie; die Token-Datei wird
danach geschrieben ([Von Designtool-Variablen zu CSS Custom Properties](von-designtool-variablen-zu-css-custom-properties.md)).

## Verwandte Seiten

- [Design-Tokens](../designer/theme-design/design-tokens.md)
- [Dunkelmodus mit prefers-color-scheme](../designer/theme-design/dunkelmodus-mit-prefers-color-scheme.md)
- [Tutorial: Dunkelmodus ergänzen](../designer/theme-design/tutorial-dunkelmodus-hinzufuegen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
