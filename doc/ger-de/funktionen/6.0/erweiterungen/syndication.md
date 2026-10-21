# syndication: Inhalte zwischen Installationen teilen

Diese Seite ist für Administratoren, die mehrere Installationen von Exponential betreiben, die Inhalte teilen.
`syndication` exportiert einen Teil des Inhaltsbaums einer Website **als Feed**, und eine andere Website **importiert**
ihn, im Gleichschritt gehalten von cron. Es unterstützt:

- **Einseitige Syndizierung**: zum Beispiel gemeinsame Inhalte oder Authentifizierungsdaten zwischen getrennten
  Datenbanken und Kunden-Websites.
- **Zweiseitige Syndizierung**: zum Beispiel Foren, Portalnetzwerke, soziale Plattformen und große E-Commerce-Netzwerke.

Die Feeds sind zwischengespeichertes XML plus Binärdateien, eine Datei pro exportiertem Objekt, ausgeliefert über eine
**SOAP**-Schnittstelle (`soap.ini` `EnableSOAP=true`, `SOAPExtensions[]=syndication`). Der Einstieg in der
Administrationsoberfläche ist `/syndication/menu`.

Sie begann als Extension von eZ Systems und wurde im September 2025 (1.1.0) von 7x öffentlich veröffentlicht, auf PHP 8
portiert und dokumentiert.

## Einen Export einrichten (die Hauptwebsite)

1. Installieren Sie die Extension, legen Sie die Tabellen an (`sql/mysql.sql` oder seit 1.3.1 `share/db_schema.dba` für
   jede Datenbank) und aktivieren Sie sie.
2. Öffnen Sie `/syndication/menu`, wählen Sie **Export**, dann **New Feed**, und schließen Sie den Assistenten ab. Eine
   Teilbaum-Quelle schließt die Wurzel des Teilbaums ein.
3. Führen Sie den Cronjob-Teil regelmäßig aus. Alle 3 Minuten wird empfohlen, da er im Betrieb viel Last trägt:

```bash
php runcronjobs.php export_feed
```

## Einen Import einrichten (die zweite Website)

1. Installieren und aktivieren Sie die Extension auf der zweiten Website.
2. Öffnen Sie `/syndication/menu`, wählen Sie **Import** und legen Sie einen Import mit der Serveradresse des Feeds und
   dem Knoten Ihrer Website an, der die Inhalte aufnimmt.
3. Ist die Quelle geschützt, geben Sie Anmeldename und Passwort an (siehe unten).
4. Führen Sie den Cronjob-Teil zuerst von Hand aus und prüfen Sie den ersten Import:

```bash
php runcronjobs.php import_feed
```

5. Führen Sie ihn dann regelmäßig aus cron aus. Nach dem ersten Lauf sind Importe schneller und müssen nicht regelmäßig
   beobachtet werden.

## Aus der Administrationsoberfläche und der Shell bedienen

Öffnen Sie **Syndizierung** in der Administrationsoberfläche. Die Startseite zeigt, was exportiert wird, was importiert
wird, wann jeder Cronjob-Teil zuletzt lief und was Aufmerksamkeit braucht (ein Feed ohne Quelle, ein Import ohne
Platzierung, fehlgeschlagene Elemente, SOAP aus). Fehlen die Tabellen, bietet sie an, sie anzulegen. **Export all active
feeds** und **Fetch all active imports** starten denselben Code wie der Cronjob im Hintergrund und zeigen seinen
Fortschritt auf der Seite; die eigene Seite eines Feeds und eines Imports hat dieselben Schaltflächen für diesen einen
Feed oder Import.

In der Shell erscheinen die Befehle in `./console list ext:syndication`:

```bash
./console ext:syndication:status
./console ext:syndication:export --dry-run
./console ext:syndication:import --import=3 --fetch-only
```

Siehe die [Spezifikation](../../../spezifikationen/6.0/syndikation.md) für jede Option.

## Eine Quelle mit HTTP-Authentifizierung schützen

