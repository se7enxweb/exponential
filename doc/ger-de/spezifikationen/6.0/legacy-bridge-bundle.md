# Spezifikation: das Legacy-Bridge-Bundle

Diese Seite ist die Referenz für die Legacy-Bridge, das Symfony-Bundle, das den Kernel von Exponential 6 innerhalb einer
Plattform-Anfrage ausführt: sein Aufbau, die Ereignisse, auf die Sie hören können, die Einstellungen, die es von der Plattform
in den Legacy-Kernel überträgt, sein Konfigurationsbaum und seine Branches. Lesen Sie sie, wenn Sie ein Projekt konfigurieren
oder erweitern, das beide Kernel ausführt. Eine Schritt-für-Schritt-Einrichtung finden Sie unter
[Legacy-Bridge: Exponential 6 innerhalb der Symfony-Plattform ausführen](../../funktionen/6.0/legacy-bridge.md).

| Element | Wert |
|---|---|
| Paket | `se7enxweb/legacy-bridge` (Repository `legacyBridge`) |
| Bundle-Klasse | `eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle` |
| Wurzel der Konfiguration | `ez_publish_legacy` |

## Was die Bridge tut

Sie startet den Kernel von Exponential 6 innerhalb einer Symfony-Anfrage, teilt Datenbank, Siteaccess, Benutzer und
Caches mit dem Plattform-Kernel und lässt jeden Kernel die URLs bearbeiten, die ihm gehören.

## Aufbau des Bundles

| Pfad | Zuständigkeit |
|---|---|
| `bundle/DependencyInjection/` | Konfigurationsbaum (`ez_publish_legacy`), Behandlung der Geltungsbereiche, Laden der Services aus `bundle/Resources/config/*.yml` |
| `bundle/LegacyMapper/` | `Configuration`, `LegacyBundles`, `Security`, `Session`, `SiteAccess`: übertragen den Zustand der Plattform in die Einstellungen des Legacy-Kernels, bevor dieser aufgebaut wird |
| `bundle/Controller/` | `LegacyKernelController` (führt eine Legacy-Anfrage aus), `LegacyRestController`, `LegacySetupController` (Setup-Assistent), `LegacyTreeMenuController`, `PreviewController`, `WebsiteToolbarController` |
| `bundle/EventListener/` | `ConfigScopeListener`, `CsrfTokenResponseListener`, `IndexRequestListener`, `LegacyKernelListener`, `RequestListener`, `RestListener`, `SetupListener` |
| `bundle/Command/` | die sechs Befehle `exponential:legacy:*` ([Namen](platform-konsolenbefehle.md)) |
| `bundle/Cache/` | Leeren des Persistenz-Caches, wenn der Legacy-View-Cache geleert wird |
| `bundle/LegacyBundles/`, `bundle/Composer/` | Installation von Legacy-Extensions, die Symfony-Bundles definieren; Composer-Skripte |
| `bundle/FieldType/`, `bundle/Rest/`, `bundle/Routing/`, `bundle/Security/`, `bundle/SetupWizard/`, `bundle/Collector/` | Unterstützung des Feldtyps XML-Text, REST-Brücke, Legacy-bewusstes Routing, Behandlung der Anmeldung, Einbindung des Setup-Assistenten, Datensammler für die Debug-Toolbar |
| `mvc/` | Aufbau des Kernels (`Kernel`, `LegacyEvents`, `LegacyKernelAware`), Ereignisklassen, Adapter für Bilder, Sitzungen, Sicherheit und Signal-Slots |

## Ereignisse

Konstanten von `LegacyEvents`. Ein Listener darf die Legacy-Einstellungen ändern, während der Kernel aufgebaut wird:

| Konstante | Ereignisname | Wann |
|---|---|---|
| `PRE_BUILD_LEGACY_KERNEL_WEB` | `ezpublish_legacy.build_kernel_web_handler` | bevor der Web-Handler aufgebaut wird |
| `PRE_BUILD_LEGACY_KERNEL` | `ezpublish_legacy.build_kernel` | bevor der Kernel aufgebaut wird; hier hört der `Configuration`-Mapper der Bridge (Priorität 128) |
| `POST_BUILD_LEGACY_KERNEL` | `ezpublish_legacy.post_build_kernel` | nachdem der Kernel aufgebaut ist |
| `PRE_RESET_LEGACY_KERNEL` | `ezpublish_legacy.pre_reset_legacy_kernel` | bevor der Kernel zurückgesetzt wird |

