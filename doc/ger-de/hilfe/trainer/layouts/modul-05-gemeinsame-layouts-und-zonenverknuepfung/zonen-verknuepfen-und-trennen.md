---
title: "Lektion 5.2: Zonen verknüpfen und trennen"
audience: trainer, editor, website-creator
category: training
order: 52
summary: Die Teilnehmenden verknüpfen die Kopf- und Fußzeilenzonen ihres Kurs-Layouts mit Link zone with shared layout zone mit einem gemeinsamen Layout, ändern eine Verknüpfung und trennen eine Zone. 45 Minuten.
keywords: exponential layouts, zone verknüpfen, link zone, link zone with shared layout zone, choose a zone you wish to link, update link, choose different zone, unlink, zone trennen, verknüpfte zone, gemeinsame kopfzeile, gemeinsame fußzeile, unlink zone, linked zone, shared header, shared footer
type: help
---
# Lektion 5.2: Zonen verknüpfen und trennen

Das Verknüpfen ist im Editor ein kurzer Ablauf, hat aber zwei Schritte, die Einsteiger verwirren: Zuerst wählen Sie
Ihre eigene Zone, dann das gemeinsame Layout und dessen Zone. Diese Lektion übt an den Kurs-Layouts der
Teilnehmenden, eine Verknüpfung herzustellen, zu ändern und zu entfernen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 5: Gemeinsame Layouts und Zonenverknüpfung](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure und Website-Ersteller |
| Voraussetzungen | [Lektion 5.1](was-gemeinsame-layouts-sind.md); ein veröffentlichtes gemeinsames Kopf-/Fußzeilen-Layout auf der Schulungs-Website |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Verknüpfungsmodus mit **Link zone with shared layout zone** öffnen;
- eine zu verknüpfende Zone wählen (**Link zone**), dann das gemeinsame Layout und seine Zielzone;
- die Verknüpfung einer verknüpften Zone lesen (**Layout:**, **Zone:**) und sie mit **Update link** oder **Choose
  different zone** ändern;
- eine Verknüpfung mit **Unlink** entfernen und das bestätigen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Rundgang durch die Administration: verknüpfte und gemeinsame Zonen](../../../rundgang-durch-die-administration.md#6-verknüpfte-und-gemeinsame-zonen)
und [Gemeinsame Layouts: Bauen](../../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md#aufbauen).

1. **Den Modus öffnen** (2 Minuten). Die Schaltfläche unten links im Editor, **Link zone with shared layout zone**.
   Ein Hinweis erinnert: *Multiple zones in this layout can be linked to zones from different shared layouts.* (Mehrere
   Zonen dieses Layouts können mit Zonen verschiedener gemeinsamer Layouts verknüpft werden.)
2. **Schritt 1** (3 Minuten). *Choose a zone you wish to link*: Jede Zone hat **Link zone**; eine verknüpfte Zone
   zeigt eine rote Verknüpfungsschaltfläche.
3. **Schritt 2** (3 Minuten). *Choose one of the shared layouts and then select a target zone*: Wählen Sie das
   gemeinsame Layout in der Liste, dann die Zone (zum Beispiel seine Zone `header`). Die Zone zeigt nun die gemeinsamen
   Blöcke.
4. **Eine bestehende Verknüpfung** (3 Minuten). Eine verknüpfte Zone meldet *This zone is already linked with* und
   nennt **Layout:** und **Zone:**; **Update link** oder **Choose different zone** ändern sie, **Unlink** entfernt sie
   nach der Frage *Are you sure you want to unlink this zone?*
5. **Zurück und veröffentlichen** (2 Minuten). **Back** führt zum Editor zurück. Die Verknüpfung ist Teil des
   Entwurfs: Sie erscheint auf der Website, nachdem das Layout veröffentlicht wurde (Modul 6).

### Häufige Fragen

**Müssen die Zonennamen übereinstimmen?** Nein, eine Zone kann mit jeder Zone eines gemeinsamen Layouts verknüpft
werden; übereinstimmende Namen (header mit header) halten die Dinge verständlich.

**Warum steht mein gemeinsames Layout nicht in der Liste?** Es ist nicht veröffentlicht oder nicht als gemeinsam
gekennzeichnet.

**Was geschieht mit den Blöcken, die ich in der Zone hatte?** Sie werden ignoriert, solange die Zone verknüpft ist,
und kommen zurück, wenn Sie die Verknüpfung trennen.

### Stolperfallen

- **Schritt 1 überspringen.** Die Teilnehmenden wählen ein gemeinsames Layout, ohne zuerst ihre eigene Zone zu
  wählen.
- **Die falsche Zone verknüpfen.** Lesen Sie den Zonennamen, bevor Sie auf **Link zone** klicken.
- **Erwarten, dass sich die Website ändert.** Nichts ändert sich, bis das Layout veröffentlicht ist.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihr Kurs-Layout. Der Trainer hat das gemeinsame Kopf-/Fußzeilen-Layout
genannt.

1. Öffnen Sie Ihr Kurs-Layout mit **Öffnen** (setzen Sie Ihren Entwurf fort, wenn Sie gefragt werden).
2. Klicken Sie unten links auf **Link zone with shared layout zone**.
3. Lesen Sie die Anweisung. Klicken Sie bei Ihrer Zone *HEADER* auf **Link zone**.
4. Wählen Sie das gemeinsame Kopf-/Fußzeilen-Layout und dann seine Zone `header`.
5. Klicken Sie auf **Back**. Ihre Zone *HEADER* zeigt nun die gemeinsame Kopfzeile, mit rot gestricheltem Rahmen und
   schraffierten Blöcken.
6. Verknüpfen Sie Ihre Zone *FOOTER* auf dieselbe Weise mit der Zone `footer` des gemeinsamen Layouts.
7. Öffnen Sie den Verknüpfungsmodus erneut und sehen Sie sich *HEADER* an: Lesen Sie *This zone is already linked
   with*, **Layout:** und **Zone:**.
8. Verknüpfen Sie *PRE_FOOTER* mit der Zone `pre_footer` des gemeinsamen Layouts (oder mit einem gemeinsamen
   Prefooter-Layout, wenn die Website eines hat).
9. Trennen Sie *PRE_FOOTER* mit **Unlink** und bestätigen Sie *Are you sure you want to unlink this zone?*
10. Klicken Sie auf **Back** und wählen Sie **Entwurf speichern und schließen**.

## Erwartetes Ergebnis

- Im Entwurf sind *HEADER* und *FOOTER* mit dem gemeinsamen Kopf-/Fußzeilen-Layout verknüpft und *PRE_FOOTER* ist
  getrennt.
- Die verknüpften Zonen zeigen die gemeinsamen Blöcke im Aussehen verknüpfter Zonen.
- Der Trainer prüft das, indem er den Entwurf einer Person am Beamer im Verknüpfungsmodus öffnet.

## Quiz

1. Welche Schaltfläche öffnet den Verknüpfungsmodus?
2. Welches sind die beiden Schritte beim Verknüpfen einer Zone?
3. Was bewirkt **Unlink** bei den eigenen Blöcken der Zone?
4. Richtig oder falsch: Eine neue Verknüpfung ist auf der Website sichtbar, sobald sie hergestellt ist.
5. Eine verknüpfte Zone meldet *This zone is already linked with*. Welche zwei Dinge nennt sie?

<details>
<summary>Antworten</summary>

1. **Link zone with shared layout zone** ([Rundgang durch die Administration](../../../rundgang-durch-die-administration.md#6-verknüpfte-und-gemeinsame-zonen)).
2. Die eigene Zone wählen (**Link zone**), dann das gemeinsame Layout und seine Zielzone.
3. Sie werden wieder angezeigt; sie wurden ignoriert, solange die Verknüpfung bestand.
4. **Falsch.** Die Verknüpfung ist Teil des Entwurfs, bis das Layout veröffentlicht wird.
5. **Layout:** und **Zone:**, mit denen sie verknüpft ist.

</details>

## Weiterführende Informationen

- [Rundgang durch die Administration: verknüpfte und gemeinsame Zonen](../../../rundgang-durch-die-administration.md#6-verknüpfte-und-gemeinsame-zonen)
- [Gemeinsame Layouts: Regeln, die Sie kennen sollten](../../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md#regeln-die-sie-kennen-sollten)

## Verwandte Seiten

- [Modul 5: Gemeinsame Layouts und Zonenverknüpfung](README.md)
- [Exponential Layouts: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
