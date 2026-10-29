# Twig templates: operator reference

This page is for template developers who write `.twig` templates and need the exact form of one operator. It lists
the template operators of the kernel, of the template library and of the extensions of the standard distribution,
with the filter form (with an input) and the function form (without one) a `.twig` uses, and how the operator takes
its input. The rules behind the columns are in [Operators and fetch functions](twig-templates-operators.md).

The tables were written from the operators the template system registers (`eZTemplate::factory()->Operators`) on a
development installation; an extension you add brings its operators the same way, and every one of them is a filter
and a function of the same name.

## How to read the tables

- **Filter form**: `x|name(...)`, the operator with an input, as `{$x|name( ... )}` in a `.tpl`.
- **Function form**: `name(...)`, the operator without an input, as `{name( ... )}` in a `.tpl`.
- **exp_ forms**: where Twig has a filter or function of the same name, or the name is a word of Twig's parser
  (`and`, `or`, `not`, `true`, `false`, `null`), the operator is `exp_<name>`. `exp_<name>` works for every operator.
- **Input**: `input`: the operator works on its input (the filter form gives it); `param`: the input becomes the first
  parameter, so `x|op(y)` and `op(x, y)` are the same call; `ignores input`: a control operator or one that reads its
  parameters only.

## Kernel (kernel/)

| Operator | Filter form | Function form | Input | Class file |
|---|---|---|---|---|
| `action_icon` | `x\|action_icon` | `action_icon()` | input | `ezwordtoimageoperator.php` |
| `alphabet` | `x\|alphabet` | `alphabet()` | ignores input | `ezalphabetoperator.php` |
| `audit_label` | `x\|audit_label` | `audit_label()` | input | `expaudittemplateoperator.php` |
| `autolink` | `x\|autolink` | `autolink()` | input | `ezautolinkoperator.php` |
| `class_icon` | `x\|class_icon` | `class_icon()` | input | `ezwordtoimageoperator.php` |
| `classgroup_icon` | `x\|classgroup_icon` | `classgroup_icon()` | input | `ezwordtoimageoperator.php` |
| `content_structure_tree` | `x\|content_structure_tree` | `content_structure_tree()` | ignores input | `ezcontentstructuretreeoperator.php` |
| `csp_nonce` | `x\|csp_nonce` | `csp_nonce()` | ignores input | `expcontentsecuritypolicyoperator.php` |
| `d18n` | `x\|d18n` | `d18n()` | input | `ezi18noperator.php` |
| `exp_demo_mode` | `x\|exp_demo_mode` | `exp_demo_mode()` | ignores input | `expdemomodeoperator.php` |
| `exp_environment` | `x\|exp_environment` | `exp_environment()` | ignores input | `expenvironmentoperator.php` |
| `exp_environment_badge` | `x\|exp_environment_badge` | `exp_environment_badge()` | ignores input | `expenvironmentoperator.php` |
| `ezmodule` | `x\|ezmodule` | `ezmodule()` | ignores input | `ezmoduleoperator.php` |
| `ezpackage` | `x\|ezpackage` | `ezpackage()` | input | `ezpackageoperator.php` |
| `ezpreference` | `x\|ezpreference` | `ezpreference()` | param | `ezkerneloperator.php` |
| `eztoc` | `x\|eztoc` | `eztoc()` | ignores input | `eztocoperator.php` |
| `flag_icon` | `x\|flag_icon` | `flag_icon()` | input | `ezwordtoimageoperator.php` |
| `i18n` | `x\|i18n` | `i18n()` | input | `ezi18noperator.php` |
| `icon` | `x\|icon` | `icon()` | input | `ezwordtoimageoperator.php` |
| `icon_info` | `x\|icon_info` | `icon_info()` | ignores input | `ezwordtoimageoperator.php` |
| `installation_name` | `x\|installation_name` | `installation_name()` | ignores input | `expinstallationoperator.php` |
| `is_production_system` | `x\|is_production_system` | `is_production_system()` | ignores input | `expinstallationoperator.php` |
| `language_switcher` | `x\|language_switcher` | `language_switcher()` | ignores input | `ezplanguageswitcheroperator.php` |
| `mimetype_icon` | `x\|mimetype_icon` | `mimetype_icon()` | input | `ezwordtoimageoperator.php` |
| `module_params` | `x\|module_params` | `module_params()` | ignores input | `ezmoduleparamsoperator.php` |
| `month_overview` | `x\|month_overview` | `month_overview()` | input | `ezdateoperatorcollection.php` |
| `simpletags` | `x\|simpletags` | `simpletags()` | input | `ezsimpletagsoperator.php` |
| `siteaccess_menu` | `x\|siteaccess_menu` | `siteaccess_menu()` | param | `ezpsiteaccessmenuoperator.php` |
| `siteaccess_menu_key` | `x\|siteaccess_menu_key` | `siteaccess_menu_key()` | param | `ezpsiteaccessmenuoperator.php` |
| `siteaccess_url` | `x\|siteaccess_url` | `siteaccess_url()` | param | `ezpsiteaccessurloperator.php` |
| `topmenu` | `x\|topmenu` | `topmenu()` | ignores input | `eztopmenuoperator.php` |
| `transfer_actions` | `x\|transfer_actions` | `transfer_actions()` | ignores input | `expkerneltransferoperator.php` |
| `transfer_related` | `x\|transfer_related` | `transfer_related()` | ignores input | `expkerneltransferoperator.php` |
| `transfer_tool` | `x\|transfer_tool` | `transfer_tool()` | ignores input | `expkerneltransferoperator.php` |
| `transfer_tools` | `x\|transfer_tools` | `transfer_tools()` | ignores input | `expkerneltransferoperator.php` |
| `treemenu` | `x\|treemenu` | `treemenu()` | ignores input | `eztreemenuoperator.php` |
| `view_parameter` | `x\|view_parameter` | `view_parameter()` | input | `expviewparameteroperator.php` |
| `wordtoimage` | `x\|wordtoimage` | `wordtoimage()` | input | `ezwordtoimageoperator.php` |
| `x18n` | `x\|x18n` | `x18n()` | input | `ezi18noperator.php` |

