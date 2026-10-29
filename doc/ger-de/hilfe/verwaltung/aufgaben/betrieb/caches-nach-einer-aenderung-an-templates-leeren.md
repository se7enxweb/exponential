---
title: Caches nach einer Änderung an Templates leeren
audience: government, admin, developer
category: public-sector
order: 80
summary: Schritt für Schritt - ein geändertes oder neues Template auf der öffentlichen Site sichtbar machen - die Template-Caches leeren (und für neue Templates den Override-Cache), den View-Cache der Inhalte und den Antwort-Cache von Velocity, ohne Neustart.
keywords: Änderung an Templates, Template-Cache, template-override, design_base, Inhalts-Cache, Velocity-Cache, exp:cache clear, neues Template, Stylesheet, ezjscore-Packer, template change, template cache, content cache, Velocity cache, new template, stylesheet, ezjscore packer
type: help
---
# Caches nach einer Änderung an Templates leeren

Diese Aufgabe ist für Administratoren und Entwickler, nachdem eine Änderung am Design ausgerollt wurde. Änderungen an
Templates und Stylesheets brauchen weniger Schritte als PHP-Änderungen: keinen Neustart, nur Caches
([Bereitstellung, Abschnitt 7](../../../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Das Template ist auf den Server ausgerollt.
- Shell-Zugang oder **Setup > Cache-Verwaltung** mit `setup/managecache`.

## Schritte

| Sie haben geändert | Ausführen |
|---|---|
| Ein bestehendes Template | `./console exp:cache clear --id=template --allow-root-user` |
| Ein neues Template oder eine neue Override-Regel | zusätzlich `php bin/php/ezcache.php --clear-id=template-override --allow-root-user` und `--clear-id=design_base` |
| Ein vom Packer gebündeltes Stylesheet oder Skript | `./console exp:velocity deploy --packer --allow-root-user` oder die Caches von Packer und Template-Blöcken leeren |

Dann die Seiten-Caches:

```bash
php bin/php/ezcache.php --clear-tag=content --allow-root-user
./console exp:velocity cache clear --apply --allow-root-user
```

Im Browser: **Setup > Cache-Verwaltung** hat die Gruppen *Templates* (Template-Cache, Template-Block-Cache,
Template-Überschreib-Cache), *Inhalte und Ansichten* und *Seiten, Abfragen und der Server*, jede mit **Leeren**.

## Prüfen, ob es funktioniert hat

- Öffnen Sie die geänderte Seite in einem privaten Fenster, als Besucher.
- Wenn ein Browser noch ein altes Stylesheet zeigt, holt ein hartes Neuladen es.
- Das neue Template wird überhaupt nicht verwendet, die Seite zeigt einen leeren Inhaltsbereich: Der Override-Cache
  wurde nicht geleert.

## Verwandte Seiten
- [Caches nach einer Änderung an Einstellungen leeren](caches-nach-einer-aenderung-an-einstellungen-leeren.md)
- [Den Antwort-Cache von Velocity leeren](den-antwort-cache-von-velocity-leeren.md)
- [Veralteter Cache](../../betrieb/vorfaelle/veralteter-cache.md)
- [Anleitung zu Templates und Design](../../../../anleitungen/templates-und-design.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
