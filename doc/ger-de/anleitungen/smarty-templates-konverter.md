# Smarty-Templates: der Konverter und die Portierung eines Designs

Diese Anleitung richtet sich an Site-Bauer und Template-Entwickler, die ein ganzes Design in Smarty haben möchten, oder
ein Smarty-Design zurück in `.tpl`. Die Konvertierungsbefehle des Befehlsraums `exp:template:convert` erledigen die
Arbeit:

- `./console exp:template:convert:tpl-to-stpl` macht aus jedem `.tpl` eines Designs ein `.stpl`, das dieselbe Ausgabe
  rendert, meldet, was es nicht exakt konvertieren konnte, und schreibt die Override-Regeln, mit denen ein Siteaccess
  die konvertierten Templates verwendet (Teile 1 bis 5);
- `./console exp:template:convert:stpl-to-tpl` macht das Umgekehrte: Jedes `.stpl` wird zu einem `.tpl` (Teil 6).

Beide sind der allgemeine Befehl `./console exp:template:convert` mit festgelegten Engines (`--from-engine=tpl
--to-engine=stpl` und umgekehrt). Jede Option, jede Ausgabezeile, die Exit-Codes und die Markierung, die von Hand
geänderte Dateien schützt, werden einmal für alle Engines in
[Templates zwischen Template-Engines konvertieren](template-konvertierung.md) erklärt; diese Anleitung handelt von
Smarty: was die Konverter schreiben, ihre Hinweise und der Siteaccess, der ein konvertiertes Design ausliefert.

Sie führen die Konvertierung aus, lesen ihre Hinweise, prüfen das Ergebnis und richten einen Siteaccess ein, der das
konvertierte Design ausliefert, mit der Portierung des Media-Designs (516 Templates) und einem Siteaccess `smarty` als
durchgearbeitetem Beispiel. Planen Sie eine Stunde für die Einrichtung ein und so lange, wie die Prüfungen Ihres
Designs dauern.

Lesen Sie zuerst [Smarty-Templates: .stpl-Templates schreiben und ausführen](smarty-templates.md); die
[Anleitung zu den Operatoren](smarty-templates-operatoren.md) erklärt die Operatorformen, die der Konverter schreibt.

## 1. Den Konverter ausführen

```bash
./console exp:template:convert:tpl-to-stpl -s <siteaccess> \
    --from=<design directory with the .tpl templates> \
    [--to=<design directory for the .stpl templates>] \
    [--file=<one template>] [--override-ini=<file to write>] [--dry-run] [--force] --allow-root-user
```

