---
title: The ezhttp operator
audience: designer, developer
category: templates
order: 280
summary: Reading request values (GET, POST, session, cookie) in templates with ezhttp and ezhttp_hasvariable, why every such value is untrusted and must be washed, and why view parameters and fetch results are usually the better source.
keywords: ezhttp, ezhttp_hasvariable, get parameter, post parameter, query string, session, cookie, request values, untrusted input, xss, wash
type: help
---
# The ezhttp operator

This page is for designers who need a value from the request in a template, for example a search term to print back.

## Contents

1. [The call](#the-call)
2. [Untrusted input](#untrusted-input)
3. [Better sources](#better-sources)
4. [Caching](#caching)

## The call

```tpl
{if ezhttp_hasvariable( 'SearchText', 'get' )}
    <p>{'You searched for %term'|i18n( 'design/mytheme/search',, hash( '%term', ezhttp( 'SearchText', 'get' )|wash ) )}</p>
{/if}
```

Parameters (`kernel/common/ezurloperator.php`): the variable name and the method `post` (default), `get`, `session` or
`cookie`. Test with `ezhttp_hasvariable` first; reading a missing variable is a warning.

## Untrusted input

Everything in the request is written by the visitor, or by an attacker in a crafted link:

- **always wash** it before printing ([Wash and escaping](wash-and-escaping.md));
- cast numbers: `ezhttp( 'page', 'get' )|int`;
- never use it to build a fetch's class list or sort field without checking it against a list of allowed values;
- never print session or cookie values that might hold anything private.

## Better sources

| You need | Better than ezhttp |
|---|---|
| paging offset, filters in the URL | view parameters: `/(offset)/20` arrives as `$view_parameters.offset` in the view template |
| the current page | `$module_result`, `$node` |
| the signed-in user | `$current_user`, `fetch( 'user', 'current_user' )` |
| search results | the search module's own template variables |

## Caching

A value from the request inside a view-cached template or a cache-block is frozen into the cache. View parameters are
part of the view cache key; arbitrary GET values are not. Keep `ezhttp` output outside cached parts.

## Related pages

- [The ezini operator](ezini-operator.md)
- Search results template
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
