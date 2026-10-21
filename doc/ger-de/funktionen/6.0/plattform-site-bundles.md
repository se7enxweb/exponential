# Site-Bundles, Metadaten und die Twig-Werkzeuge der Plattform

Diese Seite ist für Entwickler, die eine [Nexus](../../../eng-us/features/6.0/platform-nexus-starter.md)-Website auf Exponential Platform bauen oder
erweitern. Diese Pakete sind die Bausteine, aus denen Nexus-Websites bestehen. Normalerweise installieren Sie sie nicht
von Hand; die Nexus-Distribution verlangt sie. Zu wissen, was jedes tut, sagt Ihnen, wo Sie suchen müssen, wenn sich eine
Website unerwartet verhält: Ein Menü, ein Query-Typ, ein Layout-Block oder eine Legacy-Zuordnung steckt meist in einem
davon.

**Repositorys:** `se7enxweb/site-bundle`, `se7enxweb/site-legacy-bundle`, `se7enxweb/ngsymfonytools`,
`se7enxweb/metadata-bundle`, `se7enxweb/mediata-ezpage-fieldtype-bundle`, `se7enxweb/ez-support-tools`.

## Was jedes Paket einer Website gibt

| Paket | Was es einer Website gibt |
|---|---|
| `se7enxweb/site-bundle` (Fork von Netgen Site Bundle) | Die gemeinsamen Funktionen, auf denen Netgen Websites baut: `Menu`, Integration von `Layouts`, `ContentForms`, `InfoCollection`, Metadaten `OpenGraph`, Blättern mit `Pagerfanta`, `QueryType` für Inhaltsabfragen, Ausgabe von `RichText`, Helfer `Relation` und `Topic`, eine Integration von `Imagine` für Bildvarianten, ein `ContextProvider` (welcher Teil des Baums die Website ist), ein Datensammler für die Debug-Werkzeugleiste, Konsolenbefehle. Nicht für den eigenständigen Einsatz; es ist das Basis-Bundle eines Projekts. |
| `se7enxweb/site-legacy-bundle` | Verbindung zwischen dem neuen und dem Legacy-Kernel für Websites im Stil von Netgen, die mit der [Legacy Bridge](../../../eng-us/features/6.0/legacy-bridge.md) laufen: Legacy-Mapper, Templating, Ereignis-Listener und ein Verzeichnis `ezpublish_legacy` mit der Legacy-Extension. Die Copyright-Templates der Administrationsoberfläche tragen seit dem 17.04.2026 die Marke Exponential. |
| `se7enxweb/ngsymfonytools` | Eine Legacy-Extension: Template-Operatoren, mit denen ein `.tpl`-Template von Exponential Twig und Symfony aufrufen kann |
| `se7enxweb/metadata-bundle` | Der Feldtyp für Metadaten (SEO) der Plattform, datenkompatibel mit der Legacy-Extension `xrowmetadata`, sodass dasselbe Feld und dieselben gespeicherten Daten auf dem Legacy-Kernel und auf dem neuen Stack funktionieren. Das Release `v5.0.0` fügt Unterstützung für Ibexa 5.x und PHP 8.4 hinzu. |
| `se7enxweb/mediata-ezpage-fieldtype-bundle` | Der Feldtyp page (Landingpage) für Ibexa 4, am 16.03.2026 als für 4.6 getestet importiert, mit einem Twig-Operator, sodass einfache Symfony-Projekte ihn verwenden können |
| `se7enxweb/ez-support-tools` | Die Seite mit Systeminformationen für Administratoren (PHP, Composer-Pakete, Plattformversionen). Im April 2026 korrigiert, sodass eine Null-Version aus `InstalledVersions::getVersion()` die Seite nicht mehr zerstört. |

Die Pakete von se7enxweb ersetzen die Upstream-Namen über Deklarationen `replace` von Composer. `site-bundle` deklariert
`netgen/site-bundle:*` als ersetzt (Commit vom 19.04.2026, Release `3.0.6`), sodass ein Projekt nicht mit dem Fork und
der Upstream-Kopie nebeneinander installiert enden kann. Siehe die
[Hinweise zur Aktualisierung](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md).

## Twig aus einem Legacy-Template aufrufen

Mit aktivem `ngsymfonytools` kann ein Legacy-`.tpl` ein Twig-Template einbinden oder einen Symfony-Controller ausgeben.

