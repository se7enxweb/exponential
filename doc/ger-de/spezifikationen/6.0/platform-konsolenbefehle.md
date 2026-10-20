# Spezifikation: Namen der Konsolenbefehle von Exponential Platform

Diese Seite führt die Namen der Konsolenbefehle der auf Symfony basierenden Exponential Platform (`bin/console`) auf: die
Hauptnamen mit `exponential:`, die alten Namen, die als Aliase erhalten bleiben, und die Optionen der Befehle, die Sie am
häufigsten verwenden. Lesen Sie sie, wenn Sie Cron-Einträge, Bereitstellungsskripte oder Betriebshandbücher für ein
Plattformprojekt schreiben. Sie behandelt nicht `./console` und die Skripte `bin/php/*` des Legacy-Kernels; diese sind unter
[Konsole](../../../eng-us/bc/6.0/console.md) beschrieben.

Eingeführt am 2026-04-07 (Kernel, Legacy-Bridge) und im Skeleton am 2026-04-10 dokumentiert.

## Die Regel

Der Hauptname jedes migrierten Befehls beginnt mit `exponential:`. Die früheren Namen bleiben als veraltete Aliase
registriert, sodass Skripte, Cron-Einträge und Betriebshandbücher, die sie verwenden, weiter funktionieren. Verwenden Sie in
allem Neuen den Namen mit `exponential:`.

| Präfix | Status |
|---|---|
| `exponential:*` | Hauptname |
| `ibexa:*` | Veralteter Alias, voll funktionsfähig |
| `ezplatform:*` | Veralteter Alias in den Linien 3.x / 4.6; existiert in Platform v5 nicht |
| `ezpublish:*` | Veralteter Alias der Befehle der Legacy-Bridge |

Befehle, die nicht migriert wurden, behalten ihren Upstream-Namen, zum Beispiel `ibexa:cron:run` und
`ibexa:graphql:generate-schema`. So sehen Sie, was Ihr Projekt registriert und welche Optionen ein Befehl akzeptiert:

```bash
php bin/console list
php bin/console help <command>
```

## Befehle des Plattform-Kernels

Commit `4b90bbf4d` (2026-04-07) im Kernel-Fork `ezplatform-kernel` für 3.x / 4.6. Jeder Befehl erhält seinen neuen Namen
mit `setName()` und behält den alten über den Mechanismus für veraltete Aliase.

In Platform v5 kommen dieselben Namen aus dem Kernel-Paket `se7enxweb/exponential-platform-dxp-core`, der Version, die
der Nexus-Starter und das DXP-Skeleton installieren. Dieses Paket fügt außerdem `exponential:check-urls`,
`exponential:content-type-group:set-system` und den Installationstyp `exponential-media` hinzu
([Nexus-Starter](../../../eng-us/features/6.0/platform-nexus-starter.md)). Sein Repository ist nicht Teil des Änderungsverzeichnisses,
daher stammen die beiden mit „v5“ markierten Zeilen aus der Anleitung des Skeletons und dem installierten Paket.

| Hauptname | Alter Name | Zweck |
|---|---|---|
| `exponential:install` | `ibexa:install` | Schema und Startdaten in eine leere Datenbank installieren |
| `exponential:reindex` | `ibexa:reindex` | Den Suchindex neu aufbauen |
| `exponential:check-urls` (v5) | `ibexa:check-urls` | Externe Links in Inhalten prüfen |
| `exponential:content:cleanup-versions` | `ibexa:content:cleanup-versions` | Alte Inhaltsversionen bereinigen |
| `exponential:content:remove-duplicate-fields` | `ibexa:content:remove-duplicate-fields` | Doppelte Inhaltsfelder entfernen |
| `exponential:content-type-group:set-system` (v5) | `ibexa:content-type-group:set-system` | Eine Inhaltstypgruppe als System markieren |
| `exponential:copy-subtree` | `ibexa:copy-subtree` | Einen Teilbaum eines Ortes kopieren |
| `exponential:delete-content-translation` | `ibexa:delete-content-translation` | Eine Übersetzung eines Inhaltselements löschen |
| `exponential:urls:regenerate-aliases` | `ibexa:urls:regenerate-aliases` | URL-Aliase neu aufbauen |
| `exponential:images:resize-original` | `ibexa:images:resize-original` | Gespeicherte Originalbilder stapelweise skalieren |
| `exponential:images:normalize-paths` | `ibexa:images:normalize-paths` | Speicherpfade von Bildfeldern korrigieren |
| `exponential:timestamps:to-utc` | `ibexa:timestamps:to-utc` | Datums- und Datum-Uhrzeit-Werte nach UTC umrechnen |
| `exponential:io:migrate-files` | `ibexa:io:migrate-files` | Binärdateien zwischen IO-Handlern verschieben |
| `exponential:user:validate-password-hashes` | `ibexa:user:validate-password-hashes` | Algorithmen der Passwort-Hashes prüfen |
| `exponential:user:expire-password` | `ibexa:user:expire-password` | Den Ablauf von Passwörtern erzwingen |
| `exponential:debug:config-resolver` | `ibexa:debug:config-resolver` | Konfigurationswerte des Siteaccess anzeigen |
| `exponential:debug:config` | (kurzer Alias des vorigen Befehls) | Wie oben |

### Optionen der am häufigsten verwendeten Befehle

