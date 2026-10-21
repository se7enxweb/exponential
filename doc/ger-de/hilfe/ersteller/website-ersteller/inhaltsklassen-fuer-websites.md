---
title: Inhaltsklassen für Websites
audience: website-creator, admin
category: website-creation
order: 180
summary: Inhaltsklassen im Klasseneditor anlegen und ändern - Gruppen, Attribute, Pflichtfelder und übersetzbare Felder, Namensmuster, Container - und was auf einer Website mit Inhalten gefahrlos geändert werden kann.
keywords: Inhaltsklasse, Klasseneditor, Klasse anlegen, Attribute, Klassengruppe, Muster für Objektnamen, Muster für URL-Aliasnamen, Container, Pflichtfeld, übersetzbar, durchsuchbar, Informationssammler, Website-Ersteller, content class, class editor, create class, attributes, class group, object name pattern, URL alias pattern, container, required, translatable, searchable, information collector, website creator
type: help
---
# Inhaltsklassen für Websites

Diese Seite richtet sich an Website-Ersteller, die die Inhaltsklassen einer Website in der Administration anlegen oder
anpassen. Sie behandelt den Klasseneditor, die Einstellungen einer Klasse und jedes Attributs und welche Änderungen
gefahrlos sind, sobald die Website Inhalte enthält.

![Setup, Klassen: die Klassengruppen mit der Anzahl der Klassen in jeder](../../../../eng-us/help/images/creators/classes-groups.webp)

## Inhalt

1. [Wo Klassen liegen](#wo-klassen-liegen)
2. [Eine Klasse anlegen](#eine-klasse-anlegen)
3. [Klasseneinstellungen](#klasseneinstellungen)
4. [Attributeinstellungen](#attributeinstellungen)
5. [Eine Klasse auf einer Live-Website ändern](#eine-klasse-auf-einer-live-website-ändern)
6. [Klassen zwischen Installationen übertragen](#klassen-zwischen-installationen-übertragen)

## Wo Klassen liegen

**Setup > Klassen** (`/class/grouplist`) listet die Klassengruppen auf (Content, Users, Media, Setup); jede Gruppe
listet ihre Klassen auf ([Klassengruppen](../../../anleitungen/klassengruppen.md)). Die Klassen einer neuen Installation stammen
aus dem Seitenpaket ([Seitenpakete und die mitgelieferten Themes](seitenpakete-und-mitgelieferte-themes.md)).

## Eine Klasse anlegen

Aus [2. Eine Klasse anlegen](../../../anleitungen/inhaltsmodell-und-bearbeitung.md#2-eine-klasse-anlegen):

1. Öffnen Sie **Setup > Klassen**, die Gruppe **Content**, dann **Neue Klasse**.
2. Geben Sie einen Namen (`Event`) und einen Bezeichner (`event`) an.
3. Legen Sie das Muster für Objektnamen fest, zum Beispiel `<title>`.
4. Fügen Sie Attribute hinzu: Wählen Sie unter **Attribut hinzufügen** einen Datentyp, klicken Sie auf
   **Hinzufügen**, geben Sie einen Namen und einen Bezeichner an.
5. Klicken Sie auf **OK**. Die Klasse kann nun überall angelegt werden, wo Richtlinien es erlauben.

## Klasseneinstellungen

| Einstellung | Was sie bewirkt |
|---|---|
| Name, Bezeichner | was die Redaktion liest; was Templates und Overrides verwenden |
| Muster für Objektnamen | bildet den Namen jedes Objekts aus Attributen: `<title>`, `<first_name> <last_name>`, `<short_title\|title>` (das erste, das nicht leer ist) |
| Muster für URL-Aliasnamen | bildet den URL-Alias; leer bedeutet der Objektname |
| Container | die Klasse darf Unterelemente haben |
| Standardsortierung der Unterelemente | die Sortierung, die neue Objekte dieser Klasse ihren Unterelementen geben |
| Standard-Objektverfügbarkeit | ob ein Objekt in Sprachen angezeigt wird, in die es nicht übersetzt wurde („immer verfügbar“) |

## Attributeinstellungen

| Einstellung | Verwendung |
|---|---|
| Pflichtfeld | das Objekt kann nicht veröffentlicht werden, solange es leer ist |
| Durchsuchbar | der Text des Attributs gelangt in den Suchindex |
| Übersetzbar | jede Sprache hat ihren eigenen Wert; schalten Sie es für Preise, Datumsangaben, Codes aus |
| Informationssammler | das Attribut wird von Besuchern ausgefüllt, nicht von der Redaktion ([Formulare mit dem Informationssammler](formulare-mit-dem-informationssammler.md)) |
| Kategorie | gruppiert Attribute in Reiter des Bearbeitungsformulars |
| Datentyp-Optionen | hängen vom Datentyp ab: maximale Länge, Standardwert, erlaubte Klassen für Relationen, Größenlimit für Bilder ... |

Zwei Datentyp-Optionen kamen in Exponential 6 hinzu und sind für Websites wissenswert
([9. Pflicht-Alternativtext ...](../../../anleitungen/inhaltsmodell-und-bearbeitung.md#9-pflicht-alternativtext-gekürzte-textzeilen-und-von-php-erzeugte-namen)):
Bildfelder können einen Alternativtext verlangen, und Textzeilen können den gespeicherten Wert kürzen.

## Eine Klasse auf einer Live-Website ändern

| Änderung | Gefahrlos? | Hinweis |
|---|---|---|
| Ein Attribut hinzufügen | ja | bestehende Objekte erhalten einen leeren Wert; prüfen Sie, ob Templates mit leeren Werten umgehen |
| Den Namen eines Attributs umbenennen | ja | der Bezeichner bleibt |
| Einen Bezeichner ändern | riskant | jedes Template und jeder Fetch, der ihn verwendet, muss gleichzeitig geändert werden |
| Ein Attribut zum Pflichtfeld machen | ja, aber | bestehende Objekte bleiben, wie sie sind, bis sie bearbeitet werden |
| Ein Attribut entfernen | zerstörend | die Daten dieses Attributs werden aus jedem Objekt gelöscht |
| Einen Datentyp ändern | an Ort und Stelle nicht möglich | ein neues Attribut hinzufügen, die Daten migrieren, das alte entfernen |
| Ein Attribut nach oben oder unten verschieben | ja | nur die Reihenfolge im Bearbeitungsformular |

> **Warnung**
> Große Klassen mit vielen Objekten brauchen eine Weile für Änderungen, weil jedes Objekt das neue Attribut erhält.
> Ändern Sie Klassen außerhalb der Hauptzeiten und mit einem Backup
> ([Backups und Wiederherstellungen](backups-und-wiederherstellungen.md)).

## Klassen zwischen Installationen übertragen

Bauen Sie Klassen auf der Produktions-Website nicht von Hand nach. Exportieren Sie sie aus der Entwicklungsinstallation
als Paket und importieren Sie es ([Inhaltspakete (.ezpkg)](../anwendungsentwickler/pakete-und-ezpkg.md),
[Pakete](../../../anleitungen/pakete.md)). Bezeichner und Remote-IDs sorgen dafür, dass Templates und Overrides auf beiden
Seiten funktionieren.

## Verwandte Seiten

- [Ihr Inhaltsmodell entwerfen](ihr-inhaltsmodell-entwerfen.md)
- [Datentypen wählen](datentypen-waehlen.md)
- [Das Inhaltsmodell und die Bearbeitung](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
