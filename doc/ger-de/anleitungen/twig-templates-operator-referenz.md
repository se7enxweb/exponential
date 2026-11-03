# Twig-Templates: Operator-Referenz

Diese Seite ist für Template-Entwickler, die `.twig`-Templates schreiben und die genaue Form eines Operators brauchen.
Sie listet die Template-Operatoren des Kernels, der Template-Bibliothek und der Extensions der Standarddistribution, mit
der Filterform (mit Eingabe) und der Funktionsform (ohne Eingabe), die ein `.twig` benutzt, und wie der Operator seine
Eingabe nimmt. Die Regeln hinter den Spalten stehen in [Operatoren und Fetch-Funktionen](twig-templates-operatoren.md).

Die Tabellen wurden aus den Operatoren geschrieben, die das Template-System in einer Entwicklungsinstallation registriert
(`eZTemplate::factory()->Operators`); eine Extension, die Sie hinzufügen, bringt ihre Operatoren auf demselben Weg mit,
und jeder davon ist ein Filter und eine Funktion gleichen Namens.

## Wie Sie die Tabellen lesen

- **Filterform**: `x|name(...)`, der Operator mit einer Eingabe, wie `{$x|name( ... )}` in einem `.tpl`.
- **Funktionsform**: `name(...)`, der Operator ohne Eingabe, wie `{name( ... )}` in einem `.tpl`.
- **exp_-Formen**: Wo Twig einen Filter oder eine Funktion gleichen Namens hat oder der Name ein Wort von Twigs Parser
  ist (`and`, `or`, `not`, `true`, `false`, `null`), heißt der Operator `exp_<name>`. `exp_<name>` funktioniert für
  jeden Operator.
- **Eingabe**: Eingabe: Der Operator arbeitet auf seiner Eingabe (die Filterform gibt sie ihm); Parameter: Die Eingabe
  wird der erste Parameter, sodass `x|op(y)` und `op(x, y)` derselbe Aufruf sind; ignoriert Eingabe: ein
  Steueroperator oder einer, der nur seine Parameter liest.

## Kernel (kernel/)