## Von der Plattform in den Legacy-Kernel übertragene Einstellungen

Der `Configuration`-Mapper (wenn die Bridge eingeschaltet ist) setzt diese Werte als Legacy-INI-Overrides, bevor der Kernel startet.

| Legacy-Einstellung | Quelle |
|---|---|
| `site.ini/DatabaseSettings/Server`, `Port`, `User`, `Password`, `Database`, `Socket` | Doctrine-Verbindungsparameter `host`, `port`, `user`, `password`, `dbname`, `unix_socket` (`Socket` ist `disabled`, wenn kein Socket gesetzt ist) |
| `site.ini/DatabaseSettings/DatabaseImplementation` | Doctrine-Treiber über die Zuordnung unten |
| `site.ini/DatabaseSettings/Database` (SQLite) | Doctrine-Parameter `path`, seit `v4.0.0.1` |
| `site.ini/FileSettings/VarDir`, `StorageDir` | Plattformparameter `var_dir`, `storage_dir` |
| `site.ini/UserSettings/AnonymousUserID` | Plattformparameter `anonymous_user_id` |
| `site.ini/ContentSettings/ViewCaching` | fest auf `enabled` gesetzt |
| `site.ini/SiteAccessSettings/PathPrefix`, `PathPrefixExclude` | Wurzelort und Pfadpräfix des Siteaccess |
| `site.ini/SiteSettings/IndexPage`, `DefaultPage` | konfigurierte Werte, sonst `/content/view/full/<root location id>/` |
| `image.ini/ImageMagick/*` | ImageMagick-Optionen und -Filter der Plattform |
| `file.ini/ClusteringSettings/FileHandler` und `eZDFSClusteringSettings/*` | DFS-Clustering-Parameter, wenn DFS konfiguriert ist |

Treiberzuordnung (`LegacyMapper\Configuration`):

| Doctrine-Treiber | Legacy-`DatabaseImplementation` |
|---|---|
| `pdo_mysql` | `ezmysqli` |
| `pdo_pgsql` | `ezpostgresql` |
| `oci8` | `ezoracle` |
| `pdo_sqlite` | `sqlite3` (hinzugefügt in `v4.0.0.1`, 2026-04-07) |

Jeder andere Treiber löst eine `RuntimeException` aus: „Could not map database driver to Legacy Stack database implementation.“

## Konfigurationsbaum

Wurzel `ez_publish_legacy`, in einer Konfigurationsdatei der Plattform wie `config/packages/ezplatform.yaml` oder `app/config/ezplatform.yml`:

| Schlüssel | Typ | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|---|
| `enabled` | bool | `true` | global | schaltet die Bridge ein oder aus |
| `clear_all_spi_cache_on_symfony_clear_cache` | bool | `true` | global | `cache:clear` leert auch den Persistenz-Cache |
| `clear_all_spi_cache_from_legacy` | bool | `true` | global | das Leeren des Legacy-Inhalts-View-Caches leert den Persistenz-Cache |
| `root_dir` | string | keiner | global | Legacy-Wurzelverzeichnis; die Validierung schlägt fehl, wenn der Pfad nicht existiert |
| `legacy_aware_routes` | Liste von Strings | `[]` | global | Routen oder Routenpräfixe, die noch erreichbar sind, wenn `legacy_mode` true ist |
| `templating.view_layout` | string | keiner | Siteaccess | Twig-Pagelayout, das verwendet wird, wenn eine Inhaltsansicht in Legacy gerendert wird |
| `templating.module_layout` | string | keiner | Siteaccess | Twig-Pagelayout für Legacy-Module |
| `legacy_mode` | bool | keiner | Siteaccess | der Legacy-Kernel bearbeitet die URL-Aliase des Siteaccess |

Beispiel für den Siteaccess `legacy_admin`:

