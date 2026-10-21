# Legacy Bridge: Exponential 6 innerhalb der Symfony-Plattform betreiben

Diese Seite richtet sich an Teams, die eine Exponential-6-Site auf die Symfony-Plattform umziehen oder auf der
öffentlichen Site Twig einsetzen möchten, während die Redakteure die Legacy-Administrationsoberfläche behalten.
Referenz: [Spezifikation des Legacy-Bridge-Bundles](../../spezifikationen/6.0/legacy-bridge-bundle.md).

**Repositories:** `se7enxweb/legacy-bridge` (Composer-Paket, Repository `legacyBridge`), `se7enxweb/ibexa-legacy-bridge`
(Portierung für Platform 4), `se7enxweb/site-legacy-bundle`, `se7enxweb/ngsymfonytools`, `se7enxweb/exponential-legacy-installer`.

## Was es ist

Die Legacy Bridge ist ein Symfony-Bundle, das den Kernel von Exponential 6 (das System, über das Sie hier lesen)
innerhalb einer Installation der Symfony-Plattform lädt. Eine einzige Webanwendung bedient dann beide Welten:

- die **Legacy-Administrationsoberfläche und die Legacy-Templates** (Ihre `.tpl`-Dateien, Ihre Extensions, Ihre
  Inhaltsklassen) über den Siteaccess `legacy_admin`;
- den **neuen Stack** (Twig-Templates, Symfony-Routen, REST, GraphQL, die Administrationsoberfläche der Plattform).

Beide lesen dieselbe Datenbank. Inhalte, die Redakteure in der Legacy-Administrationsoberfläche anlegen, erscheinen im
Twig-Frontend, und umgekehrt.

## Wozu Sie es einsetzen

- Sie haben eine Exponential-6-Site und möchten auf die Symfony-Plattform umsteigen, ohne die Redakteure auszusperren.
  Betreiben Sie beides, bis die Migration abgeschlossen ist.
- Sie möchten Twig, das Symfony-Routing oder den Seitenbaukasten Netgen Layouts für die öffentliche Site nutzen, Ihre
  Redakteure behalten aber die vertraute Legacy-Administrationsoberfläche.
- Sie halten Legacy-Extensions (zum Beispiel `ezjscore`, `eztags`, `ezwebin`) in einem Plattformprojekt verfügbar.

## Welche Version für welche Plattform

Das Paket hat eine Linie je Plattformgeneration: Die Linie für Platform 2.5 wird auf `master` getaggt, die späteren
Linien haben jeweils einen eigenen Branch. Die Tags sind in der [Release-Historie](../../../eng-us/history/ecosystem/legacyBridge.md)
aufgeführt.

| Branch / Tags | Plattform | Symfony | PHP | Benötigt |
|---|---|---|---|---|
| `master` (`v2.1.10` bis `v2.1.12`, 2025-08 bis 2026-04) | eZ Platform 2.5 LTS | 3.4 | 8.x mit Korrekturen | den Legacy-Kernel von Exponential |
| `3.x` (`3.0.0.1` bis `v3.0.0.37`, 25.03. bis 11.04.2026) | eZ Platform 3.3 | 5.4 | `^8.0` | `se7enxweb/exponential ^6.0.12`, `ezsystems/ezplatform-kernel ~1.3` |
| `4.x` (`v4.0.0.0` bis `v4.0.0.3`, 06.04. bis 17.04.2026) | mit Ibexa 4.6 kompatible Installationen | 5.4 | `^8.0` | `se7enxweb/exponential dev-main` |
| `5.x` (`v5.0.0.0`, `v5.0.1` bis `v5.0.10`) | Platform v5 | 7.4 | `^8.4` | zusätzlich `se7enxweb/site-bundle`, `se7enxweb/site-legacy-bundle`, `se7enxweb/ngsymfonytools` |

## Installation (Projekte im Stil von Platform 2.5)

```bash
composer require --update-with-all-dependencies "se7enxweb/legacy-bridge"
```

1. Aktivieren Sie das Bundle in `app/AppKernel.php`: Fügen Sie `new eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle( $this ),`
   am Ende des Arrays `$bundles` hinzu. Das Argument `$this` ist erforderlich.
2. Bereiten Sie das Projekt vor:

```bash
php bin/console ezpublish:legacy:init
```

