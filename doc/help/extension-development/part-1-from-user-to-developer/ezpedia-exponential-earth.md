---
title: eZpedia (ezpedia.exponential.earth)
audience: user, developer, designer, app-creator
category: extension-development
order: 130
summary: eZpedia, the free community encyclopedia under the GNU FDL: the articles that matter to extension developers, how old they are, and how to reuse them with credit.
keywords: ezpedia, wiki, encyclopedia, GFDL, learning, solution, snippet, extensions, template override, debugging, datatype, workflow, kernel override, view parameters
type: help
---
# eZpedia (ezpedia.exponential.earth)

Part 1, chapter 13. [eZpedia](https://ezpedia.exponential.earth/) is the free encyclopedia of this code base, written by
its community since 2006. It holds hundreds of articles (722 English articles when this page was written in
October 2026) in namespaces for learning, solutions, code snippets, projects and people. This chapter picks the
articles that matter to extension developers and says how to use them.

## Its license

The site states: "All text is available under the terms of the GNU Free Documentation License". That is the same
family of license as this help (GNU FDL 1.3, see [Free documentation](../../free-documentation.md)), so you may quote
and adapt eZpedia text, as long as you keep it under the GNU FDL and **credit the article** with its title and a link.
In this book we still prefer to link: the articles are worth reading whole, in their own context.

## The namespaces

| Namespace | Address | What is in it |
|---|---|---|
| Learning | [/en/learning](https://ezpedia.exponential.earth/en/learning) | Concepts and how-tos: extensions, templates, designs, debugging, datatypes, workflows |
| Solution | [/en/solution](https://ezpedia.exponential.earth/en/solution) | Concrete problems and their fixes, many with code |
| Snippet | [/en/snippet](https://ezpedia.exponential.earth/en/snippet) | Short pieces of code and template |
| Project | [/en/project](https://ezpedia.exponential.earth/en/project) | Notes on extensions and tools |

## Articles for this book's readers

Titles are quoted as the articles name themselves; they use the product name of their time.

| Topic | Article | Read with |
|---|---|---|
| Extensions | ["Extensions"](https://ezpedia.exponential.earth/en/learning/extensions) | [What an extension is](what-an-extension-is.md) |
| Building in an extension | ["Learning: How to develop eZ publish solutions within an extension"](https://ezpedia.exponential.earth/en/learning/learning_how_to_develop_ez_publish_solutions_within_an_extension) | Part 3 |
| Troubleshooting | ["HOWTO Troubleshoot eZ Publish Extensions"](https://ezpedia.exponential.earth/en/learning/howto_troubleshoot_ez_publish_extensions) | Debugging extensions |
| Design extensions | ["Learning: How to create a design extension"](https://ezpedia.exponential.earth/en/learning/learning_how_to_create_a_design_extension) | Part 6 |
| Override system | ["Template Override System"](https://ezpedia.exponential.earth/en/learning/template_override_system) | [Template overrides: the basics](../part-2-builder/template-overrides-basics.md) |
| Template debugging | ["Learning: Template Debugging"](https://ezpedia.exponential.earth/en/learning/learning_template_debugging) | [Debugging templates](../part-2-builder/debugging-templates.md) |
| Debugging | ["Debugging"](https://ezpedia.exponential.earth/en/learning/debugging) | [Debug output and logs for developers](debug-output-and-logs-for-developers.md) |
| Pagelayout | ["Pagelayout.tpl"](https://ezpedia.exponential.earth/en/learning/pagelayout_tpl) | View results and the pagelayout |
| Node templates | ["Custom Node Templates"](https://ezpedia.exponential.earth/en/learning/custom_node_templates) | [Override conditions](../part-2-builder/override-conditions.md) |
| Datatypes | ["Datatype"](https://ezpedia.exponential.earth/en/learning/datatype) | Datatypes |
| Workflows | ["Workflow"](https://ezpedia.exponential.earth/en/learning/workflow) | Workflow event types |
| Settings | ["Configuration Files"](https://ezpedia.exponential.earth/en/learning/configuration_files) | [How INI settings work](../part-2-builder/how-ini-settings-work.md) |
| Kernel overrides | ["Overriding kernel classes within an extension"](https://ezpedia.exponential.earth/en/solution/overriding_kernel_classes_within_an_extension) | Kernel class overrides: the last resort |
| View parameters | ["How to add view parameter support to your custom module view"](https://ezpedia.exponential.earth/en/solution/how_to_add_view_parameter_support_to_your_custom_module_view) | View parameters |
| Image aliases from PHP | ["Creating image alias image variations in PHP"](https://ezpedia.exponential.earth/en/solution/creating_image_alias_image_variations_in_php) | Part 8 reference |
| Rich text from PHP | ["Manipulating ezxml document contents"](https://ezpedia.exponential.earth/en/solution/manipulating_ezxml_document_contents) | Creating, publishing and changing content from PHP |
| Relations from PHP | ["Adding related object programmatic"](https://ezpedia.exponential.earth/en/solution/adding_related_object_programmatic) | the same |
| PHP API examples | ["PHP eZ Publish API source code examples"](https://ezpedia.exponential.earth/en/snippet/php_ez_publish_api_source_code_examples) | Part 8 |
| Edit and action handlers | ["Content edit handlers"](https://ezpedia.exponential.earth/en/ez/content_edit_handlers), ["Content action handlers"](https://ezpedia.exponential.earth/en/ez/content_action_handlers) | Handlers named by settings |

## How old is it?

Many articles were last changed between 2006 and 2012. The concepts (settings, overrides, the design chain, modules,
datatypes, workflow events) have hardly changed since. What has changed:

| Then | In Exponential 6 |
|---|---|
| PHP 4 and PHP 5 code (`function ClassName()` constructors, `each()`, `create_function()`) | PHP 8.0 to 8.5; see Part 4 |
| `include_once( 'kernel/classes/...' )` lines at the top of files | Autoloading; regenerate with `php bin/php/ezpgenerateautoloads.php -e` |
| Inline `<script>` and `onclick=""` in templates | Forbidden in the administration by its Content Security Policy |
| YUI and older jQuery | jQuery 4 and the `Exp` namespace of expui |
| Clearing caches by deleting folders | `php bin/php/ezcache.php`, `./console exp:cache` |

So: trust the concepts, check the code against the kernel you have (grep for every class and method it uses), and
expect to modernize it.

## Improve it

eZpedia is a wiki; anyone may edit. When an article helped you but its code is out of date, the kindest thing you can
do is add a note with the Exponential 6 way and a link to the page of this book that explains it.

## Related pages

- Previous: [Exponential Projects (projects.exponential.earth)](projects-exponential-earth.md)
- Next: [Learning from the free books](learning-from-the-books.md)
- [Linking versus copying: citing sources fairly](linking-versus-copying.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
