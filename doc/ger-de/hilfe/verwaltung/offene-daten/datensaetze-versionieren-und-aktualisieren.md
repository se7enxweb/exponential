---
title: Datensätze versionieren und aktualisieren
audience: government, editor, admin
category: public-sector
order: 140
summary: Wie Sie Datensätze in Exponential aktualisieren, berichtigen, ersetzen und zurückziehen, ohne Adressen zu brechen oder die Aufzeichnung zu verlieren - Versionen und ihre Grenze, Zeiträume als getrennte Distributionen, Änderungshinweise und Zurückziehen statt Entfernen.
keywords: Versionen, Datensatz aktualisieren, Daten berichtigen, ersetzen, zurückziehen, Versionsgeschichte, DefaultVersionHistoryLimit, Änderungshinweis, Archiv, offene Daten, Rechenschaft, versions, update dataset, correct data, supersede, withdraw, version history, change notice, archive, open data, accountability
type: help
---
# Datensätze versionieren und aktualisieren

Diese Seite richtet sich an Redaktionen und Administratoren, die veröffentlichte Datensätze aktuell halten. Jedes
Speichern eines Inhaltsobjekts in Exponential ist eine Version, und die älteren Versionen bleiben bis zu einer Grenze in
der Geschichte ([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)). Das ist die Grundlage dafür, Daten so zu
aktualisieren, dass Nachnutzer es nachvollziehen können.

## Inhalt

1. [Drei Arten von Änderungen](#drei-arten-von-änderungen)
2. [Was die Versionsgeschichte behält](#was-die-versionsgeschichte-behält)
3. [Einen Datensatz regelmäßig aktualisieren](#einen-datensatz-regelmäßig-aktualisieren)
4. [Einen Fehler berichtigen](#einen-fehler-berichtigen)
5. [Ersetzen und zurückziehen](#ersetzen-und-zurückziehen)
6. [Mehr Versionen aufbewahren](#mehr-versionen-aufbewahren)

## Drei Arten von Änderungen

| Änderung | Beispiel | Empfohlenes Vorgehen |
|---|---|---|
| Regelmäßige Aktualisierung | ein neuer Monat mit Zahlen | die Datei der Distribution „aktuell“ ersetzen oder eine Distribution pro Zeitraum hinzufügen |
| Berichtigung | falsche Werte in der Datei des Vormonats | die Datei ersetzen, die Berichtigung auf der Datensatzseite erklären |
| Einstellung | die Daten werden nicht mehr erhoben | die Seite behalten, als zurückgezogen kennzeichnen, sagen warum und wohin es weitergeht |

## Was die Versionsgeschichte behält

- Jedes Veröffentlichen erzeugt eine neue Version; die vorherige veröffentlichte Version wird **archiviert**.
- Jede Version behält ihre Felder, einschließlich der Datei eines Dateifelds.
- Die Seite **Versionen** eines Objekts (`/content/history/<object ID>`) listet sie auf und kann **Zwei Versionen
  vergleichen**.
- Die Anzahl der aufbewahrten Versionen ist begrenzt: standardmäßig
  `content.ini [VersionManagement] DefaultVersionHistoryLimit=45` und `VersionHistoryClass[]` pro Klasse. Wenn die
  Grenze erreicht ist, wird die älteste archivierte Version entfernt, um Platz zu schaffen.

Die Geschichte ist eine interne Aufzeichnung für Mitarbeitende mit `content/versionread`; sie wird Besuchern nicht
gezeigt. Wenn Nachnutzer ältere Dateien brauchen, veröffentlichen Sie sie als eigene Distributionen.

## Einen Datensatz regelmäßig aktualisieren

Zwei Muster; wählen Sie eines pro Datensatz:

**Eine aktuelle Datei.** Eine Distribution „Aktuelle Daten“, deren Datei Sie bei jeder Aktualisierung ersetzen.
Nachnutzer rufen immer dieselbe Seite ab. Behalten Sie den Dateinamen bei, damit die Download-Adresse gleich bleibt.

**Eine Datei pro Zeitraum.** Eine neue Distribution pro Monat oder Jahr („2025“, „2026“). Alte Dateien bleiben unter
ihren eigenen Adressen herunterladbar; die Datensatzseite listet alle auf. Das passt zu Statistiken, die nach Zeitraum
zitiert werden.

In beiden Fällen aktualisieren Sie das Datum „Zuletzt geändert“ des Datensatzes, und, wenn Ihr Katalog einen hat,
nimmt sein Feed einen Änderungshinweis auf (siehe „Feeds für neue und geänderte Datensätze“).

## Einen Fehler berichtigen

1. Bearbeiten Sie die Distribution, ersetzen Sie die Datei, veröffentlichen Sie.
2. Fügen Sie auf der Datensatzseite unter „Berichtigungen“ eine datierte Zeile hinzu: was falsch war, welcher Zeitraum,
   wann es behoben wurde.
3. War der Fehler groß, veröffentlichen Sie eine Nachricht und verlinken Sie sie.

Die alte Datei bleibt in der Versionsgeschichte, sodass Sie später genau zeigen können, was vor der Berichtigung
veröffentlicht war.

## Ersetzen und zurückziehen

Entfernen Sie keine Datensatzseite: Durch das Entfernen antwortet ihre Adresse mit „nicht gefunden“, und nichts leitet
weiter ([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)).
Stattdessen:

1. Fügen Sie oben in der Beschreibung einen Hinweis hinzu: „Zurückgezogen am <Datum>: <Grund>. Ersetzt durch <Link>.“
2. Ändern Sie die Aktualisierungshäufigkeit auf „wird nicht mehr aktualisiert“.
3. Verschieben Sie den Datensatz bei Bedarf in einen Ordner „Archivierte Datensätze“; die alte Adresse leitet an den
   neuen Ort weiter.

Wenn ein Datensatz wirklich verschwinden muss (zum Beispiel weil versehentlich personenbezogene Daten veröffentlicht
wurden), entfernen Sie die Datei und entscheiden Sie dann mit Ihren Zuständigen für Schriftgut über die Seite. Das
Entfernen einer Datei aus der aktuellen Version entfernt sie nicht aus älteren Versionen: Entfernen Sie auch diese
Versionen auf der Seite Versionen, sonst bleibt die Datei in der Geschichte.

## Mehr Versionen aufbewahren

Für die Klassen Datensatz und Distribution möchten Sie vielleicht eine längere Geschichte als die voreingestellten 45:

```ini
# settings/override/content.ini.append.php
[VersionManagement]
VersionHistoryClass[dataset]=200
VersionHistoryClass[distribution]=200
```

Leeren Sie danach den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`). Eine spätere Senkung der Grenze entfernt
Versionen nicht von selbst; `php bin/php/cleanupversions.php` entfernt die archivierten Versionen jenseits der Grenze.
Mehr Versionen brauchen mehr Platz in Datenbank und Speicher; sprechen Sie mit Ihrer Administration.

## Verwandte Seiten

- [Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)
- [Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)
- [Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