## Template library (lib/)

| Operator | Filter form | Function form | Input | Class file |
|---|---|---|---|---|
| `abs` | `x\|exp_abs` | `abs()` | param | `eztemplatearithmeticoperator.php` |
| `addcslashes` | `x\|addcslashes` | `addcslashes()` | input | `eztemplatestringsoperator.php` |
| `addslashes` | `x\|addslashes` | `addslashes()` | input | `eztemplatestringsoperator.php` |
| `and` | `x\|exp_and` | `exp_and()` | param | `eztemplatelogicoperator.php` |
| `append` | `x\|append` | `append()` | input | `eztemplatearrayoperator.php` |
| `array` | `x\|array` | `[]` | input | `eztemplatearrayoperator.php` |
| `array_append` | `x\|array_append` | `array_append()` | input | `eztemplatearrayoperator.php` |
| `array_merge` | `x\|array_merge` | `array_merge()` | input | `eztemplatearrayoperator.php` |
| `array_prepend` | `x\|array_prepend` | `array_prepend()` | input | `eztemplatearrayoperator.php` |
| `array_sum` | `x\|array_sum` | `array_sum()` | input | `eztemplatearrayoperator.php` |
| `attribute` | `x\|attribute` | `exp_attribute()` | input | `eztemplateattributeoperator.php` |
| `begins_with` | `x\|begins_with` | `begins_with()` | input | `eztemplatearrayoperator.php` |
| `bin2hex` | `x\|bin2hex` | `bin2hex()` | input | `eztemplatestringsoperator.php` |
| `break` | `x\|break` | `break()` | input | `eztemplatestringoperator.php` |
| `ceil` | `x\|ceil` | `ceil()` | param | `eztemplatearithmeticoperator.php` |
| `choose` | `x\|choose` | `choose()` | input | `eztemplatelogicoperator.php` |
| `chr` | `x\|chr` | `chr()` | input | `eztemplatestringoperator.php` |
| `chunk_split` | `x\|chunk_split` | `chunk_split()` | input | `eztemplatestringsoperator.php` |
| `compare` | `x\|compare` | `compare()` | input | `eztemplatearrayoperator.php` |
| `concat` | `x\|concat` | `concat()` | param | `eztemplatetextoperator.php` |
| `cond` | `x\|cond` | `cond()` | ignores input | `eztemplatecontroloperator.php` |
| `contains` | `x\|contains` | `contains()` | input | `eztemplatearrayoperator.php` |
| `convert_uudecode` | `x\|convert_uudecode` | `convert_uudecode()` | input | `eztemplatestringsoperator.php` |
| `convert_uuencode` | `x\|convert_uuencode` | `convert_uuencode()` | input | `eztemplatestringsoperator.php` |
| `count` | `x\|count` | `count()` | input | `eztemplatearithmeticoperator.php` |
| `count_chars` | `x\|count_chars` | `count_chars()` | input | `eztemplatestringoperator.php` |
| `count_words` | `x\|count_words` | `count_words()` | input | `eztemplatestringoperator.php` |
| `crc32` | `x\|crc32` | `crc32()` | input | `eztemplatedigestoperator.php` |
| `currentdate` | `x\|currentdate` | `currentdate()` | param | `eztemplatelocaleoperator.php` |
| `datetime` | `x\|datetime` | `datetime()` | input | `eztemplatelocaleoperator.php` |
| `dec` | `x\|dec` | `dec()` | param | `eztemplatearithmeticoperator.php` |
| `div` | `x\|div` | `div()` | param | `eztemplatearithmeticoperator.php` |
| `downcase` | `x\|downcase` | `downcase()` | input | `eztemplatestringoperator.php` |
| `dump` | `x\|dump` | `exp_dump()` | input | `eztemplateattributeoperator.php` |
| `ends_with` | `x\|ends_with` | `ends_with()` | input | `eztemplatearrayoperator.php` |
| `eq` | `x\|eq` | `eq()` | param | `eztemplatelogicoperator.php` |
| `exp_smarty_render` | `x\|exp_smarty_render` | `exp_smarty_render()` | ignores input | `expsmartyoperator.php` |
| `exp_twig_render` | `x\|exp_twig_render` | `exp_twig_render()` | ignores input | `exptwigoperator.php` |
| `expinfo` | `x\|expinfo` | `expinfo()` | param | `eztemplateexpinfooperator.php` |
| `explode` | `x\|explode` | `explode()` | input | `eztemplatearrayoperator.php` |
| `extract` | `x\|extract` | `extract()` | input | `eztemplatearrayoperator.php` |
| `extract_left` | `x\|extract_left` | `extract_left()` | input | `eztemplatearrayoperator.php` |
| `extract_right` | `x\|extract_right` | `extract_right()` | input | `eztemplatearrayoperator.php` |
| `false` | `x\|exp_false` | `false` | param | `eztemplatelogicoperator.php` |
| `fetch` | `x\|fetch` | `fetch()` | param | `eztemplateexecuteoperator.php` |
| `fetch_alias` | `x\|fetch_alias` | `fetch_alias()` | param | `eztemplateexecuteoperator.php` |
| `first_set` | `x\|first_set` | `first_set()` | ignores input | `eztemplatecontroloperator.php` |
| `float` | `x\|float` | `float()` | param | `eztemplatearithmeticoperator.php` |
| `floor` | `x\|floor` | `floor()` | param | `eztemplatearithmeticoperator.php` |
| `ge` | `x\|ge` | `ge()` | param | `eztemplatelogicoperator.php` |
| `get_class` | `x\|get_class` | `get_class()` | param | `eztemplatetypeoperator.php` |
| `get_type` | `x\|get_type` | `get_type()` | param | `eztemplatetypeoperator.php` |
| `getenv` | `x\|getenv` | `getenv()` | param | `eztemplatephpoperator.php` |
| `gettime` | `x\|gettime` | `gettime()` | input | `eztemplatelocaleoperator.php` |
| `gt` | `x\|gt` | `gt()` | param | `eztemplatelogicoperator.php` |
| `has_any_role` | `x\|has_any_role` | `has_any_role()` | ignores input | `eztemplateroleoperator.php` |
| `has_policy` | `x\|has_policy` | `has_policy()` | ignores input | `eztemplateroleoperator.php` |
| `has_policy_by_user` | `x\|has_policy_by_user` | `has_policy_by_user()` | ignores input | `eztemplateroleoperator.php` |
| `has_role` | `x\|has_role` | `has_role()` | ignores input | `eztemplateroleoperator.php` |
| `has_role_by_user` | `x\|has_role_by_user` | `has_role_by_user()` | ignores input | `eztemplateroleoperator.php` |
| `has_role_by_user_id` | `x\|has_role_by_user_id` | `has_role_by_user_id()` | ignores input | `eztemplateroleoperator.php` |
| `has_role_id` | `x\|has_role_id` | `has_role_id()` | ignores input | `eztemplateroleoperator.php` |
| `hash` | `x\|hash` | `{}` | input | `eztemplatearrayoperator.php` |
| `hebrev` | `x\|hebrev` | `hebrev()` | input | `eztemplatestringsoperator.php` |
| `hex2bin` | `x\|hex2bin` | `hex2bin()` | input | `eztemplatestringsoperator.php` |
| `html_entity_decode` | `x\|html_entity_decode` | `html_entity_decode()` | input | `eztemplatestringsoperator.php` |
| `htmlentities` | `x\|htmlentities` | `htmlentities()` | input | `eztemplatestringsoperator.php` |
| `htmlspecialchars_decode` | `x\|htmlspecialchars_decode` | `htmlspecialchars_decode()` | input | `eztemplatestringsoperator.php` |
| `imagefile` | `x\|imagefile` | `imagefile()` | param | `eztemplateimageoperator.php` |
| `implode` | `x\|implode` | `implode()` | input | `eztemplatearrayoperator.php` |
| `inc` | `x\|inc` | `inc()` | param | `eztemplatearithmeticoperator.php` |
| `indent` | `x\|indent` | `indent()` | input | `eztemplatetextoperator.php` |
| `insert` | `x\|insert` | `insert()` | input | `eztemplatearrayoperator.php` |
| `int` | `x\|int` | `int()` | param | `eztemplatearithmeticoperator.php` |
| `is_array` | `x\|is_array` | `is_array()` | param | `eztemplatetypeoperator.php` |
| `is_boolean` | `x\|is_boolean` | `is_boolean()` | param | `eztemplatetypeoperator.php` |
| `is_class` | `x\|is_class` | `is_class()` | param | `eztemplatetypeoperator.php` |
| `is_float` | `x\|is_float` | `is_float()` | param | `eztemplatetypeoperator.php` |
| `is_integer` | `x\|is_integer` | `is_integer()` | param | `eztemplatetypeoperator.php` |
| `is_null` | `x\|is_null` | `is_null()` | param | `eztemplatetypeoperator.php` |
| `is_numeric` | `x\|is_numeric` | `is_numeric()` | param | `eztemplatetypeoperator.php` |
| `is_object` | `x\|is_object` | `is_object()` | param | `eztemplatetypeoperator.php` |
| `is_set` | `x\|is_set` | `is_set()` | param | `eztemplatetypeoperator.php` |
| `is_string` | `x\|is_string` | `is_string()` | param | `eztemplatetypeoperator.php` |
| `is_unset` | `x\|is_unset` | `is_unset()` | param | `eztemplatetypeoperator.php` |
| `l10n` | `x\|l10n` | `l10n()` | input | `eztemplatelocaleoperator.php` |
| `lcfirst` | `x\|lcfirst` | `lcfirst()` | input | `eztemplatestringsoperator.php` |
| `le` | `x\|le` | `le()` | param | `eztemplatelogicoperator.php` |
| `levenshtein` | `x\|levenshtein` | `levenshtein()` | input | `eztemplatestringsoperator.php` |
| `locale` | `x\|locale` | `locale()` | input | `eztemplatelocaleoperator.php` |
| `lt` | `x\|lt` | `lt()` | param | `eztemplatelogicoperator.php` |
| `ltrim` | `x\|ltrim` | `ltrim()` | input | `eztemplatestringsoperator.php` |
| `makedate` | `x\|makedate` | `makedate()` | input | `eztemplatelocaleoperator.php` |
| `maketime` | `x\|maketime` | `maketime()` | input | `eztemplatelocaleoperator.php` |
| `max` | `x\|max` | `exp_max()` | param | `eztemplatearithmeticoperator.php` |
| `md5` | `x\|md5` | `md5()` | input | `eztemplatedigestoperator.php` |
| `merge` | `x\|exp_merge` | `merge()` | input | `eztemplatearrayoperator.php` |
| `metaphone` | `x\|metaphone` | `metaphone()` | input | `eztemplatestringsoperator.php` |
| `min` | `x\|min` | `exp_min()` | param | `eztemplatearithmeticoperator.php` |
| `mod` | `x\|mod` | `mod()` | param | `eztemplatearithmeticoperator.php` |
| `mul` | `x\|mul` | `mul()` | param | `eztemplatearithmeticoperator.php` |
| `ne` | `x\|ne` | `ne()` | param | `eztemplatelogicoperator.php` |
| `nl2br` | `x\|exp_nl2br` | `nl2br()` | input | `eztemplatephpoperator.php` |
| `not` | `x\|exp_not` | `exp_not()` | param | `eztemplatelogicoperator.php` |
| `null` | `x\|exp_null` | `exp_null()` | input | `eztemplatelogicoperator.php` |
| `number_format` | `x\|exp_number_format` | `number_format()` | input | `eztemplatestringsoperator.php` |
| `or` | `x\|exp_or` | `exp_or()` | param | `eztemplatelogicoperator.php` |
| `ord` | `x\|ord` | `ord()` | input | `eztemplatestringoperator.php` |
| `pad` | `x\|pad` | `pad()` | input | `eztemplatestringoperator.php` |
| `pdf` | `x\|pdf` | `pdf()` | ignores input | `ezpdf.php` |
| `prepend` | `x\|prepend` | `prepend()` | input | `eztemplatearrayoperator.php` |
| `quoted_printable_decode` | `x\|quoted_printable_decode` | `quoted_printable_decode()` | input | `eztemplatestringsoperator.php` |
| `quoted_printable_encode` | `x\|quoted_printable_encode` | `quoted_printable_encode()` | input | `eztemplatestringsoperator.php` |
| `quotemeta` | `x\|quotemeta` | `quotemeta()` | input | `eztemplatestringsoperator.php` |
| `rand` | `x\|rand` | `rand()` | param | `eztemplatearithmeticoperator.php` |
| `rawurldecode` | `x\|rawurldecode` | `rawurldecode()` | param | `eztemplatephpoperator.php` |
| `rawurlencode` | `x\|rawurlencode` | `rawurlencode()` | param | `eztemplatephpoperator.php` |
| `remove` | `x\|remove` | `remove()` | input | `eztemplatearrayoperator.php` |
| `repeat` | `x\|repeat` | `repeat()` | input | `eztemplatearrayoperator.php` |
| `replace` | `x\|exp_replace` | `replace()` | input | `eztemplatearrayoperator.php` |
| `reverse` | `x\|exp_reverse` | `reverse()` | input | `eztemplatearrayoperator.php` |
| `ristring` | `x\|ristring` | `ristring()` | input | `eztemplatestringsoperator.php` |
| `roman` | `x\|roman` | `roman()` | param | `eztemplatearithmeticoperator.php` |
| `rot13` | `x\|rot13` | `rot13()` | input | `eztemplatedigestoperator.php` |
| `round` | `x\|exp_round` | `round()` | param | `eztemplatearithmeticoperator.php` |
| `rstring` | `x\|rstring` | `rstring()` | input | `eztemplatestringsoperator.php` |
| `rtrim` | `x\|rtrim` | `rtrim()` | input | `eztemplatestringsoperator.php` |
| `serialize` | `x\|serialize` | `serialize()` | param | `eztemplatephpoperator.php` |
| `shorten` | `x\|shorten` | `shorten()` | input | `eztemplatestringoperator.php` |
| `si` | `x\|si` | `si()` | input | `eztemplateunitoperator.php` |
| `similar_text` | `x\|similar_text` | `similar_text()` | input | `eztemplatestringsoperator.php` |
| `simplify` | `x\|simplify` | `simplify()` | input | `eztemplatestringoperator.php` |
| `soundex` | `x\|soundex` | `soundex()` | input | `eztemplatestringsoperator.php` |
| `sprintf` | `x\|sprintf` | `sprintf()` | input | `eztemplatestringsoperator.php` |
| `str_contains` | `x\|str_contains` | `str_contains()` | input | `eztemplatestringsoperator.php` |
| `str_ends_with` | `x\|str_ends_with` | `str_ends_with()` | input | `eztemplatestringsoperator.php` |
| `str_getcsv` | `x\|str_getcsv` | `str_getcsv()` | input | `eztemplatestringsoperator.php` |
| `str_shuffle` | `x\|str_shuffle` | `str_shuffle()` | input | `eztemplatestringsoperator.php` |
| `str_split` | `x\|str_split` | `str_split()` | input | `eztemplatestringsoperator.php` |
| `str_starts_with` | `x\|str_starts_with` | `str_starts_with()` | input | `eztemplatestringsoperator.php` |
| `str_word_count` | `x\|str_word_count` | `str_word_count()` | input | `eztemplatestringsoperator.php` |
| `strcasecmp` | `x\|strcasecmp` | `strcasecmp()` | input | `eztemplatestringsoperator.php` |
| `strcmp` | `x\|strcmp` | `strcmp()` | input | `eztemplatestringsoperator.php` |
| `strcoll` | `x\|strcoll` | `strcoll()` | input | `eztemplatestringsoperator.php` |
| `strcspn` | `x\|strcspn` | `strcspn()` | input | `eztemplatestringsoperator.php` |
| `strip_tags` | `x\|strip_tags` | `strip_tags()` | param | `eztemplatephpoperator.php` |
| `stripcslashes` | `x\|stripcslashes` | `stripcslashes()` | input | `eztemplatestringsoperator.php` |
| `stripos` | `x\|stripos` | `stripos()` | input | `eztemplatestringsoperator.php` |
| `stripslashes` | `x\|stripslashes` | `stripslashes()` | input | `eztemplatestringsoperator.php` |
| `stristr` | `x\|stristr` | `stristr()` | input | `eztemplatestringsoperator.php` |
| `strlen` | `x\|strlen` | `strlen()` | input | `eztemplatestringsoperator.php` |
| `strnatcasecmp` | `x\|strnatcasecmp` | `strnatcasecmp()` | input | `eztemplatestringsoperator.php` |
| `strnatcmp` | `x\|strnatcmp` | `strnatcmp()` | input | `eztemplatestringsoperator.php` |
| `strncasecmp` | `x\|strncasecmp` | `strncasecmp()` | input | `eztemplatestringsoperator.php` |
| `strncmp` | `x\|strncmp` | `strncmp()` | input | `eztemplatestringsoperator.php` |
| `strpbrk` | `x\|strpbrk` | `strpbrk()` | input | `eztemplatestringsoperator.php` |
| `strpos` | `x\|strpos` | `strpos()` | input | `eztemplatestringsoperator.php` |
| `strrchr` | `x\|strrchr` | `strrchr()` | input | `eztemplatestringsoperator.php` |
| `strripos` | `x\|strripos` | `strripos()` | input | `eztemplatestringsoperator.php` |
| `strrpos` | `x\|strrpos` | `strrpos()` | input | `eztemplatestringsoperator.php` |
| `strspn` | `x\|strspn` | `strspn()` | input | `eztemplatestringsoperator.php` |
| `strstr` | `x\|strstr` | `strstr()` | input | `eztemplatestringsoperator.php` |
| `strtok` | `x\|strtok` | `strtok()` | input | `eztemplatestringsoperator.php` |
| `strtr` | `x\|strtr` | `strtr()` | input | `eztemplatestringsoperator.php` |
| `sub` | `x\|sub` | `sub()` | param | `eztemplatearithmeticoperator.php` |
| `substr` | `x\|substr` | `substr()` | input | `eztemplatestringsoperator.php` |
| `substr_compare` | `x\|substr_compare` | `substr_compare()` | input | `eztemplatestringsoperator.php` |
| `substr_count` | `x\|substr_count` | `substr_count()` | input | `eztemplatestringsoperator.php` |
| `substr_replace` | `x\|substr_replace` | `substr_replace()` | input | `eztemplatestringsoperator.php` |
| `sum` | `x\|sum` | `sum()` | param | `eztemplatearithmeticoperator.php` |
| `texttoimage` | `x\|texttoimage` | `texttoimage()` | input | `eztemplateimageoperator.php` |
| `trim` | `x\|exp_trim` | `trim()` | input | `eztemplatestringoperator.php` |
| `true` | `x\|exp_true` | `true` | param | `eztemplatelogicoperator.php` |
| `unique` | `x\|unique` | `unique()` | input | `eztemplatearrayoperator.php` |
| `unserialize` | `x\|unserialize` | `unserialize()` | param | `eztemplatephpoperator.php` |
| `upcase` | `x\|upcase` | `upcase()` | input | `eztemplatestringoperator.php` |
| `upfirst` | `x\|upfirst` | `upfirst()` | input | `eztemplatestringoperator.php` |
| `upword` | `x\|upword` | `upword()` | input | `eztemplatestringoperator.php` |
| `urldecode` | `x\|urldecode` | `urldecode()` | param | `eztemplatephpoperator.php` |
| `urlencode` | `x\|urlencode` | `urlencode()` | param | `eztemplatephpoperator.php` |
| `vsprintf` | `x\|vsprintf` | `vsprintf()` | input | `eztemplatestringsoperator.php` |
| `wash` | `x\|wash` | `wash()` | input | `eztemplatestringoperator.php` |
| `wrap` | `x\|wrap` | `wrap()` | input | `eztemplatestringoperator.php` |

