---
title: Erste Schritte mit 7x SQLI Import
audience: admin, integrator, developer
category: integration
order: 10
summary: 7x SQLI Import 2.0.0-alpha.1 installieren - die benötigten Erweiterungen, die Tabellen, die zwei Cron-Teile, der Reiter Importe und seine Falle in menu.ini - und die Installation mit einem ersten Probelauf prüfen.
keywords: sqliimport, Installation, composer, ActiveExtensions, install-schema, expqueue, expui, runcronjobs, Cron-Teil, Reiter Importe, TopAdminMenu, Tabs, menu.ini, Probelauf, install, cron part, Imports tab, dry run
type: help
---
# Erste Schritte mit 7x SQLI Import

Diese Seite installiert 7x SQLI Import 2.0.0-alpha.1 und prüft, ob es funktioniert.

## Inhalt

1. [Voraussetzungen](#voraussetzungen)
2. [Installieren](#installieren)
3. [Der Reiter Importe](#der-reiter-importe)
4. [Ein erster Probelauf](#ein-erster-probelauf)

## Voraussetzungen

- Exponential 6.0.14 oder neuer, PHP 8.0 oder neuer.
- Die Erweiterung **expqueue** (die Job-Warteschlange): Ihre Worker verarbeiten jeden Lauf.
- Die Erweiterung **expui** (die gemeinsamen Komponenten der Administration).

## Installieren

1. Fordern Sie das Paket an:

   ```bash
   composer require se7enxweb/sqliimport:^2.0@alpha
   ```

2. Aktivieren Sie `expqueue`, `expui` und `sqliimport` in `settings/override/site.ini.append.php`
   (`[ExtensionSettings] ActiveExtensions[]`), oder nur für die Administration in `ActiveAccessExtensions[]`.
3. Erzeugen Sie die Autoloads neu und legen Sie die Tabellen an. Beide Befehle `install-schema` sind idempotent;
   der zweite aktualisiert auch die Tabellen von 1.x:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ./console ext:expqueue:install-schema
   ./console ext:sqliimport:install-schema
   ```

4. Lassen Sie beide Cron-Teile jede Minute laufen:

   ```bash
   * * * * * cd /pfad/zu/exponential && php runcronjobs.php -q expqueue
   * * * * * cd /pfad/zu/exponential && php runcronjobs.php -q sqliimport
   ```

5. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini`.

## Der Reiter Importe

Die Erweiterung fügt der Administration den Reiter **Importe** hinzu. Ein Siteaccess, der `[TopAdminMenu] Tabs[]`
in seiner eigenen `menu.ini.append.php` zurücksetzt (eine Zeile `Tabs[]` ohne Wert), muss dort selbst
`Tabs[]=sqliimport` eintragen, sonst erscheint der Reiter in diesem Siteaccess nicht.

Der Reiter braucht die Richtlinie `sqliimport/view`; Läufe zu starten braucht `sqliimport/run`. Für 1.x angelegte
Rollen mit `sqliimport/listimports` oder `sqliimport/manageimports` funktionieren weiter.

## Ein erster Probelauf

Listen Sie die Importe auf. Nach einer frischen Installation ist die Liste leer, außer es sind Handler von 1.x
eingerichtet oder Sie haben von 1.x migriert ([Aktualisierung von 1.x](aktualisierung-von-1x.md)):

```bash
./console ext:sqliimport:list
```

Ein Probelauf veröffentlicht nichts. Er liest die ersten 20 Datensätze und vermerkt, was geschehen würde:

```bash
./console ext:sqliimport:run <bezeichner> --dry-run --wait
./console ext:sqliimport:status <lauf-id>
```

In der Administration: Öffnen Sie **Importe**, wählen Sie den Import und klicken Sie auf **Dry run** (Probelauf);
verfolgen Sie den Lauf dann auf seiner Seite. Die Beschriftungen der Schaltflächen sind in 2.0.0-alpha.1 noch nicht
übersetzt. Warnt die Übersicht, dass die Tabellen oder die Job-Warteschlange fehlen, führen Sie den passenden
Befehl `install-schema` aus.

Weiter: [Importe und Läufe](importe-und-laeufe.md).
