---
title: Cache-Invalidierung und Leeren
audience: software-engineer, developer, admin
category: software-engineering
order: 310
summary: Wie Exponential seine Caches selbst invalidiert (eZContentCacheManager beim Veröffentlichen, das Event content/cache, Ablaufzeitstempel, Tabellenverfolgung im Abfrage-Cache) und wie Sie sie von Hand nach ID oder Tag leeren, welche Änderung welches Leeren braucht, die Reihenfolge gegenüber dem Neuladen von Code und die Regeln für Code, der Cache-Dateien schreibt.
keywords: Cache-Invalidierung, Cache leeren, ezcache.php, exp:cache, clear-tag, clear-id, eZContentCacheManager, eZCache, Ablauf, Event content/cache, template-block, ini, Velocity cache clear, Softwareingenieur, cache invalidation, cache clear, expiry
type: help
---
# Cache-Invalidierung und Leeren

Diese Seite richtet sich an Ingenieure, die Code, Einstellungen oder Daten ändern und die richtigen Caches geleert haben
müssen, nicht mehr und nicht weniger, und an jene, die Code schreiben, der etwas in einem Cache speichert. Die
Administrationsseite ist unter [Caches](../../anleitungen/caches.md) beschrieben; die Cache-Tags und -IDs für Autoren von
Erweiterungen stehen unter
[Cache-Tags und Leeren](../erweiterungsentwicklung/teil-8-referenz/cache-tags-und-leeren.md).

## Inhalt

1. [Was sich selbst invalidiert](#was-sich-selbst-invalidiert)
2. [Von Hand leeren](#von-hand-leeren)
3. [Welche Änderung welches Leeren braucht](#welche-änderung-welches-leeren-braucht)
4. [Die Reihenfolge zählt](#die-reihenfolge-zählt)
5. [Regeln für Code, der zwischenspeichert](#regeln-für-code-der-zwischenspeichert)

## Was sich selbst invalidiert

| Mechanismus | Was er tut |
|---|---|
| `eZContentCacheManager` (`kernel/classes/ezcontentcachemanager.php`) | leert beim Veröffentlichen die View-Caches der betroffenen Knoten und löst das Event `content/cache` aus |
| `eZExpiryHandler` (`expiry.php` im Cache-Verzeichnis) | hält Ablaufzeitstempel; ein Cache, der älter als sein Zeitstempel ist, wird neu erzeugt |
| Rollenbewusster HTTP-Cache | leert gespeicherte Ansichten bei Inhaltsänderungen (standardmäßig `ContentChangePurges=all`) |
| SQL-Abfrage-Cache | ein Schreibvorgang in eine Tabelle macht jedes zwischengespeicherte Ergebnis veraltet, das sie gelesen hat |
| `sevenx_valkey_cache` | umgekehrte Knoten- und Teilbaum-Indizes leeren genau die Einträge, die ein Veröffentlichen betrifft |
| Template-Blöcke | laufen nach ihrer konfigurierten Zeit ab, sofern sich ihre Schlüssel nicht vorher ändern |

## Von Hand leeren

```bash
php bin/php/ezcache.php --list-ids --allow-root-user        # every cache id
php bin/php/ezcache.php --list-tags --allow-root-user       # every tag
php bin/php/ezcache.php --clear-tag=ini --allow-root-user   # the INI and global INI caches
php bin/php/ezcache.php --clear-id=template-block --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user       # everything: expensive on a busy site
./console exp:velocity cache clear                         # Velocity's response cache
```

Zu den Cache-IDs in `eZCache` gehören `content`, `template`, `template-block`, `template-override`, `ini`, `global_ini`,
`translation`, `design_base`, `active_extensions`, `classid`, `urlalias`, `imagealias`, `user_info_cache`,
`exphttpcache` und `querycache`; die Tags sind `content`, `template`, `ini`, `i18n`, `image` und `user`. Jedes Leeren wird
im Audit-Trail als `system.cache.clear` festgehalten, mit der Angabe, wer es getan hat.

## Welche Änderung welches Leeren braucht

| Sie haben geändert | Leeren Sie |
|---|---|
| Eine vorhandene `.tpl`-Datei | `template`, wenn das gerenderte Ergebnis veraltet aussieht, und den View-Cache der Seiten, die sie verwenden |
| Eine neue Template-Datei oder eine neue Override-Regel | `template-override` (sonst wird die neue Datei nicht gefunden) |
| Eine INI-Datei | `--clear-tag=ini`; unter Velocity außerdem neu starten |
| Eine PHP-Klasse | die Klassenzuordnungen neu erzeugen, PHP-FPM neu laden, Velocity neu starten ([Eine Änderung bereitstellen](eine-aenderung-bereitstellen.md)) |
| Ein Stylesheet oder Skript eines Designs | den Packer-Cache (`ezjscore-packer`) und `template-block` |
| Eine Übersetzung | `translation` |
| Inhalte, über den Kernel | nichts: der Kernel invalidiert |
| Die Datenbank, von außen (Wiederherstellung, SQL-Client) | `querycache`, `content` und die HTTP-Caches |

## Die Reihenfolge zählt

Laden Sie zuerst den neuen Code, leeren Sie Seiten-Caches zuletzt. Eine Seite, die der alte Code zwischen einem Leeren
und dem Neuladen von PHP-FPM oder Velocity rendert, wird erneut zwischengespeichert und so ausgeliefert, als hätte die
Änderung nicht gewirkt. `exp:velocity deploy` führt die Schritte in dieser Reihenfolge aus
([Kompatibilität, Releases und Bereitstellung](../../standards/architektur/kompatibilitaet-releases-deployment.md#eine-code-änderung-ausrollen)).

## Regeln für Code, der zwischenspeichert

- Cache-Dateien **müssen** über `eZClusterFileHandler::instance()` geschrieben werden, nicht mit direkten Dateiaufrufen,
  damit Installationen im Cluster funktionieren.
- Registrieren Sie einen eigenen Cache mit einer ID und einem Tag, damit `ezcache.php`, `exp:cache` und Setup > Caches
  ihn leeren können ([Caching in Anwendungen](../ersteller/anwendungsentwickler/caching-in-anwendungen.md)).
- Schlüsseln Sie einen Cache nach allem, was seinen Inhalt ändert: dem Zugriffskontext des Benutzers, dem Siteaccess, der
  Sprache.
- Ein inhaltsabhängiger Cache hört auf das Event `content/cache` oder schlüsselt nach der Änderung der Inhalte, damit ein
  Veröffentlichen ihn erreicht.

## Verwandte Seiten

- [Caching-Ebenen für Ingenieure](caching-ebenen-fuer-ingenieure.md)
- [Caches leeren und vorwärmen, für Hoster](../hosting/leistung/caches-leeren-und-vorwaermen.md)
- [Cache-Konsole](../../bc/6.0/cache-konsole.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