## Extension ezjscore

| Operator | Filter form | Function form | Input | Class file |
|---|---|---|---|---|
| `ezcss` | `x\|ezcss` | `ezcss()` | input | `ezjscpackertemplatefunctions.php` |
| `ezcss_load` | `x\|ezcss_load` | `ezcss_load()` | input | `ezjscpackertemplatefunctions.php` |
| `ezcss_require` | `x\|ezcss_require` | `ezcss_require()` | input | `ezjscpackertemplatefunctions.php` |
| `ezcssfiles` | `x\|ezcssfiles` | `ezcssfiles()` | input | `ezjscpackertemplatefunctions.php` |
| `ezscript` | `x\|ezscript` | `ezscript()` | input | `ezjscpackertemplatefunctions.php` |
| `ezscript_load` | `x\|ezscript_load` | `ezscript_load()` | input | `ezjscpackertemplatefunctions.php` |
| `ezscript_require` | `x\|ezscript_require` | `ezscript_require()` | input | `ezjscpackertemplatefunctions.php` |
| `ezscriptfiles` | `x\|ezscriptfiles` | `ezscriptfiles()` | input | `ezjscpackertemplatefunctions.php` |
| `has_access_to_limitation` | `x\|has_access_to_limitation` | `has_access_to_limitation()` | input | `ezjscaccesstemplatefunctions.php` |
| `json_encode` | `x\|exp_json_encode` | `json_encode()` | input | `ezjscencodingtemplatefunctions.php` |
| `node_encode` | `x\|node_encode` | `node_encode()` | input | `ezjscencodingtemplatefunctions.php` |
| `xml_encode` | `x\|xml_encode` | `xml_encode()` | input | `ezjscencodingtemplatefunctions.php` |

