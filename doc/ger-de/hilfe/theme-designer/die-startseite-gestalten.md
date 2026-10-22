---
title: "Die Startseite gestalten"
audience: theme-designer
category: theme-design
order: 40
summary: Wie Theme-Designer die Startseite einer Exponential-Website angehen - die drei Arten, eine Startseite zu bauen (ein Layout, eine eigene Klasse, ein Override des Wurzelknotens), wie Sie mit dem Kunden wählen, Gestaltung für täglich wechselnde Inhalte und die Rolle der Startseite im Rahmen.
keywords: Theme-Designer, Startseite, Homepage, Landingpage, Layout, Startseitenklasse, Override, Wurzelknoten, Redakteure, hervorgehobene Inhalte, Hero, theme designer, front page, home page, landing page, layout, frontpage class, override, root node, editors, featured content, hero
type: help
---
# Die Startseite gestalten

Diese Seite ist für Theme-Designer, die die Startseite einer Website gestalten. Es ist die Seite, die Kunden am
wichtigsten ist und die sich am häufigsten ändert; wie sie gebaut wird, entscheidet also, was Redakteure ohne Sie ändern
können. Exponential bietet drei Arten, sie zu bauen; die Wahl ist ebenso eine Design- wie eine technische Entscheidung
und sollte fallen, bevor das Mockup endgültig ist.

## Inhalt

