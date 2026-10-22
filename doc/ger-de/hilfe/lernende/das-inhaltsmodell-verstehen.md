---
title: Das Inhaltsmodell verstehen
audience: student
category: learning
order: 18
summary: Eine Erklärung des Inhaltsmodells von Exponential für Lernende - Inhaltsklassen, Attribute und Datentypen, Objekte, Versionen und Übersetzungen, Knoten und der Inhaltsbaum, Sektionen und Objektzustände -, mit einem durchgearbeiteten Beispiel, den häufigsten Verwechslungen und Übungen, um Ihr Verständnis zu prüfen.
keywords: inhaltsmodell, inhaltsklasse, attribut, datentyp, objekt, version, übersetzung, knoten, platzierung, inhaltsbaum, sektion, objektzustand, erklärung, lernende, content model, content class, attribute, datatype, object, translation, node, location, content tree, section, object state, explanation, learner
type: help
---
# Das Inhaltsmodell verstehen

Das Inhaltsmodell ist die Idee, die jede Rolle in Exponential nutzt, von Redakteuren bis zu Entwicklern. Das README
nennt das „vollständig anpassbare und erweiterbare Inhaltsmodell“ die bemerkenswerteste Eigenschaft des Systems
([Presse, Medien und Influencer: hier beginnen](../presse/presse-hier-beginnen.md#exponential-in-einem-absatz)). Diese
Seite erklärt es für Lernende, mit einem durchgearbeiteten Beispiel. Die Referenz ist
[Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md).

## Inhalt

1. [Die Begriffe](#die-begriffe)
2. [Ein durchgearbeitetes Beispiel](#ein-durchgearbeitetes-beispiel)
3. [Objekte und Knoten](#objekte-und-knoten)
4. [Versionen und Übersetzungen](#versionen-und-übersetzungen)
5. [Sektionen und Zustände](#sektionen-und-zustände)
6. [Häufige Verwechslungen](#häufige-verwechslungen)
7. [Prüfen Sie Ihr Verständnis](#prüfen-sie-ihr-verständnis)

## Die Begriffe

| Begriff | Bedeutung |
|---|---|
| **Inhaltsklasse** | Eine Art von Inhalt: ein Name, ein Bezeichner und eine Liste von Attributen (zum Beispiel Artikel: Titel, Einleitung, Haupttext, Bild) |
| **Klassengruppe** | Ein Ordner von Klassen im Klasseneditor (Content, Users, Media, Setup) |
| **Attribut** | Ein Feld einer Klasse, mit einem Datentyp |
| **Datentyp** | Die Art eines Attributs: `ezstring` (eine Zeile), `eztext` (mehrere Zeilen), `ezxmltext` (Rich Text), `ezimage`, `ezobjectrelationlist` und so weiter |
| **Inhaltsobjekt** | Ein einzelner Inhalt, eine Instanz einer Klasse; hat Versionen (Entwurf, veröffentlicht, archiviert) und Sprachen |
| **Knoten / Platzierung** | Ein Platz eines Objekts im Inhaltsbaum; ein Objekt kann mehrere haben |

Quelle: [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md#1-wie-das-inhaltsmodell-zusammenpasst).
Die Datentypen, die für neue Attribute zur Verfügung stehen, sind die Liste `AvailableDataTypes[]` in
`settings/content.ini`; die Anleitung zählt 35 Einträge in der ausgelieferten Liste, und Erweiterungen können weitere
hinzufügen.

## Ein durchgearbeitetes Beispiel

Denken Sie an eine Schülerzeitung:

```text
Class "Article"          attributes: Title (Text line), Intro (Text block), Body (Rich text), Image (Image)
  |
  +-- Object 4711        "School garden opens"        versions 1, 2, 3 (3 is published)
        |                                             translations: eng-US (main), ger-DE
        +-- Node 120     Home / News / School garden opens
        +-- Node 233     Home / Garden club / School garden opens
```

Eine **Klasse** beschreibt, was jeder Artikel hat. Ein **Objekt** ist ein Artikel. Es erscheint im Baum an zwei
**Knoten**: unter News und unter Garden club. Wenn Sie es einmal bearbeiten, ändern sich beide Orte, weil es nur ein
Objekt gibt.

## Objekte und Knoten

- Der **Inhaltsbaum** ist die Hierarchie der Knoten, die die Administrationsoberfläche zeigt; Knoten 1 ist seine
  Wurzel ([Glossar](../../glossar.md#c)).
- Die Seite eines Elements zeigt beide Nummern, **Knoten** und **Objekt** ([Rundgang durch die Administration](../rundgang-durch-die-administration.md#die-seite-eines-objekts)).
- Der Reiter **Orte** eines Elements listet seine Knoten auf. Das Hinzufügen einer Platzierung kopiert das Objekt nicht.
- Der **Inhaltsfinder** öffnet ein Element über seine Nummer, seinen Pfad oder seine Adresse
  ([Suchen](../suchen.md#ein-objekt-über-seine-nummer-oder-adresse-öffnen)).

Entwickler begegnen derselben Unterscheidung in Templates: `$node` ist ein Knoten, und `$node.data_map` liefert die
Attribute seines Objekts ([Templates und Design](../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten)).

## Versionen und Übersetzungen

- Jedes Speichern einer Bearbeitung erzeugt eine **Version**; eine unfertige Bearbeitung ist ein **Entwurf**. Die
  Anzahl der aufbewahrten archivierten Versionen ist je Klasse begrenzt (`[VersionManagement] DefaultVersionHistoryLimit`
  in `settings/content.ini`, standardmäßig 45) ([Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md#3-ein-objekt-erstellen-und-bearbeiten)).
- Eine **Übersetzung** ist eine Sprachfassung eines Objekts. Jedes Objekt hat eine **Hauptsprache** und kann als
  **immer verfügbar** eingestellt werden, sodass es in seiner Hauptsprache gezeigt wird, wenn keine der aufgeführten
  Sprachen vorhanden ist ([Inhaltssprachen](../../anleitungen/inhaltssprachen.md#6-hauptsprache-und-immer-verfügbar)).
- Das Veröffentlichen einer Übersetzung ändert die anderen nicht ([Sprachen](../sprachen.md#ein-objekt-übersetzen)).

## Sektionen und Zustände

- Eine **Sektion** ist eine Kennzeichnung an Inhalten, die ein Design und Berechtigungen auswählt
  ([Glossar](../../glossar.md#s), [Sektionen](../../anleitungen/sektionen.md)). Rollen können auf eine Sektion
  eingeschränkt werden.
- Ein **Objektzustand** ist eine Kennzeichnung aus einer Objektzustandsgruppe, zum Beispiel der Sperrzustand „Not
  locked“, den Richtlinien prüfen können; er ist nicht dasselbe wie der Status einer Version
  ([Glossar](../../glossar.md#s), [Objektzustände](../../anleitungen/objektzustaende.md)).

## Häufige Verwechslungen

| Verwechslung | Richtig ist |
|---|---|
| „Klasse“ bedeutet eine PHP-Klasse | Hier ist meist eine **Inhaltsklasse** gemeint, in der Administrationsoberfläche definiert |
| Verschieben kopiert ein Element | Verschieben ändert seinen Knoten; das Objekt bleibt dasselbe |
| Zwei Platzierungen sind zwei Kopien | Zwei Knoten, ein Objekt: Eine Bearbeitung ändert beide |
| Ein Entwurf ist für Besucher sichtbar | Auf der Website werden nur veröffentlichte Versionen gezeigt |
| Sektion und Ordner sind dasselbe | Ein Ordner ist ein Knoten im Baum; eine Sektion ist eine Kennzeichnung, die viele Knoten tragen können |
| Objektzustand und Versionsstatus sind dasselbe | Ein Zustand ist eine Kennzeichnung für Richtlinien; Entwurf, veröffentlicht und archiviert sind Versionsstatus |

## Prüfen Sie Ihr Verständnis

1. Ein Objekt hat zwei Platzierungen. Sie bearbeiten seinen Titel. Wie viele Orte auf der Website ändern sich?
2. Welche Einstellungsdatei listet die Datentypen, die Sie für ein neues Attribut wählen können?
3. Wo in der Administrationsoberfläche sehen Sie die Knoten- und die Objektnummer eines Elements?
4. Nennen Sie zwei Dinge, die eine Sektion entscheiden kann.
5. Üben: Bearbeiten Sie [Projekt 1](projekt-01-eine-eigene-inhaltsklasse.md) und zeichnen Sie Ihre Klasse wie im
   durchgearbeiteten Beispiel.

<details>
<summary>Antworten</summary>

1. Beide, weil es ein einziges Objekt ist.
2. `settings/content.ini`, die Liste `AvailableDataTypes[]`.
3. Auf der Seite des Elements, in der Zeile mit seiner Version und seinen Nummern, und im Reiter **Details**.
4. Welches Design verwendet wird und welche Berechtigungen gelten.

</details>

## Verwandte Seiten

- [Die Template-Sprache lernen](die-template-sprache-lernen.md)
- [Siteaccesses und Einstellungen verstehen](siteaccesses-und-einstellungen-verstehen.md)
- [Glossar für Lernende](glossar-fuer-lernende.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
