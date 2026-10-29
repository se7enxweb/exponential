---
title: Template operators
audience: developer, designer, app-creator
category: development
order: 0
summary: Every template operator registered on a development installation, grouped by family with parameters, input, return value and examples rendered on a development installation, plus how operators are registered, written, extended and overridden, fetch aliases, fetch functions, and OW Simple Operator.
keywords: template operators, operator reference, operator families, fetch_alias, fetch functions, owsimpleoperator, custom operator, override operator
type: help
---
# Template operators

An operator takes a value and returns one: `{$node.name|wash}`, `{sum( 2, 3 )}`, `{fetch( 'content', 'node', hash( 'node_id', 89 ) )}`.
Operators come from the template library (`lib/eztemplate/`), the kernel (`kernel/common/` and friends) and the
active extensions; on a development installation the engine registers 401 of them. This section has 47 pages:

- the [operator index](operator-index.md), A to Z, which leads to the section of each operator;
- one page per operator family, with every operator's parameters, input, return value and at least one example
  rendered on a development installation against real content (interpreted, and compiled into a scratch folder for comparison);
- how operators are registered, written, extended and overridden, and the traps found in the code on the way;
- [fetch aliases](fetch-aliases.md) and [fetch functions](fetch-functions-how-they-work.md), the data side of
  templates;
- [OW Simple Operator](owsimpleoperator.md), the extension for writing small operators quickly.

The generated pages in [Examples: template operators](../../examples/template-operators/README.md) list each
operator with its parameter list from the code and its first use in a shipped template.

## Indexes

- [Operator index](operator-index.md): All 401 template operators registered on the site and admin siteaccesses, A to Z, each with the family page that documents its parameters, return value and examples, the class that serves it and where that class comes from.

## Template library operators

