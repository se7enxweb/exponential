# Smarty-Templates: Operator-Referenz

Diese Seite ist für Template-Entwickler, die `.stpl`-Templates schreiben und die genaue Form eines Operators brauchen.
Sie listet jeden Template-Operator, den eine Standardinstallation registriert, nach Bereichen gruppiert, mit seiner
`.stpl`-Form, ob er das Präfix `exp_` braucht, wie er seine Eingabe nimmt, und einem Beispiel. Die Regeln hinter den
Spalten erklärt [Smarty-Templates: Operatoren und Fetch-Funktionen](smarty-templates-operatoren.md); lesen Sie das
zuerst, wenn Ihnen die Wörter „input“ und „param“ unten neu sind.

Die Liste wurde aus dem Code entnommen: aus den Operator-Registrierungen von `lib/eztemplate/classes/eztemplateautoload.php`,
`kernel/common/eztemplateautoload.php`, `kernel/private/eztemplate/eztemplateautoload.php`,
`lib/ezpdf/classes/eztemplateautoload.php`, aus `[PHP] PHPOperatorList[]` von `settings/template.ini` und aus den
`autoloads/eztemplateautoload.php` der Extensions, die mit der Installation ausgeliefert werden. Der Eingabemodus jedes
Operators ist das, was `expSmartyRenderer::operatorInputMode()` aus den Template-Hinweisen des Operators entscheidet,
und für die Operatoren ohne Hinweise das, was `expSmartyRenderer::PARAMETER_OPERATORS` und
`[SmartySettings] ParameterOperatorList[]` von `smarty.ini` sagen.

## Wie die Tabellen zu lesen sind

**Name in `.stpl`.** Der Name, den Sie nach dem Pipe-Zeichen oder vor die Klammern schreiben. `exp_<name>`, wo Smarty 5
einen Modifikator gleichen Namens hat (Smartys eigene Modifikatoren gewinnen immer; `exp_` führt an ihnen vorbei). Ein
Operator, dessen eigener Name mit `exp_` beginnt (`exp_path`, `exp_environment`), wird mit diesem Namen aufgerufen.

**Modus.**

- **input**: Der Operator arbeitet mit seiner Eingabe. `$x|op:a:b` übergibt `$x` als Eingabe und `a`, `b` als
  Parameter; `op($x, a, b)` ist derselbe Aufruf. Ohne Eingabe aufgerufen (ein `.tpl`-`op(a, b)` ohne Pipe-Zeichen),
  schreiben Sie `op(null, a, b)`. Jeder Operator ohne Template-Hinweise ist von dieser Art, darunter fast jeder Operator
  der Extensions, ausgenommen die Operatoren des Kernels, die nur ihre Parameter lesen (unten), und die, die eine
  Extension in `[SmartySettings] ParameterOperatorList[]` von `smarty.ini` aufführt.
- **param**: Der Operator liest nur seine Parameter. Der per Pipe übergebene Wert wird zum ersten Parameter, also sind
  `$a|op:b` und `op($a, b)` derselbe Aufruf, in welcher Reihenfolge Sie ihn auch schreiben möchten. Die Funktionsform
  des `.tpl` ist die `.stpl`-Form: `{ezhttp('SearchText', 'get')}`, `{if has_role('Editor')}`. Bei den Operatoren des
  Kernels ohne Template-Hinweise, die von dieser Art sind (`ezhttp`, `has_role`, `topmenu` ...), wird ein `null` davor,
  wie in `ezhttp(null, 'SearchText', 'get')`, die Form, die konvertierte Templates früher hatten, weggelassen, sodass
  beide Formen funktionieren.

**Beispiel.** Eine `.stpl`-Zeile. Wo die eigenen Tests der Template-Engine (`eZTemplateOperatorMatrixTest`, gegen
`.stpl` geprüft von `tests/tests/lib/smarty/expSmartyOperatorParityTest.php`) den Fall enthalten, steht die Ausgabe
nach `→`.

Zuerst die Literale, da mehrere Operatoren des `.tpl` in Smarty Literale sind:

| `.tpl` | `.stpl` |
|---|---|
| `array(1, 2, 3)` | `[1, 2, 3]` |
| `hash('a', 1, 'b', 2)` | `['a' => 1, 'b' => 2]` |
| `true()`, `false()` | `true`, `false` |
| `null` (ein bloßes Wort, das das `.tpl` als den Text `'null'` liest) | `'null'`; für ein echtes null schreiben Sie `null` |
| ein leerer Parameter: `i18n('ctx',,hash(...))` | `expvoid()`: `i18n:'ctx':expvoid():[...]` |

