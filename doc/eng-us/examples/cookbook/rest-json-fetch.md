---
title: REST and JSON calls
audience: developer
category: examples
order: 90
summary: Read content as JSON three ways — an ezjscore server function of your own called from a CSP-safe design script, the bundled expservices catalogue with a personal API token, and the REST API below /api/ — with the policies each checks.
keywords: REST, JSON, ezjscore, server function, ezjscServerFunctions, ezjscore.ini, ezjscServer, FunctionList, RequirePolicy, ezjscore/call, fetch, JavaScript, CSP, expservices, API token, X-CSRF-Token, ezxform_token, index_rest.php, ezprestapi, /api/ezp/v2
type: example
---
# REST and JSON calls

Three ways to read Exponential over HTTP, from the smallest to the most complete:

| Way | Address | Best for |
|---|---|---|
| An **ezjscore server function** of your own | `/ezjscore/call/<name>::<function>::<argument>` | one answer a page of your design needs, called by its own script |
| The bundled **expservices** catalogue | `/ezjscore/call/exp<domain>::<method>` | scripts and apps that need much of the administration, with personal API tokens |
| The **REST API** | `/api/ezp/v1/...`, `/api/ezp/v2/...` | content clients (mobile apps) that read nodes, objects and fields; needs the extension `ezprestapi` |

Prerequisites: the extension of [An extension skeleton](extension-skeleton.md), switched on; `ezjscore` is bundled and
active by default.

## 1. A server function of your own

You add `expexample::count::<node id>`: the number of children of a node the caller may read.

### The class

`extension/expexample/classes/expexampleserverfunctions.php` (found through the autoload array):

```php
<?php
/**
 * The ezjscore server functions of expexample: /ezjscore/call/expexample::count::<node id>.
 * Registered in ezjscore.ini [ezjscServer_expexample]; found through the autoload array.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

class expExampleServerFunctions extends ezjscServerFunctions
{
    /**
     * The number of children of a node the current user may read.
     *
     * @param array $args the arguments after the function name: array( <node id> )
     * @return int
     * @throws InvalidArgumentException for a node that does not exist or may not be read
     */
    public static function count( $args )
    {
        $nodeID = isset( $args[0] ) ? (int)$args[0] : 0;
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$node instanceof eZContentObjectTreeNode || !$node->canRead() )
            throw new InvalidArgumentException( 'Node not found' );
        return (int)$node->subTreeCount( array( 'Depth' => 1, 'DepthOperator' => 'eq' ) );
    }
}
```

`ezjscore/call` checks no policy of its own (it is in `site.ini [RoleSettings] PolicyOmitList[]`): every function
guards itself. The router refuses a class without an `[ezjscServer_<name>]` block, and checks `Functions[]` against
the user's `ezjscore/call` policy. The function above also checks `canRead()`, so it never tells a visitor about a
node the visitor may not see. An exception becomes the answer's `error_text`.

### The registration

`extension/expexample/settings/ezjscore.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[ezjscServer]
# The permission function list of ezjscore/call: a role grants ezjscore/call with the limitation
# "FunctionList: expexample"
FunctionList[]=expexample

[ezjscServer_expexample]
Class=expExampleServerFunctions
# The user needs ezjscore/call with FunctionList expexample
Functions[]=expexample
# Refuse a request whose calls are all refused with 403 Forbidden
RequirePolicy=enabled

*/ ?>
```

Grant it: **Users > Roles and policies**, a role of the users (the *Anonymous* role for visitors), **New policy**,
module *ezjscore*, function *call*, limitation *FunctionList*: `expexample`.

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
curl -s -H 'Accept: application/json' https://example.com/ezjscore/call/expexample::count::2
```

Expected: `{"error_text":"","content":<number>}`. Without the policy, `403` (with `RequirePolicy=enabled`).
`?ContentType=json` in the address does the same as the `Accept` header, which helps in a browser.

### Calling it from a page, within a Content Security Policy

The administration enforces a Content Security Policy (`script-src 'self'` and a nonce), and a site may too: no inline
`<script>`, no `onclick=""`. The template only marks where the answer goes, with the address in a `data-` attribute:

`extension/expexample/design/standard/templates/expexample/count.tpl`:

```tpl
{* Include where the number of children of $node should appear; expexample.js fills it in *}
<span class="expexample-count"
      data-expexample-url={concat( 'ezjscore/call/expexample::count::', $node.node_id )|ezurl}>…</span>
```

`ezurl` adds the siteaccess path and the quotes. The script is a file of the design,
`extension/expexample/design/standard/javascript/expexample.js`:

```js
/*
 * expexample.js: fills every element with a data-expexample-url attribute with the answer of that ezjscore
 * server function call. No inline script: the file is loaded by the pagelayout from design.ini
 * [JavaScriptSettings] FrontendJavaScriptList[], so a Content Security Policy with script-src 'self' allows it.
 */
