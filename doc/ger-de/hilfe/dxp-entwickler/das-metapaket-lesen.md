---
title: Das Metapaket lesen
audience: dxp-developer
category: dxp-development
order: 5
summary: Ein Gang durch die composer.json von se7enxweb/exponential-platform-dxp und des Skeletons - wofür jede Anforderung da ist, die Konflikte, das Lizenzfeld, die Rezept-Endpunkte von Flex und die Mindeststabilität -, damit Sie wissen, was ein DXP-Projekt wirklich installiert.
keywords: Metapaket, composer.json, se7enxweb/exponential-platform-dxp, Skeleton, require, conflict, Symfony Flex, Rezepte, minimum-stability, branch-alias, Pakete, metapackage, skeleton, recipes, packages
type: help
---
# Das Metapaket lesen

Diese Seite richtet sich an Entwickler, die genau wissen möchten, was ein DXP-Projekt installiert und warum. Sie geht
die beiden `composer.json`-Dateien durch, die ein Projekt festlegen: das Metapaket `se7enxweb/exponential-platform-dxp`
und das Skeleton `se7enxweb/exponential-platform-dxp-skeleton`.

## Inhalt

1. [Zwei Dateien, zwei Aufgaben](#zwei-dateien-zwei-aufgaben)
2. [Das Metapaket, Abschnitt für Abschnitt](#das-metapaket-abschnitt-für-abschnitt)
3. [Das Skeleton, Abschnitt für Abschnitt](#das-skeleton-abschnitt-für-abschnitt)
4. [Fragen, die die Dateien beantworten](#fragen-die-die-dateien-beantworten)
5. [Sie im eigenen Projekt lesen](#sie-im-eigenen-projekt-lesen)

## Zwei Dateien, zwei Aufgaben

| Datei | Pakettyp | Aufgabe |
|---|---|---|
| `se7enxweb/exponential-platform-dxp` | `metapackage` | Listet jedes Paket der DXP. Es hat keinen eigenen Code. |
| `se7enxweb/exponential-platform-dxp-skeleton` | `project` | Der Ausgangspunkt Ihres Projekts: verlangt das Metapaket, richtet Flex, Autoloading und Skripte ein. Nach `create-project` ist es die `composer.json` Ihres Projekts. |

## Das Metapaket, Abschnitt für Abschnitt

**`require`.** PHP `>=8.3`, `ext-ctype` und `ext-iconv`, dann vier Gruppen:

- die Inhaltspakete (se7enxweb-Forks und Upstream-Pakete `ibexa/*`), gruppiert unter
  [Was die DXP mitliefert](was-die-dxp-mitliefert-und-was-nicht.md#was-sie-mitliefert);
- Komponenten und Bundles von Symfony 7.4 (`symfony/framework-bundle`, `symfony/security-bundle`, `symfony/form`,
  `symfony/mailer`, `symfony/twig-bundle`, `symfony/webpack-encore-bundle` und andere);
- Doctrine (`doctrine/doctrine-bundle`, `doctrine/doctrine-migrations-bundle`);
- Werkzeug-Bundles (`lexik/jwt-authentication-bundle`, `overblog/graphiql-bundle`, `knplabs/knp-menu-bundle`,
  `friendsofsymfony/jsrouting-bundle`, `monolog/monolog`, `twig/extra-bundle`).

Mehrere Inhaltspakete werden in einem Entwicklungszweig verlangt (zum Beispiel `~5.0.x-dev`); die se7enxweb-Forks
werden in getaggten Versionen verlangt, wo es eine gibt (`se7enxweb/admin-ui` `^5.0.5.0`,
`se7enxweb/exponential-platform-dxp-core` `^5.0.1.0`).

**`conflict`.** Bekannte fehlerhafte Versionen, die Composer nie installieren darf, zum Beispiel
`doctrine/doctrine-bundle` `2.6.3`, `lexik/jwt-authentication-bundle` `2.12.0 || 2.18.0` und
`symfony/webpack-encore-bundle` `1.14.0`. Entfernen Sie sie in Ihrem Projekt nicht.

**`license`.** `(GPL-2.0-or-later or proprietary)` ([Lizenzierung](lizenzierung.md)).

**`extra.branch-alias`.** `dev-master` ist als Alias von `1.3.x-dev` gesetzt.

## Das Skeleton, Abschnitt für Abschnitt

**`require`.** PHP `>=8.3`, die Erweiterungen `ctype`, `iconv`, `gd`, `curl`, `json`, `xsl`, `xml`, `intl`,
`mbstring`, dann `se7enxweb/exponential-platform-dxp` in `dev-master`, `symfony/flex`, `symfony/runtime` und
`nyholm/psr7`. **`require-dev`** ergänzt das Debug-Bundle, Stopwatch und den Web Profiler von Symfony.

**`config.allow-plugins`.** Nur `ibexa/post-install`, `symfony/flex` und `symfony/runtime` dürfen als
Composer-Plugins laufen; jedes andere Plugin wird abgelehnt (`"*": false`). Halten Sie diese Liste in Ihrem Projekt
kurz.

**`autoload`.** Ihr Code liegt im Namensraum `App\` in `src/`, Tests in `App\Tests\` in `tests/`.

**`scripts`.** Nach Installation und Aktualisierung laufen `cache:clear` und `assets:install --symlink --relative`
automatisch.

**`extra.symfony.endpoint`.** Flex liest Rezepte aus dem Index `ibexa/recipes`, aus `se7enxweb/sevenx-recipes` und aus
den Standards von Flex. Die Rezepte schreiben Ihre Konfiguration unter `config/`
([sevenx-recipes](../../funktionen/6.0/erweiterungen/sevenx-recipes.md)).

**`minimum-stability` und `prefer-stable`.** Das Skeleton setzt `minimum-stability` auf `dev` und `prefer-stable` auf
`false`, weil mehrere Pakete in Entwicklungszweigen verlangt werden. Entscheiden Sie vor einem Produktions-Release,
welche Pakete Sie in Ihrer eigenen `composer.json` auf Tags festlegen, und committen Sie `composer.lock`
([Aktualisieren](dxp-aktualisieren.md)).

**`conflict`.** `symfony/symfony` steht in Konflikt: Installieren Sie Symfony als einzelne Komponenten, nie als Monolith.

## Fragen, die die Dateien beantworten

| Frage | Wo nachsehen |
|---|---|
| Ist GraphQL enthalten? | `ibexa/graphql` im Metapaket: ja |
| Ist ein Layout-Editor enthalten? | Nur `se7enxweb/layouts-core`: kein Editor |
| Welche PHP-Version? | `"php": ">=8.3"` in beiden Dateien |
| Welche Symfony-Version? | Die Anforderungen `symfony/*`: `^7.4` |
| Warum lehnt Composer eine Version ab? | Der Abschnitt `conflict` |

## Sie im eigenen Projekt lesen

```bash
composer show se7enxweb/exponential-platform-dxp      # version installed and its requirements
composer depends ibexa/rest                           # who requires a package
composer why-not php 8.2                              # why an older PHP is refused
composer outdated "se7enxweb/*"                       # newer releases of the forks
```

## Verwandte Seiten

- [Die se7enxweb-Forks und `replace`](die-se7enxweb-forks-und-replace.md)
- [Übersicht der Plattformpakete](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Release Notes von exponential-platform-dxp](../../../changelogs/extensions/exponential-platform-dxp.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
