# Smarty-Templates (.stpl) neben .tpl

Diese Seite ist für Site-Builder und Template-Entwickler, die Smarty kennen oder ein Design darauf portieren wollen. Ein
Design kann jetzt Smarty-5-Templates (`*.stpl`) neben den üblichen Exponential-Templates (`*.tpl`) enthalten. Ein
`.stpl` wird wie jedes Template gefunden, überschrieben, eingebunden und gecacht, sieht dieselben Variablen und kann
jeden Template-Operator von Exponential, jede Fetch-Funktion und die `*_view_gui`-Funktionen verwenden. Ein Konverter
wandelt ein ganzes Design aus `.tpl`-Dateien in `.stpl` um. Die Funktion ist Teil der Kernel-Bibliothek: Der Code liegt
in `lib/smarty/`, die Einstellungen in `settings/smarty.ini`, und nichts muss aktiviert werden: `.stpl`-Templates
funktionieren in jedem Siteaccess. Twig-3-Templates (`.twig`) sind die dritte Engine ([Twig-Templates](twig-templates.md));
die Wahl zwischen ihnen: [Die drei Template-Engines im Vergleich](../../anleitungen/template-engines.md). Hinzugefügt im
Oktober 2026. Wie schnell es ist und warum: [Smarty-Templates: Leistung (Geschwindigkeit)](../../anleitungen/smarty-templates-leistung.md).
Wenn Sie von `.tpl` kommen: [Von .tpl zu .stpl umziehen](../../hilfe/template-sprache/von-tpl-zu-stpl-umziehen.md).

Die Anleitungen gehen weiter:

| Anleitung | Für |
|---|---|
| [Ein erstes .stpl, Schritt für Schritt](../../anleitungen/smarty-templates-tutorial.md) | Ihr erstes Smarty-Template: ein Siteaccess, eine Override-Regel, Variablen, Operatoren, ein Fetch mit Blättern, eine Schleife, Includes |
| [.stpl-Templates schreiben und ausführen](../../anleitungen/smarty-templates.md) | wie es funktioniert: der Stub, die Einrichtung, Variablen und Zurückschreiben, Objekte, Includes, Schleifen, Funktionen, Blöcke, Caching, Fehlersuche, Velocity, Grenzen |
| [Operatoren und Fetch-Funktionen](../../anleitungen/smarty-templates-operatoren.md) | die beiden Aufrufformen, wie ein Operator seine Eingabe bekommt, das Präfix `exp_`, `fetch()`, die Helfer |
| [Operator-Referenz](../../anleitungen/smarty-templates-operator-referenz.md) | jeder Operator der Installation und ihrer Erweiterungen, mit seiner `.stpl`-Form, seinem Modus und einem Beispiel |
| [Template-Funktionen und Blöcke](../../anleitungen/smarty-templates-funktionen.md) | jede überbrückte Funktion und jeder überbrückte Block, die `exp*`-Tags und -Blöcke, Smartys eigene Tags |
| [Caching und Leistung](../../anleitungen/smarty-templates-caching.md) | die Cache-Schichten, `{expcacheblock}`, ein Seitenblock mit Platzhaltern je Besucher, Schlüssel, Ablauf, Messen |
| [Leistung (Geschwindigkeit)](../../anleitungen/smarty-templates-leistung.md) | die ganze Geschichte, wie `.stpl` schnell wurde: was langsam war, jede Behebung und warum, die Zahlen, das Muster für den Seiten-Cache, Messen, Velocity gegenüber PHP-FPM, eine Checkliste |
| [Von .tpl zu .stpl umziehen](../../hilfe/template-sprache/von-tpl-zu-stpl-umziehen.md) | das Kapitel des Handbuchs zur Template-Sprache: wann Sie `.stpl` wählen, was sich ändert, die Konverter in beide Richtungen, nebeneinander |
| [Templates zwischen Template-Engines konvertieren](../../anleitungen/template-konvertierung.md) | die Befehle `exp:template:convert`: jede Option, die Ausgabe und Exit-Codes, die Markierung, die Override-Einstellungen, dasselbe aus PHP |
| [Der Konverter und das Portieren eines Designs](../../anleitungen/smarty-templates-konverter.md) | `exp:template:convert:tpl-to-stpl` und `exp:template:convert:stpl-to-tpl`, alles, was sie schreiben, ihre Hinweise, ein Siteaccess für ein konvertiertes Design |
| [Ein Design migrieren](../../anleitungen/smarty-templates-migrieren.md) | Planen, Konvertieren, Gleichheit Seite für Seite, Unterschiede beheben, `.tpl` und `.stpl` im Gleichschritt halten |
| [Problembehebung und FAQ](../../anleitungen/smarty-templates-problembehebung.md) | jede Fehlermeldung mit ihrer Ursache und Behebung, und die häufigen Fragen |
| [Operatoren und Funktionen schreiben](../../anleitungen/smarty-templates-plugins.md) | eigene Operatoren und Funktionen, die in beiden Engines funktionieren |
| [Sicherheit und Maskierung](../../anleitungen/smarty-templates-sicherheit.md) | `wash`, `escape` und `raw`, Werte von Besuchern, was Smarty erreichen kann, das Formular-Token, Cache-Blöcke |

