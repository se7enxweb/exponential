---
title: "Navigation und Menüs gestalten"
audience: theme-designer
category: theme-design
order: 19
summary: Wie Theme-Designer die Navigation einer Website gestalten, deren Struktur Redakteure ändern - Menüs aus dem Inhaltsbaum, Grenzen für Tiefe und Breite, die aktuelle Seite, Brotkrümelnavigation, Unternavigation, kleine Bildschirme und was Sie mit Entwickler und Redakteuren vereinbaren.
keywords: Theme-Designer, Navigation, Hauptmenü, Untermenü, Brotkrümelnavigation, Breadcrumbs, Inhaltsbaum, Menütiefe, Mega-Menü, aktuelle Seite, mobiles Menü, Informationsarchitektur, theme designer, navigation, main menu, sub menu, breadcrumbs, content tree, menu depth, mega menu, current page, mobile menu, information architecture
type: help
---
# Navigation und Menüs gestalten

Diese Seite ist für Theme-Designer, die die Menüs einer Website gestalten. Auf den meisten Exponential-Websites ist das
Hauptmenü keine feste Liste in einem Template: Es wird aus dem **Inhaltsbaum** abgerufen und ändert sich also, wenn
Redakteure Ordner hinzufügen, umbenennen, verstecken oder umsortieren. Ein gutes Navigationsdesign funktioniert für den
Baum, den die Website heute hat, und für den, den sie nächstes Jahr haben wird. Die Template-Seite steht in
[Menüs und Navigation](../designer/vorlagen/menues-und-navigation.md) und
[Brotkrümelnavigation](../designer/vorlagen/brotkrumen.md).

## Inhalt

