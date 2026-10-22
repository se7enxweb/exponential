---
title: Bundles auf Composer-Basis
audience: bundler, developer
category: bundling
order: 80
summary: Wie Sie ein Bundle als eigenes Composer-Projekt bauen - die composer.json im Wurzelverzeichnis, das Installer-Plugin und seine Freigabe, Ihre eigenen Erweiterungen und Repositorys, Versionsbedingungen im Stil des Projekts, der Autoload-Schritt nach der Installation und was Sie in einem Bundle mit Composer nie tun sollten.
keywords: Composer, composer.json, Bundle, Projekt, Repositorys, Versionsbedingungen, project, require, repositories, allow-plugins, exponential-legacy-installer, ezpublish-legacy, post-install-cmd, ezpgenerateautoloads, --no-dev, tilde, constraints
type: help
---
# Bundles auf Composer-Basis

Diese Seite richtet sich an Bündler, die das Bundle als Composer-Projekt verwalten: das Starter-Kit einer Agentur, eine
eigene Distribution, einen Image-Build. Exponential und alle seine Erweiterungen werden über Composer ausgeliefert,
daher erhält ein Bundle, das Composer spricht, Updates auf genau dem Weg, auf dem das Projekt sie veröffentlicht.

## Inhalt

1. [Der Aufbau eines Composer-Bundles](#der-aufbau-eines-composer-bundles)
2. [Eine composer.json im Wurzelverzeichnis](#eine-composerjson-im-wurzelverzeichnis)
3. [Das Installer-Plugin](#das-installer-plugin)
4. [Ihre eigenen Erweiterungen](#ihre-eigenen-erweiterungen)
5. [Skripte nach der Installation](#skripte-nach-der-installation)
6. [Regeln](#regeln)

## Der Aufbau eines Composer-Bundles

```text
your-bundle/
    composer.json        your root package: requires se7enxweb/exponential and your parts
    composer.lock        committed: the exact versions of this bundle release
    kickstart.ini.template  or the exp:install options your build uses
    packages/            your site package(s), if any
    README.md, LICENSE   your notices
```

Nach `composer install` landet der Kernel im Wurzelverzeichnis des Projekts (`.`), neben Ihren Dateien, weil das
Installer-Plugin den Pakettyp `ezpublish-legacy` dort installiert
([3.2 C](../../installation/03-den-code-beschaffen.md#c-composer-require-in-ein-leeres-verzeichnis)).

## Eine composer.json im Wurzelverzeichnis

Ein minimales Beispiel; ersetzen Sie die Namen und Versionen durch Ihre und prüfen Sie vor der Verwendung, dass jedes
Paket existiert:

```json
{
    "name": "example/exponential-bundle",
    "description": "Example bundle, based on Exponential",
    "license": "GPL-2.0-or-later",
    "type": "project",
    "require": {
        "se7enxweb/exponential": "~6.0.14",
        "example/example_theme": "~1.2.0"
    },
    "repositories": [
        { "type": "vcs", "url": "https://git.example.com/example/example_theme.git" }
    ],
    "config": {
        "allow-plugins": {
            "se7enxweb/exponential-legacy-installer": true
        }
    },
    "scripts": {
        "post-install-cmd": [ "@php bin/php/ezpgenerateautoloads.php" ],
        "post-update-cmd": [ "@php bin/php/ezpgenerateautoloads.php" ]
    }
}
```

- `license`: Nennen Sie die Lizenz Ihres Bundles mit einem SPDX-Bezeichner. Code, der mit Exponential kombiniert und
  zusammen weitergegeben wird, muss mit der GPL vereinbar sein; fragen Sie einen Rechtsbeistand
  ([GPL-Pflichten](gpl-pflichten-beim-weitergeben.md)).
- Die `~`-Bedingungen folgen dem Stil des Projekts: Die letzte Stelle bleibt frei
  ([Versionen, Tags und Pinning](versionen-tags-und-pinning.md#tilde-bedingungen)).
- Die `scripts` spiegeln, was die eigene `composer.json` des Kernels ausführt
  (`"legacy-scripts": ["@php bin/php/ezpgenerateautoloads.php"]` bei `post-install-cmd` und `post-update-cmd`), denn
  "Composer führt nur die Skripte des Wurzelpakets aus, und das ist jetzt Ihres".

## Das Installer-Plugin

Composer 2.2 und neuer fragt, ob `se7enxweb/exponential-legacy-installer` vertraut werden soll. Erlauben Sie es in der
`composer.json` (wie oben) oder mit:

```bash
composer config allow-plugins.se7enxweb/exponential-legacy-installer true
```

"Wenn Sie verneinen, landet der Kernel in `vendor/se7enxweb/exponential/` und die Erweiterungen ebenfalls in
`vendor/`, wo nichts sie findet." In einer Build-Pipeline antwortet niemand, schreiben Sie die Einstellung also in die
Datei.

## Ihre eigenen Erweiterungen

- Veröffentlichen Sie jede Erweiterung als eigenes Composer-Paket (eine `composer.json` mit Namen, Lizenz und dem
  Typ, den Ihre bestehenden Erweiterungen verwenden) in einem Repository, das Sie kontrollieren.
- Geben Sie jedem Release einen Tag und schreiben Sie vor dem Taggen dieselbe Version in `ezinfo.php` und
  `extension.xml` ([Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md#release-regel)).
- Aktivieren Sie die Erweiterungen in Ihrem Seitenpaket oder in `settings/override/site.ini.append.php`
  (`[ExtensionSettings] ActiveExtensions[]`), wie es die Seite zur
  [Standardauswahl an Erweiterungen](../../funktionen/6.0/standard-extension-auslieferung.md#eine-extension-hinzufügen) zeigt.
- Eigene Erweiterungen können in einem zweiten Erweiterungsverzeichnis liegen (`site.ini [ExtensionSettings]
  AdditionalExtensionDirectories[]`, standardmäßig leer), getrennt von denen Dritter
  ([Zusätzliche Erweiterungsverzeichnisse](../../bc/6.0/zusaetzliche-erweiterungsverzeichnisse.md)).

## Skripte nach der Installation

Nach jedem `composer install` oder `update` braucht das Bundle frische Autoload-Arrays und Caches. Das Skript oben
erledigt das Erste; der Rest steht unter [Caches und Autoloads nach dem Bauen](caches-und-autoloads-nach-dem-bauen.md).

## Regeln

- [ ] Bauen Sie mit `composer install` aus Ihrer eingecheckten `composer.lock`; führen Sie `composer update` nur aus,
      wenn Sie ein neues Bundle-Release vorbereiten.
- [ ] Ergänzen Sie `--no-dev` für alles, was in Produktion geht.
- [ ] Bearbeiten Sie nie Dateien in `vendor/` oder in einer mit Composer installierten Erweiterung: Die nächste
      Installation ersetzt sie. Legen Sie Änderungen in Ihre eigene Erweiterung oder reichen Sie sie beim Projekt ein.
- [ ] Installationen aus Git-Checkouts der Erweiterungen dürfen `composer update` nicht blind ausführen: "Es ersetzt
      die Checkouts" ([Standardauswahl an Erweiterungen](../../funktionen/6.0/standard-extension-auslieferung.md#eine-weglassen)).
- [ ] Halten Sie mit jedem Bundle-Release die Ausgabe von `composer show` fest.

## Verwandte Seiten

- [Lock-Dateien und reproduzierbare Builds](lock-dateien-und-reproduzierbare-builds.md)
- [Erweiterungen hinzufügen und entfernen](erweiterungen-hinzufuegen-und-entfernen.md)
- [Starter-Kits für Agenturen](starter-kits-fuer-agenturen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
