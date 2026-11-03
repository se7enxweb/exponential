# Smarty-Templates: Leistung (Geschwindigkeit)

Diese Seite ist für Template-Entwickler, Site-Bauer und Betreiber, die schnelle `.stpl`-Seiten möchten, und für alle,
die wissen wollen, warum sie schnell sind. Sie erzählt die ganze Geschichte, wie Smarty-Templates in Exponential
schnell wurden: was anfangs langsam war und warum, jede Änderung, die vorgenommen wurde, mit ihrem Grund, eine Änderung,
die bewusst nicht vorgenommen wurde, die Zahlen vorher und nachher, was jede Cache-Ebene beiträgt, das Seiten-Cache-Muster
der Media-Portierung, wie man misst, ohne sich selbst zu täuschen, wie sich Velocity und PHP-FPM unterscheiden, die
Einstellungen, auf die es ankommt, eine Checkliste und eine Liste von Fallstricken.

Alle Zahlen auf dieser Seite wurden am 28. Oktober 2026 auf einer Entwicklungsinstallation gemessen, mit dem
Media-Design (das `.tpl`-Original in `extension/sevenx_themes_media/design/media/` und seine Smarty-Portierung in
`.../design/smarty_media/`, 516 Templates), ausgeliefert von zwei Siteaccesses, die denselben Inhalt zeigen: `site` mit
dem `.tpl`-Design und `smarty` mit dem `.stpl`-Design. Die Adressen unten verwenden `https://www.example.com`.

