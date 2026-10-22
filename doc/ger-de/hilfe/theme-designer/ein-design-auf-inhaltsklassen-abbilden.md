---
title: "Ein Design auf Inhaltsklassen abbilden"
audience: theme-designer
category: theme-design
order: 6
summary: Wie Theme-Designer Mockups in eine Übersicht aus Inhaltsklassen, Feldern und Ansichtsmodi verwandeln - eine Klasse lesen, wie jeder gängige Datentyp aussehen kann, ein Zuordnungsblatt zum Ausfüllen und was Sie den Entwickler fragen, wenn ein Feld fehlt.
keywords: Theme-Designer, Inhaltsklasse, Felder, Attribute, Datentypen, Zuordnung, Inhaltsmodell, Mockup, Designinventar, ezstring, ezxmltext, ezimage, Verknüpfung, theme designer, content class, fields, attributes, datatypes, mapping, content model, design inventory, relation
type: help
---
# Ein Design auf Inhaltsklassen abbilden

Diese Seite ist für Theme-Designer, die ihre Mockups mit den Inhalten verbinden müssen, die eine Installation wirklich
speichert. In Exponential zeigt jede Seite ein Objekt einer **Inhaltsklasse**, und jedes sichtbare Stück Text oder
Medium stammt aus einem der **Felder** (Attribute) der Klasse. Ein Zuordnungsblatt, ausgefüllt, bevor Sie Einzelheiten
zeichnen, hält Design und Inhaltsmodell im Gleichschritt. Das Inhaltsmodell selbst ist Arbeit des Entwicklers:
[Ein Inhaltsmodell entwerfen](../../../eng-us/help/cms-developers/designing-a-content-model.md) erklärt, wie es entsteht.

## Inhalt

