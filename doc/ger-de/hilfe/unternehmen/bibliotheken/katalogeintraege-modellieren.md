---
title: Katalogeinträge modellieren
audience: businesses, website-creator, editor
category: business-solutions
order: 20
summary: Wie Sie aus einem Eintrag einer Bibliothek oder eines Archivs eine Inhaltsklasse von Exponential machen - eine Klasse pro Art von Eintrag, die Datentypen für Titel, Kennungen, Daten, Urheber, Themen und Dateien, Relationen zwischen Einträgen und Personen, Pflichtfelder und übersetzbare Felder und das sichere spätere Ändern des Modells.
keywords: Katalogeintrag, Inhaltsklasse, Datentypen, ezisbn, ezobjectrelationlist, ezdate, ezselection, ezmatrix, Metadaten, Relationen, Autoren, Art von Eintrag, Klassenentwurf, catalog record, content class, datatypes, metadata, relations, authors, record type, class design
type: help
---
# Katalogeinträge modellieren

Diese Seite ist für die Leitung der Katalogisierung und den Website-Ersteller, die vereinbaren, wie Einträge gespeichert
werden. Sie zeigt, wie ein typischer Eintrag auf eine Inhaltsklasse abgebildet wird, welcher mitgelieferte Datentyp zu
jedem Feld passt und wie Einträge mit Personen und Sammlungen zusammenhängen. Die allgemeinen Regeln stehen unter
[Ihr Inhaltsmodell entwerfen](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md); diese Seite wendet sie
auf Kataloge an.

## Inhalt

