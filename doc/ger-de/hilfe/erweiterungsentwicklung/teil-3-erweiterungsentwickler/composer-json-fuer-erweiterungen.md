---
title: composer.json für eine Erweiterung
audience: developer, app-creator
category: extension-development
order: 60
summary: Die composer.json, die eine Erweiterung nach extension/<name> installiert - Pakettyp, Installer, Name der Erweiterung, Anforderungen an PHP und Kernel, Lizenz, Autoren, mit zwei echten Beispielen.
keywords: Composer, Paketname, Lizenz, Autoren, PHP-Version, Anforderungen, composer.json, composer, packagist, ezpublish-legacy-extension, ezpublish-legacy-extension-name, installer, require, php version, license, authors, support, package name
type: help
---
# composer.json für eine Erweiterung

Teil 3, Kapitel 6. Mit einer `composer.json` lässt sich Ihre Erweiterung mit `composer require` installieren und auf
Packagist veröffentlichen. Dieses Kapitel erklärt anhand von zwei echten Dateien die Felder, auf die es bei einer
Erweiterung ankommt.

## Ein modernes Beispiel

Die `composer.json` von `se7enxweb/expqueue` (gekürzt):

```json
{
    "name": "se7enxweb/expqueue",
    "description": "The Exponential job queue: a generic, database-backed job queue ...",
    "type": "ezpublish-legacy-extension",
    "homepage": "https://github.com/se7enxweb/expqueue",
    "license": "GPL-2.0-or-later",
    "authors": [
        { "name": "7x", "homepage": "https://se7enx.com", "role": "Author" }
    ],
    "support": {
        "issues": "https://github.com/se7enxweb/expqueue/issues",
        "source": "https://github.com/se7enxweb/expqueue"
    },
    "require": {
        "php": ">=8.0",
        "ext-json": "*",
        "ext-mbstring": "*",
        "se7enxweb/exponential": "^6.0.14",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": {
        "ezpublish-legacy-extension-name": "expqueue"
    }
}
```

## Die Felder

| Feld | Was Sie eintragen | Warum |
|---|---|---|
| `name` | `<vendor>/<package>`, in Kleinbuchstaben | der Name auf Packagist; meist der Name des Repositorys |
| `type` | `ezpublish-legacy-extension` | sagt dem Installer, dass das Paket nach `extension/` gehört |
| `extra.ezpublish-legacy-extension-name` | der Ordnername | der Installer benennt den Ordner danach; ohne diesen Eintrag wird der Paketname verwendet |
| `require.php` | die PHP-Versionen, die Sie getestet haben | `>=8.0` oder eine Liste wie `^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 \|\| ^8.4` |
| `require` eines Legacy-Installers | `se7enxweb/ezpublish-legacy-installer` oder `se7enxweb/exponential-legacy-installer` | das Composer-Plugin, das den Erweiterungstyp kennt (die `composer.json` des Kernels verlangt beide) |
| `require.se7enxweb/exponential` | die Kernel-Versionen, die Ihr Code braucht, wenn er neue APIs des Kernels verwendet | Composer verweigert eine Installation mit einem älteren Kernel |
| `require.ext-*` | PHP-Erweiterungen, die Sie aufrufen | eine klare Fehlermeldung statt eines fatalen Fehlers zur Laufzeit |
| `license` | ein SPDX-Bezeichner: `GPL-2.0-or-later` | Packagist und Werkzeuge lesen ihn |
| `authors` | alle, die daran geschrieben haben, auch die ursprünglichen Autoren | Anerkennung; siehe unten |
| `support` | Links zu Issues und Quellcode | wo Benutzer Probleme melden |
| `suggest` | optionale Pakete mit Begründung | von Composer bei der Installation angezeigt |

Tragen Sie `version` nicht in die `composer.json` ein, es sei denn, Sie halten es mit jedem Tag im Gleichschritt:
Packagist nimmt die Versionen aus den Tags. Wenn Sie es doch führen, erhöhen Sie es zusammen mit `ezinfo.php` und
`extension.xml`
([ezinfo.php und extension.xml hochzählen](../teil-7-veroeffentlichen/ezinfo-und-extension-xml-anheben.md)).

## Den ursprünglichen Autor nennen

`se7enxweb/expchangeclass` ist ein Fork; seine `composer.json` nennt weiterhin den ursprünglichen Autor:

```json
"authors": [
    { "name": "7x", "homepage": "https://se7enx.com" },
    { "name": "<original author>", "role": "Original author of eZChangeclass" }
]
```

(Die echte Datei nennt die Person; diese Seite lässt Namen von Einzelpersonen weg.) Machen Sie es genauso, wenn Sie
forken:
[Namensnennung und Urheberrechtsvermerke](../teil-5-erweitern-forken-zurueckgeben/namensnennung-und-urheberrechtshinweise.md).

## Prüfen

```bash
composer validate extension/mysite_hello/composer.json
```

`composer validate` meldet fehlende oder ungültige Felder; beheben Sie jeden Fehler und lesen Sie die Warnungen.

## Aus einem Repository installieren, bevor das Paket auf Packagist ist

Solange das Paket nicht auf Packagist ist, kann ein Projekt es aus Git installieren, indem es seiner eigenen
`composer.json` ein Repository hinzufügt:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/your-organization/mysite_hello" }
]
```

und dann `composer require your-organization/mysite_hello:dev-main` ausführt. Das Veröffentlichen auf Packagist
beschreibt
[Composer und Packagist](../teil-7-veroeffentlichen/composer-und-packagist.md).

> **Warnung**
> Führen Sie auf einer Live-Website nie `composer update` aus, um eine neue Erweiterung auszuprobieren. Probieren Sie
> sie auf Ihrer Entwicklungsinstallation aus, committen Sie `composer.lock` und stellen Sie diesen Stand bereit.

## Verwandte Seiten

- Zurück: [Ladereihenfolge der Erweiterungen](ladereihenfolge-der-erweiterungen.md)
- Weiter: [Einstellungen in einer Erweiterung](einstellungen-in-einer-erweiterung.md)
- [Ideen auf Packagist finden](../teil-1-vom-benutzer-zum-entwickler/ideen-auf-packagist-finden.md)
- [getcomposer.org: das Schema der composer.json](https://getcomposer.org/doc/04-schema.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
