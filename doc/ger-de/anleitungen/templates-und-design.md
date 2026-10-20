# Templates und Design: das Aussehen einer Seite ändern

Diese Anleitung richtet sich an Site-Entwickler, die ändern möchten, wie Seiten aussehen. Sie erfahren, welches Design Ihre Site verwendet, sehen, welche Template-Datei einen Teil einer Seite erzeugt hat, ändern ihn mit einem Override, das Sie rückgängig machen können, prüfen die Syntax und stellen die Administrationsoberfläche auf das Design `admin4` um. Arbeiten Sie die Anleitung von oben nach unten durch; jeder Teil endet mit etwas, das Sie prüfen können. Sie dauert etwa fünfzehn Minuten.

Jeder Befehl wird aus dem Wurzelverzeichnis der Installation ausgeführt, dem Verzeichnis, das `index.php` enthält. Wenn Sie als `root` angemeldet sind, fügen Sie `--allow-root-user` hinzu (die Beispiele schreiben es aus). Sie benötigen eine laufende Site: [Erste Schritte](erste-schritte.md) bringt Sie in wenigen Minuten dorthin.

## 1. Wie ein Template gefunden wird

Eine Seite wird aus `.tpl`-Dateien erzeugt (die Template-Sprache ist in Teil 6 beschrieben). Exponential sucht ein Template, zum Beispiel `node/view/full.tpl`, in dieser Reihenfolge und verwendet die erste Datei, die es findet:

1. die **Override-Templates**, deren Regeln in `override.ini` auf die Seite passen (eine Klasse, ein Knoten, eine Sektion ...), zu finden in `design/<design>/override/templates/` des Site-Designs und der Extensions, die Design-Dateien mitbringen;
2. das einfache Verzeichnis `templates/` des Site-Designs, `design/<design>/templates/`;
3. dieselben Verzeichnisse der zusätzlichen Designs;
4. das Design `standard`, `design/standard/templates/`, das die Rückfallebene ist und das Sie nie bearbeiten.

Ein **Design** ist ein Verzeichnis mit `templates/`, `override/templates/`, `stylesheets/`, `images/` und `javascript/`. Es liegt in `design/` der Installation oder in `extension/<name>/design/<design>/`. Eine Extension, die ein Design mitbringt, wird mit `DesignExtensions[]=<name>` in ihrer `settings/design.ini.append.php` eingeschaltet.

## 2. Herausfinden, welches Design Sie verwenden

```bash
./console exp:ini where site/DesignSettings/SiteDesign eng --allow-root-user
```

Ersetzen Sie `eng` durch den Siteaccess, den Sie betrachten (die Ordner von `settings/siteaccess/`). Erwartete Ausgabe beim ausgelieferten Multisite-Setup:

```
site.ini/DesignSettings/SiteDesign (load order of siteaccess eng)
 1. settings/site.ini                                            scope default
      SiteDesign=admin
 2. settings/siteaccess/eng/site.ini.append.php                  scope siteaccess:eng
      SiteDesign=media
In effect:
      SiteDesign=media
```

Die letzte Zeile ist Ihr Design. Seine Dateien liegen in `design/media/` oder in einer Extension, hier `extension/sevenx_themes_media/design/media/`. Um die anderen Designs eines Siteaccess zu sehen:

```bash
./console exp:ini get site/DesignSettings/AdditionalSiteDesignList eng --allow-root-user
```

Referenz: [Ladereihenfolge der Extensions](../../eng-us/features/6.0/extension-loading-order.md), [Zusätzliche Extension-Verzeichnisse](../../eng-us/features/6.0/additional-extension-directories.md).

## 3. Sehen, welches Template welches Markup geschrieben hat

Schalten Sie die Template-Pfad-Kommentare ein. Sie sind für die Entwicklung gedacht: Sie fügen jedem Template Markup hinzu, lassen Sie sie daher nie für einen Siteaccess im Produktivbetrieb oder für Feeds eingeschaltet.

```bash
./console exp:ini set site/TemplateSettings/ShowTemplatePathComments enabled siteaccess:eng --allow-root-user
php bin/php/cache.php clear --tag=template --allow-root-user
```