```yaml
ez_publish_legacy:
    clear_all_spi_cache_from_legacy: true
    legacy_aware_routes: [ 'my_legacy_route_' ]
    system:
        legacy_admin:
            legacy_mode: true
```

## Branches und Anforderungen

| Branch | `php` | Wichtigste Anforderungen (aus `composer.json`) |
|---|---|---|
| `3.x` | `^8.0` | `se7enxweb/exponential ^6.0.12`, `se7enxweb/ezpublish-legacy-installer ^2.0.4`, `ezsystems/ezplatform-kernel ~1.3`, `se7enxweb/ezplatform-xmltext-fieldtype ^2.0`, `symfony/framework-bundle ^5.4`, `se7enxweb/ngsymfonytools ^4.0`, `se7enxweb/richtext-datatype-bundle 2.0.x-dev` |
| `4.x` | `^8.0` | wie `3.x`, mit `se7enxweb/exponential dev-main` und `se7enxweb/ezplatform-xmltext-fieldtype ^4.0` |
| `5.x` | `^8.4` | `se7enxweb/exponential dev-main`, `se7enxweb/ezplatform-xmltext-fieldtype 5.x-dev`, `se7enxweb/ngsymfonytools v5.0.x-dev`, `se7enxweb/richtext-datatype-bundle *`, `se7enxweb/site-bundle 5.0.x-dev`, `se7enxweb/site-legacy-bundle`, ein Paket für den Datenbank-Übersetzer |

## Verwandte Bundles

- `site-legacy-bundle`: Bindeglied zwischen dem neuen und dem Legacy-Kernel für Sites im Netgen-Stil (im März 2026 auf die Service-IDs von eZ Platform 3.3 portiert).
- `ngsymfonytools`: Legacy-Extension mit einem Template-Operator `symfony_include`, um aus einer Legacy-`.tpl` ein Twig-Template oder eine Symfony-Unteranfrage zu rendern. Der Operator holt den Service `twig` aus dem Container; das funktioniert ab Symfony 5, weil der `TwigPass` der Bridge den Service öffentlich macht (`v5.0.9`, April 2026). Außerdem bringt sie einen Klassen-Alias vom alten Repository-Interface auf das von Ibexa mit.
- `ibexa-legacy-bridge` (Platform 4): Portierung der Bridge auf die APIs von Ibexa 4, mit `exponential:legacy:script`.

## Verwandte Seiten

- Funktionen: [Legacy-Bridge](../../funktionen/6.0/legacy-bridge.md), [Site-Bundles](../../funktionen/6.0/plattform-site-bundles.md), [Administrationsoberfläche der Plattform](../../funktionen/6.0/plattform-admin-ui-fork.md), [DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md), [Layouts auf der Plattform](../../funktionen/6.0/plattform-layouts-core-fork.md), [Nexus-Starter](../../funktionen/6.0/plattform-nexus-starter.md), [Framework-Forks für PHP 8.5](../../funktionen/6.0/plattform-php85-framework-forks.md), [SQLite für Exponential Platform](../../funktionen/6.0/plattform-sqlite-installation.md), [Datenbankverwaltung AdminNeo](../../funktionen/6.0/adminneo-datenbankverwaltung.md)
- Spezifikationen: [Namen der Konsolenbefehle der Plattform](platform-konsolenbefehle.md), [Paketübersicht der Plattform](platform-paketzuordnung.md), [SQLite-Installer der Plattform](platform-sqlite-installer.md)
- Upgrade-Hinweise: [Paket-Forks und umbenannte Befehle](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Geschichte: [Überblick über das Ökosystem](../../geschichte/oekosystem.md), [Monate des Ökosystems](../../geschichte/oekosystem/monate/2026-04.md), [Änderungsverzeichnis](../../geschichte/aenderungsverzeichnis/README.md); Repositorys [legacyBridge](../../geschichte/oekosystem/legacyBridge.md), [ibexa-legacy-bridge---7x](../../geschichte/oekosystem/ibexa-legacy-bridge---7x.md), [site-legacy-bundle](../../geschichte/oekosystem/site-legacy-bundle.md), [ngsymfonytools](../../geschichte/oekosystem/ngsymfonytools.md)
