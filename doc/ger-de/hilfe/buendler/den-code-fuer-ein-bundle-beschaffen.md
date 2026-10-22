---
title: Den Code für ein Bundle beschaffen
audience: bundler, developer
category: bundling
order: 60
summary: Die drei dokumentierten Wege, den Code von Exponential zu beschaffen (git clone und composer install, composer create-project, composer require in Ihr eigenes Projekt), welcher zu welcher Art von Bundle passt und wie Sie das Ergebnis prüfen, bevor Sie darauf aufbauen.
keywords: Code beschaffen, Build, Bundle bauen, get the code, git clone, composer install, create-project, composer require, --no-dev, --prefer-source, --keep-vcs, ezpublish-legacy, LegacyKernelInstaller, allow-plugins, bundle build
type: help
---
# Den Code für ein Bundle beschaffen

Diese Seite richtet sich an alle, die den Build eines Bundles schreiben. Die Installationsanleitung dokumentiert drei
Wege, den Code zu beschaffen
([3.2 Drei Wege zum Code](../../installation/03-den-code-beschaffen.md#32-drei-wege-zum-code)); jeder passt zu einer
anderen Art von Bundle.

## Inhalt

1. [Die drei Wege](#die-drei-wege)
2. [Welcher Weg für welches Bundle](#welcher-weg-für-welches-bundle)
3. [Build-Befehle](#build-befehle)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
5. [Häufige Fehler](#häufige-fehler)

## Die drei Wege

| Weg | Liefert Ihnen | Quelle |
|---|---|---|
| A. `git clone` und `composer install` | den Kernel und jede se7enxweb-Erweiterung als Git-Klone mit ihrer Historie | [3.2 A](../../installation/03-den-code-beschaffen.md#a-git-clone-und-composer-install-empfohlen) |
| B. `composer create-project` | Release-Archive ohne Historie (Klone mit `--prefer-source --keep-vcs`) | [3.2 B](../../installation/03-den-code-beschaffen.md#b-composer-create-project) |
| C. `composer require` in ein leeres Verzeichnis | den Kernel als Paket Ihres eigenen Projekts, mit Ihrer eigenen `composer.json` | [3.2 C](../../installation/03-den-code-beschaffen.md#c-composer-require-in-ein-leeres-verzeichnis) |

## Welcher Weg für welches Bundle

| Bundle | Weg | Warum |
|---|---|---|
| Container- oder Server-Image eines festen Releases | B, mit Version | Archive sind kleiner; das Image wird ohnehin für jedes Update neu gebaut |
| Starter-Kit einer Agentur | C | Ihre `composer.json` und `composer.lock` sind die Spezifikation des Kits |
| Ein Bundle, auf dem die Empfänger weiterentwickeln | A | Git zeigt, was sich geändert hat, und erlaubt Updates mit Git ([Mit Git entwickeln](../mit-git-entwickeln.md)) |
| Installer für ein Hosting-Panel | B oder ein vorbereitetes Archiv von B | schnell, keine Historie in jedem Konto nötig |
| Distributionspaket | B oder ein Quellarchiv des Tags | der Paketmanager übernimmt die Updates |

## Build-Befehle

Ein festgelegtes Release, Archive, keine Testwerkzeuge (Weg B):

```bash
composer create-project se7enxweb/exponential:v6.0.14 exponential --no-dev
```

`create-project` "behandelt die heruntergeladene `composer.json` als Wurzelpaket, daher gelten ihre
`config.allow-plugins` ... und ihr `post-install-cmd`-Skript: Am Ende werden die Klassenzuordnungen erzeugt
(`php bin/php/ezpgenerateautoloads.php`). Auf einem Produktionsserver ergänzen Sie `--no-dev`, das die Testwerkzeuge
weglässt" ([3.2 B](../../installation/03-den-code-beschaffen.md#b-composer-create-project)).

Ihr eigenes Projekt (Weg C):

```bash
mkdir exponential
cd exponential
composer config allow-plugins.se7enxweb/exponential-legacy-installer true
composer require se7enxweb/exponential
php bin/php/ezpgenerateautoloads.php
```

Das Paket hat den Typ `ezpublish-legacy`; das Installer-Plugin legt es im Wurzelverzeichnis des Projekts ab. "Wenn
Sie [dem Plugin zu vertrauen] verneinen, landet der Kernel in `vendor/se7enxweb/exponential/` und die Erweiterungen
ebenfalls in `vendor/`, wo nichts sie findet." Composer führt nur die Skripte Ihres Wurzelpakets aus, erzeugen Sie die
Klassenzuordnungen also selbst.

> **Hinweis**
> Welches PHP eine Version braucht, entscheidet, ob Composer sie überhaupt installiert: `v6.0.8` bis `v6.0.14`
> verlangen PHP `^8.1`; die Linie 6.0.15 (`main`) akzeptiert PHP 8.0. "Kein veröffentlichter Tag akzeptiert PHP 8.0"
> ([3.1](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches)). Bauen Sie mit dem PHP, das Ihr Bundle
> ausliefert.

## Das Ergebnis prüfen

```bash
php bin/php/console --version
ls vendor/autoload.php
composer show se7enxweb/exponential
```

Der erste Befehl nennt die Version (zum Beispiel `console (Exponential) 6.0.15stable`). "Fehlt
`vendor/autoload.php`, hat Composer nicht fertig gearbeitet; führen Sie `composer install` erneut aus und lesen Sie
die Ausgabe ab dem ersten Fehler" ([Das Ergebnis prüfen](../../installation/03-den-code-beschaffen.md#das-ergebnis-prüfen)).

Prüfen Sie danach, was dem PHP Ihres Bundles fehlt:

```bash
php bin/php/console exp:velocity ext check
```

Der Befehl "listet jede Erweiterung auf, die die Installation braucht, markiert, was fehlt, und gibt den
Installationsbefehl aus" ([Container: Docker und Podman](../hosting/modelle/container-docker-und-podman.md#images)).

## Häufige Fehler

- **Aus `main` bauen und es Release nennen.** `main` bewegt sich; halten Sie den Commit fest oder bauen Sie aus einem
  Tag ([Versionen, Tags und Pinning](versionen-tags-und-pinning.md)).
- **`composer update` in einem Build ausführen.** Es löst jedes Mal neue Versionen auf. Bauen Sie mit
  `composer install` aus Ihrer Lock-Datei
  ([Lock-Dateien und reproduzierbare Builds](lock-dateien-und-reproduzierbare-builds.md)).
- **Als root bauen und Dateien im Besitz von root ausliefern.** Korrigieren Sie die Besitzrechte vor dem Paketieren
  ([Dateirechte und Besitz von var/](dateirechte-und-besitz-von-var.md)).
- **Versehentlich `.git`-Verzeichnisse ausliefern.** Weg A und `--keep-vcs` behalten sie; entscheiden Sie, ob Ihre
  Empfänger sie haben wollen.

## Verwandte Seiten

- [Kapitel 3: Den Code beschaffen](../../installation/03-den-code-beschaffen.md)
- [Bundles auf Composer-Basis](bundles-auf-composer-basis.md)
- [Composer und Git auf Servern](../hosting/grundlagen/composer-und-git-auf-servern.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
