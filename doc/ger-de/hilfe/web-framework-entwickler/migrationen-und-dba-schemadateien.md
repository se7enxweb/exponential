---
title: "Migrationen und Startdaten: .dba-Schemadateien und Pakete"
audience: web-framework-developer, developer
category: web-frameworks
order: 160
summary: Was in Exponential Migrationsklassen, Seeder und Fixtures ersetzt - datenbankneutrale .dba-Schemadateien, ezsqldumpschema und ezsqlinsertschema, ein idempotenter Installationsbefehl, Upgrade-Schritte, die Sie mitliefern, Startdatensätze in db_data.dba, Inhaltspakete für Klassen und Inhalte und wie ein Deployment sie anwendet.
keywords: Migrationen, migrations, Schema, schema, Seeder, seeders, Fixtures, fixtures, Startdaten, dba, db_schema.dba, db_data.dba, eZDbSchema, ezsqldumpschema, ezsqlinsertschema, ezsqldiff, ezimportdbafile, Pakete, packages, ezpkg, Upgrade-Skripte, upgrade scripts, Doctrine migrations, Laravel migrations, Django migrations, Web-Framework-Entwickler, web framework developer
type: help
---
# Migrationen und Startdaten: .dba-Schemadateien und Pakete

Diese Seite richtet sich an Entwickler, die `doctrine:migrations:migrate`, `php artisan migrate`, `manage.py migrate`
oder `rails db:migrate` ausführen. Exponential hat **keinen Migrations-Runner und keine Migrationstabelle**. Tabellen
werden in datenbankneutralen `.dba`-Dateien beschrieben, durch Befehle angelegt und durch Upgrade-Schritte geändert,
die Sie selbst schreiben und mitliefern. Inhaltsklassen und Inhalte werden mit **Paketen** übertragen. Sagen Sie das
früh im Projekt; es verändert, wie ein Release geplant wird.

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Das .dba-Format](#das-dba-format)
3. [Ein Schema anlegen und installieren](#ein-schema-anlegen-und-installieren)
4. [Upgrades: Ihre eigenen Migrationsschritte](#upgrades-ihre-eigenen-migrationsschritte)
5. [Startdaten und Inhalte](#startdaten-und-inhalte)
6. [Im Vergleich mit Migrationswerkzeugen](#im-vergleich-mit-migrationswerkzeugen)

## Die Bausteine

| Bedarf | Exponential |
|---|---|
| Tabellen für jede Datenbank beschreiben | `extension/<ext>/share/db_schema.dba` |
| Startdatensätze | `extension/<ext>/share/db_data.dba` |
| Die Tabellen anlegen | `ezsqlinsertschema.php` oder ein Installationsbefehl Ihrer Erweiterung |
| Schemas vergleichen | `ezsqldiff.php` |
| Inhaltsklassen und Inhalte | Pakete (`.ezpkg`), installiert unter **Setup > Pakete** oder mit `ezpm.php` |
| Änderungen nach dem ersten Release | Upgrade-Skripte in Ihrer Erweiterung |

## Das .dba-Format

Eine `.dba`-Datei ist ein PHP-Array von Tabellen, jede mit Feldern (Typ, Länge, Standardwert, not null), Indizes und
dem Primärschlüssel. Das eigene Schema des Kernels ist `share/db_schema.dba`, seine Daten liegen in
`share/db_data.dba`. Die Klasse, die sie liest und schreibt, ist `eZDbSchema` (`lib/ezdbschema/`); sie erzeugt aus
derselben Datei das SQL für SQLite, PostgreSQL, MySQL/MariaDB und Oracle
([Datenbankschema und .dba-Dateien](../ersteller/anwendungsentwickler/schema-und-dba-dateien.md)).

## Ein Schema anlegen und installieren

Legen Sie die Tabelle in einer Entwicklungsdatenbank an und exportieren Sie dann nur Ihre Tabellen:

```bash
php bin/php/ezsqldumpschema.php --type=sqlite --table-include=expdemo_item --output-array \
  var/storage/sqlite3/exponential.db extension/expdemo/share/db_schema.dba --allow-root-user
```

Installieren Sie es in einer Datenbank:

```bash
php bin/php/ezsqlinsertschema.php --type=mysql --user=<user> extension/expdemo/share/db_schema.dba <database>
```

Besser: Geben Sie Ihrer Erweiterung einen idempotenten Befehl, der fehlende Tabellen anlegt, so wie expqueue es mit
`./console ext:expqueue:install-schema` tut. Ein Skript in `extension/expdemo/bin/php/install-schema.php` wird ohne
Registrierung zu `./console ext:expdemo:install-schema` ([Kommandozeile](cli-und-konsolenbefehle.md)).

## Upgrades: Ihre eigenen Migrationsschritte

1. Ändern Sie eine veröffentlichte Tabelle nie direkt ohne einen Upgrade-Schritt.
2. Liefern Sie Upgrade-Schritte als nummerierte Skripte oder SQL-Dateien pro Datenbank-Engine aus (zum Beispiel in
   `extension/<ext>/update/`), und lassen Sie Ihren Installationsbefehl die fehlenden anwenden. Halten Sie in einer
   eigenen kleinen Tabelle oder Einstellung fest, was gelaufen ist; der Kernel führt darüber kein Buch für Sie.
3. Fügen Sie Spalten mit Standardwerten hinzu, damit der alte Code während des Deployments weiter funktioniert.
4. Schreiben Sie Schritte, die nach Möglichkeit nur hinzufügen, damit ein Rollback des Codes auch mit dem neuen Schema
   noch läuft.
5. Prüfen Sie Datenänderungen Zeile für Zeile, nicht anhand eines Diffs: Eine `.dba` ist ein PHP-Array, und das
   Entfernen einer Zeile nummeriert den Rest neu.

## Startdaten und Inhalte

| Startdaten | Verwendung |
|---|---|
| Zeilen Ihrer eigenen Tabellen | `db_data.dba` neben dem Schema |
| Inhaltsklassen, von denen Ihr Code abhängt | ein Klassenpaket, das Ihr Installationsbefehl installiert; halten Sie die Bezeichner stabil ([Inhaltspakete](../ersteller/anwendungsentwickler/pakete-und-ezpkg.md)) |
| Demo- oder Startinhalte | ein Inhaltspaket oder `eZContentFunctions::createAndPublishObject()` mit Remote-IDs, sodass ein zweiter Durchlauf aktualisiert statt zu duplizieren |
| Test-Fixtures | eine Wegwerf-Datenbank für Integrationstests ([Testen](testen.md)) |

```bash
php ezpm.php list --allow-root-user
php ezpm.php import var/tmp/<package>.ezpkg --allow-root-user
```

## Im Vergleich mit Migrationswerkzeugen

| Migrationswerkzeuge bieten | Exponential |
|---|---|
| Aus Modelländerungen erzeugte Migrationen | nein: Schema exportieren und den Upgrade-Schritt schreiben |
| `up()` und `down()` | nur Upgrade-Schritte; ein Rollback ist das Zurückspielen einer Sicherung plus der vorherige Code |
| Eine Tabelle, die festhält, was gelaufen ist | führen Sie selbst, falls Sie eine brauchen |
| Ein Befehl für alles | der Installationsbefehl Ihrer Erweiterung, im Deployment ausgeführt |
| Änderungen am Inhaltsmodell in Migrationen | Pakete und Upgrade-Schritte für Klassen |

Die Deployment-Reihenfolge, die sich daraus ergibt, steht unter [Server und Deployment](server-und-deployment.md) und
[Eine Deployment-Pipeline](../ersteller/anwendungsentwickler/deployment-pipeline.md).

## Verwandte Seiten

- [Datenbankschema und .dba-Dateien](../ersteller/anwendungsentwickler/schema-und-dba-dateien.md)
- [Paket-Assistent](../../funktionen/6.0/paketassistent.md)
- [CLI des Paketmanagers ezpm](../../funktionen/6.0/ezpm-paketmanager-cli.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
