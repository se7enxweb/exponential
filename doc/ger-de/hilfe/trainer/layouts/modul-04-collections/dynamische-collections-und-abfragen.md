---
title: "Lektion 4.2: Dynamische Collections und Abfragetypen"
audience: trainer, editor, website-creator
category: training
order: 42
summary: Die Teilnehmenden geben einem Listenblock eine dynamische Collection, wählen einen Abfragetyp, verwenden den aktuellen Ort oder einen festen übergeordneten Ort, filtern nach Inhaltstyp und sortieren und erklären, was ein Layout wiederverwendbar macht. 60 Minuten.
keywords: exponential layouts, dynamische collection, abfrage, abfragetyp, children, subtree, latest, exponential-inhaltssuche, use current location, übergeordneter ort, inhaltstyp-filter, sortierart, sortierrichtung, dynamic collection, query, query type, exponential content search, parent location, content type filter, sort type, sort direction
type: help
---
# Lektion 4.2: Dynamische Collections und Abfragetypen

Eine Liste, die sich selbst aktuell hält, braucht eine **dynamische** Collection: eine Abfrage, die ausgeführt wird,
wenn die Seite angezeigt wird. Diese Lektion vermittelt die Abfragetypen und die eine Einstellung, die ein Layout auf
einer ganzen Website wiederverwendbar macht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 4: Collections](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure und Website-Ersteller |
| Voraussetzungen | [Lektion 4.1](listenbloecke-und-manuelle-collections.md); ein Ordner mit mehreren Artikeln auf der Schulungs-Website |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- einem Listenblock eine dynamische Collection geben und einen Abfragetyp wählen;
- zwischen **use current location** und einem festen übergeordneten Ort wählen und den Unterschied erklären;
- nach Inhaltstyp filtern und Sortierart und -richtung festlegen;
- eine einfachere Abfrage (Unterelemente, Teilbaum, neueste) der allgemeinen Inhaltssuche vorziehen, wenn sie passt.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Abfragetypen und ihre Parameter](../../../../../eng-us/bc/6.0/LAYOUTS.md#query-types-and-their-parameters) und
[Die Exponential-Inhaltssuche](../../../../../eng-us/bc/6.0/LAYOUTS.md#the-exponential-content-search).

1. **Abfragetypen** (5 Minuten). Zwölf werden mitgeliefert: **Children of a node**, **Parent of a node**, **Subtree
   of a node**, **Siblings of a node**, **Latest content**, **Random content**, **Manual collection**, Relationsliste
   und umgekehrte Relationsliste, Tags, die allgemeine **Exponential**-Inhaltssuche und **Topics**.
2. **Der Parameterbereich** (5 Minuten). Vier Grundparameter stehen zuerst: das Thema aus dem aktuellen Inhalt
   verwenden, den aktuellen Ort verwenden, Sortierart und Sortierrichtung. Alles andere ist erweitert und
   eingeklappt. Manche Kontrollkästchen blenden Felder ein: *use current location* zeigt ein Feld für den
   übergeordneten Ort, wenn es **nicht** angekreuzt ist; *filter by content type*, *filter by section* und *filter by
   object state* zeigen ihre Listen, wenn sie angekreuzt sind.
3. **Das wiederverwendbare Layout** (5 Minuten). Mit angekreuztem *use current location* listet eine Abfrage
   **Children of a node** die Unterelemente der jeweils angezeigten Seite: Ein Layout dient jedem Bereich. Nicht
   angekreuzt, ist sie an einen Ort gebunden. Das ist der Unterschied zwischen einem Layout, das einmal geschrieben
   wird, und einem, das zwanzigmal kopiert wird.
4. **Einfach zuerst** (3 Minuten). Verwenden Sie Unterelemente oder Teilbaum, wenn sie passen; die allgemeine
   Inhaltssuche, wenn Sie ihre Filter brauchen. Einfache Abfragen sind auch in sechs Monaten noch leicht zu lesen.

### Häufige Fragen

**Warum ist meine dynamische Liste im Editor leer?** Mit *use current location* hat der Editor keine aktuelle Seite;
die Vorschau oder die Website zeigen die echten Elemente. Oder der Filter schließt alles aus.

**Kann eine dynamische Liste Elemente aus zwei Ordnern zeigen?** Verwenden Sie die Inhaltssuche mit einem gemeinsamen
übergeordneten Ort oder zwei Blöcke.

**Aktualisiert sich die Liste sofort, wenn ein Artikel veröffentlicht wird?** Beim nächsten Seitenaufruf, abhängig von
den Caches der Website.

### Stolperfallen

- **Umgekehrte Kontrollkästchen.** *Use current location* blendet das Ortsfeld aus, wenn es angekreuzt ist; die
  Teilnehmenden suchen das Feld.
- **Zu breite Abfragen.** Ein Teilbaum der ganzen Website ohne Inhaltstyp-Filter listet auch Ordner und Bilder auf.
- **Standardsortierung.** Legen Sie die Sortierung ausdrücklich fest (für Nachrichten die neuesten zuerst).

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihr Kurs-Layout. Der Trainer hat einen Ordner mit Artikeln genannt (zum
Beispiel **Training** mit den Kursordnern der Teilnehmenden darunter).

1. Öffnen Sie Ihr Kurs-Layout mit **Öffnen** (setzen Sie Ihren Entwurf fort, wenn Sie gefragt werden).
2. Fügen Sie in *MAIN* unter *Hand-picked articles* einen zweiten **List**-Block hinzu. Nennen Sie ihn
   `Newest articles`.
3. Geben Sie ihm eine dynamische Collection und wählen Sie den Abfragetyp **Exponential** (die allgemeine
   Inhaltssuche) oder **Subtree of a node**, wenn Ihre Schulungsinstallation das einfacher anbietet.
4. Entfernen Sie das Häkchen bei *use current location* und wählen Sie den Ordner des Trainers als übergeordneten Ort.
5. Kreuzen Sie *filter by content type* an und wählen Sie *Article*.
6. Setzen Sie die Sortierart auf das Veröffentlichungsdatum und die Richtung auf absteigend.
7. Sehen Sie sich die Elemente an, die der Block im Editor zeigt.
8. Ändern Sie die Abfrage in **Children of a node** mit angekreuztem *use current location* und notieren Sie, was der
   Editor zeigt. Stellen Sie danach wieder die Inhaltssuche aus den Schritten 3 bis 6 ein.
9. Wählen Sie **Entwurf speichern und schließen**.

## Erwartetes Ergebnis

- Der Entwurf hat einen Block *Newest articles*, der die neuesten Artikel unter dem Ordner des Trainers auflistet, die
  neuesten zuerst.
- Schritt 8 hat gezeigt, dass eine an den aktuellen Ort gebundene Abfrage im Editor nichts (oder ein Beispiel) zeigt.
- Der Trainer prüft das, indem er nach Modul 6 die Vorschau des Layouts einer Person ansieht oder den Entwurf am
  Beamer öffnet.

## Quiz

1. Nennen Sie vier Abfragetypen.
2. Was bewirkt *use current location*?
3. Welche Einstellung macht aus einer Unterelement-Liste eine, die jedem Bereich einer Website dient?
   A. Sortierrichtung B. Use current location C. Limit D. Offset
4. Warum sollten Sie **Children of a node** der allgemeinen Inhaltssuche vorziehen, wenn es passt?
5. Ihre dynamische Liste zeigt Ordner und Bilder. Was fügen Sie hinzu?

<details>
<summary>Antworten</summary>

1. Vier beliebige aus: Unterelemente, übergeordnetes Element, Teilbaum, Geschwister, neueste, zufällige, manuelle,
   Relationsliste, umgekehrte Relationsliste, Tags, die Exponential-Inhaltssuche, Themen
   ([Exponential Layouts](../../../../../eng-us/bc/6.0/LAYOUTS.md#query-types-and-their-parameters)).
2. Die Abfrage verwendet die angezeigte Seite als Ort statt eines festen Ortes.
3. **B.**
4. Sie ist später leichter zu lesen und zu verstehen.
5. Einen Inhaltstyp-Filter (zum Beispiel *Article*).

</details>

## Weiterführende Informationen

- [Exponential Layouts: Abfragetypen und ihre Parameter](../../../../../eng-us/bc/6.0/LAYOUTS.md#query-types-and-their-parameters)
- [Exponential Layouts: Durchgang B](../../../../../eng-us/bc/6.0/LAYOUTS.md#walkthrough-b-building-a-new-layout-from-scratch)

## Verwandte Seiten

- [Modul 4: Collections](README.md)
- [Exponential Layouts: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