## Extension ezoe

| Operator | Filter form | Function form | Input | Class file |
|---|---|---|---|---|
| `ezoe_ini_section` | `x\|ezoe_ini_section` | `ezoe_ini_section()` | input | `ezoetemplateutils.php` |

## Extension ezstarrating

| Operator | Filter form | Function form | Input | Class file |
|---|---|---|---|---|
| `fetch_by_starrating` | `x\|fetch_by_starrating` | `fetch_by_starrating()` | input | `ezsrtemplateoperators.php` |
| `fetch_starrating_data` | `x\|fetch_starrating_data` | `fetch_starrating_data()` | input | `ezsrtemplateoperators.php` |
| `fetch_starrating_stats` | `x\|fetch_starrating_stats` | `fetch_starrating_stats()` | input | `ezsrtemplateoperators.php` |

## Extension eztags

| Operator | Filter form | Function form | Input | Class file |
|---|---|---|---|---|
| `eztags_parent_string` | `x\|eztags_parent_string` | `eztags_parent_string()` | input | `eztagstemplatefunctions.php` |
| `eztagscloud` | `x\|eztagscloud` | `eztagscloud()` | input | `eztagscloud.php` |
| `latest_tags` | `x\|latest_tags` | `latest_tags()` | input | `eztagstemplatefunctions.php` |
| `tag_icon` | `x\|tag_icon` | `tag_icon()` | input | `eztagstemplatefunctions.php` |
| `user_limitations` | `x\|user_limitations` | `user_limitations()` | input | `eztagstemplatefunctions.php` |

