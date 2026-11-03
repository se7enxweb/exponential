# Smarty-Templates: ein erstes .stpl, Schritt für Schritt

Dieses Tutorial ist für Template-Entwickler, die noch nie ein `.stpl` geschrieben haben. Sie beginnen mit einem eigenen
Siteaccess und enden mit einer Ordnerseite, die Smarty rendert: eine Override-Regel, die das Template auswählt, eine
Variable, Operatoren, ein Fetch der Kinder des Ordners, eine Schleife darüber mit Blättern, ein Include eines
`.stpl`-Teils und eines `.tpl`-Teils, und ein Wert, der an das Pagelayout zurückgegeben wird. Jeder Schritt endet mit
etwas, das Sie im Browser prüfen. Es dauert etwa vierzig Minuten.

Sie brauchen eine laufende Installation mit Inhalten (den mitgelieferten Demo-Inhalten oder Ihren eigenen) und einen
Ordner, der einige Kinder hat. Jeder Befehl läuft im Installationsverzeichnis; fügen Sie `--allow-root-user` hinzu,
wenn Sie als `root` angemeldet sind (die Beispiele schreiben es aus). Smarty 5 kommt mit der Installation:
`ls vendor/smarty/smarty/src/Smarty.php` druckt den Pfad. Falls nicht, lesen Sie zuerst
[Prüfen, dass Smarty verfügbar ist](smarty-templates.md#2-prüfen-dass-smarty-verfügbar-ist).

Die Seiten, die tiefer gehen:

- [Smarty-Templates: .stpl-Templates schreiben und ausführen](smarty-templates.md), die Anleitung;
- [Operator-Referenz](smarty-templates-operator-referenz.md) und [Template-Funktionen und Blöcke](smarty-templates-funktionen.md);
- [Problembehebung und FAQ](smarty-templates-problembehebung.md), wenn etwas nicht erscheint.

## 1. Ein eigener Siteaccess

Arbeiten Sie in einem eigenen Siteaccess, damit die öffentliche Site nie berührt wird. Hier heißt er `stpltest`, eine
Kopie des öffentlichen Siteaccess `site`:

```bash
cp -r settings/siteaccess/site settings/siteaccess/stpltest
```

Finden Sie heraus, welche Designs `site` verwendet:

```bash
./console exp:ini where site/DesignSettings/SiteDesign site --allow-root-user
./console exp:ini where site/DesignSettings/AdditionalSiteDesignList site --allow-root-user
```

Geben Sie dem neuen Siteaccess ein eigenes Design vor diesen. Ersetzen Sie in
`settings/siteaccess/stpltest/site.ini.append.php` den Block `[DesignSettings]` (hier verwendete `site` das Design
`media`; tragen Sie die Liste ein, die Sie gefunden haben, mit dem alten `SiteDesign` zuerst):

```ini
[DesignSettings]
SiteDesign=tutorial
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=media
AdditionalSiteDesignList[]=ezwebin
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

Legen Sie das Design-Verzeichnis an. Ein Design im Verzeichnis `design/` der Installation wird über seinen Namen
gefunden:

```bash
mkdir -p design/tutorial/override/templates/full design/tutorial/templates/parts
```

Machen Sie den Siteaccess in `settings/override/site.ini.append.php` bekannt, neben den Einträgen, die dort schon stehen:

```ini
[SiteSettings]
SiteList[]=stpltest

[SiteAccessSettings]
AvailableSiteAccessList[]=stpltest
```

Lassen Sie Besucher hinein: Bearbeiten Sie in der Administration unter **Benutzer > Rollen und Richtlinien** die Rolle
**Anonymous** und fügen Sie `stpltest` zur SiteAccess-Einschränkung ihrer Richtlinie `user/login` hinzu. Leeren Sie
dann den INI-Cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

**Prüfen.** Mit URI-Matching (`[SiteAccessSettings] MatchOrder=uri`, die Voreinstellung vieler Installationen) öffnen
Sie `https://<your host>/stpltest/`. Die Site sieht aus wie `site`: Noch ist nichts überschrieben. Mit Host-Matching
fügen Sie stattdessen eine Zeile `HostMatchMapItems[]` für einen Host des neuen Siteaccess hinzu.

## 2. Eine Override-Regel, die ein .stpl auswählt

Sagen Sie dem Siteaccess, dass er die volle Ansicht von Ordnern mit einem Template des Tutorial-Designs rendern soll.
Legen Sie `settings/siteaccess/stpltest/override.ini.append.php` an (wenn die Kopie schon eine hat, fügen Sie den Block
oben ein: Die erste passende Regel gewinnt):

```ini
<?php /* #?ini charset="utf-8"?

[folder_full_stpl]
Source=node/view/full.tpl
MatchFile=full/folder.stpl
Subdir=templates
Match[class_identifier]=folder

*/ ?>
```

`MatchFile` wird unterhalb von `override/templates/` jedes Designs des Siteaccess gesucht, also verwendet diese Regel
`design/tutorial/override/templates/full/folder.stpl`. Die Regel ist dieselbe wie für ein `.tpl`; nur die Endung von
`MatchFile` entscheidet, dass Smarty es rendert.

Schreiben Sie eine erste Fassung von `design/tutorial/override/templates/full/folder.stpl`:

```smarty
<div class="content-view-full class-folder">
    <h1>{$node.name|wash}</h1>
    <p>Rendered by Smarty {$smarty.version}.</p>
</div>
```

Eine neue Template-Datei und eine neue Override-Regel brauchen geleerte Template-Caches und gerenderte Ansichten:

```bash
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-id=content --allow-root-user
```

**Prüfen.** Öffnen Sie einen Ordner auf `stpltest`. Die Seite zeigt den Namen des Ordners in der Überschrift und die
Zeile „Rendered by Smarty 5.x.“ im gewohnten Pagelayout. Öffnen Sie denselben Ordner auf `site`: unverändert.

Von nun an ist eine Änderung an `folder.stpl` bei der nächsten Anfrage zu sehen (Smarty kompiliert eine geänderte Datei
neu, solange `[SmartySettings] CompileCheck=enabled` gilt, die Voreinstellung). Wenn der Cache der Inhaltsansicht die
Seite ausliefert, leeren Sie den Content-Cache oder schalten Sie den Ansichts-Cache im Siteaccess ab, während Sie
arbeiten (`[ContentSettings] ViewCaching=disabled` in `site.ini.append.php` von `stpltest`; schalten Sie ihn wieder
ein, wenn Sie fertig sind).

## 3. Variablen und Attribute

Eine volle Ansicht bekommt dieselben Variablen wie ihr `.tpl`: `$node` (der angezeigte Knoten), `$view_parameters`
(die Teile `/(offset)/20` der Adresse) und die anderen der Inhaltsansicht. Exponential-Objekte werden mit Smartys
Punkt-Syntax gelesen, wie in einem `.tpl`. Fügen Sie unter der Überschrift hinzu:

```smarty
    <p class="byline">
        Published {$node.object.published|l10n:'shortdatetime'}
        by {$node.object.owner.name|wash},
        {$node.children_count} items below.
    </p>
    {if isset($node.data_map.short_description) && $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
```

Was das zeigt:

- `$node.object.owner.name` geht Attribute über Objekte hinweg entlang: Jeder Schritt ist das Attribut des Objekts davor.
- `isset($node.data_map.short_description)` ist Smartys `isset`; es ist wahr, wenn das Attribut existiert und nicht
  `null` ist. Die `.tpl`-Form `is_set(...)` funktioniert auch.
- `{attribute_view_gui attribute=...}` ist die Template-Funktion eines `.tpl`, geschrieben als Smarty-Tag mit denselben
  benannten Parametern. Sie rendert das Attribut mit dem Ansichts-Template seines Datentyps (einem `.tpl` oder einem
  `.stpl`).

**Prüfen.** Die Zeile mit dem Autor zeigt das Datum in der Locale des Siteaccess, den Eigentümer und die Zahl der
Kinder. Hat die Ordnerklasse kein `short_description`, erscheint nichts weiter; das ist das `isset`.

## 4. Operatoren

Jeder Template-Operator ist verfügbar, als Smarty-Modifikator (`$value|operator:param1:param2`) oder als Funktion
(`operator(param1, param2)`). Ändern Sie den Absatz unter der Überschrift und fügen Sie einen Link zurück zum
übergeordneten Knoten hinzu:

```smarty
    <p>{'Rendered by Smarty %version.'|i18n:'design/tutorial':expvoid():(['%version' => $smarty.version])}</p>
    <p><a href={$node.parent.url_alias|ezurl}>{'Up one level'|i18n:'design/tutorial'}</a></p>
```

- `i18n` übersetzt seine Eingabe in dem Kontext, der als erster Parameter angegeben ist; der zweite Parameter ist ein
  Kommentar, mit `expvoid()` leer gelassen; der dritte gibt die Werte von `%version`. Ohne Übersetzung wird der Text so
  gezeigt, wie er ist.
- `ezurl` macht aus einem URL-Alias einen Link für den aktuellen Siteaccess, mit den Anführungszeichen darum (`href=`
  bekommt hier keine eigenen). `ezurl:'no'` gibt die Adresse ohne Anführungszeichen, `ezurl:'no':'full'` mit dem Host.
- Parameter von Modifikatoren folgen auf Doppelpunkte. `|i18n('design/tutorial')`, die Schreibweise des `.tpl`, ist in
  Smarty ein Syntaxfehler.

Wo Smarty einen Modifikator mit demselben Namen wie ein Operator hat (`count`, `implode`, `explode` ...), wird der
Operator mit dem Präfix `exp_` aufgerufen. Fügen Sie die Klassen-Identifier der Kinder hinzu, durch Kommas getrennt:

```smarty
    {$classes = $node.children|expcolumn:'class_identifier'}
    <p>Classes below: {$classes|exp_implode:', '|wash}</p>
```

`expcolumn` nimmt ein Attribut jedes Elements eines Arrays. `exp_implode` ist der Exponential-Operator `implode`; ein
einfaches `implode` wäre Smartys eigenes, in Smarty 5 veraltet. Die [Operator-Referenz](smarty-templates-operator-referenz.md)
listet jeden Operator mit der Form, die er braucht.

**Prüfen.** Der Link führt zum übergeordneten Ordner; die Klassenliste zeigt Identifier wie `article, folder`.

## 5. Ein Fetch mit Blättern

`fetch()` ruft jede Fetch-Funktion eines Moduls auf, mit ihren Parametern als Array-Literal (dem `.tpl`-`hash(...)`).
Holen Sie die Kinder zu je zehn, beginnend beim Offset in der Adresse, sortiert so, wie der Ordner es vorgibt:

```smarty
    {$page_limit = 10}
    {$children = fetch('content', 'list', ['parent_node_id' => $node.node_id,
                                           'offset' => $view_parameters.offset,
                                           'limit' => $page_limit,
                                           'sort_by' => $node.sort_array])}
    {$children_count = fetch('content', 'list_count', ['parent_node_id' => $node.node_id])}
```

`$view_parameters.offset` ist auf der ersten Seite 0 und auf den folgenden die Zahl in `/(offset)/10`.
`$node.sort_array` ist die Sortierung, die der Redakteur für den Ordner gewählt hat.

## 6. Eine Schleife

Gehen Sie mit Smartys `{foreach}` über die Kinder und rendern Sie jedes mit seiner Zeilenansicht:

```smarty
    <ul class="children">
    {foreach $children as $child}
        <li class="{if $child@iteration is odd}odd{else}even{/if}">
            {node_view_gui view='line' content_node=$child}
        </li>
    {foreachelse}
        <li>{'This folder is empty.'|i18n:'design/tutorial'}</li>
    {/foreach}
    </ul>
```

- `{foreachelse}` wird gezeigt, wenn das Array leer ist.
- `$child@iteration` zählt ab 1, `$child@index` ab 0, `$child@first` und `$child@last` sind beim ersten und beim
  letzten Element wahr.
- `{node_view_gui view='line' content_node=$child}` rendert das Kind mit der Zeilenansicht seiner Klasse, die ein
  `.tpl` oder ein von Override-Regeln gewähltes `.stpl` sein kann.
- `{foreach}` iteriert über eine Variable. Eine berechnete Quelle (ein Fetch, ein Wert mit Modifikatoren) muss zuerst
  einer Variablen zugewiesen werden, wie `$children` oben; `{foreach fetch(...) as $child}` wird mit „missing 'from'
  attribute“ abgelehnt.

**Prüfen.** Die Kinder erscheinen, abwechselnd mit den Klassen `odd` und `even`; ein leerer Ordner zeigt die Meldung.

## 7. Includes: ein .stpl-Teil und ein .tpl-Teil

Legen Sie die Blätter-Navigation in einen eigenen Teil. Legen Sie `design/tutorial/templates/parts/paging.stpl` an:

```smarty
{if $children_count > $page_limit}
    {expinclude uri='design:navigator/google.tpl'
                name='navigator'
                page_uri=$node.url_alias
                item_count=$children_count
                view_parameters=$view_parameters
                item_limit=$page_limit}
{/if}
```

und binden Sie ihn in `folder.stpl` nach der Liste ein:

```smarty
    {expinclude uri='design:parts/paging.stpl'}
```

- `{expinclude}` ist das `{include}` des Template-Systems: Die URI geht durch die Design-Ressource und die
  Override-Regeln, und das eingebundene Template darf ein `.stpl` oder ein `.tpl` sein.
- `parts/paging.stpl` wird ohne `name=` eingebunden, teilt also die Variablen von `folder.stpl` (`$children_count`,
  `$page_limit`, `$node`, `$view_parameters`) und läuft als Smarty-Subtemplate, ohne den Umweg über das
  Template-System.
- `navigator/google.tpl` ist der `.tpl`-Navigator des Designs standard. Er wird mit `name='navigator'` eingebunden,
  läuft also in einem eigenen Namensraum, wie das klassische `{include name=navigator uri=...}`; die benannten
  Parameter werden zu seinen Variablen.

**Prüfen.** Bei einem Ordner mit mehr als zehn Kindern erscheinen die Seitenzahlen unter der Liste; Seite 2 hat
`/(offset)/10` in der Adresse und zeigt die nächsten Kinder.

## 8. Einen Wert an das Pagelayout zurückgeben

Ein `.stpl` schreibt die Variablen, die es zuweist, an das Template zurück, das es gerendert hat, so wie `{def}` und
`{set}` es tun. Um dem Pagelayout einen Wert zu geben, den es aus einem globalen Namensraum liest, verwenden Sie
`{expset}` mit `scope='global'`. Viele Designs lesen die `persistent_variable` auf diese Weise (die `.tpl`-Form ist
`{set scope=global persistent_variable=...}`):

```smarty
{expset name='persistent_variable' value=['extra_menu' => false, 'left_menu' => false] scope='global'}
```

Ob das Pagelayout sie liest, hängt von Ihrem Design ab; das Design media tut es nicht, das Design `ezwebin` tut es.

## 9. Das ganze Template

`design/tutorial/override/templates/full/folder.stpl` lautet jetzt:

```smarty
<div class="content-view-full class-folder">
    <h1>{$node.name|wash}</h1>
    <p>{'Rendered by Smarty %version.'|i18n:'design/tutorial':expvoid():(['%version' => $smarty.version])}</p>
    <p><a href={$node.parent.url_alias|ezurl}>{'Up one level'|i18n:'design/tutorial'}</a></p>
    <p class="byline">
        Published {$node.object.published|l10n:'shortdatetime'}
        by {$node.object.owner.name|wash},
        {$node.children_count} items below.
    </p>
    {if isset($node.data_map.short_description) && $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
    {$classes = $node.children|expcolumn:'class_identifier'}
    <p>Classes below: {$classes|exp_implode:', '|wash}</p>

    {$page_limit = 10}
    {$children = fetch('content', 'list', ['parent_node_id' => $node.node_id,
                                           'offset' => $view_parameters.offset,
                                           'limit' => $page_limit,
                                           'sort_by' => $node.sort_array])}
    {$children_count = fetch('content', 'list_count', ['parent_node_id' => $node.node_id])}
    <ul class="children">
    {foreach $children as $child}
        <li class="{if $child@iteration is odd}odd{else}even{/if}">
            {node_view_gui view='line' content_node=$child}
        </li>
    {foreachelse}
        <li>{'This folder is empty.'|i18n:'design/tutorial'}</li>
    {/foreach}
    </ul>
    {expinclude uri='design:parts/paging.stpl'}
</div>
```

Beachten Sie die Design-Regel des Projekts für echte Designs: kein `style=""` und kein `<style>` in Templates; Klassen
wie `odd` und `even` bekommen ihr Aussehen aus den Stylesheets des Designs.

## 10. Wenn etwas schiefgeht

Schalten Sie die Debug-Ausgabe für Ihre eigene Adresse ein (siehe [die Exp-Debug-Leiste](../funktionen/6.0/exp-debug-leiste.md)).
Ein `.stpl`, das fehlschlägt, wird durch nichts ersetzt, und der Grund steht in der Debug-Ausgabe mit dem Label `smarty`
und auf der Seite als HTML-Kommentar `<!-- smarty: ... -->` dort, wo das Template gewesen wäre. Machen Sie absichtlich
einen Fehler: Ändern Sie `{/foreach}` in `{/forech}` und laden Sie neu. Der Kommentar lautet
`<!-- smarty: rendering .../folder.stpl failed: Syntax error in template ... -->`. Jede Meldung und ihre Behebung:
[Problembehebung und FAQ](smarty-templates-problembehebung.md).

## 11. Aufräumen

Wenn Sie fertig sind, entfernen Sie den Siteaccess aus `SiteList[]` und `AvailableSiteAccessList[]`, entfernen Sie
`stpltest` aus der Einschränkung der Rolle Anonymous, und löschen Sie `settings/siteaccess/stpltest/` und
`design/tutorial/` (oder behalten Sie das Design und verwenden Sie es). Leeren Sie den INI- und den Template-Cache.

## Wie es weitergeht

- Sehen Sie, wie schnell es ist, und warum: [Smarty-Templates: Leistung (Geschwindigkeit)](smarty-templates-leistung.md).
- Ein ganzes Design portieren, statt es zu schreiben: [Ein Design von .tpl nach .stpl migrieren](smarty-templates-migrieren.md).
- Seiten schnell halten: [Caching und Leistung](smarty-templates-caching.md).
- Ausgabe, die sicher ist: [Sicherheit und Escaping](smarty-templates-sicherheit.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
