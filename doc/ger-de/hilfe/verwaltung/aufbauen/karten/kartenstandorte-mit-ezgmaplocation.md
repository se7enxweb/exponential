---
title: Kartenstandorte mit ezgmaplocation
audience: government, admin, website-creator
category: public-sector
order: 30
summary: Wie Sie die Erweiterung ezgmaplocation einrichten, damit eine Inhaltsklasse (zum Beispiel eine Servicestelle) ein Standortfeld erhält - Aktivierung, die Datenbanktabelle je Engine, die Einstellung für den API-Schlüssel, die Content Security Policy und das Hinzufügen des Feldes zu einer Klasse.
keywords: Standortfeld, Breitengrad, Längengrad, Google-Maps-Schlüssel, Klasseneditor, Servicestelle, ezgmaplocation, GMap Location, location field, latitude, longitude, ApiKey, GMapSettings, Google Maps key, Content Security Policy, class editor, service point
type: help
---
# Kartenstandorte mit ezgmaplocation

Diese Seite richtet sich an Administratoren und Site-Ersteller. `ezgmaplocation` ist ein Datentyp, der einen Breiten-
und Längengrad (Dezimalgrad) und eine Adresse an einem Objekt speichert; Redakteure finden den Punkt, indem sie unter
einer Google-Karte eine Adresse eingeben ([ezgmaplocation](../../../../../eng-us/features/6.0/extensions/ezgmaplocation.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Aktivieren und die Tabelle anlegen](#aktivieren-und-die-tabelle-anlegen)
3. [Der API-Schlüssel](#der-api-schlüssel)
4. [Content Security Policy](#content-security-policy)
5. [Das Feld einer Klasse hinzufügen](#das-feld-einer-klasse-hinzufügen)
6. [Upgrade von 6.0.7 oder älter](#upgrade-von-607-oder-älter)

## Bevor Sie beginnen

Lesen Sie „Karten und Datenschutz“: Mit einem Schlüssel laden das Bearbeitungsformular und die Seite das Skript von
Google, einem Drittanbieter.

## Aktivieren und die Tabelle anlegen

1. Aktivieren Sie die Erweiterung (**Setup > Extensions** oder `ActiveExtensions[]`) und erzeugen Sie die Autoloads
   der Erweiterungen neu: `php bin/php/ezpgenerateautoloads.php -e`.
2. Legen Sie die Tabelle `ezgmaplocation` aus `extension/ezgmaplocation/sql/<engine>/` an (`mysql`, `postgresql`,
   `oracle`, `sqlite`); eine `share/db_schema.dba` wird ebenfalls mitgeliefert.

## Der API-Schlüssel

Das README der Erweiterung (die aktuelle Quelle) sagt: Tragen Sie Ihren Schlüssel für die Google Maps JavaScript API in
`ezgmaplocation.ini [GMapSettings] ApiKey` in `settings/override/ezgmaplocation.ini.append.php` oder je Siteaccess
ein. Das alte `site.ini [SiteSettings] GMapsKey` wird nicht mehr gelesen (die Funktionsseite zu 6.0 nennt es noch).
Ohne Schlüssel laden Bearbeitungsformular und Ansicht kein Skript von Google und zeigen die Koordinaten ohne Karte.

## Content Security Policy

Eine Site oder Administration, die eine Content Security Policy sendet (`site.ini [ContentSecurityPolicy]`), muss die
Hosts von Google erlauben; das README listet die Zeilen `AdditionalSources[]` für `script-src`, `img-src`,
`connect-src`, `font-src`, `style-src`, `frame-src` und `worker-src`. Ohne Schlüssel muss nichts erlaubt werden
([Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md)).

## Das Feld einer Klasse hinzufügen

1. **Setup > Klassen**, die Klasse (zum Beispiel „Servicestelle“), **Bearbeiten**.
2. Wählen Sie unter **Attribut hinzufügen** **GMap Location**, drücken Sie **Hinzufügen**, Name „Standort“, Bezeichner
   `location`.
3. **OK**.

Redakteure geben dann eine Adresse ein und drücken **Find address**; die Karte setzt die Markierung dorthin und füllt
die Felder **Latitude** und **Longitude**, die auch ein Klick auf die Karte oder das Ziehen der Markierung aktualisiert.
**Restore** kehrt zu den Werten zurück, mit denen die Seite geöffnet wurde. (Das README und die Funktionsseite zu 6.0
nennen noch eine Schaltfläche „Update Values“; das aktuelle Bearbeitungs-Template,
`design/standard/templates/content/datatype/edit/ezgmaplocation.tpl`, hat keine.) Die Adresse ist durchsuchbar.

## Upgrade von 6.0.7 oder älter

Frühere Versionen speicherten die Adresse mit HTML-Entitäten. Das README verlangt, die gespeicherten Zeilen einmal je
Datenbank zu dekodieren, bevor Redakteure mit der neuen Version arbeiten:
`php extension/ezgmaplocation/bin/php/decodeaddressentities.php -s <siteaccess>` (ein Probelauf, der die Zeilen
auflistet), dann dasselbe mit `--apply`. Führen Sie es nur einmal aus.

## Verwandte Seiten

- [Was es für Karten gibt](was-es-fuer-karten-gibt.md)
- [Den Standort einer Servicestelle hinzufügen](den-standort-einer-servicestelle-hinzufuegen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