Die Linie für Platform 2.5 (`v2.1.x`, auf `master`) kennt nur die Befehlsnamen `ezpublish:*`; die unter
[Konsolenbefehle](#konsolenbefehle) beschriebenen Namen `exponential:legacy:*` gibt es dort nicht.

Der Befehl gibt aus, was als Nächstes zu tun ist: Verschieben Sie Ihre Legacy-Dateien (Extensions, Einstellungen,
optional Designs) in das Projekt und fügen Sie dann den Siteaccess `legacy_admin` zur Siteaccess-Liste `list` und zur
genannten `site_group` hinzu. Das Legacy-Backoffice benötigt für diesen Siteaccess `legacy_mode: true`, was der
Initialisierungsschritt an das Ende Ihrer Plattformkonfiguration schreibt.

3. Führen Sie `composer symfony-scripts` ein- oder zweimal aus, damit die Assets erzeugt und verlinkt werden.
4. Optional: Erlauben Sie den Legacy-Einrichtungsassistenten in `app/config/security.yml`:

```yaml
ezpublish_setup:
    pattern: ^/ezsetup
    security: false
```

5. Fügen Sie die Legacy-Rewrite-Regeln zu Ihrem Apache-Virtual-Host hinzu, damit Legacy-Assets als Dateien ausgeliefert
   werden (Bilder im Speicher, `design`, `share/icons`, Designs der Extensions, `packages/styles`,
   `var/storage/packages`); eine Entsprechung für Nginx steht in der `INSTALL.md` der Bridge.
6. Korrigieren Sie die Berechtigungen wie bei jedem Plattformprojekt.

Fehlende Legacy-Extensions, die ältere Kernel mitbrachten (`ezfind`, `eztags`, der Skriptmonitor, die Extension für
Systeminformationen), fügen Sie mit `composer require` zur `composer.json` des Projekts hinzu.

### Cache-Verhalten

Standardmäßig leert die Bridge auch den Persistenz-Cache (SPI) der Plattform, wenn der Legacy-Cache für
Inhaltsansichten geleert wird, und `bin/console cache:clear` leert ihn ebenfalls. Beides lässt sich in der
Plattformkonfiguration einzeln abschalten:

| Datei | Schlüssel | Standard | Wirkung |
|---|---|---|---|
| Plattformkonfiguration (zum Beispiel `app/config/ezplatform.yml`) | `ez_publish_legacy.clear_all_spi_cache_from_legacy` | `true` | SPI-Cache leeren, wenn der Legacy-Ansichts-Cache geleert wird |
| dieselbe | `ez_publish_legacy.clear_all_spi_cache_on_symfony_clear_cache` | `true` | SPI-Cache bei `cache:clear` leeren |
| dieselbe | `ez_publish_legacy.enabled` | `true` | Bridge ein oder aus |
| dieselbe | `ez_publish_legacy.root_dir` | keiner | Pfad des Legacy-Wurzelverzeichnisses; muss existieren |
| dieselbe | `ez_publish_legacy.legacy_aware_routes` | leere Liste | Routen (oder Routenpräfixe), die bei aktivem `legacy_mode` erlaubt sind |
| Siteaccess-Geltungsbereich | `ez_publish_legacy.<scope>.legacy_mode` | nicht gesetzt | den Legacy-Kernel URL-Aliase verarbeiten lassen |
| Siteaccess-Geltungsbereich | `ez_publish_legacy.<scope>.templating.view_layout` | nicht gesetzt | Twig-Pagelayout, wenn eine Inhaltsansicht im Legacy-Kernel gerendert wird |
| Siteaccess-Geltungsbereich | `ez_publish_legacy.<scope>.templating.module_layout` | nicht gesetzt | Twig-Pagelayout für Legacy-Module (sonst das Legacy-Pagelayout) |

## Konsolenbefehle

Die sechs Befehle der Bridge wurden im April 2026 umbenannt, und zwar nur in den Linien `3.x`, `4.x` und `5.x`. Die
alten Namen bleiben dort als veraltete Aliase erhalten, sodass Skripte weiter funktionieren.

| Linie der Bridge | Verfügbare Befehlsnamen |
|---|---|
| `master` (`v2.1.10` bis `v2.1.12`, Platform 2.5) | nur die alten Namen `ezpublish:*` |
| `3.x` bis `v3.0.0.29` | nur die alten Namen `ezpublish:*` |
| `3.x` ab `v3.0.0.30` | `exponential:legacy:*`, mit den alten Namen als Aliase |
| `4.x` `v4.0.0.0` und `v4.0.0.1` | nur die alten Namen `ezpublish:*` |
| `4.x` ab `v4.0.0.2` | `exponential:legacy:*`, mit den alten Namen als Aliase |
| `5.x` (alle Tags) | `exponential:legacy:*`, mit den alten Namen als Aliase |

| Befehl | Alter Name (Alias) | Funktion |
|---|---|---|
| `exponential:legacy:init` | `ezpublish:legacy:init` | bereitet die Plattforminstallation für den Legacy-Betrieb vor |
| `exponential:legacy:configure` | `ezpublish:configure` | erzeugt die Plattformkonfiguration aus einem vorhandenen Legacy-Verzeichnis |
| `exponential:legacy:install-extensions` | `ezpublish:legacybundles:install_extensions` | installiert Legacy-Extensions, die Symfony-Bundles definieren (standardmäßig als symbolischer Link) |
| `exponential:legacy:symlink` | `ezpublish:legacy:symlink` | installiert Legacy-Einstellungen und Design-Dateien aus `src` nach `ezpublish_legacy/` |
| `exponential:legacy:assets-install` | `ezpublish:legacy:assets_install` | installiert Legacy-Assets und Front-Controller-Wrapper (zum Beispiel `index_cluster.php`) |
| `exponential:legacy:script` | `ezpublish:legacy:script` | führt ein Legacy-CLI-Skript im eingebundenen Kernel aus |

Beispiel: ein Legacy-Skript mit den Datenbankeinstellungen der Plattform ausführen. Der Skriptpfad ist relativ zum
Legacy-Wurzelverzeichnis; alle weiteren Optionen werden an das Legacy-Skript weitergereicht, und `--legacy-help` zeigt
die Hilfe des Skripts selbst:

```bash
php bin/console exponential:legacy:script bin/php/ezcache.php --legacy-help
php bin/console exponential:legacy:script bin/php/ezcache.php --clear-id=content --siteaccess=legacy_admin
```

## SQLite

Seit `v4.0.0.1` (07.04.2026) kann auch eine Plattforminstallation auf SQLite den Legacy-Kernel ausführen. Die Bridge
ordnet den Doctrine-Treiber `pdo_sqlite` der Legacy-Einstellung `DatabaseImplementation=sqlite3` zu und schreibt den
Pfad der Datenbankdatei in `[DatabaseSettings] Database` der Legacy-`site.ini`. Vor dieser Korrektur brach die Bridge
mit „Could not map database driver“ ab. Siehe [SQLite für die Plattform](plattform-sqlite-installation.md).

## Was sich im Lauf der Zeit geändert hat

- 2025-08: erste Exponential-Releases (`v2.1.10`): Der Installationsbefehl des Wrappers akzeptiert bei Installation über
  Composer ein in Anführungszeichen gesetztes Webverzeichnis, die Vendor-Namen wechselten zu se7enxweb.
- 2026-02: Das Admin-Design `admin3` ersetzt `admin2` als Standarddesign des Siteaccess `legacy_admin` (`v2.1.11`),
  siehe [die responsive Administrationsoberfläche](admin3-responsives-admin.md).
- 2026-03: Branch `3.x`: Portierung auf Symfony 5.4 und PHP 8: neue `TreeBuilder`-API, Events der Symfony Contracts,
  `RequestEvent` / `ResponseEvent`, typisierter Config-Resolver, Twig 3, über die DI registrierte Befehle mit
  Konstruktor-Injektion (kein `ContainerAwareCommand`), Permission-Resolver-API von Platform 3.
- 2026-04: Branch `4.x`: Service-Aliase von Ibexa 4.6, Session-Verwaltung für Symfony 5.3+ (Factory-basierte Sessions),
  SQLite, Umbenennung der Befehle, zusätzliche Oberflächenelemente für `legacy_admin` über die Extension `ngsite`.

## Grenzen

- Das Upstream-Projekt gab an, dass die Bridge auf eZ Platform 3.x nicht unterstützt wird, weil das Schema vom
  Legacy-Schema abweichen würde. Die Branches von se7enxweb behalten das Legacy-Schema für die oben genannten
  Plattform-Kernel bei; verwenden Sie den Branch, der zu Ihrer Plattform passt.
- Das Änderungsverzeichnis des Repositorys erfasst den ausgecheckten Branch (`4.x`); die Branches `3.x` und `5.x`
  tragen eigene Tags.

## Verwandte Seiten

- Plattformfunktionen: [Site-Bundles und Twig-Werkzeuge](plattform-site-bundles.md), [Nexus-Starter](plattform-nexus-starter.md), [SQLite für Exponential Platform](plattform-sqlite-installation.md), [Administrationsoberfläche der Plattform](plattform-admin-ui-fork.md), [DXP-Skeleton](plattform-dxp-skeleton.md), [Layouts auf der Plattform](plattform-layouts-core-fork.md), [Framework-Forks für PHP 8.5](plattform-php85-framework-forks.md), [Datenbankverwaltung AdminNeo](adminneo-datenbankverwaltung.md)
- Spezifikationen: [Legacy-Bridge-Bundle](../../spezifikationen/6.0/legacy-bridge-bundle.md), [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md), [Paketübersicht der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md), [SQLite-Installer der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md)
- Hinweise zur Aktualisierung: [Paket-Forks und Umbenennung von Befehlen](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Historie: [legacyBridge](../../../eng-us/history/ecosystem/legacyBridge.md) (49 Änderungen, 34 Releases), [ibexa-legacy-bridge---7x](../../../eng-us/history/ecosystem/ibexa-legacy-bridge---7x.md), [site-legacy-bundle](../../../eng-us/history/ecosystem/site-legacy-bundle.md), [exponential-legacy-installer](../../../eng-us/history/ecosystem/exponential-legacy-installer.md), [Überblick über das Ökosystem](../../../eng-us/history/ecosystem.md), [Monate des Ökosystems](../../../eng-us/history/ecosystem/months/2026-04.md), [Änderungsverzeichnis](../../../eng-us/history/ledger/README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