(function () {
    'use strict';

    function callServer(url) {
        // ezjscore/call answers JSON when the request asks for it: {"error_text": "", "content": ...}
        return fetch(url, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        }).then(function (answer) {
            if (answer.error_text) {
                throw new Error(answer.error_text);
            }
            return answer.content;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-expexample-url]').forEach(function (element) {
            callServer(element.getAttribute('data-expexample-url')).then(function (content) {
                element.textContent = String(content);
            }).catch(function () {
                element.textContent = '?';
            });
        });
    });
}());
```

It is loaded through `design.ini [JavaScriptSettings] FrontendJavaScriptList[]=expexample.js` (see
[An extension skeleton](extension-skeleton.md)), which the pagelayout of a site design packs with `ezscript_load`; in an
administration design use `BackendJavaScriptList[]`. A design that does not read the list loads the file with
`{ezscript_require( 'expexample.js' )}` in its pagelayout.

**Writes.** A function that changes something takes POST only and must check the form token: send it as the header
`X-CSRF-Token` (or the field `ezxform_token`); the extension `ezformtoken` refuses a POST of a signed-in user without
it. Read the token from the page (the pagelayout of the administration carries it) or from `expsession::token`.

## 2. The expservices catalogue

`expservices` (bundled) exposes most of the administration as ezjscore server functions with one answer format,
`{"ok": true, "data": ..., "meta": ...}`, paging and per-service access checks. Discover what exists, then call it:

```bash
curl -s 'https://example.com/ezjscore/call/expservices::domains?ContentType=json'
curl -s 'https://example.com/ezjscore/call/expservices::catalog::system?ContentType=json'
```

Scripts sign in with a personal API token (create it once while signed in, with `expsession::tokenCreate`) and send
it as `Authorization: Bearer expt_...` or, where a proxy drops that header, `X-Exp-Token: expt_...`:

```bash
export EXP_TOKEN='expt_...'
curl -s -H "X-Exp-Token: $EXP_TOKEN" 'https://example.com/ezjscore/call/expsystem::version?ContentType=json'
```

A request with a token needs POST for writes but no form token. A service of your own extends `expServiceBase` and
declares itself in `public static $services`; see
[Backend services over ezjscore](../../bc/6.0/backend_ezjscore_services.md#writing-a-service).

## 3. The REST API

`/api/` is routed to `index_rest.php` by every shipped web server configuration. The content routes come from the
extension `ezprestapi` (installed separately, `ActiveExtensions[]=ezprestapi`): reads at `v1` and `v2`, writes at `v2`,
every call checked against the user's `content/*` policies.

```bash
export EXP_API_KEY='expk_...'     # a personal API key (My account > API access)
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/2
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v1/content/node/2/list/offset/0/limit/20?ResponseGroups=Fields"
```

Routes, authentication (OAuth2 or personal API keys) and configuration: [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md).

## Check it

Checked for this page (2026-10-18): `php -l` on the server function class and the settings file;
`bin/php/eztemplatecheck.php` reports `count.tpl` valid (syntax). The answer format, the policy check and
`RequirePolicy` are those of `extension/ezjscore/classes/runnable/views/ezjscore/call.php` and
`extension/ezjscore/settings/ezjscore.ini`; the form token header is the one `extension/ezformtoken/event/ezxformtoken.php`
reads. The calls were not run against a site for this page.

## Common mistakes

| Symptom | Cause |
|---|---|
| `Not a valid ezjscServerRouter argument` | no `[ezjscServer_<name>]` block, a typo in the name, or the INI cache |
| The answer is HTML, not JSON | no `Accept: application/json` header and no `?ContentType=json` |
| `403` for every call | the role lacks `ezjscore/call` with the `FunctionList` limitation of the function |
| The script never runs in the administration | an inline `<script>` or `on...=` attribute, refused by the Content Security Policy; load a file |
| `Authorization` never reaches PHP | Apache with PHP-FPM drops it unless configured (`CGIPassAuth On`); use `X-Exp-Token` for expservices |

## Derived from

- `extension/ezjscore/settings/ezjscore.ini` (`[ezjscServer]`, `[ezjscServer_<name>]`, `RequirePolicy`)
- `extension/ezjscore/classes/runnable/views/ezjscore/call.php`, `extension/ezjscore/classes/ezjscajaxcontent.php`
- `extension/ezjscore/classes/ezjscserverfunctionsjs.php`, `extension/ezjscore/settings/site.ini.append.php`
- `extension/ezformtoken/event/ezxformtoken.php`; `doc/eng-us/bc/6.0/backend_ezjscore_services.md`;
  `doc/eng-us/guides/rest-api-v1-and-v2.md`

## Related pages

- [Cookbook](README.md)
- [Remote services: the whole admin as an HTTP API](../../features/6.0/remote-services-expservices.md)
- [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md), [Personal API keys](../../guides/api-keys.md)
- [Content Security Policy](../../features/6.0/content-security-policy.md)
- [Fetch functions](../fetch-functions/README.md) (the same data inside templates) and [policy functions](../policies/README.md)
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
