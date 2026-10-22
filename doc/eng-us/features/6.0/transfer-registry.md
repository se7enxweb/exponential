# The transfer registry: import and export tools that know each other (6.0.15)

Read this page if you write or maintain an import or export tool (in the kernel or an extension), or a template that
links to one. Users read [Which import or export tool do I need?](../../help/import-export-tools.md).

## In short

| | |
|---|---|
| What it is | A list of every import and export tool of the installation, kept by the kernel service container. Each tool declares itself in `kernelservices.ini`: what it does, which way, on what, in which formats, where it starts, which policy it needs, its help page and its round-trip partners. |
| Who reads it | The "Looking for…?" box on each tool's main page, the content view's **Import/Export** menu (context entries), the class page, the **Import and export** list of the Setup menu and the tools' left menus, the header search's tool suggestions, and the **?** help menu of a tool's pages. |
| What it guarantees | No link to a tool that is not active (its extension is off, its module missing) or that the user may not use (its policy). A new tool appears everywhere by adding one settings block; nothing else is edited. |
| Classes | `expKernelTransferRegistry` (service `transfer.registry`), `expKernelTransferTool` (one per tool, tag `transfer.tool`), `expKernelTransferOperator` (the template operators) |

## Declaring a tool

One block in `settings/kernelservices.ini` (the kernel's tools) or in
`extension/<name>/settings/kernelservices.ini.append.php` (an extension's):

```ini
[Service_transfer.xrowextract]
Class=expKernelTransferTool
Factory=expKernelTransferTool::fromSettings
Arguments[]=transfer.xrowextract
Scope=worker
Tags[]=transfer.tool
Description=Download the items of a class, a folder or the whole site as CSV, XML or JSON, and import them back.
Name=Export (xrowextract)
Direction=both
Scopes[]=site
Scopes[]=subtree
Scopes[]=class
Formats[]=CSV
Formats[]=XML
Formats[]=JSON
EntryURL=xrowextract/csv
Permission=xrowextract/csv|xrowextract/import
HelpPage=ext/xrowextract/README.md
RoundTrip[]=transfer.xrowextract
RoundTrip[]=transfer.sqliimport
Keywords[]=spreadsheet
Keywords[]=Tabelle
Actions[]=class|export|Export the items of this class…|xrowextract/csv/(class)/%classID%
Weight=20
```

| Setting | Meaning |
|---|---|
| `Description` | One sentence: what it is for (also shown by `exp:services:list`) |
| `Name` | Its name in lists |
| `Direction` | `import`, `export` or `both` |
| `Scopes[]` | `site`, `subtree`, `object`, `class`, `data` (records that are not content: submissions, subscribers, logs) |
| `Formats[]` | The file formats it reads or writes |
| `EntryURL` | Its main page; the first part is the tool's module |
| `Permission` | `module/function` needed to use it; alternatives separated by `\|`; without it, any policy of the module |
| `Modules[]` | Further modules whose pages belong to the tool |
| `HelpPage` | A help page id (below `doc/eng-us/`, `ext/<extension>/<path>` for an extension's), `#anchor` allowed |
| `RoundTrip[]` | Ids of tools that read what it writes or write what it reads (either side may name the other) |
| `Keywords[]` | Task words the header search finds it by, in English and German |
| `Actions[]` | `context\|import or export\|label\|URL[\|module/function]`, an entry in a context: `node` (any published item), `container` (an item below which the user may create), `class` (a class page), `setup` (the Setup pages). The URL may hold `%nodeID%`, `%objectID%`, `%classID%`, `%classIdentifier%`; an action whose placeholder the context does not give is left out. The optional permission replaces the tool's. |
| `Weight` | Order in lists (lower first, then by name; default 100) |

Names, descriptions and labels are translated in the context `kernel/transfer` (put the strings in your extension's
translation file). `Enabled=false` in an override switches a tool off, like any service.

After adding or changing a block: `./console exp:services:compile` (the deploy does it), then
`./console exp:services:list --tag=transfer.tool` lists the tools and `exp:services:check` validates them.

## Which tools are shown

`expKernelTransferRegistry::available()` keeps a tool when:

1. its block is read in this siteaccess (an extension that is not active, or an access extension of another
   siteaccess, adds nothing),
2. its module exists (`eZModule::exists()`), and
3. the current user has one of its `Permission` functions (or, without one, any function of its module).

The registry is a worker-scoped service: it holds the tools only. Access is checked on each call.

## Using it

PHP:

```php
$registry = expKernelTransferRegistry::instance();
$registry->available();                                           // id => expKernelTransferTool
$registry->select( array( 'direction' => 'import', 'scope' => 'subtree', 'format' => 'csv' ) );
$registry->actions( 'container', array( '%nodeID%' => 42 ) );     // context entries
$registry->related( 'transfer.xrowextract' );                     // for a "Looking for…?" box
$registry->search( 'import csv' );                                // by task words, best first
```

Templates (operators of the kernel, each returning hashes of the available tools only):

```
{def $import_tools = transfer_tools( hash( 'direction', 'import', 'scope', 'subtree' ) )}
{def $entries = transfer_actions( 'container', hash( '%nodeID%', $node.node_id ), 'import' )}
{def $others = transfer_related( 'transfer.bccie' )}
{def $tool = transfer_tool( 'xrowextract' )}   {* also an inactive one: $tool.active *}
```

A tool hash has `id`, `name`, `description`, `direction`, `scopes`, `formats`, `url`, `module`, `help_page`,
`help_url`, `round_trip`, `keywords` (and `partner` from `transfer_related`, `active` from `transfer_tool`). An action
hash has `tool`, `name`, `context`, `direction`, `label`, `url`.

## The shared template parts

The kernel ships them in `design/standard/templates/transfer/`, so every admin design and every extension finds them:

| Template | Use | Parameters |
|---|---|---|
| `transfer/looking_for.tpl` | The "Looking for…?" box at the top or bottom of a tool's main page | `tool` (its id) |
| `transfer/left_menu.tpl` | The **Import and export** list for a left menu (the Setup menu has it; an extension's left menu includes it after its own links) | `current` (the tool's id, optional) |
| `transfer/actions.tpl` | The entries of a context as a list (the class page) | `context`, `values`, `direction` (optional) |
| `transfer/search_suggestions.tpl` | The tools that match the search words, above the search results | `query` |

They use the shared admin components (`a4-notice`, `leftmenu-items`, `a4-plain`) and no CSS of their own. An extension
includes them like this:

```
{include uri='design:transfer/looking_for.tpl' tool='transfer.bccie'}
```

## Where the kernel uses it

- **Import/Export menu of an item** (`popupmenu/popup_export_items.tpl`): after the package entries and the
  extensions' menu templates, the `node` actions, and on a container the user may create in, the `container` actions,
  and a last entry **Which tool do I need?**.
- **Class page** (`class/view.tpl`): the `class` actions.
- **Setup menu** (`parts/setup/menu.tpl`): the **Import and export** list.
- **Search results** (`content/search.tpl`): up to three tools matching the search words, above the results.
- **Help menu** (`expHelpContextRegistry::menu()`): on a tool's page, the "Which tool" page and the tool's help page
  are among the related topics.

## The kernel's own tools

`transfer.package` (Packages), `transfer.rss_import`, `transfer.rss_export`, `transfer.infocollector` (Collected
information), `transfer.audit` (Audit export), `transfer.pdf` (PDF export). Extensions declare theirs:
`transfer.xrowextract`, `transfer.bccie`, `transfer.sqliimport`, `transfer.syndication`, `transfer.newsletter`
(cjw_newsletter), `transfer.ezodf`.

## Tests

`tests/tests/kernel/classes/expKernelTransferRegistryTest.php` (DB-less, testsuite kernel-classes): the shipped
blocks validate and their help pages exist, parsing, availability, filters, actions, round-trip order, search, the
help menu topics, the operators, and that the help page lists every kernel tool in English and German.