- [Arithmetic operators](arithmetic-operators.md): The 18 operators of eZTemplateArithmeticOperator (sum, sub, mul, div, mod, inc, dec, min, max, abs, ceil, floor, round, int, float, count, roman, rand) with their parameters, input, return value and examples rendered on a development installation.
- [Array operators](array-operators.md): The 24 operators of eZTemplateArrayOperator (array, hash, append, prepend, merge, contains, compare, extract, extract_left, extract_right, begins_with, ends_with, implode, explode, repeat, reverse, insert, remove, replace, unique, array_sum and three deprecated names), on arrays and on strings, with examples rendered on a development installation.
- [Logic operators](logic-operators.md): The 13 operators of eZTemplateLogicOperator (eq, ne, lt, gt, le, ge, and, or, not, null, true, false, choose), including the count rule of lt/gt/le/ge that compares strings by length, with examples rendered on a development installation.
- [Control operators](control-operators.md): cond and first_set from eZTemplateControlOperator - inline conditions and fallbacks that evaluate only what they need, how the interpreter and the compiled template differ for a false default, with examples rendered on a development installation.
- [Type operators](type-operators.md): The 13 operators of eZTemplateTypeOperator (is_array, is_boolean, is_integer, is_float, is_numeric, is_string, is_object, is_null, is_class, is_set, is_unset, get_type, get_class) with their input rules and examples rendered on a development installation.
- [String operators](string-operators.md): The 15 operators of eZTemplateStringOperator (upcase, downcase, upfirst, upword, count_words, count_chars, trim, simplify, pad, shorten, wrap, break, wash, ord, chr), every wash type (xhtml, email, javascript, pdf), and examples rendered on a development installation.
- [PHP string functions: search and compare](php-string-functions-search-and-compare.md): The searching, comparing and similarity operators of eZTemplateStringsOperator (strlen, strpos, stripos, strrpos, strripos, strstr, stristr, strrchr, strpbrk, strspn, strcspn, substr_count, str_contains, str_starts_with, str_ends_with, strcmp, strcasecmp, strncmp, strncasecmp, strnatcmp, strnatcasecmp, strcoll, substr_compare, levenshtein, similar_text, soundex, metaphone), how their parameters map to PHP, with examples rendered on a development installation.
- [PHP string functions: transform](php-string-functions-transform.md): The transforming operators of eZTemplateStringsOperator (substr, substr_replace, ristring, rstring, strtr, str_split, str_word_count, str_getcsv, strtok, ltrim, rtrim, lcfirst, chunk_split, addslashes, stripslashes, addcslashes, stripcslashes, quotemeta, strip_tags, str_shuffle, hebrev, plus str_increment and str_decrement), the strip_tags registration trap, with examples rendered on a development installation.
- [PHP string functions: encode and format](php-string-functions-encode-and-format.md): The encoding and formatting operators of eZTemplateStringsOperator (bin2hex, hex2bin, convert_uuencode, convert_uudecode, quoted_printable_encode, quoted_printable_decode, htmlentities, html_entity_decode, htmlspecialchars_decode, sprintf, vsprintf, number_format) with their PHP argument mapping and defaults, and examples rendered on a development installation.
- [PHP function operators](php-function-operators.md): How template.ini [PHP] PHPOperatorList turns one-argument PHP functions into template operators through eZTemplatePHPOperator, the operators it gives on a development installation (urlencode, urldecode, rawurlencode, rawurldecode, nl2br, serialize, unserialize, getenv, strip_tags), which extension adds which, the risks, and examples rendered on a development installation.
- [Text operators](text-operators.md): concat and indent from eZTemplateTextOperator - joining values into one string and indenting multi-line text with spaces, tabs or a custom filler, the object trap of concat in compiled templates, with examples rendered on a development installation.
- [Locale operators](locale-operators.md): The 7 operators of eZTemplateLocaleOperator - l10n with all nine formatting types, datetime with the datetime.ini classes and custom formats, locale, currentdate, maketime, makedate and gettime - with examples rendered on a development installation and the compiled gettime difference.
- [Attribute and dump operators](attribute-and-dump-operators.md): attribute and dump from eZTemplateAttributeOperator - listing what an object, a hash or an array offers to templates, with or without values, to a depth, as an HTML table or as text, and why their output must never reach a public page; examples rendered on a development installation.
- [Digest operators](digest-operators.md): crc32, md5 and rot13 from eZTemplateDigestOperator, plus sha1, which the class offers but which only works after another digest operator has loaded it; what they are for in templates and what not, with examples rendered on a development installation.
- [The si unit operator](unit-operator.md): si from eZTemplateUnitOperator - formatting a number with a unit and a decimal or binary prefix (file sizes, lengths, weights) from units.ini, with every parameter, the auto prefix rule and examples rendered on a development installation.
- [The fetch and fetch_alias operators](execute-operators.md): fetch and fetch_alias from eZTemplateExecuteOperator - how a template reaches a module's function_definition.php through eZFunctionHandler and eZModuleFunctionInfo, parameters, results and failures, what the compiler does with a constant fetch, with examples rendered on a development installation.
- [Role operators](role-operators.md): The 7 operators of eZTemplateRoleOperator (has_role, has_role_id, has_any_role, has_role_by_user, has_role_by_user_id, has_policy, has_policy_by_user) - what they check, why the piped input is ignored, why they are not an access check, with read-only examples rendered on a development installation as the anonymous user.
- [Image operators](image-operators.md): texttoimage, image and imagefile from eZTemplateImageOperator - rendering text as an image with a TrueType font from texttoimage.ini, composing layers, and reading an image file's size - which ones write files under var/cache/texttoimage, and examples from a development installation.
- [The expinfo operator](expinfo-operator.md): expinfo from eZTemplateExpInfoOperator - the active extensions, all available extensions, one extension's metadata from extension.xml and ezinfo.php, and the kernel information, which keys each mode returns, and which kernel values must never be printed on a public page; examples rendered on a development installation.

## Kernel operators