| Operator | Filterform | Funktionsform | Eingabe | Klassendatei |
|---|---|---|---|---|
| `action_icon` | `x\|action_icon` | `action_icon()` | Eingabe | `ezwordtoimageoperator.php` |
| `alphabet` | `x\|alphabet` | `alphabet()` | ignoriert Eingabe | `ezalphabetoperator.php` |
| `audit_label` | `x\|audit_label` | `audit_label()` | Eingabe | `expaudittemplateoperator.php` |
| `autolink` | `x\|autolink` | `autolink()` | Eingabe | `ezautolinkoperator.php` |
| `class_icon` | `x\|class_icon` | `class_icon()` | Eingabe | `ezwordtoimageoperator.php` |
| `classgroup_icon` | `x\|classgroup_icon` | `classgroup_icon()` | Eingabe | `ezwordtoimageoperator.php` |
| `content_structure_tree` | `x\|content_structure_tree` | `content_structure_tree()` | ignoriert Eingabe | `ezcontentstructuretreeoperator.php` |
| `csp_nonce` | `x\|csp_nonce` | `csp_nonce()` | ignoriert Eingabe | `expcontentsecuritypolicyoperator.php` |
| `d18n` | `x\|d18n` | `d18n()` | Eingabe | `ezi18noperator.php` |
| `exp_demo_mode` | `x\|exp_demo_mode` | `exp_demo_mode()` | ignoriert Eingabe | `expdemomodeoperator.php` |
| `exp_environment` | `x\|exp_environment` | `exp_environment()` | ignoriert Eingabe | `expenvironmentoperator.php` |
| `exp_environment_badge` | `x\|exp_environment_badge` | `exp_environment_badge()` | ignoriert Eingabe | `expenvironmentoperator.php` |
| `ezmodule` | `x\|ezmodule` | `ezmodule()` | ignoriert Eingabe | `ezmoduleoperator.php` |
| `ezpackage` | `x\|ezpackage` | `ezpackage()` | Eingabe | `ezpackageoperator.php` |
| `ezpreference` | `x\|ezpreference` | `ezpreference()` | Parameter | `ezkerneloperator.php` |
| `eztoc` | `x\|eztoc` | `eztoc()` | ignoriert Eingabe | `eztocoperator.php` |
| `flag_icon` | `x\|flag_icon` | `flag_icon()` | Eingabe | `ezwordtoimageoperator.php` |
| `i18n` | `x\|i18n` | `i18n()` | Eingabe | `ezi18noperator.php` |
| `icon` | `x\|icon` | `icon()` | Eingabe | `ezwordtoimageoperator.php` |
| `icon_info` | `x\|icon_info` | `icon_info()` | ignoriert Eingabe | `ezwordtoimageoperator.php` |
| `installation_name` | `x\|installation_name` | `installation_name()` | ignoriert Eingabe | `expinstallationoperator.php` |
| `is_production_system` | `x\|is_production_system` | `is_production_system()` | ignoriert Eingabe | `expinstallationoperator.php` |
| `language_switcher` | `x\|language_switcher` | `language_switcher()` | ignoriert Eingabe | `ezplanguageswitcheroperator.php` |
| `mimetype_icon` | `x\|mimetype_icon` | `mimetype_icon()` | Eingabe | `ezwordtoimageoperator.php` |
| `module_params` | `x\|module_params` | `module_params()` | ignoriert Eingabe | `ezmoduleparamsoperator.php` |
| `month_overview` | `x\|month_overview` | `month_overview()` | Eingabe | `ezdateoperatorcollection.php` |
| `simpletags` | `x\|simpletags` | `simpletags()` | Eingabe | `ezsimpletagsoperator.php` |
| `siteaccess_menu` | `x\|siteaccess_menu` | `siteaccess_menu()` | Parameter | `ezpsiteaccessmenuoperator.php` |
| `siteaccess_menu_key` | `x\|siteaccess_menu_key` | `siteaccess_menu_key()` | Parameter | `ezpsiteaccessmenuoperator.php` |
| `siteaccess_url` | `x\|siteaccess_url` | `siteaccess_url()` | Parameter | `ezpsiteaccessurloperator.php` |
| `topmenu` | `x\|topmenu` | `topmenu()` | ignoriert Eingabe | `eztopmenuoperator.php` |
| `transfer_actions` | `x\|transfer_actions` | `transfer_actions()` | ignoriert Eingabe | `expkerneltransferoperator.php` |
| `transfer_related` | `x\|transfer_related` | `transfer_related()` | ignoriert Eingabe | `expkerneltransferoperator.php` |
| `transfer_tool` | `x\|transfer_tool` | `transfer_tool()` | ignoriert Eingabe | `expkerneltransferoperator.php` |
| `transfer_tools` | `x\|transfer_tools` | `transfer_tools()` | ignoriert Eingabe | `expkerneltransferoperator.php` |
| `treemenu` | `x\|treemenu` | `treemenu()` | ignoriert Eingabe | `eztreemenuoperator.php` |
| `view_parameter` | `x\|view_parameter` | `view_parameter()` | Eingabe | `expviewparameteroperator.php` |
| `wordtoimage` | `x\|wordtoimage` | `wordtoimage()` | Eingabe | `ezwordtoimageoperator.php` |
| `x18n` | `x\|x18n` | `x18n()` | Eingabe | `ezi18noperator.php` |

## Template-Bibliothek (lib/)

