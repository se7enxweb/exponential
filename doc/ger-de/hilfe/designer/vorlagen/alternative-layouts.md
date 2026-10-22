---
title: Alternative Layouts (Druck, Popup und eigene)
audience: designer, developer
category: templates
order: 728
summary: Jede Seite mit dem Adresspräfix layout/set/<name> in einem anderen Pagelayout zeigen - die Layouts, die layout.ini definiert, wie Sie mit PageLayout, ContentType, UseAccessPass und UseFullUrl eigene hinzufügen, der Override-Schlüssel layout und ein Drucklink.
keywords: alternatives Layout, Drucklayout, Druckansicht, druckfreundlich, Popup, Vollbild, layout/set, alternate layout, print layout, print_pagelayout.tpl, popup, fullscreen, layout.ini, PageLayout, ContentType, UseAccessPass, UseFullUrl, Match[layout], printer friendly
type: help
---
# Alternative Layouts (Druck, Popup und eigene)

Diese Seite richtet sich an Designer, die denselben Inhalt in einem anderen Rahmen brauchen: eine druckfreundliche Seite,
eine Seite für ein kleines Popup-Fenster oder ein nacktes Fragment für einen Ajax-Aufruf. Setzen Sie `layout/set/<name>/`
vor eine beliebige Adresse, und die Seite wird statt mit `pagelayout.tpl` mit dem Pagelayout dargestellt, das `layout.ini`
für `<name>` nennt.

## Inhalt

- [Die mitgelieferten Layouts](#die-mitgelieferten-layouts)
- [Auf ein Layout verlinken](#auf-ein-layout-verlinken)
- [Ein eigenes Layout hinzufügen](#ein-eigenes-layout-hinzufügen)
- [Andere Templates innerhalb des Layouts](#andere-templates-innerhalb-des-layouts)
- [Was Sie beachten sollten](#was-sie-beachten-sollten)

## Die mitgelieferten Layouts

`settings/layout.ini` definiert drei, jedes mit einem Pagelayout-Template in `design/standard/templates`:

| Name | `PageLayout` | Gibt aus |
|---|---|---|
| `print` | `print_pagelayout.tpl` | den Inhalt und die Copyright-Zeile, mit den Stylesheets und Skripten der Website, ohne Navigation und ohne den Drucklink im Kopf |
| `popup` | `popup_pagelayout.tpl` | den Inhalt und die Copyright-Zeile nur mit dem Kern-Stylesheet, für ein kleines Fenster |
| `fullscreen` | `fullscreen_pagelayout.tpl` | den Inhalt und die Copyright-Zeile ohne die Menüs des Seitenrahmens |

`/layout/set/print/content/view/full/96` und `/layout/set/print/news/my-article` zeigen dieselbe Seite im Drucklayout. Ihr
Design kann die drei Templates überschreiben wie jedes andere.

## Auf ein Layout verlinken

```
<a href={concat( 'layout/set/print/', $node.url_alias )|ezurl} rel="nofollow">
    {'Print this page'|i18n( 'design/mysite/full' )}
</a>
```

Solange eine Seite über `layout/set/<name>` angezeigt wird, behalten mit `ezurl` erzeugte Links das Präfix, der Besucher
bleibt also im Layout, während er weiterklickt. Setzen Sie `UseAccessPass=false` für ein Layout, bei dem das nicht gewünscht
ist.

## Ein eigenes Layout hinzufügen

Fügen Sie `layout.ini` in einer Erweiterung oder einer Override-Datei einen Block hinzu, der nach dem Layout benannt ist:

```ini
# extension/mysite/settings/layout.ini.append.php
[plain]
PageLayout=plain_pagelayout.tpl
UseAccessPass=false

[feed]
PageLayout=feed_pagelayout.tpl
ContentType=application/xml
```

| Einstellung | Wirkung |
|---|---|
| `PageLayout` | das Pagelayout-Template, gesucht in `templates/` der Designkette |
| `ContentType` | wird als Header `Content-Type` gesendet, mit dem Zeichensatz der Website ergänzt |
| `UseAccessPass` | `false` hält `layout/set/<name>` aus den Links heraus, die auf der Seite erzeugt werden |
| `UseFullUrl` | `true` macht die auf der Seite erzeugten Links absolut, mit dem Hostnamen |

Ein minimales Pagelayout gibt mindestens das Ergebnis der Ansicht aus:

```
{* design/mysite/templates/plain_pagelayout.tpl *}
<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}">
<head>
    <meta charset="utf-8" />
    <title>{$site.title|wash}</title>
</head>
<body>
{$module_result.content}
</body>
</html>
```

Ein Name, den `layout.ini` nicht definiert, ergibt eine leere Seite und in der Debug-Ausgabe den Fehler „No such layout
style“.

## Andere Templates innerhalb des Layouts

Die Ansicht setzt den Override-Schlüssel `layout` auf den Namen des Layouts, eine Regel kann einem Knoten also nur in einem
Layout ein eigenes Template geben:

```ini
# settings/siteaccess/<site>/override.ini.append.php
[article_print]
Source=node/view/full.tpl
MatchFile=print/article.tpl
Subdir=templates
Match[class_identifier]=article
Match[layout]=print
```

Ohne eine solche Regel wird im Drucklayout das normale Vollansichts-Template verwendet; oft genügt ein Druck-Stylesheet
([Druck-Stylesheets](../theme-design/druck-stylesheets.md)).

## Was Sie beachten sollten

- **Caching.** Der Name des Layouts ist Teil des View-Cache-Schlüssels, die Druckfassung und die normale Seite werden also
  getrennt gecacht, und beide werden geleert, wenn sich der Inhalt ändert.
- **Suchmaschinen.** Druckfassungen verdoppeln Inhalte. Kennzeichnen Sie die Links mit `rel="nofollow"` und geben Sie dem
  Drucklayout ein `<meta name="robots" content="noindex" />`.
- **Keine eigenen Rechte.** `layout/set` steht allen offen; die Seite darin prüft wie üblich ihre eigenen Rechte.
- **Nicht für die Administration.** Die Layouts ändern den Rahmen der öffentlichen Website; die Designs der Administration
  haben ihre eigenen Pagelayouts.

## Verwandte Seiten

- [Das Pagelayout](../theme-design/das-pagelayout.md)
- [Pagelayout-Variablen](../theme-design/pagelayout-variablen.md)
- [Override-Match-Schlüssel](override-match-schluessel.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Templates von Modulansichten](../../template-sprache/templates-von-modulansichten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
