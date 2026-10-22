---
title: Kompatibilität und Abhängigkeiten im Ökosystem
audience: ecosystem, developer, admin, hosting
category: ecosystem
order: 27
summary: Wie Sie prüfen, ob ein Paket von Exponential zu Ihrer Installation passt - PHP-Versionen, die Kernel-Version, Datenbanken, Engines, die Ladereihenfolge der Erweiterungen und Composer-Konflikte - und wie Sie die Anforderungen eines Pakets lesen, bevor Sie es installieren.
keywords: Kompatibilität, Abhängigkeiten, composer.json, require, PHP-Version, Kernel-Version, Datenbank, Velocity, Ladereihenfolge der Erweiterungen, Konflikte, composer why, composer show, compatibility, dependencies, PHP version, kernel version, database, extension loading order, conflicts
type: help
---
# Kompatibilität und Abhängigkeiten im Ökosystem

## Inhalt

1. [Zuerst die Anforderungen lesen](#zuerst-die-anforderungen-lesen)
2. [Die zu prüfenden Dimensionen](#die-zu-prüfenden-dimensionen)
3. [Nützliche Befehle](#nützliche-befehle)
4. [Wenn etwas nicht passt](#wenn-etwas-nicht-passt)

## Zuerst die Anforderungen lesen

Ein Composer-Paket sagt in seiner `composer.json`, was es braucht (`require`, `conflict`, `suggest`). Lesen Sie sie vor
der Installation, und trauen Sie keiner Beschreibung, die nicht durch sie gedeckt ist. Das gilt auch für ganze
Distributionen: Die DXP zum Beispiel liefert keinen Layout-Editor, keinen Workflow, keinen Zeitplaner und keinen
Legacy-Kernel, was ihre Anforderungen zeigen ([Exponential Platform DXP](../produkte/platform-dxp.md)).

## Die zu prüfenden Dimensionen

| Dimension | Wo Sie prüfen | Seite |
|---|---|---|
| PHP-Version | `require.php` des Pakets und des Kernels (PHP 8.0 bis 8.5 für 6.0.15) | [PHP-Versionen](../php-stack/php-versionen.md) |
| Kernel-Version | `require."se7enxweb/exponential"` oder die README | [Der Kernel von Exponential 6](../produkte/exponential-6-kernel.md) |
| Datenbank | Ob die Erweiterung SQL verwendet, das Ihrer Datenbank fehlt | [Datenbanken](../php-stack/datenbanken.md) |
| Engine | Persistente Worker halten Klassen im Speicher; nach Änderungen neu starten | [Exponential Velocity](../produkte/exponential-velocity.md) |
| Andere Erweiterungen | Template-Overrides und Einstellungen können kollidieren; die Ladereihenfolge entscheidet | [Ladereihenfolge der Erweiterungen](../../../funktionen/6.0/ladereihenfolge-der-extensions.md) |
| PHP-Erweiterungen | Anforderungen `ext-*` | [PHP-Versionen und -Erweiterungen](../../hosting/grundlagen/php-versionen-und-erweiterungen.md) |

## Nützliche Befehle

```bash
composer show se7enxweb/exponential      # the installed kernel version
composer why <vendor/package>            # which package requires it
composer why-not <vendor/package> <ver>  # what blocks a version
./console exp:info --plain --no-logo     # every extension with its version, the engine, the caches
```

`exp:info` ist unter [Die Systemkarte](../../../funktionen/6.0/systemkarte.md) beschrieben.

## Wenn etwas nicht passt

- Suchen Sie nach einem neueren Release des Pakets.
- Prüfen Sie, ob ein Fork gepflegt wird ([Einen Fork synchron halten](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/einen-fork-synchron-halten.md)).
- Portieren Sie es und schicken Sie die Änderung zurück ([Mit Pull-Requests zurückgeben](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md)).
- Fragen Sie in den Foren, ob es schon jemand getan hat.

## Verwandte Seiten

- [Pakete](README.md)
- [Eine Erweiterung finden](eine-erweiterung-finden.md)
- [Teil 4: Upgrades](../../erweiterungsentwicklung/teil-4-aktualisieren/README.md)
- [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