| Operator | Filterform | Funktionsform | Eingabe | Klassendatei |
|---|---|---|---|---|
| `abs` | `x\|exp_abs` | `abs()` | Parameter | `eztemplatearithmeticoperator.php` |
| `addcslashes` | `x\|addcslashes` | `addcslashes()` | Eingabe | `eztemplatestringsoperator.php` |
| `addslashes` | `x\|addslashes` | `addslashes()` | Eingabe | `eztemplatestringsoperator.php` |
| `and` | `x\|exp_and` | `exp_and()` | Parameter | `eztemplatelogicoperator.php` |
| `append` | `x\|append` | `append()` | Eingabe | `eztemplatearrayoperator.php` |
| `array` | `x\|array` | `[]` | Eingabe | `eztemplatearrayoperator.php` |
| `array_append` | `x\|array_append` | `array_append()` | Eingabe | `eztemplatearrayoperator.php` |
| `array_merge` | `x\|array_merge` | `array_merge()` | Eingabe | `eztemplatearrayoperator.php` |
| `array_prepend` | `x\|array_prepend` | `array_prepend()` | Eingabe | `eztemplatearrayoperator.php` |
| `array_sum` | `x\|array_sum` | `array_sum()` | Eingabe | `eztemplatearrayoperator.php` |
| `attribute` | `x\|attribute` | `exp_attribute()` | Eingabe | `eztemplateattributeoperator.php` |
| `begins_with` | `x\|begins_with` | `begins_with()` | Eingabe | `eztemplatearrayoperator.php` |
| `bin2hex` | `x\|bin2hex` | `bin2hex()` | Eingabe | `eztemplatestringsoperator.php` |
| `break` | `x\|break` | `break()` | Eingabe | `eztemplatestringoperator.php` |
| `ceil` | `x\|ceil` | `ceil()` | Parameter | `eztemplatearithmeticoperator.php` |
| `choose` | `x\|choose` | `choose()` | Eingabe | `eztemplatelogicoperator.php` |
| `chr` | `x\|chr` | `chr()` | Eingabe | `eztemplatestringoperator.php` |
| `chunk_split` | `x\|chunk_split` | `chunk_split()` | Eingabe | `eztemplatestringsoperator.php` |
| `compare` | `x\|compare` | `compare()` | Eingabe | `eztemplatearrayoperator.php` |
| `concat` | `x\|concat` | `concat()` | Parameter | `eztemplatetextoperator.php` |
| `cond` | `x\|cond` | `cond()` | ignoriert Eingabe | `eztemplatecontroloperator.php` |
| `contains` | `x\|contains` | `contains()` | Eingabe | `eztemplatearrayoperator.php` |
| `convert_uudecode` | `x\|convert_uudecode` | `convert_uudecode()` | Eingabe | `eztemplatestringsoperator.php` |
| `convert_uuencode` | `x\|convert_uuencode` | `convert_uuencode()` | Eingabe | `eztemplatestringsoperator.php` |
| `count` | `x\|count` | `count()` | Eingabe | `eztemplatearithmeticoperator.php` |
| `count_chars` | `x\|count_chars` | `count_chars()` | Eingabe | `eztemplatestringoperator.php` |
| `count_words` | `x\|count_words` | `count_words()` | Eingabe | `eztemplatestringoperator.php` |
| `crc32` | `x\|crc32` | `crc32()` | Eingabe | `eztemplatedigestoperator.php` |
| `currentdate` | `x\|currentdate` | `currentdate()` | Parameter | `eztemplatelocaleoperator.php` |
| `datetime` | `x\|datetime` | `datetime()` | Eingabe | `eztemplatelocaleoperator.php` |
| `dec` | `x\|dec` | `dec()` | Parameter | `eztemplatearithmeticoperator.php` |
| `div` | `x\|div` | `div()` | Parameter | `eztemplatearithmeticoperator.php` |
| `downcase` | `x\|downcase` | `downcase()` | Eingabe | `eztemplatestringoperator.php` |
| `dump` | `x\|dump` | `exp_dump()` | Eingabe | `eztemplateattributeoperator.php` |
| `ends_with` | `x\|ends_with` | `ends_with()` | Eingabe | `eztemplatearrayoperator.php` |
| `eq` | `x\|eq` | `eq()` | Parameter | `eztemplatelogicoperator.php` |
| `exp_smarty_render` | `x\|exp_smarty_render` | `exp_smarty_render()` | ignoriert Eingabe | `expsmartyoperator.php` |
| `exp_twig_render` | `x\|exp_twig_render` | `exp_twig_render()` | ignoriert Eingabe | `exptwigoperator.php` |
| `expinfo` | `x\|expinfo` | `expinfo()` | Parameter | `eztemplateexpinfooperator.php` |
| `explode` | `x\|explode` | `explode()` | Eingabe | `eztemplatearrayoperator.php` |
| `extract` | `x\|extract` | `extract()` | Eingabe | `eztemplatearrayoperator.php` |
| `extract_left` | `x\|extract_left` | `extract_left()` | Eingabe | `eztemplatearrayoperator.php` |
| `extract_right` | `x\|extract_right` | `extract_right()` | Eingabe | `eztemplatearrayoperator.php` |
| `false` | `x\|exp_false` | `false` | Parameter | `eztemplatelogicoperator.php` |
| `fetch` | `x\|fetch` | `fetch()` | Parameter | `eztemplateexecuteoperator.php` |
| `fetch_alias` | `x\|fetch_alias` | `fetch_alias()` | Parameter | `eztemplateexecuteoperator.php` |
| `first_set` | `x\|first_set` | `first_set()` | ignoriert Eingabe | `eztemplatecontroloperator.php` |
| `float` | `x\|float` | `float()` | Parameter | `eztemplatearithmeticoperator.php` |
| `floor` | `x\|floor` | `floor()` | Parameter | `eztemplatearithmeticoperator.php` |
| `ge` | `x\|ge` | `ge()` | Parameter | `eztemplatelogicoperator.php` |
| `get_class` | `x\|get_class` | `get_class()` | Parameter | `eztemplatetypeoperator.php` |
| `get_type` | `x\|get_type` | `get_type()` | Parameter | `eztemplatetypeoperator.php` |
| `getenv` | `x\|getenv` | `getenv()` | Parameter | `eztemplatephpoperator.php` |
| `gettime` | `x\|gettime` | `gettime()` | Eingabe | `eztemplatelocaleoperator.php` |
| `gt` | `x\|gt` | `gt()` | Parameter | `eztemplatelogicoperator.php` |
| `has_any_role` | `x\|has_any_role` | `has_any_role()` | ignoriert Eingabe | `eztemplateroleoperator.php` |
| `has_policy` | `x\|has_policy` | `has_policy()` | ignoriert Eingabe | `eztemplateroleoperator.php` |
| `has_policy_by_user` | `x\|has_policy_by_user` | `has_policy_by_user()` | ignoriert Eingabe | `eztemplateroleoperator.php` |
| `has_role` | `x\|has_role` | `has_role()` | ignoriert Eingabe | `eztemplateroleoperator.php` |
| `has_role_by_user` | `x\|has_role_by_user` | `has_role_by_user()` | ignoriert Eingabe | `eztemplateroleoperator.php` |
| `has_role_by_user_id` | `x\|has_role_by_user_id` | `has_role_by_user_id()` | ignoriert Eingabe | `eztemplateroleoperator.php` |
| `has_role_id` | `x\|has_role_id` | `has_role_id()` | ignoriert Eingabe | `eztemplateroleoperator.php` |
| `hash` | `x\|hash` | `{}` | Eingabe | `eztemplatearrayoperator.php` |
| `hebrev` | `x\|hebrev` | `hebrev()` | Eingabe | `eztemplatestringsoperator.php` |
| `hex2bin` | `x\|hex2bin` | `hex2bin()` | Eingabe | `eztemplatestringsoperator.php` |
| `html_entity_decode` | `x\|html_entity_decode` | `html_entity_decode()` | Eingabe | `eztemplatestringsoperator.php` |
| `htmlentities` | `x\|htmlentities` | `htmlentities()` | Eingabe | `eztemplatestringsoperator.php` |
| `htmlspecialchars_decode` | `x\|htmlspecialchars_decode` | `htmlspecialchars_decode()` | Eingabe | `eztemplatestringsoperator.php` |
| `imagefile` | `x\|imagefile` | `imagefile()` | Parameter | `eztemplateimageoperator.php` |
| `implode` | `x\|implode` | `implode()` | Eingabe | `eztemplatearrayoperator.php` |
| `inc` | `x\|inc` | `inc()` | Parameter | `eztemplatearithmeticoperator.php` |
| `indent` | `x\|indent` | `indent()` | Eingabe | `eztemplatetextoperator.php` |
| `insert` | `x\|insert` | `insert()` | Eingabe | `eztemplatearrayoperator.php` |
| `int` | `x\|int` | `int()` | Parameter | `eztemplatearithmeticoperator.php` |
| `is_array` | `x\|is_array` | `is_array()` | Parameter | `eztemplatetypeoperator.php` |
| `is_boolean` | `x\|is_boolean` | `is_boolean()` | Parameter | `eztemplatetypeoperator.php` |
| `is_class` | `x\|is_class` | `is_class()` | Parameter | `eztemplatetypeoperator.php` |
| `is_float` | `x\|is_float` | `is_float()` | Parameter | `eztemplatetypeoperator.php` |
| `is_integer` | `x\|is_integer` | `is_integer()` | Parameter | `eztemplatetypeoperator.php` |
| `is_null` | `x\|is_null` | `is_null()` | Parameter | `eztemplatetypeoperator.php` |
| `is_numeric` | `x\|is_numeric` | `is_numeric()` | Parameter | `eztemplatetypeoperator.php` |
| `is_object` | `x\|is_object` | `is_object()` | Parameter | `eztemplatetypeoperator.php` |
| `is_set` | `x\|is_set` | `is_set()` | Parameter | `eztemplatetypeoperator.php` |
| `is_string` | `x\|is_string` | `is_string()` | Parameter | `eztemplatetypeoperator.php` |
| `is_unset` | `x\|is_unset` | `is_unset()` | Parameter | `eztemplatetypeoperator.php` |
| `l10n` | `x\|l10n` | `l10n()` | Eingabe | `eztemplatelocaleoperator.php` |
| `lcfirst` | `x\|lcfirst` | `lcfirst()` | Eingabe | `eztemplatestringsoperator.php` |
| `le` | `x\|le` | `le()` | Parameter | `eztemplatelogicoperator.php` |
| `levenshtein` | `x\|levenshtein` | `levenshtein()` | Eingabe | `eztemplatestringsoperator.php` |
| `locale` | `x\|locale` | `locale()` | Eingabe | `eztemplatelocaleoperator.php` |
| `lt` | `x\|lt` | `lt()` | Parameter | `eztemplatelogicoperator.php` |
| `ltrim` | `x\|ltrim` | `ltrim()` | Eingabe | `eztemplatestringsoperator.php` |
| `makedate` | `x\|makedate` | `makedate()` | Eingabe | `eztemplatelocaleoperator.php` |
| `maketime` | `x\|maketime` | `maketime()` | Eingabe | `eztemplatelocaleoperator.php` |
| `max` | `x\|max` | `exp_max()` | Parameter | `eztemplatearithmeticoperator.php` |
| `md5` | `x\|md5` | `md5()` | Eingabe | `eztemplatedigestoperator.php` |
| `merge` | `x\|exp_merge` | `merge()` | Eingabe | `eztemplatearrayoperator.php` |
| `metaphone` | `x\|metaphone` | `metaphone()` | Eingabe | `eztemplatestringsoperator.php` |
| `min` | `x\|min` | `exp_min()` | Parameter | `eztemplatearithmeticoperator.php` |
| `mod` | `x\|mod` | `mod()` | Parameter | `eztemplatearithmeticoperator.php` |
| `mul` | `x\|mul` | `mul()` | Parameter | `eztemplatearithmeticoperator.php` |
| `ne` | `x\|ne` | `ne()` | Parameter | `eztemplatelogicoperator.php` |
| `nl2br` | `x\|exp_nl2br` | `nl2br()` | Eingabe | `eztemplatephpoperator.php` |
| `not` | `x\|exp_not` | `exp_not()` | Parameter | `eztemplatelogicoperator.php` |
| `null` | `x\|exp_null` | `exp_null()` | Eingabe | `eztemplatelogicoperator.php` |
| `number_format` | `x\|exp_number_format` | `number_format()` | Eingabe | `eztemplatestringsoperator.php` |
| `or` | `x\|exp_or` | `exp_or()` | Parameter | `eztemplatelogicoperator.php` |
| `ord` | `x\|ord` | `ord()` | Eingabe | `eztemplatestringoperator.php` |
| `pad` | `x\|pad` | `pad()` | Eingabe | `eztemplatestringoperator.php` |
| `pdf` | `x\|pdf` | `pdf()` | ignoriert Eingabe | `ezpdf.php` |
| `prepend` | `x\|prepend` | `prepend()` | Eingabe | `eztemplatearrayoperator.php` |
| `quoted_printable_decode` | `x\|quoted_printable_decode` | `quoted_printable_decode()` | Eingabe | `eztemplatestringsoperator.php` |
| `quoted_printable_encode` | `x\|quoted_printable_encode` | `quoted_printable_encode()` | Eingabe | `eztemplatestringsoperator.php` |
| `quotemeta` | `x\|quotemeta` | `quotemeta()` | Eingabe | `eztemplatestringsoperator.php` |
| `rand` | `x\|rand` | `rand()` | Parameter | `eztemplatearithmeticoperator.php` |
| `rawurldecode` | `x\|rawurldecode` | `rawurldecode()` | Parameter | `eztemplatephpoperator.php` |
| `rawurlencode` | `x\|rawurlencode` | `rawurlencode()` | Parameter | `eztemplatephpoperator.php` |
| `remove` | `x\|remove` | `remove()` | Eingabe | `eztemplatearrayoperator.php` |
| `repeat` | `x\|repeat` | `repeat()` | Eingabe | `eztemplatearrayoperator.php` |
| `replace` | `x\|exp_replace` | `replace()` | Eingabe | `eztemplatearrayoperator.php` |
| `reverse` | `x\|exp_reverse` | `reverse()` | Eingabe | `eztemplatearrayoperator.php` |
| `ristring` | `x\|ristring` | `ristring()` | Eingabe | `eztemplatestringsoperator.php` |
| `roman` | `x\|roman` | `roman()` | Parameter | `eztemplatearithmeticoperator.php` |
| `rot13` | `x\|rot13` | `rot13()` | Eingabe | `eztemplatedigestoperator.php` |
| `round` | `x\|exp_round` | `round()` | Parameter | `eztemplatearithmeticoperator.php` |
| `rstring` | `x\|rstring` | `rstring()` | Eingabe | `eztemplatestringsoperator.php` |
| `rtrim` | `x\|rtrim` | `rtrim()` | Eingabe | `eztemplatestringsoperator.php` |
| `serialize` | `x\|serialize` | `serialize()` | Parameter | `eztemplatephpoperator.php` |
| `shorten` | `x\|shorten` | `shorten()` | Eingabe | `eztemplatestringoperator.php` |
| `si` | `x\|si` | `si()` | Eingabe | `eztemplateunitoperator.php` |
| `similar_text` | `x\|similar_text` | `similar_text()` | Eingabe | `eztemplatestringsoperator.php` |
| `simplify` | `x\|simplify` | `simplify()` | Eingabe | `eztemplatestringoperator.php` |
| `soundex` | `x\|soundex` | `soundex()` | Eingabe | `eztemplatestringsoperator.php` |
| `sprintf` | `x\|sprintf` | `sprintf()` | Eingabe | `eztemplatestringsoperator.php` |
| `str_contains` | `x\|str_contains` | `str_contains()` | Eingabe | `eztemplatestringsoperator.php` |
| `str_ends_with` | `x\|str_ends_with` | `str_ends_with()` | Eingabe | `eztemplatestringsoperator.php` |
| `str_getcsv` | `x\|str_getcsv` | `str_getcsv()` | Eingabe | `eztemplatestringsoperator.php` |
| `str_shuffle` | `x\|str_shuffle` | `str_shuffle()` | Eingabe | `eztemplatestringsoperator.php` |
| `str_split` | `x\|str_split` | `str_split()` | Eingabe | `eztemplatestringsoperator.php` |
| `str_starts_with` | `x\|str_starts_with` | `str_starts_with()` | Eingabe | `eztemplatestringsoperator.php` |
| `str_word_count` | `x\|str_word_count` | `str_word_count()` | Eingabe | `eztemplatestringsoperator.php` |
| `strcasecmp` | `x\|strcasecmp` | `strcasecmp()` | Eingabe | `eztemplatestringsoperator.php` |
| `strcmp` | `x\|strcmp` | `strcmp()` | Eingabe | `eztemplatestringsoperator.php` |
| `strcoll` | `x\|strcoll` | `strcoll()` | Eingabe | `eztemplatestringsoperator.php` |
| `strcspn` | `x\|strcspn` | `strcspn()` | Eingabe | `eztemplatestringsoperator.php` |
| `strip_tags` | `x\|strip_tags` | `strip_tags()` | Parameter | `eztemplatephpoperator.php` |
| `stripcslashes` | `x\|stripcslashes` | `stripcslashes()` | Eingabe | `eztemplatestringsoperator.php` |
| `stripos` | `x\|stripos` | `stripos()` | Eingabe | `eztemplatestringsoperator.php` |
| `stripslashes` | `x\|stripslashes` | `stripslashes()` | Eingabe | `eztemplatestringsoperator.php` |
| `stristr` | `x\|stristr` | `stristr()` | Eingabe | `eztemplatestringsoperator.php` |
| `strlen` | `x\|strlen` | `strlen()` | Eingabe | `eztemplatestringsoperator.php` |
| `strnatcasecmp` | `x\|strnatcasecmp` | `strnatcasecmp()` | Eingabe | `eztemplatestringsoperator.php` |
| `strnatcmp` | `x\|strnatcmp` | `strnatcmp()` | Eingabe | `eztemplatestringsoperator.php` |
| `strncasecmp` | `x\|strncasecmp` | `strncasecmp()` | Eingabe | `eztemplatestringsoperator.php` |
| `strncmp` | `x\|strncmp` | `strncmp()` | Eingabe | `eztemplatestringsoperator.php` |
| `strpbrk` | `x\|strpbrk` | `strpbrk()` | Eingabe | `eztemplatestringsoperator.php` |
| `strpos` | `x\|strpos` | `strpos()` | Eingabe | `eztemplatestringsoperator.php` |
| `strrchr` | `x\|strrchr` | `strrchr()` | Eingabe | `eztemplatestringsoperator.php` |
| `strripos` | `x\|strripos` | `strripos()` | Eingabe | `eztemplatestringsoperator.php` |
| `strrpos` | `x\|strrpos` | `strrpos()` | Eingabe | `eztemplatestringsoperator.php` |
| `strspn` | `x\|strspn` | `strspn()` | Eingabe | `eztemplatestringsoperator.php` |
| `strstr` | `x\|strstr` | `strstr()` | Eingabe | `eztemplatestringsoperator.php` |
| `strtok` | `x\|strtok` | `strtok()` | Eingabe | `eztemplatestringsoperator.php` |
| `strtr` | `x\|strtr` | `strtr()` | Eingabe | `eztemplatestringsoperator.php` |
| `sub` | `x\|sub` | `sub()` | Parameter | `eztemplatearithmeticoperator.php` |
| `substr` | `x\|substr` | `substr()` | Eingabe | `eztemplatestringsoperator.php` |
| `substr_compare` | `x\|substr_compare` | `substr_compare()` | Eingabe | `eztemplatestringsoperator.php` |
| `substr_count` | `x\|substr_count` | `substr_count()` | Eingabe | `eztemplatestringsoperator.php` |
| `substr_replace` | `x\|substr_replace` | `substr_replace()` | Eingabe | `eztemplatestringsoperator.php` |
| `sum` | `x\|sum` | `sum()` | Parameter | `eztemplatearithmeticoperator.php` |
| `texttoimage` | `x\|texttoimage` | `texttoimage()` | Eingabe | `eztemplateimageoperator.php` |
| `trim` | `x\|exp_trim` | `trim()` | Eingabe | `eztemplatestringoperator.php` |
| `true` | `x\|exp_true` | `true` | Parameter | `eztemplatelogicoperator.php` |
| `unique` | `x\|unique` | `unique()` | Eingabe | `eztemplatearrayoperator.php` |
| `unserialize` | `x\|unserialize` | `unserialize()` | Parameter | `eztemplatephpoperator.php` |
| `upcase` | `x\|upcase` | `upcase()` | Eingabe | `eztemplatestringoperator.php` |
| `upfirst` | `x\|upfirst` | `upfirst()` | Eingabe | `eztemplatestringoperator.php` |
| `upword` | `x\|upword` | `upword()` | Eingabe | `eztemplatestringoperator.php` |
| `urldecode` | `x\|urldecode` | `urldecode()` | Parameter | `eztemplatephpoperator.php` |
| `urlencode` | `x\|urlencode` | `urlencode()` | Parameter | `eztemplatephpoperator.php` |
| `vsprintf` | `x\|vsprintf` | `vsprintf()` | Eingabe | `eztemplatestringsoperator.php` |
| `wash` | `x\|wash` | `wash()` | Eingabe | `eztemplatestringoperator.php` |
| `wrap` | `x\|wrap` | `wrap()` | Eingabe | `eztemplatestringoperator.php` |

