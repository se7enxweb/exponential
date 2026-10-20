---
title: The template language
audience: developer, designer, app-creator
category: development
order: 0
summary: The Exponential template language (eZ TPL, ezpl) in depth - what it is and where it came from, its syntax side by side with Smarty, values, variables and namespaces, control structures, the object model of nodes, objects and attributes, module view templates, the compiler and caches, and how to extend it - with every example rendered on alpha.
keywords: template language, eZ TPL, ezpl, tpl, Smarty, syntax, object model, module result, compiler, cache, template functions, operators
type: help
---
# The template language

Exponential renders every page, mail and PDF with its own template language, known as eZ TPL or ezpl: brace tags
like Smarty's, but with operators and functions written for content objects, a fetch API to read data, an
override system to choose templates, and a compiler that turns templates into PHP. This section documents the
language itself, from the code in `lib/eztemplate/` and the kernel, in 28 pages.

Every example on these pages was rendered on alpha, the project's own installation, against its real content
(the Fit & Healthy site), with the output shown under it exactly as the template engine produced it; examples were
also compiled into a scratch folder and compared. Where a page shows code that was not run, it says so and why.

For a gentler, task-oriented introduction, start with the designers' [templates overview](../designers/templates/templates-overview.md)
and [TPL syntax basics](../designers/templates/tpl-syntax-basics.md). Every operator is documented in
[Template operators](../template-operators/README.md).

## The language

