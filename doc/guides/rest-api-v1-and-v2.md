# REST API v1 and v2: one extension

This guide explains how Exponential 6.0.15 serves its REST content API: version 1 and version 2 come from **one**
extension, `ezprestapi` (1.2.6 or later). It shows which URL answers at which version, how a client authenticates,
how to call the API with curl, which settings drive it, and how to move an installation off the deprecated
`ezprestapiprovider` extension.

It is written for two readers. **Developers** who write clients or extend the API read sections 1 to 5.
**Site administrators** who install, upgrade or check an installation read sections 5 to 8.

Every statement below was checked on the demonstration server (Apache with PHP-FPM, and Velocity) on 15 October 2026.
Host names, identifiers and secrets are replaced by documentation values (`example.com`, `<client id>`, `expk_...`).

[Guides](README.md) · Related: [Personal API keys](api-keys.md) ·
[ezprestapi](../features/6.0/extensions/ezprestapi.md) · [ezprestapiprovider](../features/6.0/extensions/ezprestapiprovider.md) (deprecated) ·
[Remote services and apps](remote-services-and-apps.md)

## In short

- **One extension, one activation:** `ActiveExtensions[]=ezprestapi`. It registers one provider class,
  `ezp7xRestApiProvider`, for the prefixes `ezp` and `ezpl`, with one route table and one controller,
  `ezp7xRestContentController`.
- **Reads answer at v1 and v2** (`/api/ezp/v1/...`, `/api/ezp/v2/...`); **writes** (create, update, delete) answer at
  **v2 only**.
- **Every read and write checks the user's policies** (`content/read`, `content/create`, `content/edit`,
  `content/remove`), whatever the request was authenticated with. The check is the kernel's
  `expRestContentPermission` and needs Exponential 6.0.15.
- **Authentication:** an OAuth2 token from `POST /api/auth/v1/oauth/token` (grants `authorization_code` and
  `refresh_token`), or a [personal API key](api-keys.md) sent as `Authorization: Bearer expk_...`.