## Extension ezjscore

| Operator | Filterform | Funktionsform | Eingabe | Klassendatei |
|---|---|---|---|---|
| `ezcss` | `x\|ezcss` | `ezcss()` | Eingabe | `ezjscpackertemplatefunctions.php` |
| `ezcss_load` | `x\|ezcss_load` | `ezcss_load()` | Eingabe | `ezjscpackertemplatefunctions.php` |
| `ezcss_require` | `x\|ezcss_require` | `ezcss_require()` | Eingabe | `ezjscpackertemplatefunctions.php` |
| `ezcssfiles` | `x\|ezcssfiles` | `ezcssfiles()` | Eingabe | `ezjscpackertemplatefunctions.php` |
| `ezscript` | `x\|ezscript` | `ezscript()` | Eingabe | `ezjscpackertemplatefunctions.php` |
| `ezscript_load` | `x\|ezscript_load` | `ezscript_load()` | Eingabe | `ezjscpackertemplatefunctions.php` |
| `ezscript_require` | `x\|ezscript_require` | `ezscript_require()` | Eingabe | `ezjscpackertemplatefunctions.php` |
| `ezscriptfiles` | `x\|ezscriptfiles` | `ezscriptfiles()` | Eingabe | `ezjscpackertemplatefunctions.php` |
| `has_access_to_limitation` | `x\|has_access_to_limitation` | `has_access_to_limitation()` | Eingabe | `ezjscaccesstemplatefunctions.php` |
| `json_encode` | `x\|exp_json_encode` | `json_encode()` | Eingabe | `ezjscencodingtemplatefunctions.php` |
| `node_encode` | `x\|node_encode` | `node_encode()` | Eingabe | `ezjscencodingtemplatefunctions.php` |
| `xml_encode` | `x\|xml_encode` | `xml_encode()` | Eingabe | `ezjscencodingtemplatefunctions.php` |