Optionen und Standardwerte, wie in der Anleitung des Skeletons dokumentiert:

| Befehl | Option | Standard |
|---|---|---|
| `exponential:install` | Argument `type` | `exponential-oss` |
| `exponential:install` | `--skip-indexing` | aus |
| `exponential:check-urls` | `-c, --iteration-count` | `50` |
| `exponential:check-urls` | `-u, --user` | `admin` (benötigt `read` und `versionread` für Inhalte) |
| `exponential:images:resize-original` | `-f, --filter` | erforderlich |
| `exponential:images:resize-original` | `-i, --iteration-count` | `25` |
| `exponential:images:resize-original` | `-u, --user` | `admin` |

Beispiele:

```bash
php bin/console exponential:install exponential-oss                      # type: exponential-oss (default) or ibexa-oss
php bin/console exponential:install exponential-oss --skip-indexing      # no automatic reindex
php bin/console exponential:reindex --iteration-count=100                # batch size
php bin/console exponential:reindex --content-ids=2,34,68                # only these content ids
php bin/console exponential:reindex --content-type=article
php bin/console exponential:reindex --subtree=45
php bin/console exponential:check-urls --iteration-count=100 --user=editor
php bin/console exponential:copy-subtree 42 2 --user=admin
php bin/console exponential:images:resize-original image banner --filter=large --iteration-count=10
```

## Befehle der Legacy-Bridge

Commit `8cea9e8` (2026-04-07); außerdem `dc19470` in der Bridge für Platform 4.

| Hauptname | Alter Name | Zweck |
|---|---|---|
| `exponential:legacy:init` | `ezpublish:legacy:init` | Die Plattform für die Legacy-Nutzung vorbereiten |
| `exponential:legacy:configure` | `ezpublish:configure` | Plattformkonfiguration aus einem Legacy-Verzeichnis schreiben |
| `exponential:legacy:install-extensions` | `ezpublish:legacybundles:install_extensions` | Von Bundles definierte Legacy-Extensions installieren |
| `exponential:legacy:symlink` | `ezpublish:legacy:symlink` | Legacy-Einstellungen und -Designs aus `src` installieren |
| `exponential:legacy:assets-install` | `ezpublish:legacy:assets_install` | Legacy-Assets und Wrapper für den Front-Controller installieren |
| `exponential:legacy:script` | `ezpublish:legacy:script` | Ein Legacy-Skript im Bridge-Kernel ausführen |

Zwei Namen haben ihre Schreibweise geändert: aus `install_extensions` wurde `install-extensions` und aus `assets_install`
wurde `assets-install`. Die alten Schreibweisen mit Unterstrich sind die Aliase.

## Ihre Skripte migrieren

1. Suchen Sie alte Namen in Cron-Dateien und Skripten:

   ```bash
   grep -rnE "bin/console +(ibexa|ezplatform|ezpublish):" /etc/cron.d /var/spool/cron ~/bin ./deploy 2>/dev/null
   ```

   Jede Ausgabezeile hat die Form `file:line:text` mit einem alten Namen. Keine Ausgabe bedeutet, dass nichts zu ändern ist.
2. Ersetzen Sie sie durch die Hauptnamen aus den Tabellen oben. Es geht nichts kaputt, wenn Sie das nicht tun; die Aliase bleiben.
3. Wenn ein Werkzeug die Ausgabe von `list` oder von Befehlen auswertet, rechnen Sie mit den neuen Namen in der Auflistung.

## Verwandte Seiten

- Upgrade-Hinweise: [Paket-Forks und umbenannte Befehle](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- Spezifikationen: [Paketübersicht der Plattform](platform-paketzuordnung.md), [SQLite-Installer der Plattform](platform-sqlite-installer.md), [Legacy-Bridge-Bundle](legacy-bridge-bundle.md)
- Funktionen: [Legacy-Bridge](../../../eng-us/features/6.0/legacy-bridge.md), [DXP-Skeleton](../../../eng-us/features/6.0/platform-dxp-skeleton.md), [Nexus-Starter](../../../eng-us/features/6.0/platform-nexus-starter.md), [Administrationsoberfläche der Plattform](../../../eng-us/features/6.0/platform-admin-ui-fork.md), [Layouts auf der Plattform](../../../eng-us/features/6.0/platform-layouts-core-fork.md), [Framework-Forks für PHP 8.5](../../../eng-us/features/6.0/platform-php85-framework-forks.md), [Site-Bundles](../../../eng-us/features/6.0/platform-site-bundles.md), [SQLite für Exponential Platform](../../../eng-us/features/6.0/platform-sqlite-install.md), [Datenbankverwaltung AdminNeo](../../../eng-us/features/6.0/adminneo-database-manager.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Geschichte: [Überblick über das Ökosystem](../../../eng-us/history/ecosystem.md), [Monate des Ökosystems](../../../eng-us/history/ecosystem/months/2026-04.md), [Änderungsverzeichnis](../../../eng-us/history/ledger/README.md); Repositorys [ezplatform-kernel](../../../eng-us/history/ecosystem/ezplatform-kernel.md), [legacyBridge](../../../eng-us/history/ecosystem/legacyBridge.md), [ibexa-legacy-bridge---7x](../../../eng-us/history/ecosystem/ibexa-legacy-bridge---7x.md), [exponential-platform-dxp-skeleton](../../../eng-us/history/ecosystem/exponential-platform-dxp-skeleton.md)
