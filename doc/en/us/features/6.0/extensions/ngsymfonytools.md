# ngsymfonytools: Twig and Symfony from legacy templates

This page is for developers of a site in transition from legacy templates to Symfony. `ngsymfonytools` (Netgen
Symfony Tools) lets **legacy templates** include **Twig templates** and run **Symfony sub-requests**, so legacy pages
can reuse Symfony or Ibexa-side pieces.

It only works in a [legacy bridge](../legacy-bridge.md) setup, where the legacy kernel runs inside a Symfony
application.

## Operators

| Operator | What it does |
|---|---|
| `symfony_include` | Includes a Twig template with parameters. |
| `symfony_path`, `symfony_url` | A relative or absolute URL for a route, with the arguments of Twig's `path` and `url`. |
| `symfony_render` | Renders a route as a sub-request. |

`doc/USAGE.md` of the extension describes all of them.

## Example

```
{symfony_include(
    'NetgenTestBundle:Test:test.html.twig',
    hash(
        'theAnswer', 42,
        'homepage', fetch( 'content', 'node', hash( 'node_id', 2 ) )
    )
)}
```

Parameters that are `eZContentObject` or `eZContentObjectTreeNode` are converted to the API `Content` and `Location`
objects before they reach the Twig template.

## What changed in the fork (March to April 2026)

- The package name and license in `composer.json` are the se7enxweb ones, with a `replace` section so it overrides
  cleanly.
- `symfony_include` asked the container for the removed `templating` service (gone since Symfony 5), which threw
  `ServiceNotFoundException`. It now uses the `twig` service. A workaround with `Twig\Environment::class` was tried and
  reverted once the legacy bridge (v5.0.9) made the `twig` service public.
- A `class_alias` shim loads the API content converter on both the old `eZ\Publish\API\Repository\Repository`
  interface and Ibexa DXP 5.0's `Ibexa\Contracts\Core\Repository\Repository`.

## Related pages

- [Legacy bridge](../legacy-bridge.md)
- [Chronicle of the repository](../../../history/ecosystem/ngsymfonytools.md) and [release notes](../../../changelogs/extensions/ngsymfonytools.md) (covered with the platform repositories)
- [Change ledger](../../../history/ledger/ngsymfonytools.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
