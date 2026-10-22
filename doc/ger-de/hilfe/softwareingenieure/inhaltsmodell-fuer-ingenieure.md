---
title: Das Inhaltsmodell für Ingenieure
audience: software-engineer, developer
category: software-engineering
order: 150
summary: Das Inhaltsmodell von Exponential als Datenmodell - zur Laufzeit festgelegte Klassen und Attribute, Objekte mit Versionen und Übersetzungen, Knoten in einem Baum, Datentypen, denen Speicherung und Prüfung gehören, Sektionen, Objektzustände und Relationen - mit den Kernel-Klassen hinter jedem und was das für Abfragen, Migrationen und APIs bedeutet.
keywords: Inhaltsmodell, Inhaltsklasse, Attribut, Datentyp, Inhaltsobjekt, Version, Übersetzung, Knoten, Platzierung, Baum, Sektion, Objektzustand, Relation, eZContentObject, eZContentObjectTreeNode, eZContentClass, eZDataType, Softwareingenieur, content model, content class, datatype, content object, translation, node, location, tree, object state, relation
type: help
---
# Das Inhaltsmodell für Ingenieure

Diese Seite richtet sich an Ingenieure, die das Inhaltsmodell als Datenmodell verstehen müssen, bevor sie darauf
aufbauen: was die Entitäten sind, welche Kernel-Klasse jede darstellt und was daraus für Abfragen, Migrationen und APIs
folgt. Die Sicht der Redakteure auf dasselbe Modell steht unter
[Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md).

## Inhalt