Laden Sie eine Seite der Site neu, öffnen Sie „Quelltext anzeigen“ und suchen Sie nach `START`. Erwartet: Jeder Teil ist eingerahmt, zum Beispiel

```html
<!-- START design/media/templates/pagelayout/footer.tpl -->
...
<!-- STOP design/media/templates/pagelayout/footer.tpl -->
```

Hat ein Override geantwortet, folgt der ursprüngliche Name in Klammern: `<!-- START .../full/article.tpl (node/view/full.tpl) -->` bedeutet „die Datei `article.tpl` hat die Anfrage `node/view/full.tpl` beantwortet“. Schalten Sie die Kommentare mit demselben Befehl und `disabled` wieder aus, und leeren Sie erneut den Template-Cache.

Wenn Sie sich lieber durchklicken, finden Sie denselben Schalter in der Debug-Leiste: [Exp-Debug-Leiste](../../eng-us/features/6.0/exp-debug-bar.md), Reiter Einstellungen, Gruppe templates. Details: [Template-Pfad-Kommentare](../../eng-us/features/6.0/template-path-comments.md).

## 4. Eine Änderung mit einem Override vornehmen

Sie gestalten einen Artikel (ein Objekt) um, ohne irgendetwas anderes anzurühren. Ein Override besteht aus zwei Teilen: der Template-Datei und einer Regel, die festlegt, wann es gilt.

### Per Klick (keine Dateien, mit einem Klick rückgängig zu machen)

1. Öffnen Sie in der Administrationsoberfläche **Design > Template-Editor**.
2. Öffnen Sie `node/view/full.tpl` Ihres Designs.
3. Drücken Sie **Neues Überschreib-Template**, wählen Sie *Kopie des Standard-Templates*, geben Sie die **Objekt ID** des Artikels ein und drücken Sie **Erstellen**.
4. Bearbeiten Sie das neue Template, fügen Sie eine sichtbare Zeile wie `<p>Override works</p>` hinzu und speichern Sie.
5. Laden Sie den Artikel auf der Site neu. Die Zeile ist da, und bei eingeschalteten Pfad-Kommentaren sehen Sie dafür `START ... (node/view/full.tpl)`.
6. Um die Änderung rückgängig zu machen, kreuzen Sie das Override in der Liste an und drücken Sie **Ausgewählte entfernen**: Die Regel und die Datei werden gelöscht.

Der Editor schreibt `settings/siteaccess/<siteaccess>/override.ini.append.php` für Sie, ordnet Overrides per Drag-and-drop und bewahrt vor dem Schreiben eine Kopie der Datei auf. Siehe [Template-Editor](../../eng-us/features/6.0/template-editor-overrides.md).

### Über Dateien (was Sie in der Versionsverwaltung halten)

1. Legen Sie das Template im Override-Verzeichnis Ihres Designs an:

   ```bash
   mkdir -p design/media/override/templates/full
   cp design/standard/templates/node/view/full.tpl design/media/override/templates/full/my_article.tpl
   ```

   Verwenden Sie den Designnamen, den Sie in Teil 2 ermittelt haben. Liegt Ihr Design in einer Extension, legen Sie die Dateien stattdessen dort an.
2. Fügen Sie eine Regel in `settings/siteaccess/eng/override.ini.append.php` hinzu (oder in `settings/override/override.ini.append.php` für jeden Siteaccess):

   ```ini
   <?php /* #?ini charset="utf-8"?

   [full_my_article]
   Source=node/view/full.tpl
   MatchFile=full/my_article.tpl
   Subdir=templates
   Match[object]=1234

   */ ?>
   ```

   `Source` ist das zu ersetzende Template, `MatchFile` Ihre Datei relativ zu `override/templates/`, `Subdir` ist immer `templates`, `Match[...]` sind die Bedingungen (`class_identifier`, `node`, `object`, `section`, `parent_node`, `viewmode` und weitere; alle müssen zutreffen). Regeln ohne `Priority` kommen nach Regeln mit einer; die niedrigste Zahl wird zuerst versucht.
3. Für einen neuen Template-Pfad müssen der Override- und der Template-Cache einmal geleert werden:

   ```bash
   php bin/php/cache.php clear --id=template-override --allow-root-user
   php bin/php/cache.php clear --tag=template --allow-root-user
   php bin/php/cache.php clear --tag=content --allow-root-user
   ```

   Erwartet: Jeder Befehl endet mit `PASS`.