## Was es tut

- **Ein `.stpl` ist ein Template wie jedes andere.** Der Design-Fallback findet es, eine Regel in `override.ini` kann
  mit ihrem `MatchFile` darauf zeigen, `{include uri='design:parts/x.stpl'}` bindet es ein, und `node_view_gui` oder
  `attribute_view_gui` können darin enden. Ein `.stpl` kann `.tpl`-Templates einbinden und umgekehrt.
- **Bei jeder Anfrage von Smarty gerendert.** Das Template-System parst den Smarty-Text nie. Für ein `.stpl` parst es
  einen einzeiligen Stub, `{exp_smarty_render( '<file>' )}`, und kompiliert und cacht diesen Stub wie jedes Template.
  Der Stub läuft bei jeder Anfrage, auch aus dem kompilierten Cache, und rendert die Datei mit Smarty und den Variablen
  des aufrufenden Templates. Nichts, was eine Anfrage gerendert hat, wird je in einem Template-Cache gespeichert.
- **Dieselben Variablen, in beide Richtungen.** Ein `.stpl` sieht, was ein `.tpl` an seiner Stelle sieht (`$node`,
  `$view_parameters`, `$module_result` und `$site` in einem Pagelayout, die Parameter eines Includes). Was es zuweist,
  geht an das aufrufende Template zurück, wie es `{def}`, `{set}` und `{undef}` tun.
- **Operatoren als Modifier und Funktionen.** `{$node.name|wash}`, `{'Read more'|i18n:'design/standard/node'}`,
  `{concat('a', $b)}`, `{fetch('content', 'node', ['node_id' => 2])}`. Wo Smarty einen Modifier mit demselben Namen
  hat, wird der Exponential-Operator mit dem Präfix `exp_` aufgerufen (`{$list|exp_count}`).
- **Template-Funktionen als Tags.** `{attribute_view_gui attribute=$node.data_map.body}`,
  `{node_view_gui view='line' content_node=$child}`, und Funktionen mit einem Rumpf als Blöcke
  (`{explblock name='content'}...{/explblock}`). Die Exponential-Tags, die Template-Struktur sind, haben
  Smarty-Entsprechungen: `{expinclude}`, `{expset}`, `{expundefall}`, `{expsetblock}`, `{expappendblock}`,
  `{expcacheblock}`, `{exprunonce}`, `{expfetch}`.
- **Konverter in beide Richtungen.** `./console exp:template:convert:tpl-to-stpl` wandelt jedes `.tpl` eines Designs in
  ein `.stpl` um, das dieselbe Ausgabe rendert, meldet, was es nicht genau umwandeln konnte, und kann die
  `override.ini` schreiben, mit der ein Siteaccess die konvertierten Templates verwendet;
  `./console exp:template:convert:stpl-to-tpl` wandelt zurück. Beide gehören zum allgemeinen Befehl
  `exp:template:convert` ([Templates zwischen Template-Engines konvertieren](../../anleitungen/template-konvertierung.md)).

