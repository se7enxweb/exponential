---
title: Ein Inhaltsmodell entwerfen
audience: cms-developer
category: cms-development
order: 12
summary: Für CMS-Entwickler am Anfang eines Projekts eine schrittweise Methode von den Anforderungen zu einem Satz von Klassen, Attributen, einem Inhaltsbaum, Sektionen und Relationen. Sie endet mit einem durchgerechneten Beispiel und der Prüfung, die vor jedem Bau kommt.
keywords: Inhaltsmodell entwerfen, Inhaltsmodellierung, Anforderungen, Inhaltsinventar, Klassen, Attribute, Inhaltsbaum, Sektionen, Relationen, Wiederverwendung, Beispiel, Veranstaltungs-Website, Aufgabe, content model design, content modelling, requirements, content inventory, worked example, event site
type: help
---
# Ein Inhaltsmodell entwerfen

Diese Seite richtet sich an Entwickler, die eine Website beginnen und ihre Klassen festlegen müssen, bevor jemand ein
Template baut. Sie ist eine Aufgabe: acht Schritte, von den vorhandenen Inhalten zu einem Modell, das Sie prüfen
können. Die Bausteine erklärt [Das Inhaltsmodell](das-inhaltsmodell.md); hier geht es darum, sie gut einzusetzen.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritt 1: Inhalte auflisten, nicht Seiten](#schritt-1-inhalte-auflisten-nicht-seiten)
3. [Schritt 2: Elemente zu Klassen gruppieren](#schritt-2-elemente-zu-klassen-gruppieren)
4. [Schritt 3: Felder auflisten und Datentypen wählen](#schritt-3-felder-auflisten-und-datentypen-wählen)
5. [Schritt 4: entscheiden, was eine Relation ist](#schritt-4-entscheiden-was-eine-relation-ist)
6. [Schritt 5: den Baum zeichnen](#schritt-5-den-baum-zeichnen)
7. [Schritt 6: den Zugriff mit Sektionen festlegen](#schritt-6-den-zugriff-mit-sektionen-festlegen)
8. [Schritt 7: Sprachen und Versionen](#schritt-7-sprachen-und-versionen)
9. [Schritt 8: aufschreiben und prüfen](#schritt-8-aufschreiben-und-prüfen)
10. [Ein durchgerechnetes Beispiel: eine Veranstaltungs-Website](#ein-durchgerechnetes-beispiel-eine-veranstaltungs-website)

## Bevor Sie beginnen

Sie brauchen die Anforderungen der Website, eine Auswahl echter Inhalte (kein Lorem ipsum) und eine Installation, an
der Sie Klassen ausprobieren können ([Entwicklungsumgebung](entwicklungsumgebung.md)). Sehen Sie sich an, was das
Seitenpaket, von dem Sie ausgehen, bereits mitliefert: Das Paket `sevenx_multisite` bringt eigene Klassen mit, und
deren Wiederverwendung spart Templates ([Die sevenx-Klassen](../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md)).

## Schritt 1: Inhalte auflisten, nicht Seiten

Schreiben Sie jede Art von Element auf, die die Website enthält oder zeigt: News, Veranstaltungen, Personen, Orte,
Produkte, Downloads, Bilder, FAQ-Einträge. Notieren Sie für jede, wie viele es geben wird, wer sie bearbeitet und wie
oft. Eine "Seite" in einem Wireframe ist meist eine *Ansicht* eines Elements oder eine Liste von Elementen; entwerfen
Sie die Elemente, die Seiten ergeben sich aus Templates und Layouts.

## Schritt 2: Elemente zu Klassen gruppieren

Eine Klasse je Art von Element mit eigenen Feldern. Zwei Arten, die alle Felder teilen und sich nur darin
unterscheiden, wo sie erscheinen, sind eine Klasse an zwei Stellen des Baums, nicht zwei Klassen. Zwei Arten, die
einige Felder teilen, sich aber unterschiedlich verhalten, sind zwei Klassen mit denselben Attributbezeichnern für die
gemeinsamen Felder.

| Anzeichen | Entscheidung |
|---|---|
| Gleiche Felder, anderer Ort | eine Klasse, getrennte Ordner |
| Gleiche Felder, andere Regeln dafür, wer bearbeitet | eine Klasse, getrennte Sektionen |
| Ein Feld "Typ" würde die meisten anderen Felder ändern | getrennte Klassen |
| Ein Element hat eine Liste von Unterelementen mit Feldern | eine Behälterklasse mit einer Unterklasse |

## Schritt 3: Felder auflisten und Datentypen wählen

Schreiben Sie für jede Klasse die Felder mit Bezeichner, Datentyp, erforderlich oder nicht, übersetzbar oder nicht,
durchsuchbar oder nicht auf. Verwenden Sie [Datentypen wählen](datentypen-waehlen.md). Verwenden Sie Bezeichner über
Klassen hinweg wieder (`title`, `intro`, `image`). Markieren Sie, welches Feld den Objektnamen liefert und, falls sie
abweichen soll, welches die URL.

## Schritt 4: entscheiden, was eine Relation ist

Alles, was für sich besteht und an mehr als einer Stelle verwendet wird, wird ein eigenes Objekt, auf das über ein
`ezobjectrelationlist`-Attribut verwiesen wird: ein Referent mehrerer Veranstaltungen, ein Veranstaltungsort, ein
Autor, ein gemeinsam genutztes Bild. Alles, was nur zu einem Element gehört, bleibt ein Feld. Relationen erhalten eine
einzige Quelle der Wahrheit; Kopien laufen auseinander ([Relationen und Einbettungen](relationen-und-einbettungen.md)).

## Schritt 5: den Baum zeichnen

Skizzieren Sie den Inhaltsbaum unter dem Wurzelknoten (Knoten 2): die Ordner, welche Klassen wohin gehören und die
Sortierung jedes Behälters. Der Baum ist zugleich Navigation, URL-Struktur und Geltungsbereich der Berechtigungen:

- ein Ordner oberster Ebene je Hauptmenüpunkt lässt URLs und Menüs dem Baum der Redakteure folgen;
- Elemente, die an zwei Stellen erscheinen, erhalten eine zweite Platzierung oder erscheinen in einer Liste, die von
  anderswo abgerufen wird;
- große Sammlungen (Tausende von News) werden besser nach Datum sortiert und geblättert als von Hand auf Ordner
  verteilt.

## Schritt 6: den Zugriff mit Sektionen festlegen

Listen Sie auf, wer was lesen und wer was bearbeiten darf. Jede Regel der Art "nur Mitglieder sehen X" oder "nur das
Veranstaltungsteam bearbeitet Y" wird zu einer Sektion auf dem Teilbaum plus einer Rolle mit Richtlinien, die auf diese
Sektion eingeschränkt sind ([Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md)). Stufen wie *in
Prüfung* werden zu Objektzuständen.

## Schritt 7: Sprachen und Versionen

Legen Sie die Sprachen fest, welche Klassen übersetzt werden, welche Felder nicht, und ob nicht übersetzte Elemente in
der Hauptsprache angezeigt werden (*immer verfügbar*). Entscheiden Sie, ob das Veröffentlichen einen
Freigabe-Workflow braucht ([Workflows und Auslöser](workflows-und-ausloeser.md)).

## Schritt 8: aufschreiben und prüfen

Schreiben Sie das Modell als Tabelle je Klasse und als Skizze des Baums in das Repository Ihres Projekts. Prüfen Sie es
vor dem Bau mit einem Redakteur und einem Designer:
[Checkliste zur Prüfung des Inhaltsmodells](checkliste-zur-pruefung-des-inhaltsmodells.md). Legen Sie dann die Klassen an
([Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md)) und bewahren Sie sie in Dateien auf
([Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md#klassen-unter-versionsverwaltung)).

## Ein durchgerechnetes Beispiel: eine Veranstaltungs-Website

Anforderungen: Veranstaltungen mit Datum, Veranstaltungsort und Referenten; Referentenprofile; Seiten für
Veranstaltungsorte mit Karte; ein News-Bereich; Aufzeichnungen nur für Mitglieder.

| Klasse (Bezeichner) | Felder (Bezeichner: Datentyp) | Hinweise |
|---|---|---|
| Veranstaltung (`exp_event`) | `title`: ezstring, `intro`: eztext, `body`: ezxmltext, `start_date`: ezdatetime, `end_date`: ezdatetime, `venue`: ezobjectrelationlist, `speakers`: ezobjectrelationlist, `image`: ezimage, `tags`: eztags | Behälter: nein; Name `<title>` |
| Referent (`exp_speaker`) | `first_name`, `last_name`: ezstring, `bio`: ezxmltext, `portrait`: ezimage, `website`: ezurl | Name `<first_name> <last_name>` |
| Veranstaltungsort (`exp_venue`) | `title`: ezstring, `address`: eztext, `location`: ezgmaplocation, `body`: ezxmltext | ein Ort, viele Veranstaltungen |
| Aufzeichnung (`exp_recording`) | `title`: ezstring, `event`: ezobjectrelationlist, `file`: ezmedia | in der Sektion *Members* |
| News (vorhandene Klasse `article`) | die Klasse des Seitenpakets | wiederverwendet, nicht neu definiert |

Der Baum:

```text
Home (node 2)
  Events (folder; event lists sort by start_date in the fetch)
  Speakers (folder, sorted by name)
  Venues (folder, sorted by name)
  News (folder, sorted by published, newest first)
  Recordings (folder, section Members)
```

Eine Liste nach einem Attribut wie `start_date` zu sortieren geschieht im Fetch (`sort_by`, `array( 'attribute',
true(), 'exp_event/start_date' )`), nicht in der eigenen Sortierung des Knotens, die nur die Felder kennt, die unter
[Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md#sortierung-der-unterelemente) aufgeführt sind.

Zugriff: Die Rolle Anonymous liest die Sektion Standard; eine Rolle Members liest die Sektion Members; eine Rolle für
Veranstaltungsredakteure darf `exp_event`, `exp_speaker` und `exp_venue` unter den drei Ordnern anlegen.

## Verwandte Seiten

- [Inhalte auf Exponential abbilden](../../installation/migration/inhaltsmodell-zuordnen.md): dieselben Entscheidungen bei einer Migration
- [Inhaltsklassen für Website-Bauer: gute Modelle sind langweilig](../erweiterungsentwicklung/teil-2-builder/inhaltsklassen-fuer-builder.md#gute-modelle-sind-langweilig)
- [Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
