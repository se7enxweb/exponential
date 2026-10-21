---
title: Das Pagelayout
audience: website-creator, developer
category: website-creation
order: 130
summary: Was pagelayout.tpl tut, welche Variablen es erhält ($module_result, $site, $current_user), ein minimales CSP-freundliches Pagelayout als Ausgangspunkt, Cache-Blöcke und die Fehler, die eine Website langsam oder leer machen.
keywords: Pagelayout, Rahmen, Kopfbereich, Fußbereich, Layout-Template, Cache-Block, Website-Ersteller, pagelayout, pagelayout.tpl, module_result, site, current_user, cache-block, ezpagedata, frame, header, footer, layout template, CSP, website creator
type: help
---
# Das Pagelayout

Diese Seite richtet sich an Website-Ersteller, die ein Design schreiben oder anpassen. Jede Seite eines Siteaccess ist
das Ergebnis einer Modulansicht (für Inhalte: `content/view`), eingebettet in ein einziges Template, `pagelayout.tpl`.
Das Pagelayout ist der Rahmen: der `<head>`, der Kopfbereich, die Menüs, der Fußbereich. Die Ausgabe der Ansicht wird
darin platziert.

## Inhalt

1. [Wie eine Seite zusammengesetzt wird](#wie-eine-seite-zusammengesetzt-wird)
2. [Was das Pagelayout erhält](#was-das-pagelayout-erhält)
3. [Ein minimales Pagelayout](#ein-minimales-pagelayout)
4. [Cache-Blöcke](#cache-blöcke)
5. [Skripte ohne Inline-Code](#skripte-ohne-inline-code)
6. [Alternative Pagelayouts](#alternative-pagelayouts)
7. [Fehler, die Sie vermeiden sollten](#fehler-die-sie-vermeiden-sollten)

## Wie eine Seite zusammengesetzt wird

1. Die Anfrage erreicht `index.php`; der Siteaccess wird anhand der Adresse gewählt.
2. Die Modulansicht läuft. Für eine Inhaltsseite rendert `content/view/full/<node>` das Template `node/view/full.tpl`
   (oder seinen Override) und legt das HTML in `$module_result.content` ab.
3. Das Pagelayout der Design-Kette des Siteaccess wird gerendert, mit dem Ergebnis der Ansicht in seinen Variablen.

## Was das Pagelayout erhält

| Variable | Enthält |
|---|---|
| `$module_result.content` | das HTML der Ansicht: geben Sie es dort aus, wo der Hauptinhalt hingehört |
| `$module_result.path` | die Elemente des Pfads (Brotkrumennavigation), jeweils mit `text`, `url` und, bei Inhalten, `url_alias` |
| `$module_result.node_id` | der Knoten einer Inhaltsseite |
| `$module_result.content_info` | Klassen-, Sektions-, Knoten- und Objektdaten einer Inhaltsseite |
| `$module_result.uri` | die Adresse der Ansicht |
| `$site.title` | der Seitentitel, den der Kernel gebildet hat |
| `$current_user` | der Benutzer, der die Seite ansieht (`is_logged_in`, `contentobject_id` ...) |

Die auf `ezwebin` basierenden Designs ergänzen `ezpagedata()` (aus `extension/ezwebin/autoloads/`), das fertige Werte
wie `top_menu`, `left_menu`, `show_path`, `css_classes` und `persistent_variable` zurückgibt; das mitgelieferte
Pagelayout von `sevenx_themes_simple` verwendet es.

## Ein minimales Pagelayout

Speichern Sie es als `design/<your design>/templates/pagelayout.tpl` in Ihrer Design-Erweiterung:

```text
<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$site.title|wash}</title>
  {ezcss_load( ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ) )}
</head>
<body>
  <a class="skip-link" href="#main">{'Skip to content'|i18n( 'design/mysite' )}</a>
  <header>
    <a href={'/'|ezurl}>{ezini( 'SiteSettings', 'SiteName' )|wash}</a>
    {include uri='design:parts/top_menu.tpl'}
  </header>
  <nav aria-label="{'Breadcrumb'|i18n( 'design/mysite' )}">
    {foreach $module_result.path as $item}
      {if $item.url}<a href={cond( is_set( $item.url_alias ), $item.url_alias, $item.url )|ezurl}>{$item.text|wash}</a> /{else}{$item.text|wash}{/if}
    {/foreach}
  </nav>
  <main id="main">
    {$module_result.content}
  </main>
  <footer>{include uri='design:parts/footer.tpl'}</footer>
  {ezscript_load( ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ) )}
</body>
</html>
```

Hinweise:

- `$module_result.content` ist bereits HTML; wenden Sie niemals `wash` darauf an.
- `ezcss_load` und `ezscript_load` übernehmen die Listen aus Ihrer `design.ini` und packen sie
  ([Stylesheets und Skripte](stylesheets-und-skripte.md)).
- Der Sprunglink und die Landmarks (`header`, `nav`, `main`, `footer`) sind der Anfang einer barrierefreien Website
  ([Barrierefreiheit für Website-Ersteller](barrierefreiheit-fuer-website-ersteller.md)).
- Prüfen Sie die Datei mit `php bin/php/eztemplatecheck.php -s<site> design/<design>/templates/pagelayout.tpl`.

## Cache-Blöcke

Das Pagelayout läuft bei jeder Anfrage, deshalb gehören aufwendige Teile (aus Fetches gebaute Menüs) in einen
`cache-block`. Das mitgelieferte Theme simple umschließt den größten Teil seines Pagelayouts mit einem solchen Block,
mit der Adresse, dem Zustand des Warenkorbs und dem Benutzer als Schlüssel:

```text
{cache-block keys=array( $module_result.uri, $current_user.contentobject_id )}
  {include uri='design:parts/top_menu.tpl'}
{/cache-block}
```

Die Schlüssel müssen alles enthalten, wovon die Ausgabe des Blocks abhängt: Ein Menü, das sich je Benutzer
unterscheidet, braucht den Benutzer in seinen Schlüsseln, ein Block, der die aktuelle Seite zeigt, braucht die Adresse.
Ohne `keys` ist ein Block für jede Seite gleich. Die Caches werden beim Veröffentlichen geleert
([View-Cache und Cache-Blöcke](view-cache-und-cache-bloecke.md)).

## Skripte ohne Inline-Code

Öffentliche Siteaccesses können unter einer Content Security Policy laufen (`[ContentSecurityPolicy] Mode` in
`site.ini`; siehe [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)). Halten Sie das
Pagelayout dafür bereit: kein `<script>` mit Code, kein `onclick=`, kein `style=""`. Übergeben Sie Daten an Skripte so,
wie es das Theme simple tut, als JSON-Datenblock, den eine Skriptdatei liest:

```text
{set-block variable=$page_conf}{json_encode( hash( 'root', '/'|ezurl( no ) ) )}{/set-block}
<script type="application/json" id="mysite-page">{$page_conf|explode( '<' )|implode( '<' )}</script>
{ezscript( array( 'mysite_page.js' ) )}
```

Ein Block mit `type="application/json"` ist Daten, kein Code, deshalb blockiert ihn die Policy nicht.

## Alternative Pagelayouts

Eine Ansicht kann ein anderes Pagelayout anfordern, zum Beispiel ein Druck- oder Pop-up-Layout; `[TemplateSettings]` in
`site.ini` und das Modul layout (`/layout/set/<name>/...`) wählen es aus. Um in einem Template eine Layout-Ansicht zu
erkennen, verwenden Sie den Schlüssel `layout`
([11. Eine Layout-Ansicht erkennen](../../../anleitungen/templates-und-design.md#11-eine-layout-ansicht-erkennen-der-schlüssel-layout)).

## Fehler, die Sie vermeiden sollten

| Fehler | Ergebnis | Stattdessen |
|---|---|---|
| Fetches ganzer Teilbäume im Pagelayout ohne Cache-Block | jede Seite langsam | Cache-Block mit den richtigen Schlüsseln |
| Ein Cache-Block ohne den Benutzer in seinen Schlüsseln um "Hallo, <name>" | der Name eines Besuchers wird anderen angezeigt | `$current_user.contentobject_id` zu den Schlüsseln hinzufügen oder den Teil außerhalb lassen |
| Inline-`<script>` und `onclick` | unter einer Content Security Policy blockiert | Skriptdateien und Datenblöcke |
| Benutzertext ohne `wash` ausgeben | Cross-Site-Scripting | überall `|wash` |
| Das Pagelayout des Themes direkt bearbeiten | beim nächsten Update verloren | Ihre eigene Design-Erweiterung |

## Verwandte Seiten

- [Template-Overrides](template-overrides.md)
- [Menüs und Navigation](menues-und-navigation.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