4. Laden Sie den Artikel neu; der Pfad-Kommentar zeigt Ihre Datei.

Spätere Änderungen an derselben Datei erfordern kein Leeren, solange der Template-Cache ausgeschaltet ist; ist er eingeschaltet, führen Sie die Zeile mit `--tag=template` erneut aus. Welches von mehreren passenden Overrides gewinnt und wie die Overrides einer Extension eingeordnet werden: [Template-Editor](../../eng-us/features/6.0/template-editor-overrides.md#reorder-by-drag-and-drop-september-2026) und [Ladereihenfolge der Extensions](../../eng-us/features/6.0/extension-loading-order.md). Eine Extension kann auch die Templates eines Moduls überschreiben: [Modul-Override durch Extensions](../../eng-us/features/6.0/extension-module-override.md).

## 5. Die Syntax prüfen, bevor Sie in den Browser schauen

```bash
php bin/php/eztemplatecheck.php -seng design/media/override/templates/full/my_article.tpl --allow-root-user
```

Erwartet: `Template file valid: design/media/override/templates/full/my_article.tpl`. Statt einer Datei kann auch ein ganzes Design oder Verzeichnis angegeben werden (`design/`). Ein Syntaxfehler wird mit dem Template und der Zeile gemeldet; auch die Warnungen des Compilers auf der Seite nennen Template und Zeile.

## 6. Die Template-Sprache in zehn Minuten

Ein Template ist HTML mit `{ ... }`-Ausdrücken. Kommentare sind `{* ... *}`.

| Sie schreiben | Bedeutung |
|---|---|
| `{$node.name\|wash}` | Eine Variable ausgeben. `wash` maskiert HTML; verwenden Sie es für alles, was ein Benutzer eingegeben hat. |
| `{def $items = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 5 ) )}` | Eine Variable deklarieren (`{def}` einmal je Template; `{set $items = ...}` ändert sie später; `{undef $items}` gibt sie frei). `fetch` liest Daten, `hash` baut ein Array benannter Werte. |
| `{foreach $items as $child}` ... `{/foreach}` | Schleife. `{delimiter}` und `{sequence}` helfen bei Trennzeichen und abwechselnd gefärbten Zeilen. |
| `{if $node.depth\|gt( 2 )}` ... `{else}` ... `{/if}` | Bedingung. Operatoren werden als Pipes geschrieben: `gt`, `lt`, `eq`, `and`, `or`, `not`. |
| `{cond( $a, 'yes', 'no' )}` | Bedingung innerhalb eines Ausdrucks. |
| `<a href={$child.url_alias\|ezurl}>` | Einen Link bauen, der den Siteaccess berücksichtigt. `ezurl` wird in einem Attribut ohne Anführungszeichen geschrieben. |
| `<img src={'logo.png'\|ezimage}>` | Ein Bild in `images/` des Designs finden. |
| `{'Search'\|i18n( 'design/media/pagelayout' )}` | Eine übersetzte Zeichenkette. |
| `{attribute_view_gui attribute=$node.data_map.title}` | Ein Inhaltsattribut mit dem Template seines Datentyps anzeigen. |
| `{node_view_gui content_node=$child view='line'}` | Einen Knoten mit seiner `node/view/line.tpl` und dem Override dafür anzeigen. |
| `{include uri='design:parts/header.tpl' title=$node.name}` | Ein anderes Template einbinden; `design:` bezeichnet einen Pfad innerhalb des Designs. |

Ein vollständiges, kleines Template zum Ausprobieren im Override aus Teil 4 (es listet die Kinder des Elternknotens des Artikels auf, höchstens zehn):

```
<h1>{$node.name|wash}</h1>
{def $siblings = fetch( 'content', 'list', hash( 'parent_node_id', $node.parent_node_id, 'limit', 10 ) )}
<ul>
{foreach $siblings as $sibling}
    <li><a href={$sibling.url_alias|ezurl}>{$sibling.name|wash}</a></li>
{/foreach}
</ul>
{undef $siblings}
```

Erwartet: die Überschrift, gefolgt von einer Liste von Links. Referenzlisten aller Operatoren: [Template-Operatoren für Zeichenketten](../../eng-us/features/6.0/string-template-operators.md), [Template-Operatoren für Rollen und Richtlinien](../../eng-us/features/6.0/role-and-policy-template-operators.md).

## 7. Templates debuggen

Wenn eine Seite falsch ist, arbeiten Sie diese Liste von oben nach unten ab:

| Frage | Tun Sie Folgendes |
|---|---|
| Welche Datei hat es geschrieben? | Template-Pfad-Kommentare (Teil 3). |
| Warum diese Datei und nicht meine? | Prüfen Sie die Regel: `Source`, `MatchFile` und `Match[...]`, und leeren Sie dann `template-override` und `template`. `./console exp:ini where override/<block>/MatchFile eng --allow-root-user` listet die Dateien auf, die Ihren Block definieren. |
| Stimmt die Syntax? | `eztemplatecheck.php` (Teil 5). |
| Welche Templates, wie lange, wie viele Abfragen? | Die [Exp-Debug-Leiste](../../eng-us/features/6.0/exp-debug-bar.md): Reiter Templates, Zeiten, SQL. Die Debug-Ausgabe selbst schalten Sie für sich mit `[DebugSettings] DebugOutput=enabled` und `DebugByUser`/`DebugByIP` ein; siehe [Verbesserungen der Debug-Ausgabe](../../eng-us/features/6.0/debug-output-improvements.md). |
| Die Seite ändert sich nach einer Bearbeitung nicht | Leeren Sie je nachdem, was Sie geändert haben: [Betrieb einer Site, Teil 2](betrieb-einer-website.md). Ein geändertes Stylesheet erfordert zusätzlich das Leeren des Template-Block-Caches; siehe diese Anleitung. |
| Eine Zeit lang veraltete Seiten | Inhalts-View-Cache: `php bin/php/cache.php clear --tag=content --allow-root-user`. |

### Ein Operator-Parameter ohne Standardwert

Ein Template-Operator listet seine Parameter mit einem Standardwert für jeden, den ein Template weglassen darf. Wenn
ein Operator (oft einer aus Ihrer eigenen Extension) für einen Parameter keinen Standardwert angibt und ein Template
den Parameter weglässt, erhält der Operator dafür `null`, und die Debug-Ausgabe nennt den Operator und den Parameter:

```
Warning: eZTemplate:eZTemplateOperatorElement @ design/site/templates/full/article.tpl:12[4]
Operator 'my_operator' has no default value for parameter 'length'
```

Die Warnung wird je Operator und Parameter einmal pro Anfrage geschrieben, gleich wie oft der Operator läuft. Für ein
kompiliertes Template (`[TemplateSettings] TemplateCompile=enabled`, der Standard) wird einmal beim Kompilieren
gewarnt, mit `(compiling <template>)` nach dem Text; die Ausführung des kompilierten Templates prüft nicht erneut und
kostet daher nichts. Um die Warnung erneut zu sehen, leeren Sie den Template-Cache. Beheben Sie das Problem in
`namedParameterList()` des Operators: Geben Sie dem Parameter ein `'default' => ...`. Bei einem Upgrade ändert sich
nichts außer der neuen Warnung; Dank an Mugo Web, deren LoveStack-Commit 0bb0bac618 die Idee lieferte.

### Bearbeiten-Links in der Liste der verwendeten Templates

Mit `[TemplateSettings] ShowUsedTemplates=enabled` listet die Debug-Ausgabe die Templates der Seite auf, jedes mit
einem „Bearbeiten“-Symbol, das das Template unter **Setup > Templates** (`visual/templateedit`) des aktuellen
Siteaccess öffnet. Auf einem öffentlichen Siteaccess ist dieser Editor meist nicht der Ort, an dem Sie bearbeiten
möchten; `[DebugSettings] UsedTemplateEditUrl` legt den Link fest, als `sprintf()`-Format:

| Platzhalter | Steht für |
|---|---|
| `%1$s` | die Adresse von `visual/templateedit` im aktuellen Siteaccess, gefolgt von der Template-Datei |
| `%2$s` | den aktuellen Siteaccess |
| `%3$s` | die Template-Datei, zum Beispiel `design/standard/templates/node/view/full.tpl` |

```ini
# settings/override/site.ini.append.php
[DebugSettings]
# empty (the default): %1$s/(siteAccess)/%2$s, the link the list always had
# open the template in the administration interface's editor instead:
UsedTemplateEditUrl=/admin/visual/templateedit/%3$s/(siteAccess)/%2$s
```

Erwartet: Laden Sie eine Seite mit eingeschalteter Debug-Ausgabe neu; die „Bearbeiten“-Symbole der Liste zeigen auf
`/admin/visual/templateedit/design/.../template.tpl/(siteAccess)/<siteaccess>`. Ein Format, das `sprintf()` nicht
verwenden kann (ein `%4$s`, ein einzelnes `%`), ergibt den üblichen Link und eine Warnung. Ein Upgrade ändert nichts:
Die Einstellung ist leer.
Dies folgt dem LoveStack-Commit fb20bee0d2 von Mugo Web, mit Dank.

## 8. Die Administrationsoberfläche auf admin4 umstellen

`admin4` ist ein vollständiges Admin-Design mit einem hellen und einem dunklen Modus, das weder `admin3` noch `admin2` oder `admin` voraussetzt. Setzen Sie es nur für den Admin-Siteaccess:

```bash
./console exp:ini set site/DesignSettings/SiteDesign admin4 siteaccess:admin --allow-root-user
php bin/php/cache.php clear --all --allow-root-user
```

Erwartet: Beide Befehle enden mit `PASS`; laden Sie die Administrationsoberfläche neu, und die Kopfzeile trägt nach dem Suchfeld einen Hell/Dunkel-Umschalter. Um zurückzukehren, setzen Sie `SiteDesign` auf `admin3` und leeren Sie erneut die Caches. Um es zuerst auszuprobieren, ohne etwas zu ändern, öffnen Sie den Siteaccess `admintest_admin4`. Alles darüber, einschließlich der Frage, wie Sie eigene Styles darauf aufsetzen (`design/admin4/stylesheets/admin4.css`, keine Cascade Layers): [Das Design admin4](../../eng-us/features/6.0/admin4-design.md); das frühere responsive Design: [admin3](../../eng-us/features/6.0/admin3-responsive-admin.md).

## 9. Site-Daten in Templates

Ein Template kann eine Zeile von `ezsite_data` (die Name-Wert-Paare, die unter Setup > Site-Daten aufgeführt sind)
lesen, sobald ihr Name in `site.ini` freigegeben ist:

```ini
# settings/override/site.ini.append.php
[SiteDataSettings]
TemplateReadable[]=my-flag
```

```
{def $flag = fetch( 'content', 'site_data', hash( 'name', 'my-flag' ) )}
{if $flag}{$flag.value|wash}{/if}
{undef $flag}
```

Der Fetch liefert die Zeile (`.name`, `.value`) oder `false` für einen Namen, der nicht aufgeführt oder nicht
gespeichert ist; Namen sind vollständige Namen, keine Muster. Er liest nur: Kein Template kann eine Zeile schreiben.
Die Liste ist in jeder Installation leer, ob neu oder aktualisiert, sodass nichts lesbar ist, bis Sie einen Namen
hinzufügen; die Abfrage eines nicht aufgeführten Namens hinterlässt einen Hinweis in der Debug-Ausgabe. PHP-Code
verwendet weiterhin `eZSiteData::fetchByName()`, und `eZSiteData::createNew( $name, $value )` speichert eine Zeile
mit einem Aufruf. Dies stammt aus LoveStack (mugoweb/ezpublish-legacy, Commits `55029bd473` und `a679a13176`), mit Dank
an Mugo Web; Exponential ergänzt die Freigabeliste.

## 10. Einen Wert für eine URL kodieren: `rawurlencode` und `rawurldecode`

`urlencode` macht aus einem Leerzeichen ein `+`, wie es ein Formular tut. Für einen Teil eines Pfads oder einen Wert,
den ein anderes System nach den Regeln von RFC 3986 liest, verwenden Sie `rawurlencode`, das `%20` ergibt;
`rawurldecode` ist die Umkehrung:

```
<a href={concat( '/search/', $term|rawurlencode )|ezurl}>{$term|wash}</a>
{'x%20y%2Bz'|rawurldecode}   {* x y+z *}
```

Beide sind in `settings/template.ini [PHP] PHPOperatorList[]` aufgeführt, in jeder Installation, ob neu oder
aktualisiert; `urlencode` und `urldecode` bleiben unverändert. Die Liste ist über die Einstellungsseite und aus Code
nur lesbar, da sie PHP-Funktionen nennt. Mit Dank an Mugo Web (LoveStack, Commit `627004e5c4`).

## 11. Eine Layout-Ansicht erkennen: der Schlüssel `layout`

Eine Seite, die über `layout/set/<layout>/...` angezeigt wird (zum Beispiel eine Druckansicht), wird mit dem
Override-Schlüssel `layout` gerendert, der auf dieses Layout gesetzt ist, und ein Override kann darauf passen:

```ini
# settings/siteaccess/<site>/override.ini.append.php
[full_article_print]
Source=node/view/full.tpl
MatchFile=full/article_print.tpl
Subdir=templates
Match[class_identifier]=article
Match[layout]=print
```

Seit 6.0.15 hat jede Knotenansicht diesen Schlüssel, als leeren Wert, wenn kein Layout verwendet wird, sodass ein
Knoten-Template ihn immer abfragen kann, ohne zu prüfen, ob er existiert:

```
{if $DesignKeys:used.layout|eq( 'print' )}...{/if}
```

Overrides, die `layout` nicht nennen, passen wie bisher. In PHP setzt `eZTemplateDesignResource::instance()->setKeyValue(
$key, $value, $override = true )` einen Schlüssel; mit `$override = false` behält ein bereits gesetzter Schlüssel
seinen Wert, und so setzt die Knotenansicht `layout` auf `''`, ohne das Layout von `layout/set` zu ersetzen. Mit
Dank an Mugo Web (LoveStack, Commit `856a95e240`).

## 12. Antwort-Header von Fehlerseiten

Die Fehlerseiten (eine Seite, die nicht existiert, ein verweigerter Zugriff) senden die Statuszeile aus
`settings/error.ini [HTTPError-<code>] HTTPName`. Weitere Header können je Code hinzugefügt werden, zum Beispiel, damit
ein Proxy die Seite „nicht gefunden“ fünf Minuten lang vorhält:

```ini
# settings/override/error.ini.append.php
[HTTPError-404]
HeaderList[]
HeaderList[Cache-Control]=public, must-revalidate, max-age=300
```

Die Header folgen den Statuszeilen und werden mit der Seite im Inhalts-View-Cache gespeichert, sodass eine
zwischengespeicherte Fehlerseite sie erneut sendet. Ein Name, der kein gültiger Header-Name ist, oder ein Wert mit
einem Zeilenumbruch wird mit einer Warnung in der Debug-Ausgabe ausgelassen. `HeaderList[Status]=<text>` steht für
`HTTPName`, wenn dieses nicht gesetzt ist. Die Liste ist in jeder Installation leer, ob neu oder aktualisiert, sodass
sich nichts ändert, bis Sie einen Eintrag hinzufügen. Mit Dank an Mugo Web
(LoveStack, Commit `97690e9c93`).

## Siehe auch

- [Das Inhaltsmodell und das Bearbeiten von Inhalten](inhaltsmodell-und-bearbeitung.md): die Klassen und Attribute, die Ihre Templates anzeigen.
- [Betrieb einer Site](betrieb-einer-website.md): Caches, Cronjobs und Reparaturen.
- [Bereitstellung](bereitstellung.md): Ihr Design auf einen Server bringen.
- Funktionsseiten: [Template-Editor](../../eng-us/features/6.0/template-editor-overrides.md), [Template-Pfad-Kommentare](../../eng-us/features/6.0/template-path-comments.md), [Icon-Themes in Extensions](../../eng-us/features/6.0/icon-themes-in-extensions.md), [Ausgeblendete Admin-Reiter](../../eng-us/features/6.0/hidden-admin-tabs.md).
- Upgrade-Hinweise: [Debug-Leiste](../../eng-us/bc/6.0/debug-bar.md), [Einstellungen für die seitenweise Anzeige](../../eng-us/bc/6.0/pagination-settings.md).
- [Extensions](extensions.md): Ihr Design in einer eigenen Extension mitbringen.
