---
title: "Für Redakteure gestalten"
audience: theme-designer
category: theme-design
order: 8
summary: Designregeln, die ein Exponential-Theme funktionsfähig halten, wenn Redakteure statt Designer es füllen - variable Textlänge, optionale Felder, Bilder in jeder Form, formatierter Text, Übersetzungen und die Feldbeschreibungen, die Redakteure im Bearbeitungsformular anleiten.
keywords: Theme-Designer, Redakteure, robustes Design, optionale Felder, variable Länge, leere Felder, formatierter Text, Feldbeschreibung, redaktionelle Anleitung, inhaltsfestes Design, theme designer, editors, robust design, optional fields, variable length, empty fields, rich text, field description, editorial guidance, content-proof design
type: help
---
# Für Redakteure gestalten

Diese Seite ist für Theme-Designer, deren Themes von Redakteuren gefüllt werden. Nach dem Start entscheiden die
Redakteure, nicht Sie, was eine Seite enthält. Ein Design, das nur mit den Inhalten funktioniert, die Sie ausgewählt
haben, sieht innerhalb von Wochen kaputt aus. Diese Seite listet die Regeln auf, die ein Design redakteursfest machen,
und wie Sie Redakteuren sagen, was das Design erwartet.

## Inhalt

1. [Wer das Design füllt](#wer-das-design-füllt)
2. [Regel 1: Jeder Text wächst](#regel-1-jeder-text-wächst)
3. [Regel 2: Jedes optionale Feld hat ein Design für leer](#regel-2-jedes-optionale-feld-hat-ein-design-für-leer)
4. [Regel 3: Bilder kommen in jeder Form](#regel-3-bilder-kommen-in-jeder-form)
5. [Regel 4: Formatierter Text ist eine Designfläche](#regel-4-formatierter-text-ist-eine-designfläche)
6. [Regel 5: Listen haben beliebig viele Einträge](#regel-5-listen-haben-beliebig-viele-einträge)
7. [Regel 6: Kein Design hängt davon ab, dass Redakteure sich erinnern](#regel-6-kein-design-hängt-davon-ab-dass-redakteure-sich-erinnern)
8. [Redakteuren sagen, was das Design erwartet](#redakteuren-sagen-was-das-design-erwartet)

## Wer das Design füllt

Redakteure schreiben in der Administration oder der Redakteursoberfläche, sehen sich einen Entwurf in der Vorschau an
und veröffentlichen ([Inhalte bearbeiten](../inhalte-bearbeiten.md), [Veröffentlichen und Versionen](../veroeffentlichen-und-versionen.md)).
Auf Websites mit Layouts ordnen sie auch Blöcke an ([Der Layout-Editor](../layout-editor.md)). Sie sind keine
Designer, sie arbeiten unter Zeitdruck, und sie arbeiten in mehreren Sprachen. Gestalten Sie dafür.

## Regel 1: Jeder Text wächst

- Schlagzeilen brechen um; entscheiden Sie die höchste Zeilenzahl und was danach geschieht (meist nichts: Lassen Sie
  sie umbrechen).
- Schaltflächen und Beschriftungen nehmen übersetzten Text auf, der oft ein Drittel länger ist als der englische.
- Setzen Sie Text nie in einen Kasten mit fester Höhe. Verwenden Sie `min-height`, wenn ein Minimum nötig ist.
- Kürzen (`text-overflow: ellipsis` oder eine Zeilenbegrenzung) ist eine Designentscheidung nur für Listen und Karten,
  nie für die Vollansicht, und der vollständige Text muss erreichbar bleiben.

## Regel 2: Jedes optionale Feld hat ein Design für leer

Zeichnen Sie für jedes optionale Feld in Ihrem [Zuordnungsblatt](ein-design-auf-inhaltsklassen-abbilden.md#das-zuordnungsblatt)
das Layout ohne es. Übliche Antworten:

| Fehlendes Feld | Antwort des Designs |
|---|---|
| Bild auf einer Karte | eine Kartenvariante nur mit Text, kein grauer Platzhalterkasten |
| Einleitung | die Karte zeigt nur Titel und Datum |
| Autor | gar keine Autorenzeile, nicht "Von ." |
| Bildunterschrift | das Bild ohne Bereich für die Bildunterschrift |
| Haupttext | die Vollansicht endet nach der Einleitung, ohne leeren Abschnitt |

Das Template umschließt dann jedes optionale Element mit einer Bedingung, sodass leere Hüllen nie auf die Seite
gelangen ([If und Bedingungen](../designer/vorlagen/if-und-bedingungen.md)).

## Regel 3: Bilder kommen in jeder Form

Redakteure laden Hochformate, Panoramen, Bildschirmfotos und Logos hoch. Bild-Aliase verkleinern ein Bild, damit es in
einen Kasten passt; entscheiden Sie pro Platz, ob das Bild ganz gezeigt wird (mit Rändern in seinem Platz) oder mit
`object-fit: cover` im Stylesheet auf ein festes Seitenverhältnis zugeschnitten wird, und akzeptieren Sie, dass beim
Zuschneiden Gesichter oder Text verloren gehen können. Siehe
[Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md).

## Regel 4: Formatierter Text ist eine Designfläche

Felder mit formatiertem Text lassen Redakteure Überschriften, Listen, Tabellen, Zitate, Links, eingebettete Objekte und
Custom-Tags verwenden. Jedes braucht einen Stil, und jedes muss neben den anderen funktionieren. Gestalten Sie einen
Artikel "mit allem" mit jedem Element und nehmen Sie ihn in Ihre Stressfälle auf
([Mit echten Inhalten gestalten](mit-echten-inhalten-gestalten.md#die-stressfälle)). Templates für Custom-Tags und
Einbettungen: [Templates für formatierten Text](../designer/vorlagen/rich-text-templates.md),
[Templates für Custom-Tags](../designer/vorlagen/templates-fuer-custom-tags.md), [Einbettungsansicht](../designer/vorlagen/embed-ansicht.md).

## Regel 5: Listen haben beliebig viele Einträge

Ein dreispaltiges Raster mit zwei Einträgen, ein Slider mit einer Folie, eine Liste mit 400 Nachrichten: Gestalten Sie
für null, einen, einige und viele. Lange Listen brauchen eine Seitennavigation ([Seitennavigation](../designer/vorlagen/seitenumbruch.md));
leere Listen brauchen einen leeren Zustand ([Leere Zustände und Fehlerzustände gestalten](leere-zustaende-und-fehlerzustaende-gestalten.md)).

## Regel 6: Kein Design hängt davon ab, dass Redakteure sich erinnern

Wenn ein Element nur richtig aussieht, wenn der Redakteur ein Bild mit 1600 mal 900 Pixeln hochlädt, genau zwei Zeilen
schreibt oder die richtige Farbklasse wählt, wird es falsch aussehen. Bevorzugen Sie Designs, bei denen das Template die
Regel durchsetzt (ein Alias, ein Seitenverhältnis, eine feste Menge von Ansichtstypen für Blöcke), statt Regeln, die
Redakteure sich merken müssen.

## Redakteuren sagen, was das Design erwartet

Wo Anleitung hilft, setzen Sie sie dorthin, wo Redakteure sie sehen:

- **Feldbeschreibungen.** Eine Inhaltsklasse kann pro Feld eine Beschreibung tragen, die neben dem Feld im
  Bearbeitungsformular erscheint. Bitten Sie den Entwickler, dort eine kurze Anleitung zu ergänzen: "Wird auf Karten
  gezeigt; 60 Zeichen oder weniger lesen sich am besten." Das Bearbeitungs-Template der Administration gibt sie aus der
  Beschreibung des Klassenattributs aus.
- **Ansichtstypen von Blöcken.** In Layouts ist ein Ansichtstyp mit klarem Namen ("Karten, drei Spalten") selbst eine
  Anleitung ([Layoutblöcke entwerfen](layoutbloecke-gestalten.md)).
- **Eine Styleguide-Seite** auf der Website, die jede Komponente mit echten Inhalten zeigt
  ([Eine Styleguide-Seite bauen](eine-styleguide-seite-bauen.md)).
- **Schulung.** Die Kurse für Redakteure stehen unter [Editors 101](../trainer/redakteure-101/README.md).

## Verwandte Seiten

- [Mit echten Inhalten gestalten](mit-echten-inhalten-gestalten.md)
- [Grundlagen der Barrierefreiheit für Themes](../designer/theme-design/barrierefreiheit-grundlagen-fuer-themes.md)
- [Häufige Fehler in Templates](../designer/vorlagen/haeufige-template-fehler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