## Extension ezoe

| Operator | Filterform | Funktionsform | Eingabe | Klassendatei |
|---|---|---|---|---|
| `ezoe_ini_section` | `x\|ezoe_ini_section` | `ezoe_ini_section()` | Eingabe | `ezoetemplateutils.php` |

## Extension ezstarrating

| Operator | Filterform | Funktionsform | Eingabe | Klassendatei |
|---|---|---|---|---|
| `fetch_by_starrating` | `x\|fetch_by_starrating` | `fetch_by_starrating()` | Eingabe | `ezsrtemplateoperators.php` |
| `fetch_starrating_data` | `x\|fetch_starrating_data` | `fetch_starrating_data()` | Eingabe | `ezsrtemplateoperators.php` |
| `fetch_starrating_stats` | `x\|fetch_starrating_stats` | `fetch_starrating_stats()` | Eingabe | `ezsrtemplateoperators.php` |

## Extension eztags

| Operator | Filterform | Funktionsform | Eingabe | Klassendatei |
|---|---|---|---|---|
| `eztags_parent_string` | `x\|eztags_parent_string` | `eztags_parent_string()` | Eingabe | `eztagstemplatefunctions.php` |
| `eztagscloud` | `x\|eztagscloud` | `eztagscloud()` | Eingabe | `eztagscloud.php` |
| `latest_tags` | `x\|latest_tags` | `latest_tags()` | Eingabe | `eztagstemplatefunctions.php` |
| `tag_icon` | `x\|tag_icon` | `tag_icon()` | Eingabe | `eztagstemplatefunctions.php` |
| `user_limitations` | `x\|user_limitations` | `user_limitations()` | Eingabe | `eztagstemplatefunctions.php` |

