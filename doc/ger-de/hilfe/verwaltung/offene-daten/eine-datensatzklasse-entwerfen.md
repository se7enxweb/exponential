---
title: Eine Datensatzklasse entwerfen
audience: government, website-creator, admin
category: public-sector
order: 40
summary: Für Websiteersteller in einer öffentlichen Stelle, die Datensatzseiten brauchen - eine Inhaltsklasse „Datensatz“ mit einer untergeordneten Klasse „Distribution“, gebaut unter Setup > Klassen aus den Datentypen, die Exponential mitliefert, mit Bezeichnern, die DCAT-Begriffen entsprechen.
keywords: Datensatzklasse, Inhaltsklasse, Distribution, DCAT, Metadaten, Klasseneditor, Datentypen, ezbinaryfile, ezselection, ezdate, ezurl, offene Daten, dataset class, content class, distribution, metadata, class editor, datatypes, open data
type: help
---
# Eine Datensatzklasse entwerfen

Diese Seite richtet sich an Websiteersteller und Administratoren, die den Ort bauen, an dem eine öffentliche Stelle
ihre Datensätze veröffentlicht. Exponential liefert keine Datensatzklasse mit, also legen Sie eine im Klasseneditor
an. Der Entwurf unten verwendet nur Datentypen, die mit dem Kernel ausgeliefert werden, und benennt seine Felder nach
den [DCAT-3](https://www.w3.org/TR/vocab-dcat-3/)-Begriffen, die Kataloge verwenden, sodass ein späterer DCAT-Export
daraus geschrieben werden kann. Die Schritte im Klasseneditor stammen aus
[Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md).

## Inhalt

1. [Zwei Klassen: Datensatz und Distribution](#zwei-klassen-datensatz-und-distribution)
2. [Die Klasse Datensatz](#die-klasse-datensatz)
3. [Die Klasse Distribution](#die-klasse-distribution)
4. [Die Klassen anlegen](#die-klassen-anlegen)
5. [Entscheidungen, die Sie festhalten sollten](#entscheidungen-die-sie-festhalten-sollten)
6. [Fallstricke](#fallstricke)

## Zwei Klassen: Datensatz und Distribution

DCAT trennt einen **Datensatz** (die Sammlung von Daten mit ihrer Beschreibung) von seinen **Distributionen** (jede
Datei oder jedes Format, das Sie anbieten: eine CSV-Datei, eine JSON-Datei, eine Tabelle). Dieselbe Trennung
funktioniert auch als Inhalt gut:

| Inhalt | Klasse | Container | DCAT-Begriff |
|---|---|---|---|
| Ordner des Datenkatalogs | Ordner (mitgeliefert) | ja | `dcat:Catalog` |
| Eine Datensatzseite | Datensatz (Ihre) | ja | `dcat:Dataset` |
| Eine Datei darunter | Distribution (Ihre) oder die mitgelieferte Klasse Datei | nein | `dcat:Distribution` |

Wenn der Datensatz ein Container ist, bleiben seine Dateien zusammen, sodass die Datensatzseite ihre Unterobjekte
auflisten und ein RSS-Export des Katalogordners neuen Datensätzen folgen kann.

## Die Klasse Datensatz

| Name des Attributs | Bezeichner | Datentyp | Erforderlich | Übersetzbar | DCAT-Begriff |
|---|---|---|---|---|---|
| Titel | `title` | Textzeile (`ezstring`) | ja | ja | `dct:title` |
| Beschreibung | `description` | Formatierter Text (`ezxmltext`) | ja | ja | `dct:description` |
| Herausgeber | `publisher` | Textzeile oder eine Objektbeziehung zu einer Organisationsseite | ja | nein | `dct:publisher` |
| Kontaktstelle | `contact_point` | E-Mail (`ezemail`) eines Teampostfachs, nie einer Person | ja | nein | `dcat:contactPoint` |
| Lizenz | `license` | Auswahl (`ezselection`) | ja | nein | `dct:license` |
| Thema | `theme` | Tags (`eztags`) oder Auswahl | nein | nein | `dcat:theme` |
| Schlüsselwörter | `keywords` | Schlüsselwörter (`ezkeyword`) | nein | ja | `dcat:keyword` |
| Veröffentlicht | `issued` | Datum (`ezdate`) | ja | nein | `dct:issued` |
| Geändert | `modified` | Datum (`ezdate`) | nein | nein | `dct:modified` |
| Aktualisierungshäufigkeit | `accrual_periodicity` | Auswahl | nein | nein | `dct:accrualPeriodicity` |
| Räumliche Abdeckung | `spatial` | Textzeile | nein | ja | `dct:spatial` |
| Zeitliche Abdeckung | `temporal` | Textzeile, etwa „2020-2025“ | nein | ja | `dct:temporal` |
| Konformität oder Schema | `conforms_to` | URL (`ezurl`) | nein | nein | `dct:conformsTo` |

Setzen Sie das Objekt Namensschema auf `<title>`. Markieren Sie Titel, Beschreibung und Schlüsselwörter als
**Durchsuchbar**. Die Kontaktstelle ist ein Funktionspostfach (zum Beispiel `opendata@example.gov`), sodass die Seite
keine Einzelperson nennt.

## Die Klasse Distribution

| Name des Attributs | Bezeichner | Datentyp | DCAT-Begriff |
|---|---|---|---|
| Titel | `title` | Textzeile | `dct:title` |
| Datei | `file` | Datei (`ezbinaryfile`) | `dcat:downloadURL` (die Download-Adresse) |
| Oder: Zugangsadresse | `access_url` | URL, für Daten, die anderswo bereitgestellt werden | `dcat:accessURL` |
| Format | `format` | Auswahl: CSV, JSON, XML, XLSX, GeoJSON ... | `dct:format` |
| Beschreibung | `description` | Textabschnitt (`eztext`) | `dct:description` |

Die mitgelieferte Klasse **Datei** (`file`) funktioniert ebenfalls, wenn Sie kein Formatfeld brauchen: Ihr Name und
ihre Datei genügen, und der MIME-Typ des Uploads wird vom Datentyp Datei angezeigt.

## Die Klassen anlegen

1. Öffnen Sie **Setup > Klassen**, die Gruppe **Content**, dann **Neue Klasse**.
2. Name `Dataset`, Bezeichner `dataset`, Objekt Namensschema `<title>`, kreuzen Sie **Container** an.
3. Für jede Zeile der Tabelle: Wählen Sie den Datentyp unter **Attribut hinzufügen**, klicken Sie auf **Hinzufügen**,
   vergeben Sie Name und Bezeichner und setzen Sie **Erforderlich**, **Durchsuchbar** und **Übersetzbar** wie in der
   Tabelle.
4. Tragen Sie bei der Auswahl Lizenz die Lizenzen ein, die Ihre Stelle genehmigt hat (siehe „Datensätze
   lizenzieren“).
5. Klicken Sie auf **OK**. Wiederholen Sie das für `distribution`.
6. Erlauben Sie die Klassen dort, wo sie hingehören: Geben Sie dem Datenteam `content/create` für die Klassen
   `dataset` und `distribution` unterhalb des Katalogordners
   ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

## Entscheidungen, die Sie festhalten sollten

- Welche Felder erforderlich sind: Wenige Pflichtfelder machen Entwürfe einfach, aber Lizenz, Herausgeber und
  Kontaktstelle sollten bei einem veröffentlichten Datensatz nie leer sein.
- Welche Felder übersetzt werden: Titel und Beschreibungen ja; Daten, Lizenzen und Formate nein.
- Wem die Klasse gehört: Eine spätere Änderung eines Bezeichners bricht jedes Template, das ihn liest.

## Fallstricke

- **Das Entfernen eines Attributs löscht seine Daten aus jedem Objekt.** Fügen Sie neue Felder unbesorgt hinzu;
  entfernen Sie sie nur nach einer Datensicherung
  ([Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md#changing-a-class-on-a-live-site)).
- **Verwenden Sie die Klasse Artikel nicht für Datensätze**: Die Felder unterscheiden sich, und Templates und Feeds
  lassen sich dann schwer auseinanderhalten.
- **Keine Schemaprüfung.** Das Feld Format hält fest, was die Redaktion gewählt hat; Exponential untersucht die Datei
  nicht.

## Verwandte Seiten

- [Datentypen wählen](../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)
- [Ihr Inhaltsmodell entwerfen](../../../../eng-us/help/creators/website-creators/designing-your-content-model.md)
- [Inhaltsmodell und Bearbeitung](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