- [URL, design, INI and HTTP operators](url-operators.md): The ten operators of the kernel's eZURLOperator (ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable), how the ezurlmultisite extension takes them over on a development installation, and its own ezurl_path, ezurl_siteaccess and ezurl_roots, each with proven examples.
- [Translation operators i18n, d18n and x18n](i18n-operators.md): The three translation operators of eZi18nOperator, their parameters (context, comment, arguments, extension), how ezpI18n::tr() finds a translation and inserts arguments, how compiled templates translate at compile time, and why d18n exists, with examples rendered in English and German on a development installation.
- [Icon operators](icon-operators.md): The eight operators of eZWordToImageOperator - class_icon, classgroup_icon, mimetype_icon, action_icon, icon, flag_icon, icon_info and wordtoimage - with their positional parameters, the icon.ini theme and size lookup, and examples rendered on a development installation.
- [Menu and tree operators](menu-and-tree-operators.md): The kernel operators that build navigation - treemenu (a menu that unfolds along the current path), content_structure_tree (a nested tree of nodes) and topmenu (the admin tabs from menu.ini) - with every parameter, the shape of what they return, their costs, and examples rendered on a development installation.
- [Text helper operators of the kernel](kernel-text-helper-operators.md): Five small kernel operators - alphabet, autolink, simpletags, eztoc and month_overview - with their parameters, the settings they read, what they escape and what they do not, and examples rendered on a development installation.
- [Preference, module, package and PDF operators](kernel-misc-operators.md): The kernel operators ezpreference, ezmodule, module_params and ezpackage and the library operator pdf - what each reads, its parameters, what it returns, which of them have side effects - with examples rendered on a development installation.
- [Siteaccess operators](siteaccess-operators.md): The kernel operators that know the siteaccesses of an installation - siteaccess_url, siteaccess_menu, siteaccess_menu_key and language_switcher - with their parameters, the settings behind them, what they return, how they compile, and examples rendered on a development installation with three different siteaccesses.
- [Installation, environment, audit and CSP operators](exponential-kernel-operators.md): Seven operators the Exponential kernel added - installation_name, is_production_system, exp_environment, exp_environment_badge, audit_label, csp_nonce and view_parameter - with the settings they read, what they return, the caching rule for csp_nonce, and examples rendered on a development installation.

## Extension operators

- [ezjscore operators in depth](ezjscore-operators.md): Every operator of the ezjscore extension - the packer operators ezscript, ezscript_require, ezscript_load, ezscriptfiles, ezcss, ezcss_require, ezcss_load, ezcssfiles, the encoders json_encode, xml_encode, node_encode and has_access_to_limitation - with all parameters, the persistent-variable mechanism behind require and load, pack levels, and examples rendered on a development installation.
- [ezwebin operators](ezwebin-operators.md): The six operators of the ezwebin extension - ezpagedata, ezpagedata_set, ezpagedata_append, ezarchive, ezkeywordlist and eztagcloud - with their parameters, what ezpagedata computes for a pagelayout, the database engines ezarchive supports, and examples rendered on a development installation.
- [eztags and star rating operators](eztags-and-starrating-operators.md): The five operators of the eztags extension (eztagscloud, latest_tags, eztags_parent_string, tag_icon, user_limitations) and the three of ezstarrating (fetch_starrating_stats, fetch_starrating_data, fetch_by_starrating), with parameters, return shapes, the Solr path of the tag cloud, a crash to avoid, and examples rendered on a development installation.
- [The swark operators](swark-operators.md): How the swark extension registers its 37 one-class-per-operator template operators, the SwarkOperator base class with name=default parameter strings and compiled execute() calls, which of its names other extensions or the kernel take over on a development installation, and a proven case where the owner of an operator changes in the middle of a request.
- [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md): Every array, string and URL operator of the swark extension - sort, rsort, asort, arsort, ksort, krsort, shuffle, array_search, range, set_array_element, remove_array_element, split_by_length, shortenw, preg_match, preg_replace, strpos, strrpos, substr, ltrim, rtrim, str_replace, json_encode, uri_path_segment, add_view_parameters, modify_view_parameter - with parameters, return values and examples rendered on a development installation.
- [Swark operators: request and system](swark-operators-request-and-system.md): The request and system operators of the swark extension - charset, current_siteaccess, current_layout, is_post_request, server, cookie, user_id_by_login, variable_names, debug, debug_attributes, redirect, return and clear_object_cache - with parameters, return values, which ones end the request or change state, and examples rendered on a development installation.
- [Operators of smaller extensions](small-extension-operators.md): The template operators of ezoe, ezwt, enhancedezbinaryfile, recaptcha, xrowmetadata and bcwebsitestatistics - ezoe_ini_section, ezcreateclasslistgroups, parsexml, recaptcha_get_html, metadata, the six xrowmetadata_license operators and the six bc_ga operators - with parameters, return values, pitfalls found on a development installation (operator names with capital letters, a die() on a missing key) and examples rendered on a development installation.
- [Newsletter operators of cjw_newsletter](newsletter-operators.md): The thirteen template operators of the cjw_newsletter extension - three general ones (cjw_newsletter_str_replace, cjw_newsletter_preg_replace, cjw_newsletter_variable) and ten rendering operators (conditions, plain text and rich text conversion, interests block, absolute URLs, underline, wrap, rendering data) - with parameters, return values, when the condition markers appear, and examples rendered on a development installation.
- [Layouts, expui and expai operators](layouts-and-expui-operators.md): The template operators of the Exponential layout extensions (expl_parse, expl_strip, expl_has, expl_first, explayouts_ui_has_template), of expui (exp_config) and of expai (expai_money, expai_tokens, expai_number, expai_retention, expai_json), with parameters, return values, escaping, and examples rendered on a development installation, the expai ones on the admin siteaccess.

