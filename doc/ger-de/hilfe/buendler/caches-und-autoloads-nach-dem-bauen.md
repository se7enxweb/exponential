---
title: Caches und Autoloads nach dem Bauen
audience: bundler, developer, hosting
category: bundling
order: 400
summary: Was ein Bundle-Build und ein Update auffrischen müssen - die Autoload-Arrays (ezpgenerateautoloads.php -e, -k, -o, .autoloadignore), die Caches (ezcache.php --clear-tag, --clear-id, --clear-all, cache.php), die Worker und der Antwort-Cache von Velocity und die dokumentierte Reihenfolge nach einer Codeänderung.
keywords: Autoload, Cache, Cache leeren, Reihenfolge, Antwort-Cache, autoload, ezpgenerateautoloads.php, -e, -k, -o, .autoloadignore, cache, ezcache.php, --clear-tag, --clear-id, --clear-all, --list-tags, cache.php, exp:velocity deploy, response cache, OPcache, order, bundle
type: help
---
# Caches und Autoloads nach dem Bauen

Diese Seite richtet sich an Bündler, die Images und Pakete bauen, und an alle, die eine gebündelte Installation
aktualisieren. Ein Build, der die Autoload-Arrays vergisst, liefert eine Website aus, die ihre Klassen nicht findet;
einer, der die Caches vergisst, liefert veraltete Einstellungen und Templates aus.

## Inhalt

1. [Autoload-Arrays](#autoload-arrays)
2. [Caches](#caches)
3. [Die Reihenfolge nach einer Codeänderung](#die-reihenfolge-nach-einer-codeänderung)
4. [In einem Bundle-Build](#in-einem-bundle-build)
5. [Bei einem Bundle-Update](#bei-einem-bundle-update)

## Autoload-Arrays

"Exponential findet Klassen über erzeugte Autoload-Arrays in `autoload/` (der Kernel) und `var/autoload/` (die
Erweiterungen). Eine Klasse, die in keinem Array steht, wird nicht gefunden, auch wenn ihre Datei vorhanden ist"
([10.7 Autoloads der Klassen](../../installation/10-nach-der-installation.md#107-autoloads-der-klassen)).

```bash
php bin/php/ezpgenerateautoloads.php -e        # extensions: after adding, renaming or removing a class
php bin/php/ezpgenerateautoloads.php -k        # the kernel: only when kernel/ or lib/ changed
php bin/php/ezpgenerateautoloads.php -o        # kernel overrides
php bin/php/ezpgenerateautoloads.php -e -n     # dry run: report, write nothing
```

Zu den Optionen des Skripts (`kernel/private/classes/commands/ezpgenerateautoloads.php`) gehören außerdem `-q`, `-v`,
`-p` (Fortschritt), `-s` (Tests), `-t` (Ziel), `--exclude` und `--allow-root-user`. "Eine Option, die es nicht kennt,
beendet es mit Exit-Status 1, ohne dass etwas geschrieben wird", ein Build-Skript kann sich also auf seinen Status
verlassen.

`.autoloadignore` im Wurzelverzeichnis der Installation begrenzt, welche Verzeichnisse durchlaufen werden; "Die
ausgelieferte Datei schließt `vendor` ..., `ai`, `var` und `.claude` aus". Ergänzen Sie Ihre eigenen
Arbeitsverzeichnisse, falls Ihr Build-Baum welche hat. Das `post-install-cmd` von Composer im Kernel führt bereits
`php bin/php/ezpgenerateautoloads.php` aus; ein Bundle mit eigener `composer.json` im Wurzelverzeichnis ergänzt
dasselbe Skript ([Bundles auf Composer-Basis](bundles-auf-composer-basis.md#skripte-nach-der-installation)).

## Caches

`bin/php/ezcache.php` nimmt `--clear-tag`, `--clear-id`, `--clear-all`, `--list-tags`, `--list-ids` und `--purge` mit
dessen Optionen `--iteration-sleep`, `--iteration-max` und `--expiry` (`kernel/private/classes/commands/ezcache.php`).

| Sie haben geändert | Leeren |
|---|---|
| eine `.ini`-Einstellung | `--clear-tag=ini` (nicht `--clear-id=ini`, das den globalen INI-Cache übersieht) |
| eine `.tpl`-Datei | `--clear-tag=template` |
| ein neues Template, eine Override-Regel, eine Erweiterung oder ein Design | `--clear-id=template-override,design_base` |
| ein Stylesheet oder Skript | `--clear-id=ezjscore-packer,template-block`, dann den Velocity-Cache |
| Inhalte wirken veraltet | `--clear-tag=content` |
| unsicher | `--clear-all` |

Quelle: [Was zu leeren ist](../../installation/10-nach-der-installation.md#welcher-cache-zu-leeren-ist). `php bin/php/cache.php` (Konsole
`exp:cache`) erreicht auch die Caches vor PHP: statischer Cache, HTTP, der Antwort-Cache von Velocity, OPcache
([Die Caches vor PHP](../../installation/10-nach-der-installation.md#die-caches-vor-php)).

## Die Reihenfolge nach einer Codeänderung

"Eine Seite, die zwischen dem Leeren eines Caches und dem Neuladen von PHP vom alten Code gerendert wird, wird erneut
zwischengespeichert und sieht aus, als hätte die Änderung nicht gewirkt. Die Reihenfolge lautet: INI- und
Template-Caches, dann PHP-FPM neu laden und Velocity neu starten, und **erst dann** die Inhalts-, HTTP-,
Template-Block- und Antwort-Caches." Unter Velocity erledigt `exp:velocity deploy` das in dieser Reihenfolge, mit PASS
oder FAIL je Schritt
([Die Reihenfolge nach einer Codeänderung](../../installation/10-nach-der-installation.md#die-reihenfolge-nach-einer-code-änderung)).

## In einem Bundle-Build

1. `composer install` aus der Lock-Datei (sein Skript erzeugt die Arrays neu) oder führen Sie
   `ezpgenerateautoloads.php` selbst aus.
2. `php bin/php/ezpgenerateautoloads.php -e`, nachdem Sie Ihre Erweiterungen hinzugefügt haben.
3. Liefern Sie `var/cache/` aus dem Build **nicht** aus; die Website baut ihre Caches bei der ersten Verwendung auf.
4. Prüfen Sie: `php bin/php/console exp:checkclasses` "lädt jede deklarierte Klasse und meldet die, die PHP nicht laden
   will".

## Bei einem Bundle-Update

1. Legen Sie den neuen Code an seinen Platz (Image, Paket, Git und Composer).
2. Erzeugen Sie die Arrays neu (`-e`; `-k`, wenn sich Kernel-Dateien geändert haben).
3. Wenden Sie das Datenbank-Update an, falls das Release eines braucht
   ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)).
4. Leeren Sie in der dokumentierten Reihenfolge oder führen Sie `exp:velocity deploy` aus (`--kernel`, wenn eine
   Kernel-Klasse hinzugefügt oder umbenannt wurde).

## Verwandte Seiten

- [Caches leeren und vorwärmen](../hosting/leistung/caches-leeren-und-vorwaermen.md)
- [10.6 Caches](../../installation/10-nach-der-installation.md#106-caches)
- [Velocity in einem Bundle](velocity-in-einem-bundle.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
