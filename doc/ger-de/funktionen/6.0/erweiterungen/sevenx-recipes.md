# sevenx-recipes: Symfony-Flex-Rezepte für die Plattformpakete

Diese Seite richtet sich an Entwickler, die ein Projekt auf der Symfony-basierten Exponential-Plattform beginnen oder
pflegen. `sevenx-recipes` ist das **Symfony-Flex-Rezept-Repository** der Plattformpakete von se7enxweb. Wenn
`composer require` ein Paket installiert oder aktualisiert, liest Flex die `index.json` dieses Repositorys, schlägt die
registrierten Rezeptversionen des Pakets nach und kopiert die Dateien und die Konfiguration des passenden
**Rezept-Slots** in das Projekt: Konfiguration, den Front-Controller, das Setup für den Web-Build, die
Legacy-Einstellungen und die Installationsskripte.

Warum das hilft: Ein neues Projekt erhält die funktionierende Konfiguration der Exponential-Plattform (Sicherheit,
Siteaccess, Legacy-Einstellungen, den Web-Build, das Skript für die Legacy-Links) mit einem einzigen `composer require`,
statt sie von Hand aus einem Referenzprojekt zu kopieren.

Es gehört zur Plattformseite (Symfony und Ibexa) des Projekts; siehe die
[Übersicht über das Ökosystem](../../../geschichte/oekosystem.md). Die Aliasse `exponential-dxp`,
`exponential-platform-dxp` und `sevenx-dxp` bedeuten alle `se7enxweb/exponential-platform-dxp`.

## In einem Projekt verwenden

1. Fügen Sie das Repository als Flex-Endpunkt neben dem Upstream-Endpunkt in den Block `extra.symfony` der
   `composer.json` des Projekts ein. Die laufenden Plattform-Websites dieser Installation machen es so. Die Reihenfolge
   ist wichtig: Die Endpunkte werden nacheinander abgefragt.

```json
"extra": {
    "symfony": {
        "allow-contrib": true,
        "endpoint": [
            "https://api.github.com/repos/ibexa/recipes/contents/index.json?ref=flex/main",
            "https://api.github.com/repos/se7enxweb/sevenx-recipes/contents/index.json?ref=master",
            "flex://defaults"
        ]
    }
}
```

   Die dritte Endpunktzeile ist der Flex-Standard und eine Annahme dieser Seite. Prüfen Sie sie in Ihrem Projekt mit
   `composer config extra.symfony.endpoint`.

2. Fordern Sie das Plattformpaket an; Flex kopiert die Dateien des Rezepts:

```bash
composer require se7enxweb/exponential-platform-dxp
```

3. Sehen Sie mit `composer recipes se7enxweb/exponential-platform-dxp` nach, was installiert ist, und wenden Sie ein
   geändertes Rezept mit `composer recipes:update <package>` erneut an.

Die Version, die das Projekt verlangt, bestimmt den Slot. Zum Beispiel wird `dev-5.x-LB` des Plattformpakets über
seinen `branch-alias` zu `1.4` aufgelöst. Die Zuordnung von Branches zu Slots steht in der `MAINTENANCE.md` des
Repositorys: `4.6` zu `1.0`, `4.6.x-LB` zu `1.2`, `master` zu `1.3`, `5.x-LB` zu `1.4`.

**Wie Flex den Slot auswählt:** Eine `dev-`-Version mit passendem `extra.branch-alias` übernimmt den Alias (zum Beispiel
wird `dev-5.x-LB` zu `1.4.x-dev`). Das Ergebnis wird auf `MAJOR.MINOR` reduziert, und die registrierten Slots werden in
absteigender Reihenfolge durchlaufen; der erste, der nicht über dieser Version liegt, gewinnt. `MAINTENANCE.md` enthält
die Branch-Alias-Zuordnung und eine Tabelle dessen, was jeder Slot enthält. Löschen Sie die historischen Slots nicht:
Ältere Projekte sind auf sie festgelegt.

## Rezepte und Slots

| Paket | Slots (März bis April 2026) |
|---|---|
| `se7enxweb/exponential-platform-dxp` | `1.0` (Ibexa DXP 4.6 pur), `1.2` (4.6 mit der Legacy Bridge), `1.3` (Ibexa DXP 5.x pur), `1.4` (5.x mit der Legacy Bridge), dazu die historischen `4.6.x-dev` und `5.0` |
| `se7enxweb/exponential-platform-dxp-skeleton` | `dev-master` (das Verzeichnis und sein `.json`-Manifest existieren, aber **es ist in der `index.json` nicht unter `recipes` aufgeführt**, daher liefert Flex es nicht aus) |
| `se7enxweb/site-bundle` | `3.1` |
| `se7enxweb/tagsbundle` | `5.0` |
| `se7enxweb/ezplatform-solr-search-engine` | `3.3` |
| `se7enxweb/doctrine-dbal-schema` | `1.0` |

