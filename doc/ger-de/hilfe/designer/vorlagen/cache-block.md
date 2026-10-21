---
title: Cache-Block
audience: designer, developer
category: templates
order: 130
summary: Die Funktion cache-block im Detail - Schlüssel, Ablauf (Standard 7200 Sekunden), ignore_content_expiry, subtree_expiry, was einen Block ungültig macht, sichere Schlüssel für Menüs und benutzerabhängige Teile und die Fehler, die einem Besucher die Seite eines anderen zeigen.
keywords: cache-block, Schlüssel, Ablauf, ignore_content_expiry, subtree_expiry, Template-Cache, Cache-Schlüssel, Menü-Cache, benutzerabhängiger Cache, DelayedCacheBlockCleanup, TemplateCache, keys, expiry, template cache, cache keys, menu cache, user specific cache
type: help
---
# Cache-Block

Diese Seite ist für Designer, die teure Teile eines Templates billig machen. Ein `{cache-block}` speichert die Ausgabe
seines Inhalts und verwendet sie wieder, bis sich seine Schlüssel ändern oder er abläuft. Implementierung:
`lib/eztemplate/classes/eztemplatecachefunction.php` und `eztemplatecacheblock.php`.

## Inhalt

1. [Syntax und Parameter](#syntax-und-parameter)
2. [Schlüssel](#schlüssel)
3. [Ablauf](#ablauf)
4. [Sichere Muster](#sichere-muster)
5. [Fehler](#fehler)
6. [Einstellungen und Leeren](#einstellungen-und-leeren)

## Syntax und Parameter

```tpl
{cache-block keys=array( $module_result.uri, $current_user.role_id_list|implode( ',' ) ) expiry=3600}
    ...
{/cache-block}
```

| Parameter | Bedeutung |
|---|---|
| `keys` | ein Wert oder Array; jede andere Kombination erhält ihre eigene gespeicherte Kopie |
| `expiry` | Lebensdauer in Sekunden; Standard 7200 (`DEFAULT_TTL`); `0` bedeutet keine Zeitgrenze |
| `ignore_content_expiry` | die Kopie behalten, wenn Inhalte veröffentlicht werden (standardmäßig lässt eine Veröffentlichung Cache-Blöcke ablaufen) |
| `subtree_expiry` | ablaufen, wenn Inhalte unterhalb des angegebenen Knotens (ID oder Pfad) veröffentlicht werden, statt bei jeder Veröffentlichung |

Ohne `keys` ist die Position des Blocks im Template der einzige Schlüssel: eine Kopie für jede Seite und jeden Besucher.

## Schlüssel

Jeder Wert, der die Ausgabe ändert, muss in den Schlüsseln stehen:

| Der Block zeigt | Zu den Schlüsseln hinzufügen |
|---|---|
| ein Menü, das die aktuelle Seite markiert | `$module_result.uri` oder die ID des aktuellen Knotens |
| Einträge, die von Berechtigungen abhängen | `$current_user.role_id_list\|implode( ',' )` und `$current_user.limited_assignment_value_list\|implode( ',' )` |
| den Namen oder Warenkorb des Besuchers | `$current_user.contentobject_id` (oder ihn nicht cachen) |
| sprachabhängigen Text | nichts zusätzlich, wenn jede Sprache ein eigener Siteaccess ist: Der Name des Siteaccess ist Teil des Cache-Pfads jedes Blocks |

`subtree_expiry` schließt auch `ignore_content_expiry` ein: Ein solcher Block läuft nur bei Veröffentlichungen unterhalb
seines Knotens ab. Das Design simple verschlüsselt seinen ganzen Rahmen nach URI, Warenkorbzustand und Benutzer-ID; das
Pagelayout der Administration baut einen Benutzer-Hash aus Rollen- und Einschränkungslisten.

## Ablauf

- Standard: zwei Stunden und bei jeder Veröffentlichung von Inhalten.
- Eine Fußzeile, die sich nie mit Inhalten ändert: `ignore_content_expiry` und ein langes `expiry`.
- Ein Menü einer Sektion: `subtree_expiry` mit dem Wurzelknoten der Sektion, sodass Veröffentlichungen anderswo ihn
  behalten.

## Sichere Muster

```tpl
{* a top menu: same for everyone with the same roles on the same page *}
{cache-block keys=array( $module_result.uri, $current_user.role_id_list|implode( ',' ) ) subtree_expiry=2}
    {include uri='design:pagelayout/menu.tpl'}
{/cache-block}
```

```tpl
{* a footer: same everywhere, changes rarely *}
{cache-block ignore_content_expiry expiry=86400}
    {include uri='design:pagelayout/footer.tpl'}
{/cache-block}
```

## Fehler

- **Benutzerdaten ohne den Benutzer in den Schlüsseln**: Der Name eines Besuchers wird anderen gezeigt.
- **`csp_nonce()` innerhalb eines Blocks**: Die gespeicherte Nonce ist bei jeder späteren Anfrage falsch, und das Skript
  wird blockiert.
- **Ein Fetch innerhalb des Blocks, dessen Ergebnis außerhalb verwendet wird**: Bei einem Cache-Treffer läuft der Fetch
  nicht.
- **Schlüssel mit vielen verschiedenen Werten** (vollständige Query-Strings, Zeitstempel): eine Datei pro Wert und keine
  Wiederverwendung. Wandeln oder reduzieren Sie sie (das Pagelayout der Administration wandelt seinen Sektionsfilter
  deshalb in eine Zahl um).
- **Verschachtelte Cache-Blöcke**: funktionieren, machen das Ungültigwerden aber schwer nachvollziehbar; vermeiden Sie sie.

## Einstellungen und Leeren

- `site.ini [TemplateSettings] TemplateCache=enabled` schaltet Cache-Blöcke ein (disabled: nie verwendet).
- `DelayedCacheBlockCleanup` (Standard disabled) lässt abgelaufene Teilbaum-Blöcke auf der Platte, damit ein Cronjob sie
  entfernt.
- Leeren: `php bin/php/ezcache.php --clear-id=template-block`.

## Verwandte Seiten

- [Caching für Designer](../theme-design/caching-fuer-designer.md)
- [Run-once](run-once.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
