---
title: Menüs und Navigation
audience: website-creator, admin
category: website-creation
order: 150
summary: Wie Menüs auf einer Exponential-Website entstehen - aus dem Inhaltsbaum, eingeschränkt durch Klassenlisten in menu.ini, mit Brotkrumennavigation, verborgenen Elementen, Sortierung und einem Template zum Kopieren - und wie Sie sie schnell halten.
keywords: Menü, Navigation, Hauptmenü, linkes Menü, Brotkrumennavigation, Pfad, verborgen, Priorität, Sortierung, Website-Ersteller, menu, navigation, top menu, left menu, breadcrumb, path, menu.ini, TopIdentifierList, LeftIdentifierList, fetch content list, hidden, priority, sort, website creator
type: help
---
# Menüs und Navigation

Diese Seite richtet sich an Website-Ersteller, die die Navigation einer Website bauen. Auf einer Exponential-Website
sind die Menüs keine separate Liste, die Sie pflegen: Sie entstehen aus dem Inhaltsbaum. Ein Ordner unter der Wurzel
wird zu einem Menüeintrag, seine Unterelemente werden zu einem Untermenü. Sie entscheiden, welche Klassen als
Menüeinträge zählen und wie sie sortiert werden.

![Das Hauptmenü der öffentlichen Website, hervorgehoben: ein Eintrag pro Ordner unterhalb der Wurzel der Website](../../../../eng-us/help/images/creators/site-navigation.webp)

## Inhalt