1. [Die Entitäten](#die-entitäten)
2. [Ein zur Laufzeit festgelegtes Schema](#ein-zur-laufzeit-festgelegtes-schema)
3. [Versionen und Übersetzungen](#versionen-und-übersetzungen)
4. [Der Baum](#der-baum)
5. [Datentypen](#datentypen)
6. [Folgen für Ingenieure](#folgen-für-ingenieure)

## Die Entitäten

| Entität | Bedeutung | Kernel-Klasse |
|---|---|---|
| Inhaltsklasse | ein Inhaltstyp: Name, Bezeichner, Attribute | `eZContentClass` |
| Klassenattribut | ein Feld einer Klasse, mit einem Datentyp und seinen Einstellungen | `eZContentClassAttribute` |
| Inhaltsobjekt | ein Inhalt, eine Instanz einer Klasse | `eZContentObject` |
| Version | ein Zustand eines Objekts: Entwurf, veröffentlicht, archiviert | `eZContentObjectVersion` |
| Objektattribut | der Wert eines Klassenattributs in einer Version und einer Sprache | `eZContentObjectAttribute` |
| Knoten (Platzierung) | ein Platz eines Objekts im Inhaltsbaum; ein Objekt kann mehrere haben | `eZContentObjectTreeNode` |
| Datentyp | Speicherung, Prüfung, Bearbeitung und Indexierung einer Art von Attribut | Unterklassen von `eZDataType` |
| Sektion | eine Aufteilung der Inhalte, die Berechtigungen und Designs verwenden | `eZSection` |
| Objektzustand | ein Wert aus einer Zustandsgruppe, in Berechtigungen verwendbar | `eZContentObjectState` |

## Ein zur Laufzeit festgelegtes Schema

Inhaltsklassen sind Daten, kein Code. Administratoren fügen in der Administration ohne Bereitstellung Klassen und
Attribute hinzu, und die Tabellen ändern sich nicht: Attributwerte liegen in allgemeinen Spalten von
`ezcontentobject_attribute` und werden vom Datentyp interpretiert. Klassen lassen sich auch in Paketen ausliefern
([Pakete](../../anleitungen/pakete.md)).

Diese Flexibilität hat einen Preis: Eine Abfrage nach einem Feldwert ist ein Join über die Attributtabelle, gefiltert
nach Klassenattribut; verwenden Sie die Fetch-Funktionen und ihre Attributfilter statt handgeschriebenem SQL.

## Versionen und Übersetzungen

- Jede Bearbeitung erzeugt eine neue Version; das Veröffentlichen macht sie zur aktuellen Version. Unveröffentlichte
  Bearbeitungen bleiben als Entwürfe.
- Die Zahl der aufbewahrten archivierten Versionen ist je Klasse durch `content.ini [VersionManagement]` begrenzt
  (`DefaultVersionHistoryLimit`, `VersionHistoryClass[]`); `exp:cleanupversions` entfernt alte Versionen (lesen Sie
  zuerst sein `--help`: ohne löscht es).
- Jedes Objektattribut existiert je Sprache. Welche Sprachen ein Siteaccess zeigt und der Rückfall sind Einstellungen
  ([Inhaltssprachen](../../anleitungen/inhaltssprachen.md),
  [Rückfall-Gebietsschema für Inhaltsobjekte](../../bc/6.0/ersatz-locale-fuer-inhaltsobjekte.md)).

## Der Baum

Knoten bilden einen Baum; jeder Knoten hat einen übergeordneten Knoten, einen Pfad und eine Tiefe. Knoten 2 ist in einer
Standardinstallation die Wurzel des Inhaltsbaums. Berechtigungen mit Teilbaum-Einschränkungen, URL-Aliase und die
meisten Listen folgen dem Baum; seine Form ist also Teil Ihres Entwurfs. Das Verschieben oder Verbergen eines Knotens
ändert die URLs und die Sichtbarkeit von allem darunter.

## Datentypen

Ein Datentyp ist der Erweiterungspunkt des Inhaltsmodells: eine PHP-Klasse, die eine Art von Attribut speichert, prüft,
bearbeitet, ausgibt und indexiert. Datentypen registrieren sich in `settings/content.ini [DataTypeSettings]
AvailableDataTypes[]`; eine Erweiterung fügt ihre eigenen über `ExtensionDirectories[]` hinzu. Ein Datentyp muss
Eingaben in `validateObjectAttributeHTTPInput()` prüfen und darf das gespeicherte Format vorhandener Attribute nicht
ohne Aktualisierungsskript ändern ([Eigene Datentypen](../ersteller/anwendungsentwickler/eigene-datentypen.md),
[Lebenszyklus eines Datentyps](../ersteller/anwendungsentwickler/lebenszyklus-eines-datentyps.md)).

## Folgen für Ingenieure

| Thema | Folge |
|---|---|
| Lesen | Fetch-Funktionen (Templates) oder die Inhalts-API (PHP) verwenden; sie wenden Berechtigungen an ([Die Inhalts-API](../ersteller/anwendungsentwickler/die-content-api.md)) |
| Schreiben | über den Kernel anlegen und veröffentlichen, damit Versionen, Caches, Suche und Audit-Trail stimmig bleiben ([Inhalte im Code anlegen](../ersteller/anwendungsentwickler/inhalte-im-code-anlegen.md)) |
| Migrationen der Inhaltsstruktur | Klassenänderungen sind Datenänderungen; planen Sie sie mit Paketen oder Skripten, nicht mit SQL |
| APIs | die REST-API stellt Objekte, Knoten und Attribute mit denselben Berechtigungsprüfungen bereit ([API-Design](api-design-rest-ezjscore-headless.md)) |
| Große Operationen | Entfernen, Kopieren und Verschieben großer Teilbäume laufen als Inhaltsjobs im Hintergrund ([Inhaltsjobs](../../bc/6.0/inhaltsjobs.md)) |
| Caches | ein Veröffentlichen leert die View-Caches der betroffenen Knoten über `eZContentCacheManager` ([Cache-Invalidierung und Leeren](cache-invalidierung-und-leeren.md)) |

## Verwandte Seiten

- [Persistenz und die Datenbankschicht](persistenz-und-die-datenbankschicht.md)
- [Inhaltsfunktionen aus PHP](../erweiterungsentwicklung/teil-8-referenz/inhaltsfunktionen-aus-php.md)
- [Referenz zu eZContentObject](../erweiterungsentwicklung/teil-8-referenz/ezcontentobject.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