## Arrays und Listen (eZTemplateArrayOperator)

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `array` | Literal `[...]` | input | `{$list = [1, 2, 3]}` |
| `hash` | Literal `[k => v]` | input | `{$h = ['x' => 'y']}{$h.x}` → `y` |
| `append` | `append` | input | `{[1]\|append:2:3\|exp_implode:','}` → `1,2,3` |
| `prepend` | `prepend` | input | `{[3]\|prepend:1:2\|exp_implode:','}` → `1,2,3` |
| `merge` | `merge` | input | `{(['a' => 1]\|merge:['a' => 2])\|expattr:'a'}` → `2` |
| `array_append`, `array_prepend`, `array_merge` | dieselben | input | veraltete Namen von `append`, `prepend`, `merge` |
| `contains` | `contains` | input | `{if [1, 2]\|contains:3}y{else}n{/if}` → `n`; funktioniert auch mit Zeichenketten |
| `compare` | `compare` | input | `{if [1, 2]\|compare:[2, 1]}y{else}n{/if}` → `y` (Reihenfolge wird ignoriert) |
| `extract` | `extract` | input | `{'abcdef'\|extract:3}` → `def`; `extract:offset:length` |
| `extract_left` | `extract_left` | input | `{[1, 2, 3]\|extract_left:2\|exp_implode:','}` → `1,2` |
| `extract_right` | `extract_right` | input | `{[1, 2, 3]\|extract_right:2\|exp_implode:','}` → `2,3` |
| `begins_with` | `begins_with` | input | `{if [1, 2, 3]\|begins_with:1:2}y{/if}` → `y` |
| `ends_with` | `ends_with` | input | `{if [1, 2, 3]\|ends_with:2:3}y{/if}` → `y` |
| `implode` | `exp_implode` | input | `{$keywords\|exp_implode:', '}` |
| `explode` | `exp_explode` | input | `{'a,,b'\|exp_explode:','\|exp_count}` → `3` |
| `repeat` | `repeat` | input | `{[1, 2]\|repeat:2\|exp_implode:','}` → `1,2,1,2` |
| `reverse` | `reverse` | input | `{(['x', 'y']\|reverse)\|expattr:0}` → `y` |
| `insert` | `insert` | input | `{'ad'\|insert:1:'bc'}` → `abcd` |
| `remove` | `remove` | input | `{'abcd'\|remove:1:2}` → `ad` (Offset, Länge) |
| `replace` | `exp_replace` | input | `{[1, 2, 3, 4]\|exp_replace:1:2:8:9\|exp_implode:','}` → `1,8,9,4` |
| `unique` | `unique` | input | `{['a', 'b', 'a']\|unique\|exp_implode:','}` → `a,b` |
| `array_sum` | `array_sum` | input | `{[0.5, 1.5]\|array_sum}` → `2` |

## Logik und Vergleich (eZTemplateLogicOperator, eZTemplateControlOperator)

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `eq` | `eq` | param | `{if eq(4, 4, 4)}y{/if}` → `y`; `{if $a\|eq:'x'}` |
| `ne` | `ne` | param | `{if $block.values.tag\|ne:''}` |
| `lt`, `gt`, `le`, `ge` | dieselben | param | `{if 2\|lt:3}y{/if}` → `y`; `{if $count\|gt:0}` |
| `and` | `and` | param | `{and(1, 'a', 'last')}` → `last` (der letzte Wert, wenn alle wahr sind) |
| `or` | `or` | param | `{or(0, '', 'x', 'y')}` → `x` (der erste wahre Wert) |
| `not` | `not` | param | `{if $x\|not}`, `{if not($list)}` |
| `true`, `false` | Literale `true`, `false` | param | `{$flag = true}` |
| `null` | `null` (Operator) | input | `{if 'x'\|null}y{else}n{/if}` → `n` |
| `choose` | `choose` | input | `{0\|choose:'a':'b'}` → `a`; `{2\|choose:'a':'b':'c'}` → `c` |
| `cond` | `cond` | param | `{cond($edit_language, 'yes', 'no')}`; schreiben Sie ihn als Funktion (siehe den Hinweis unten) |
| `first_set` | `first_set` | param | `{first_set($redirect_uri, $redirect_url, '/')}`; `{first_set(0, 'b')}` → `0` |

