# ibexa-recipes: Geschichte des Plattform-Repositorys

Die Geschichte von `ibexa-recipes`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Layouts und Rezepte). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 142 Änderungen von 2023-12-14 bis 2026-03-02: 1 vom se7enxweb-Team und 141 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Symfony-Flex-Rezepte der Plattform-Pakete.

## Wie es mit Exponential zusammenhängt

Spiegel des Upstream-Rezept-Repositorys; der Endpunkt von se7enxweb ist sevenx-recipes.

## Was Anwender davon haben

Flex konfiguriert Bundles bei composer require.

## Sicherheits-, Leistungs- und Upgrade-Änderungen in der Geschichte

Diese Änderungen sind im Abdeckungsnachweis eingeordnet und werden sonst nirgends auf dieser Seite genannt. Die meisten sind Upstream-Geschichte, die der Fork mitführt; Datum und Betreffzeile des Commits sind der Eintrag im Änderungsverzeichnis (siehe das [vollständige Änderungsverzeichnis](../aenderungsverzeichnis/README.md)). Die Betreffzeile sagt, was sich geändert hat; die genaue Wirkung zeigt der Commit im Repository.

### Sicherheit (5)

- 2024-05-23 `cf3ce7f` IBX-8140: Enabled authenticator manager-based security (#118)
- 2024-08-14 `8a7c7ef` IBX-8656: Reworked OAuth2 client authentication to comply with new Symfony security (#133)
- 2024-08-20 `1a4333f` IBX-8657: Reworked CDP security layer to not rely on deprecated guard authenticators (#136)
- 2024-08-21 `8b9ed28` IBX-8691: Simplified security config for OAuth2 server usages (#135)
- 2025-05-29 `3ec1063` IBX-9944: Add support for stateless CSRF protection in admin-ui (#183)

<!-- rev2-listed-rows:end -->

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 67 |
| Fehlerbehebungen | 12 |
| Verhaltens- und Upgrade-Änderungen | 2 |
| Sicherheit | 5 |
| Leistung | 1 |
| Dokumentation | 1 |
| Werkzeuge | 20 |
| Ohne Nutzen für Anwender | 34 |

Außerdem: 1 Merge- oder Finanzierungs-Commit des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 5 | 3 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 0 | `678cde6` IBX-7332: Updated graphic request in Welcome Page: Content and Commerce (#101); `12d5fae` IBX-6645: As the User I want to change my data and avatar in User profile (#99) |
| 2024-01 | 5 | 2 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `c601a78` IBX-7525: UDW as npm package (#106); `e77012d` IBX-7242: Excluded "Product categories", "Tags" and "Corporate account" locations from site context (#105) |
| 2024-02 | 4 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 3 | `bc4c912` IBX-7611: Bump version for webpack and sass-loader (#108); `e8417d6` [ibexa/docker] Set the default image to php:8.2-node18 (#109) |
| 2024-03 | 2 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `841db1b` [ALL] Added recipes for 5.0 (#111); `5cd134c` IBX-3740: Fixed resolving `ELASTICSEARCH_DSN` variable for Platform.sh |
| 2024-04 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `29c5275` IBX-7687: Add editor content icon to configuration above OSS (#116) |
| 2024-05 | 8 | 4 | 0 | 0 | 1 | 0 | 0 | 2 | 0 | 1 | `e2314fb` [All] Added app-switcher and headless-assets bundles (#115); `bbc8fa4` IBX-8144: [Rebranding] Remove compatibility layer from webpack encore (#117) |
| 2024-06 | 3 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `53e9df8` IBX-8290: Reworked REST authentication to comply with the new Symfony authenticator mechanism under separate firewall (#121); `bf4c8ff` IBX-8356: Reworked JWT firewall to be in-tact with the new Symfony auth (#122) |
| 2024-07 | 13 | 7 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 4 | `3fd2560` IBX-8150: Added enabled attributes types configuration (#123); `aa3a512` IBX-8356: Reworked JWT GraphQL firewall configuration to comply with Symfony-based authentication (#124) |
| 2024-08 | 7 | 3 | 0 | 0 | 3 | 0 | 0 | 0 | 0 | 1 | `564c4ef` IBX-8137: [ibexa/*] Dropped Swiftmailer bundle (#139); `f28dc8c` IBX-8817: Documented `http_basic` authentication config (#138) |
| 2024-09 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `fbfc208` IBX-8775: Added recipe for ibexa/product-catalog-symbol-attribute (#137); `8c7ee96` IBX-8784: Added ibexa/connector-qualifio REST routes |
| 2024-10 | 5 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `36e2220` Enabled ibexa/core-search (#143); `3460a12` Added recipe for ibexa/discounts package (#144) |
| 2024-11 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `c136fb8` IBX-9094: Added recipe for ibexa/product-catalog-date-time-attribute (#147) |
| 2024-12 | 3 | 0 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `a317c26` IBX-9109: Enabling TypeScript (ts-loader) with Webpack Encore (#145); `c22e2bd` IBX-8732: Fixed ibexa/collaboration recipe (#148) |
| 2025-01 | 3 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `0516c1b` IBX-9419: Updated ts-config tag (#151); `148032b` Updated copyright year to 2025 |
| 2025-02 | 6 | 3 | 1 | 0 | 0 | 0 | 0 | 2 | 0 | 0 | `6b54c91` IBX-8470: Upgraded codebase to Symfony 6 (#150); `dee2f4d` [ibexa/test-fixtures] Fixed configuration recipes (#160) |
| 2025-03 | 13 | 4 | 1 | 1 | 0 | 0 | 0 | 2 | 0 | 5 | `0e7fcc3` IBX-9584: Resolved Symfony 6.x deprecations vol. 2 (#159); `a281298` Added `IbexaDiscountsCodes` bundle to recipes |
| 2025-04 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `d5dac94` IBX-9339: Added recipe for ibexa/fieldtype-richtext-rte (#149); `19ebaae` IBX-8552: Changed customer registration group id to remote id (#165) |
| 2025-05 | 11 | 3 | 0 | 1 | 1 | 0 | 0 | 5 | 0 | 1 | `c88b6e8` IBX-9940: Removed Sass deprecations (#177); `a32d816` IBX-9939: TypeScript aliases for bundles (#171) |
| 2025-06 | 7 | 6 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `d811f15` IBX-7845: Changed icons set to IDS Icons (#182); `1538eba` Allowed to install `ibexa/personalization` as opt-in package for 5.0 ( |
| 2025-07 | 23 | 12 | 4 | 0 | 0 | 1 | 1 | 2 | 0 | 3 | `678d3e2` IBX-10173: Extract webpack files from root dir (#191); `339cbc9` IBX-10331: Fixed memory leak while compiling assets (#200) |
| 2025-08 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `9ff3d90` [CS] Fixed indent in webpack.config.js (#213); `5eb7667` IBX-9353: Added default mailer configuration with global sender (4.6) (#214) |
| 2025-09 | 6 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `a00b6c4` IBX-10640: Changed order of assets compilation (#218); `af0aaea` IBX-10525: Added shareable_link firewall (#215) |
| 2025-10 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 |  |

## Verwandte Seiten

- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ibexa-recipes.md)
- [Fork von layouts-core](../../../eng-us/features/6.0/platform-layouts-core-fork.md)
- Plattform-Ökosystem nach Monat: [2023-12](monate/2023-12.md), [2024-01](monate/2024-01.md), [2024-02](monate/2024-02.md), [2024-03](monate/2024-03.md), [2024-04](monate/2024-04.md), [2024-05](monate/2024-05.md), [2024-06](monate/2024-06.md), [2024-07](monate/2024-07.md), [2024-08](monate/2024-08.md), [2024-09](monate/2024-09.md), [2024-10](monate/2024-10.md), [2024-11](monate/2024-11.md), [2024-12](monate/2024-12.md), [2025-01](monate/2025-01.md), [2025-02](monate/2025-02.md), [2025-03](monate/2025-03.md), [2025-04](monate/2025-04.md), [2025-05](monate/2025-05.md), [2025-06](monate/2025-06.md), [2025-07](monate/2025-07.md), [2025-08](monate/2025-08.md), [2025-09](monate/2025-09.md), [2025-10](monate/2025-10.md), [2026-03](monate/2026-03.md)
