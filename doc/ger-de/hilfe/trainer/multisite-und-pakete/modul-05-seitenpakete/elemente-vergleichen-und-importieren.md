---
title: "Lektion 5.5: Ein Paket mit der Website vergleichen und einzelne Elemente importieren"
audience: trainer, admin, developer
category: training
order: 55
summary: Die Teilnehmenden vergleichen ein Inhaltspaket mit der Website, lesen die Status neu, geändert, nur auf der Website, identisch und Klasse fehlt, öffnen ein Element in der Gegenüberstellung und importieren nach der Bestätigung einen einzelnen geänderten Wert. 50 Minuten.
keywords: multisite und seitenpakete, paketvergleich, vergleichen, ausgewählte importieren, remote-id, multisite and site packages, package compare, package/compare, Compare, import selected, Import this item, remote ID, new, changed, only on the site, identical, class missing, eZPackageComparisonImport
type: help
---
# Lektion 5.5: Ein Paket mit der Website vergleichen und einzelne Elemente importieren

Ein Inhaltspaket auf einer Website zu installieren, die bereits Inhalte hat, war früher ein Sprung ins Ungewisse.
**Vergleichen** zeigt Element für Element, wie sich ein Paket von der Website unterscheidet, und lässt Sie nur das
importieren, was Sie wählen, nach einer Bestätigung, die genau auflistet, was sich ändern wird. Diese Lektion ändert in
der Übungsumgebung ein Element, nachdem es exportiert wurde, vergleicht dann und holt einen Wert aus dem Paket zurück.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 5: Seitenpakete](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren |
| Voraussetzungen | [Lektion 5.3](pakete-auf-der-kommandozeile.md): Das Paket `clubb_content` existiert |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Vergleich eines Pakets öffnen und seine fünf Status lesen;
- den Vergleich filtern, sortieren und durchsuchen und ein Element in der Gegenüberstellung öffnen;
- einen Wert eines geänderten Elements importieren und die anderen so belassen, wie die Website sie hat;
- die Regeln des Importers angeben: Nichts, was nur auf der Website existiert, wird entfernt, Orte werden nie
  verdoppelt oder verschoben.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [In ein Paket hineinsehen, es mit Ihrer Website vergleichen, einzelne Elemente importieren](../../../../funktionen/6.0/pakete-vergleichen-und-importieren.md).

1. **Wie Elemente zugeordnet werden** (4 Minuten). Inhaltsklassen über ihren Bezeichner, Inhaltsobjekte über ihre
   Remote-ID. Beide Seiten werden vom selben Code serialisiert und dann pro Datentyp normalisiert (Rich Text als
   kanonisches XML, Beziehungen über Remote-IDs, Bilder und Dateien über Name, Größe und Prüfsumme). Übersetzungen, die
   nur auf einer Seite existieren, und die Platzierung werden ebenfalls verglichen.
2. **Die Status** (3 Minuten). **neu**, **geändert**, **only on the site** (nur auf der Website), **identical**
   (identisch), **class missing** (Klasse fehlt): jeweils ein Chip mit eigenem Symbol und eigener Beschriftung, sodass
   Farbe nie das einzige Signal ist; die Chips dienen auch als Filter.
3. **Ein Element** (3 Minuten). Werte nebeneinander, entfernter Text durchgestrichen, hinzugefügter Text unterstrichen,
   das XML des Rich Text griffbereit. **Erneut vergleichen** baut den zwischengespeicherten Vergleich neu auf.
4. **Importieren** (5 Minuten). Mit `package/install` zusätzlich zu `package/read` haben Zeilen, die nicht identisch
   sind, **Import** und ein Kontrollkästchen, neben **Ausgewählte importieren** und **Import all changes of this
   filter** (alle Änderungen dieses Filters importieren); ein geöffnetes Element hat **Diesen Eintrag importieren** und
   ein Häkchen pro Wert. Jeder Import führt zu einer Bestätigung, die auflistet, was gesetzt und was beibehalten wird.
   Die Regeln: Ein neues Objekt wird dort angelegt, wo das Paket es platziert; ein geändertes Objekt bekommt die Werte
   des Pakets, nicht angehakte Werte behalten die der Website; Orte, die die Website hat, werden nie verdoppelt oder
   verschoben; etwas, das nur auf der Website existiert, wird nie entfernt.

### Häufige Fragen

**Ändert Vergleichen etwas?** Nein. Es liest nur; nichts wird installiert oder gespeichert, nur ein
zwischengespeicherter Vergleich im Cache-Verzeichnis.

**Warum wird ein Element abgelehnt?** Ein Objekt, das nicht veröffentlicht ist, dessen Klasse abweicht oder das keine
Werte hat, wird vor der Bestätigung abgelehnt, mit Angabe des Grundes.

**Wie viele Elemente pro Anfrage?** Höchstens 25; die Ergebnisseite bietet den nächsten Schritt an, wenn ein Filter
mehr enthält.

**Was ist mit Klassen?** Eine geänderte Klasse wird an Ort und Stelle aktualisiert; ein Objekt, dessen Werte zuerst
eine importierte Klasse brauchen, wird zusammen mit dieser Klasse importiert, die in der Bestätigung zuerst aufgeführt
wird.

### Stolperfallen

- **Ein Seitenpaket vergleichen.** Vergleichen ist für Pakete gedacht, die Inhalte installieren; ein Seitenpaket hat
  für sich allein nichts zu installieren.
- **Unveröffentlichte Entwürfe.** Ein Objekt, das nur als Entwurf geändert wurde, wird als die veröffentlichte Version
  verglichen.
- **Alles importieren.** **Import all changes of this filter** ohne Filter importiert jeden Unterschied; filtern Sie
  zuerst.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation und ein Administratorkonto, mit dem Paket `clubb_content` aus Lektion 5.3.

1. Bearbeiten Sie in der Administration den Ordner *News* unterhalb von *Site B*: Ändern Sie seinen Namen in
   `News and events` und veröffentlichen Sie.
2. Öffnen Sie **Setup** > **Pakete**, öffnen Sie `clubb_content` und klicken Sie auf **Vergleichen**.
3. Lesen Sie die Anzahl jedes Status. Welche Elemente sind **identical**, welche **geändert**?
4. Filtern Sie nach **geändert**. Öffnen Sie das Element *News and events*.
5. Lesen Sie die Werte nebeneinander: den alten Namen aus dem Paket und den neuen auf der Website.
6. Klicken Sie auf **Diesen Eintrag importieren**, entfernen Sie das Häkchen bei jedem Wert außer dem Namen und gehen
   Sie weiter zur Bestätigung.
7. Lesen Sie die Bestätigung: was gesetzt, was beibehalten wird.
8. Bestätigen Sie. Lesen Sie das Ergebnis.
9. Öffnen Sie den Ordner in der Administration: Sein Name ist wieder `News`. Öffnen Sie **Versionen verwalten** und
   sehen Sie sich die neue Version an, die der Import angelegt hat.
10. Klicken Sie zurück im Vergleich auf **Erneut vergleichen**: Das Element ist jetzt **identical**.

## Erwartetes Ergebnis

- Schritt 3: Die meisten Elemente sind **identical**, der Ordner ist **geändert**.
- Schritt 7 listet den Namen als gesetzt und die anderen Werte als beibehalten.
- Schritt 9: Der Ordner heißt *News*, in einer neuen Version; sonst hat sich auf der Website nichts geändert.
- Schritt 10: Der Vergleich zeigt das Element als **identical**.
- Der Trainer prüft Schritt 7 am Beamer, bevor die Teilnehmenden bestätigen.

## Quiz

1. Wie werden die Inhaltsobjekte eines Pakets der Website zugeordnet?
2. Welchen Status hat ein Objekt, das auf der Website unterhalb des obersten Knotens des Pakets existiert, aber nicht
   im Paket?
   A. neu B. geändert C. nur auf der Website D. Klasse fehlt
3. Richtig oder falsch: Das Importieren eines Pakets entfernt Objekte, die nur auf der Website existieren.
4. Sie entfernen das Häkchen bei einem Wert in **Diesen Eintrag importieren**. Was geschieht mit ihm?
5. Welche Richtlinien werden zum Importieren gebraucht?

<details>
<summary>Antworten</summary>

1. Über die Remote-ID ([Paketvergleich und Import](../../../../funktionen/6.0/pakete-vergleichen-und-importieren.md#2-ein-paket-mit-der-website-vergleichen)).
2. **C.**
3. **Falsch.** Etwas, das nur auf der Website existiert, wird nie entfernt.
4. Er behält den Wert der Website.
5. `package/install` zusätzlich zu `package/read`.

</details>

## Weiterführende Informationen

- [In ein Paket hineinsehen, es mit Ihrer Website vergleichen, einzelne Elemente importieren](../../../../funktionen/6.0/pakete-vergleichen-und-importieren.md).
- [Pakete, 3. Die Seite eines Pakets](../../../../anleitungen/pakete.md#3-die-seite-eines-pakets).
- [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md).

## Verwandte Seiten

- [Modul 5: Seitenpakete](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
