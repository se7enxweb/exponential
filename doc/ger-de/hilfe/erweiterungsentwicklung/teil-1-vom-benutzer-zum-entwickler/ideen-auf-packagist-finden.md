---
title: Ideen auf Packagist finden
audience: user, developer, designer, app-creator
category: extension-development
order: 100
summary: Packagist führt jedes Paket vom Typ Erweiterung - wie Sie sie finden, Versionen, Anforderungen und Lizenzen lesen und eines auf einer Entwicklungsinstallation installieren.
keywords: Packagist, Composer, Pakettyp, Versionen, Anforderungen, Lizenz, packagist, composer, ezpublish-legacy-extension, package type, versions, require, license, composer show, composer require, vendor se7enxweb
type: help
---
# Ideen auf Packagist finden

Teil 1, Kapitel 10. [Packagist](https://packagist.org/) ist der öffentliche Index, den Composer liest. Jede
Erweiterung, die sich mit `composer require` installieren lässt, steht dort, mit ihren Versionen, ihren Anforderungen
und ihrer Lizenz. Für jemanden, der Ideen sucht, ist es ein zweiter Katalog, geordnet nach Paketen statt nach
Repositories.

## Der Pakettyp für Erweiterungen

Composer-Pakete sagen mit einem `type`, was sie sind. Erweiterungen für diese Codebasis verwenden den Typ
`ezpublish-legacy-extension`; das Installer-Paket (`se7enxweb/exponential-legacy-installer`) erkennt diesen Typ und
legt das Paket in `extension/<name>` statt in `vendor/` ab. Der Name des Ordners stammt aus
`extra.ezpublish-legacy-extension-name` in der `composer.json` des Pakets, zum Beispiel:

```json
{
    "name": "se7enxweb/str_replace",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "extra": {
        "ezpublish-legacy-extension-name": "str_replace"
    }
}
```

## Jedes Erweiterungspaket auflisten

Packagist hat eine Liste nach Typ. Im Browser:
[packagist.org/packages/list.json?type=ezpublish-legacy-extension](https://packagist.org/packages/list.json?type=ezpublish-legacy-extension).
Im Terminal:

```bash
curl -s 'https://packagist.org/packages/list.json?type=ezpublish-legacy-extension' | grep -o '"[^"]*/[^"]*"' | wc -l
curl -s 'https://packagist.org/packages/list.json?vendor=se7enxweb' | grep -o '"se7enxweb/[^"]*"'
```

Als diese Seite geschrieben wurde (Oktober 2026), führte Packagist 398 Pakete vom Typ Erweiterung, 127 davon unter dem
Vendor `se7enxweb`, und insgesamt 218 Pakete unter `se7enxweb` (der Rest sind Bibliotheken, Distributionen und
Bundles). Die Zahlen ändern sich; führen Sie die Befehle aus, um die heutigen zu sehen.

## Ein Paket lesen

Die Seite `https://packagist.org/packages/<vendor>/<name>` zeigt für jede Version:

| Auf der Seite | Warum es für Sie wichtig ist |
|---|---|
| Versionen und ihr Datum | Wird es gepflegt? Passt die neueste Version zum neuesten Tag auf GitHub? |
| `require` | Welches PHP und welche anderen Pakete es braucht |
| Lizenz | Was Sie mit dem Code tun dürfen |
| Quelle | Das Repository; lesen Sie dort die Historie |
| Installationen | Ein grober Hinweis darauf, wie viele Websites es verwenden |

Dasselbe als JSON, was in Skripten nützlich ist:

```bash
curl -s https://packagist.org/packages/se7enxweb/eztags.json | head -c 600
```

Aus einer Installation zeigt Composer, was es über ein installiertes Paket weiß:

```bash
composer show se7enxweb/eztags
composer show --all se7enxweb/eztags | head -30
```

## Versionen, denen Sie vertrauen können

Eine Version auf Packagist stammt aus einem Git-Tag. Ein Tag wird, sobald Packagist es gelesen hat, nie wieder gelesen;
eine Versionsnummer bedeutet also für immer denselben Code. Deshalb verschieben die Projekte hinter diesen Paketen nie
ein Tag, sondern veröffentlichen stattdessen eine neue Version (siehe
[Tags und Releases sind dauerhaft](../teil-7-veroeffentlichen/tags-sind-dauerhaft.md)).

Wenn auf GitHub ein neueres Tag existiert, Packagist es aber nicht zeigt, fehlt meist der Webhook des Repositorys zu
Packagist; [Der Packagist-Webhook](../teil-7-veroeffentlichen/der-packagist-webhook.md) erklärt, wie Sie das prüfen.

## Eines auf Ihrer Entwicklungsinstallation installieren

```bash
composer require se7enxweb/<name>
```

Composer legt das Paket in `extension/<name>` ab. Damit ist es noch nicht eingeschaltet: Aktivieren Sie es wie in
[Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../../anleitungen/extensions.md)
beschrieben, erzeugen Sie die Autoloads neu und leeren Sie die Caches. Manche Erweiterungen brauchen mehr (eine
Datenbanktabelle, eine Inhaltsklasse); ihre README sagt es.

> **Warnung**
> Installieren Sie neue Pakete zuerst auf Ihrer Entwicklungsinstallation, nie direkt auf einer Live-Website. Ein Paket
> läuft mit allen Rechten des Kernels.

## Packagist oder GitHub

| Frage | Sehen Sie nach auf |
|---|---|
| Kann ich es mit Composer installieren? | Packagist |
| Welche Versionen gibt es? | Packagist (die Tags, die es gelesen hat), GitHub (alle Tags) |
| Warum wurde es geändert? | GitHub (Historie, Issues, Pull Requests) |
| Gibt es eine ältere, unveröffentlichte Erweiterung, die das tut? | GitHub-Forks, [Exponential Projects](projects-exponential-earth.md) |

## Verwandte Seiten

- Zurück: [Ideen auf GitHub finden](ideen-auf-github-finden.md)
- Weiter: [Share Exponential (share.exponential.earth)](share-exponential-earth.md)
- [composer.json für eine Erweiterung](../teil-3-erweiterungsentwickler/composer-json-fuer-erweiterungen.md)
- [Composer und Packagist](../teil-7-veroeffentlichen/composer-und-packagist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