- [What the template language is](what-the-template-language-is.md): The Exponential template language (eZ TPL, ezpl) in one page - a Smarty-like language for .tpl files, where it is used (designs, module views, mail, PDF), the parts of the engine in lib/eztemplate, and what it does beyond Smarty out of the box.
- [History of the template language](history.md): How the template language grew, told only from what the repository records - the git history from 2002, the changelogs from 2.9, the compiler of 2003, the def/if/foreach syntax of 2005 (version 3.6) that replaced let and section, and what still remains of the older style.
- [Smarty and the template language side by side](smarty-side-by-side.md): A side-by-side table of Smarty syntax and the Exponential template language - output, modifiers and operators, assignment, defaults, conditions, loops, cycling, include, literal, delimiters, comments, capture, escaping, dates, caching and inheritance - with every template-language example rendered on alpha.
- [Smarty and the template language: the differences in depth](smarty-differences-in-depth.md): The differences that trip up Smarty users, each shown on alpha - no infix arithmetic or comparison, no PHP in templates, no automatic escaping, unknown operators that pass their input through, strict def and set, braces in CSS and JavaScript, operators as registered PHP classes, fetch and objects, overrides instead of inheritance, namespaces and whitespace.
- [Moving a template from Smarty](moving-from-smarty.md): A porting guide - a typical Smarty list template translated to the template language in three steps (variables, data from fetch, the loop with conditions, links, dates and escaping), each step rendered on alpha, plus a checklist of the replacements to make.
- [Lexical structure](lexical-structure.md): How the parser reads a template - the delimiters, the kinds of tags (output tags, function tags, end tags, single tags), comments, literal blocks, ldelim and rdelim, escaping a brace inside a tag, the rule that removes one newline around each function tag, and how parser errors report line and column.
- [Values and literals](values-and-literals.md): The values a template can write down - strings in single or double quotes (no variable interpolation, the escapes the parser knows), integers and floats, true() and false(), arrays and hashes built with array() and hash(), bare identifiers, and what stands in for null - each shown on alpha with get_type.
- [Variables in depth](variables-in-depth.md): The exact rules of def, undef, set, let and default from eZTemplateDefFunction and eZTemplateSetFunction - the warnings for defining twice, setting an unknown variable and undefining one that def did not make, undef without arguments, the lifetime of a variable (the file, the let block), and set with scope=relative, root or global across an include.
- [Namespaces](namespaces.md): How the template engine keeps variables apart - the root and current namespace, the four ways to name a variable ($var, $ns:var, $:var, $#var), what include does with and without name=, the namespaces section and let create, and the error for a variable in the wrong namespace.
- [Attribute access](attribute-access.md): How $a.b and $a[b] are resolved - arrays by key, objects through attributes(), hasAttribute() and attribute(), eZPersistentObject fields and function attributes, dynamic keys, numeric indexes, attributes after an operator, the error with its list of choices, and the attribute and dump operators for looking inside a value - shown on node 90 of alpha.
- [Operators in expressions](operators-in-expressions.md): How operators are written and evaluated - the pipe form and the call form, what the input value is in each, chaining, nesting operators as parameters, attributes after an operator, operators inside function parameters, how parameters are matched to named parameters with defaults, and what happens with an unknown operator or a missing parameter.
- [Functions versus operators](functions-vs-operators.md): The two kinds of named things in the template language compared - template functions are tags that write output and control the template, operators compute values - with their registration arrays, the methods each PHP class implements, block functions and children, interpreted processing versus compiled transformation, and when to write which.
- [Control structures reference](control-structures-reference.md): Every parameter of if, elseif and else, switch and case (match, in, key and the default case), foreach (key, sequence, offset, max, reverse), for, while and do, and the loop helpers delimiter (with modulo), break, continue and skip - with their exact behavior from the code, each shown on alpha.
- [Sections versus foreach](sections-vs-foreach.md): The section function in full - loop over arrays, numbers and strings, name and the namespace it creates, var and the iterator with last-value, sequence, offset, max, reverse, show with section-else, the section-exclude and section-include filters and delimiter with modulo - each next to its foreach, for or if equivalent, all rendered on alpha, plus the one compiled-mode difference found.

## The object model in templates

- [The object model in templates](object-model.md): How a template sees the PHP objects of the content model - node, content object, version, data map, content object attribute and its datatype content - and the attribute protocol (attributes, hasAttribute, attribute) the engine uses for every dot in a variable path, proven on real content.
- [Node attributes reference](node-attributes-reference.md): Every attribute a template can read from a node (eZContentObjectTreeNode) - the 16 database fields and the 35 function attributes of its definition, the PHP method behind each, and the value each returned for a real article on alpha.
- [Content object attributes reference](object-attributes-reference.md): Every attribute a template can read from a content object (eZContentObject) - the 12 database fields and the 65 function attributes of its definition, the PHP method behind each, and the value each returned for a real article on alpha.
- [Content attribute and version reference](content-attribute-reference.md): Every attribute a template can read from a content object attribute (eZContentObjectAttribute, the items of data_map) and from a version (eZContentObjectVersion), with the PHP method behind each, values from alpha, and what .content returns for each datatype found in alpha's content.
- [Data map and content](data-map-and-content.md): Reading the fields of a content object in templates - data_map, has_content, content, the raw data_text and data_int, and attribute_view_gui - proven field by field on a real article on alpha (ezstring, ezxmltext, ezimage, ezobjectrelationlist, eztags, dates).

## Module view templates

- [Module view templates](module-view-templates.md): How a module view renders its templates - the PHP side (eZTemplate::factory(), setVariable(), fetch( 'design:...' )), the $Result array a view returns, and how the kernel hands it to the pagelayout as $module_result together with the other pagelayout variables - proven by running content/view on alpha read-only.
- [Module result reference](module-result-reference.md): Every key of $Result / $module_result found in the kernel - content, path, title_path, content_info and its 25 keys, view_parameters, pagelayout, navigation_part, left_menu, errorType and the rest - with the code that sets each and the values content/view returned on alpha.
- [View parameters](view-parameters.md): The /(name)/value part of a URL - how eZURI parses it, how eZModule::run() maps it to a view's unordered parameters and UserParameters, what content/view turns it into ($view_parameters, one variable per name, content_info.offset), and how it changes the content view cache key - proven on alpha.
- [The persistent variable, inside](persistent-variable-internals.md): How $persistent_variable travels from a node view template to the pagelayout - the kernel sets it to false, reads it back from the root namespace after rendering, stores it in content_info and in the content view cache, and makes it an override key - and why scope='global' matters, proven with namespaces on alpha.

## Compiler, caches and resources

- [The template compiler](template-compiler.md): How eZTemplateCompiler turns a parsed template into PHP - the pipeline (node transformation, static optimizations, combining, PHP generation), the compiled file name and folder, the operator and function hints that let an operator become inline PHP, the processOperator fallback, DevelopmentMode, the settings - with generated code from alpha and one real difference between compiled and interpreted output.
- [Template caches](template-caches.md): Every cache a template touches - compiled templates, the template-block cache of cache-block (keys, expiry, subtree_expiry, ignore_content_expiry and how the cache file name is built), the content view cache and its key, the template override cache, the design base and INI caches - with the ezcache ids that clear each and what exp:velocity deploy clears.
- [Template resources and overrides](resources-and-overrides.md): How a template name becomes a file - the design: and file: resources, the ordered design bases of a siteaccess (site design, additional designs, standard design, each with its extension designs first), override.ini rules with their match keys and order, and how include and the view functions use them - with the real resolution on alpha's site siteaccess.
- [Errors and debugging](errors-and-debugging.md): The messages the template engine logs - unknown variable, unknown attribute, unknown operator or function, parser errors with line and column, a missing template, def/set/undef misuse - each produced on alpha, what the engine outputs in each case, the whitespace rule that surprises everyone, and the debug settings (Debug, ShowXHTMLCode, ShowUsedTemplates, ShowTemplatePathComments, ShowMethodDebug, DisplayWarnings).

## Extending the language

- [Custom template functions](custom-template-functions.md): Writing a template function (a brace tag with named parameters and an optional body) in an extension - functionList, hasChildren, attributeList, process, evaluating parameters, rendering the body, registration in eztemplateautoload.php, compile-time hints - and extending or overriding the kernel's functions, with a sample function rendered on alpha.

## Related pages

- [Template operators](../template-operators/README.md): every operator, fetch aliases, fetch functions
- [Templates for designers](../designers/templates/templates-overview.md)
- [Function reference](../designers/templates/function-reference.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