Prüfen Sie die Tabelle jederzeit am Code: Öffnen Sie die `index.json` in einem Klon des Repositorys und lesen Sie das
Objekt `recipes`. Die Slot-Verzeichnisse sind `se7enxweb/<package>/<slot>/`, jedes mit einer `manifest.json`; die
abgeflachten Dateien `<package>.<slot>.json` im Wurzelverzeichnis sind das, worauf `recipe_template` verweist. Das
Verzeichnis `4.6.x-LB-dev` existiert ebenfalls, ist aber auch nicht registriert.

## Was ein Slot installiert (geprüft in den Slots `1.2` und `1.4`)

| Ordner oder Datei im Slot | Was das Projekt erhält |
|---|---|
| `config/packages/*.yaml`, `config/routes/*.yaml`, `config/services.yaml` | Konfiguration der Plattform-Bundles (Sicherheit, Caches, Assets, HTTP-Cache, GraphQL, Solr, JWT), die Routen (Admin-UI, REST, Suche, Benutzer, Layouts, Tags, Informationssammlung) und `ez_publish_legacy.yaml`, die Parameter der Legacy Bridge |
| `public/` | Die Front-Controller `index.php`, `index_cluster.php` (Dateiauslieferung im Cluster) und `index_rest.php` (der Legacy-REST-Einstieg), `.htaccess`, `favicon.ico`, `favicon.png` |
| `src/EventListener/LegacyRequestListener.php`, `LegacyRestListener.php` | Anfragebehandlung für den Legacy-Kernel und seine REST-Aufrufe |
| `src/EventSubscriber/LegacyInjectedSettingsSubscriber.php` | Injiziert beim Start INI-Einstellungen in den Legacy-Kernel, siehe unten |
| `src/LegacySettings/` | INI-Dateien für die Legacy-Siteaccesses `legacy_site`, `legacy_admin`, `ngadminui` und `site` sowie eine globale `override/site.ini.append.php` |
| `src/LegacyRoot/var/site/storage/` | Ein leerer Storage-Ordner für das Legacy-Verzeichnis `var` |
| `src/Installer/ExponentialOssInstaller.php` | Die Installer-Klasse von `exponential:install`; eine Änderung am 14. April hat sie auf die einfache leere Form zurückgeführt |
| `encore/` | Das Setup für den Web-Build (`webpack.config.js`, `ibexa.webpack.config.manager.js`, `package.json`, `ibexa.tsconfig.json`; `tsconfig.json` ab `1.4`) |
| `bin/install-legacy-links` | Ein Skript, das die Symlinks anlegt, die den Legacy-Baum mit dem Projekt verbinden (die eigene Extension `app`, den globalen Override-Ordner, die Einstellungen des Siteaccess `legacy_admin` und die Delegationen von `ngadminui`); Composer führt es bei Installation und Aktualisierung aus |

`LegacyInjectedSettingsSubscriber` injiziert INI-Einstellungen, global oder pro Siteaccess, aus den Container-Parametern
`app.legacy.injected_settings`, `app.legacy.injected_merge_settings`, `app.legacy.siteaccess_injected_settings` und
`app.legacy.siteaccess_injected_merge_settings` in `config/packages/ez_publish_legacy.yaml`. Der Subscriber und der
Siteaccess `legacy_site` kamen am 4. April 2026 hinzu; seit dem 5. April wird der Siteaccess `legacy_admin` aus dem
Anfrageattribut aufgelöst (eine Fehlerkorrektur); die Listener und `index_rest.php` kamen am 6. April hinzu; der
SiteAccess-Namespace wechselte am 13. April von `eZ` zu `Ibexa`.

Um genau zu sehen, was ein Slot enthält, bevor Sie das Paket anfordern, in einem Klon:

```bash
cat se7enxweb/exponential-platform-dxp/1.4/manifest.json    # files and where each is copied
find se7enxweb/exponential-platform-dxp/1.4 -type f | sort
```

Die Slots unterscheiden sich in den Details, die ein Projekt festlegen muss: `1.0` und `1.2` sind die Slots für
Ibexa 4.6, `1.3` und `1.4` die Slots für Ibexa 5 (siehe `MAINTENANCE.md`). `1.3` hat keine
`config/packages/ez_publish_legacy.yaml`, keine `LegacySettings` und kein `bin/install-legacy-links`.

