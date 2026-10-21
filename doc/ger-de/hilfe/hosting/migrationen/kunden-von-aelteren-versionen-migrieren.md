---
title: Kunden von älteren Versionen migrieren
audience: hosting
category: hosting
order: 30
summary: Was ein Hoster wissen muss, wenn die Website eines Kunden auf einem älteren Release der Software läuft (3.x, 4.x und 5.x unter dem alten Namen oder ein älteres 6.0.x) - was gleich bleibt, was sich ändert, die Treiber des Aufwands, ein Staging-Plan und die Kapitel des Installationsbuchs für jeden Ausgangspunkt.
keywords: Migration, Upgrade, ältere Versionen, 3.10, 4.x, 5.x, Legacy, 6.0.x, PHP 5, PHP 8, Update-Dateien, Datenbank-Update, Erweiterungen, Portierung, Staging, Generalprobe, dbupdate, migration, upgrade, older versions, update files, database update, extensions, porting, staging, rehearsal
type: help
---
# Kunden von älteren Versionen migrieren

Diese Seite ist für Hosting-Anbieter, deren Kunden ein älteres Release betreiben: eine Website aus der Zeit von 3.x, 4.x
oder 5.x (als das Produkt noch eZ Publish hieß, ein Produkt, das sein Hersteller nach fast 18 Jahren eingestellt hat) oder
ein älteres Exponential 6.0.x. Sie beantwortet: wie groß die Aufgabe ist, was den Aufwand bestimmt und wo die genauen
Schritte stehen.

> **Hinweis**
> Die Schritte selbst stehen im Installationsbuch. Diese Seite sagt einem Hoster, wie er die Arbeit bewertet, anbietet
> und in Staging vorbereitet; sie wiederholt die Runbooks zur Datenbank nicht.

## Inhalt