`cond` und `first_set` lesen nur ihre Parameter. Per Pipe aufgerufen, wird der übergebene Wert zum ersten Parameter:
`$a|first_set:'d'` ist „der erste von `$a`, `'d'`, der gesetzt ist“, und `$a|cond:'x':'y'` verwendet `$a` als Bedingung.
Ein `.tpl`-`$a|first_set('d')` ignoriert `$a` und ist immer `'d'`; der Konverter schreibt es als `first_set('d')`, um
das beizubehalten. Smartys eigene `==`, `!=`, `&&`, `||`, `!` und `?:` funktionieren auch, aber `eq` vergleicht so wie
das `.tpl` (`{if eq(1, '1')}` ist wahr; mehrere Parameter müssen alle gleich sein).

## Typen (eZTemplateTypeOperator)

Alle lesen ihre Parameter: `is_set($x)` und `$x|is_set` sind dasselbe.

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `is_array` | `exp_is_array` | param | `{if exp_is_array($module_result.content_info)}` |
| `is_boolean`, `is_integer`, `is_float`, `is_numeric`, `is_string`, `is_object`, `is_null` | dieselben | param | `{if is_float(1.5)}y{/if}` → `y` |
| `is_class` | `is_class` | param | Parameter wie im `.tpl` |
| `is_set`, `is_unset` | dieselben | param | `{if is_set($block.view_type)}`; Smartys `isset()` funktioniert auch |
| `get_type` | `get_type` | param | `{get_type(5)}` → `integer`; `{get_type(true)}` → `boolean[true]` |
| `get_class` | `get_class` | param | `{get_class($node)}` |

## Arithmetik (eZTemplateArithmeticOperator)

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `sum`, `sub`, `mul`, `div`, `mod` | dieselben | param | `{1\|sum:2:3}` → `6`; `{div(100, 5, 2)}` → `10`; `{sub(mul(3, 4), div(10, 5))}` → `10` |
| `inc`, `dec` | dieselben | param | `{inc(9)}` → `10` |
| `max`, `min` | dieselben | param | `{max(1.5, 2.5, 2)}` → `2.5` |
| `abs`, `ceil`, `floor`, `int`, `float` | dieselben | param | `{-2.5\|abs}` → `2.5`; `{floor(-1.5)}` → `-2` |
| `round` | `exp_round` | param | `{2.6\|exp_round}` → `3` |
| `roman` | `roman` | param | `{4\|roman}` → `IV` |
| `rand` | `rand` | param | `{rand(1, 6)}` |
| `count` | `exp_count` | input | `{$list\|exp_count}`; `{exp_count(null, 'abc')}` → `3` (eine Zeichenkette zählt ihre Zeichen) |

Smartys `+`, `-`, `*`, `/` und `%` eignen sich für Zahlen ebenfalls.

## Zeichenketten (eZTemplateStringOperator, eZTemplateTextOperator, eZTemplateNl2BrOperator)

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `wash` | `wash` | input | `{$node.name\|wash}`; `wash:'email'`, `wash:'javascript'`, `wash:'pdf'`; siehe [Sicherheit und Escaping](smarty-templates-sicherheit.md) |
| `upcase`, `downcase` | dieselben | input | `{'äöü'\|upcase}` → `ÄÖÜ` |
| `upfirst`, `upword` | dieselben | input | `{$title\|upfirst}` |
| `count_words` | `exp_count_words` | input | `{''\|exp_count_words}` → `0` |
| `count_chars` | `count_chars` | input | `{'äöü'\|count_chars}` → `3` |
| `trim` | `trim` | input | `{'xxaxx'\|trim:'x'}` → `a` |
| `simplify` | `simplify` | input | `{'a--b---c'\|simplify:'-'}` → `a-b-c` |
| `shorten` | `shorten` | input | `{'hello world'\|shorten:7:'..'}` → `hello..`; `shorten:7:'...':'middle'` |
| `pad` | `pad` | input | `{'ab'\|pad:4}` → `ab  ` |
| `wrap` | `wrap` | input | `{$text\|wrap:72}` |
| `break` | `break` | input | `{$text\|break}` (Zeilenumbrüche als `<br />`) |
| `chr`, `ord` | dieselben | input | `{[97]\|chr}` → `a`; `{'AB'\|ord\|exp_implode:','}` → `65,66` |
| `concat` | `concat` | param | `{concat(1, 2, 3)}` → `123`; `{'a'\|concat:'b'}` → `ab` |
| `indent` | `exp_indent` | input | `{'x'\|exp_indent:3:'custom':'-'}` → `---x` |
| `nl2br` | `exp_nl2br` | input | `{$text\|wash\|exp_nl2br}` |

