# RSS: Feeds für Apple Podcasts, eine seitenweise Feed-Liste und sicherere Exporte

Diese Seite ist für Website-Betreiber, die Feeds oder Podcasts veröffentlichen, und für Administratoren von Websites mit
vielen Feeds. Sie behandelt, was das RSS-Modul in 6.0 im September 2026 hinzubekommen hat: Feeds für Apple Podcasts,
eine seitenweise Feed-Liste, Feed-Adressen auf der öffentlichen Website und eine sicherere Ausgabe. Das Format OPML hat
eine eigene Seite, [OPML-Exporte](../../../eng-us/bc/6.0/opml.md).

## Apple Podcasts als Feed-Format

Ein RSS-Export kann als **Apple Podcasts (RSS 2.0 + iTunes)** geschrieben werden: RSS 2.0
mit dem iTunes-Namensraum, sodass eine hier veröffentlichte Sendung ohne handgeschriebenen Feed
bei Apple eingereicht werden kann.

### Einen anlegen

1. **Setup > RSS > Exporte**, einen Export anlegen oder bearbeiten.
2. Wählen Sie die Version *Apple Podcasts (RSS 2.0 + iTunes)*. Der Abschnitt *Apple Podcasts*
   des Formulars erscheint.
3. Füllen Sie die Felder des Kanals aus (unten).
4. Fügen Sie eine Inhaltsquelle für die Folgen hinzu und ordnen Sie ihre Klassenattribute zu. Die
   Zuordnung *enclosure* muss ein Medien- oder Dateiattribut nennen, das den Ton enthält.
5. Veröffentlichen Sie den Export und öffnen Sie seine Feed-Adresse.

### Felder des Kanals

| Feld | Pflicht | Geschrieben als |
|---|---|---|
| Autor | nein | `itunes:author` |
| Name des Eigentümers, E-Mail des Eigentümers | ja | `itunes:owner` |
| Adresse des Covers | ja | `itunes:image` (`href`) |
| Kategorie, Unterkategorie | Kategorie ja | verschachteltes `itunes:category` |
| Art der Sendung (Standard `episodic`) | nein | `itunes:type` |
| Sprache, Copyright | nein | Elemente des Kanals |
| Untertitel, Zusammenfassung | nein | `itunes:subtitle`, `itunes:summary` |
| Enthält explizite Inhalte (Standard aus) | nein | `itunes:explicit` |
| Die Sendung ist beendet | nein | `itunes:complete` |
| Die Sendung aus dem Verzeichnis heraushalten | nein | `itunes:block` |
| Umgezogen nach | nein | `itunes:new-feed-url` |

Die Felder werden als JSON in der neuen Spalte `ezrss_export.podcast_head` gespeichert.

### Folgen

Jeder Knoten der Quelle wird zu einem Eintrag mit `title`, `itunes:title`, `link`,
`description`, `pubDate`, einem `enclosure` (URL, Länge in Byte und MIME-Typ aus
der Datei gelesen), einer `guid` gleich der Remote-ID des Objekts (sie ändert sich nie, sodass
Reader alte Folgen nicht als neu sehen), `itunes:author` (der Ersteller)
und `itunes:duration`. Die Dauer wird nur bei MPEG-Audio mit konstanter Bitrate aus der Datei
ermittelt, weil die Medien-Datentypen Größe und Typ speichern, nicht die
Länge. Eine Folge ohne abspielbares Enclosure wird ausgelassen, da Apple
sie zurückweist. Verborgene Knoten werden übersprungen, sofern die Website keine unsichtbaren Knoten zeigt.

### Einstellung

`site.ini` `[RSSSettings]` führt die Formate auf; die mitgelieferte Liste enthält jetzt
`AvailableVersionList[]=ITUNES`.

### Aktualisierung

Bestehende Installationen: Wenden Sie die Datenbankaktualisierung von 6.0.0 auf 6.0.15 an
(`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`), die
`ALTER TABLE ezrss_export ADD COLUMN podcast_head longtext;` hinzufügt. Eine saubere Installation
hat sie aus `share/db_schema.dba`.

## Die Feed-Liste zeigt eine Seite auf einmal

`rss/list` holt und zeichnet eine Seite Exporte und eine Seite Importe, sodass eine
Installation mit Tausenden von Feeds die Seite öffnen kann. Überschriften sortieren; das
Blättern behält die Sortierung. Seitengrößen: `content.ini [RSSListSettings]
ItemsPerPageList[]` (25, 50, 250), gewählt mit dem Steuerelement *Einträge pro Seite*. Die sortierenden
Überschriften verwenden dieselben Klassen wie der Reiter Platzierungen, sodass eine sortierte Spalte
auf beiden gleich markiert ist ([Blättern im Reiter Platzierungen](../../../eng-us/bc/6.0/locations-tab-paging-and-sorting.md)).

## Feed-Adressen verwenden die öffentliche Website

Ein Feed meldete sich unter `localhost` oder dem Host der Administrationsoberfläche, weil
die Exportadresse aus dem Siteaccess kam, der gerade lief. Jetzt wird sie aus dem eigenen
Siteaccess des Feeds ermittelt, mit dem Standardzugang als Rückfall.
Outline-Links von OPML werden aus demselben Grund gegen die eigene Website des Dokuments gebaut.
Öffnen Sie den Feed nach der Aktualisierung einmal erneut und prüfen Sie seinen `<link>`.

## Sicherere Ausgabe

Was das Modul schreibt, lässt sich nicht mehr gegen den wenden, der es liest:
Werte werden auf ihrem Weg in die Dokumente maskiert, und die
Konsistenzprüfungen der Administrationsoberfläche melden kein Problem mehr, gegen das niemand etwas tun kann.
Einzelheiten und die Testliste stehen im Abschnitt Security von [OPML-Exporte](../../../eng-us/bc/6.0/opml.md).

## Aufräumen, was ein Import angelegt hat

Ein RSS-Import fügt pro Eintrag ein Objekt hinzu und entfernt nie eines. Siehe
[Aufräumen nach RSS-Importen](../../../eng-us/bc/6.0/cleanuprss.md), ein Skript und ein Cronjob-Teil.

## Einstellungen auf einen Blick

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/site.ini` | `RSSSettings` | `AvailableVersionList[]` | `1.0`, `2.0`, `ATOM`, `OPML`, `ITUNES` | global |
| `settings/site.ini` | `RSSSettings` | `OPMLMaxOutlines` | `5000` (nie über 50000) | global |
| `settings/content.ini` | `RSSListSettings` | `ItemsPerPageList[]` | `25`, `50`, `250` | global |

Prüfen: `grep -n "AvailableVersionList\|OPMLMaxOutlines" settings/site.ini` und `grep -n -A6 RSSListSettings settings/content.ini`.

## Verwandte Seiten

- [OPML-Exporte](../../../eng-us/bc/6.0/opml.md), [Aufräumen nach RSS-Importen](../../../eng-us/bc/6.0/cleanuprss.md), [Spezifikation der Syndikation](../../spezifikationen/6.0/syndikation.md)
- [Cronjobs in der Konsole](cronjobs-in-der-konsole.md) (der Teil zum Aufräumen nach Importen), [Blättern, Sortieren und Seitengrößen](seitenweise-listen-im-admin.md) (die RSS-Liste wird seitenweise angezeigt)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik: September 2026, erste Hälfte, 14. September](../../../eng-us/history/2026/2026-09a.md#14-september-pdf-rss-and-the-rad-tools)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
