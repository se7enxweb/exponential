---
title: Versionen länger aufbewahren
audience: government, admin
category: public-sector
order: 160
summary: Für Administratoren - wie viele Versionen Exponential aufbewahrt (content.ini VersionManagement), wie Sie die Grenze für aktenrelevante Klassen anheben, was cleanupversions.php tut und wer Versionen entfernen dürfen sollte.
keywords: Versionsgrenze, DefaultVersionHistoryLimit, VersionHistoryClass, cleanupversions.php, Versionen, Aufbewahrung, archivierte Versionen, content.ini, versionremove, Schriftgut, version limit, retention, archived versions, records
type: help
---
# Versionen länger aufbewahren

Diese Seite richtet sich an Administratoren, die entscheiden, wie viel Versionsgeschichte eine Website aufbewahrt. Für
Transparenzinhalte (Beschlüsse, Richtlinien, veröffentlichte Auskünfte) erleichtert eine längere Geschichte den
Nachweis, was wann veröffentlicht war.

## Inhalt

1. [Die ausgelieferten Grenzen](#die-ausgelieferten-grenzen)
2. [Die Grenze für einzelne Klassen anheben](#die-grenze-für-einzelne-klassen-anheben)
3. [Eine Grenze senken](#eine-grenze-senken)
4. [Wer Versionen entfernen darf](#wer-versionen-entfernen-darf)
5. [Entwürfe](#entwürfe)
6. [Kosten](#kosten)

## Die ausgelieferten Grenzen

`settings/content.ini`, Block `[VersionManagement]`:

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `DefaultVersionHistoryLimit` | `45` | die höchste Anzahl aufbewahrter Versionen pro Objekt |
| `VersionHistoryClass[1]` | `75` | Klasse 1 (die Ordnerklasse) behält 75 |
| `VersionHistoryClass[<class identifier>]` | nicht gesetzt | eine Grenze pro Klasse, nach ID oder Bezeichner |
| `DeleteDrafts` | `disabled` | |

Wenn ein Objekt seine Grenze erreicht, wird die älteste **archivierte** Version entfernt, um Platz für eine neue zu
schaffen ([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)).

## Die Grenze für einzelne Klassen anheben

```ini
# settings/override/content.ini.append.php
[VersionManagement]
VersionHistoryClass[decision]=500
VersionHistoryClass[disclosure_entry]=200
VersionHistoryClass[meeting]=200
```

Leeren Sie dann den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und, wenn Velocity die Website ausliefert,
`./console exp:velocity cache clear`. Die neue Grenze gilt ab der nächsten erzeugten Version.

## Eine Grenze senken

Das Senken eines Werts entfernt Versionen nicht sofort. `php bin/php/cleanupversions.php` entfernt die archivierten
Versionen, die die Grenzen überschreiten:

```bash
php bin/php/cleanupversions.php --help
```

Führen Sie es nur aus, nachdem Sie Ihren Aufbewahrungsplan geprüft und die Datenbank gesichert haben: Entfernte
Versionen sind weg.

## Wer Versionen entfernen darf

`content/versionremove` erlaubt einem Benutzer, Entwürfe sowie archivierte und abgelehnte Versionen auf der
Versionsseite zu entfernen; die veröffentlichte Version und Versionen in einem laufenden Workflow werden dort nie
entfernt. Für aktenrelevante Sektionen geben Sie dieses Recht nur Administratoren, und beachten Sie, dass Entfernungen
im Kanal `content` des Prüfprotokolls aufgezeichnet werden ([Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)).

## Entwürfe

Alte Entwürfe sind keine Aufzeichnung dessen, was veröffentlicht war. Redaktionen können ihre eigenen alten Entwürfe
mit **Alte Entwürfe entfernen** entfernen ([Entwürfe und ausstehende Einträge](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md));
`content.ini [VersionManagement] DraftsDuration` und der Cronjob `old_drafts_cleanup` erledigen das automatisch, wenn
sie eingerichtet sind.

## Kosten

Jede Version speichert alle Felder, einschließlich der Dateien. Eine Klasse mit großen Dateien und einer hohen Grenze
lässt die Datenbank und das Speicherverzeichnis wachsen. Prüfen Sie den Speicherverbrauch nach einigen Monaten
([Eine Website betreiben](../../../anleitungen/betrieb-einer-website.md)).

## Verwandte Seiten

- [Versionsgeschichte als Rechenschaft](versionsgeschichte-als-rechenschaft.md)
- [Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