1. [Klassen, Objekte und Felder](#klassen-objekte-und-felder)
2. [Ein Eintrag Feld für Feld](#ein-eintrag-feld-für-feld)
3. [Personen und Organisationen als eigene Objekte](#personen-und-organisationen-als-eigene-objekte)
4. [Sammlungen und Reihen](#sammlungen-und-reihen)
5. [Pflicht, durchsuchbar und übersetzbar](#pflicht-durchsuchbar-und-übersetzbar)
6. [Namen und Adressen von Einträgen](#namen-und-adressen-von-einträgen)
7. [Das Modell später ändern](#das-modell-später-ändern)
8. [Checkliste zur Prüfung](#checkliste-zur-prüfung)

## Klassen, Objekte und Felder

In Exponential ist jede Art von Eintrag eine **Inhaltsklasse** (zum Beispiel „Buch“), jeder Eintrag ein
**Inhaltsobjekt** dieser Klasse und jedes Feld ein **Attribut** mit einem **Datentyp**, der bestimmt, was es speichert
und wie es bearbeitet wird ([Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)).
Das Produkt liefert 35 Datentypen mit ([Datentypen wählen](../../ersteller/website-ersteller/datentypen-waehlen.md));
Erweiterungen fügen weitere hinzu, etwa das Tag-Feld von `eztags`.

## Ein Eintrag Feld für Feld

Eine durchgearbeitete Zuordnung für einen Bucheintrag. Passen Sie die Kennungen an Ihre eigenen Regeln an.

| Feld des Eintrags | Datentyp | Warum |
|---|---|---|
| Titel | `ezstring` (Textzeile) | Kurzer Text, im Objektnamen verwendet |
| Untertitel | `ezstring` | Optional |
| ISBN | `ezisbn` | Ein mitgelieferter Datentyp für ISBNs ([Datentypen wählen](../../ersteller/website-ersteller/datentypen-waehlen.md#die-mitgelieferten-datentypen)) |
| Andere Kennungen (Signatur, Inventarnummer) | `ezstring` oder `ezidentifier` | Nicht übersetzbar |
| Urheber (Autoren, Herausgeber) | `ezobjectrelationlist` | Mehrere verknüpfte Person-Objekte, in Reihenfolge |
| Verlag | `ezobjectrelation` oder `ezstring` | Eine Relation, wenn Sie Verlage beschreiben, sonst eine Textzeile |
| Erscheinungsdatum | `ezdate` | Sortierbar und filterbar; ein Feld nur mit dem Jahr kann ein `ezinteger` sein |
| Sprache des Objekts | `ezselection` | Eine feste Liste, aus der Redakteure wählen |
| Format (Druck, E-Book, Audio) | `ezselection` | Eine feste Liste |
| Themen | Tag-Feld von `eztags` | Kontrollierte Schlagwörter ([Tags und Schlagwörter](tags-und-schlagwoerter.md)) |
| Zusammenfassung oder Abstract | `ezxmltext` (Rich Text) oder `eztext` | Rich Text, wenn Redakteure Absätze und Links brauchen |
| Coverbild | `ezimage` | Mit vorgeschriebenem Alternativtext |
| Digitalisierte Datei | `ezbinaryfile` | Wird über `content/download` heruntergeladen ([Dateien und digitale Sammlungen](dateien-und-digitale-sammlungen.md)) |
| Externer Link (Katalogeintrag im Bibliothekssystem) | `ezurl` | Ein Link auf den führenden Eintrag |
| Inhaltsverzeichnis oder zusätzliche Daten | `ezmatrix` | Eine Wertetabelle, wenn die Werte Daten sind |

Die Tabelle „choosing by need“ der Anleitung für Website-Ersteller nennt dieselben Entscheidungen mit Alternativen, zum
Beispiel „A date to sort or filter by: `ezdate` or `ezdatetime`“ und „Several related items (authors, related
articles): `ezobjectrelationlist`“
([Datentypen wählen](../../ersteller/website-ersteller/datentypen-waehlen.md#nach-bedarf-wählen)).

> **Tipp**
> Fragen Sie bei jedem Feld: „Wird jemand danach sortieren, filtern oder stöbern?“ Wenn ja, geben Sie ihm einen
> Datentyp, der einen Wert speichert (ein Datum, eine Auswahl, eine Relation, ein Tag), keinen freien Text.

## Personen und Organisationen als eigene Objekte

Modellieren Sie Urheber als eigene Klasse (Person, Organization) und verknüpfen Sie Einträge mit ihnen. Dann:

- listet eine Personenseite über umgekehrte Relationen alles auf, was die Person geschrieben hat („everything that
  points to this object“, [Relationen und Wiederverwendung](../../ersteller/website-ersteller/relationen-und-wiederverwendung.md#rückwärtsrelationen));
- wird eine Korrektur an einem Namen einmal vorgenommen;
- kann die Person eigene Felder tragen (Daten, Biografie, Porträt).

Relationsfelder können einschränken, welche Klassen gewählt werden dürfen, sodass Redakteure im Feld Urheber nur eine
Person wählen können (Optionen des Datentyps,
[Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md#attributeinstellungen)).

## Sammlungen und Reihen

Eine Sammlung, eine Reihe oder ein Regal ist ein **Container**: eine Klasse, deren Objekte Unterelemente haben. Einträge
liegen dann unterhalb ihrer Sammlung im Inhaltsbaum, was das Menü und die URLs ergibt
([Ihr Inhaltsmodell entwerfen, Containers and children](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md#container-und-unterelemente)).
Ein Eintrag, der zu mehreren Sammlungen gehört, kann zusätzliche Orte haben oder stattdessen von jeder Sammlung aus
verknüpft werden. Themen, die quer durch Sammlungen gehen, gehören in Tags, nicht in den Baum
([Verschlagworten mit eztags](../../ersteller/website-ersteller/verschlagworten-mit-eztags.md#wann-sie-tags-verwenden)).

## Pflicht, durchsuchbar und übersetzbar

| Einstellung | Verwendung in einem Katalog |
|---|---|
| Pflicht | Titel, mindestens ein Urheber oder eine Regel „Urheber unbekannt“, Datum; der Eintrag kann nicht veröffentlicht werden, solange ein Pflichtfeld leer ist |
| Durchsuchbar | Titel, Untertitel, Urheber, Zusammenfassung, Kennungen, die Leser eintippen; keine internen Notizen |
| Übersetzbar | Titel und Zusammenfassungen, wenn Sie in mehreren Sprachen beschreiben; nicht ISBN, Daten oder Signaturen |
| Kategorie | Felder in Tabs gruppieren: Beschreibung, Kennungen, Dateien, Intern |

Quelle: [Inhaltsklassen für Websites, Attribute settings](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md#attributeinstellungen).
Bildfelder können einen Alternativtext verlangen, was die Barrierefreiheit von Covern und Scans unterstützt.

## Namen und Adressen von Einträgen

Das **Muster für den Objektnamen** bildet den Namen jedes Eintrags aus seinen Feldern, zum Beispiel `<title>` oder
`<short_title|title>` (das erste, das nicht leer ist). Das **Muster für den URL-Alias** bildet die Adresse; leer
bedeutet den Objektnamen
([Inhaltsklassen für Websites, Class settings](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md#klasseneinstellungen)).
Lesbare Adressen helfen Lesern und Suchmaschinen; siehe
[URL-Aliase und sprechende URLs](../../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md).

## Das Modell später ändern

Manche Änderungen sind bei einem laufenden Katalog sicher, andere nicht
([Eine Klasse auf einer laufenden Website ändern](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md#eine-klasse-auf-einer-live-website-ändern)):

| Änderung | Sicher? |
|---|---|
| Ein Feld hinzufügen | Ja; vorhandene Einträge bekommen einen leeren Wert |
| Die Beschriftung eines Felds umbenennen | Ja |
| Die Kennung eines Felds ändern | Riskant; jedes Template, das es verwendet, muss gleichzeitig geändert werden |
| Ein Feld entfernen | Zerstörend; die Daten werden aus jedem Eintrag gelöscht |
| Einen Datentyp ändern | Nicht an Ort und Stelle möglich: ein neues Feld hinzufügen, migrieren, das alte entfernen |

Große Klassen brauchen für eine Änderung eine Weile, weil jedes Objekt das neue Attribut bekommt; ändern Sie sie
außerhalb der Hauptzeiten und mit einem Backup (dieselbe Seite). Bauen und testen Sie Klassenänderungen auf einer
Entwicklungskopie und übertragen Sie sie als Paket.

## Checkliste zur Prüfung

- [ ] Eine Klasse pro Art von Eintrag, nicht pro Thema.
- [ ] Jedes Feld, nach dem sortiert, gefiltert oder gestöbert wird, hat einen Wert-Datentyp.
- [ ] Urheber und Verlage sind Objekte, wenn Sie sie beschreiben; Relationsfelder auf ihre Klassen beschränkt.
- [ ] Themen sind ein Tag-Feld mit einem kontrollierten Vokabular.
- [ ] Pflicht, durchsuchbar und übersetzbar für jedes Feld bewusst gesetzt.
- [ ] Muster für Objektname und URL-Alias ergeben lesbare Namen.
- [ ] Eine Stichprobe echter Einträge vor dem Import von Katalogisierenden eingegeben und geprüft.

## Verwandte Seiten

- [Bibliotheken und Kataloge](README.md)
- [Katalog planen](katalog-planen.md)
- [Ihr Inhaltsmodell entwerfen](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md)
- [Inhaltsmodell und Bearbeitung (Anleitung)](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
