---
title: "expservices: remote services for extensions"
audience: developer, app-creator
category: extension-development
order: 370
summary: What expservices offers extension authors - services over ezjscore with declared access, paging and one envelope, a catalogue, API tokens - and how to write a service of your own.
keywords: expservices, expServiceBase, services, ezjscore, catalogue, envelope, API token, X-Exp-Token, access, paging, write, guard, ok, page, remote access
type: help
---
# expservices: remote services for extensions

Part 8, reference 33. `expservices` is an extension shipped with the kernel that offers the installation's functions
as services over ezjscore: one class per domain (system, caches, settings, users, content ...), called as
`<root>/ezjscore/call/exp<domain>::<method>`, answering in one JSON envelope. Apps, scripts and other servers use it;
an extension can add its own domain. The full documentation is
[Backend services over ezjscore](../../../bc/6.0/backend_ezjscore_services.md).

## What you get over a plain server function

| | Plain ezjscore server function | expservices service |
|---|---|---|
| Declaration | a public static method | the method plus an entry in `static::$services` (summary, access, write, args, returns) |
| Access | checked by your code | declared, checked by `guard()` |
| Writes | your code checks POST and the token | `'write' => true`: POST and form token required, audited |
| Errors | `error_text` | `{ "ok": false, "error": { "code": 403, "message": ... } }` with HTTP-style codes |
| Lists | your format | paged: `data` plus `meta` (`total`, `offset`, `limit`, `count`, `has_more`) |
| Discovery | none | `expservices::catalog` lists every service with its declaration |
| Non-browser clients | the session cookie | also personal API tokens (header `X-Exp-Token`) |

## Writing a service

From the documentation's own example, with your domain:

```php
<?php
class expMyExtServices extends expServiceBase
{
    public static $services = array(
        'itemName' => array( 'summary' => 'The name of one item',
            'access' => array( 'myext', 'read' ), 'write' => false,
            'args' => array( 'item_id' => 'int' ), 'returns' => 'id, name' ),
    );

    public static function itemName( $args )
    {
        self::guard( __FUNCTION__ );
        $item = myExtItem::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$item )
        {
            throw new expServiceException( 'No such item', 404 );
        }
        return self::ok( array( 'id' => (int)$item->attribute( 'id' ), 'name' => $item->attribute( 'name' ) ) );
    }
}
```

Register it in your extension's `settings/ezjscore.ini.append.php`:

```ini
[ezjscServer_expmyext]
Class=expMyExtServices
```

and regenerate the autoloads. Call it as `<root>/ezjscore/call/expmyext::itemName::42`.

| Helper (protected static) | Use |
|---|---|
| `guard( $method )` | check the declared access, POST and token for writes |
| `arg( $args, $i, $type, $default = null )` | a typed URL argument |
| `post( $name, $type, $default = null )` | a typed POST field |
| `ok( $data, array $meta = array() )` | the success envelope |
| `page( array $items, $total, $offset, $limit )`, `pageOf( ... )` | a paged list |

`expServiceException` codes: 400 (arguments), 401 (sign in), 403 (denied), 404 (not found), 409 (conflict), 422
(invalid).

## Security

- Every service declares its access; `'public'` only for what anonymous visitors may know.
- Writes are POST with the form token, or an API token, and are recorded in the audit trail.
- The ezjscore policy `ezjscore/call` still applies ([ezjscore server functions reference](ezjscore-server-functions-reference.md)).
- Never return passwords, tokens or personal data a caller does not need.

**Demo mode.** In demo mode (`EXP_DEMO_MODE=1`) the password methods refuse administrators: `requireAcceptablePassword()` throws HTTP 403 with `expDemoMode::refusalText()`, and `expaccount.forgotRequest` makes no key for an administrator. A service of your own that changes passwords should ask `expDemoMode::protectsPassword()` first. [Demo mode for developers](demo-mode-for-developers.md).

## Related pages

- Previous: [The help system for extensions](help-system-for-extensions.md)
- Next: [Glossary for extension developers](glossary-for-extension-developers.md)
- [Backend services over ezjscore](../../../bc/6.0/backend_ezjscore_services.md)
- [API keys](../../../bc/6.0/api-keys.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
