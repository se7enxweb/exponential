# Smarty-Templates: Caching und Leistung

Diese Seite ist für Template-Entwickler und Betreiber, die wollen, dass `.stpl`-Seiten schnell und richtig sind. Sie
geht jeden Cache durch, dem eine `.stpl`-Seite begegnet, von außen nach innen, erklärt das Muster, mit dem die
Media-Portierung eine ganze Seite in einem Cache-Block hält, während die eigenen Teile des Besuchers frisch bleiben,
zeigt, wie Sie einen Cache für angemeldete Besucher schlüsseln, listet auf, was was ablaufen lässt, und wie Sie
messen. Die gemessenen Zahlen der Media-Portierung stehen auf der Funktionsseite:
[Geschwindigkeit](../funktionen/6.0/smarty-templates.md#geschwindigkeit).

## Die Schichten, von außen nach innen

| Schicht | Was sie behält | Wer sie bekommt | Läuft ab durch | Besonderheit bei `.stpl` |
|---|---|---|---|---|
| Velocity-Antwort-Cache | ganze Antworten, im Serverprozess | Anfragen ohne Session-Cookie, wenn die Seite es erlaubt | seine TTL, eine berührte Datei | nichts |
| Rollenbewusster HTTP-Cache (`httpcache.ini`) | ganze gerenderte Seiten, je Berechtigungskontext | anonyme Besucher der Siteaccesses, die er auflistet | Inhaltsänderungen, Layoutänderungen, `MaxAge` | nichts: er behält die Ausgabe |
| Inhalts-View-Cache (`ViewCaching`) | die Ausgabe einer Inhaltsansicht (`$module_result.content`) je Knoten, Ansicht, View-Parametern, Sprache sowie Rollen und Einschränkungen des Besuchers | alle | eine Veröffentlichung des Knotens oder verwandter Inhalte, ein Leeren des Caches | nichts: er behält die Ausgabe |
| Template-Block-Cache (`{expcacheblock}`) | die Ausgabe eines Blocks, nach seinen Schlüsseln | alle, deren Schlüssel passen | `expiry`, eine Inhaltsveröffentlichung, `subtree_expiry`, eine Layoutveröffentlichung, ein Leeren des Caches | derselbe Cache wie `{cache-block}` |
| Kompilierte Stubs | `{exp_smarty_render( '<file>' )}`, zu PHP kompiliert | — | die Template-Caches | das Einzige an einem `.stpl`, das das Template-System cacht |
| Smartys kompilierte Templates | jedes `.stpl` zu PHP kompiliert, in `<cache directory>/smarty/compile/` | — | ein geändertes `.stpl` (mit `CompileCheck=enabled`), `--clear-id=smarty`, die Template-Caches | ja |
| Erzeugte Funktionsaufrufe | ein kleines `.tpl` je Template-Funktion und Satz von Parameternamen, in `<cache directory>/smarty/functions/` | — | dasselbe | ja |

Keiner der Caches speichert die Ausgabe eines `.stpl` für sich: Smartys eigenes Ausgabe-Caching ist aus. Ein `.stpl`
wird bei jeder Anfrage gerendert, die nicht von einer der ersten vier Schichten beantwortet wird.

### Die Ganzseiten-Caches

Der rollenbewusste HTTP-Cache behält ganze Seiten für anonyme Besucher und beantwortet sie, bevor der Kernel startet
([Der rollenbewusste HTTP-Cache](../bc/6.0/httpcache.md)); der Antwort-Cache von Velocity tut dasselbe im Serverprozess
([Velocity-Antwort-Cache](../funktionen/6.0/velocity-antwort-cache.md)). Beide behalten Ausgabe und behandeln daher
`.tpl`- und `.stpl`-Seiten gleich. Zwei Folgen:

- **Eine Seite mit dem Debug-Bericht wird nie gespeichert.** Trägt die Seite die Exp-Debug-Leiste oder den klassischen
  Debug-Bericht, antwortet der HTTP-Cache mit `X-Exp-Cache: BYPASS (debug report)` und rendert sie. Mit eingeschalteter
  Debug-Ausgabe sehen Sie also immer die Renderzeit, nie den Cache; mit ausgeschalteter kann ein anonymer Besucher die
  Seite bekommen, ohne dass irgendein Template läuft.
- **Ein angemeldeter Besucher wird nicht aus ihnen bedient.** Für angemeldete Besucher leisten der Inhalts-View-Cache
  und der Template-Block-Cache die Arbeit.

Prüfen Sie, was der HTTP-Cache getan hat:

```bash
curl -s -o /dev/null -D - https://<host>/<page> | grep -i x-exp-cache
```

### Der Inhalts-View-Cache

Eine Vollansicht (oder jeder View-Modus in `[ContentSettings] CachedViewModes`) wird als Ausgabe behalten, geschlüsselt
nach Knoten, View-Modus, View-Parametern, Sprache sowie Rollen und Einschränkungen des Besuchers. Eine `.stpl`-Vollansicht
wird genau wie eine `.tpl`-Ansicht gecacht; während Sie daran arbeiten, leeren Sie nach einer Änderung den
Inhalts-Cache oder schalten den View-Cache in Ihrem Arbeits-Siteaccess ab (`[ContentSettings] ViewCaching=disabled`).
Der Reiter **Rendering** der Debug-Leiste sagt für jede Anfrage, ob die Ansicht aus dem Cache gelesen oder erzeugt
wurde, und warum.

## {expcacheblock} im Detail

```smarty
{expcacheblock id=2 keys=[$node.node_id, $view_parameters.offset] expiry=3600}
    {$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'offset' => $view_parameters.offset, 'limit' => 10])}
    {foreach $children as $child}{node_view_gui view='line' content_node=$child}{/foreach}
{/expcacheblock}
```

- **Der Schlüssel** besteht aus den `keys`, die Sie angeben, der Stelle des Blocks (`stpl:<file>:<id>`) und dem
  Siteaccess (dazu die Design-Variante der Administration aus dem UI-Umschalter). Ohne `keys` ist der Block für jede
  Seite gleich, die diese Datei rendert: richtig für eine Fußzeile, falsch für alles, was vom Knoten abhängt.
- **`id`** muss in der Datei eindeutig sein. Zwei Blöcke mit derselben id in einer Datei teilen sich einen
  Cache-Eintrag.
- **`expiry`** in Sekunden (Standard 7200, zwei Stunden); `0` behält den Block, bis etwas anderes ihn ablaufen lässt.
- **Ablauf durch Inhalte.** Eine Veröffentlichung irgendeines Inhalts lässt jeden Block ablaufen, es sei denn, der Block
  sagt `ignore_content_expiry=true`.
- **`subtree_expiry`** (eine Knoten-ID oder ein Pfad wie `'content/view/full/2'` oder ein URL-Alias): Nur eine
  Veröffentlichung unterhalb dieses Knotens lässt den Block ablaufen, und der globale Ablauf durch Inhalte wird
  übergangen.
- **Ist der Block gecacht, läuft sein Rumpf nicht**: Nichts darin wird abgerufen oder gerendert, und die Variablen, die
  er zuweisen würde, werden nicht zugewiesen. Weisen Sie, was Sie danach brauchen, außerhalb des Blocks zu.
- **Nur mit `[TemplateSettings] TemplateCache=enabled`**: Andernfalls läuft der Rumpf immer, und nichts wird
  gespeichert.
- **Legen Sie nie Ausgabe je Besucher oder je Anfrage hinein**: den Warenkorb, „angemeldet als ...“, ein
  Formular-Token, eine CSP-Nonce (`csp_nonce()`), die aktuelle Zeit. Schlüsseln Sie den Block entweder nach dem, was
  ihn unterscheidet, oder lassen Sie diesen Teil mit dem Muster des nächsten Abschnitts draußen.

Smartys `{nocache}` und Smartys eigenes Caching gelten nicht: Das Ausgabe-Caching ist in der Brücke aus.

## Der Seitenblock mit Platzhaltern

Die Media-Portierung rendert die ganze Seite, Layout, Kopf- und Fußzeile eingeschlossen, in einem `{expcacheblock}` in
`templates/pagelayout.stpl`, geschlüsselt nach dem, was die Seite unterscheidet. Der eine Teil, der dem Besucher
gehört, das Benutzermenü mit der Warenkorbzahl, wird nie behalten: Eine Markierung steht an seiner Stelle im Block, und
das Menü wird bei jeder Anfrage nach dem Block frisch gerendert und an die Stelle der Markierung gesetzt. Bei einer
gecachten Anfrage führt das Pagelayout nur das Benutzermenü aus.

Die drei Teile, aus `extension/sevenx_themes_media/design/smarty_media/templates/`:

1. **Ein globales Flag** sagt „ein Seitenblock wird gebaut“. `pagelayout.stpl` setzt es vor dem Block und löscht es
   danach:

   ```smarty
   {$el_page_user = fetch('user', 'current_user')}
   {expsetblock variable='exp_dynamic_parts' scope='global'}
   1{/expsetblock}
   {capture assign=el_page_html}
   {expcacheblock id=4 keys=(['pagelayout-page', $module_result.content_info.node_id|default:0, $module_result.uri|default:'', $module_result.content|md5, $el_page_user.role_id_list|exp_implode:',', $el_page_user.limited_assignment_value_list|exp_implode:',', $el_page_user.is_logged_in])}
   ... die ganze Seite ...
   {/expcacheblock}
   {/capture}
   {expsetblock variable='exp_dynamic_parts' scope='global'}
   0{/expsetblock}
   ```

2. **Der Teil, der das Menü des Besuchers rendern würde, schreibt stattdessen eine Markierung, solange das Flag gesetzt
   ist** (`pagelayout/header.stpl`):

   ```smarty
   {if and(is_set(expvar('exp_dynamic_parts', '')), eq(expvar('exp_dynamic_parts', ''), '1'))}
   <!--exp-dynamic:user_menu-->{else}
   {expinclude uri='design:pagelayout/header/user_menu.tpl'}{/if}
   ```

3. **Nach dem Block ersetzt das frische Menü die Markierung** in der Seite, gecacht oder nicht:

   ```smarty
   {capture assign=el_user_menu}
   {expinclude uri='design:pagelayout/header/user_menu.tpl'}{/capture}
   {($el_page_html|exp_explode:'<!--exp-dynamic:user_menu-->')|exp_implode:$el_user_menu}{$el_page_html = null}
   ```

Warum es so gebaut ist:

- Das Flag ist global (`scope='global'`, gelesen mit `expvar(..., '')`), weil die Kopfzeile ein Template ist, das
  mehrere Ebenen tiefer eingebunden wird; eine globale Variable erreicht es unabhängig von den Namensräumen dazwischen.
- Die Markierung ist ein HTML-Kommentar; eine Seite, die außerhalb des Musters gerendert wird (das Flag nicht gesetzt),
  zeigt sie also nie, und eine Seite aus einem alten Cache ohne die Markierung hat einfach nichts zu ersetzen.
- `exp_explode` und `exp_implode` ersetzen jede Markierung, ohne regulären Ausdruck und ohne das Menü zu maskieren.
- Der Schlüssel enthält `$module_result.content|md5`: den eigenen Inhalt der Seite, der selbst die Ausgabe der Ansicht
  ist (gecacht vom View-Cache, geschlüsselt nach Knoten, View-Parametern und den Rollen des Besuchers); Blättern, ein
  Suchergebnis oder ein Formularfehler ergeben also einen anderen Seitenblock.
- Das `.tpl`-Pagelayout des Media-Designs tut dasselbe mit `{set-block}`, `{cache-block}`, `$#exp_dynamic_parts` und
  `{$el_layout_html|explode( '<!--exp-dynamic:user_menu-->' )|implode( $el_user_menu )}`, sodass beide Engines dieselbe
  Seite cachen.

Verwenden Sie dasselbe Muster für einen Warenkorb in einer Kopfzeile, ein „Hallo, <Name>“ oder einen Hinweis mit den
Meldungen der Session: Geben Sie jedem Teil einen eigenen Markierungsnamen (`<!--exp-dynamic:cart-->`), schreiben Sie
die Markierung, solange das Flag gesetzt ist, rendern Sie den Teil nach dem Block und ersetzen Sie seine Markierung.

## Schlüssel für angemeldete Besucher

Was ein Besucher sehen darf, hängt von seinen Rollen und Einschränkungen ab. Ein Block, der etwas
Berechtigungsabhängiges zeigt (ein Menü, das ausblendet, was der Besucher nicht lesen darf, Bearbeiten-Links, eine
Liste von Inhalten), muss danach geschlüsselt sein:

| Schlüsselteil | Aus | Warum |
|---|---|---|
| `$user.role_id_list\|exp_implode:','` | `fetch('user', 'current_user')` | die Rollen, die der Besucher hat, über jede Gruppe |
| `$user.limited_assignment_value_list\|exp_implode:','` | dasselbe | Rollen, die mit einer Einschränkung zugewiesen sind (ein Teilbaum, eine Sektion), ergeben andere Inhalte |
| `$user.is_logged_in` | dasselbe | anonym und angemeldet unterscheiden sich auch bei gleichen Rollen (das Menü, die Formulare) |
| `$module_result.content\|md5` | das Pagelayout | alles, was die Ansicht erzeugt hat |
| `$node.node_id`, `$view_parameters.offset` | eine Ansicht | der Knoten und die Seite einer Liste |
| der Siteaccess | automatisch | jeder Block gilt je Siteaccess |

Schlüsseln Sie nicht nach der Benutzer-ID, es sei denn, der Block ist wirklich je Benutzer: Jeder Besucher bekommt
einen eigenen Eintrag, und der Cache wächst mit der Zahl der Besucher. Schlüssel nach Rollen halten einen Eintrag je
Berechtigungskontext.

## Was was ablaufen lässt

| Ereignis | Lässt ablaufen |
|---|---|
| Eine Änderung an einem `.stpl` | seine kompilierte Datei beim nächsten Rendern (mit `CompileCheck=enabled`); mit `disabled` nichts bis `--clear-id=smarty` |
| Ein neues `.stpl`, eine neue oder geänderte Override-Regel | für sich nichts: Leeren Sie die Template-Caches (`--clear-tag=template`) und den Inhalts-Cache |
| Eine Inhaltsveröffentlichung | den View-Cache des Knotens und verwandter Inhalte (Smart View Cache), jeden Template-Block (`{expcacheblock}`) ohne `ignore_content_expiry`, die Blöcke, deren `subtree_expiry` den Knoten abdeckt, den HTTP-Cache (`ContentChangePurges`) |
| Eine Veröffentlichung eines Layouts oder einer Layout-Regel (Exponential Layouts) | jeden Template-Block, sodass ein Pagelayout, das das gerenderte Layout in einem Block hält, das neue zeigt; den Cache des Layout-Resolvers |
| `php bin/php/ezcache.php --clear-tag=template` | kompilierte Stubs und `.tpl`-Templates, Smartys kompilierte Dateien und erzeugte Aufrufe, Template-Blöcke, den Override-Cache |
| `php bin/php/ezcache.php --clear-id=content` | den Inhalts-View-Cache |
| Eine geänderte INI-Einstellung | nichts in Templates: Leeren Sie den INI-Cache (`--clear-tag=ini`) und laden Sie PHP-FPM neu / starten Sie Velocity neu |

`./console exp:velocity deploy --allow-root-user` leert all das in der richtigen Reihenfolge und lädt die Server neu.

## So messen Sie

1. **Schalten Sie die Debug-Ausgabe nur für Ihre Adresse ein** ([die Exp-Debug-Leiste](../funktionen/6.0/exp-debug-leiste.md)).
   Der HTTP-Cache speichert oder liefert nie eine Seite mit dem Debug-Bericht, also wird jede Anfrage gerendert.
2. **Lesen Sie die Leiste.** Die Skriptzeit, die Zeitakkumulatoren des Reiters **Timing** (die Template-Verarbeitung
   darunter) und den Reiter **Rendering**:
   - die Zeile **Content view cache**: Treffer oder erzeugt, und warum;
   - die Tabelle **Cache blocks**: jeder `{cache-block}` und `{expcacheblock}`, Treffer oder erzeugt, seine Zeit und
     Schlüssel. Ein `{cache-block}` wird nach seinem Template und seiner Zeile benannt, ein `{expcacheblock}` nach
     seiner `.stpl`-Datei und seiner `id` (`full.stpl id=2`);
   - den **Template tree**: jedes Template, das lief, mit seiner Zeit. Ein `.stpl`, das von einem `.stpl` ohne `name=`
     eingebunden wird, läuft als Smarty-Subtemplate und steht im Baum unter dem Template, das es eingebunden hat, so
     wie ein `{include}` eines `.tpl`.
3. **Vergleichen Sie die Engines** an derselben URL: der `.tpl`-Siteaccess und der `.stpl`-Siteaccess, jeweils der
   Median von zehn Anfragen, mit denselben warmen Caches.
4. **Ohne die Debug-Ausgabe**, aus einer Shell:

   ```bash
   for i in 1 2 3 4 5 6 7 8 9 10; do
       curl -s -o /dev/null -w '%{time_total}\n' https://<host>/smarty/<page>
   done | sort -n | sed -n 5,6p
   ```

   druckt die beiden mittleren Zeiten von zehn Anfragen. Achten Sie auch auf `X-Exp-Cache`: Ein `HIT` misst den
   Cache, nicht das Template.

## Warum eine .stpl-Seite schnell ist

Die ganze Geschichte, mit den Zahlen vorher und nachher, jeder Änderung und ihrem Grund, und wie Sie messen:
[Smarty-Templates: Leistung (Geschwindigkeit)](smarty-templates-leistung.md).

- Ein `.stpl`, das von einem `.stpl` eingebunden wird (`{expinclude}` ohne `name=`), läuft als Smarty-Subtemplate,
  nicht über das Template-System, seinen Stub und ein zweites Rendern.
- Der Wert einer Variablen wird einmal je Seite für Smarty umgewandelt: Das nächste `.stpl`, das dasselbe `$node` oder
  `$module_result` sieht, bekommt den umgewandelten Wert nach einem Vergleich zurück (`expSmartyRenderer::wrapNamed()`).
- Wofür ein Modifier-, Funktions- oder Blockname steht, wird einmal je Prozess nachgeschlagen (`expSmartyExtension`).
- Die Smarty-Instanz wird einmal je Prozess und Satz von Designs erzeugt.
- Die Variablen, die ein Template zugewiesen hat, werden mit einem Aufruf am Ende seines kompilierten Codes eingesammelt
  (ein Post-Filter der Brücke), da Smarty 5 sie verwirft, wenn `fetch()` zurückkehrt; nichts durchläuft die Variablen
  des Templates während des Renderns.
- Unter Velocity bleiben der Kernel, Smarty und die kompilierten Templates in den Workern geladen.

Was in einem `.stpl` Zeit kostet, ist das, was in einem `.tpl` Zeit kostet: Fetches, Template-Funktionen (jede ist ein
Aufruf des Template-Systems) und `{expinclude}` von `.tpl`-Templates. Cache-Blöcke um die teuren Teile und der
Seitenblock oben bringen am meisten.

## Verwandte Seiten

- [Smarty-Templates: Leistung (Geschwindigkeit)](smarty-templates-leistung.md): was `.stpl` schnell gemacht hat,
  gemessen
- [Smarty-Templates (.stpl) neben .tpl](../funktionen/6.0/smarty-templates.md#geschwindigkeit): die gemessenen Zahlen
- [Template-Funktionen und Blöcke](smarty-templates-funktionen.md#expcacheblock)
- [Caches](caches.md), [Der rollenbewusste HTTP-Cache](../bc/6.0/httpcache.md),
  [Die Exp-Debug-Leiste](../funktionen/6.0/exp-debug-leiste.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
