---
title: Das Muster für dringende Bekanntmachungen
audience: government, website-creator, admin, editor
category: public-sector
order: 30
summary: Wie Sie dringende Bekanntmachungen auf jeder Seite oder der Startseite einer Exponential-Site ohne eingebaute Warnfunktion zeigen - ein Ordner für Bekanntmachungen, den das Template listet, wenn er nicht leer ist, eine kurze Klasse für Bekanntmachungen mit Endzeit, die hide.php versteckt, eine Rolle zum Veröffentlichen zu jeder Stunde und die Erneuerung der Caches.
keywords: dringende Bekanntmachung, Warnbanner, siteweite Bekanntmachung, Startseite, Ordner für Bekanntmachungen, Cache-Block, View-Cache, statischer Cache, Layout-Block, Bereitschaft, urgent notice, alert banner, site-wide notice, front page, notices folder, hide.php, HideSettings, cache block, view cache, static cache, layouts block, on-call
type: help
---
# Das Muster für dringende Bekanntmachungen

Diese Seite richtet sich an Site-Ersteller, Administratoren und Redakteure, die eine dringende Bekanntmachung vor jeden
Besucher bringen müssen: eine Störung einer Leistung, Unwetter, eine Schließung einer Dienststelle. Exponential hat kein
eingebautes Warnbanner. Diese Seite beschreibt ein Muster aus vorhandenen Bausteinen: ein Ordner, eine kleine Klasse,
eine Template-Liste, der Cronjob `hide.php`, Rollen und Caches.

## Inhalt

1. [Die Teile](#die-teile)
2. [Die Klasse Notice](#die-klasse-notice)
3. [Das Template](#das-template)
4. [Bekanntmachungen automatisch beenden](#bekanntmachungen-automatisch-beenden)
5. [Caches](#caches)
6. [Alternative: ein Layout-Block](#alternative-ein-layout-block)

## Die Teile

| Teil | Was |
|---|---|
| Ein Ordner „Notices“ | außerhalb der Menüs, in einer Sektion, die Anonymous lesen darf |
| Eine Klasse Notice | Überschrift, Text, Schweregrad, Endzeit |
| Eine Template-Liste im Pagelayout oder auf der Startseite | zeigt Bekanntmachungen, wenn es welche gibt, sonst nichts |
| Eine Rolle | lässt das Bereitschaftsteam Bekanntmachungen anlegen und verstecken, und sonst nichts |
| `hide.php` | versteckt Bekanntmachungen nach ihrer Endzeit |

## Die Klasse Notice

| Attribut | Bezeichner | Datentyp | Erforderlich |
|---|---|---|---|
| Überschrift | `title` | Textzeile | ja |
| Text | `text` | Textabschnitt | ja |
| Schweregrad | `severity` | Auswahlliste: Information, Warnung | ja |
| Weitere Informationen | `link` | Objekt Verknüpfung (eine Seite mit Einzelheiten) | nein |
| Verstecken nach | `hide_date` | Datum und Zeit | nein |

Kurz und einfach: Eine Bekanntmachung sind ein oder zwei Sätze und ein Link zu den Einzelheiten.

## Das Template

Im Pagelayout (für jede Seite) oder im Template der Startseite:

```text
{def $notices = fetch( 'content', 'list', hash(
        'parent_node_id', $notices_node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'notice' ),
        'sort_by', array( 'published', false() ) ) )}
{if $notices|count}
<section class="site-notices" aria-label="{'Urgent notices'|i18n( 'design/<your design>' )}">
  {foreach $notices as $n}
    <p class="notice notice-{$n.data_map.severity.class_content.options[$n.data_map.severity.value[0]].name|downcase|wash}">
      <strong>{$n.name|wash}</strong> {$n.data_map.text.content|wash}
    </p>
  {/foreach}
</section>
{/if}
{undef $notices}
```

Versteckte Bekanntmachungen werden Besuchern nicht gelistet, weil Fetches die Sichtbarkeit beachten
(`ShowHiddenNodes=false`). Zeigen Sie den Schweregrad als Wort und als Farbe. Verwenden Sie `role="alert"` nicht für
Bekanntmachungen, die beim Laden der Seite schon da sind; es ist für Meldungen gedacht, die während der Benutzung
erscheinen.

## Bekanntmachungen automatisch beenden

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]=<node id of the Notices folder>
HideDateAttributeList[notice]=hide_date
```

Leeren Sie den INI-Cache. `hide.php` läuft im globalen Teil der Cronjobs; eine Bekanntmachung wird beim ersten Lauf nach
ihrer Zeit versteckt ([Cronjobs](../../../../anleitungen/cronjobs.md)). Eine Bekanntmachung vorzeitig beenden:
**Verstecken / Zeigen** in ihrem Menü.

## Caches

| Cache | Wirkung auf Bekanntmachungen | Tun Sie |
|---|---|---|
| View-Cache von Inhaltsseiten | eine Bekanntmachung im Pagelayout liegt außerhalb des View-Caches der Inhalte | in Ordnung |
| Cache-Blöcke im Pagelayout | eine zwischengespeicherte Liste von Bekanntmachungen bleibt, bis der Block abläuft | geben Sie dem Block einen kurzen Ablauf oder einen Ablauf für den Teilbaum des Ordners Notices ([View-Cache und Cache-Blöcke](../../../ersteller/website-ersteller/view-cache-und-cache-bloecke.md)) |
| Statischer Cache | statische Seiten behalten die alte Bekanntmachung, bis sie erneuert werden | nehmen Sie die Hauptseiten in `AlwaysUpdateArray[]` auf, oder erzeugen Sie neu |
| Antwort-Cache von Velocity, CDN | Kopien bis zum Ende ihrer Lebensdauer | leeren: `./console exp:velocity cache clear`; CDN-Purge |

Proben Sie: Veröffentlichen Sie eine Test-Bekanntmachung und messen Sie, wie lange es dauert, bis ein anonymer Besucher
sie überall sieht.

## Alternative: ein Layout-Block

Mit Exponential Layouts kann ein gemeinsames Layout (zum Beispiel die Kopfzeile) einen Block enthalten, den Redakteure
mit einer Bekanntmachung füllen und danach leeren ([Der Layout-Editor](../../../layout-editor.md)). Das braucht keine
Template-Arbeit, aber auch keine automatische Endzeit; jemand muss ihn entfernen.

## Verwandte Seiten

- [Nachrichten und Warnmeldungen](README.md)
- [Archiv und Ablauf](archiv-und-ablauf.md)
- [Warnmeldungen, Verfügbarkeit, Wartungsmodus und statischer Cache](../../ebenen/rettungsdienste/warnungen-verfuegbarkeit-wartung-und-statischer-cache.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