Weiterführend: [Caching und Leistung](smarty-templates-caching.md) (die Cache-Ebenen als Referenz) und die Funktionsseite
[Smarty-Templates (.stpl) neben .tpl](../funktionen/6.0/smarty-templates.md#geschwindigkeit).

## Kurz gesagt

- **Eine `.stpl`-Seite ist jetzt schneller als dieselbe `.tpl`-Seite**: 18 bis 25 % auf Apache mit PHP-FPM, 32 bis
  60 % auf Velocity, bei Byte für Byte identischem HTML.
- **Anfangs war sie viel langsamer.** Jedes von einem `.stpl` eingebundene `.stpl` ging den ganzen Weg durch das
  Template-System und startete ein neues Smarty-Rendern, bei dem jede Variable umgewandelt und zurückgeschrieben wurde;
  eine Startseite tat das 266-mal.
- **Sechs Änderungen** machten den Unterschied: Einbindungen als Smarty-Subtemplates, eine Smarty-Instanz pro Satz von
  Designs, Werte einmal pro Seite umgewandelt, unveränderte Variablen nicht zurückgeschrieben, Namensauflösungen pro
  Prozess gemerkt und das Zurückschreiben in einen Post-Filter verlegt. Operatoraufrufe wurden gemessen und so
  belassen: Sie waren günstig.
- **Der größte einzelne Gewinn für echte Besucher ist das Caching**: Die Media-Portierung hält ihr ganzes Pagelayout in
  einem `{expcacheblock}` und rendert nur das eigene Benutzermenü des Besuchers frisch, was die meisten
  Velocity-Anfragen auf 0,03 bis 0,07 s brachte.
- **Messen Sie sorgfältig**: die Skriptzeit der Debug-Leiste, der Median von zehn Anfragen, dieselben Caches und
  derselbe Debug-Zustand für beide Engines und ein Server, der nicht überlastet ist.

## Die Ergebnisse

Gemessen mit der Skriptzeit der Debug-Leiste, dem Median von 10 Anfragen je Seite, mit warmem Content-View-Cache und
eingeschalteter Debug-Leiste für beide Engines; der `.tpl`- und der `.stpl`-Siteaccess zeigten Byte für Byte dasselbe
HTML.

| Seite | `.tpl`, Apache + PHP-FPM | `.stpl`, Apache + PHP-FPM | `.tpl`, Velocity | `.stpl`, Velocity |
|---|---|---|---|---|
| Eine Kategorieseite (`/fitness`) | 0,119 s | 0,097 s (18 % schneller) | 0,102 s | 0,069 s (32 % schneller) |
| Eine Rezeptliste (`/recipes`) | 0,102 s | 0,077 s (25 % schneller) | 0,093 s | 0,037 s (60 % schneller) |
| Die Startseite | — | 0,104 s | — | 0,036 s |

- Die `.tpl`-Startseite wurde in diesen Läufen aus dem Ganzseiten-Cache beantwortet und hatte daher keine eigene
  Skriptzeit zum Vergleich (siehe [warum](#der-ganzseiten-cache-und-der-debug-bericht) unten).
- Allein in der Template-Verarbeitung (dem Akkumulator der Leiste) brauchte die `.stpl`-Kategorieseite 74 ms gegenüber
  90 ms für die `.tpl`-Seite, mit 14 MB Speicher gegenüber 18 MB.
- Viele `.stpl`-Anfragen auf Velocity wurden in 0,024 bis 0,040 s beantwortet.

Die Zahlen hängen von der Last des Servers und von seiner Hardware ab; was sich auf eine andere Installation übertragen
lässt, ist das Verhältnis, und auch das nur, wenn beide Engines unter identischen Bedingungen gemessen werden.

## Wo es begann: warum .stpl anfangs langsam war

Die erste funktionierende Version renderte jedes `.stpl` korrekt, und zwar langsam: Die Smarty-Seiten waren viel
langsamer als die `.tpl`-Seiten, aus denen sie konvertiert wurden. Das Profil einer Startseite zeigte, wo die Zeit blieb.

| Befund auf einer Startseite | Anzahl oder Kosten |
|---|---|
| Vollständige Rendervorgänge über die Brücke | 266 |
| Operatoraufrufe über die Brücke | 5.483 |
| Suchen der Smarty-Instanz (`expSmartyRenderer::smarty()`): die Template-Verzeichnisse auf der Platte geprüft und die Einstellungen gehasht, bei jedem Rendern | etwa 1,7 ms pro Aufruf |
| Umwandeln jeder Variablen des Namensraums für Smarty (`wrap()`) bei jedem Rendern | etwa 190 ms auf dieser Seite unter Last |
| Zurückschreiben jeder Variablen nach jedem Rendern, jede tief mit ihrem alten Wert verglichen (`writeBack()`) | etwa 240 ms auf dieser Seite unter Last |

Die Ursache war bei allen dieselbe: **Jedes von einem `.stpl` eingebundene `.stpl` war ein vollständiges Rendern.** Die
Einbindung ging von Smarty zum Template-System (`{expinclude}`), das die Datei über die Design-Ressource lud, ein
`.stpl` fand, dessen Stub `{exp_smarty_render( '<file>' )}` zurückgab, den Stub ausführte, der
`expSmartyRenderer::render()` aufrief, das die Smarty-Instanz suchte, jede Variable des Namensraums in Smarty-Werte
umwandelte (Objekte in Proxys, Arrays Element für Element durchlaufen), ein neues Smarty-Template anlegte, es abrief
und dann jede Variable noch einmal durchlief, um zurückzuschreiben, was sich geändert hatte. Eine Seite aus 37
Smarty-Templates wurde zu 266 Rendervorgängen, von denen jeder all das bezahlte.

## Was geändert wurde, und warum

### 1. Ein von einem .stpl eingebundenes .stpl ist ein Smarty-Subtemplate

Wenn `{expinclude}` ohne `name=` verwendet wird (das eingebundene Template teilt sich den Namensraum des einbindenden,
wie bei einem `.tpl`-`{include}`), fragt die Brücke zuerst die Design-Ressource, für welche Datei die URI steht, ohne
sie zu laden, und rendert diese Datei, wenn sie ein `.stpl` ist, als Smarty-Subtemplate des einbindenden Templates.

`lib/smarty/classes/expsmartyincludefunction.php`:

```php
// A .stpl in the shared namespace runs as a Smarty subtemplate of this one: the same file the template
// system would load (the design resource, its override rules and keys), the same variables, without the
// detour through the template system, its stub and a second render.
if ( $shared )
{
    $file = expSmartyRenderer::resolveURI( $tpl, $uri, $templateName );
    if ( $file !== null && expSmartyRenderer::isSmartyTemplate( $file ) )
        return expSmartyRenderer::renderSubtemplate( $template, $file, $params, $uri, $templateName );
}
```

`expSmartyRenderer::resolveURI()` fragt die Ressource mit `eZTemplate::RESOURCE_QUERY`, dem Modus, in dem die Ressource
einen Namen auflöst (Override-Regeln, Design-Fallback, die Design-Schlüssel der Seite) und die Datei zurückgibt, ohne
sie zu laden:

```php
$resource = $tpl->resourceFor( $uri, $resourceName, $templateName );
if ( !is_object( $resource ) )
    return null;
$resourceData = $tpl->resourceData( $resource, $uri, $resourceName, $templateName );
$extraParameters = array();
if ( !$resource->handleResource( $tpl, $resourceData, eZTemplate::RESOURCE_QUERY, $extraParameters ) )
    return null;
```

`renderSubtemplate()` legt das Kind-Template mit dem einbindenden als Elternteil an, sodass es die Variablen des
einbindenden Templates ohne jede Umwandlung sieht, weist die Einbindungsparameter zu, ruft es ab und gibt zurück, was
das Kind definiert oder geändert hat, an das Elternteil. Nichts geht durch die Namensräume des Template-Systems.

**Warum es richtig ist**: Die Datei ist die, die das Template-System geladen hätte (dieselbe Ressource, dieselben
Override-Regeln, dieselben Schlüssel), die Variablen sind dieselben, und was die Einbindung definiert, ist danach
sichtbar, wie zuvor. **Was es brachte**: Die Startseite ging von 266 Rendervorgängen über die Brücke auf 37. Bei
eingeschalteter Debug-Ausgabe ist jedes Subtemplate weiterhin ein Eintrag im Template-Baum der Debug-Leiste, unter dem
Template, das es eingebunden hat.

Eine Einbindung mit `name=` oder eines `.tpl` geht weiterhin durch das Template-System, weil dort die Namensräume
zählen.

### 2. Eine Smarty-Instanz pro Satz von Designs

`expSmartyRenderer::smarty()` wird für jedes `.stpl` aufgerufen, das eine Seite rendert. Früher prüfte es bei jedem
Aufruf jedes Template-Verzeichnis jedes Designs auf der Platte und hashte die Einstellungen. Jetzt wird die Instanz nach
den Design-Basen und dem Cache-Verzeichnis gemerkt und mit einem einzigen Array-Zugriff gefunden:

```php
// Called for every .stpl a page renders: the instance of the same design bases and cache directory is
// found without checking the directories and the settings again (an INI change needs a deploy anyway).
$bases = eZTemplateDesignResource::allDesignBases();
$basesKey = eZSys::cacheDirectory() . "\n" . implode( "\n", $bases );
if ( isset( self::$instancesByBases[$basesKey] ) )
    return self::$instancesByBases[$basesKey];
```

**Warum es sicher ist**: Die Template-Verzeichnisse ergeben sich aus den Design-Basen, und die Einstellungen, die die
Instanz formen (`CompileCheck`, `Debugging`, `Security` und ihre Listen), ändern sich nur mit einem Deploy, der die
Worker neu startet. Ein Siteaccess mit anderen Designs bekommt seine eigene Instanz.

### 3. Werte werden einmal pro Seite umgewandelt

Exponential-Objekte erreichen Smarty als schreibgeschützte Proxys, Arrays Element für Element umgewandelt (`wrap()`),
und gehen ausgepackt zurück (`unwrap()`). Dasselbe `$node`, `$module_result` oder `$view_parameters` wurde für jedes
`.stpl` der Seite erneut umgewandelt. Jetzt merkt sich die Brücke pro Variablenname das letzte Paar aus rohem und
umgewandeltem Wert:

```php
public static function wrapNamed( $name, $value )
{
    if ( isset( self::$pairs[$name] ) && self::$pairs[$name][0] === $value )
        return self::$pairs[$name][1];
    if ( !is_array( $value ) && !is_object( $value ) )
        return $value;
    $wrapped = self::wrap( $value );
    self::$pairs[$name] = array( $value, $wrapped );
    return $wrapped;
}
```

**Warum es schnell ist**: `===` auf demselben Objekt ist ein einziger Zeigervergleich, und bei demselben Array vergleicht
PHP zuerst das Array, das es teilt, und das ist bei einer unveränderten Variablen dasselbe Array: Die Antwort steht fest,
ohne den Wert zu durchlaufen. `unwrapNamed()` macht dasselbe in der Gegenrichtung. Die Paare werden verworfen, wenn das
äußerste Rendern einer Anfrage endet, sodass nichts von einer Anfrage die nächste erreicht.

### 4. Unveränderte Variablen werden nicht zurückgeschrieben

Nach einem Rendern schreibt die Brücke zurück, was das Template definiert, geändert oder auf `null` gesetzt hat, wie es
`{def}`, `{set}` und `{undef}` tun. Früher schrieb sie alles zurück. Jetzt wird eine Variable übersprungen, deren Wert
der ist, mit dem sie hereinkam:

```php
$value = self::unwrapNamed( $name, $value );
if ( array_key_exists( $name, $input ) && $input[$name] === $value )
    continue;
```

Mit der Paarbildung aus Änderung 3 ist dieser Vergleich wieder ein Zeigervergleich für Objekte und geteilte Arrays.

### 5. Namensauflösungen werden pro Prozess gemerkt

Ein kompiliertes Smarty-Template fragt die Erweiterung jedes Mal nach dem Callback eines Modifiers, wenn es einen
verwendet, nicht einmal beim Kompilieren. Auf einer Seite mit Tausenden von Operatoraufrufen lief die Auflösung (ist es
ein Hilfsmittel, gibt es einen Operator dieses Namens, gilt `exp_`) Tausende Male. `expSmartyExtension` merkt sich
jetzt jede Antwort, auch "nicht gefunden":

```php
private static $found = array( 'modifier' => array(), 'function' => array(), 'block' => array() );

public function getModifierCallback( string $modifierName )
{
    if ( !array_key_exists( $modifierName, self::$found['modifier'] ) )
        self::$found['modifier'][$modifierName] = $this->findModifierCallback( $modifierName );
    return self::$found['modifier'][$modifierName];
}
```

**Warum es sicher ist**: Welche Operatoren und Funktionen es gibt, ändert sich nicht, während ein Prozess läuft; eine
neu aktivierte Erweiterung braucht ohnehin einen Neustart der Worker.

### 6. Das Zurückschreiben geht über einen Post-Filter

Smarty 5 setzt die Variablen eines Templates zurück, wenn `fetch()` zurückkehrt, sodass es nach dem Rendern nichts mehr
zum Zurückschreiben gab, außer man durchlief das Template, während es lief. Die Brücke fügt jetzt am Ende jedes
kompilierten Templates einen Aufruf an, einen Smarty-Post-Filter, der die Variablen des Templates in dem Moment
festhält, in dem es endet:

```php
public static function appendCapture( $code )
{
    return $code . "<?php \\expSmartyRenderer::captureVariables( \$_smarty_tpl ); ?>\n";
}
```

`captureVariables()` speichert sie unter der ID des Template-Objekts; `render()` und `renderSubtemplate()` holen sie
nach `fetch()` heraus und schreiben von dort zurück. Das kostet eine Array-Kopie pro Template, einmal in das Template
hineinkompiliert.

### 7. Nicht gemacht: native Schnellpfade für Operatoren

Eine naheliegende Idee war, den meistgenutzten Operatoren (`eq`, `ne`, `and`, `or`, `concat` ...) eine native
Smarty-Implementierung zu geben, am Template-System vorbei. Zuerst wurde der Operatoraufruf über die Brücke gemessen:
`eq`, auf einer Seite 1.158-mal aufgerufen, brauchte insgesamt 4,8 ms, etwa 4 Mikrosekunden pro Aufruf. Dort blieb die
Zeit nicht, und eine zweite Implementierung jedes Operators müsste das Verhalten des Template-Systems genau treffen
(`eq` vergleicht locker und nimmt mehrere Parameter an; `and` gibt den letzten wahren Wert zurück, `or` den ersten). Der
Paritätstest (`tests/tests/lib/smarty/expSmartyOperatorParityTest.php`) garantiert, dass jeder Operatorfall aus den
eigenen Tests der Template-Engine in `.stpl` dieselbe Ausgabe ergibt, weil jeder Operator den eigenen Code des
Template-Systems ausführt. Schnellpfade hätten diese Garantie gegen ein paar Millisekunden eingetauscht. Sie wurden
nicht gebaut.

### Die Reihenfolge der Arbeit

| Schritt | Was sich änderte | Wirkung auf die Startseite |
|---|---|---|
| Start | jede `.stpl`-Einbindung ein vollständiges Rendern | 266 Rendervorgänge, 5.483 Operatoraufrufe, `wrap()` und `writeBack()` jeder Variablen jedes Mal |
| 1 | Einbindungen von `.stpl` als Subtemplates | 37 Rendervorgänge über die Brücke |
| 2 | Smarty-Instanz pro Design-Basen | keine Verzeichnisprüfungen und kein Einstellungs-Hash pro Rendern (vorher etwa 1,7 ms pro Aufruf) |
| 3, 4 | Paarbildung und Überspringen unveränderter Variablen | die Umwandlungen und das Zurückschreiben durchlaufen keine unveränderten Werte mehr |
| 5 | Auflösungen gemerkt | eine Auflösung pro Name und Prozess |
| 6 | Erfassung per Post-Filter | Zurückschreiben, ohne das Template zu durchlaufen |
| Cache | das ganze Pagelayout in einem `{expcacheblock}` | die meisten Velocity-Anfragen 0,03 bis 0,07 s |

## Was bei jeder Anfrage weiterhin bezahlt wird

| Kosten | Auf PHP-FPM | Auf Velocity |
|---|---|---|
| Laden der Smarty-Bibliothek | etwa 3 ms pro Anfrage | einmal pro Worker |
| Anlegen der Smarty-Instanz | etwa 2,5 ms pro Anfrage | einmal pro Worker |
| Die erste Anfrage nach einem kalten Opcode-Cache | etwa 75 ms | einmal pro Worker-Start |
| Jedes über das Template-System gerenderte `.stpl` (das äußerste, `.tpl`-Einbindungen von `.stpl`, `name=`-Einbindungen) | ein Stub-Lauf und ein Rendern | dasselbe |
| Jeder Operatoraufruf | etwa 4 Mikrosekunden für einen einfachen | dasselbe |
| Jede Template-Funktion (`{attribute_view_gui}` ...) | ein Aufruf des Template-Systems über ein erzeugtes `.tpl` | dasselbe |

## Velocity und PHP-FPM

PHP-FPM beginnt jede Anfrage bei null: Der Kernel bootet, die Einstellungen werden gelesen, Smarty wird geladen und
seine Instanz angelegt, und der Bootstrap mit dem Debug-Bericht kostet 40 bis 60 ms vor der eigentlichen Seite.
Velocity hält seine Worker am Laufen: Der Kernel, die Smarty-Instanz, die kompilierten Templates im Opcode-Cache und
die gemerkten Auflösungen bleiben zwischen Anfragen im Speicher. Deshalb erreicht eine `.stpl`-Seite auf Velocity
0,04 s oder weniger und bleibt auf PHP-FPM mit eingeschaltetem Debug-Bericht bei 0,08 bis 0,10 s.

Ein Worker behält von den Variablen einer Anfrage nichts für die nächste: Die Paare aus Änderung 3 und die erfassten
Variablen aus Änderung 6 werden verworfen, wenn das äußerste Rendern einer Anfrage endet. Ein Test, der dieselbe Seite
25-mal mit einer Cookie-Datei auf Velocity neu lud, zeigte keine Verlangsamung von einer Anfrage zur nächsten.

Was ein dauerhafter Worker behält und was Sie wissen müssen:

- die Smarty-Instanz und die Einstellungen, aus denen sie gebaut wurde: Nach einer Änderung von `smarty.ini` starten
  Sie die Worker neu (`./console exp:velocity deploy --allow-root-user` erledigt das);
- die gemerkten Namen: Ein Operator oder eine Funktion, die später verfügbar wird, ist nach einem Neustart bekannt;
- der Eingabemodus jedes Operators und `ParameterOperatorList[]`: einmal pro Prozess gelesen.

## Die Caches, und was jeder hier bewirkte

| Ebene | Gleich für `.tpl` und `.stpl`? | Was wir gelernt haben |
|---|---|---|
| Ganzseiten-Cache (rollenbewusster HTTP-Cache, `httpcache.ini`) | ja, er hält Ausgabe | speichert nie eine Seite, die einen Debug-Bericht trägt; eine Seite, die ein anderer Besucher in den Cache gebracht hat, zeigt `HIT` |
| Antwort-Cache von Velocity | ja | antwortet, bevor ein Worker gewählt wird |
| Content-View-Cache | ja | 0 SQL-Abfragen bei einer wiederholten Vollansicht in beiden Engines |
| `{expcacheblock}` | derselbe Cache wie `{cache-block}` | braucht `[TemplateSettings] TemplateCache=enabled`; ein Block um das ganze Pagelayout brachte am meisten |
| Kompilierte Stubs und Smartys kompilierte Templates | nur `.stpl` | einmal geschrieben; `CompileCheck` entscheidet, ob eine Änderung bemerkt wird |

### Der Ganzseiten-Cache und der Debug-Bericht

Der Ganzseiten-Cache (der rollenbewusste HTTP-Cache, für die Siteaccesses in `[HttpCacheSettings]
CachedSiteAccesses[]` von `httpcache.ini` eingeschaltet) hält ganze Seiten für anonyme Besucher. Er speichert nie eine
Antwort, die den Debug-Bericht trägt (die Exp-Debug-Leiste oder den klassischen Bericht), und sagt das auch:

```bash
curl -s -o /dev/null -D - https://www.example.com/fitness | grep -i x-exp-cache
```

```
x-exp-cache: BYPASS (debug report)
```

Zwei Folgen für das Messen:

- **Mit eingeschalteter Debug-Leiste sehen Sie immer ein Rendern**, nie den Cache. Das ist, was Sie wollen, wenn Sie
  Templates messen.
- **Ohne sie zeigt eine Seite, deren Speicherung ein anderer Besucher schon ausgelöst hat, `x-exp-cache: HIT`**, und
  ihre Zeit sagt nichts über Templates. So kam es, dass die `.tpl`-Startseite oben keine Skriptzeit zum Vergleich hatte:
  Sie kam aus dem Cache.

### Der Content-View-Cache

Eine Vollansicht wird als Ausgabe pro Knoten, Ansicht, View-Parametern, Sprache sowie den Rollen und Beschränkungen des
Besuchers gehalten. Er arbeitet für beide Engines gleich: Eine wiederholte Vollansicht führte in keiner der beiden eine
einzige SQL-Abfrage aus. Das Pagelayout drumherum gehört nicht zum View-Cache; es wird bei jeder Anfrage gerendert,
sofern es kein Cache-Block hält.

### {expcacheblock} braucht TemplateCache

`{expcacheblock}` speichert nur mit `[TemplateSettings] TemplateCache=enabled` in den Template-Block-Cache; ist es aus,
läuft der Rumpf des Blocks bei jeder Anfrage, und nichts wird gespeichert, ohne jede Meldung. Prüfen Sie das zuerst,
wenn ein Block nicht zu cachen scheint.

## Die Seite in einem Cache-Block

Das Pagelayout ist der Teil jeder Seite, den der View-Cache nicht hält: der Kopf, die Menüs, die Layout-Zonen von
Exponential Layouts, der Fuß. Die Media-Portierung hält all das in **einem** `{expcacheblock}`. Die erste Version hatte
drei Blöcke (Kopf, Layout, Seitenende); sie zu einem zusammenzulegen, brachte die Velocity-Zahlen auf 0,03 bis 0,07 s
herunter, weil eine gecachte Anfrage dann fast nichts vom Pagelayout ausführt.

Die Schlüssel des Blocks, aus `templates/pagelayout.stpl`:

```smarty
{$el_page_user = fetch('user', 'current_user')}
{expsetblock variable='exp_dynamic_parts' scope='global'}
1{/expsetblock}
{capture assign=el_page_html}
{expcacheblock id=4 keys=(['pagelayout-page', $module_result.content_info.node_id|default:0, $module_result.uri|default:'', $module_result.content|md5, $el_page_user.role_id_list|exp_implode:',', $el_page_user.limited_assignment_value_list|exp_implode:',', $el_page_user.is_logged_in])}
... the whole page ...
{/expcacheblock}
{/capture}
```

| Schlüssel | Warum |
|---|---|
| `'pagelayout-page'` | ein Name, damit der Schlüssel nie auf den eines anderen Blocks trifft |
| `$module_result.content_info.node_id` | der angezeigte Knoten |
| `$module_result.uri` | Seiten ohne Knoten (Suche, Benutzerseiten, Fehlerseiten) |
| `$module_result.content\|md5` | alles, was die Ansicht erzeugt hat: Blättern, Suchergebnisse, Formularfehler ergeben eine andere Seite |
| `$el_page_user.role_id_list` | was der Besucher sehen darf, hängt von seinen Rollen ab |
| `$el_page_user.limited_assignment_value_list` | Rollen, die auf einen Teilbaum oder eine Sektion beschränkt sind |
| `$el_page_user.is_logged_in` | anonyme und angemeldete Seiten unterscheiden sich auch bei denselben Rollen |
| der Siteaccess | vom Block selbst hinzugefügt |

### Der Teil pro Besucher als Platzhalter

Das Benutzermenü mit dem Namen des Besuchers und der Anzahl im Warenkorb gehört dem Besucher allein und darf nie
gespeichert werden. Während der Seitenblock gebaut wird, sagt ein globales Flag das, und der Kopfbereich schreibt eine
Markierung statt des Menüs. `templates/pagelayout/header.stpl`:

```smarty
{if and(is_set(expvar('exp_dynamic_parts', '')), eq(expvar('exp_dynamic_parts', ''), '1'))}
<!--exp-dynamic:user_menu-->{else}
{expinclude uri='design:pagelayout/header/user_menu.tpl'}{/if}
```

Nach dem Block, ob gecacht oder frisch gerendert, löscht das Pagelayout das Flag, rendert das Menü und setzt es an die
Stelle der Markierung:

```smarty
{expsetblock variable='exp_dynamic_parts' scope='global'}
0{/expsetblock}
{capture assign=el_user_menu}
{expinclude uri='design:pagelayout/header/user_menu.tpl'}{/capture}
{($el_page_html|exp_explode:'<!--exp-dynamic:user_menu-->')|exp_implode:$el_user_menu}{$el_page_html = null}
```

Das Flag ist global (`{expsetblock ... scope='global'}`, gelesen mit `expvar(..., '')`), weil der Kopfbereich mehrere
Einbindungsebenen tiefer liegt; die Markierung ist ein HTML-Kommentar, harmlos, falls sie je ausgegeben wird. Das
`.tpl`-Pagelayout des Media-Designs cacht sein Layout auf dieselbe Weise (`{set-block scope=global
variable=exp_dynamic_parts}`, `{cache-block keys=...}`,
`{$el_layout_html|explode( '<!--exp-dynamic:user_menu-->' )|implode( $el_user_menu )}`), sodass beide Engines
Gleiches mit Gleichem cachen.

### Die Teile pro Besucher finden: eine durchgearbeitete Bestandsaufnahme

Bevor Sie eine ganze Seite cachen, muss jeder Teil gefunden werden, der vom Besucher abhängt. Die Bestandsaufnahme des
Media-Designs:

| Teil | Hängt ab von | Wie er behandelt wurde |
|---|---|---|
| Benutzermenü mit der Anzahl im Warenkorb (`pagelayout/header/user_menu`) | der Sitzung, dem Warenkorb | der Platzhalter oben: bei jeder Anfrage frisch gerendert |
| Die Merkzettel-Schaltfläche (`content/parts/product_buy_form.stpl`) | `is_logged_in` | abgedeckt durch den Schlüssel `is_logged_in` |
| Der Titel einer Fehlerseite (`pagelayout/head/title.stpl`: "Please sign in to see this page" oder "You don't have access to this page" bei einem Zugriffsfehler) | `is_logged_in` | abgedeckt durch den Schlüssel `is_logged_in` |
| Menüs und Listen, die ausblenden, was der Besucher nicht lesen darf | Rollen und Beschränkungen | abgedeckt durch `role_id_list` und `limited_assignment_value_list` |
| Formulare mit einem Formular-Token | der Sitzung | nichts zu tun: Das Token wird nach dem Rendern zur Ausgabe hinzugefügt |

So finden Sie sie in Ihrem Design: Durchsuchen Sie die Templates nach `current_user`, `is_logged_in`, `basket`,
`session`, `ezhttp`, `ezpreference`, `csp_nonce` und nach Fetches der Daten des aktuellen Benutzers; öffnen Sie jede
Seite angemeldet und abgemeldet und vergleichen Sie.

### Was außerhalb eines gecachten Blocks bleiben muss

- **Inhalt pro Besucher**: der aktuelle Benutzer, die Sitzung, der Warenkorb, Meldungen. Verwenden Sie einen
  Platzhalter.
- **Skripte und Stile, die beim Rendern angefordert werden**: `ezscript_require()` und `ezcss_require()` registrieren
  eine Datei für den Kopf oder das Ende der Seite, wenn sie laufen. In einem gecachten Block laufen sie bei einer
  gecachten Anfrage nicht, und die Datei fehlt auf der Seite. Fordern Sie sie außerhalb des Blocks an, oder halten Sie
  den Code, der sie ausgibt, im selben Block.
- **`csp_nonce()`**: Es ändert sich bei jeder Anfrage; eine gespeicherte Nonce passt nicht mehr zum Header der Seite,
  und der Browser blockiert das Skript.
- **Kein Problem: Formulare.** Das Formular-Token angemeldeter Besucher (`ezformtoken`) wird jedem
  `<form method="post">` in der Ausgabe hinzugefügt, nachdem alle Templates gelaufen sind, sodass ein Formular, das aus
  einem Cache-Block kommt, das aktuelle Token bekommt.

### Was den Block verfallen lässt

| Ereignis | Wirkung |
|---|---|
| Eine Inhaltsveröffentlichung | lässt jeden Template-Block-Cache verfallen, sodass die nächste Anfrage die Seite neu rendert |
| Eine Layout-Veröffentlichung oder eine Änderung einer Layout-Regel (Exponential Layouts) | lässt ebenfalls jeden Template-Block-Cache verfallen: `explayouts_core` ruft `eZContentObject::expireTemplateBlockCache()` auf, weil ein Pagelayout das gerenderte Layout in einem Block hält |
| `expiry` des Blocks | sein Alter (der Standard ist zwei Stunden) |
| `php bin/php/ezcache.php --clear-tag=template` | alles, was mit Templates zu tun hat, die Blöcke eingeschlossen |

## Wie man misst

### Die Debug-Leiste

Schalten Sie die Debug-Ausgabe für Ihre eigene Adresse ein ([die Exp-Debug-Leiste](../funktionen/6.0/exp-debug-leiste.md)).
Die Seite wird dann immer gerendert (der Ganzseiten-Cache lässt sie durch), und die Leiste zeigt:

| Wo | Was zu lesen ist |
|---|---|
| Die Leiste | die Skriptzeit, die Anzahl der SQL-Abfragen, den Spitzenspeicher |
| Reiter Timing, Zeitakkumulatoren | `Template processing` (die Arbeit der Templates), `Template load`, `Load cache`, `Cache load`, `TS init` (das Übersetzungssystem) |
| Reiter Rendering, Content-View-Cache | Treffer oder erzeugt, und warum |
| Reiter Rendering, Cache-Blöcke | jedes `{cache-block}` und `{expcacheblock}` (benannt nach seiner `.stpl`-Datei und `id`), Treffer oder erzeugt, mit seiner Zeit |
| Reiter Rendering, Template-Baum | jedes Template mit seiner Zeit, `.stpl`-Subtemplates unter dem Template, das sie eingebunden hat |

Vergleichen Sie Engines mit demselben Debug-Zustand: Der Debug-Bericht selbst kostet Zeit, eine Seite mit der Leiste
gegen eine Seite ohne sie ist also kein Vergleich.

### curl

Ohne Debug-Ausgabe, aus einer Shell, der Median von zehn Anfragen:

```bash
for i in 1 2 3 4 5 6 7 8 9 10; do
    curl -s -o /dev/null -w '%{time_total}\n' https://www.example.com/smarty/fitness
done | sort -n | sed -n 5,6p
```

Prüfen Sie gleichzeitig `x-exp-cache`: Ein `HIT` misst den Ganzseiten-Cache, nicht die Templates.

### Regeln, die die Zahlen ehrlich hielten

- **Mehrere Anfragen, der Median.** Einzelne Anfragen schwanken stark; zehn Anfragen und der mittlere Wert sind stabil.
- **Identische Bedingungen.** Dieselben Caches warm (der View-Cache, der Block-Cache), derselbe Debug-Zustand, derselbe
  Server, direkt nacheinander.
- **Achten Sie auf die Last des Servers.** Die Ergebnisse schwankten mit der Last um das Drei- bis Fünffache. Während
  eines Laufs lag die Last bei 52 auf 12 Kernen, und jede Zahl dieses Laufs war bedeutungslos; er wurde verworfen und
  später neu gemessen.
- **Prüfen Sie auf Anhäufung in dauerhaften Workern.** Laden Sie dieselbe Seite viele Male mit einer Cookie-Datei neu
  (`curl -b var/tmp/jar -c var/tmp/jar ...` 25-mal) und achten Sie auf einen Trend. Es gab keinen.
- **Wissen Sie, was geantwortet hat.** Ein Treffer im Ganzseiten-Cache, ein Treffer im View-Cache und ein Rendern sind
  drei verschiedene Messungen.

## Einstellungen, auf die es ankommt

| Einstellung | Standard | Wirkung auf die Geschwindigkeit |
|---|---|---|
| `[SmartySettings] CompileCheck` (`smarty.ini`) | `enabled` | `enabled`: Smarty prüft jedes `.stpl` bei jeder Verwendung auf eine Änderung. `disabled` spart diese Prüfungen im Produktivbetrieb; leeren Sie dann die Template-Caches nach jeder Template-Änderung und starten Sie die Worker neu, da die Instanz (mit der Einstellung) gemerkt wird |
| `[SmartySettings] Debugging` | `disabled` | `enabled` fügt jeder Seite Smartys Debug-Konsole hinzu; nie im Produktivbetrieb |
| `[SmartySettings] Security` | `disabled` | `enabled` kompiliert Templates mit der Sicherheitsrichtlinie nach `<cache dir>/smarty/compile-secure`. Die Prüfung erfolgt beim Kompilieren eines Templates, nicht beim Ausführen, ein kompiliertes Template kostet also dasselbe; das Einschalten kompiliert jedes Template einmal neu |
| `[SmartySettings] ParameterOperatorList[]` | leer | Operatoren ohne Template-Hinweise, die nur ihre Parameter lesen; einmal pro Prozess gelesen. Keine Geschwindigkeitseinstellung, aber ein falscher Modus bedeutet falsche Ausgabe |
| `[TemplateSettings] TemplateCache` (`site.ini`) | `enabled` | ohne sie cacht `{expcacheblock}` nichts |
| `[ContentSettings] ViewCaching` | `enabled` | der Content-View-Cache, für beide Engines |
| `[HttpCacheSettings] Enabled`, `CachedSiteAccesses[]` (`httpcache.ini`) | `disabled` | der Ganzseiten-Cache für anonyme Besucher |

## Checkliste für eine schnelle .stpl-Website

1. Liefern Sie sie mit Velocity aus, wenn Sie können; auf PHP-FPM stellen Sie sicher, dass der Opcode-Cache an und warm
   ist.
2. Binden Sie `.stpl` aus `.stpl` mit `{expinclude uri=...}` ohne `name=` ein, damit es als Subtemplate läuft.
3. Halten Sie das Pagelayout in einem `{expcacheblock}` mit Schlüsseln aus dem Knoten, der URI, dem md5 des Inhalts und
   den Rollen, Beschränkungen und dem Anmeldezustand des Besuchers, mit den Teilen pro Besucher als Platzhaltern.
4. Machen Sie eine Bestandsaufnahme der Teile pro Besucher, bevor Sie cachen, und testen Sie angemeldet und abgemeldet.
5. Halten Sie `ezscript_require()`, `ezcss_require()` und `csp_nonce()` aus gecachten Blöcken heraus.
6. Setzen Sie `{expcacheblock}` um teure Fetches in Ansichten, die der View-Cache nicht hält.
7. Prüfen Sie `[TemplateSettings] TemplateCache=enabled` und den View-Cache.
8. Erwägen Sie im Produktivbetrieb `CompileCheck=disabled`, mit einem Leeren des Template-Caches und einem Neustart der
   Worker in jedem Deploy.
9. Messen Sie mit der Debug-Leiste und mit curl, Mediane von zehn, identische Bedingungen, und achten Sie auf
   `x-exp-cache`.

## Fallstricke

| Fallstrick | Was passiert | Stattdessen |
|---|---|---|
| `{expinclude ... name='x'}` für ein `.stpl`, das keinen Namensraum braucht | es geht durch das Template-System: ein Stub und ein vollständiges Rendern | `name=` weglassen |
| Ein Cache-Block ohne die Rollen des Besuchers in seinen Schlüsseln | ein Besucher sieht ein Menü oder eine Liste, die für jemand anderen gemacht wurde | `role_id_list`, `limited_assignment_value_list`, `is_logged_in` in die Schlüssel aufnehmen |
| Inhalt pro Besucher in einem Cache-Block | der Name oder Warenkorb des ersten Besuchers wird allen gezeigt | ein Platzhalter, der nach dem Block gerendert wird |
| `ezscript_require()` in einem Cache-Block | das Skript fehlt bei gecachten Anfragen | es außerhalb des Blocks anfordern |
| `TemplateCache` ausgeschaltet | Blöcke cachen nichts, stillschweigend | einschalten |
| Messen mit eingeschalteter Debug-Leiste bei einer Engine und ausgeschalteter bei der anderen | die 40 bis 60 ms des Debug-Berichts entscheiden den Vergleich | derselbe Debug-Zustand für beide |
| Messen eines Treffers im Ganzseiten-Cache | die Zeit sagt nichts über Templates | `x-exp-cache` prüfen; die Debug-Leiste verwenden |
| Messen auf einem ausgelasteten Server | die Zahlen schwanken um das Drei- bis Fünffache | messen, wenn die Last niedrig ist, und wiederholen |
| `smarty.ini` ändern und erwarten, dass es sofort wirkt | die Instanz und die Auflösungen werden pro Worker gemerkt | deployen (die Worker neu starten) |
| `CompileCheck=disabled` ohne Leeren des Caches im Deploy | alte Templates laufen weiter | die Template-Caches in jedem Deploy leeren |
| Smartys eigenes `count`, `implode`, `explode` bei Änderungen von Hand | falsche Ausgabe, nicht langsamer | die `exp_`-Namen |

## Verwandte Seiten

- [Caching und Leistung](smarty-templates-caching.md): die Cache-Ebenen, `{expcacheblock}`, Schlüssel, Verfall
- [Smarty-Templates (.stpl) neben .tpl](../funktionen/6.0/smarty-templates.md), die Funktionsseite
- [.stpl-Templates schreiben und ausführen](smarty-templates.md#12-velocity-und-dauerhafte-worker): Velocity und dauerhafte Worker
- [Problembehebung und FAQ](smarty-templates-problembehebung.md)
- [Benchmarking](benchmarking.md), [Caches](caches.md), [Der rollenbewusste HTTP-Cache](../bc/6.0/httpcache.md)
- [Velocity und der Opcode-Cache](../funktionen/6.0/velocity-opcode-cache-und-profil.md)
- [Geschwindigkeit der drei Template-Engines](template-engines-geschwindigkeit.md): Smarty, Twig und `.tpl`
  nebeneinander gemessen, gecacht, ungecacht und mit der Debug-Ausgabe

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