## So verwenden Sie es

1. Smarty 5 kommt mit der Installation: Die `composer.json` im Wurzelverzeichnis verlangt `smarty/smarty ^5`. Ohne
   Composer richten Sie `[SmartySettings] LibraryPath` auf einen Smarty-5-Checkout aus (siehe
   [Einstellungen](#einstellungen)).
2. Legen Sie ein `.stpl` in ein Design und verwenden Sie es über eine Override-Regel, zum Beispiel in
   `settings/siteaccess/<siteaccess>/override.ini.append.php`:

   ```ini
   [folder_smarty]
   Source=node/view/full.tpl
   MatchFile=full/folder.stpl
   Subdir=templates
   Match[class_identifier]=folder
   ```

   `MatchFile` wird unterhalb von `override/templates/` jedes Designs nachgeschlagen; diese Regel verwendet also
   `design/<design>/override/templates/full/folder.stpl`.

3. Schreiben Sie das Template in Smarty-Syntax:

   ```smarty
   <div class="content-view-full">
       <h1>{$node.name|wash}</h1>
       {if isset($node.data_map.short_description) && $node.data_map.short_description.has_content}
           <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
       {/if}
       {$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 5, 'sort_by' => ['priority', true]])}
       <ul>
       {foreach $children as $child}
           <li>{node_view_gui view='line' content_node=$child}</li>
       {foreachelse}
           <li>{'No items.'|i18n:'design/standard/node'}</li>
       {/foreach}
       </ul>
   </div>
   ```

4. Leeren Sie die Caches einmal, weil eine neue Template-Datei hinzugekommen ist
   (`php bin/php/ezcache.php --clear-tag=template --allow-root-user`, und den Inhalts-Cache, wenn der View-Cache an
   ist). Danach wird eine Änderung am `.stpl` bei der nächsten Anfrage sichtbar, solange `CompileCheck` eingeschaltet
   ist.

Der Kernel liefert in `design/standard/templates/smarty/` ein funktionierendes Demo-Paar mit: `demo_full.stpl` (eine
Vollansicht mit einem Fetch und `attribute_view_gui`) und `demo_children.stpl` (eingebunden mit Smartys eigenem
`{include file=...}`). Weil sie in `templates/` liegen, nicht in `override/templates/`, erreicht eine Override-Regel sie
mit `MatchFile=../../templates/smarty/demo_full.stpl`.

## Warum es sich lohnt

Smarty ist eine optionale Engine. Die eigene Template-Sprache von Exponential bleibt der Standard, und für eine
Website, die nie ein `.stpl` hinzufügt, ändert sich nichts. Was ein `.stpl` bekommt, ist keine reduzierte Teilmenge
hinter einem Stub, sondern das ganze System, auf das sich die Templates einer Website verlassen:

- **Jeden Template-Operator** des Kernels und der aktiven Erweiterungen, als Smarty-Modifier oder -Funktion, so
  aufgerufen, wie das kompilierte Template-System ihn aufruft (`i18n`, `ezurl`, `ezimage`, `wash`, `ezini`, `cond`, die
  Zeichenkettenoperatoren, die Operatoren von Exponential Layouts ...).
- **Jede Fetch-Funktion** jedes Moduls, mit denselben Parametern und Berechtigungsprüfungen.
- **Jede Template-Funktion**: `attribute_view_gui`, `node_view_gui`, `content_view_gui` und die übrigen als Tags,
  Blockfunktionen wie `{explblock}` von Layouts als Blöcke.
- **Override-Regeln und den Design-Fallback**: Ein `.stpl` wird von `override.ini` wie ein `.tpl` gewählt, und eine
  Portierung kann für alles, was sie nicht hat, auf das ursprüngliche Design zurückgreifen.
- **Includes in beide Richtungen**, `.tpl` aus `.stpl` und `.stpl` aus `.tpl`, die ihre Variablen teilen, wie es
  `{include}` tut.
- **Übersetzungen**: `i18n` mit Kontexten, Kommentaren und Argumenten, aus denselben Übersetzungsdateien.
- **Caching**: die Template-Caches, den Inhalts-View-Cache, `{expcacheblock}` (den Template-Block-Cache mit seinen
  Ablaufregeln), den Antwort-Cache von Velocity; Smartys kompilierte Dateien werden mit den Template-Caches geleert.
- **Velocity und PHP-FPM gleichermaßen**, mit dauerhaften Workern: Nichts aus einer Anfrage bleibt für die nächste
  erhalten.
- **Einen Konverter**, der ein ganzes Design portiert, und die Override-Regeln, die es auswählen.

Ein Team, das Smarty kennt, kann also vom ersten Tag an Templates darin schreiben, und ein Design kann Template für
Template zu Smarty umziehen, neben den `.tpl`-Dateien, die es noch verwendet.

### Geschwindigkeit

Eine `.stpl`-Seite ist schneller als dieselbe Seite in `.tpl`. Gemessen auf einer Entwicklungsinstallation am
28. Oktober 2026: die Skriptzeit, die die Debug-Leiste zeigt, der Median von 10 Anfragen derselben Seite, mit warmem
Inhalts-View-Cache, der Debug-Leiste für beide eingeschaltet und den Siteaccesses `site` (`.tpl`) und `smarty`
(`.stpl`), die byte-genau dasselbe HTML zeigen:

| Seite | `.tpl`, Apache + PHP-FPM | `.stpl`, Apache + PHP-FPM | `.tpl`, Velocity | `.stpl`, Velocity |
|---|---|---|---|---|
| Eine Kategorieseite (`/fitness`) | 0,119 s | 0,097 s (18 % schneller) | 0,102 s | 0,069 s (32 % schneller) |
| Eine Rezeptliste (`/recipes`) | 0,102 s | 0,077 s (25 % schneller) | 0,093 s | 0,037 s (60 % schneller) |
| Die Startseite | — | 0,104 s | — | 0,036 s |

Die Startseite der `.tpl`-Website wurde in diesen Läufen aus dem Ganzseiten-Cache beantwortet und hat daher keine
eigene Skriptzeit zum Vergleich. Allein bei der Template-Verarbeitung zeigte die Debug-Leiste 74 ms für die
`.stpl`-Kategorieseite gegenüber 90 ms für die `.tpl`-Seite, mit 14 MB Speicher gegenüber 18 MB.

Woher die Geschwindigkeit kommt:

- **Ein `.stpl`, das von einem `.stpl` eingebunden wird, läuft als Smarty-Subtemplate**, ohne den Umweg über das
  Template-System, seinen Stub und ein zweites Rendern: Die Startseite rendert 37 `.stpl`-Templates über die Brücke,
  nicht 266.
- **Werte werden einmal je Seite umgewandelt**: Eine Variable, die das nächste Template unverändert sieht (`$node`,
  `$module_result` ...), wird mit einem Vergleich gefunden, und die Modifier, Funktionen und Blöcke werden einmal je
  Prozess nachgeschlagen.
- **Die Media-Portierung hält ihre ganze Seite in einem `{expcacheblock}`**, je Knoten, Seiteninhalt, Rollen des
  Besuchers und ob der Besucher angemeldet ist. Nur was dem Besucher gehört, das Benutzermenü mit dem Warenkorb, wird
  bei jeder Anfrage gerendert und in die gecachte Seite gesetzt. Eine Veröffentlichung von Inhalten, eines Layouts oder
  einer Layout-Regel lässt ihn ablaufen.
- **Velocity hält den Kernel, Smarty und die kompilierten Templates in seinen Workern**, sodass eine Anfrage sie nicht
  neu startet: Deshalb liegen die `.stpl`-Seiten dort meist bei 0,03 bis 0,07 s.

Die Zahlen hängen von der Last des Servers ab. Um Ihre eigenen Seiten zu messen, vergleichen Sie die Skriptzeit der
Debug-Leiste oder `curl -w '%{time_total}'` für dieselbe URL auf beiden Siteaccesses. Eine Seite, die der
Ganzseiten-Cache beantwortet (anonyme Besucher, kein Debug-Bericht), wird ohne Rendern ausgeliefert, in beiden Engines.

Die ganze Geschichte, mit jeder vorgenommenen Änderung, ihrem Grund, den verbleibenden Kosten und wie Sie messen:
[Smarty-Templates: Leistung (Geschwindigkeit)](../../anleitungen/smarty-templates-leistung.md).

## Das konvertierte Media-Design in einem Siteaccess einschalten

Das Media-Design wurde zu Smarty portiert: 516 `.stpl`-Templates in
`extension/sevenx_themes_media/design/smarty_media/`, konvertiert aus `design/media/`. Das Beispiel liefert es über den
Siteaccess `smarty` aus (`https://www.example.com/smarty/`), eine Kopie des öffentlichen Siteaccess `site` der
Media-Website. So richten Sie dasselbe in Ihrer Installation ein (hier heißt der neue Siteaccess `smarty`, kopiert aus
`site`):

1. Kopieren Sie `settings/siteaccess/site/` nach `settings/siteaccess/smarty/` und setzen Sie die Design-Liste in
   `settings/siteaccess/smarty/site.ini.append.php`; die `.stpl`-Portierung kommt zuerst, das Media-Design und seine
   Eltern sind der Fallback für Stylesheets, Bilder und jedes nicht portierte Template:

   ```ini
   [DesignSettings]
   SiteDesign=smarty_media
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=media
   AdditionalSiteDesignList[]=ezwebin
   AdditionalSiteDesignList[]=standard
   AdditionalSiteDesignList[]=base
   ```

2. Legen Sie die Override-Regeln, die die Templates auf `.stpl` abbilden, in den Siteaccess. Die Erweiterung liefert
   die Datei ihrer Portierung mit; `./console exp:template:convert:tpl-to-stpl --override-ini=...` schreibt dasselbe
   für ein eigenes Design:

   ```bash
   cp extension/sevenx_themes_media/doc/smarty/override.ini.append.php settings/siteaccess/smarty/override.ini.append.php
   ```

3. Machen Sie den Siteaccess in `settings/override/site.ini.append.php` bekannt, neben den Einträgen, die dort schon
   stehen:

   ```ini
   [SiteSettings]
   SiteList[]=smarty

   [SiteAccessSettings]
   AvailableSiteAccessList[]=smarty
   ```

   Mit `MatchOrder=uri` (oder `uri;host`) ist der Siteaccess unter `/smarty/` erreichbar; für die Zuordnung über den
   Host fügen Sie eine Zeile `HostMatchMapItems[]` hinzu.

4. Lassen Sie Besucher herein: Bearbeiten Sie in der Administration unter **Benutzer > Rollen und Richtlinien** die
   Rolle **Anonymous** und fügen Sie `smarty` der SiteAccess-Einschränkung ihrer `user/login`-Richtlinie hinzu. Ohne
   das verlangt jede Seite eine Anmeldung.

5. Leeren Sie die Caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezcache.php --clear-tag=template --allow-root-user
   php bin/php/ezcache.php --clear-id=content --allow-root-user
   ```

   oder führen Sie `./console exp:velocity deploy --allow-root-user` aus, das auch PHP-FPM neu lädt und Velocity neu
   startet.

Nichts muss aktiviert werden: Der Kernel rendert `.stpl`-Templates für jeden Siteaccess. Die Einzelheiten, der
Konverter und die Prüfungen nach dem Konvertieren stehen in [der Anleitung zum Konverter](../../anleitungen/smarty-templates-konverter.md).

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `settings/smarty.ini` | `SmartySettings` | `LibraryPath` | leer | Smarty 5, wenn es nicht im Autoload von Composer ist: das Verzeichnis, das `src/Smarty.php` enthält, relativ zum Wurzelverzeichnis der Installation oder absolut |
| `settings/smarty.ini` | `SmartySettings` | `CompileCheck` | `enabled` | Smarty kompiliert ein geändertes `.stpl` neu; `disabled` prüft nie (Produktion; leeren Sie nach einer Änderung den Template-Cache) |
| `settings/smarty.ini` | `SmartySettings` | `Debugging` | `disabled` | `enabled` öffnet die Debug-Konsole von Smarty |
| `settings/smarty.ini` | `SmartySettings` | `FunctionList[]` | die `*_view_gui`-Familie | Exponential-Template-Funktionen, die immer als Smarty-Tags aufrufbar sind |
| `settings/site.ini` | `Cache_smarty` | `path`, `tags[]` | `smarty`, `template` | Smartys kompilierte Templates in `<cache directory>/smarty/`, mit den Template-Caches geleert |
| `settings/site.ini` | `TemplateSettings` | `AutoloadPathList[]` | enthält `lib/smarty/classes/` | registriert den Operator `exp_smarty_render`, den der Stub aufruft |

Überschreiben Sie sie in `settings/override/smarty.ini.append.php` oder je Siteaccess, wie jede INI-Datei.

## Befehle

| Befehl | Tut |
|---|---|
| `./console exp:template:convert:tpl-to-stpl` (`php bin/php/exptemplateconverttpltostpl.php`) | wandelt die `.tpl`-Templates eines Designs, oder mit `--file` eines, in `.stpl` um; `--override-ini` schreibt die Override-Regeln; `--dry-run`, `--force` |
| `./console exp:template:convert:stpl-to-tpl` (`php bin/php/exptemplateconvertstpltotpl.php`) | die Umkehrung: `.stpl` zu `.tpl` |
| `./console exp:template:convert --from-engine=tpl --to-engine=stpl` (`php bin/php/exptemplateconvert.php`) | der allgemeine Befehl; dieselbe Arbeit mit den Engines als Optionen |
| `php bin/php/ezcache.php --clear-id=smarty` | leert Smartys kompilierte Templates und die erzeugten Funktionsaufrufe (werden auch mit `--clear-tag=template` geleert) |

Jede Option, jede Ausgabezeile und jeder Exit-Code (0 `PASS`, 1 `FAIL` mit Fehlern, 2 falsche Optionen, 3 eine
Richtung, die nicht verfügbar ist): [Templates zwischen Template-Engines konvertieren](../../anleitungen/template-konvertierung.md).
Die Befehlsreferenz: [exp:template:convert](../../beispiele/befehle/exptemplateconvert.md),
[exp:template:convert:tpl-to-stpl](../../beispiele/befehle/exptemplateconverttpltostpl.md) und
[exp:template:convert:stpl-to-tpl](../../beispiele/befehle/exptemplateconvertstpltotpl.md).

## Innenleben

Der Smarty-Code liegt in `lib/smarty/classes/`, die Konvertierung ganzer Designs in `lib/templatebridge/classes/`; das
Template-System selbst hat zwei kleine Einstiegspunkte, in einer Klasse.

| Teil | Rolle |
|---|---|
| `eZTemplateFileResource::handleResourceData()`, `cacheKey()` | die beiden Einstiegspunkte des Kernels: Ein `.stpl` wird dem Template-System als der Stub `{exp_smarty_render( '<file>' )}` übergeben, kompiliert unter einem eigenen Schlüssel |
| `expSmartyOperator` | der Operator `exp_smarty_render`, den der Stub aufruft, registriert über `[TemplateSettings] AutoloadPathList[]=lib/smarty/classes/` |
| `expSmartyRenderer` | rendert ein `.stpl` mit Smarty: die Smarty-Instanz je Prozess und Satz von Designs, die Variablen hinein und wieder heraus, ein `.stpl`, das von einem `.stpl` eingebunden wird, als Smarty-Subtemplate, Operator-Aufrufe mit ihrem Eingabemodus, Funktionsaufrufe, Fehlermeldungen |
| `expSmartyExtension` | die Smarty-5-Erweiterung, die Operatoren zu Modifiern und Template-Funktionen zu Tags oder Blöcken macht, mit dem Präfix `exp_` und den `exp*`-Helfern; Antworten werden je Prozess gemerkt |
| `expSmartyObjectProxy` | liest Exponential-Objekte mit Smartys Punktsyntax, nur lesend |
| `expSmartyFunction`, `expSmartyBlockFunction` | Template-Funktionen als Tags und als Blöcke |
| `expSmartyIncludeFunction`, `expSmartySetFunction`, `expSmartyUndefAllFunction`, `expSmartyFetchFunction` | `{expinclude}`, `{expset}`, `{expundefall}`, `{expfetch}` |
| `expSmartySetBlock`, `expSmartyCacheBlock`, `expSmartyRunOnceBlock` | `{expsetblock}` und `{expappendblock}`, `{expcacheblock}`, `{exprunonce}` |
| `expSmartyVoid` | der Wert von `expvoid()`, ein leerer Operator-Parameter |
| `expSmartyConverter`, `expSmartyReverseConverter` | die Konverter eines Template-Texts, tpl zu stpl und zurück, registriert in `site.ini [TemplateBridgeSettings] ConverterList` |
| `expTemplateDesignConverter` (`lib/templatebridge/classes/`) | die Arbeit der Befehle `exp:template:convert`: durchläuft ein Design, behält von Hand geänderte Dateien (die Markierung), schreibt die Override-Einstellungen |

Tests, ohne Datenbank: `tests/tests/lib/smarty/expSmartyTemplateRenderTest.php` (Rendern, Zurückschreiben, der Stub
und sein Schlüssel, der Konverter an den Konstrukten der Media-Portierung), `expSmartyOperatorParityTest.php` (jeder
Engine- und Operator-Fall der eigenen Tests der Template-Engine ergibt dieselbe Ausgabe wie ein `.stpl`),
`expSmartyReverseConverterTest.php` (der Rückweg) und
`tests/tests/kernel/private/classes/commands/expTemplateConvertCommandTest.php` (die Befehle, beide Richtungen an einem
Design, der Hin- und Rückweg eines ganzen Designs). Ohne installiertes Smarty 5 werden die Fälle zum Rendern
übersprungen.

## Wer es verwenden darf

Es gibt keine eigene Richtlinie: Ein `.stpl` ist eine Design-Datei, und wer die Design-Dateien ändern darf, darf eines
schreiben. Ein `.stpl` kann jede Fetch-Funktion und Template-Funktion aufrufen, die ein `.tpl` aufrufen kann, mit
denselben Berechtigungsprüfungen, da sie über dasselbe Template-System laufen. Smarty fügt hinzu, was ein `.tpl` nicht
kann (Dateien mit `{fetch file=...}` lesen, die Anfrage über `$smarty.get` und `$smarty.server`): Es ist keine
Smarty-Sicherheitsrichtlinie gesetzt; behandeln Sie `.stpl`-Dateien also als Code und geben Sie das Recht, Templates in
der Administration zu bearbeiten, nur Personen, die Sie auch PHP ändern ließen. Siehe
[Sicherheit und Maskierung](../../anleitungen/smarty-templates-sicherheit.md).

## Verwandte Seiten

- [Templates und Design](../../anleitungen/templates-und-design.md): wie ein Template gefunden und überschrieben wird
- [Template-Pfad-Kommentare](template-pfad-kommentare.md) und [die Exp-Debug-Leiste](exp-debug-leiste.md)
- [Caches](../../anleitungen/caches.md): die Template-Caches, mit denen der Smarty-Cache geleert wird
- [Velocity: Exponential in einem Webserver mit dauerhaften Workern ausführen](velocity-server-mit-dauerhaften-workern.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