## Grenzen

`MAINTENANCE.md` beschreibt einen Helfer `php bin/build-recipe-manifest` zum Neuerzeugen eines Slot-Manifests, aber das
Repository hat in seinem Wurzelverzeichnis kein Verzeichnis `bin/`. Erledigen Sie diesen Schritt von Hand (oder mit dem
Werkzeug, das Sie neben dem Klon pflegen), bis das Skript hinzugefügt ist.

## Was sich geändert hat

- **12. März 2026:** das erste Rezept, für `exponential-platform-dxp`, und die Schlüssel der `index.json`, die Flex
  verlangt (`branch`, `recipe-conflicts`, `versions`, `is_contrib`).
- **17. bis 28. März:** Rezepte für die Forks der Netgen-Pakete (`site-bundle` und `tagsbundle`); der Produktname Ibexa
  in für Benutzer sichtbaren Templates wurde durch Logos und Marken des Projekts ersetzt; die Willkommensmeldung der
  Installation wurde umgestaltet; ein Nexus-Rezept `1.1` wurde hinzugefügt und dann zugunsten eines
  Installationsordners plus Symlink-Skript wieder entfernt.
- **4. bis 17. April:** Unterstützung für den Branch 4.6.x der Plattform:
  - Korrekturen am Web-Build (Dart Sass 1.x), Favicon und `.htaccess`, REST-API-401, fehlende Icons und das Laden der
    JavaScript-Übersetzungen;
  - eine Sicherheitskonfiguration mit dem Passwort-Hasher für die Ibexa-Benutzerklasse, Sitzungseinstellungen und
    Siteaccess der Legacy Bridge, Routen-Importe;
  - das Skript `bin/install-legacy-links`, vorhanden in jedem Slot außer `1.3` (prüfen Sie es mit
    `ls se7enxweb/exponential-platform-dxp/*/bin` in einem Klon);
  - eine Bereinigung von Dateien und Service-Definitionen der Legacy Bridge und von Netgen aus dem reinen Ibexa-5-Slot
    (`1.3` enthält keine; der Slot `1.0` enthält, obwohl er die reine 4.6-Variante ist, noch
    `bin/install-legacy-links`, `src/LegacySettings` und `src/LegacyRoot`, nehmen Sie also nicht an, dass er frei von
    der Bridge ist);
  - Kompatibilität der Konfiguration mit Symfony 7 und Ibexa 5 (`storage_factory_id` statt `storage_id`), die
    bereinigten `1.x`-Slots, Encore-Konfiguration für die React-Builds, `tsconfig.json` und `controllers.json`, der
    Installationsbefehl, der während `exponential:install` das Schema von Netgen Layouts lädt, und INI-Einstellungen
    der Legacy-Siteaccesses.
- **13. bis 17. April, kleinere Korrekturen mit für Benutzer sichtbarer Wirkung:**
  - die registrierten Rezeptversionen wurden neu geordnet, damit Flex' `version_compare` `5.0` korrekt auswählt (die
    Reihenfolge der Liste `recipes` in der `index.json` ist für Flex wichtig);
  - die Routendatei von Netgen Layouts im Slot `5.0` ist `routing.yaml`, nicht `routing.yml`;
  - `imagemagick.pre_parameters` und `imagemagick.post_parameters` sind als Container-Parameter deklariert (ein
    undefinierter Parameter stoppt sonst den Aufbau des Containers);
  - das Manifest von `4.6.x-dev` wurde neu erzeugt, nachdem die Dateien der Legacy Bridge daraus entfernt worden waren,
    und verweist auf den Commit, der `ez_publish_legacy.yaml` entfernt;
  - die `site.ini.append.php` des Slots `1.4` erhielt die Übersetzer-Extension und eine korrigierte Reihenfolge der
    Abschnitte;
  - die Design-Einstellungen von `legacy_site` nennen die richtigen CSS- und JavaScript-Dateien (`main`, nicht
    `main-un`).
- Die Releases 1.2.0 bis 1.3.23 sind Tags aus diesem Zeitraum (12. bis 17. April).

## Verwandte Seiten

- [Plattform-DXP-Skeleton](../plattform-dxp-skeleton.md)
- [Legacy Bridge](../legacy-bridge.md)
- [Chronik](../../../geschichte/erweiterungen/sevenx-recipes.md) und [Versionshinweise](../../../../changelogs/extensions/sevenx-recipes.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/sevenx-recipes.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-03](../../../geschichte/erweiterungen/monate/2026-03.md), [2026-04](../../../geschichte/erweiterungen/monate/2026-04.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
