# PHP 8.4 und 8.5 für den älteren Symfony-Stack: die Framework-Forks

Diese Seite ist für Teams, die ein Projekt mit Exponential Platform 1.x / 2.5 oder Nexus 1.x auf einem aktuellen PHP
am Leben halten. Für den Kernel von Exponential 6 selbst siehe
[Unterstützung für PHP 8 im Kernel von Exponential 6](../../bc/6.0/php8.md).

**Repositorys:** `se7enxweb/symfony`, `se7enxweb/twig`, `se7enxweb/doctrine-bundle` (Repository `DoctrineBundle`),
`se7enxweb/doctrine-dbal-schema`, `se7enxweb/php-parser` (Repository `PHP-Parser`) sowie die Plattformpakete
`ezpublish-kernel`, `ezplatform-kernel`, `ezplatform-http-cache`, `ezplatform-user`, `ezplatform-richtext`, die
PHP-Bedingungen erhielten.

## Was es ist

Exponential Platform 1.x / 2.5 und Nexus 1.x laufen auf der Generation Symfony 3.4 des Frameworks. Upstream hörte lange vor dem Erscheinen von PHP 8.4 und 8.5 auf, sie zu pflegen, sodass diese Stacks auf einem aktuellen PHP mit fatalen Fehlern abbrechen oder das Protokoll mit Deprecations fluten. Die hier aufgeführten Forks sind kleine, gezielte Patches, die den alten Stack auf PHP 8.2 bis 8.5 lauffähig machen. Sie werden automatisch installiert: Jeder Fork deklariert das Upstream-Paket unter `replace`, sodass Composer den Fork überall verwendet, wo der Upstream-Name verlangt wird.

## Warum es hilft

- Sie können eine ältere Website mit Exponential Platform auf einer unterstützten PHP-Version betreiben, statt auf einer nicht unterstützten zu bleiben.
- Ihre Protokolle zeigen Ihre eigenen Warnungen, nicht Tausende von Deprecations des Frameworks.
- Keine Änderung an Ihrem Projektcode: Der Fork hat dieselben Namensräume und dieselbe API.

## Was jeder Fork behebt

### `se7enxweb/symfony` (Monolith Symfony 3.4)

| Datum | Änderung | Wirkung |
|---|---|---|
| 24.08.2025 | `ErrorHandler`, `LazyLoadingValueHolderGenerator`, `ArrayNode`, `ExceptionCaster` für PHP 8.2 / 8.4 angepasst | der Debug-Fehler-Handler, Lazy Services, Konfigurations-Arrays und die Ausgabe von Ausnahmen laufen auf PHP 8.x |
| 28.01.2026 | Rückgabetypen an der VarDumper-Klasse `Data`; `WebProfilerExtension` verwendet eine Methodenreferenz statt einer Closure | `dump()` und die Profiler-Werkzeugleiste funktionieren auf PHP 8 |
| 29.01.2026 | ausdrückliche nullbare Typen und eine Korrektur einer Deprecation von `ReflectionProperty` in `ErrorHandler` | keine Deprecation-Hinweise vom Handler |
| 30.01.2026 | `composer.json` verlangt `se7enxweb/twig` statt `twig/twig` | ein Twig für den ganzen Stack |
| 10.02.2026 | Deprecations von PHP 8.5 in `Container` und `Yaml\Inline` | sauberer Container und sauberes YAML-Parsen unter 8.5 |
| 14.02.2026 | Änderungen am Datensammler des Profilers und an `Kernel`, die Nexus braucht: Deprecation-Statistiken werden schonender erzeugt und gezeigt | die Werkzeugleiste einer Standardinstallation von Nexus scheitert nicht an Deprecations |
| 09.04.2026 | `NativeFileSessionHandler` überspringt seine Aufrufe von `ini_set()`, wenn bereits eine Sitzung aktiv ist oder Header gesendet wurden | `cache:clear` und andere CLI-Befehle stürzen nicht mehr mit „An unexpected error has occurred“ ab, wenn der Legacy-Kernel eine Sitzung gestartet hat, bevor der Handler gebaut wird |

### `se7enxweb/twig` (Twig 2.x)

| Datum | Änderung | Wirkung |
|---|---|---|
| 24.08.2025 | Korrektur an `NameExpression` für PHP 8.2+ | Templates kompilieren auf PHP 8.2 und neuer |
| 30.01.2026 | Paket ersetzt `twig/twig`; Bedingung und Version auf 2.16.2 angehoben | Composer installiert den Fork |
| 09.04.2026 | `CallExpression::reflectCallable()` erkennt die Form von Closure-Namen in PHP 8.5 `{closure:Class::method():N}` | der Compiler erzeugt keinen ungültigen dynamischen Methodenaufruf für Closures mehr; als Closures definierte Template-Funktionen funktionieren unter PHP 8.5 |
| 11.05.2026 | ausdrückliche nullbare Parametertypen in 36 Dateien (Node, Error, Sandbox, Extension, Loader, Template, Parser) | keine Deprecation „implicitly nullable parameter“ unter PHP 8.4 und 8.5 |