1. [Ausgangspunkte](#ausgangspunkte)
2. [Was gleich bleibt](#was-gleich-bleibt)
3. [Was sich ändert](#was-sich-ändert)
4. [Was den Aufwand bestimmt](#was-den-aufwand-bestimmt)
5. [Von einem älteren Release 6.0.x](#von-einem-älteren-release-60x)
6. [Ein Staging-Plan](#ein-staging-plan)
7. [Was Sie dem Kunden sagen](#was-sie-dem-kunden-sagen)

## Ausgangspunkte

| Die Website läuft auf | Kapitel |
|---|---|
| 3.10, 4.0 bis 4.7, einem frühen Legacy-Kernel 5.x auf PHP 5 | [Kapitel 14: Migration von der Linie 4.x](../../../installation/14-migration-von-4x.md) |
| 5.0 bis 5.4, den Community-Releases 2013.x und 2014.x, 5.90 (Legacy-Kernel neben einem Symfony-Stack) | [Kapitel 15: Migration vom Legacy-Stack 5.x](../../../installation/15-migration-von-5x-legacy.md) |
| Exponential 6.0.x, älter als das aktuelle Release | [Kapitel 11: Upgrade, 11.6](../../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand) |
| einer dieser Versionen, Referenz zu Tabellen und Skripten | [Kapitel 17: Referenz zur Migration](../../../installation/17-migrationsreferenz.md) |

## Was gleich bleibt

Exponential 6.0 ist derselbe Legacy-Kernel, weitergeführt ([14.1](../../../installation/14-migration-von-4x.md#141-was-sich-ändert-und-was-nicht)):

- **Die Familie des Datenbankschemas.** Dieselben Tabellen; die alten Update-Dateien bringen eine alte Datenbank auf das
  aktuelle Schema. Nichts wird exportiert und wieder importiert.
- **Die Datentypen.** Die Datentypen des Kernels 3.x/4.x sind noch da; Rich Text bleibt `ezxmltext`.
- **Templates und Einstellungen.** Die Template-Sprache und die Namen der INI-Blöcke und -Schlüssel bleiben erhalten.
- **Die Erweiterungs-API.** Module, Datentypen, Workflow-Ereignisse, Fetch-Funktionen und Template-Operatoren werden auf
  dieselbe Weise geschrieben.

## Was sich ändert

Aus demselben Abschnitt von Kapitel 14: PHP 5 zu PHP 8 (Ihr eigener PHP-Code ist das größte Stück Arbeit), eZ Components
ersetzt durch Zeta Components, die Composer installiert, die Cluster-Handler, die Dateien in der Datenbank speicherten, YUI
(entfernt), jQuery (4.0.0), der Online-Editor, mehrere Rewrite-Regeln und eine strengere Sicherheitsgrundlage.

Für eine Website auf 5.x ist der Umzug eher ein Herauslösen als eine Neufassung: Der Legacy-Teil wird aus der
Symfony-Anwendung herausgenommen, seine Symfony-Konfiguration wird in INI-Dateien festgehalten, und die Datenbank wird an Ort
und Stelle aktualisiert ([Kapitel 15](../../../installation/15-migration-von-5x-legacy.md)).

## Was den Aufwand bestimmt

Kapitel 14 sagt klar, dass die Zeit weit mehr von der Größe einiger Tabellen abhängt als von der Version, von der Sie
kommen ([14.15](../../../installation/14-migration-von-4x.md#1415-wie-lange-es-dauert)). Bewerten Sie für ein Angebot:

| Treiber | Wächst mit | Erfragen Sie |
|---|---|---|
| Datenbankschritte | Größe der Datenbank, Zeilen von `ezcontentobject_attribute`, `ezurlalias_ml` | einen Datenbank-Dump für eine Generalprobe |
| Neuindizierung der Suche | Anzahl der Objekte; meist der längste einzelne Schritt | die Anzahl der Objekte |
| Kopieren der Dateien | Anzahl und Größe der Dateien in `var/` | die Größe von `var/storage` |
| Portieren von Erweiterungen | Zeilen von eigenem PHP und eigenen Templates, nicht die Größe der Datenbank | die Liste der eigenen Erweiterungen und ihren Quellcode |
| Designs | eigene Templates und JavaScript, die YUI oder altes jQuery nutzten | die Design-Erweiterungen |

> **Tipp**
> Machen Sie die Generalprobe zum ersten abrechenbaren Schritt. Die gemessene Zeit der Generalprobe ist die einzige
> verlässliche Grundlage für das Produktionsfenster und für den Rest des Angebots.

## Von einem älteren Release 6.0.x

Das ist ein Update, keine Migration. In Umrissen ([11.6](../../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand)):

1. Bringen Sie den neuen Code an seinen Platz (Git-Checkout des Tags oder `composer update se7enxweb/exponential`).
2. Erzeugen Sie die Autoloads neu und leeren Sie die Caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

3. Laden Sie das PHP neu, das die Website ausliefert; unter Velocity führt `php bin/php/console exp:velocity deploy` jeden
   Schritt in der richtigen Reihenfolge aus ([Änderungen ausrollen](../betrieb/aenderungen-ausrollen.md)).
4. Spielen Sie die Datenbank-Update-Datei der Linie einmal ein, vollständig und in Reihenfolge:
   `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, dann das Skript für die Audit-Tabellen
   `php update/common/scripts/6.0/createaudittables.php` (zuerst mit `--dry-run`).
5. Lesen Sie die Checkliste jedes Zeitraums, den Sie durchlaufen, und prüfen Sie die eigenen Erweiterungen des Kunden
   (11.6, Schritte 3 und 4).

Siehe [Kunden-Websites aktualisieren](../betrieb/kunden-websites-aktualisieren.md) für die Routine des Hosters.

## Ein Staging-Plan

1. Kopieren Sie die alte Website (Datenbank und Dateien) auf eine Staging-Maschine auf Ihrer Plattform.
2. Gehen Sie das Kapitel für den Ausgangspunkt auf dieser Kopie durch, und schreiben Sie jeden Schritt und seine Dauer auf.
3. Portieren Sie die eigenen Erweiterungen auf PHP 8; die Checkliste steht in
   [14.9](../../../installation/14-migration-von-4x.md#149-eigene-extensions-von-php-5-auf-php-8-portieren).
4. Lassen Sie den Kunden die Staging-Kopie testen.
5. Wiederholen Sie die Datenbankschritte für den endgültigen Lauf auf einer frischen Kopie, in einem Wartungsfenster.
6. Halten Sie den Rückfallplan des Kapitels bereit ([14.14](../../../installation/14-migration-von-4x.md#1414-rollback-plan)).

## Was Sie dem Kunden sagen

- Seine alte Version erhält keine Sicherheitskorrekturen: Nur die Linie 6.0 wird gepflegt (`SECURITY.md`).
- Seine Inhalte, Benutzer und URLs werden übernommen; die Datenbank wird aktualisiert, nicht neu aufgebaut.
- Sein eigener Code muss vielleicht portiert werden; das ist die größte Unbekannte und wird während der Generalprobe gemessen.
- Die endgültige Umschaltung braucht ein Einfrieren der Inhalte und ein Wartungsfenster.

## Verwandte Seiten

- [Kunden zu Ihrem Hosting migrieren](kunden-zu-ihrem-hosting-migrieren.md)
- [Von eZ Platform und Ibexa](kunden-von-ez-platform-und-ibexa-migrieren.md)
- [Kunden-Websites aktualisieren](../betrieb/kunden-websites-aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