`indent` und `nl2br` geben als Hinweis an, dass ihre Eingabe ein Parameter ist, lesen sie aber zur Laufzeit als ihre
Eingabe; die Brücke führt sie in `expSmartyRenderer::INPUT_AT_RUN_TIME` auf.

## PHP-Zeichenkettenfunktionen (eZTemplateStringsOperator)

Einundsechzig PHP-Zeichenkettenfunktionen sind Operatoren
([PHP-Zeichenkettenfunktionen als Template-Operatoren](../funktionen/6.0/template-operatoren-fuer-zeichenketten.md)).
Alle sind **input**-Operatoren: Der per Pipe übergebene Wert ist das Subjekt der PHP-Funktion.

| Name in `.stpl` | Operatoren |
|---|---|
| mit `exp_` | `exp_strip_tags`, `exp_strlen`, `exp_substr`, `exp_number_format` |
| so, wie sie sind | `addslashes`, `addcslashes`, `bin2hex`, `hex2bin`, `chunk_split`, `convert_uudecode`, `convert_uuencode`, `hebrev`, `html_entity_decode`, `htmlentities`, `htmlspecialchars_decode`, `lcfirst`, `levenshtein`, `ltrim`, `rtrim`, `metaphone`, `quoted_printable_decode`, `quoted_printable_encode`, `quotemeta`, `similar_text`, `soundex`, `sprintf`, `vsprintf`, `str_contains`, `str_ends_with`, `str_starts_with`, `str_getcsv`, `str_shuffle`, `str_split`, `str_word_count`, `strcasecmp`, `strcmp`, `strcoll`, `strcspn`, `stripcslashes`, `stripslashes`, `stripos`, `stristr`, `strnatcasecmp`, `strnatcmp`, `strncasecmp`, `strncmp`, `strpbrk`, `strpos`, `strrchr`, `strripos`, `strrpos`, `strspn`, `strstr`, `strtok`, `strtr`, `substr_compare`, `substr_count`, `substr_replace`, `ristring`, `rstring` |

```smarty
{$text|exp_substr:0:5}
{if $text|str_contains:'Expo'}...{/if}
{'%s has %d items'|sprintf:$name:$count}
{1234567.891|exp_number_format:2:'.':','}
{$node.data_map.success_text.data_text|exp_strip_tags}
{(first_set($form.parent.parent.vars, $form.parent.vars))|expattr:'name'|ristring:'_':'-'}
```

Smartys eigene `substr`, `strlen`, `strip_tags` und `number_format` sind die PHP-Funktionen mit derselben Reihenfolge
der Parameter, also drucken die Namen ohne Präfix meist dasselbe; die Namen mit Präfix sind das, was der Konverter
schreibt, und das, was garantiert so arbeitet wie im `.tpl`.

## Prüfsummen und Einheiten (eZTemplateDigestOperator, eZTemplateUnitOperator)

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `md5`, `crc32`, `rot13` | dieselben | input | `{$module_result.content\|md5}`; `{'a1z'\|rot13}` → `n1m` |
| `si` | `si` | input | `{1048576\|si:'byte'}` → `1.00 MB` |

## Datum und Locale (eZTemplateLocaleOperator)

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `l10n` | `l10n` | input | `{$node.object.published\|l10n:'shortdate'}`; Typen `time`, `shorttime`, `date`, `shortdate`, `datetime`, `shortdatetime`, `currency`, `clean_currency`, `number` |
| `locale` | `locale` | input | `{(((ezini('RegionalSettings', 'Locale'))\|locale)\|expattr:'http_locale_code')\|wash}` |
| `datetime` | `datetime` | input | `{$time\|datetime:'custom':'%d.%m.%Y'}` |
| `currentdate` | `currentdate` | param | `{currentdate()}` |
| `maketime`, `makedate` | dieselben | input | ohne Eingabe: `maketime(null, hour, minute, second, month, day, year)`, `makedate(null, month, day, year)` |
| `gettime` | `gettime` | input | `{($time\|gettime)\|expattr:'year'}` |