### Kleinere Forks

| Fork | Was sich geändert hat |
|---|---|
| `se7enxweb/doctrine-bundle` (Linie 1.5) | Anbietername ersetzt, `replace` von `doctrine/doctrine-bundle` und die Regel für die Twig-Anforderung (30.01.2026) |
| `se7enxweb/doctrine-dbal-schema` | Unterstützung für PHP 8.1 (01.07.2025), Schicht `replace` für den Upstream-Namen (12.04.2026) |
| `se7enxweb/php-parser` (Linie 0.9) | Kompatibilität mit PHP-8-Tokens: Tokens qualifizierter Namen werden wieder in die Folgen zerlegt, die die alte Grammatik erwartet; `match`, `enum`, `readonly` werden auf Bezeichner abgebildet; der Nullsafe-Operator wird auf `->` abgebildet; Attribute sowie Rückgabetypen und Nullbarkeitsmarkierungen ab PHP 7.0 werden entfernt (11.05.2026), dazu `replace` von `nikic/php-parser` 0.9.5 |
| `se7enxweb/ezpublish-kernel` | Unterstützung für PHP 8.2 / 8.3, auf Closures beruhende Twig-Funktionen für PHP 8.5 durch benannte Methoden ersetzt (30.01.2026), fehlende Blöcke der Cache-Konfiguration (09.02.2026, 10.02.2026) |

## Prüfen, was installiert ist

```bash
composer show -i | grep -E "se7enxweb/(symfony|twig|doctrine-bundle|php-parser)"
composer why twig/twig          # shows the fork that satisfies the upstream name
php -v
```

(Nur lesende Befehle; sie ändern nichts.)

## Grenzen

- Das sind Patches, um einen alten Stack am Leben zu halten, keine Aktualisierung von Symfony. Neue Projekte sollten auf [Platform v5](plattform-dxp-skeleton.md) beginnen (Symfony 7.4, PHP 8.3+).
- `se7enxweb/twig` folgt der Linie Twig 2.16; der Stack v5 verwendet Twig 3.x von Upstream.

## Verwandte Seiten

- [Unterstützung für PHP 8 im Kernel von Exponential 6](../../bc/6.0/php8.md)
- Tests auf PHP 8: [PHPUnit 10](../../bc/6.0/phpunitv10.md), [PHPUnit 13](../../bc/6.0/phpunitv13.md), [Aufräumen der Testsuite für PHPUnit 13 / PHP 8.4.23](../../bc/6.0/phpunitv13-fuer-php841.md), [PHP-8-Korrekturen und Tests für `ezpSessionHandlerDB`](../../bc/6.0/ezpsessionhandlerdb-php8-korrektur-und-tests.md)
- Plattformfunktionen: [Nexus-Starter](plattform-nexus-starter.md), [DXP-Skeleton](plattform-dxp-skeleton.md), [Administrationsoberfläche der Plattform](plattform-admin-ui-fork.md), [Layouts auf der Plattform](plattform-layouts-core-fork.md), [Site-Bundles](plattform-site-bundles.md), [SQLite für Exponential Platform](plattform-sqlite-installation.md), [Legacy Bridge](legacy-bridge.md), [Datenbankverwaltung AdminNeo](adminneo-datenbankverwaltung.md)
- Spezifikationen: [Paketkarte der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md), [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md), [SQLite-Installer der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md), [Bundle der Legacy Bridge](../../spezifikationen/6.0/legacy-bridge-bundle.md)
- Hinweise zur Aktualisierung: [Paket-Forks und Umbenennungen von Befehlen](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Chronik: [symfony](../../geschichte/oekosystem/symfony.md), [twig](../../geschichte/oekosystem/twig.md), [DoctrineBundle](../../geschichte/oekosystem/DoctrineBundle.md), [doctrine-dbal-schema](../../geschichte/oekosystem/doctrine-dbal-schema.md), [PHP-Parser](../../geschichte/oekosystem/PHP-Parser.md), [ezpublish-kernel](../../geschichte/oekosystem/ezpublish-kernel.md), [Übersicht über das Ökosystem](../../geschichte/oekosystem.md), [Monate des Ökosystems](../../geschichte/oekosystem/monate/2026-04.md), [Änderungsverzeichnis](../../geschichte/aenderungsverzeichnis/README.md), [April 2025](../../geschichte/2025/2025-04.md), [September 2025](../../geschichte/2025/2025-09.md), [Dezember 2025](../../geschichte/2025/2025-12.md), [Februar 2026](../../geschichte/2026/2026-02.md), [März 2026](../../geschichte/2026/2026-03.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
