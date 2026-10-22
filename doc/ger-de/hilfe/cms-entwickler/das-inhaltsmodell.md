---
title: Das Inhaltsmodell
audience: cms-developer
category: cms-development
order: 8
summary: Für CMS-Entwickler, die neu bei Exponential sind, das ganze Inhaltsmodell auf einer Seite. Die Seite behandelt Klassen, Attribute und Datentypen, Objekte, Versionen und Übersetzungen, Knoten im Inhaltsbaum, Sektionen, Objektzustände und wie sie zusammenwirken.
keywords: Inhaltsmodell, Inhaltsklasse, Attribut, Datentyp, Inhaltsobjekt, Version, Übersetzung, Knoten, Platzierung, Inhaltsbaum, Sektion, Objektzustand, Remote-ID, Klassenbezeichner, content model, content class, datatype, content object, node, location, content tree, object state, remote id, class identifier
type: help
---
# Das Inhaltsmodell

Diese Seite richtet sich an Entwickler, die Websites mit Exponential bauen und das Inhaltsmodell im Kopf haben müssen,
bevor sie irgendetwas entwerfen. Sie nennt jeden Baustein, zeigt, wie sie zusammenhängen, und verweist auf die Seite,
die jeden im Detail behandelt.

## Inhalt

1. [Das Modell in einem Bild](#das-modell-in-einem-bild)
2. [Klassen, Attribute und Datentypen](#klassen-attribute-und-datentypen)
3. [Objekte, Versionen und Übersetzungen](#objekte-versionen-und-übersetzungen)
4. [Knoten und der Inhaltsbaum](#knoten-und-der-inhaltsbaum)
5. [Sektionen und Objektzustände](#sektionen-und-objektzustände)
6. [Auch Benutzer sind Inhalte](#auch-benutzer-sind-inhalte)
7. [Bezeichner, die Sie täglich verwenden](#bezeichner-die-sie-täglich-verwenden)
8. [Was außerhalb des Inhaltsmodells liegt](#was-außerhalb-des-inhaltsmodells-liegt)
9. [Wie es weitergeht](#wie-es-weitergeht)

## Das Modell in einem Bild

```text
Content class (article)            the type: a name, an identifier and a list of attributes
  Class attribute (title, body)    each one of a datatype: ezstring, ezxmltext, ezimage ...
Content object (one article)       an instance of a class
  Version 1, 2, 3 ...              draft, published, archived; each version holds every translation
    Object attribute               the value of one class attribute in one version and language
  Node (location) 61, 74           the object's places in the content tree; one is the main node
Section                            a label on the object for access rules and designs
Object state                       labels of your own (in review, approved ...), grouped
```

Alles, was ein Redakteur anlegt, von einer Seite oder einem News-Artikel bis zu einem Bild, einer Datei, einem
Benutzerkonto oder einer Benutzergruppe, ist ein **Inhaltsobjekt** einer **Inhaltsklasse**. Es gibt keine eigene
Tabelle für "Seiten", "Medien" oder "Benutzerprofile", um die herum Sie entwerfen: Sie entwerfen Klassen.

## Klassen, Attribute und Datentypen

Eine **Inhaltsklasse** ist die Typdefinition. Sie hat einen (übersetzbaren) Namen, einen Bezeichner wie `article`,
eine Liste von Attributen, ein Muster für den Objektnamen (zum Beispiel `<title>`), ein optionales Muster für den
URL-Alias, eine Angabe, ob Objekte der Klasse Unterelemente haben dürfen (`is_container`), die Standardsortierung
dieser Unterelemente und eine Angabe *immer verfügbar*. Das sind die Spalten von `ezcontentclass`
(`kernel/classes/ezcontentclass.php`).

Jedes **Klassenattribut** hat einen Bezeichner, einen Datentyp und Kennzeichen: erforderlich, durchsuchbar,
übersetzbar und Informationssammler (`kernel/classes/ezcontentclassattribute.php`). Der **Datentyp** bestimmt, wie der
Wert gespeichert, geprüft, bearbeitet, durchsucht und angezeigt wird. Der Kernel liefert 35 Datentypen in
`settings/content.ini [DataTypeSettings] AvailableDataTypes[]` mit, und Erweiterungen fügen weitere hinzu.

Details: [Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md) und
[Datentypen wählen](datentypen-waehlen.md).

## Objekte, Versionen und Übersetzungen

Ein **Inhaltsobjekt** wird nie an Ort und Stelle geändert. Jede Bearbeitung erzeugt eine neue **Version** (einen
Entwurf); deren Veröffentlichung macht sie zur aktuellen Version und archiviert die vorherige. Eine Version enthält
alle Übersetzungen des Objekts, und jede Übersetzung ist ein Satz von Objektattributen in einer Sprache. Die Zahl der
aufbewahrten archivierten Versionen ist je Klasse begrenzt (`settings/content.ini [VersionManagement]`).

Weil jede Änderung eine Version ist, bekommen Sie Verlauf, Vergleich und Wiederherstellung umsonst, und Sie dürfen nie
direkt in die Inhaltstabellen schreiben: Die Veröffentlichungsoperation des Kernels erzeugt die Version, führt
Workflows aus, aktualisiert den Suchindex und leert Caches ([Inhalte aus PHP](inhalte-aus-php.md)).

Details: [Versionen, Entwürfe und Veröffentlichen](versionen-entwuerfe-und-veroeffentlichen.md) und
[Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md).

## Knoten und der Inhaltsbaum

Ein Objekt erscheint auf der Website über seine **Knoten** (auch **Platzierungen** genannt). Die Knoten bilden einen
Baum. Ein Objekt kann mehrere Knoten haben, sodass derselbe Artikel unter *News* und unter *Sport* erscheinen kann;
einer davon ist der **Hauptknoten**, die anderen verweisen auf dasselbe Objekt. Der Baum bestimmt:

- die Navigation und die URL jeder Seite (der URL-Alias eines Knotens wird aus seinem Pfad gebildet);
- den Geltungsbereich von Berechtigungen (eine Richtlinie kann auf einen Teilbaum eingeschränkt werden);
- welches Template verwendet wird (Overrides können auf einen übergeordneten Knoten oder eine Tiefe passen);
- was ein Fetch zurückgibt (Unterelemente, einen Teilbaum, eine Tiefe).

Der mitgelieferte Baum hat feste Spitzen: Die Inhaltswurzel ist Knoten 2, die Benutzer liegen unter Knoten 5, die
Medienbibliothek unter Knoten 43 (`settings/content.ini [NodeSettings]`). Details:
[Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md).

## Sektionen und Objektzustände

Eine **Sektion** ist eine Kennzeichnung je Objekt, die Rollen (eine Richtlinie kann auf eine Sektion eingeschränkt
werden), Template-Overrides (`Match[section]`) und Navigationsbereiche der Administration verwenden. Eine neue
Installation hat fünf: Standard, Users, Media, Setup und Design ([Sektionen](../../anleitungen/sektionen.md)).

**Objektzustände** sind eigene Kennzeichnungen in Gruppen, zum Beispiel ein Prüfzustand mit *Entwurf*, *in Prüfung*
und *freigegeben*. Rollen können auf Zustände eingeschränkt werden; damit bilden Sie redaktionelle Stufen über Entwurf
und veröffentlicht hinaus ab ([Objektzustände](../../anleitungen/objektzustaende.md)).

Details: [Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md).

## Auch Benutzer sind Inhalte

Ein Benutzerkonto ist ein Objekt der Klasse `user` (Klassen-ID 4, `site.ini [UserSettings] UserClassID`) mit einem
`ezuser`-Attribut, das Anmeldenamen, E-Mail-Adresse und Passwort-Hash enthält. Benutzergruppen sind Objekte der Klasse
`user_group` im Benutzerbaum, und ein Benutzer gehört zu den Gruppen, in denen seine Knoten liegen. Rollen werden
Benutzern oder Gruppen zugewiesen. Sie können der Benutzerklasse also wie jeder anderen Klasse Attribute hinzufügen und
Benutzer wie Inhalte abrufen ([Benutzer und Registrierung](benutzer-und-registrierung.md)).

## Bezeichner, die Sie täglich verwenden

| Bezeichner | Beispiel | Wofür Sie ihn verwenden |
|---|---|---|
| Klassenbezeichner | `article` | Templates, Overrides, Fetch-Filter, Importe; stabil über Installationen hinweg |
| Attributbezeichner | `title`, `body` | `$node.data_map.title` in Templates, `attributes` in PHP |
| Knoten-ID | `2` | Links und Fetches nur in einer Installation |
| Objekt-ID | `57` | Relationen; nur in einer Installation |
| Remote-ID | `myimport-article-42` | eine eigene stabile ID für Objekte, die Sie importieren oder aus Code ansprechen |

Knoten- und Objekt-IDs unterscheiden sich zwischen Installationen. Code, Einstellungen und Pakete, die in jeder
Installation funktionieren müssen, verweisen auf Klassenbezeichner und Remote-IDs, nicht auf Zahlen.

## Was außerhalb des Inhaltsmodells liegt

Manches ist kein Inhaltsobjekt: Einstellungen (INI-Dateien), Templates und Designs, Rollen und Richtlinien,
URL-Platzhalter, Layouts von Exponential Layouts (eigene Tabellen, über Regeln mit Inhalten verbunden), Workflows und
Auslöser, Bestellungen des Shops und Informationen, die über Formulare gesammelt werden. Jedes davon hat in diesem
Bereich eine eigene Seite.

## Wie es weitergeht

1. [Ein Inhaltsmodell entwerfen](ein-inhaltsmodell-entwerfen.md): die Aufgabe, von den Anforderungen bis zu den Klassen.
2. [Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md): die Aufgabe in der Administration.
3. [Inhalte auf Exponential abbilden](../../installation/migration/inhaltsmodell-zuordnen.md): dasselbe Modell aus Sicht
   einer Migration, mit einer Zuordnungstabelle für andere Systeme.

## Verwandte Seiten

- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md): die Anleitung für Administratoren
- [Inhaltsklassen für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/inhaltsklassen-fuer-builder.md): das Kapitel des Buchs für Website-Bauer
- [Knoten, Objekte und Attribute in Templates](../designer/vorlagen/knoten-objekte-und-attribute.md): die Sicht der Designer
- [Glossar für CMS-Entwickler](glossar-fuer-cms-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
