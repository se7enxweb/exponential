# Das Plattform-Ökosystem von Exponential

Exponential 6 ist der Legacy-Kernel, mit dem diese Installation läuft. Um ihn herum pflegt das se7enxweb-Team eine Familie von Repositorys. Sie bringen dasselbe Produkt auf die Symfony-basierte Plattform (Exponential Platform 3.x, 4.6 und v5), halten den alten Stack aus Symfony, Twig und Doctrine unter PHP 8.4 und 8.5 lauffähig und liefern fertige Websites aus. Diese Seite ordnet sie, sagt, was jedes Repository Ihnen bietet, und verlinkt die Geschichte jedes einzelnen. Lesen Sie sie, wenn Sie ein Plattform-Paket auswählen oder wissen wollen, welcher Fork welches Upstream-Paket ersetzt; wenn Sie nur Exponential 6 betreiben, ist die [Monatschronik](README.md) der bessere Einstieg.

Die Geschichte umfasst 6556 Änderungen des Änderungsverzeichnisses in 53 Repositorys seit Dezember 2023. 1037 davon stammen vom se7enxweb-Team; der Rest ist Upstream-Geschichte, die ein Fork mitführt (zum Beispiel die 1.192 Upstream-Änderungen im Fork der Administrationsoberfläche). Die vollständige Aufzeichnung Zeile für Zeile steht in den [Änderungsverzeichnissen](aenderungsverzeichnis/README.md).

## So wählen Sie aus

| Sie wollen | Verwenden Sie | Lesen Sie |
|---|---|---|
| Das Legacy-CMS Exponential 6 (diese Installation) | `se7enxweb/exponential` | [exponential](oekosystem/exponential.md) |
| Eine Website mit Symfony 7.4 / PHP 8.4+ mit Layouts, REST und GraphQL | `se7enxweb/exponential-platform-nexus-starter` oder `se7enxweb/exponential-platform-dxp-skeleton` | [Nexus-Starter](../../eng-us/features/6.0/platform-nexus-starter.md), [DXP-Skeleton](../../eng-us/features/6.0/platform-dxp-skeleton.md) |
| Eine bestehende Website mit Exponential 6 behalten und die Symfony-Plattform daneben stellen | `se7enxweb/legacy-bridge` | [Legacy Bridge](../../eng-us/features/6.0/legacy-bridge.md) |
| Eine Datenbank ohne Server | SQLite-Installation der Plattform | [SQLite für die Plattform](../../eng-us/features/6.0/platform-sqlite-install.md) |
| Wissen, welches Paket welches Upstream-Paket ersetzt | die Paketübersicht | [Paketübersicht](../spezifikationen/6.0/platform-paketzuordnung.md) |
| Wissen, was sich beim Upgrade eines Forks ändert | den Upgrade-Hinweis | [Paket-Forks und umbenannte Befehle](../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md) |

## Distributionen und Starter