Seit 1.2.0 (19. Juli 2026) sendet der Import einen **Anmeldenamen und ein Passwort** an die exportierende Website, sodass
sich der SOAP-Endpunkt durch HTTP-Zugriffskontrolle schützen lässt. Geben Sie sie an, entweder:

- in der Serveradresse: `https://<login>:<password>@<host>/`, oder
- in den Feldern **Login** und **Passwort** des ersten Schritts des Imports.

Der Port ist standardmäßig 443 für `https` und sonst 80. Halten Sie die Zugangsdaten des Imports aus der
Versionsverwaltung heraus.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `syndication.ini` | `SyndicationFilters` | `FilterArray[]` | `Section`, `Attribute` | Filter, die für eine Feed-Quelle oder einen Import zur Verfügung stehen |
| `syndication.ini` | `Syndication` | `CacheDir` | `syndication` | Cache-Ordner der Feed-Dateien |
| `syndication.ini` | `Syndication` | `CronUser` | `14` | Benutzer-ID, unter der die Cronjob-Teile laufen |
| `soap.ini` | `GeneralSettings` | `EnableSOAP` | `true` | SOAP ausliefern |
| `cronjob.ini` | `CronjobPart-export_feed`, `CronjobPart-import_feed` | | `syndication_export.php`, `syndication_import.php` | Die beiden Teile |

Ein Kernel-Patch (`kernel_patch/addrelated.diff`) ist optional, wird aber empfohlen. Installieren Sie ihn als
**Kernel-Überschreibungsklasse**, nicht als Bearbeitung des Kernels von Hand.

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.1.0 | 13. September 2025 | Die Extension aus PHP 5 auf PHP 8 portiert; Debug-Anweisungen und ein verirrtes `die()` entfernt. |
| 1.2.0 | 19. Juli 2026 | HTTP-Authentifizierung von Importen und ein linkes Menü (Syndication, Feeds, Imports, Feed Sources) in der Administrationsoberfläche. |
| 1.3.0 | 22. September 2026 | Modulansichten sind auf einem dauerhaften Worker (Velocity) sicher. |
| 1.3.1 | 2. Oktober 2026 | `share/db_schema.dba` beschreibt die elf Tabellen der Syndizierung (Spalten, Standards, Primärschlüssel und die drei Indizes des SOAP-Protokolls) im Engine-neutralen Schemaformat, sodass ein Installer sie auf jeder Datenbank anlegen kann, die Exponential unterstützt, nicht nur auf MySQL. Geprüft durch Laden in eine frische SQLite-Datenbank und durch Erzeugen des Schemas für MySQL und PostgreSQL daraus. `ezinfo.php` und `extension.xml` geben Version, Lizenz und Website an. |
| 1.3.2 | 2. Oktober 2026 | Befehle und Cronjob-Teile führen eine Beschreibung; die Copyright-Hinweise nennen zuerst 1998 - 2026 7x & Exponential Foundation. |
| 1.3.3 | | Ein Dashboard, Listen mit Filter, Sortierung und Blättern, Detailansichten für Feeds und Importe, Entfernen mit Bestätigung, Hinweise auf der Seite, deutsche Texte; die Tabellen lassen sich aus dem Dashboard anlegen; „jetzt ausführen“ im Hintergrund; Konsolenbefehle und ausführbare Cronjob-Teile. Korrekturen: Das Entfernen eines Feeds, einer Quelle, eines Filters oder eines Imports war unter PHP 8 ein fataler Fehler; der Import-Assistent begann bei jedem Klick von vorn; Filtertypen wurden als Code ausgewertet; entfernte Daten wurden mit Objekten deserialisiert; ein erster Abruf scheiterte, weil der Parameter `modified` ausgelassen wurde. |

## Verwandte Seiten

- [Spezifikation](../../../spezifikationen/6.0/syndikation.md)
- [Velocity-Engines](../../../bc/6.0/velocity-engines.md)
- [Abstraktionen für Kommandozeile, Cronjobs und Ansichten](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/syndication.md) und [Versionshinweise](../../../../changelogs/extensions/syndication.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/syndication.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2025-09](../../../geschichte/erweiterungen/monate/2025-09.md), [2026-07](../../../geschichte/erweiterungen/monate/2026-07.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