## Extension ezurlmultisite

| Operator | Filter form | Function form | Input | Class file |
|---|---|---|---|---|
| `exturl` | `x\|exturl` | `exturl()` | input | `ezurlmultisiteurloperator.php` |
| `ezdesign` | `x\|ezdesign` | `ezdesign()` | input | `ezurlmultisiteurloperator.php` |
| `ezhttp` | `x\|ezhttp` | `ezhttp()` | ignores input | `ezurlmultisiteurloperator.php` |
| `ezhttp_hasvariable` | `x\|ezhttp_hasvariable` | `ezhttp_hasvariable()` | ignores input | `ezurlmultisiteurloperator.php` |
| `ezimage` | `x\|ezimage` | `ezimage()` | input | `ezurlmultisiteurloperator.php` |
| `ezini` | `x\|ezini` | `ezini()` | param | `ezurlmultisiteurloperator.php` |
| `ezini_hasvariable` | `x\|ezini_hasvariable` | `ezini_hasvariable()` | param | `ezurlmultisiteurloperator.php` |
| `ezroot` | `x\|ezroot` | `ezroot()` | input | `ezurlmultisiteurloperator.php` |
| `ezsys` | `x\|ezsys` | `ezsys()` | input | `ezurlmultisiteurloperator.php` |
| `ezurl` | `x\|ezurl` | `ezurl()` | input | `ezurlmultisiteurloperator.php` |
| `ezurl_path` | `x\|ezurl_path` | `ezurl_path()` | input | `ezurlmultisiteoperator.php` |
| `ezurl_roots` | `x\|ezurl_roots` | `ezurl_roots()` | input | `ezurlmultisiteoperator.php` |
| `ezurl_siteaccess` | `x\|ezurl_siteaccess` | `ezurl_siteaccess()` | input | `ezurlmultisiteoperator.php` |