| Repository | Was es ist | Was Anwender davon haben | Änderungen (eigene / gesamt) |
|---|---|---|---|
| [exponential-platform-nexus-starter](oekosystem/exponential-platform-nexus-starter.md) | Nexus für Platform v5 (Symfony 7.4, PHP 8.4+, SQLite-Starter). | Composer und ein Installationsbefehl ergeben eine v5-Website mit Layouts, REST, GraphQL und Administration. | 16 / 248 |
| [exponential-platform-dxp](oekosystem/exponential-platform-dxp.md) | Metapaket se7enxweb/exponential-platform-dxp für Platform v5. | Ein Paketname für eine vollständige v5-Installation. | 20 / 112 |
| [exponential-platform-nexus](oekosystem/exponential-platform-nexus.md) | Exponential Platform Nexus: Netgen Layouts + Media Site + Tags + Plattform und Legacy. | Eine funktionierende Website mit Demo-Inhalten, Layouts und Administration, in Minuten installiert. | 97 / 97 |
| [cjw-exponential-platform-nexus](oekosystem/cjw-exponential-platform-nexus.md) | Die CJW-Variante von Nexus (se7enxweb/cjw-exponential-platform-nexus). | Nexus mit den Startinhalten von CJW. | 89 / 89 |
| [exponential-platform-legacy](oekosystem/exponential-platform-legacy.md) | Distribution Exponential Platform Legacy 2.5 (Plattform plus Legacy). | Composer-Projekt für die LTS-Linie 2.5 auf PHP 8.1 und 8.2. | 46 / 46 |
| [exponential-platform-v4x-dxp-skeleton](oekosystem/exponential-platform-v4x-dxp-skeleton.md) | Projekt-Skeleton für Platform 4.6.x DXP. | create-project für 4.6. | 19 / 25 |
| [exponential-platform-dxp-skeleton](oekosystem/exponential-platform-dxp-skeleton.md) | Projekt-Skeleton für Platform v5 DXP (create-project). | composer create-project und ein dokumentierter Installationsweg. | 15 / 21 |
| [exponential-platform](oekosystem/exponential-platform.md) | Distribution Exponential Platform 3.x (Symfony). | Composer-Projekt, das eine Website mit Exponential Platform 3.2.9 startet. | 11 / 11 |
| [oss](oekosystem/oss.md) | Metapaket für die Open-Source-Edition der Plattform (Linie 3.3 / 4.6). | Ein einziges require ohne ein Paket ezsystems/*. | 5 / 10 |
| [oss-skeleton](oekosystem/oss-skeleton.md) | Projektpaket zur Installation der Open-Source-Edition. | Referenz-Skeleton. | 2 / 8 |
| [mirror.exponential.se7enx.com](oekosystem/mirror.exponential.se7enx.com.md) | Repository nur mit README, das den Spiegel der Dokumentation und der Bücher beschreibt. | Wo Sie die Bücher herunterladen. | 5 / 5 |
| [exponential-legacy-installer](oekosystem/exponential-legacy-installer.md) | Composer-Plugin, das den Legacy-Kernel und Legacy-Erweiterungen installiert. | composer install funktioniert weiter mit aktuellem Composer. | 4 / 4 |

## Kernel

| Repository | Was es ist | Was Anwender davon haben | Änderungen (eigene / gesamt) |
|---|---|---|---|
| [core](oekosystem/core.md) | Fork des Content-Repositorys Ibexa core 5.0 (der neue, Symfony-basierte Stack). | Exponential Platform v5 ganz ohne Datenbankserver installieren und betreiben (SQLite), neben MySQL, MariaDB und PostgreSQL. | 4 / 655 |
| [exponential](oekosystem/exponential.md) | Der Legacy-Kernel von Exponential 6 (Composer-Paket se7enxweb/exponential, PHP 8.1 und neuer). | Unterstützung für PHP 8.1 bis 8.5, ein SQLite-Datenbanktreiber und Installationsweg, das responsive Design admin3, neue Template-Operatoren, INI-Verarbeitung für mehrere Websites und die Releases v6.0.x. | 420 / 423 |
| [ezplatform-kernel](oekosystem/ezplatform-kernel.md) | Fork des Kernels von eZ Platform 1.13 / 2.5 / 3.3 (Content-Repository, APIs, Symfony-Integration). | Marke Exponential, Versionsbedingungen für PHP 8.5, Konsolenbefehle exponential:* und Unterstützung der Installation mit SQLite in der Linie 3.x / 4.6. | 23 / 38 |
| [ezpublish-kernel](oekosystem/ezpublish-kernel.md) | Fork des Kernels aus der Zeit von 2014.11, den der Symfony-Stack verwendet, der den Legacy-Kernel umschließt. | Composer-Installationen auf PHP 8.1 bis 8.3 und neuer, Template-Korrekturen für PHP 8.5, funktionierende Cache-Konfiguration, SQLite-Installer. | 30 / 31 |
| [ezplatform-core](oekosystem/ezplatform-core.md) | Container-Paket, das die Kern-Bundles von Exponential Platform holt. | Eine require-Zeile, die die passenden Kern-Bundles bringt. | 2 / 8 |

## Administrationsoberfläche

| Repository | Was es ist | Was Anwender davon haben | Änderungen (eigene / gesamt) |
|---|---|---|---|
| [admin-ui-7x](oekosystem/admin-ui-7x.md) | Fork der Ibexa-Administrationsoberfläche (se7enxweb/admin-ui) für Platform v5. | Logo und Favicons von Exponential, übersetzte Texte und Asset-Pfade, die innerhalb der Pakete von se7enxweb aufgelöst werden, sodass sich die Administration ohne die Vendor-Verzeichnisse des Upstreams bauen lässt. | 11 / 1193 |
| [admin-ui-ibexa](oekosystem/admin-ui-ibexa.md) | Dasselbe Repository wie admin-ui-7x, gesehen über den Namen des Upstream-Remotes. | Siehe admin-ui-7x; diese Seite führt die Releases der Linie 5.0.5.x. | 10 / 1192 |
| [admin-ui-assets](oekosystem/admin-ui-assets.md) | Externe JavaScript- und CSS-Abhängigkeiten der Administrationsoberfläche von Platform v5. | Die Admin-Assets werden vom Vendor se7enxweb installiert. | 3 / 44 |
| [ezplatform-admin-ui](oekosystem/ezplatform-admin-ui.md) | Fork der Administrationsoberfläche von eZ Platform 2.x / 3.x (Exponential Platform Admin v2). | Marke Exponential, Umstellung von SCSS auf @use/@forward, funktionierende Asset-Pfade unter PHP 8.5. | 23 / 36 |
| [ezplatform-standard-design](oekosystem/ezplatform-standard-design.md) | Standard-Design-Bundle von eZ Platform (Exponential Platform Standard Design Bundle). | Das Design-Bundle für das Frontend lässt sich auf aktuellem PHP installieren. | 4 / 4 |
| [ezplatform-admin-ui-assets](oekosystem/ezplatform-admin-ui-assets.md) | Externe Asset-Abhängigkeiten der Administrationsoberfläche 2.x / 3.x. | Die Admin-Assets werden vom Vendor se7enxweb installiert. | 2 / 3 |
| [ezplatform-design-engine](oekosystem/ezplatform-design-engine.md) | Fallback-Mechanismus für Designs (Theme-Kette) für die Plattform. | Der Theme-Fallback funktioniert auch in Installationen mit Forks weiter. | 3 / 3 |
| [ezplatform-alloyeditor-element-width](oekosystem/ezplatform-alloyeditor-element-width.md) | Bundle, das dem Online-Editor das Bearbeiten der Elementbreite hinzufügt. | Redakteure können die Breite von Elementen im Rich Text festlegen. | 1 / 1 |

## Feldtypen

| Repository | Was es ist | Was Anwender davon haben | Änderungen (eigene / gesamt) |
|---|---|---|---|
| [fieldtype-richtext-ibexa](oekosystem/fieldtype-richtext-ibexa.md) | Fork des Feldtyps RichText von Ibexa für Platform v5 (se7enxweb/fieldtype-richtext). | Das Rich-Text-Feld wird korrekt dargestellt und bearbeitet, wenn es aus Paketen von se7enxweb installiert ist. | 2 / 244 |
| [ezplatform-richtext](oekosystem/ezplatform-richtext.md) | Fork des Feldtyps RichText von eZ Platform (Linie 3.x). | Rich Text funktioniert unter PHP 8.5 weiter. | 7 / 7 |
| [ezplatform-xmltext-fieldtype](oekosystem/ezplatform-xmltext-fieldtype.md) | Feldtyp XmlText für die Symfony-Plattform. | Bestehende XML-Textfelder aus Legacy-Inhalten werden im neuen Stack angezeigt. | 7 / 7 |
| [mediata-ezpage-fieldtype-bundle-main](oekosystem/mediata-ezpage-fieldtype-bundle-main.md) | Der Feldtyp Page (Landing Page) für Ibexa 4. | Der Feldtyp Page ist auf Platform 4.6 / 3.3 verfügbar. | 7 / 7 |
| [ezplatform-matrix-fieldtype](oekosystem/ezplatform-matrix-fieldtype.md) | Feldtyp Matrix (Tabelle). | Matrix-Felder lassen sich auf aktuellem PHP installieren. | 4 / 4 |
| [metadata-bundle](oekosystem/metadata-bundle.md) | Bundle mit einem Feldtyp für Metadaten, kompatibel mit der Legacy-Erweiterung xrowmetadata. | Dasselbe Feld für SEO-Metadaten funktioniert im Legacy-Kernel und im neuen Stack. | 4 / 4 |
| [ezplatform-query-fieldtype](oekosystem/ezplatform-query-fieldtype.md) | Feldtyp, der eine Inhaltsabfrage speichert. | Abfragefelder lassen sich auf aktuellem PHP installieren. | 3 / 3 |
| [ibexa-xmltext-fieldtype](oekosystem/ibexa-xmltext-fieldtype.md) | Feldtyp XmlText für Ibexa OSS. | XML-Textinhalte sind auf Platform v4/v5 lesbar. | 1 / 1 |

## Suche, Cache und API

| Repository | Was es ist | Was Anwender davon haben | Änderungen (eigene / gesamt) |
|---|---|---|---|
| [ez-support-tools](oekosystem/ez-support-tools.md) | Seiten mit Systeminformationen für Administratoren (system info). | Die Seite Systeminformationen der Administration bricht bei Paketversionen null nicht mehr ab. | 5 / 7 |
| [ezplatform-http-cache](oekosystem/ezplatform-http-cache.md) | HTTP-Cache-Verarbeitung (Varnish / Symfony) für die Plattform. | Caching über einen Reverse Proxy funktioniert unter PHP 8.5. | 6 / 7 |
| [ezplatform-solr-search-engine](oekosystem/ezplatform-solr-search-engine.md) | Anbindung der Suchmaschine Solr. | Volltextsuche über Solr. | 3 / 6 |
| [ezplatform-graphql](oekosystem/ezplatform-graphql.md) | GraphQL-Server für das Content-Repository. | GraphQL-API in Installationen mit Forks. | 4 / 4 |
| [ezplatform-search](oekosystem/ezplatform-search.md) | Such-Bundle der Plattform. | Das Such-Bundle wird von se7enxweb installiert. | 4 / 4 |
| [ezplatform-user](oekosystem/ezplatform-user.md) | Benutzer-Bundle (Anmeldung, Registrierung, Profil). | Benutzerfunktionen unter PHP 8.5. | 4 / 4 |
| [ezplatform-cron](oekosystem/ezplatform-cron.md) | Einfaches Cron-Bundle. | Geplante Jobs auf der Plattform. | 1 / 1 |

## Legacy Bridge und Site-Bundles

| Repository | Was es ist | Was Anwender davon haben | Änderungen (eigene / gesamt) |
|---|---|---|---|
| [site-bundle](oekosystem/site-bundle.md) | Netgen Site Bundle: gemeinsame Website-Funktionen (Menüs, Verbindung zu Layouts, Website-Kontext) für Ibexa-Websites. | Website-Funktionen, auf denen Nexus aufbaut. | 2 / 105 |
| [legacyBridge](oekosystem/legacyBridge.md) | Bridge, die den Legacy-Kernel von Exponential innerhalb der Symfony-Plattform ausführt (se7enxweb/legacy-bridge). | Eine bestehende Website mit Exponential 6 neben Platform 3.x / 4.6 / 5.x betreiben, mit Code, der mit PHP 8.x kompatibel ist, und den Befehlen exponential:legacy:*. | 49 / 49 |
| [ibexa-legacy-bridge---7x](oekosystem/ibexa-legacy-bridge---7x.md) | Bridge von Ibexa 4 zum Legacy-Kernel von Exponential (se7enxweb/ibexa-legacy-bridge). | Legacy Bridge für Platform 4.6. | 7 / 16 |
| [site-legacy-bundle](oekosystem/site-legacy-bundle.md) | Netgen Site Legacy Bundle: Verbindung zwischen dem neuen und dem Legacy-Kernel. | Legacy-Seiten der Administration in Nexus zeigen die richtige Marke und funktionieren auf Platform 3.3. | 10 / 10 |
| [ngsymfonytools](oekosystem/ngsymfonytools.md) | Legacy-Erweiterung, die Twig-Templates und Symfony-Unteranfragen aus Legacy-Templates einbindet. | Ein Legacy-Template (.tpl) kann ein Twig-Template oder eine Symfony-Route einbetten. | 7 / 7 |

## Layouts und Rezepte

| Repository | Was es ist | Was Anwender davon haben | Änderungen (eigene / gesamt) |
|---|---|---|---|
| [layouts-core](oekosystem/layouts-core.md) | Der Kern von Netgen Layouts (Page-Builder-Engine in PHP/Symfony), Fork se7enxweb/layouts-core. | Layout-Page-Builder unter PHP 8.4: Getter-Shims lassen Twig die Parameter lesen. | 1 / 450 |
| [ibexa-recipes](oekosystem/ibexa-recipes.md) | Symfony-Flex-Rezepte der Plattform-Pakete. | Flex konfiguriert Bundles bei composer require. | 1 / 142 |

## Framework-Forks

| Repository | Was es ist | Was Anwender davon haben | Änderungen (eigene / gesamt) |
|---|---|---|---|
| [symfony](oekosystem/symfony.md) | Fork des Frameworks Symfony 3.4, aufgeteilt in se7enxweb/symfony. | Rückgabetypen für PHP 8, Nullable-Typen, Korrekturen für Closures und Reflection. | 18 / 18 |
| [twig](oekosystem/twig.md) | Fork von Twig 2.x (ersetzt twig/twig). | Templates werden unter PHP 8.4 und 8.5 ohne Deprecation-Meldungen dargestellt. | 9 / 15 |
| [DoctrineBundle](oekosystem/DoctrineBundle.md) | Fork des Symfony DoctrineBundle 1.5. | Der alte Stack lässt sich auf aktuellem PHP installieren. | 2 / 2 |
| [PHP-Parser](oekosystem/PHP-Parser.md) | Fork der Linie 0.9 von PHP-Parser (ersetzt nikic/php-parser 0.9.5). | Alte Werkzeuge können PHP-8-Code parsen. | 2 / 2 |
| [doctrine-dbal-schema](oekosystem/doctrine-dbal-schema.md) | Schicht für den Schema-Import über mehrere Datenbanksysteme, die der Installer verwendet. | Schema-Import auf aktuellem PHP; der Installer des Kernels 3.x hängt davon ab. | 2 / 2 |

## Datenbankverwaltung

| Repository | Was es ist | Was Anwender davon haben | Änderungen (eigene / gesamt) |
|---|---|---|---|
| [adminneo](oekosystem/adminneo.md) | AdminNeo und EditorNeo, ein Werkzeug zur Datenbankverwaltung in einer einzigen Datei (Fork von Adminer). | Tabellen durchsehen, SQL ausführen, exportieren und importieren für MySQL, MariaDB, PostgreSQL, SQLite, MS SQL, Oracle und MongoDB. | 0 / 1121 |

## Nach Monat

- **2018:** [11](oekosystem/monate/2018-11.md)
- **2021:** [03](oekosystem/monate/2021-03.md), [04](oekosystem/monate/2021-04.md), [05](oekosystem/monate/2021-05.md), [06](oekosystem/monate/2021-06.md), [08](oekosystem/monate/2021-08.md), [09](oekosystem/monate/2021-09.md), [10](oekosystem/monate/2021-10.md), [11](oekosystem/monate/2021-11.md)
- **2022:** [02](oekosystem/monate/2022-02.md), [03](oekosystem/monate/2022-03.md), [07](oekosystem/monate/2022-07.md), [10](oekosystem/monate/2022-10.md), [11](oekosystem/monate/2022-11.md)
- **2023:** [05](oekosystem/monate/2023-05.md), [06](oekosystem/monate/2023-06.md), [07](oekosystem/monate/2023-07.md), [08](oekosystem/monate/2023-08.md), [09](oekosystem/monate/2023-09.md), [10](oekosystem/monate/2023-10.md), [11](oekosystem/monate/2023-11.md), [12](oekosystem/monate/2023-12.md)
- **2024:** [01](oekosystem/monate/2024-01.md), [02](oekosystem/monate/2024-02.md), [03](oekosystem/monate/2024-03.md), [04](oekosystem/monate/2024-04.md), [05](oekosystem/monate/2024-05.md), [06](oekosystem/monate/2024-06.md), [07](oekosystem/monate/2024-07.md), [08](oekosystem/monate/2024-08.md), [09](oekosystem/monate/2024-09.md), [10](oekosystem/monate/2024-10.md), [11](oekosystem/monate/2024-11.md), [12](oekosystem/monate/2024-12.md)
- **2025:** [01](oekosystem/monate/2025-01.md), [02](oekosystem/monate/2025-02.md), [03](oekosystem/monate/2025-03.md), [04](oekosystem/monate/2025-04.md), [05](oekosystem/monate/2025-05.md), [06](oekosystem/monate/2025-06.md), [07](oekosystem/monate/2025-07.md), [08](oekosystem/monate/2025-08.md), [09](oekosystem/monate/2025-09.md), [10](oekosystem/monate/2025-10.md), [11](oekosystem/monate/2025-11.md), [12](oekosystem/monate/2025-12.md)
- **2026:** [01](oekosystem/monate/2026-01.md), [02](oekosystem/monate/2026-02.md), [03](oekosystem/monate/2026-03.md), [04](oekosystem/monate/2026-04.md), [05](oekosystem/monate/2026-05.md), [06](oekosystem/monate/2026-06.md), [07](oekosystem/monate/2026-07.md), [09](oekosystem/monate/2026-09.md)

## Hinweise zu den Zahlen

- `admin-ui-7x` und `admin-ui-ibexa` sind dasselbe Repository unter zwei Namen von Remotes; ihre Hashes sind identisch.
- `exponential` teilt jeden Hash mit dem Repository der Hauptinstallation; die Erzählung dazu steht in der Hauptchronik.
- Merge-Commits, Finanzierungsmetadaten und Spracheinstellungen von Repositorys sind als ohne Nutzen für Anwender eingeordnet; jede solche Zeile nennt in der Tabelle zur Abdeckung ihre Begründung.
- Produktnamen von Upstream-Projekten (Ibexa, Netgen, eZ Platform) erscheinen nur dort, wo ein Paket oder Repository wirklich so heißt.

## Verwandte Seiten

- [Vollständige Änderungsverzeichnisse](aenderungsverzeichnis/README.md)
- [Paketübersicht der Plattform](../spezifikationen/6.0/platform-paketzuordnung.md) und [Konsolenbefehle](../spezifikationen/6.0/platform-konsolenbefehle.md)
- [Paket-Forks und umbenannte Befehle](../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Administrationsoberfläche](../../eng-us/features/6.0/platform-admin-ui-fork.md), [Nexus-Starter](../../eng-us/features/6.0/platform-nexus-starter.md), [layouts-core](../../eng-us/features/6.0/platform-layouts-core-fork.md), [Framework-Forks](../../eng-us/features/6.0/platform-php85-framework-forks.md), [Site-Bundles](../../eng-us/features/6.0/platform-site-bundles.md)
- [Release-Changelogs der Pakete](../../changelogs/extensions/README.md)
