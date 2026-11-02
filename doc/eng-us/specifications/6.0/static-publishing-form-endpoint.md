# Static publishing: the form endpoint

The contract between a form on a static copy and the installation that collects it. Implemented by
`expStaticFormEndpoint`, `expStaticFormToken` and the view `staticpublish/form`; written into the pages by
`expStaticDegrader` at build time. Since Exponential 6.0.15.

Related: [Dynamic features](../../guides/static-publishing-dynamic-features.md),
[The build format](static-publishing-build-format.md)

## The form in the static page

A build with `Forms=endpoint` changes each information collector form so:

```html
<form method="post" action="https://www.example.com/staticpublish/form" data-exp-static="form">
<input type="hidden" name="ExpStaticTarget" value="github-public" />
<input type="hidden" name="ExpStaticToken" value="v1.1793586627.57.jwRn67yebKzrLeQWZ3O-nN3oyhya5v2FJSXIByKh0tE" />
... the form's own fields: ContentObjectID, ContentNodeID, the attributes, ActionCollectInformation ...
</form>
```

The action is the target's live site address (`LiveURL`, or the siteaccess's `SiteURL` over https) followed by the
siteaccess path of the build and `/staticpublish/form`.

## The token

```
v1.<issued>.<object id>.<mac>
mac = base64url( HMAC-SHA256( key, "staticpublish-form/1|<target>|<object id>|<issued>" ) )
key = expMailSecret::derive( "staticpublish-form" )
```

`issued` is the Unix time of the build. A token is valid when the version is `v1`, the object id equals the
`ContentObjectID` of the post, the MAC matches for the posted target, `issued` is at most 5 minutes in the future and
at most `[StaticPublishSettings] FormTokenDays` days (default 400) in the past. The token holds no secret and no
session. Changing the installation's secret ends every token; a new publish writes new ones.

## The request

`POST /staticpublish/form`, `application/x-www-form-urlencoded` or `multipart/form-data`, from a browser on the
static copy. `OPTIONS` is answered for preflights. Every other method is `405` with `Allow: POST, OPTIONS`.

## The checks, in order

| # | Check | Answer when it fails |
| --- | --- | --- |
| 1 | static publishing is enabled and `FormEndpoint=enabled` | 404 |
| 2 | `ExpStaticTarget` names a target with `Forms=endpoint` | 404 |
| 3 | the `Origin` header (or, without one, the origin of the `Referer`) is the origin of the target's `PublicURL`, or `https://`/`http://` of its `CustomDomain` | 403 |
| 4 | `ExpStaticToken` is valid for the target and the `ContentObjectID` | 403 |
| 5 | the client address posted to this target fewer than `FormRateLimit` times (default 5) in `FormRateWindow` seconds (default 600) | 429 with `Retry-After` |

The rate is counted per hash of target and address in `<cache dir>/staticpublish/rate`; the address itself is not
stored.

## The hand-over

The post without `ExpStaticTarget` and `ExpStaticToken`, plus `ActionCollectInformation`, is run through
`content/action` as the current user (the anonymous user for a visitor). The collector's own checks apply: the object
must exist, be readable and have collector attributes; its validation, "one answer per user" rule, e-mail and
redirect settings are those of the live site. The post counts as accepted when that run ends with a redirect.

## The answer

- **JSON**, when the request has `Accept: application/json` or `X-Requested-With`: status 200 (accepted), 422 (not
  accepted by the collector) or the status of the failed check, body `{ "ok": true|false, "message": "..." }`.
- **Otherwise** `303 See Other` to the `Referer` (when it is on the target's origin; else the public address), with
  `#exp-static-form-sent` or `#exp-static-form-failed` appended. A check that fails before the origin is known
  (1 to 3) is answered as JSON.

CORS: `Access-Control-Allow-Origin` is the request's origin once it is checked, never `*`;
`Access-Control-Allow-Credentials` is never sent; `Vary: Origin` always. A preflight from a target's origin gets
`204` with `Access-Control-Allow-Methods: POST, OPTIONS`, `Access-Control-Allow-Headers: Content-Type, Accept,
X-Requested-With` and `Access-Control-Max-Age: 600`. Every answer has `Cache-Control: no-store`,
`X-Content-Type-Options: nosniff` and `X-Robots-Tag: noindex, nofollow`.

## Access and audit

`site.ini [RoleSettings] PolicyOmitList[]=staticpublish/form`: anyone may post; the checks above are the protection.
Each refusal (403, 429) and each handed-over post is the audit event `system.staticpublish.form` with the target, the
object, the result and the reason.

## Switching it off

`[StaticPublishSettings] FormEndpoint=disabled` answers 404 to every request. The next builds replace the forms with
the fallback text and say so in their warnings.