1. [Warum überhaupt zuordnen](#warum-überhaupt-zuordnen)
2. [Eine Klasse lesen](#eine-klasse-lesen)
3. [Wie jede Art von Feld aussehen kann](#wie-jede-art-von-feld-aussehen-kann)
4. [Das Zuordnungsblatt](#das-zuordnungsblatt)
5. [Ein durchgearbeitetes Beispiel](#ein-durchgearbeitetes-beispiel)
6. [Wenn das Design ein Feld braucht, das es nicht gibt](#wenn-das-design-ein-feld-braucht-das-es-nicht-gibt)

## Warum überhaupt zuordnen

Ein Mockup kann alles zeigen; ein Template kann nur zeigen, was ein Feld enthält. Drei Probleme findet die Zuordnung,
und später sind sie teuer:

- ein visuelles Element ohne Feld dahinter (ein Untertitel, eine Lesezeit, ein farbiges Label);
- ein Feld, das das Design vergessen hat (ein Autor, eine Bildunterschrift, ein Datum), das Redakteure ausfüllen und
  sehen möchten;
- ein Element, das als strukturiert gezeichnet ist, aber als freier formatierter Text gespeichert wird, oder umgekehrt.

## Eine Klasse lesen

Bitten Sie um ein Redakteurs- oder Administratorkonto auf einer Entwicklungsinstallation, öffnen Sie dann in der
Administration **Setup > Klassen** und die Klasse. Notieren Sie für jedes Feld den **Bezeichner** (zum Beispiel
`title`, `intro`, `image`), den **Datentyp** und ob es ein **Pflichtfeld** ist. Pflichtfelder sind immer da; jedes
optionale Feld braucht ein Design für "leer" ([Für Redakteure gestalten](fuer-redakteure-gestalten.md)).

Ein Entwickler kann Ihnen die Liste auch von der Kommandozeile oder aus der Klassendefinition im Paket des Projekts
geben. Hintergrund: [Inhaltsklassen und Attribute](../../../eng-us/help/cms-developers/content-classes-and-attributes.md).

## Wie jede Art von Feld aussehen kann

| Datentyp | Enthält | Designfragen |
|---|---|---|
| `ezstring` | eine Zeile Text | Längste realistische Länge? Bricht sie um oder wird sie gekürzt? |
| `eztext` | einfachen Text mit Zeilenumbrüchen | Absätze oder ein Block? |
| `ezxmltext` | formatierten Text: Überschriften, Listen, Links, Einbettungen, Tabellen | Jedes Element gestalten, das Redakteure einfügen können ([Templates für formatierten Text](../designer/vorlagen/rich-text-templates.md)) |
| `ezimage` | ein Bild mit Alternativtext | Welche Zuschnitte und Größen? ([Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md)) |
| `ezobjectrelation`, `ezobjectrelationlist` | Verknüpfungen zu anderen Objekten | Als Einbettung, Karte, Liste oder Link gezeigt? |
| `ezdate`, `ezdatetime`, `eztime` | Datum und Uhrzeit | Format pro Sprache ([Daten, Zahlen und l10n](../designer/vorlagen/datum-zahlen-und-l10n.md)) |
| `ezboolean` | ja oder nein | Ändert meist das Verhalten, nicht das Aussehen |
| `ezselection` | eine oder mehrere Auswahlen aus einer Liste | Ein Label, ein Kennzeichen, ein Filter? |
| `ezkeyword` | freie Schlüsselwörter | Tags, Links zu Tag-Seiten? |
| `ezurl`, `ezemail` | einen Link oder eine Adresse | Linktext, wenn die Beschriftung leer ist? |
| `ezauthor` | einen oder mehrere Autoren | Aufbau der Autorenzeile für einen, zwei, viele |
| `ezbinaryfile`, `ezmedia` | eine Datei, Audio oder Video | Download-Link mit Typ und Größe, ein Player? |
| `ezmatrix` | eine kleine Tabelle | Schmale Bildschirme? |

Der Kernel liefert unter `content/datatype/view/` Ansichts-Templates für jeden Datentyp, jedes Feld wird also schon
irgendwie dargestellt; Ihr Design entscheidet, wie ([Ansichts-Templates für Datentypen](../designer/vorlagen/datentyp-ansichtstemplates.md)).

## Das Zuordnungsblatt

Legen Sie eine Zeile für jedes sichtbare Element jedes Mockups an:

| Element im Mockup | Klasse | Bezeichner des Felds | Datentyp | Pflicht | Ansichtsmodi | Wenn leer |
|---|---|---|---|---|---|---|
| Schlagzeile | article | `title` | ezstring | ja | full, line, card | nie leer |
| Einleitender Absatz | article | `intro` | ezxmltext | ja | full, card | nie leer |
| Hero-Bild | article | `image` | ezobjectrelation | nein | full, card | Layout ohne Bild |

Bewahren Sie es neben den Mockups im Repository des Projekts auf. Entwickler schreiben danach die Templates, und
Prüfende vergleichen dagegen.

## Ein durchgearbeitetes Beispiel

Auf der Demonstrationsinstallation hat die Klasse Artikel `title` (Pflicht), `short_title`, `author`, `intro`
(Pflicht), `body`, `enable_comments` und `image`, wobei `image` eine Verknüpfung zu einem Bildobjekt ist, kein
Bildfeld. Das hat drei Folgen für das Design:

1. Die Karte kann `short_title` verwenden, wenn es ausgefüllt ist, und sonst `title`: Gestalten Sie einen Kartentitel,
   der in beiden Längen funktioniert.
2. Das Bild kommt aus einem anderen Objekt, also gehören sein Alternativtext und seine Bildunterschrift zu diesem
   Bildobjekt: Gestalten Sie die Bildunterschrift von dort aus.
3. `body` ist optional: Ein Artikel kann nur aus der Einleitung bestehen, die Vollansicht braucht also ein Design, das
   ohne Haupttext nicht kaputt aussieht.

Prüfen Sie Ihre eigene Installation; Klassen unterscheiden sich zwischen Websites.

## Wenn das Design ein Feld braucht, das es nicht gibt

Täuschen Sie es nicht im Template vor. Schreiben Sie dem Entwickler eine kurze Anfrage: das Element, warum es wichtig
ist, den erwarteten Datentyp, ob es Pflicht ist und in welchen Ansichten es erscheint. Ein Feld hinzuzufügen ist eine
Änderung am Inhaltsmodell und an der Arbeit der Redakteure, daher wird es gemeinsam entschieden
([Mit Entwicklern arbeiten](mit-entwicklern-arbeiten.md)).

Was besser **nicht** als Feld gespeichert wird: Werte, die aus anderen Feldern berechnet werden (Lesezeit), Werte, die
zum Rahmen gehören (der Name der Website), und rein dekorative Entscheidungen. Diese gehören ins Template oder in die
Einstellungen.

## Verwandte Seiten

- [Ansichtsmodi gestalten](ansichtsmodi-gestalten.md)
- [Knoten-Templates nach Klasse](../designer/vorlagen/knoten-templates-nach-klasse.md)
- [Knoten, Objekte und Attribute](../designer/vorlagen/knoten-objekte-und-attribute.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
