---
title: Tastatur und Fokus
audience: government, developer, designer, editor
category: public-sector
order: 230
summary: Für Entwicklung, Design und Beschäftigte, die auf die Tastatur angewiesen sind - welche Tastaturunterstützung die Administration admin4 bietet (Sprunglink, Reiter, Baum, Menüs, Alternative zum Ziehen), welche Tasten Benutzer lernen können und die Regeln, mit denen ein öffentliches Design ohne Maus vollständig nutzbar bleibt.
keywords: Tastatur, Fokus, sichtbarer Fokus, Fokusreihenfolge, Sprunglink, Tastaturfalle, Tabulator, Pfeiltasten, Inhaltsbaum, Kontextmenü, Tastenkürzel, keyboard, focus, focus visible, focus order, skip link, keyboard trap, tab, arrow keys, content tree, context menu, shortcuts, WCAG 2.1.1, 2.1.2, 2.4.3, 2.4.7, 2.4.11, admin4
type: help
---
# Tastatur und Fokus

Diese Seite richtet sich an Entwicklerinnen und Entwickler sowie Designerinnen und Designer, die Templates und
Skripte bauen, und an die Administration und Trainerinnen und Trainer, die Beschäftigte unterstützen, die mit der
Tastatur arbeiten. Alles, was eine Maus kann, muss auch mit der Tastatur möglich sein, und man muss immer sehen, wo der
Fokus ist. WCAG 2.2 behandelt das unter 2.1.1, 2.1.2, 2.4.3, 2.4.7 und 2.4.11 (<https://www.w3.org/TR/WCAG22/>).

## Inhalt

1. [In der Administration](#in-der-administration)
2. [Tasten, die Sie Ihren Beschäftigten beibringen](#tasten-die-sie-ihren-beschäftigten-beibringen)
3. [Regeln für Ihr öffentliches Design](#regeln-für-ihr-öffentliches-design)
4. [Testen](#testen)
5. [Verwandte Seiten](#verwandte-seiten)

## In der Administration

admin4 ist für die Bedienung per Tastatur gebaut und getestet
([Das Design admin4: Tastatur](../../../../eng-us/features/6.0/admin4-design.md#keyboard),
`tests/tests/design/AdminKeyboardTest.php`, `AdminTreeViewTest.php`, `AdminContextMenuTest.php`):

- „Zum Hauptinhalt springen“ ist der erste Tabulatorstopp.
- Die Reiter eines Knotens folgen dem Reitermuster von WAI-ARIA (Pfeiltasten, Pos1, Ende).
- Der Inhaltsbaum folgt dem Muster der Baumansicht (Pfeiltasten, Buchstaben, Umschalt+F10 für das Menü des Elements).
- Kontextmenüs und Menüs im Kopfbereich öffnen sich und lassen sich mit der Tastatur bedienen; Escape schließt und gibt
  den Fokus zurück.
- Das Umsortieren von Unterelementen per Ziehen und Ablegen hat am Griff einen Tastaturmodus.
- Kalenderauswahlen öffnen sich über eine benannte Schaltfläche und geben den Fokus zurück.
- Ein gemeinsamer Fokusrahmen, 3 px, auf einen Kontrast von 3:1 geprüft.

## Tasten, die Sie Ihren Beschäftigten beibringen

Die Benutzerseite [Tastenkürzel](../../tastenkuerzel.md) listet sie auf; die nützlichsten:

| Taste | Bewirkt |
|---|---|
| Tab (erster Druck) | zeigt „Zum Hauptinhalt springen“ |
| `/` | springt zum Suchfeld |
| Esc | schließt das offene Menü oder Pop-up |
| Pfeiltasten im Baum | Elemente bewegen, öffnen und schließen |
| Umschalt+F10 | öffnet das Menü eines Baumelements oder einer Zeile der Unterelemente |

## Regeln für Ihr öffentliches Design

- **Native Elemente zuerst**: Links für die Navigation, Schaltflächen für Aktionen; kein anklickbares `div`.
- **Reihenfolge**: Die Tabulatorreihenfolge folgt der visuellen Reihenfolge; verwenden Sie kein positives `tabindex`.
- **Sichtbarer Fokus** auf jedem Link und Bedienelement; nie `outline: none` ohne Ersatz.
- **Nicht verdeckt**: Fixierte Kopfbereiche, Cookie-Hinweise und Chat-Widgets dürfen das fokussierte Element nicht
  verdecken (2.4.11).
- **Keine Fallen**: Eingebettete Karten, Videoplayer und CAPTCHAs müssen den Fokus mit Tab wieder herauslassen.
- **Menüs**: Aufklappmenüs öffnen sich mit Enter oder Leertaste, schließen mit Escape und geben den Fokus zurück.
- **Dialoge**: Fokus in den Dialog setzen, dort halten, solange er offen ist, und beim Schließen zurückgeben.

## Testen

Legen Sie die Maus beiseite und bedienen Sie die Seite mit Tab, Umschalt+Tab, Enter, Leertaste, Escape und den
Pfeiltasten, nach dem Tastaturdurchgang unter
[Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md#keyboard-pass).
Tun Sie das für jedes Seiten-Template, jedes Formular und jeden interaktiven Block.

## Verwandte Seiten

- [Tastenkürzel](../../tastenkuerzel.md)
- [Das Design admin4](../../../../eng-us/features/6.0/admin4-design.md)
- [Menüs und Navigation](../../../../eng-us/help/creators/website-creators/menus-and-navigation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