- **`ezprestapiprovider` is deprecated.** Its final release, 6.0.4, is a compatibility shim on top of `ezprestapi`.
  Activate `ezprestapi` and remove `ezprestapiprovider` from `ActiveExtensions` ([section 6](#6-migrating-from-ezprestapiprovider)).
- Nothing changes for clients: the v1 and v2 answers are byte for byte the same as before, except that
  `OutputFormat=xhtml` fills `renderedOutput` again.

## Contents

1. [What serves what](#1-what-serves-what)
2. [The routes](#2-the-routes)
3. [Authentication](#3-authentication)
4. [Examples with curl](#4-examples-with-curl)
5. [Configuration](#5-configuration)
6. [Migrating from ezprestapiprovider](#6-migrating-from-ezprestapiprovider)
7. [Activation combinations, before and since](#7-activation-combinations-before-and-since)
8. [Known issue: listAtom](#8-known-issue-listatom)

---

## 1. What serves what

| Part | Where it lives | What it does |
|---|---|---|
| REST framework | the kernel (`kernel/private/rest/`), `settings/rest.ini` | prefix filter, routing, versions, authentication, answer cache, output renderers |
| Auth provider `ezpRestAuthProvider` | the kernel, prefix `auth` | the OAuth2 token endpoint and the authentication routes |
| Content provider `ezp7xRestApiProvider` | `ezprestapi`, prefixes `ezp` and `ezpl` | the content routes of v1 and v2 |
| Content controller `ezp7xRestContentController` | `ezprestapi` | every content read and write, with the permission checks |
| Create and delete backend | [nxc_powercontent](../features/6.0/extensions/nxc_powercontent.md) | creates and removes content for the controller |
| Permission helper `expRestContentPermission` | the kernel (6.0.15) | asks the user's policies the way the content module does |
| `ezprestapiprovider` 6.0.4 | deprecated shim | `ezpRestApiProvider` and `ezpRestContentController` extend the `ezprestapi` classes; maps `ezp` and `ezpl` to `ezp7xRestApiProvider` |

`ezpl` is an alias prefix of `ezp`: the same routes, the same answers.

## 2. The routes

All paths below are relative to `/api/ezp/<version>` (or `/api/ezpl/<version>`).

| Method | Path | v1 | v2 | What it does |
|---|---|---|---|---|
| `GET` | `/content/node/<id>` | yes | yes | one node (`ResponseGroups=Metadata,Locations,Fields`, `OutputFormat=xhtml`) |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | yes | yes | the children |
| `GET` | `/content/node/<id>/childrenCount` | yes | yes | the number of children |
| `GET` | `/content/node/<id>/fields` | yes | yes | every field of the node's object |
| `GET` | `/content/node/<id>/field/<identifier>` | yes | yes | one field |
| `GET` | `/content/object/<id>`, `.../fields`, `.../field/<identifier>` | yes | yes | the same by object id |
| `GET` | `/content/node/<id>/listAtom` | yes | yes | Atom feed of the children; answers 500, see [section 8](#8-known-issue-listatom) |
| `POST` | `/content/node/create` | no | yes | create and publish below `parentNodeID` |
| `POST` | `/content/node/<id>` | no | yes | update: checks `content/edit`, then answers 501 (not implemented yet) |
| `DELETE` or `POST` | `/content/node/delete/<id>` | no | yes | remove the node's object with all its locations and what is below them |

A write sent to v1 does not exist there and is refused. The links inside a v1 answer stay in v1, so existing clients
keep following them.

The answers and the policies each call checks are described in
[Personal API keys, section 3](api-keys.md#3-using-a-key-with-curl) and
[Permissions of the REST interface](api-keys.md#32-permissions-of-the-rest-interface). In short: `200`, `201` for a
create, `400` for a missing or unknown field, `401` without a valid token or key, `403 access_denied` when the user
may not, `404` for an unknown node.

## 3. Authentication

`rest.ini [Authentication] RequireAuthentication=enabled` is the default: every content route needs a user. Two
credentials are accepted.

### 3.1 OAuth2 tokens

For an application that lets *other* users sign in and act for them.

1. An administrator registers the application at **Setup > oAuth admin** (`/oauthadmin/list`). It gets a client
   identifier, a secret and the redirect address of the application.
2. The application sends the user to the authorize page of the site:
   `https://example.com/oauth/authorize?client_id=<client id>&redirect_uri=<redirect uri>&response_type=code`.
   The user signs in and allows the application; the site redirects back with `code=<code>`.
3. The application exchanges the code for a token at the token endpoint,
   `POST /api/auth/v1/oauth/token`, with `grant_type=authorization_code`, `client_id`, `client_secret`, `code` and
   `redirect_uri`.
4. It sends the token with every request (`Authorization: OAuth <token>`, or the `oauth_token` parameter).
5. When the token has expired (`rest.ini [OAuthSettings] TokenTTL`, one hour by default), the application asks for a
   new one with `grant_type=refresh_token` and `refresh_token=<refresh token>`.

The token endpoint supports the grants `authorization_code` and `refresh_token`.

### 3.2 Personal API keys

For a script of the user's own. The user makes a key on the site (**My account > API access**) and sends it as
`Authorization: Bearer expk_...`. The request runs as the key's owner, limited to the key's scopes, and never beyond
the owner's policies. The whole story is in [Personal API keys](api-keys.md).

## 4. Examples with curl

```bash
export EXP_API_KEY='expk_...'     # a personal API key; an OAuth token works the same way with "OAuth <token>"

# one node, v1 (what existing clients call)
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2

# the first 20 children with their fields, v1 (the call of the mobile app)
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v1/content/node/<node id>/list/offset/0/limit/20?ResponseGroups=Fields"

# the same node at v2, with the rendered XHTML of its view
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/2?OutputFormat=xhtml"

# one field by object id
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/object/<object id>/field/title

# create a folder below a node (v2 only)
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/create \
     -d parentNodeID=<parent node id> -d classIdentifier=folder -d languageLocale=eng-US -d name='From a script'
# {"message":"Created","objectId":<object id>,"nodeId":<node id>}

# remove it again (v2 only)
curl -s -X DELETE -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/delete/<node id>
# {"message":"Removed","nodeId":<node id>,"objectId":<object id>}
```

Exchanging an authorization code for an OAuth token:

```bash
curl -s https://example.com/api/auth/v1/oauth/token \
     -d grant_type=authorization_code -d client_id='<client id>' -d client_secret='<client secret>' \
     -d code='<code>' -d redirect_uri='<redirect uri>'
```

Keep keys, secrets and tokens in environment variables or a secret store, never in a script, a repository or shell
history on a shared machine.

## 5. Configuration

The kernel's `settings/rest.ini` holds the defaults; `ezprestapi` adds its provider and output section in
`extension/ezprestapi/settings/rest.ini.append.php`. Override in `settings/override/rest.ini.append.php`.

```ini
[System]
# the path below which the REST interface answers
ApiPrefix=/api

[ApiProvider]
# set by ezprestapi (and, identically, by the ezprestapiprovider 6.0.4 shim)
ProviderClass[ezp]=ezp7xRestApiProvider
ProviderClass[ezpl]=ezp7xRestApiProvider

[Authentication]
RequireAuthentication=enabled
AuthenticationStyle=ezpRestOauthAuthenticationStyle
#AuthenticationStyle=ezpRestBasicAuthStyle
RequireHTTPS=disabled
DefaultUserID=

[OAuthSettings]
TokenTTL=3600

# the template of OutputFormat=xhtml for the controller of ezprestapi (ezprestapi 1.2.6, kernel 6.0.15)
[ezp7xRestContentController_viewContent_OutputSettings]
Template=rest_pagelayout.tpl
```

| Setting | Meaning |
|---|---|
| `[System] ApiPrefix` | the URL path the REST interface answers below (`/api`) |
| `[ApiProvider] ProviderClass[<prefix>]` | the provider class of a prefix: `/api/<prefix>/<version>/...`. `ezp` and `ezpl` are `ezp7xRestApiProvider`; `auth` is the kernel's `ezpRestAuthProvider` |
| `[Authentication] RequireAuthentication` | `disabled` lets every request run as `DefaultUserID` (or the anonymous user); the permission checks still apply |
| `[Authentication] AuthenticationStyle` | OAuth (default) or HTTP basic authentication; personal API keys are accepted by both |
| `[Authentication] RequireHTTPS` | refuse plain HTTP |
| `[<Controller>_<action>_OutputSettings] Template` | the page layout template of the XHTML renderer for that controller action |
| `[ApiKeySettings]` | the personal API keys, see [Personal API keys, section 13](api-keys.md#13-every-setting) |

The output section is named after the controller. Up to `ezprestapi` 1.2.5 only
`[ezpRestContentController_viewContent_OutputSettings]` existed, the name of the old v1 controller, so a read with
`OutputFormat=xhtml` through `ezp7xRestContentController` found no template and answered an empty `renderedOutput`.
1.2.6 and the kernel of 6.0.15 add the section for `ezp7xRestContentController`.

After a change to `rest.ini`, clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`),
reload PHP-FPM and restart Velocity.

## 6. Migrating from ezprestapiprovider

`ezprestapiprovider` 6.0.4 (15 October 2026) is its final release. It keeps old installations working as a shim:
`ezpRestApiProvider` extends `ezp7xRestApiProvider`, `ezpRestContentController` extends
`ezp7xRestContentController`, its `rest.ini` maps `ezp` and `ezpl` to `ezp7xRestApiProvider`, and its Composer
package requires `se7enxweb/ezprestapi ~1.2.6`. It no longer ships the five classes it duplicated
(`ezpRestAtomController`, `ezpRestAtomView`, `ezpRestAtomDecorator`, `ezpRestContentModel`,
`ezpRestApiViewController`); `ezprestapi` ships them.

To move an existing installation:

1. Update the packages: `se7enxweb/ezprestapi` to 1.2.6 or later (and `se7enxweb/ezprestapiprovider` to 6.0.4 while
   it is still installed). The Exponential 6.0.15 root `composer.json` requires `se7enxweb/ezprestapi ~1.2.6` and no
   longer requires `ezprestapiprovider`.
2. In `settings/override/site.ini.append.php`, keep or add the one line and remove the other:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=ezprestapi
   # remove: ActiveExtensions[]=ezprestapiprovider
   ```

3. Clear the INI cache, regenerate the extension autoloads, and reload the web runtime:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   systemctl reload <your PHP-FPM service>
   ./console exp:velocity restart --allow-root-user     # when Velocity serves the site
   ```

4. Check: `curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2` and the same
   at `/api/ezp/v2/` answer `200`.

Things that keep working:

- A `rest.ini` override that says `ProviderClass[ezp]=ezpRestApiProvider` works through the shim while
  `ezprestapiprovider` is installed. Change it to `ezp7xRestApiProvider`.
- Code that extends `ezpRestContentController` works while the shim is installed. Extend
  `ezp7xRestContentController` instead.
- Nothing changes for OAuth clients, their tokens, personal API keys or the v1 URLs.

New installations: the setup wizard (`eZStepCreateSites::restApiExtension()` in
`kernel/setup/steps/ezstep_create_sites.php`) activates `ezprestapi`, or `ezprestapiprovider` only when `ezprestapi`
is not on disk, never both. The installers of the legacy site packages (`ezwebin_site`, `ezflow_site`,
`ezdemo_site` and their `_clean` variants) activate `ezprestapi` from the 6.0.15 packages of 15 October 2026 on; the
sevenx and cjw site packages already did.

## 7. Activation combinations, before and since

Both extensions set `rest.ini [ApiProvider] ProviderClass[ezp]`. Extension settings are prepended per
`ActiveExtensions` entry, so the extension listed **earlier** in `ActiveExtensions` is read last and its value wins.

**Before** (`ezprestapi` up to 1.2.5, `ezprestapiprovider` up to 6.0.3):

| Active | `ProviderClass[ezp]` | Result |
|---|---|---|
| `ezprestapi` alone | `ezp7xRestApiProvider` | v1 reads and v2 reads and writes, all checked |
| both, `ezprestapi` listed first | `ezp7xRestApiProvider` | the same; the provider extension contributed nothing |
| both, `ezprestapiprovider` listed first | `ezpRestApiProvider` | every `/api/ezp/v2/...` answered 404, create and delete too; v1 reads ran in `ezpRestContentController` without the permission checks; `ezpl` still served v2 through `ezprestapi` |
| `ezprestapiprovider` alone (as the setup wizard used to activate it) | `ezpRestApiProvider` | v1 reads only, unchecked; v2 404; the prefix `ezpl` did not exist |

With both installed, the five duplicate class names also made the autoload generator warn and keep the copies of
`ezprestapi`.

**Since** (`ezprestapi` 1.2.6, `ezprestapiprovider` 6.0.4):

| Active | `ProviderClass[ezp]` and `[ezpl]` | Result |
|---|---|---|
| `ezprestapi` alone | `ezp7xRestApiProvider` | v1 and v2, every content route in `ezp7xRestContentController`, all checked |
| both, in either order | `ezp7xRestApiProvider` | the same |
| `ezprestapiprovider` alone (with `ezprestapi` installed as its dependency) | `ezp7xRestApiProvider` | the same |

This was checked by building the route table for each combination. The answers were compared with fixtures of 33
URLs recorded on Apache and on Velocity before and after the change: the mobile app's
`GET /api/ezp/v1/content/node/<id>/list/offset/0/limit/20?ResponseGroups=Fields` and every other v1 and v2 read are
byte for byte identical. The only difference is `OutputFormat=xhtml`, whose `renderedOutput` is filled again.

## 8. Known issue: listAtom

`GET /content/node/<id>/listAtom` answers `500` at v1 and v2, before and after this change. Two things are missing:
the route's URL generation, and the feed view handler of the Zeta Components MVC feed tie-in
(`ezcMvcFeedViewHandler`), which the distribution does not install. Use the `list` route, or the site's RSS export,
until it is fixed.

## Related pages

- [Personal API keys](api-keys.md): making, using and revoking a key; the permission table of the REST interface
- [ezprestapi](../features/6.0/extensions/ezprestapi.md) and its [release notes](../changelogs/extensions/ezprestapi.md)
- [ezprestapiprovider](../features/6.0/extensions/ezprestapiprovider.md) and its [release notes](../changelogs/extensions/ezprestapiprovider.md)
- [nxc_powercontent](../features/6.0/extensions/nxc_powercontent.md)
- [Behavior changes of the extensions](../bc/6.0/extensions-behaviour-changes.md)
- [Remote services and apps](remote-services-and-apps.md)
