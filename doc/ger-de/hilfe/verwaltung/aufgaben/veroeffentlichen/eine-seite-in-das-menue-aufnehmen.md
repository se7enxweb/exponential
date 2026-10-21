---
title: Eine Seite in das Menü aufnehmen
audience: government, editor
category: public-sector
order: 160
summary: Für Redakteure - eine Seite im Menü der Site erscheinen lassen, in Exponential, wo Menüs aus dem Inhaltsbaum entstehen - das richtige übergeordnete Element und eine Menüklasse wählen, ihren Platz in der Reihenfolge festlegen und Seiten bei Bedarf aus dem Menü heraushalten.
keywords: Menü, Navigation, oberes Menü, linkes Menü, Inhaltsbaum, Menüklassen, Priorität, Reihenfolge, aus dem Menü ausblenden, menu, navigation, top menu, left menu, content tree, menu classes, MenuContentSettings, priority, order, hide from menu
type: help
---
# Eine Seite in das Menü aufnehmen

Diese Seite ist für Redakteure, die eine Seite im Menü haben möchten. Auf einer Exponential-Site sind Menüs keine
eigene Liste: Sie entstehen aus dem Inhaltsbaum. Eine Seite einer Menüklasse, die direkt unter einem Ordner liegt, der
im Menü ist, wird zu einem Eintrag ([Menüs und Navigation](../../../ersteller/website-ersteller/menues-und-navigation.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Wissen Sie, welche Klassen die Menüs Ihrer Site zeigen. Designs auf Basis von ezwebin lesen
  `menu.ini [MenuContentSettings]`; der Kernel liefert `folder`, `feedback_form`, `gallery` und `forum` mit, und
  ezwebin fügt weitere hinzu (`documentation_page`, `event_calendar`, `link`, `blog` ...). Ihre Site kann abweichen;
  fragen Sie das Web-Team.
- Wissen Sie, wohin im Baum der Eintrag gehört: unter die Wurzel für das obere Menü, unter einen Ordner der obersten
  Ebene für dessen Untermenü.

## Schritte

1. Öffnen Sie das übergeordnete Element: den Wurzelordner (für das obere Menü) oder einen Ordner der obersten Ebene
   (für dessen Untermenü).
2. **Neu erstellen**, wählen Sie eine Menüklasse, meist **Ordner**; geben Sie ihr den Namen, der im Menü erscheinen
   soll (**Short name**, wenn der Name lang ist), und ihren Inhalt oder ihre Unterelemente.
3. **Zur Veröffentlichung schicken**.
4. Legen Sie ihren Platz in der Reihenfolge fest: Öffnen Sie das übergeordnete Element, Reiter **Anordnung**: Wenn
   **Sortieren nach** auf *Priorität* steht, tragen Sie Zahlen in die Spalte **Priorität** der Liste **Unterelemente**
   ein und klicken Sie auf **Prioritäten aktualisieren**, oder ziehen Sie Zeilen an ihrem Griff
   ([Seiten in einem Ordner neu ordnen](seiten-in-einem-ordner-neu-ordnen.md)).

Um eine bestehende Seite in ein Menü aufzunehmen, verschieben Sie sie entweder unter einen Menüordner
([Eine Seite verschieben](eine-seite-verschieben.md)) oder geben ihr dort einen zusätzlichen Ort
([Ein Element an mehreren Stellen veröffentlichen](ein-element-an-mehreren-stellen-veroeffentlichen.md)). Ein Link zu einer anderen
Site im Menü ist ein Objekt der Klasse **Link**, sofern das Menü Links auflistet.

## Das Ergebnis prüfen

- [ ] Abgemeldet zeigt das Menü den neuen Eintrag an der richtigen Stelle.
- [ ] Die Brotkrumennavigation auf der Seite zeigt den Pfad.
- [ ] Wenn er nicht erscheint: Die Klasse ist vielleicht nicht in den Menülisten, die Seite ist vielleicht versteckt,
  oder das Menü liegt im Cache (es erneuert sich mit seinem Cache;
  [Eine Seite aktualisieren, die alte Inhalte zeigt](eine-seite-aktualisieren-die-alte-inhalte-zeigt.md)).

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Ein Artikel auf der obersten Ebene in der Erwartung eines Menüeintrags | Artikel sind meist keine Menüklassen; verwenden Sie einen Ordner |
| Ein sehr langes Menü | gruppieren Sie Seiten unter Ordnern; streben Sie ein kurzes oberes Menü an |
| Menünamen, die von den Seitentiteln abweichen | verwenden Sie **Short name** einheitlich |
| Eine Seite aus dem Menü heraushalten | verstecken Sie sie oder verwenden Sie eine Klasse, die nicht in den Menülisten steht |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Den Inhaltsbaum strukturieren](../../../ersteller/website-ersteller/den-inhaltsbaum-strukturieren.md)
- [Klassische Menüeinstellungen](../../../../anleitungen/klassische-menueeinstellungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