`php bin/php/exptemplateconverttpltostpl.php` mit denselben Optionen ist derselbe Befehl. Die Arbeit erledigt
`expTemplateDesignConverter` (`lib/templatebridge/classes/`) mit `expSmartyConverter` (`lib/smarty/classes/`) für jedes
Template; [Teil 7 der Konvertierungsanleitung](template-konvertierung.md#7-aus-php-und-die-konverter-registrierung)
zeigt, wie Sie ihn aus PHP aufrufen.

| Option | Bedeutung |
|---|---|
| `-s <siteaccess>` | der Siteaccess, der das Design heute verwendet. Seine Erweiterungen entscheiden, welche Operatoren und Template-Funktionen es gibt und wie jeder Operator seine Eingabe nimmt; seine `override.ini` ist die, die `--override-ini` kopiert. |
| `--from` | das zu lesende Design-Verzeichnis, zum Beispiel `extension/sevenx_themes_media/design/media` |
| `--to` | das zu schreibende Design-Verzeichnis (Standard: `--from`, sodass jedes `.stpl` neben sein `.tpl` geschrieben wird). Jedes `*.tpl` unterhalb von `--from` (in `templates/` wie in `override/templates/`) wird zum `*.stpl` an derselben Stelle unterhalb von `--to`. |
| `--file` | nur ein Template konvertieren: ein Pfad unterhalb von `--from` (`templates/pagelayout.tpl`) oder, ohne `--from`, eine eigenständige Datei, die daneben geschrieben wird (oder nach `--to`, dann die Zieldatei) |
| `--override-ini` | die `override.ini.append.php`, die für einen Siteaccess geschrieben wird, der das `--to`-Design verwendet (Teil 4) |
| `--dry-run` | zeigen, was geschrieben würde (`WRITE`, `REPLACE`, `SKIP`); es wird nichts geschrieben |
| `--force` | auch ein `.stpl` ersetzen, das von Hand geändert oder geschrieben wurde (siehe "Von Hand geänderte Dateien" unten) |

Führen Sie ihn im Wurzelverzeichnis der Installation aus. Das Media-Design, in ein eigenes Smarty-Design konvertiert:

```bash
./console exp:template:convert:tpl-to-stpl -s site \
    --from=extension/sevenx_themes_media/design/media \
    --to=extension/sevenx_themes_media/design/smarty_media \
    --override-ini=var/tmp/smarty-override.ini.append.php --allow-root-user
```

Erwartete Ausgabe: eine Zeile `WRITE <file>` für jedes geschriebene Template, eine Zeile `NOTE <template>: <note>` für
jedes Konstrukt, das nicht exakt konvertiert werden konnte (und für jeden Fehler, den der Parser des Template-Systems in
einer Quelle findet), dann:

```
PASS wrote var/tmp/smarty-override.ini.append.php: 74 rules pointed at .stpl, 444 rules for the design's templates
PASS tpl->stpl 516 templates: 516 written, 0 unchanged, 0 skipped, 0 errors; 126 notes in 58 templates
```

Jedes Ergebnis wird geprüft, bevor es geschrieben wird: Ein `.stpl` muss mit Smarty 5 kompilieren (wenn Smarty
installiert ist). Ein Template, das nicht gelesen, konvertiert, geprüft oder geschrieben werden kann, ist eine Zeile
`ERROR <template>: ...`, für es wird nichts geschrieben, und der Befehl endet mit `FAIL ...` und Exit-Code 1. Falsche
Optionen enden mit `FAIL ...` und Exit-Code 2 (`FAIL --from must be a design directory: ...`). Führen Sie ihn noch einmal
aus, und die Zusammenfassung sagt `516 unchanged`: Eine Datei, deren Inhalt sich nicht ändern würde, wird nicht
angefasst.

### Von Hand geänderte Dateien

Jede Datei, die der Befehl schreibt, endet mit einem Markierungskommentar in der letzten Zeile:

```
{* exp:template:convert tpl->stpl md5:<checksum> from templates/pagelayout.tpl -- generated; a file changed by hand is not replaced without --force *}
```

Die Prüfsumme ist die von allem vor der Markierung. Trifft der Befehl auf ein Ziel, das schon existiert, ersetzt er es
nur, wenn die Markierung da ist und noch zum Inhalt passt, also wenn niemand die Datei geändert hat, nachdem sie
geschrieben wurde. Andernfalls gibt er aus

```
SKIP templates/pagelayout.tpl: extension/sevenx_themes_media/design/smarty_media/templates/pagelayout.stpl was changed after it was generated; --force replaces it
SKIP override/templates/content/view/embed/embed_file.stpl: extension/sevenx_themes_media/design/media/override/templates/content/view/embed/embed_file.tpl has no conversion marker (written by hand); --force replaces it
```

und lässt die Datei in Ruhe (die Zusammenfassung zählt sie als übersprungen; der Exit-Code bleibt 0). In der
Media-Portierung wurde `templates/pagelayout.stpl` nach der Konvertierung von Hand geändert (die ganze Seite wird in
einem Cache-Block gehalten, siehe [Caching und Leistung](smarty-templates-caching.md)), sodass eine erneute Konvertierung
des Designs sie behält. Die Markierung ist ein Kommentar ganz am Ende der Datei, sie druckt also in keiner der beiden
Template-Sprachen etwas, und sie wird vor dem Konvertieren aus einer Quelle entfernt. Ein Editor, der nach der
Markierung einen Zeilenumbruch anfügt, lässt die Datei als von Hand geändert gelten, was sicher ist.

Führen Sie ihn mit `-s` auf einem Siteaccess aus, in dem jede Erweiterung aktiv ist, die das Design braucht: Eine
Template-Funktion, die der Konverter nicht kennt, wird gemeldet (`unknown function {explblock}: it prints nothing, as in
the .tpl`) und weggelassen. Ein Operator, den der Konverter nicht kennt, wird mit dem Präfix `exp_` geschrieben und so,
als läse er seine Eingabe; das läuft noch, wenn der Operator zur Renderzeit existiert, aber ein Operator, der nur seine
Parameter liest, bekäme dann einen ersten Parameter `null`.

## 2. Was er schreibt

Der Konverter parst jedes `.tpl` mit dem eigenen Parser des Template-Systems, sieht also den Baum, den das
Template-System ausführen würde, mit bereits angewendeten Leerraumregeln, und schreibt jeden Knoten als Smarty:

| `.tpl` | `.stpl` |
|---|---|
| Text | derselbe Text; ein `{`, auf das kein Leerraum folgt, wird zu `{ldelim}` |
| `{* comment *}` | nichts (der Parser verwirft Kommentare; eine Leerzeile kann bleiben) |
| `{$node.data_map.title.content}`, `{$list[$i]}` | dasselbe: Punktzugriffe, Klammerzugriffe für berechnete Schlüssel |
| ein Attribut eines Ausdrucks | `(expression)\|expattr:'name'` |
| `$#name`, `$#Namespace:name` | `expvar('name', '')`, `expvar('name', 'Namespace')` |
| `$:item` in `{section name=Items}` | `$Items__item` |
| `$x\|op(a, b)` | `$x\|op:a:b`, oder `$x\|exp_op:a:b`, wo Smarty den Namen schon hat |
| `op(a, b)` ohne Pipe | `op(a, b)` für einen Operator, der seine Parameter liest, `op(null, a, b)` für einen, der seine Eingabe liest |
| `$x\|first_set($y)`, `$x\|cond(...)` | `first_set($y)`, `cond(...)`: die Eingabe wird weggelassen, da das `.tpl` sie ignoriert |
| `hash('a', 1)`, `array(1, 2)` | `['a' => 1]`, `[1, 2]` |
| `true()`, `false()` | `true`, `false` |
| ein leerer Parameter | `expvoid()` |
| `{if}`, `{elseif}`, `{else}`, `{while}`, `{break}` | dasselbe; `{continue}` und `{skip}` werden zu `{continue}` |
| `{def $a = 1}` | `{$a = 1}` |
| `{set $a = 1}` | `{$a = 1}`, im aktuellen Namensraum (`$Items__a` innerhalb einer benannten Section) |
| `{set}` oder `{def}` mit einem anderen `scope=` als relative, oder `name=` | `{expset name='a' value=1 scope='global' namespace='...'}` |
| `{undef $a}`, `{undef}` | `{$a = null}`, `{expundefall}` |
| `{let a=1}...{/let}` | `{$a = 1}...{$a = null}` |
| `{default a=1}...{/default}` | `{if !isset($a)}{$a = 1}{/if}...` |
| `{foreach $list as $k => $v}` | dasselbe; mit `offset`, `max` oder `reverse` ist die Quelle `(...)\|expslice:offset:max:reverse` |
| ein `{foreach}` über einen Ausdruck | `{$__loop1 = expression}{foreach $__loop1 as $v}` |
| `sequence_array=` und `sequence_var=` | `{$__sequence1 = ...}` und `{$row = $__sequence1\|expsequence:$v@index}` in der Schleife |
| `{delimiter}...{/delimiter}` | `{if !$v@first}...{/if}` am Anfang der Schleife |
| `{for 1 to 5 as $i}` | `{foreach exprange(1, 5) as $i}` (zählt ab- wie aufwärts, wie das `.tpl`) |
| `{do}...{/do while ...}` | eine `{while}`-Schleife mit einem Flag |
| `{section loop=$items}` | `{$__loop1 = ($items)\|expsectionloop:offset:max:reverse}{foreach $__loop1 as $key => $item}` mit `$index`, `$number` (und `$sequence`), mit dem Namen der Section als Präfix, wenn sie einen hat |
| `{section show=...}` ohne `loop` | `{if ...}`; `{section-else}` wird zu `{else}` |
| `{section-else}` in einer Schleife | `{foreachelse}` |
| `{section-exclude}`, `{section-include}` | ein Überspring-Flag, das von jedem `match` gesetzt wird |
| `{switch match=...}{case match=...}`, `{case in=...}` | eine `{if}`/`{elseif}`-Kette auf `$__switch1`: `==` oder `in_array` (mit `expcolumn` für `key=`) |
| `{set-block variable=x}` (relative oder `scope=root`) | `{capture assign=x}...{/capture}` |
| `{set-block scope=global ...}`, `{set-block name=...}` | `{expsetblock variable='x' scope='global'}...{/expsetblock}` |
| `{append-block}` | `{expappendblock ...}` |
| `{cache-block ...}` | `{expcacheblock id=<n> ...}`, pro Datei nummeriert |
| `{run-once}` | `{exprunonce id=<n>}`, pro Datei nummeriert |
| `{include uri=... a=$b}` | `{expinclude uri=... a=$b}`; die URI behält ihren `.tpl`-Namen |
| `{literal}...{/literal}` | sein Text, mit geschweiften Klammern als `{ldelim}` geschrieben |
| `{ldelim}`, `{rdelim}` | dasselbe |
| jede andere Template-Funktion | ein Tag desselben Namens, oder ein Block, wenn sie einen Rumpf hat (`{explblock}...{/explblock}`) |
| eine unbekannte Funktion | nichts, mit einem Hinweis: Das `.tpl` druckt dafür auch nichts |

Smarty verwirft den Zeilenumbruch direkt nach einem Tag, das nichts ausgibt (`{if}`, `{foreach}`, eine Zuweisung, ein
Block). Der Konverter schreibt nach jedem solchen Tag einen Zeilenumbruch, sodass das `.stpl` lesbar bleibt und die
Zeilenumbrüche ausgibt, die das Template-System ausgegeben hätte. Deshalb stehen Tags in konvertierten Dateien in
eigenen Zeilen:

```smarty
{if $block.values.title}
    {if $block.values.tag|ne:''}
{$tag = $block.values.tag}
{else}
{$tag = concat('h', $block.values.level)}
{/if}
    <{$tag} class="section-title text-center">{$block.values.title|wash}</{$tag}>
    {$tag = null}
{/if}
```

aus

```
{if $block.values.title}
    {if $block.values.tag|ne('')}{def $tag = $block.values.tag}{else}{def $tag = concat('h', $block.values.level)}{/if}
    <{$tag} class="section-title text-center">{$block.values.title|wash}</{$tag}>
    {undef $tag}
{/if}
```

(`templates/explayouts/block/title/centered.tpl` des Media-Designs und sein `.stpl`.)

## 3. Seine Hinweise, und was nach dem Konvertieren zu prüfen ist

Jeder `NOTE` nennt das Template und was nicht exakt war:

| Hinweis | Was zu tun ist |
|---|---|
| `unknown function {x}: it prints nothing, as in the .tpl` | eine Erweiterung, die `x` registriert, ist im `-s`-Siteaccess nicht aktiv, oder das `.tpl` hat einen Fehler; konvertieren Sie mit dem richtigen Siteaccess oder entfernen Sie den Aufruf |
| `function {x}: the name is not a Smarty tag name` | eine Funktion mit einem Bindestrich im Namen (`debug-log` ...): Es wird nichts geschrieben; entfernen oder ersetzen Sie sie |
| `{if} without a condition (a parse error in the .tpl): it prints nothing, as in the .tpl` | korrigieren Sie das `.tpl` und konvertieren Sie erneut |
| `condition missing` | ein `{elseif}` oder eine Schleife ohne Bedingung: `false` wird geschrieben |
| `{x} outside of its loop or switch` | ein `{delimiter}`, `{case}` oder `{section-else}` an der falschen Stelle: verworfen |
| `{set-block} without a variable` | der Rumpf wird als einfache Ausgabe geschrieben |
| `a constant value was expected` | ein `scope=`, `name=` oder ähnlicher Parameter, der keine Konstante ist |
| `parameter name 'x' is not a Smarty name` | der Parameter wird aus dem Tag weggelassen |
| `hash() with an odd number of parameters` | der letzte Schlüssel hat keinen Wert: Korrigieren Sie das `.tpl` |
| `attribute lookup without a value`, `unknown element type`, `unknown node type` | ein Konstrukt, das der Konverter nicht lesen kann: Konvertieren Sie diesen Teil von Hand |

Prüfen Sie dann die Dinge, die der Konverter absichtlich beibehält, weil das `.tpl` sie so gemacht hat:

- **`first_set` und `cond` mit einer Pipe.** `{$x|first_set($y)}` wird zu `{first_set($y)}`, weil das `.tpl` `$x`
  ignoriert. Wenn der Autor "`$x`, oder `$y` als Standard" meinte, hatte das `.tpl` einen Fehler; schreiben Sie
  `first_set($x, $y)`.
- **Bloße Wörter sind Strings.** Ein `.tpl` liest einen Bezeichner ohne Anführungszeichen oder Klammern, etwa `null`,
  als den Text `'null'`, und das konvertierte Template ebenso: `{image($content_fields, $content, null, 'i320')}` wurde
  in `node/view/embed/embed_ng_article.stpl` zu `{image(null, $content_fields, $content, 'null', 'i320')}`. Wo ein
  echtes null gemeint war, schreiben Sie `null` ohne Anführungszeichen.
- **Operatoren ohne Eingabe** werden als `op(null, ...)` geschrieben (`exp_count(null, $validation.attributes)`), was
  das Template-System tut; meist liest sich `$x|exp_count` besser und ergibt dasselbe.
- **Einbindungen behalten ihre `.tpl`-Namen.** `{expinclude uri='design:pagelayout/head/title.tpl'}` erreicht das
  `.stpl` über die Override-Regeln aus Teil 4; ohne sie bindet es das `.tpl` des Fallback-Designs ein.
- **Inline-CSS und -JavaScript.** Geschweifte Klammern wurden zu `{ldelim}`; lange Skripte lesen sich in
  `{literal}...{/literal}` besser.
- **Leerzeilen**, wo `{* comments *}` standen.

Smarty kompiliert jedes `.stpl` bei seinem ersten Rendern, öffnen Sie also die Seiten, die sie verwenden: Ein
Kompilierfehler erscheint in der Debug-Ausgabe (Label `smarty`) und bei eingeschalteter Debug-Ausgabe als HTML-Kommentar
([Fehlersuche](smarty-templates.md#11-fehlersuche)). Vergleichen Sie jede Seite mit derselben Seite auf dem
`.tpl`-Siteaccess.

## 4. Ein Siteaccess, der das konvertierte Design ausliefert

Ein konvertiertes Design ersetzt das Original nicht: Ein eigener Siteaccess verwendet es, und das Original-Design bleibt
in seiner Fallback-Liste für alles, was die Portierung nicht hat (Stylesheets, JavaScript, Bilder, Schriften und jedes
nicht konvertierte Template). Im durchgearbeiteten Beispiel ist das der Siteaccess `smarty`, eine Kopie des öffentlichen
Siteaccess `site` der Media-Website, ausgeliefert unter `https://www.example.com/smarty/`. Für `.stpl`-Templates muss
nichts aktiviert werden: Der Kernel rendert sie für jeden Siteaccess.

### 4.1 Das Siteaccess-Verzeichnis

Kopieren Sie den Siteaccess, für den das Design konvertiert wurde, hier `site`, nach `settings/siteaccess/smarty/` und
ändern Sie dann seine `[DesignSettings]` in `settings/siteaccess/smarty/site.ini.append.php`:

```ini
# Zuerst die .stpl-Portierung; das Media-Design bleibt danach in der Kette, für seine Stylesheets,
# sein JavaScript, seine Bilder und Schriften und für jedes Template, das die Portierung nicht hat.
[DesignSettings]
SiteDesign=smarty_media
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=media
AdditionalSiteDesignList[]=ezwebin
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

Die leere Zeile `AdditionalSiteDesignList[]` leert die Liste, die die Kopie hatte, bevor die neue aufgebaut wird.
Behalten Sie den Rest der Kopie (Datenbank, Name und URL der Website, Sprachen, die verwandten Siteaccesses), wie er ist.

Im durchgearbeiteten Beispiel fügt `extension/sevenx_themes_media` die Einstellungen, die es dem Siteaccess `site` gibt,
auch `smarty` hinzu, in `extension/sevenx_themes_media/settings/siteaccess/smarty/` (`site.ini.append.php` mit dem
Pfadpräfix und den Übersetzungen des Themes, `ezjscore.ini.append.php`, `menu.ini.append.php`). Die Design-Liste und die
Override-Regeln bleiben im `settings/siteaccess/smarty/` der Installation, weil die eigenen Dateien eines Siteaccess
Vorrang vor denen einer Erweiterung haben.

### 4.2 Die Override-Regeln

Legen Sie die Datei, die `--override-ini` geschrieben hat, in den Siteaccess:

```bash
cp var/tmp/smarty-override.ini.append.php settings/siteaccess/smarty/override.ini.append.php
```

Die Erweiterung liefert die Datei ihrer Portierung als `extension/sevenx_themes_media/doc/smarty/override.ini.append.php`
mit. Sie enthält zwei Arten von Regeln:

1. Jede Override-Regel des `-s`-Siteaccess, mit `MatchFile` auf das konvertierte `.stpl`, wo eines konvertiert wurde, in
   derselben Reihenfolge und mit derselben `Priority` (eine niedrigere Priorität wird zuerst versucht):

   ```ini
   [node/view/full#frontpage]
   Source=node/view/full.tpl
   MatchFile=node/view/full/full_frontpage.stpl
   Subdir=templates
   Match[class_identifier]=frontpage
   Priority=1
   ```

2. Eine Regel pro konvertiertem Template des Verzeichnisses `templates/` des Designs, namens `stpl_<path>`, die das
   `.stpl` an die Stelle des `.tpl` setzt. Das Template-System und die Einbindungen fragen nach `pagelayout.tpl` oder
   `content/search.tpl`; diese Regeln antworten mit dem `.stpl`:

   ```ini
   [stpl_pagelayout]
   Source=pagelayout.tpl
   MatchFile=../../templates/pagelayout.stpl
   Subdir=templates
   ```

   `MatchFile` wird unterhalb von `override/templates/` jedes Designs gesucht, `../../templates/` ist also das eigene
   Verzeichnis `templates/` des Designs, in dem das `.stpl` dort liegt, wo das `.tpl` war.

Die Datei der Media-Portierung hat 637 Regeln.

### 4.3 Den Siteaccess bekannt machen

Fügen Sie in `settings/override/site.ini.append.php` den Siteaccess zur Liste der Sites und der verfügbaren
Siteaccesses hinzu (neben den Einträgen, die schon da sind; wiederholen Sie die leere Zeile `[]` nicht):

```ini
[SiteSettings]
SiteList[]=smarty

[SiteAccessSettings]
AvailableSiteAccessList[]=smarty
```

Wie eine Anfrage den Siteaccess erreicht, folgt `[SiteAccessSettings] MatchOrder`. Mit `uri;host` (wie im
durchgearbeiteten Beispiel) wählt `/smarty/` am Anfang der Adresse ihn aus. Bei Host-Zuordnung fügen Sie stattdessen
eine Zeile `HostMatchMapItems[]` für einen eigenen Host hinzu.

### 4.4 Besucher ohne Anmeldung hereinlassen

Ein Besucher darf einen Siteaccess nur verwenden, wenn die Rolle Anonymous `user/login` dafür erlaubt; andernfalls
verlangt jede Seite des neuen Siteaccess eine Anmeldung. In der Administration: **Users > Roles and policies**, öffnen
Sie **Anonymous**, bearbeiten Sie sie und bearbeiten Sie die Richtlinie `user/login`: Fügen Sie `smarty` zu ihrer
Beschränkung **SiteAccess** hinzu (oder fügen Sie eine Richtlinie `user/login` mit der Beschränkung SiteAccess `smarty`
hinzu) und speichern Sie. Benutzer, die sich anmelden, brauchen dasselbe für ihre Rollen, wenn diese nach Siteaccess
beschränkt sind.

### 4.5 Caches leeren und ansehen

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-id=content --allow-root-user
```

Oder deployen Sie in einem Schritt, der diese Caches leert, PHP-FPM neu lädt und Velocity neu startet:

```bash
./console exp:velocity deploy --allow-root-user
```

Öffnen Sie den Siteaccess (`https://www.example.com/smarty/` im durchgearbeiteten Beispiel) und dieselben Seiten auf
dem ursprünglichen Siteaccess, nebeneinander. Eine aus `.stpl` gerenderte Seite ist dieselbe Seite; die Kommentare mit
Template-Pfaden ([Template-Pfad-Kommentare](../funktionen/6.0/template-pfad-kommentare.md)) und die Template-Liste der
Debug-Ausgabe zeigen, welche Datei was geschrieben hat.

## 5. An einem portierten Design arbeiten

- Bearbeiten Sie von nun an die `.stpl`-Dateien. Ein erneuter Lauf des Konverters lässt jede Datei, die Sie geändert
  haben, in Ruhe (`SKIP`) und schreibt nur die Dateien neu, die er selbst geschrieben hat und die niemand angefasst hat;
  `--force` fängt von vorn an. Führen Sie ihn mit `-s` auf dem Siteaccess aus, der die Portierung ausliefert, dann
  schreibt `--override-ini` dessen Override-Datei mit denselben Regeln erneut.
- Ein Template, das später zum Original-Design hinzukommt, wird über den Fallback verwendet, bis es konvertiert ist:
  Konvertieren Sie es einzeln (`--from=<design> --file=templates/<name>.tpl --to=<ported design>`) oder schreiben Sie es
  von Hand, und fügen Sie seine Override-Regel hinzu.
- Halten Sie die Design-Regeln des Projekts ein: keine Inline-Stile, die gemeinsamen Komponenten und Tokens des Designs.

## 6. Smarty-Templates zurück in .tpl konvertieren

```bash
./console exp:template:convert:stpl-to-tpl -s <siteaccess> \
    --from=<design directory with the .stpl templates> \
    [--to=<design directory for the .tpl templates>] \
    [--file=<one template>] [--override-ini=<file to write>] [--dry-run] [--force] --allow-root-user
```

Die Optionen sind die aus Teil 1 mit vertauschten Richtungen (`php bin/php/exptemplateconvertstpltotpl.php` und
`./console exp:template:convert --from-engine=stpl --to-engine=tpl` sind derselbe Befehl). Die Arbeit erledigt
`expSmartyReverseConverter`. Er liest, was der Konverter tpl -> stpl schreibt, und das übliche handgeschriebene Smarty
und schreibt ein `.tpl`, das ausgibt, was das `.stpl` ausgab. Jedes Ergebnis muss im Parser des Template-Systems
parsen, bevor es geschrieben wird. `--override-ini` schreibt die Override-Regeln des `-s`-Siteaccess mit `MatchFile` auf
das konvertierte `.tpl`, wo eines konvertiert wurde.

Zurückschreiben in das Original-Design ist sicher: Seine eigenen `.tpl`-Dateien haben keine Markierung, sodass jede von
ihnen übersprungen wird, sofern Sie nicht `--force` angeben. Ein Probelauf der Media-Portierung zurück in das
Media-Design:

```bash
./console exp:template:convert:stpl-to-tpl -s smarty --from=<the converted design> \
    --to=extension/sevenx_themes_media/design/media --dry-run --allow-root-user
```

```
Dry run: nothing is written.
SKIP override/templates/content/view/embed/embed_file.stpl: extension/sevenx_themes_media/design/media/override/templates/content/view/embed/embed_file.tpl has no conversion marker (written by hand); --force replaces it
...
PASS stpl->tpl 516 templates: 0 to write, 0 unchanged, 516 skipped, 0 errors; 8 notes in 4 templates (a skipped file was changed or written by hand: --force replaces it)
```

### Was zu was wird

| Smarty | .tpl |
|---|---|
| `{$a.b}`, `{$a['b']}`, `{$a[0]}`, `{$a->b}`, `{$a[$k]}` | `{$a.b}`, `{$a.b}`, `{$a[0]}`, `{$a.b}`, `{$a[$k]}` |
| `$Ns__name` (ein Section-Namensraum, wie der Konverter ihn schreibt) | `$Ns:name` |
| `expvar('name', 'ns')` | `$#ns:name` |
| `{$x\|op:a:b}`, `{op(a, b)}` | `{$x\|op(a, b)}`, `{op(a, b)}`; `exp_op` ist `op` (das Präfix entfernt) |
| `op(null, a)` eines Operators, der seine Eingabe liest | `op(a)` |
| `X\|expattr:'k'`, `X\|expattr:0` | `X.k`, `X[0]` |
| `i18n('c', expvoid(), ...)` | `i18n('c',, ...)` (der leere Parameter) |
| `==` `!=` `<` `>` `<=` `>=` (und `eq`, `ne`, `gt`, ...) | `eq()` `ne()` `lt()` `gt()` `le()` `ge()` |
| `&&` `\|\|` `!`, `+` `-` `*` `/` `%`, `c ? a : b` | `and()` `or()` `not()`, `sum()` `sub()` `mul()` `div()` `mod()`, `cond(c, a, b)` |
| `true` `false` `null`, `[a, b]`, `['k' => v]`, `"a $b"` | `true()` `false()` `null()`, `array(a, b)`, `hash('k', v)`, `concat('a ', $b)` |
| `isset($x)`, `$x\|in_array:$l`, Smartys `upper` `lower` `count` `cat` | `is_set($x)`, `$l\|contains($x)`, `upcase` `downcase` `count` `concat` |
| `{$x = v}` | `{def $x = v}` beim ersten Mal in seinem Block, `{set $x = v}`, wenn es sicher schon definiert ist |
| `{$x = null}`, `{expundefall}` | `{undef $x}`, `{undef}`; `{undef $a}{undef $b}` wird als `{undef $a $b}` geschrieben |
| `{$x[] = v}` | `{def $x = array(v)}` oder `{set $x = $x\|append(v)}` |
| `{if}{elseif}{else}` | dasselbe, mit der Bedingung wie oben |
| `{foreach $l as $k => $v}`, `{foreach from=$l item=v key=k}` | `{foreach $l as $k => $v}` |
| `{foreachelse}` | `{if $l\|count}{foreach ...}...{/foreach}{else}...{/if}` |
| `$v@index` `@iteration` `@first` `@last` `@total` `@key` | ein Zähler: `{def $__index_v = -1}`, `{set $__index_v = $__index_v\|inc}` als Erstes im Rumpf, `{undef $__index_v}` nach der Schleife |
| `{if !$v@first}...{/if}` als Erstes im Rumpf | `{delimiter}...{/delimiter}` |
| `{$__loopN = X}{foreach $__loopN as ...}`, `X\|expslice:o:m:r` | `{foreach X as ... offset o max m reverse}` |
| `{$__sequenceN = S}` und `$s = $__sequenceN\|expsequence:$v@index` | `sequence S as $s` |
| `{$__delimN = false}`, `{if $__delimN}...{/if}{$__delimN = true}`, `{$__delimN = false}{continue}` | `{delimiter}...{/delimiter}`, `{skip}` |
| `{foreach exprange(a, b) as $i}` | `{for a to b as $i}` |
| `{for $i = a to b}` | `{for a to b as $i}`, innerhalb von `{if le(a, b)}`, sofern a <= b nicht Zahlen sind (das `.tpl`-`{for}` zählt auch abwärts) |
| `{while}`, `{break}`, `{continue}` | dasselbe |
| `{$__doN = true}{while $__doN}...{$__doN = c}{/while}` | `{do}...{/do while c}` |
| `{$__switchN = X}{if $__switchN == A}...{elseif $__switchN\|in_array:B}...{else}...{/if}` | `{switch match=X}{case match=A}...{/case}{case in=B}...{/case}{case}...{/case}{/switch}` |
| `{foreach (L)\|expsectionloop:o:m:r as $Ns__key => $Ns__item}` mit `$Ns__index`, `$Ns__number`, `$Ns__sequence`, `$__skipN` | `{section name=Ns loop=L offset=o max=m reverse=r sequence=S}` mit `{section-exclude}`, `{section-include}`, `{delimiter}`, `{section-else}` |
| `{if !isset($v)}{$v = X}{/if}` | `{default v=X}` mit dem Rest des Blocks als Rumpf |
| `{capture assign=x}`, `{expsetblock}`, `{expappendblock}` | `{set-block variable=x scope=root}`, `{set-block}`, `{append-block}` |
| `{expcacheblock id=N ...}`, `{exprunonce id=N}` | `{cache-block ...}`, `{run-once}` |
| `{expinclude uri=...}`, `{expset name='x' value=v scope='global'}`, `{expfetch module= function= assign=x ...}` | `{include uri=...}`, `{set scope=global x=v}`, `{def $x = fetch(module, function, hash(...))}` |
| `{node_view_gui ...}` und jede andere Template-Funktion | dasselbe Tag (und derselbe Block) |
| `{ldelim}` `{rdelim}`, eine geschweifte Klammer, die Smarty als Text ausgibt (`{ `), `{literal}` | `{ldelim}` `{rdelim}`, `{literal}...{/literal}` |
| `{* comment *}` | dasselbe |

Leerraum wird exakt konvertiert: Smarty verwirft den Zeilenumbruch direkt nach einem Tag, das nichts ausgibt, nach
einem Kommentar und am Anfang der Datei; das Template-System verwirft den Zeilenumbruch direkt nach und direkt vor jedem
Funktions-Tag. Der Konverter reduziert jeden Text auf das, was Smarty ausgibt, und schreibt die Zeilenumbrüche, die das
Template-System braucht, um genau das auszugeben.

### Was er nicht konvertiert

Was keine exakte `.tpl`-Form hat, wird nie geraten. Es wird als Zeile `NOTE <template>: line <n>: <the Smarty source>:
<why>; kept as a comment` ausgegeben und im `.tpl` als Kommentar behalten, der nichts ausgibt:

```
{* SMARTY (not converted: Smarty's escape has no exact template operator): {$title|escape} *}
```

Ein Block (`{if}`, `{foreach}` ...), dessen Kopf nicht konvertiert werden kann, wird ganz in einem solchen Kommentar
behalten. Nicht konvertiert werden: Smartys eigene Modifier außer denen der Tabelle (`escape`, `default`, `truncate`,
`date_format`, `replace`, `json_encode` ...), `$smarty`, `===` und `!==`, Methodenaufrufe auf Objekten, statische
Klassenmitglieder, Konstanten, Config-Variablen, eine Zuweisung an ein Array-Element (`{$a.b = 1}`),
`{include file=...}`, Smartys `{section}`, `{strip}`, `{capture name=...}`, `{for}` mit `step` oder `max`, `@last` und
`@total` eines `{for}`, eine Variable eines Section-Namensraums, die außerhalb dieser Section zugewiesen wird, und ein
`expvoid()` auf oberster Ebene (was der Konverter tpl -> stpl für einen Ausdruck schreibt, den seine Quelle nicht parsen
konnte). Schreiben Sie diese von Hand um und nehmen Sie den Kommentar als Ausgangspunkt.

### Wie weit ein Hin- und Rückweg reicht

Das Media-Design (516 Templates, 58 davon mit Fehlern, die der Parser des Template-Systems in der Quelle meldet) wurde
tpl -> stpl -> tpl konvertiert:

| Prüfung | Templates |
|---|---|
| das `.tpl` parst ohne Fehler | 516 |
| eine erneute Konvertierung des `.tpl` ergibt wieder das erste `.stpl` (Fixpunkt) | 512 |
| eine erneute Rückkonvertierung dieses `.stpl` ergibt dasselbe `.tpl` (idempotent) | 512 |
| derselbe Template-Baum wie das Original, bis auf gleichwertige Schreibweisen (ein Name und derselbe String, ein `{def}` pro Variable, `{def}` für `{set}`) | 403 |
| derselbe Template-Baum wie das Original | 209 |
| Byte für Byte das Original | 15 |
| mit Hinweisen (8 Hinweise, alle ein `expvoid()` auf oberster Ebene einer Quelle, die nicht parst) | 4 |

Die vier, die kein Fixpunkt sind, sind diese vier mit Hinweisen. Die übrigen Unterschiede zum Original sind
Schreibweisen (Leerzeichen, `fieldValue($a, 'b')` für `fieldValue( $a, 'b' )`), eine `{section}` nur mit `show=`, die
als `{if}` zurückkam, die Quellfehler, die der Parser verwirft, und, in einer Installation, in der die
Template-Funktion einer Erweiterung nicht aktiv ist, die Tags dieser Funktion (konvertieren Sie mit `-s` auf einem
Siteaccess, der sie hat). Jeder Fall aus den eigenen Tests der Template-Engine (eZTemplateEngineRenderTest,
eZTemplateOperatorMatrixTest: 453 Fälle) rendert nach tpl -> stpl -> tpl gleich;
`tests/tests/lib/smarty/expSmartyReverseConverterTest.php` prüft das und das handgeschriebene Smarty der Tabelle, und
`tests/tests/kernel/private/classes/commands/expTemplateConvertCommandTest.php` prüft die Befehle, beide Richtungen auf
einem Design und den Hin- und Rückweg des ganzen Media-Designs.

## Verwandte Seiten

- [Templates zwischen Template-Engines konvertieren](template-konvertierung.md): die Befehle `exp:template:convert` für jede Engine, jede Option, die Ausgabe und die Exit-Codes, die Markierung, die Override-Einstellungen, dasselbe aus PHP
- [Von .tpl zu .stpl umziehen](../hilfe/template-sprache/von-tpl-zu-stpl-umziehen.md): das Kapitel des Handbuchs zur Template-Sprache, mit beiden Befehlen, durchgearbeiteten Beispielen und einer Gegenüberstellung
- Befehlsreferenz: [exp:template:convert](../beispiele/befehle/exptemplateconvert.md), [exp:template:convert:tpl-to-stpl](../beispiele/befehle/exptemplateconverttpltostpl.md), [exp:template:convert:stpl-to-tpl](../beispiele/befehle/exptemplateconvertstpltotpl.md)
- Neu bei `.stpl`: [ein erstes .stpl, Schritt für Schritt](smarty-templates-tutorial.md)
- Referenz: [jeder Operator](smarty-templates-operator-referenz.md), [Template-Funktionen und Blöcke](smarty-templates-funktionen.md), [Problembehebung und FAQ](smarty-templates-problembehebung.md)
- Themen: [Caching und Leistung](smarty-templates-caching.md), [Leistung (Geschwindigkeit)](smarty-templates-leistung.md), [ein Design migrieren](smarty-templates-migrieren.md), [Operatoren und Funktionen schreiben](smarty-templates-plugins.md), [Sicherheit und Maskierung](smarty-templates-sicherheit.md)
- [Smarty-Templates: .stpl-Templates schreiben und ausführen](smarty-templates.md)
- [Smarty-Templates: Operatoren und Fetch-Funktionen](smarty-templates-operatoren.md)
- [Smarty-Templates (.stpl) neben .tpl](../funktionen/6.0/smarty-templates.md), die Funktionsseite
- [Templates und Design](templates-und-design.md), [Rollen, Richtlinien und nicht aktivierte Benutzer](rollen-und-richtlinien.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