## Abrufen und Attribute (eZTemplateExecuteOperator, eZTemplateAttributeOperator)

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `fetch` | `fetch` | param | `{$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 10])}` |
| `fetch_alias` | `fetch_alias` | param | `{$children = fetch_alias('children', ['parent_node_id' => $node.node_id])}` |
| `attribute` | `attribute` | input | `{$node\|attribute:'show'}`: eine Tabelle der Attribute des Objekts, zur Fehlersuche |
| `dump` | `dump` | input | `{$node\|dump}` |

Mehr zu `fetch()`: [Fetch-Funktionen](smarty-templates-operatoren.md#fetch-funktionen).

## Bilder (eZTemplateImageOperator)

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `texttoimage` | `texttoimage` | input | Parameter wie im `.tpl` |
| `image`, `imagefile` | dieselben | param | Parameter wie im `.tpl` |

Eine Extension, die einen Operator gleichen Namens registriert, ersetzt den des Kernels: In einem Siteaccess mit
`sevenx_themes_media` ist `image` der Operator des Themes (ein **input**-Operator, geschrieben `image(null, ...)`).

## Rollen und Richtlinien (eZTemplateRoleOperator)

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `has_role`, `has_role_id` | dieselben | param | `{if has_role('Editor')}`; `{if has_role_id(5, $user_id)}` |
| `has_any_role` | `has_any_role` | param | `{if has_any_role(['Administrator', 'Publisher'])}` |
| `has_role_by_user`, `has_role_by_user_id` | dieselben | param | `{if has_role_by_user($user, 5)}`, Parameter wie im `.tpl` |
| `has_policy`, `has_policy_by_user` | dieselben | param | `{if has_policy('content', 'edit')}` |

Sie lesen nur ihre Parameter (der aktuelle Benutzer ist die Voreinstellung): Schreiben Sie sie wie im `.tpl`. Ein per
Pipe übergebener Wert wird zum ersten Parameter, also fragt `$current_user|has_role:'Editor'` nach einer Rolle, die nach
dem Benutzer benannt ist; schreiben Sie `has_role('Editor')`. Einzelheiten:
[Rollen- und Richtlinienprüfungen in Templates](../funktionen/6.0/template-operatoren-fuer-rollen-und-richtlinien.md).

## Extensions, Installation und Umgebung (lib und kernel)

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `expinfo` | `expinfo` | param | `{if expinfo('explayouts_ui')}...{/if}` |
| `installation_name`, `is_production_system` | dieselben | param | `{installation_name()}`; `{installation_name(true)}` (leer auf einem Produktivsystem) |
| `exp_environment`, `exp_environment_badge` | dieselben (eigene `exp_`-Namen) | param | `{exp_environment()}` |
| `exp_demo_mode` | `exp_demo_mode` | param | `{if exp_demo_mode()}...{/if}` |
| `audit_label` | `audit_label` | input | Parameter wie im `.tpl` |
| `csp_nonce` | `csp_nonce` | param | `<script nonce="{csp_nonce()}">...</script>`; nie innerhalb von `{expcacheblock}` ([Sicherheit und Escaping](smarty-templates-sicherheit.md)) |
| `view_parameter` | `view_parameter` | input | `{$value\|view_parameter}`: ein Wert, kodiert für `/(name)/<value>` |
| `transfer_tools`, `transfer_actions`, `transfer_related`, `transfer_tool` | dieselben | param | Parameter wie im `.tpl` |
| `ezpackage` | `ezpackage` | input | Parameter wie im `.tpl` |
| `ezmodule` | `ezmodule` | param | `{if ezmodule('content/edit')}` |
| `module_params` | `module_params` | param | `{module_params()}` |
| `ezpreference` | `ezpreference` | param | `{ezpreference('admin_left_size')}` |

## URLs, Einstellungen und die Anfrage (eZURLOperator, eZi18nOperator)

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `ezurl` | `ezurl` | input | `<a href={$node.url_alias\|ezurl}>`; `{'/content/view'\|ezurl:'no'}`; `{'/content/view'\|ezurl:'no':'full'}` |
| `ezroot` | `ezroot` | input | `<img src={$image.url\|ezroot} />` |
| `ezdesign` | `ezdesign` | input | `<link rel="stylesheet" href={'stylesheets/site.css'\|ezdesign} />` |
| `ezimage` | `ezimage` | input | `<img src={'logo.png'\|ezimage} />` |
| `exturl` | `exturl` | input | Parameter wie im `.tpl` |
| `ezsys` | `ezsys` | input | `{'indexfile'\|ezsys}` |
| `ezini` | `ezini` | param | `{ezini('SiteSettings', 'SiteName')}`; `{ezini('SiteInfo', 'CookiePolicyID', 'menu.ini')}` |
| `ezini_hasvariable` | `ezini_hasvariable` | param | `{if ezini_hasvariable('SiteInfo', 'CookiePolicyID', 'menu.ini')}` |
| `ezhttp` | `ezhttp` | param | `{ezhttp('SearchText', 'get')}` (siehe den Hinweis) |
| `ezhttp_hasvariable` | `ezhttp_hasvariable` | param | `{if ezhttp_hasvariable('SearchText', 'get')}` |
| `i18n` | `i18n` | input | `{'Read more'\|i18n:'design/standard/node'}`; `{'Order %id'\|i18n:'design/shop':expvoid():(['%id' => $order.order_nr])}` |
| `d18n` | `d18n` | input | wie `i18n`, für einen dynamischen Text |
| `x18n` | `x18n` | input | `{'Text'\|x18n:'myext':'design/myext'}` (Extension, Kontext, Kommentar, Argumente) |

`ezhttp` und `ezhttp_hasvariable` nehmen den Namen und den Typ (`post`, `get`, `session`, `cookie`) als ihre
**Parameter**. Sie haben keine Template-Hinweise; die Brücke kennt sie als Operatoren, die nur ihre Parameter lesen
(`expSmartyRenderer::PARAMETER_OPERATORS`), also funktioniert die Funktionsform des `.tpl`:
`{ezhttp('SearchText', 'get')}`. `'SearchText'|ezhttp:'get'` ist derselbe Aufruf, und `ezhttp(null, 'SearchText', 'get')`,
das konvertierte Templates früher schrieben, funktioniert weiterhin. Escapen Sie, was er zurückgibt:
`{ezhttp('SearchText', 'get')|wash}`.

`ezurl`, `ezroot`, `ezdesign` und `ezimage` drucken die Adresse standardmäßig in doppelten Anführungszeichen
(`href={...|ezurl}`); `:'no'` lässt die Anführungszeichen weg, `:'single'` verwendet einfache. Ist `ezurlmultisite`
aktiv, übernehmen dessen eigene Operatoren diese zehn Namen mit denselben Formen.

## Inhalte, Menüs und Text (kernel/common)

Keiner davon hat Template-Hinweise. Die **input**-Operatoren arbeiten mit dem per Pipe übergebenen Wert; als Funktion
ohne Eingabe schreiben Sie zuerst `null`, mit den Parametern des `.tpl`. Die **param**-Operatoren lesen nur ihre
Parameter (`expSmartyRenderer::PARAMETER_OPERATORS`): Schreiben Sie sie wie im `.tpl`.

| Operator | Name in `.stpl` | Modus | Beispiel |
|---|---|---|---|
| `autolink` | `autolink` | input | `{$text\|wash\|autolink}` |
| `simpletags` | `simpletags` | input | `{$text\|simpletags}` |
| `wordtoimage` | `wordtoimage` | input | `{$text\|wordtoimage}` |
| `mimetype_icon`, `class_icon`, `classgroup_icon`, `action_icon`, `icon`, `flag_icon` | dieselben | input | `{$node.class_identifier\|class_icon:'small'}`; `{$language.locale\|flag_icon}` |
| `icon_info` | `icon_info` | param | `{icon_info('mimetype')}` |
| `alphabet` | `alphabet` | param | `{alphabet()}` |
| `month_overview` | `month_overview` | input | `{$events\|month_overview:'published':$date}` |
| `treemenu` | `treemenu` | param | `treemenu($module_result.path, $node_id, ['folder'], 0, 5)`, Parameter wie im `.tpl` |
| `content_structure_tree` | `content_structure_tree` | param | `content_structure_tree(2, ['folder'], 2)`, Parameter wie im `.tpl` |
| `topmenu` | `topmenu` | param | `{topmenu('content')}` |
| `eztoc` | `eztoc` | param | `{eztoc($node.data_map.body)}` |
| `siteaccess_url` | `siteaccess_url` | param | Parameter wie im `.tpl` |
| `siteaccess_menu`, `siteaccess_menu_key` | dieselben | param | Parameter wie im `.tpl` |
| `language_switcher` | `language_switcher` | param | `{language_switcher($uri)}` |
| `pdf` (lib/ezpdf) | `pdf` | param | `{pdf('new_line')}`, Parameter wie im `.tpl` |

`siteaccess_url`, `siteaccess_menu` und `siteaccess_menu_key` geben an, dass sie keine Eingabe nehmen
(`'input' => false`), also sind sie **param**-Operatoren: `siteaccess_url(a, b)` und `a|siteaccess_url:b` sind dasselbe.

## PHP-Funktionen aus template.ini

`[PHP] PHPOperatorList[]` von `settings/template.ini` macht PHP-Funktionen zu Operatoren: `urlencode`, `urldecode`,
`rawurlencode` und `rawurldecode` in einer Standardinstallation. Sie sind **param**-Operatoren mit einem Parameter:

```smarty
<a href="/search?q={$text|rawurlencode}">...</a>
```

## Operatoren der ausgelieferten Extensions

Operatoren, die die `autoloads/eztemplateautoload.php` einer Extension registriert, gibt es nur in einem Siteaccess,
in dem die Extension aktiv ist. Von den Extensions, die mit der Installation ausgeliefert werden, gibt nur
`ezurlmultisite` seinen Operatoren Template-Hinweise (dieselben wie die URL-Operatoren des Kernels); **jeder andere
Extension-Operator ist ein input-Operator**: `$x|op:a` übergibt `$x` als seine Eingabe, und ein `.tpl`-Aufruf ohne
Pipe-Zeichen wird in einem `.stpl` als `op(null, ...)` geschrieben. Einen Operator ohne Hinweise, der nur seine
Parameter liest, macht eine Extension mit `[SmartySettings] ParameterOperatorList[]` in ihrer
`smarty.ini.append.php` zu einem **param**-Operator (siehe
[Operatoren und Funktionen schreiben](smarty-templates-plugins.md#operatoren-ohne-hinweise-die-ihre-parameter-lesen)).

| Extension | Operatoren |
|---|---|
| `ezjscore` | `ezscript`, `ezscript_require`, `ezscript_load`, `ezscriptfiles`, `ezcss`, `ezcss_require`, `ezcss_load`, `ezcssfiles`, `json_encode` (schreiben Sie `exp_json_encode`: Smarty hat `json_encode`), `xml_encode`, `node_encode`, `has_access_to_limitation` |
| `eztags` | `eztags_parent_string`, `latest_tags`, `user_limitations`, `tag_icon`, `eztagscloud` |
| `explayouts` | `expl_parse`, `expl_strip`, `expl_has`, `expl_first`, `explayouts_safe_url`, `explayouts_safe_embed_url`, `explayouts_safe_color`, `explayouts_safe_tag`, `explayouts_safe_template_name`, `explayouts_safe_css_class` |
| `explayouts_ui` | `explayouts_ui_has_template` |
| `ezoe` | `ezoe_ini_section` |
| `ezfind` | `solr_escape`, `solr_quotes_escape` |
| `ezwebin`, `ezdemo` | `ezkeywordlist`, `ezarchive`, `eztagcloud`, `ezpagedata`, `ezpagedata_set`, `ezpagedata_append` |
| `ezurlmultisite` | die zehn URL-Operatoren oben sowie `ezurl_path`, `ezurl_siteaccess`, `ezurl_roots` |
| `ezstarrating` | `fetch_starrating_data`, `fetch_starrating_stats`, `fetch_by_starrating` |
| `xrowmetadata` | `metadata`, `xrowmetadata_license`, `xrowmetadata_license_enabled`, `xrowmetadata_license_choices`, `xrowmetadata_license_info`, `xrowmetadata_license_inherited`, `xrowmetadata_license_class_settings` |
| `expai` | `expai_money`, `expai_tokens`, `expai_number`, `expai_json`, `expai_retention`, `expai_help_url` |
| `expui` | `exp_config` (eigener `exp_`-Name) |
| `cjw_newsletter` | `cjw_newsletter_preg_replace`, `cjw_newsletter_str_replace`, `cjw_newsletter_variable`, `cjwnl_condition_open`, `cjwnl_condition_else`, `cjwnl_condition_close`, `cjwnl_plaintext`, `cjwnl_richtext`, `cjwnl_interests_block`, `cjwnl_abs_url`, `cjwnl_text_underline`, `cjwnl_text_wrap`, `cjwnl_rendering_data` |
| `ezflow` | `unserialize`, `json`, `feedreader`, `red5list`, `pagelink` |
| `ezwt` | `ezcreateclasslistgroups` |
| `enhancedezbinaryfile` | `parsexml` |
| `bcwebsitestatistics` | `bc_ga_urchin`, `bc_ga_urchinOrder`, `bc_ga_xmlAttributeValue`, `bc_ga_jsEscapedString`, `bc_ga_formatNumericDecimal`, `bc_ga_script_url` |
| `str_replace` | `ezstr_replace` |
| `owsimpleoperator` | `example_sum` |
| `sevenx_themes_media` | `absolute_url`, `app`, `asset`, `component_content`, `content_link`, `content_tags`, `controller`, `embed_image`, `enhanced_link`, `exp_can_login_to_siteaccess`, `exp_path`, `exp_url`, `explayouts_render_result`, `explayouts_render_zone`, `exponential`, `fieldRelation`, `fieldRelations`, `fieldValue`, `filterChildren`, `filterFieldRelationLocations`, `filterFieldRelations`, `firstNonEmptyField`, `getParameter`, `get_netgen_open_graph`, `hasField`, `hasParameter`, `haveToPaginate`, `image`, `image_link`, `intro`, `item_content_link`, `item_image_link`, `item_params`, `item_view_template`, `json_ld`, `layout_title`, `ng_image_alias`, `ng_query`, `ng_render_field`, `ng_view_content`, `ngsite`, `ngsite_group_fields`, `ngsite_language_name`, `ngsite_topic_path`, `pagerfanta`, `parameter`, `parent`, `path`, `player`, `player_slide`, `poster`, `poster_slide`, `recipe_schema`, `redirect_to_site_root`, `render`, `render_esi`, `saveXML`, `site_url`, `siteaccess_href`, `tag_url`, `title`, `tpl_block_template`, `trans`, `video_thumbnail`, `youtube_id` und die veralteten Aliase `ibexa`, `ibexa_path`, `ibexa_url`, `nglayouts_render_result`, `nglayouts_render_zone` |

Echte Zeilen aus der Media-Portierung, alles input-Operatoren, ohne Eingabe aufgerufen:

```smarty
<h3 class="title"><a href="{exp_path(null, $location)}">{ng_render_field(null, $content.fields.title)}</a></h3>
<article {item_params(null, $toolbar_macros, $content, $location)} class="view-type view-type-{$view_type}">
{$__loop1 = fieldRelations(null, $content, 'authors')}
```

Ein Operatorname, der mit `exp_` beginnt und registriert ist (`exp_path`, `exp_url`, `exp_config`,
`exp_can_login_to_siteaccess`, `exp_environment` ...), ist dieser Operator. Nur bei einem Namen, der nicht registriert
ist, wird das Präfix entfernt: `exp_count` ist `count`, `exp_implode` ist `implode`. Daher kann `exp_path` nie für einen
Operator namens `path` stehen; für diesen schreiben Sie `path`.

## Helfer der Brücke (keine Operatoren)

`expattr`, `expvar`, `expvoid`, `expslice`, `expsectionloop`, `exprange`, `expsequence`, `expcolumn` sind Modifikatoren
der Brücke selbst; siehe [Die Helfer der Brücke](smarty-templates-operatoren.md#die-helfer-der-brücke).

## Die Operatoren Ihrer Installation finden

Die Tabellen decken ab, was mit der Installation ausgeliefert wird. Ihre eigenen Extensions fügen Operatoren auf
dieselbe Weise hinzu: Suchen Sie nach `operator_names` in `extension/*/autoloads/eztemplateautoload.php` der im
Siteaccess aktiven Extensions. Um den Modus eines Operators herauszufinden, suchen Sie in seiner Klasse nach
`operatorTemplateHints()`: keine, oder `'input-as-parameter' => 'always'`, bedeutet **input**;
`'input-as-parameter' => true` oder `'input' => false` bedeutet **param**. Einen Operator schreiben, der in beiden
Engines funktioniert: [Operatoren und Funktionen für .stpl schreiben](smarty-templates-plugins.md).

## Verwandte Seiten

- [Smarty-Templates: Operatoren und Fetch-Funktionen](smarty-templates-operatoren.md): die Regeln hinter dieser Seite
- [Template-Funktionen und Blöcke](smarty-templates-funktionen.md)
- [Problembehebung und FAQ](smarty-templates-problembehebung.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