1. Aktivieren Sie die Extension (Administrationsoberfläche: Setup, Extensions; oder in
   `settings/override/site.ini.append.php`):

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=ngsymfonytools
   ```

2. Erzeugen Sie das Autoload-Array neu (Administrationsoberfläche: Setup, Extensions, „Autoload-Arrays neu erzeugen“;
   oder führen Sie das Legacy-Skript `bin/php/ezpgenerateautoloads.php` über
   [`exponential:legacy:script`](../../../eng-us/features/6.0/legacy-bridge.md) aus).

3. Binden Sie ein Twig-Template ein:

   ```smarty
   {symfony_include(
       'AppBundle:Test:test.html.twig',
       hash(
           'theAnswer', 42,
           'homepage', fetch( 'content', 'node', hash( 'node_id', 2 ) )
       )
   )}
   ```

Operatoren (aus der Benutzungsanleitung der Extension):

| Operator | Zweck |
|---|---|
| `symfony_include( template, hash )` | Ein Twig-Template einbinden; Parameter vom Typ `eZContentObject` und `eZContentObjectTreeNode` werden in Objekte `Content` und `Location` der Plattform umgewandelt |
| `symfony_path( route, hash )` | Relative URL einer Route |
| `symfony_url( route, hash )` | Absolute URL einer Route |
| `symfony_controller( controller, hash, hash )` | Verweis auf einen Controller (innerhalb von `symfony_render` verwenden) |
| `symfony_render( ... )` | Einen Controller, Pfad oder eine URL im Legacy-Template ausgeben |
| `symfony_render_esi( ... )`, `symfony_render_hinclude( ... )` | Ein ESI- oder Hinclude-Tag für einen Controller oder eine URL erzeugen |

Änderungen in 2026: Der Operator verwendet nicht mehr den Dienst `templating`, den Symfony 5 entfernt hat (05.04.2026);
er fragt den Container nach dem Dienst `twig`. Am 16.04.2026 verwendete er kurz die ID `Twig\Environment::class` und
kehrte am selben Tag zu `twig` zurück, weil das Release `v5.0.9` der Legacy Bridge den Dienst `twig` öffentlich macht
(ihr `TwigPass` ruft `setPublic(true)` auf), sodass Legacy-Code ihn wieder holen kann. Ein `class_alias` am Anfang des
Inhaltskonverters hält den alten Schnittstellennamen des Repositorys auf Ibexa DXP 4.x und 5.0 funktionsfähig.

## Grenzen

- `site-bundle` und `site-legacy-bundle` sind nicht eigenständig: Sie brauchen ein nach dem Nexus-Muster gebautes
  Projekt.
- Der Metadaten-Feldtyp braucht den Plattform-Kernel. Auf dem Legacy-Kernel verwenden Sie die Extension `xrowmetadata`,
  die dieselben Daten schreibt.

## Verwandte Seiten

- Seiten der Extensions: [ngsymfonytools](extensions/ngsymfonytools.md), [ez-support-tools](extensions/ez-support-tools.md), [xrowmetadata](extensions/xrowmetadata.md)
- Plattformfunktionen: [Nexus-Starter](../../../eng-us/features/6.0/platform-nexus-starter.md), [Legacy Bridge](../../../eng-us/features/6.0/legacy-bridge.md), [Administrationsoberfläche der Plattform](plattform-admin-ui-fork.md), [DXP-Skeleton](../../../eng-us/features/6.0/platform-dxp-skeleton.md), [Layouts auf der Plattform](plattform-layouts-core-fork.md), [Framework-Forks für PHP 8.5](plattform-php85-framework-forks.md), [SQLite für Exponential Platform](../../../eng-us/features/6.0/platform-sqlite-install.md), [Datenbankverwaltung AdminNeo](adminneo-datenbankverwaltung.md)
- Spezifikationen: [Paketkarte der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md), [Bundle der Legacy Bridge](../../spezifikationen/6.0/legacy-bridge-bundle.md), [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md), [SQLite-Installer der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md)
- Hinweise zur Aktualisierung: [Paket-Forks und Umbenennungen von Befehlen](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Chronik: [site-bundle](../../../eng-us/history/ecosystem/site-bundle.md), [site-legacy-bundle](../../../eng-us/history/ecosystem/site-legacy-bundle.md), [ngsymfonytools](../../../eng-us/history/ecosystem/ngsymfonytools.md), [metadata-bundle](../../../eng-us/history/ecosystem/metadata-bundle.md), [mediata-ezpage-fieldtype-bundle-main](../../../eng-us/history/ecosystem/mediata-ezpage-fieldtype-bundle-main.md), [ez-support-tools](../../../eng-us/history/ecosystem/ez-support-tools.md), [Übersicht über das Ökosystem](../../../eng-us/history/ecosystem.md), [Monate des Ökosystems](../../../eng-us/history/ecosystem/months/2026-04.md), [Änderungsverzeichnis](../../../eng-us/history/ledger/README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