1. [Menüs entstehen aus dem Inhaltsbaum](#menüs-entstehen-aus-dem-inhaltsbaum)
2. [Welche Klassen erscheinen: menu.ini](#welche-klassen-erscheinen-menuini)
3. [Ein Template für das Hauptmenü](#ein-template-für-das-hauptmenü)
4. [Reihenfolge, Verbergen und Links an andere Stellen](#reihenfolge-verbergen-und-links-an-andere-stellen)
5. [Brotkrumennavigation](#brotkrumennavigation)
6. [Menüs schnell halten](#menüs-schnell-halten)
7. [Checkliste](#checkliste)

## Menüs entstehen aus dem Inhaltsbaum

| Sie wollen | Sie tun |
|---|---|
| einen neuen Eintrag im Hauptmenü | einen Ordner (oder eine andere Menüklasse) direkt unter dem Wurzelordner anlegen |
| ein Untermenü | Unterelemente unter diesem Ordner anlegen |
| einen Eintrag aus dem Menü nehmen, die Seite aber online lassen | ihn verbergen oder eine Klasse verwenden, die nicht in den Menülisten steht |
| eine andere Reihenfolge | die Sortierung des Elternelements oder die Prioritäten der Unterelemente ändern |

Die Redaktion ändert die Navigation also, indem sie Inhalte ändert, ohne dass ein Entwickler beteiligt ist
([Den Inhaltsbaum strukturieren](den-inhaltsbaum-strukturieren.md)).

## Welche Klassen erscheinen: menu.ini

Die auf ezwebin basierenden Designs lesen die Listen von `[MenuContentSettings]` in `menu.ini`. Die
`settings/menu.ini` des Kernels liefert `folder`, `feedback_form`, `gallery` und `forum` mit;
`extension/ezwebin/settings/menu.ini.append.php` fügt weitere hinzu (`documentation_page`, `forums`, `event_calendar`,
`multicalendar`, `link`, `blog` ...). Setzen Sie Ihre eigenen pro Siteaccess:

```ini
# settings/siteaccess/<site>/menu.ini.append.php
[MenuContentSettings]
TopIdentifierList[]
TopIdentifierList[]=folder
TopIdentifierList[]=landing_page
LeftIdentifierList[]
LeftIdentifierList[]=folder
LeftIdentifierList[]=article
```

Die leere Zeile zuerst löscht die geerbte Liste. `[SelectedMenu]` und `AvailableMenuArray[]` (TopOnly, LeftOnly,
DoubleTop, LeftTop) wählen, welche Menü-Templates ein Design anzeigt.

## Ein Template für das Hauptmenü

Wenn Sie Ihr eigenes Design schreiben, ist ein Hauptmenü ein Fetch der Unterelemente des Wurzelordners, beschränkt auf
die Menüklassen:

```text
{def $root_node_id = ezini( 'NodeSettings', 'RootNode', 'content.ini' )
     $menu_items = fetch( 'content', 'list', hash(
         'parent_node_id', $root_node_id,
         'class_filter_type', 'include',
         'class_filter_array', ezini( 'MenuContentSettings', 'TopIdentifierList', 'menu.ini' ),
         'sort_by', fetch( 'content', 'node', hash( 'node_id', $root_node_id ) ).sort_array ) )
     $path_ids = array()}
{foreach $module_result.path as $path_item}
  {if is_set( $path_item.node_id )}{set $path_ids = $path_ids|append( $path_item.node_id )}{/if}
{/foreach}
<nav aria-label="{'Main'|i18n( 'design/mysite' )}">
  <ul>
  {foreach $menu_items as $item}
    <li{if $path_ids|contains( $item.node_id )} class="current"{/if}>
      <a href={$item.url_alias|ezurl}{if $item.node_id|eq( $module_result.node_id )} aria-current="page"{/if}>{$item.name|wash}</a>
    </li>
  {/foreach}
  </ul>
</nav>
{undef $root_node_id $menu_items $path_ids}
```

Die Pfadelemente einer Inhaltsseite tragen `node_id` (`kernel/classes/eznodeviewfunctions.php`), daher markiert
`contains` den Menüeintrag, in dem sich die aktuelle Seite befindet. Vollständige, funktionierende Menüs der auf ezwebin
basierenden Designs liegen in `extension/ezwebin/design/ezwebin/templates/menu/` (`flat_top.tpl`, `flat_left.tpl`,
`double_top.tpl`).

`fetch( 'content', 'list', ... )` liefert nur Inhalte, die der Besucher lesen darf, und standardmäßig keine verborgenen
Inhalte, sodass das Menü den Rechten von selbst folgt ([Fetch-Funktionen](../anwendungsentwickler/fetch-funktionen.md)).

## Reihenfolge, Verbergen und Links an andere Stellen

- Die **Reihenfolge** kommt aus dem Sortierfeld des Elternelements (Name, Veröffentlichungsdatum, Priorität ...). Für
  eine von Hand festgelegte Reihenfolge sortieren Sie den Wurzelordner nach Priorität und setzen die Prioritäten in
  seiner Liste der Unterelemente.
- Das **Verbergen** eines Knotens nimmt ihn und seinen Teilbaum für Besucher von der Website; die Redaktion sieht ihn
  weiterhin in der Administration.
- **Ein Menüeintrag, der woandershin zeigt** (eine andere Website, eine tief im Baum liegende Seite): Verwenden Sie ein
  Element der Klasse `link` mit einem Feld `ezurl`, und lassen Sie das Line-Template auf den Wert des Feldes verlinken.

## Brotkrumennavigation

Der Pfad der aktuellen Seite steht in `$module_result.path`; jedes Element hat `text`, `url` und, bei Inhalten,
`url_alias`. Das `parts/path.tpl` des Themes simple zeigt, wie man ihn ausgibt, mit `{delimiter}` für die Trennzeichen.
Zeichnen Sie ihn als `nav` mit einem `aria-label` aus
([Barrierefreiheit für Website-Ersteller](barrierefreiheit-fuer-website-ersteller.md)).

## Menüs schnell halten

Menüs laufen auf jeder Seite. Legen Sie sie in einen `cache-block`, dessen Schlüssel alles enthalten, was die Ausgabe
ändert (die aktuelle Adresse, wenn Sie den aktiven Eintrag markieren, die Rolle des Benutzers, wenn Menüs sich nach
Rechten unterscheiden). Veröffentlichen leert den Template-Block-Cache, sodass neue Ordner ohne manuelles Leeren
erscheinen ([View-Cache und Cache-Blöcke](view-cache-und-cache-bloecke.md)).

## Checkliste

- [ ] Das Menü listet nur Klassen auf, die für die Navigation gedacht sind.
- [ ] Die Sortierung des Wurzelordners ist die gewünschte Reihenfolge.
- [ ] Die aktuelle Seite ist mit `aria-current="page"` markiert und ihr Bereich im Menü mit einer Klasse.
- [ ] Menüs liegen in einem Cache-Block mit den richtigen Schlüsseln.
- [ ] Verborgene und eingeschränkte Inhalte erscheinen nicht für Besucher (als Anonymous prüfen).

## Verwandte Seiten

- [Das Pagelayout](das-pagelayout.md)
- [Den Inhaltsbaum strukturieren](den-inhaltsbaum-strukturieren.md)
- [Klassische Menüeinstellungen](../../../anleitungen/klassische-menueeinstellungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