1. [Woher Menüs kommen](#woher-menüs-kommen)
2. [Für einen Baum gestalten, der sich ändert](#für-einen-baum-gestalten-der-sich-ändert)
3. [Tiefe](#tiefe)
4. [Die aktuelle Seite und der Pfad](#die-aktuelle-seite-und-der-pfad)
5. [Brotkrümelnavigation](#brotkrümelnavigation)
6. [Unternavigation](#unternavigation)
7. [Kleine Bildschirme und Tastaturen](#kleine-bildschirme-und-tastaturen)
8. [Was Sie vor dem Zeichnen vereinbaren](#was-sie-vor-dem-zeichnen-vereinbaren)

## Woher Menüs kommen

| Menü | Übliche Quelle | Wer es ändert |
|---|---|---|
| Hauptmenü | Unterelemente des Wurzelknotens der Website, nach Klasse gefiltert (oft Ordner) | Redakteure, indem sie Ordner hinzufügen, verstecken oder sortieren |
| Unternavigation | Unterelemente des obersten Ordners des aktuellen Bereichs | Redakteure |
| Brotkrümelnavigation | der Pfad des aktuellen Knotens | folgt dem Baum |
| Links im Fußbereich | ein Ordner mit Links oder fester Template-Text | Redakteure oder Entwickler |
| Hilfslinks (Anmeldung, Sprache) | Template und Einstellungen | Entwickler |

Fragen Sie, welche Quelle jedes Menü der Website verwendet. Ein Menü, das im Template feststeht, lässt sich pixelgenau
gestalten; ein Menü, das aus dem Baum abgerufen wird, muss mit dem umgehen, was Redakteure tun.

## Für einen Baum gestalten, der sich ändert

- **Anzahl der Einträge.** Zeichnen Sie das Hauptmenü mit der aktuellen Anzahl und mit zwei mehr und zwei weniger.
  Entscheiden Sie, was geschieht, wenn Einträge nicht passen: Umbruch in eine zweite Zeile, Verschieben in ein Menü
  "Mehr" oder früheres Umschalten auf das Menü für kleine Bildschirme.
- **Länge der Beschriftungen.** Ordnernamen schreiben Redakteure, und sie werden übersetzt. Zeichnen Sie die längste
  Beschriftung in der längsten Sprache der Website ([Für viele Sprachen gestalten](fuer-viele-sprachen-gestalten.md)).
- **Reihenfolge.** Die Reihenfolge folgt der Sortierung, die Redakteure am Elternknoten festlegen. Gestalten Sie kein
  Menü, das davon abhängt, dass ein bestimmter Eintrag zuerst oder zuletzt steht.
- **Versteckte Einträge.** Redakteure können einen Knoten verstecken, und der Menüabruf lässt ihn normalerweise weg;
  nichts im Design sollte davon abhängen, dass ein bestimmter Eintrag existiert.

## Tiefe

Jede Menüebene, die Sie gestalten, ist ein Abruf, ein Cache und ein Testfall. Die meisten Inhaltswebsites brauchen:

- eine Ebene im Hauptmenü;
- die zweite Ebene entweder in einem Aufklappmenü oder als Unternavigation auf den Seiten des Bereichs, nicht beides;
- tiefere Ebenen nur in der Unternavigation und der Brotkrümelnavigation.

Große "Mega-Menüs" sind möglich, aber auch sie sind Inhalt: Wer pflegt die Gruppierungen und die kurzen Beschreibungen?
Lautet die Antwort "die Redakteure", braucht das Inhaltsmodell Felder dafür
([Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md)).

## Die aktuelle Seite und der Pfad

Markieren Sie die aktuelle Seite und ihre Vorfahren: Liest ein Besucher einen Artikel in "Nachrichten", zeigt der
Eintrag "Nachrichten" im Hauptmenü, dass er sich in diesem Bereich befindet. Die Markierung darf nicht nur auf Farbe
beruhen, und der aktuelle Link trägt `aria-current="page"` ([Interaktive Zustände gestalten](interaktive-zustaende-gestalten.md#aktuell-und-ausgewählt)).

## Brotkrümelnavigation

Die Brotkrümelnavigation folgt dem Pfad des Knotens im Baum, der tiefer und technischer sein kann als das Menü.
Entscheiden Sie:

- ob die Startseite der erste Krümel ist und wie sie beschriftet wird;
- wie lange Pfade auf kleinen Bildschirmen gekürzt werden (oft nur das Elternelement, als Link "zurück zu");
- dass die aktuelle Seite der letzte Krümel ist, kein Link.

## Unternavigation

Eine Unternavigation (ein Seitenmenü des aktuellen Bereichs) hilft in tiefen Bereichen. Gestalten Sie sie für den
Bereich mit den meisten Unterelementen und für einen Bereich ohne, in dem sie verschwinden sollte, statt einen leeren
Kasten zu zeigen.

## Kleine Bildschirme und Tastaturen

- Bei geringen Breiten wandert das Hauptmenü hinter eine Menüschaltfläche. Gestalten Sie den geöffneten Zustand, das
  Bedienelement zum Schließen und was mit der zweiten Ebene geschieht (eine aufklappende Liste ist einfacher als
  verschachtelte Panels).
- Jede Ebene muss mit der Tastatur erreichbar sein, und Aufklappmenüs öffnen sich bei Aktivierung, nicht nur bei Hover
  ([Tastatur und Fokus](../designer/theme-design/tastatur-und-fokus.md#menüs-aufklappbereiche-und-dialoge)).
- Die Navigation ist eine Landmark (`nav` mit einer Bezeichnung), und die Menüschaltfläche sagt an, ob sie aufgeklappt
  ist ([Landmarks und Überschriften](../designer/theme-design/landmarks-und-ueberschriften.md)).

## Was Sie vor dem Zeichnen vereinbaren

- [ ] Die Quelle jedes Menüs (Baum, Ordner mit Links, Template).
- [ ] Welche Klassen im Hauptmenü erscheinen (meist nur Ordner).
- [ ] Die Zahl der Ebenen in jedem Menü.
- [ ] Wie viele Einträge Redakteure im nächsten Jahr voraussichtlich hinzufügen.
- [ ] Ob sich Menüs pro Siteaccess oder Sprache unterscheiden.

## Verwandte Seiten

- [Menüs und Navigation](../designer/vorlagen/menues-und-navigation.md)
- [Kopf- und Fußbereiche gestalten](kopf-und-fussbereiche-gestalten.md)
- [Cache-block](../designer/vorlagen/cache-block.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
