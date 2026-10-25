---
title: Exponential 6 als Basis eines Bundles
audience: bundler, developer, hosting
category: bundling
order: 40
summary: Was ein auf Exponential 6 aufgebautes Bundle bereits enthält, bevor Sie etwas hinzufügen - den Kernel, die von der composer.json im Wurzelverzeichnis verlangten Erweiterungen, Velocity, das Standard-Theme und die Seitenpakete, die die Installer anbieten - und wie Sie diese Liste lesen und anpassen.
keywords: Exponential 6, composer.json, require, suggest, Standarderweiterungen, Velocity, sevenx_themes_simple, Seitenpaket, sevenx_multisite, Basis, Distribution, default extensions, site package, base, distribution
type: help
---
# Exponential 6 als Basis eines Bundles

Diese Seite richtet sich an Bündler, die auf dem reinen Exponential-6-Paket aufbauen. Sie zeigt, was Sie "umsonst"
bekommen, bevor Sie Ihre eigenen Teile hinzufügen, damit Sie weder etwas doppelt ausliefern noch etwas vermissen, von
dem Sie dachten, es sei schon da.

## Inhalt

1. [Was das Wurzelpaket mitbringt](#was-das-wurzelpaket-mitbringt)
2. [Lesen Sie die Liste für Ihre Version](#lesen-sie-die-liste-für-ihre-version)
3. [Erforderlich oder vorgeschlagen](#erforderlich-oder-vorgeschlagen)
4. [Was die Installer hinzufügen](#was-die-installer-hinzufügen)
5. [Was es nicht mitbringt](#was-es-nicht-mitbringt)

## Was das Wurzelpaket mitbringt

Die `composer.json` von `se7enxweb/exponential` "entscheidet, welche Erweiterungen mit `composer create-project`
ankommen" ([Die Standardauswahl an Erweiterungen](../../funktionen/6.0/standard-extension-auslieferung.md)):

| Teil | Was | Quelle |
|---|---|---|
| Der Kernel | `kernel/`, `lib/`, `bin/php/`, `design/`, `settings/`, `share/` | das Repository |
| Mit dem Kernel ausgelieferte Erweiterungen | `ezjscore`, `ezoe`, `ezformtoken`, `expservices` | [3.2 A](../../installation/03-den-code-beschaffen.md#a-git-clone-und-composer-install-empfohlen) |
| Erforderliche Erweiterungen | jedes `se7enxweb/*`-Paket unter `require`, jedes ein eigenes Composer-Paket | `composer.json` |
| Velocity | `se7enxweb/exponential-velocity ~0.0.4.55` ist seit 6.0.15 erforderlich | [Velocity-Engines](../../bc/6.0/velocity-engines.md#velocity-ist-seit-6015-teil-von-exponential) |
| Das Standard-Theme | `se7enxweb/sevenx_themes_simple`, seit 6.0.10 erforderlich, "damit das Standard-Theme immer mit ausgeliefert wird" | [Standardauswahl an Erweiterungen](../../funktionen/6.0/standard-extension-auslieferung.md#zeitleiste-der-auslieferung-composerjson-im-wurzelverzeichnis) |
| Der Oracle-Treiber | `se7enxweb/ezoracle`, erforderlich, daher in jeder Installation vorhanden | [Datenbankunterstützung](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge) |
| Die Dokumentation | `doc/`, einschließlich dieser Hilfe | das Repository |

## Lesen Sie die Liste für Ihre Version

Die Liste wächst von Release zu Release. Lesen Sie sie aus genau der Version, die Sie bündeln, nie aus dem Gedächtnis:

```bash
grep -n '"se7enxweb/' composer.json
```

"Der erste Block der Ausgabe ist `suggest`, der zweite `require`"
([Die Standardauswahl an Erweiterungen](../../funktionen/6.0/standard-extension-auslieferung.md#die-aktuelle-liste-ansehen)).
Nach `composer install` listet `composer show` auf, was tatsächlich installiert wurde, mit Versionen.

## Erforderlich oder vorgeschlagen

- `require` wird mit dem Produkt installiert. Ein erforderliches Paket aus Ihrem Bundle wegzulassen bedeutet, die
  `composer.json` zu ändern; halten Sie den Grund fest
  ([Erweiterungen hinzufügen und entfernen](erweiterungen-hinzufuegen-und-entfernen.md)).
- `suggest` "ist nur ein Hinweis, den Composer ausgibt; ein vorgeschlagenes Paket installieren Sie selbst mit
  `composer require se7enxweb/<name>`". Beispiele aus der `composer.json`: `se7enxweb/ezfind` (Solr-Suche),
  `se7enxweb/sevenx_valkey_cache` (Redis/Valkey-Caches), `se7enxweb/ezauthorize`.
- Die PHP-Erweiterungen unter `suggest` (zum Beispiel `ext-sqlite3`, `ext-pgsql`, `ext-mysqli`, `ext-gd`) sind die,
  die die Datenbanken und die Bildverarbeitung brauchen. Ihre Laufzeitumgebung entscheidet, welche davon Sie
  ausliefern ([Kapitel 2.3 PHP-Erweiterungen](../../installation/02-voraussetzungen.md#23-php-extensions)).

## Was die Installer hinzufügen

Die Installer bauen die Website aus einem **Seitenpaket** auf. Der Paketindex von 6.0.15 bietet zwölf Seitenpakete an;
der Standard von `exp:install --package` ist `sevenx_multisite`, mit `sevenx_multisite_clean` als derselben Website
ohne Demo-Inhalte ([4.3 Seitenpakete](../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)).
Ein Seitenpaket aktiviert seine eigenen Erweiterungen in `settings/override/site.ini.append.php`. Ihr Bundle wählt
das Paket; siehe [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md).

## Was es nicht mitbringt

| Nicht enthalten | Wer es bereitstellt |
|---|---|
| PHP und seine Erweiterungen | das Betriebssystem oder Ihr Image |
| Ein Datenbankserver (PostgreSQL, MariaDB ...) | Sie oder der Empfänger; SQLite braucht keinen |
| Ein anderer Webserver als Velocity | das Betriebssystem oder Ihr Image |
| Eine `composer.lock` | Sie: Die Lock-Datei "ist nicht Teil des Repositorys" ([3.2 A](../../installation/03-den-code-beschaffen.md#a-git-clone-und-composer-install-empfohlen)) |
| Zeitgesteuerter Aufruf von `runcronjobs.php` per Cron | Ihr Image oder Panel ([Cronjobs](../hosting/betrieb/cronjobs.md)) |
| Backups und Überwachung | Ihr Bundle oder der Empfänger |

## Verwandte Seiten

- [Die Basis wählen](die-basis-waehlen.md)
- [Bundles auf Composer-Basis](bundles-auf-composer-basis.md)
- [Kapitel 3: Den Code beschaffen](../../installation/03-den-code-beschaffen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
