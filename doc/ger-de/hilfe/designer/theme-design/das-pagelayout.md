---
title: Das Pagelayout
audience: designer
category: theme-design
order: 60
summary: Das Template, das jede Seite einrahmt: wie der Kernel es aufruft, was es ausgeben muss, ein minimales funktionierendes Pagelayout, wie echte Designs es in Teile aufteilen und wie ein Modul ein anderes anfordern kann.
keywords: Pagelayout, pagelayout.tpl, Seitenrahmen, Seitenhülle, module_result, Layout-Template, head, body, Kopfbereich, Fußbereich, Master-Template, Basis-Template, page frame, page shell, layout template, header, footer, master template, base template
featured: true
type: help
---
# Das Pagelayout

Diese Seite ist für Designer, die den äußeren Rahmen einer Website bauen oder ändern: `<html>`, `<head>`, Kopfbereich, Navigation und
Fußbereich um jede Seite. In Exponential ist dieser Rahmen ein einziges Template, `pagelayout.tpl`. Die Seite beantwortet: wann es läuft,
was es enthalten muss, wie Sie eines beginnen und wie die Designs dieser Installation ihres ordnen.

## Inhalt

1. [Wann das Pagelayout läuft](#wann-das-pagelayout-läuft)
2. [Was es ausgeben muss](#was-es-ausgeben-muss)
3. [Ein minimales Pagelayout](#ein-minimales-pagelayout)
4. [Es in Teile aufteilen](#es-in-teile-aufteilen)
5. [Den aktuellen Knoten holen](#den-aktuellen-knoten-holen)
6. [Seiten ohne den Rahmen](#seiten-ohne-den-rahmen)
7. [Pagelayout und der Layout-Editor](#pagelayout-und-der-layout-editor)
8. [Fehler, die Sie vermeiden sollten](#fehler-die-sie-vermeiden-sollten)

## Wann das Pagelayout läuft

Jede Webanfrage führt zuerst eine Modul-View aus (zum Beispiel `content/view/full/42`, `user/login`, `content/search`). Die View erzeugt ihr
HTML, das **Modulergebnis**. Dann setzt der Kernel (`kernel/private/classes/ezpkernelweb.php`) einige Variablen und holt
`design:pagelayout.tpl` über die Design-Kaskade. Das Pagelayout setzt `$module_result.content` irgendwo in sein Markup, und das Ergebnis ist
die Seite.

Das Pagelayout läuft also **nach** dem Inhalt. Es kann nicht ändern, was die View gerendert hat, aber es kann lesen, was die View in
`$module_result` über sich gemeldet hat (siehe [Variablen des Pagelayouts](pagelayout-variablen.md)).

## Was es ausgeben muss

- Ein vollständiges HTML-Dokument: `<!DOCTYPE html>`, `<html lang="...">`, `<head>`, `<body>`.
- `{$module_result.content}` genau einmal. Ohne es ist die Seite leer.
- Die Stylesheets und Skripte des Designs über ezjscore (`ezcss_load`, `ezscript_load`), sodass auch ausgegeben wird, was ein
  Inhalts-Template mit `ezcss_require` / `ezscript_require` angefordert hat.
- Für Sprachen von rechts nach links `dir="rtl"` am `<html>` (der Kernel stellt `$site.text_direction` bereit).

## Ein minimales Pagelayout

Ein vollständiges, funktionierendes Pagelayout für ein neues Design. Es verwendet kein Inline-CSS oder -JavaScript und funktioniert daher
auch unter einer strengen Content Security Policy.

```tpl
{* design/mytheme/templates/pagelayout.tpl *}
<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {def $page_path = first_set( $module_result.title_path, $module_result.path )
         $page_last = cond( is_array( $page_path ), $page_path|extract_right( 1 ), array() )}
    <title>{if $page_last|count}{$page_last.0.text|wash} - {/if}{$site.title|wash}</title>
    {undef $page_path $page_last}
    {ezcss_load( array( 'mytheme.css' ) )}
    {ezscript_load( array( 'mytheme.js' ) )}
</head>
<body>
    <a class="skip-link" href="#main">{'Skip to content'|i18n( 'design/mytheme' )}</a>
    <header class="site-header">
        <a class="site-name" href={'/'|ezurl}>{$site.title|wash}</a>
    </header>
    <main id="main">
        {$module_result.content}
    </main>
    <footer class="site-footer">
        {'Powered by Exponential'|i18n( 'design/mytheme' )}
    </footer>
</body>
</html>
```

Der Titel nimmt den letzten Eintrag des Pfads der Seite (jeder Eintrag ist ein Hash mit `text` und `url`), dann den Namen der Website. Die
meisten Designs bauen den Titel in einem eigenen Teil-Template; die `pagelayout/head/title.tpl` des Designs media liest den Namen des Knotens
oder seinen Meta-Titel und einen lesbaren Text für Fehlerseiten. Der Operator `|wash` maskiert jeden Wert, der aus Inhalten oder
Einstellungen stammt ([Wash und Maskierung](../vorlagen/wash-und-maskierung.md)).

## Es in Teile aufteilen

Echte Pagelayouts wachsen. Beide öffentlichen Designs dieser Installation halten `pagelayout.tpl` kurz und binden Teile ein:

```tpl
<head>
    <meta charset="utf-8">
    {include uri='design:pagelayout/head/title.tpl'}
    {include uri='design:pagelayout/head/meta.tpl'}
    {include uri='design:pagelayout/head/link.tpl'}
    {include uri='design:pagelayout/head/style.tpl'}
    {include uri='design:pagelayout/head/script.tpl'}
</head>
<body>
{include uri='design:pagelayout/accessibility_links.tpl'}
...
```

(aus `extension/sevenx_themes_media/design/media/templates/pagelayout.tpl`; das Design starter macht dasselbe in
`extension/cjw_themes_jumper/design/starter/templates/pagelayout.tpl`).

Vorteile:

- Jeder Teil lässt sich von einem weiter oben in der Kaskade stehenden Design ersetzen, ohne das ganze Pagelayout zu kopieren;
- die Kommentare mit Template-Pfaden zeigen, welcher Teil welches Markup erzeugt hat;
- ein Teil lässt sich für sich in einen `{cache-block}` einschließen (Menüs, Fußbereiche), siehe [Caching für Designer](caching-fuer-designer.md).

Mit `{def}` im Pagelayout definierte Variablen sind in den Teilen sichtbar, die es einbindet. Das Design starter definiert deshalb seine
Seitenvariablen oben mit dem Präfix `pl_` und dokumentiert sie in einem Kommentar.

## Den aktuellen Knoten holen

Das Pagelayout bekommt keine Variable `$node`. Ist die Seite eine Inhaltsansicht, steht die Knoten-id in
`$module_result.content_info.node_id`; holen Sie den Knoten, wenn Sie ihn brauchen:

```tpl
{def $current_node = false()}
{if and( is_array( $module_result.content_info ), is_set( $module_result.content_info.node_id ) )}
    {set $current_node = fetch( 'content', 'node', hash( 'node_id', $module_result.content_info.node_id ) )}
{/if}
```

Beide öffentlichen Designs hier machen genau das. Ziehen Sie die schon in `content_info` stehenden Werte vor (`class_identifier`,
`url_alias`, `node_depth`, `parent_node_id`), wenn sie genügen: Sie kosten kein fetch.

## Seiten ohne den Rahmen

- Eine View kann ein anderes Pagelayout anfordern, indem sie in PHP `$Result['pagelayout']` setzt (der Kernel liest es), oder keines
  (`false`). AJAX- und JSON-Views tun das meist.
- Das Design media verwendet eigene View-Modi für Ausgabe ohne Rahmen: `payload` gibt nur `{$module_result.content}` aus, `layout_preview`
  einen reduzierten Rahmen. Ein solcher View-Modus ist ein günstiger Weg, dem Layout-Editor oder Skripten eine Seite ohne Kopf- und
  Fußbereich zu geben.
- Der Einrichtungsassistent verwendet `site.ini [SetupSettings] PageLayout=setup_pagelayout.tpl`.

## Pagelayout und der Layout-Editor

Auf Websites, die den Layout-Editor verwenden, fragt das Pagelayout den Resolver, welches Layout für die Seite gilt, und zeichnet es; passt
keine Regel, fällt es auf `$module_result.content` zurück:

```tpl
{def $layout = fetch( 'explayouts', 'resolve_layout', hash() )}
{if $layout}
    {include uri='design:explayouts/layout.tpl' layout=$layout}
{else}
    {$module_result.content}
{/if}
```

(Muster aus `doc/eng-us/bc/6.0/LAYOUTS.md`, „How rendering actually works“). Das Design starter verwendet für Vollansichten
`fetch( 'explayouts', 'resolve_layout_for_node', hash( 'node_id', ... ) )`. Siehe [Layouts für Designer](layouts-fuer-designer.md).

## Fehler, die Sie vermeiden sollten

- **Ein `<style>`- oder `<script>`-Block im Pagelayout.** Verschieben Sie den Code in eine Datei und laden Sie sie mit ezjscore. Eine strenge
  Content Security Policy blockiert Inline-Code ([CSP-sicheres Design](csp-sicheres-design.md)).
- **Denselben Knoten in mehreren Teilen holen.** Holen Sie ihn einmal im Pagelayout und verwenden Sie die Variable in den Teilen.
- **Die CSP-Nonce in einem Cache-Block ausgeben.** `csp_nonce()` ändert sich bei jeder Anfrage; eine zwischengespeicherte Kopie trägt eine
  veraltete (`settings/site.ini`, Kommentar zu `[ContentSecurityPolicy]`).
- **Den Namen der Website oder URLs fest eintragen.** Verwenden Sie `$site.title` und den Operator `ezurl`, damit der Rahmen unter jedem
  Siteaccess und Pfadpräfix funktioniert.

## Verwandte Seiten

- [Variablen des Pagelayouts](pagelayout-variablen.md)
- [Der Aufbau des Design-Verzeichnisses](aufbau-des-design-verzeichnisses.md)
- [Landmarks und Überschriften](landmarks-und-ueberschriften.md)
- [Stylesheets laden](stylesheets-laden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