## Extension ezurlmultisite

| Operator | Filterform | Funktionsform | Eingabe | Klassendatei |
|---|---|---|---|---|
| `exturl` | `x\|exturl` | `exturl()` | Eingabe | `ezurlmultisiteurloperator.php` |
| `ezdesign` | `x\|ezdesign` | `ezdesign()` | Eingabe | `ezurlmultisiteurloperator.php` |
| `ezhttp` | `x\|ezhttp` | `ezhttp()` | ignoriert Eingabe | `ezurlmultisiteurloperator.php` |
| `ezhttp_hasvariable` | `x\|ezhttp_hasvariable` | `ezhttp_hasvariable()` | ignoriert Eingabe | `ezurlmultisiteurloperator.php` |
| `ezimage` | `x\|ezimage` | `ezimage()` | Eingabe | `ezurlmultisiteurloperator.php` |
| `ezini` | `x\|ezini` | `ezini()` | Parameter | `ezurlmultisiteurloperator.php` |
| `ezini_hasvariable` | `x\|ezini_hasvariable` | `ezini_hasvariable()` | Parameter | `ezurlmultisiteurloperator.php` |
| `ezroot` | `x\|ezroot` | `ezroot()` | Eingabe | `ezurlmultisiteurloperator.php` |
| `ezsys` | `x\|ezsys` | `ezsys()` | Eingabe | `ezurlmultisiteurloperator.php` |
| `ezurl` | `x\|ezurl` | `ezurl()` | Eingabe | `ezurlmultisiteurloperator.php` |
| `ezurl_path` | `x\|ezurl_path` | `ezurl_path()` | Eingabe | `ezurlmultisiteoperator.php` |
| `ezurl_roots` | `x\|ezurl_roots` | `ezurl_roots()` | Eingabe | `ezurlmultisiteoperator.php` |
| `ezurl_siteaccess` | `x\|ezurl_siteaccess` | `ezurl_siteaccess()` | Eingabe | `ezurlmultisiteoperator.php` |