## Writing, registering and overriding operators

- [Registration order and overrides](registration-order-and-overrides.md): How the template engine decides which class serves an operator name - the order it reads the eztemplateautoload.php files, "last registration wins", the 21 names two classes register on a development installation, and the lazy loading rule that lets a class take a name back in the middle of a request - proven with examples rendered on a development installation.
- [Overriding kernel operators](overriding-kernel-operators.md): Changing what a kernel or template-library operator does from an extension, the way extension ezurlmultisite does for ezurl - subclass the class that owns the name, register the subclass for all of its names, drop the compile-time code for what you change - with each mistake and the working pattern rendered on a development installation in interpreted and compiled mode.
- [Registering operators](registering-operators.md): Every way a template operator gets registered - eztemplateautoload.php in AutoloadPathList and ExtensionAutoloadPath, the entry keys, template.ini PHPOperatorList, owsimpleoperator.ini - how extension order is decided (ActiveExtensions, ActiveAccessExtensions, ExtensionOrdering and extension.xml dependencies), and what to clear afterwards.
- [Creating an operator, step by step](creating-an-operator-step-by-step.md): The whole path from an idea to a working template operator in an extension - choose a free name, write the class, register it, clear caches, try it interpreted and compiled, deploy - or let the template extension wizard write it; each step checked against a development installation.
- [The operator class interface](operator-class-reference.md): Every method and argument the template engine uses on a template operator object - operatorList, namedParameterPerOperator, namedParameterList, modify with its eight arguments, operatorTemplateHints and element transformations, operatorCompiledStaticData - and the registration entry keys, each shown with a sample class rendered on a development installation in interpreted and compiled mode.

## Fetch aliases

- [Fetch aliases](fetch-aliases.md): What fetch_alias() does, the fetchalias.ini syntax (Module, FunctionName, Parameter[], Constant[]), every alias defined on a development installation with what it maps to and an example rendered on a development installation, how parameters and constants combine, and how to add your own or change an existing one from an extension.

## Fetch functions

- [Fetch functions: how they work](fetch-functions-how-they-work.md): What happens when a template calls fetch() - eZFunctionHandler, eZModuleFunctionInfo, the module's function_definition.php and its function collection class - the definition format, results and errors, the module repository search order, and how an extension adds a fetch module, extends a kernel module's fetch functions or replaces them, each rendered on a development installation.

## OW Simple Operator

- [OW Simple Operator - PHP functions as template operators](owsimpleoperator.md): What the owsimpleoperator extension does, how its operators are registered from owsimpleoperator.ini, the five PHP functions it offers on this installation (time, mktime, getdate, str_replace, str_rot13), and how to call them, with every example rendered on a development installation.
- [Your own operators with OW Simple Operator](owsimpleoperator-your-own-operators.md): Writing template operators as plain public methods of a class that extends OWSimpleOperator, registering each method in owsimpleoperator.ini, optional arguments, the pipe form, and what happens when the class is registered the usual way instead; every example rendered on a development installation.
- [OW Simple Operator: limitations and risks](owsimpleoperator-limitations.md): What OW Simple Operator does not do and where it bites - the pipe form that drops an argument, null arguments that are left out, no parameter checks, no compile-time optimization, name clashes, caching and the security rule for permitted PHP functions - with the failures rendered on a development installation.

## Related pages

- [The template language](../template-language/README.md)
- [Operators overview for designers](../designers/templates/operators-overview.md)
- [Writing template operators](../creators/app-creators/template-operators.md) (application creators)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
