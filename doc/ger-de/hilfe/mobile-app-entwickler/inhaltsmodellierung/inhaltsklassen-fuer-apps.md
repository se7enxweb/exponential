---
title: Inhaltsklassen für Apps
audience: mobile-app-developer, app-creator, developer, website-creator
category: mobile-apps
order: 410
summary: Exponential-Inhaltsklassen den Bildschirmen einer App zuordnen - herausfinden, welche Klassen und Felder ein Ordner enthält, Klassendefinitionen mit den expclass-Diensten lesen, classIdentifier als Weiche für Zellen und Ansichten, mit unbekannten Klassen und geänderten Feldern umgehen und das Website-Team um app-freundliche Felder bitten.
keywords: Inhaltsklasse, classIdentifier, Felder, Attribute, expclass, Klassendefinition, Pflichtfelder, unbekannte Klasse, Schemaänderung, Zelle, Ansicht, Modell, Mobile App, content class, fields, attributes, class definition, required fields, unknown class, schema change, cell, view, model, mobile app
type: help
---
# Inhaltsklassen für Apps

Diese Seite richtet sich an App-Entwickler, die Inhaltsklassen in Bildschirme und Datenmodelle übersetzen. Sie
erklärt, wie Sie erfahren, was eine Klasse enthält, wie Sie `classIdentifier` in der App verwenden und wie Sie robust
bleiben, wenn das Website-Team eine Klasse ändert.

## Inhalt

1. [Die Klassen kennenlernen](#die-klassen-kennenlernen)
2. [classIdentifier als Weiche](#classidentifier-als-weiche)
3. [Klassenänderungen überstehen](#klassenänderungen-überstehen)
4. [Felder, die Apps helfen](#felder-die-apps-helfen)

## Die Klassen kennenlernen

Drei Wege, vom einfachsten zum vollständigsten:

| Weg | Wie | Braucht |
|---|---|---|
| Echte Inhalte lesen | `GET /api/ezp/v2/content/node/<id>?ResponseGroups=Fields`: jedes Feld mit `type` und `identifier` | `content/read` |
| Das Website-Team fragen | die Klassendefinitionen unter **Setup > Klassen** in der Administrationsoberfläche | einen Redakteur oder Administrator |
| Die Definition lesen | `expclass::get::<identifier>`, `expclass::attributes::<identifier>`, `expclass::requiredAttributes::<identifier>` | die Richtlinie `class`, also ein Entwicklerkonto verwenden, nicht das der App |

`expclass::identifiers` liefert jedem angemeldeten Benutzer eine schlanke Zuordnung von Klassen-IDs, Bezeichnern und
Namen. Die Klassendienste sind in `extension/expservices/classes/content/expclassservices.php` deklariert.

## classIdentifier als Weiche

Jeder Knoten, jedes Objekt und jeder Listeneintrag trägt `classIdentifier`. Wählen Sie damit die Zelle und den
Detailbildschirm:

```text
when (item.classIdentifier) {
    "article" -> ArticleCell
    "event"   -> EventCell
    "folder"  -> FolderCell (drill down with the list route)
    else      -> GenericCell (objectName only)
}
```

Haben Sie immer den allgemeinen Fall: Redakteure können eine Klasse, die die App nicht kennt, in einen Ordner legen, den
sie zeigt, und die App darf deswegen weder abstürzen noch die ganze Liste ausblenden.

## Klassenänderungen überstehen

Administratoren können Felder hinzufügen, entfernen und umsortieren und Klassen ändern, während die App bei den
Benutzern ist. So bleiben Sie robust:

- Lesen Sie Felder über `identifier`, nie über ihre Position.
- Behandeln Sie jedes Feld als optional, auch wenn die Klasse es als Pflichtfeld markiert; alte Inhalte können älter als
  die Pflicht sein.
- Prüfen Sie `type` vor dem Parsen; überspringen Sie ein Feld, dessen Typ sich geändert hat.
- Vereinbaren Sie mit dem Website-Team, dass das Umbenennen oder Entfernen eines Felds, das die App nutzt, wie eine
  API-Änderung angekündigt wird.
- Ergänzen Sie einen Test, der auf einer Testinstallation ein Element jeder Klasse liest, die die App nutzt, und bei
  unerwarteten Typen fehlschlägt ([Eine lokale Testinstallation](../testen/eine-lokale-testinstallation.md)).

## Felder, die Apps helfen

Wenn das Website-Team Klassen mit Blick auf die App baut oder erweitert, machen einige Felder eine App einfacher:

| Feld | Warum |
|---|---|
| Ein kurzer Teasertext (einfaches `ezstring` oder `eztext`) | Rich Text erreicht REST als reiner Text; ein eigenes Teaserfeld ist sauber für Listenzellen |
| Ein Hauptbildfeld | ein Ort für das Zellenbild ([Bilder in REST-Antworten](../medien-und-bilder/bilder-in-rest-antworten.md)) |
| Eine stabile Kennung für für die App wichtige Ordner, als Remote-ID | Einstiegspunkte überstehen Neuinstallationen ([Knoten-IDs, Remote-IDs und Deep Links](knoten-ids-remote-ids-und-deep-links.md)) |

Wie Klassen entworfen werden, ist Thema des Website-Teams:
[Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md),
[Datentypen auswählen](../../ersteller/website-ersteller/datentypen-waehlen.md).

## Verwandte Seiten

- [Felder und Feldwerte](../rest-api/felder-und-feldwerte.md)
- [Ihr Inhaltsmodell entwerfen](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md)
- [Inhaltsmodellierung für Apps](README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
