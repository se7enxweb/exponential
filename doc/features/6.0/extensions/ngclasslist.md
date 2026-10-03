# ngclasslist: a datatype that stores a list of content classes

This page is for site builders who let editors choose which kinds of content a page lists. `ngclasslist` (Netgen
Class List Datatype) lets an editor select a list of content classes and stores it on an attribute.

## Example: a category that lists chosen classes

1. Add an attribute of type `ngclasslist` to the Category class, here with the identifier `class_filter_array`.
2. In the category, select the classes to list.
3. Fetch the children in the category's template with the selected classes as filter:

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

## What changed

| Version | Date | Change |
|---|---|---|
| 1.0.1, 1.1.1 | 31 August to 2 September 2026 | Package name and license in `composer.json` (se7enxweb); the installer is the se7enxweb installer; description updated; `ezinfo.php` version. |
| 1.1.2 | 27 September 2026 | The extension states its version, license and website. |
| 1.1.3 | 2 October 2026 | English and German translations for every string the admin showed untranslated. |

## Related pages

- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/ngclasslist.md) and [release notes](../../../changelogs/extensions/ngclasslist.md)
- [Change ledger](../../../history/ledger/ngclasslist.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-08](../../../history/extensions/months/2026-08.md), [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