## Extension ezwebin

| Operator | Filterform | Funktionsform | Eingabe | Klassendatei |
|---|---|---|---|---|
| `ezarchive` | `x\|ezarchive` | `ezarchive()` | Eingabe | `ezarchive.php` |
| `ezkeywordlist` | `x\|ezkeywordlist` | `ezkeywordlist()` | Eingabe | `ezkeywordlist.php` |
| `ezpagedata` | `x\|ezpagedata` | `ezpagedata()` | Eingabe | `ezpagedata.php` |
| `ezpagedata_append` | `x\|ezpagedata_append` | `ezpagedata_append()` | Eingabe | `ezpagedata.php` |
| `ezpagedata_set` | `x\|ezpagedata_set` | `ezpagedata_set()` | Eingabe | `ezpagedata.php` |
| `eztagcloud` | `x\|eztagcloud` | `eztagcloud()` | Eingabe | `eztagcloud.php` |

## Extension ezwt

| Operator | Filterform | Funktionsform | Eingabe | Klassendatei |
|---|---|---|---|---|
| `ezcreateclasslistgroups` | `x\|ezcreateclasslistgroups` | `ezcreateclasslistgroups()` | Eingabe | `ezcreateclasslistgroups.php` |

## Siehe auch

- [Operatoren und Fetch-Funktionen](twig-templates-operatoren.md)
- [Beispiele für Template-Operatoren](../beispiele/template-operatoren/README.md)
- [Smarty-Templates: Operator-Referenz](smarty-templates-operator-referenz.md)