## Extension ezwebin

| Operator | Filter form | Function form | Input | Class file |
|---|---|---|---|---|
| `ezarchive` | `x\|ezarchive` | `ezarchive()` | input | `ezarchive.php` |
| `ezkeywordlist` | `x\|ezkeywordlist` | `ezkeywordlist()` | input | `ezkeywordlist.php` |
| `ezpagedata` | `x\|ezpagedata` | `ezpagedata()` | input | `ezpagedata.php` |
| `ezpagedata_append` | `x\|ezpagedata_append` | `ezpagedata_append()` | input | `ezpagedata.php` |
| `ezpagedata_set` | `x\|ezpagedata_set` | `ezpagedata_set()` | input | `ezpagedata.php` |
| `eztagcloud` | `x\|eztagcloud` | `eztagcloud()` | input | `eztagcloud.php` |

## Extension ezwt

| Operator | Filter form | Function form | Input | Class file |
|---|---|---|---|---|
| `ezcreateclasslistgroups` | `x\|ezcreateclasslistgroups` | `ezcreateclasslistgroups()` | input | `ezcreateclasslistgroups.php` |

## Related

- [Operators and fetch functions](twig-templates-operators.md)
- [Template operator examples](../examples/template-operators/README.md)
- [Smarty templates: operator reference](smarty-templates-operator-reference.md)
