# ezgmaplocation: ein Datentyp für Kartenstandorte

Diese Seite ist für Website-Entwickler, die an Inhalten Orte speichern, etwa Büros, Veranstaltungen oder Benutzer.
`ezgmaplocation` ist ein Datentyp, der an einem Objekt einen Breiten- und Längengrad (Dezimalgrad) speichert. Der
Redakteur findet den Punkt, indem er unter einer Google-Karte eine Adresse eingibt. Die Adresse wird mit den Koordinaten
gespeichert und ist durchsuchbar.

## Einrichten

1. Besorgen Sie einen API-Schlüssel für die Google Maps JavaScript API und setzen Sie ihn in
   `ezgmaplocation.ini [GMapSettings] ApiKey=<key>` (in `settings/override/` oder pro Siteaccess). Mit einem leeren
   Schlüssel laden das Bearbeitungsformular und die Ansicht kein Google-Skript und zeigen die Koordinaten ohne Karte;
   eine Website, die eine Content Security Policy sendet, muss die Hosts von Google erlauben.
2. Aktivieren Sie die Extension und erzeugen Sie die Extension-Autoloads neu:
   `php bin/php/ezpgenerateautoloads.php -e`.
3. Legen Sie die Tabelle `ezgmaplocation` aus `extension/ezgmaplocation/sql/<engine>/` an (ein SQLite-Schema wird seit
   6.0.3 mitgeliefert).
4. Fügen Sie einer Klasse den Datentyp **Google Maps Location** hinzu.

## Einen Standort bearbeiten

1. Geben Sie unter der Karte eine Adresse ein und drücken Sie **Find address**: Die Karte zentriert sich darauf, und die
   Felder für Breiten- und Längengrad werden ausgefüllt.
2. Verfeinern Sie, indem Sie die Markierung ziehen oder auf die Karte klicken; die Koordinaten folgen, und das
   Adressfeld wird geleert. **Restore** stellt den zuvor gespeicherten Standort wieder her, und **My current location**
   verwendet die Position des Browsers.

## Beispiel: Objekte in der Nähe eines Punktes finden

Der enthaltene Filter holt nach Entfernung, sortiert nach Nähe:

```
{def $users_close_by = fetch( 'content', 'tree', hash(
        'parent_node_id', 12,
        'limit', 3,
        'sort_by', array( 'distance', true() ),
        'class_filter_type', 'include',
        'class_filter_array', array( 'user' ),
        'extended_attribute_filter', hash( 'id', 'ezgmlLocationFilter',
                                           'params', hash( 'latitude', 59.917,
                                                           'longitude', 10.729,
                                                           'distance', 0.5 ) ) ) )}
```

## Grenzen

- Der Entfernungsfilter verwendet für die SQL-Geschwindigkeit ein umschließendes Rechteck.
  `classes/ezgmllocationfilter.php` dokumentiert die Parameter, die echte kreisförmige Genauigkeit („arccosine“) oder
  eine nähere Annäherung daran („pythagorean“) ergeben. Die Sortierung ist genau.
- Um die Entfernung anzuzeigen („2,5 km entfernt“), verwenden Sie den Parameter `arccosine` mit `as_object`,
  `false()`, um einen Wert zu erhalten, aus dem sie sich berechnen lässt.

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Änderung |
|---|---|
| 6.0.2 | Die Normalisierung von Breiten- und Längengrad funktioniert mit der strengeren Behandlung von `implode()` in PHP 8.5. |
| 6.0.3 | SQLite-Schema für die Standorttabelle; `ezinfo.php` gibt Version, Lizenz und Website an. |
| 6.0.4 | Jeder sichtbare Text ist ein Übersetzungstext mit Deutsch; die beiden JavaScript-Hinweise kommen aus übersetzten Meldungen mit Platzhaltern. |
| 6.0.5, 6.0.6 | Die Beschreibung nennt Exponential; die Copyright-Hinweise nennen zuerst 1998 - 2026 7x & Exponential Foundation. |

## Verwandte Seiten

- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/ezgmaplocation.md) und [Versionshinweise](../../../../changelogs/extensions/ezgmaplocation.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezgmaplocation.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-04](../../../geschichte/erweiterungen/monate/2026-04.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
