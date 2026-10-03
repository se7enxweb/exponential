# ngsymfonytools: Twig and Symfony from legacy templates

`ngsymfonytools` (Netgen Symfony Tools) is an Exponential extension that lets **legacy templates** include **Twig templates** and run **Symfony
sub-requests** from a template, so a site in transition can reuse Symfony or Ibexa-side pieces in its legacy pages. It provides the `symfony_include`
template operator. It only works in a legacy-bridge setup where the legacy kernel runs inside a Symfony application.

```
{symfony_include(
    'NetgenTestBundle:Test:test.html.twig',
    hash(
        'theAnswer', 42,
        'homepage', fetch( 'content', 'node', hash( 'node_id', 2 ) )
    )
)}
```

Parameters that are `eZContentObject` or `eZContentObjectTreeNode` are converted to the API `Content` and `Location` objects before they reach the
Twig template. The companion operators are `symfony_path` and `symfony_url` (a relative or absolute URL for a route, with the arguments of Twig's `path`
and `url`) and `symfony_render` (render a route as a sub-request). `doc/USAGE.md` of the extension has all of them.

## What changed in the fork (March to April 2026)

* The package name and license in `composer.json` are the se7enxweb ones, with a `replace` section so it overrides cleanly.
* The `symfony_include` operator asked the container for the removed `templating` service (gone since Symfony 5), which threw `ServiceNotFoundException`; it
  uses the `twig` service. A workaround with `Twig\Environment::class` was tried and reverted once the legacy bridge (v5.0.9) made the `twig` service
  public.
* A `class_alias` shim loads the API content converter on both the old `eZ\Publish\API\Repository\Repository` interface and Ibexa DXP 5.0's
  `Ibexa\Contracts\Core\Repository\Repository`.

## Related

* [Chronicle of the repository](../../../history/ecosystem/ngsymfonytools.md) and [release notes](../../../changelogs/extensions/ngsymfonytools.md) (covered with the platform repositories)
