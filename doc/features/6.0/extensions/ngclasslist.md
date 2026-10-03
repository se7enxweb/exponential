# ngclasslist: a datatype that stores a list of content classes

`ngclasslist` (Netgen Class List Datatype) lets an editor select a list of content classes and stores it on an attribute. Typical use: a
Category class whose children are fetched with a configurable class filter:

```
{def $children = fetch(
    'content', 'list',
    hash(
        'parent_node_id', $node.data_map.parent_node_id.content,
        'class_filter_type', $node.data_map.class_filter_type.content,
        'class_filter_array', $node.data_map.class_filter_array.content.class_identifiers
    )
)}
```

Add an attribute of type `ngclasslist` (here `class_filter_array`) to the class and use its content like above.

## What changed

* 1.0.1, 1.1.1 (31 August to 2 September 2026): package name and license in `composer.json` (se7enxweb), the installer is the se7enxweb
  installer, description updated, `ezinfo.php` version.
* 1.1.2 (27 September): the extension states its version, license and website.
* 1.1.3 (2 October): English and German translations for every string the admin showed untranslated.

## Related

* [Chronicle](../../../history/extensions/ngclasslist.md) and [release notes](../../../changelogs/extensions/ngclasslist.md)
* [Change ledger](../../../history/ledger/ngclasslist.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
* [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
* [Month: 2026-08 (all extensions)](../../../history/extensions/months/2026-08.md)
* [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
* [Month: 2026-10 (all extensions)](../../../history/extensions/months/2026-10.md)