1. [Drei Arten, sie zu bauen](#drei-arten-sie-zu-bauen)
2. [Mit dem Kunden wählen](#mit-dem-kunden-wählen)
3. [Eine Startseite aus Blöcken gestalten](#eine-startseite-aus-blöcken-gestalten)
4. [Eine feste Startseite gestalten](#eine-feste-startseite-gestalten)
5. [Inhalte, die sich täglich ändern](#inhalte-die-sich-täglich-ändern)
6. [Die Startseite und der Rahmen](#die-startseite-und-der-rahmen)
7. [Prüfungen](#prüfungen)

## Drei Arten, sie zu bauen

| Art | Wie | Wer die Anordnung ändert |
|---|---|---|
| Ein Layout | die Wurzelseite erhält ein Layout aus dem Layout-Editor; Redakteure platzieren und ordnen Blöcke | Redakteure |
| Eine eigene Klasse | eine Startseitenklasse mit Feldern (Hero, hervorgehobene Einträge, Einleitung), gezeichnet von ihrem eigenen Vollansichts-Template | das Template; Redakteure füllen die Felder |
| Ein Override des Wurzelknotens | die Vollansicht des Wurzelknotens erhält ein eigenes Template, zugeordnet über die Remote-ID | das Template; Inhalte kommen aus Abrufen |

Die Template-basierten Arten sind in [Einen einzelnen Knoten überschreiben](../designer/vorlagen/einen-einzelnen-knoten-ueberschreiben.md)
und seinen Alternativen erklärt; die Art mit Layouts in [Tutorial: eine Landingpage mit Layouts bauen](../designer/theme-design/tutorial-eine-landingpage-mit-layouts-bauen.md).

## Mit dem Kunden wählen

| Frage | Spricht für |
|---|---|
| Werden Redakteure die Reihenfolge ändern oder selbst Abschnitte ergänzen wollen? | ein Layout |
| Ist die Startseite eine strenge Komposition, von der die Marke abhängt? | eine Klasse oder ein Override |
| Werden mehrere Seiten (Kampagnenseiten, Startseiten von Bereichen) dieselbe Struktur teilen? | ein Layout oder eine Klasse für alle |
| Ist die Startseite größtenteils automatisch (neueste Nachrichten, kommende Veranstaltungen)? | ein Override mit Abrufen oder Listenblöcke mit Abfragen in einem Layout |

Die meisten Inhaltswebsites landen bei einem Layout: Redakteure erhalten Freiheit innerhalb der Blöcke und Ansichtstypen,
die Sie gestaltet haben. Schreiben Sie die Wahl und den Grund auf; sie entscheidet, was der Kunde später ohne Designer
tun kann.

## Eine Startseite aus Blöcken gestalten

Gestalten Sie die Startseite als **Beispielanordnung** Ihrer Blöcke, nicht als feste Komposition, und sagen Sie das,
wenn Sie sie präsentieren. Dann:

- prüfen Sie, dass jeder Block darin auch an anderen Positionen und neben anderen Blöcken funktioniert
  ([Layoutblöcke entwerfen](layoutbloecke-gestalten.md#in-jeder-reihenfolge-in-jeder-breite));
- gestalten Sie zwei oder drei alternative Anordnungen (eine Kampagnenwoche, eine ruhige Woche), um zu zeigen, dass das
  System trägt;
- entscheiden Sie, welche Blöcke zu einem gemeinsamen Layout gehören (oft ein Bereich vor dem Fußbereich) und auf vielen
  Seiten erscheinen ([Gemeinsame Layouts und verknüpfte Zonen](../designer/theme-design/gemeinsame-layouts-und-verknuepfte-zonen.md)).

## Eine feste Startseite gestalten

Auch eine feste Komposition muss wechselnde Inhalte aufnehmen. Ordnen Sie jedem Bereich seine Quelle zu: ein Feld des
Startseitenobjekts, ein Abruf der neuesten Einträge einer Klasse oder eine Verknüpfungsliste, die Redakteure füllen
([Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md)). Zeichnen Sie jeden Bereich mit null,
einem und der höchsten Zahl von Einträgen.

## Inhalte, die sich täglich ändern

- Das Hero-Bild wechselt; gestalten Sie seinen Platz für jedes Bild, das Redakteure wählen, mit einer Zuschnittsregel und
  Text, der auf jedem Bild lesbar bleibt (Text neben dem Bild oder auf einem einfarbigen Band statt über dem Foto)
  ([Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md)).
- Listen der neuesten Einträge unterscheiden sich in der Länge der Titel und im Vorhandensein von Bildern
  ([Inhaltslisten und Karten gestalten](inhaltslisten-und-karten-gestalten.md)).
- Vermeiden Sie Bereiche, die nur mit genau drei Einträgen richtig aussehen.
- Startseiten werden gecacht; ein Entwickler entscheidet, wie aktuell automatische Listen aktualisiert werden
  ([Caching für Designer](../designer/theme-design/caching-fuer-designer.md)).

## Die Startseite und der Rahmen

Die Startseite steht im selben Pagelayout wie jede andere Seite. Braucht die Startseite einen anderen Kopfbereich (zum
Beispiel einen transparenten Kopfbereich über dem Hero), ist das eine Variante des Rahmens für eine Seite; sie kostet
eine Bedingung im Pagelayout und Tests in beiden Formen. Bevorzugen Sie eine Startseite, die mit dem normalen
Kopfbereich funktioniert ([Kopf- und Fußbereiche gestalten](kopf-und-fussbereiche-gestalten.md)).

## Prüfungen

- [ ] Die Art, sie zu bauen, ist gewählt und aufgeschrieben.
- [ ] Jeder Bereich einer Quelle zugeordnet; null, ein und viele Einträge gezeichnet.
- [ ] Zwei alternative Anordnungen, wenn es ein Layout ist.
- [ ] Der Hero ist mit einem dunklen und einem hellen Foto lesbar, im hellen und im dunklen Theme.
- [ ] Der erste Bildschirm hält das Performance-Budget ein ([Für Performance-Budgets gestalten](fuer-performance-budgets-gestalten.md#der-erste-bildschirm)).

## Verwandte Seiten

- [Layouts für Designer](../designer/theme-design/layouts-fuer-designer.md)
- [Layout-Regeln](../designer/theme-design/layout-regeln.md)
- [Der Layout-Editor](../layout-editor.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
