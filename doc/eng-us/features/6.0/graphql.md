# The GraphQL API: ask the content for exactly the fields a page needs

This page is for developers who build a front end, an app, a static site generator or an integration on the content
of an Exponential installation, and for administrators who switch the API on and decide what it shows. The kernel
answers GraphQL queries at `/api/graphql` in every siteaccess where it is switched on. The schema is generated from
the content classes of the installation: every class is a type, every attribute a field. Every query runs as a user of
the installation, signed in with an OAuth token, a personal API key, the session of the administration, or as the
anonymous user, and the roles and policies of that user decide what it may read, exactly as in the web interface.

This is phase 1: reading content. The API answers queries; it changes nothing. What later phases add is listed under
[What comes next](#what-comes-next).

| | |
|---|---|
| End point | `POST /api/graphql` (and `GET`) in each siteaccess; the session end point `POST /graphql/query` |
| Off until | `settings/graphql.ini` `[GraphQLSettings] Enabled=enabled`, optionally for the siteaccesses of `SiteAccessList[]` |
| Library | `webonyx/graphql-php` 15, required by the root `composer.json` |
| Sign-in | an OAuth token, a personal API key with the scope `graphql:read`, the session (playground), or none (anonymous) |
| Rights | the user's roles and policies, always; the schema settings decide what can be asked at all |
| Administration | Setup > GraphQL (policy `graphql/playground`): the end points, the schema and a playground |
| Console | `exp:graphql:status`, `exp:graphql:schema`, `exp:graphql:query` |
| Settings | [graphql.ini [GraphQLSettings]](../../examples/ini-settings/graphql/GraphQLSettings.md), [[GraphQLNaming]](../../examples/ini-settings/graphql/GraphQLNaming.md) |

## Contents

1. [Quick start in 60 seconds](#quick-start-in-60-seconds)
2. [Requests and answers](#requests-and-answers)
3. [Signing in](#signing-in)
4. [The schema](#the-schema)
5. [Queries, with examples](#queries-with-examples)
6. [Persisted queries](#persisted-queries)
7. [Security](#security)
8. [Speed](#speed)
9. [Errors](#errors)
10. [The playground and the session end point](#the-playground-and-the-session-end-point)
11. [Schema extensions](#schema-extensions)
12. [Console commands](#console-commands)
13. [The library](#the-library)
14. [Settings](#settings)
15. [What comes next](#what-comes-next)

## Quick start in 60 seconds

1. **Switch it on.** For every siteaccess, in `settings/override/graphql.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?
   [GraphQLSettings]
   Enabled=enabled
   */ ?>
   ```

   For one siteaccess only, put the same block in `settings/siteaccess/<siteaccess>/graphql.ini.append.php`, or list
   the siteaccesses that answer in `SiteAccessList[]`. Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`).

2. **Check it.**

   ```bash
   ./console exp:graphql:status --siteaccess=site
   ```

   ```text
   siteaccess     site
   enabled        yes
   end point      /api/graphql
   library        webonyx/graphql-php 15.39.0
   limits         depth 12, complexity 1000, list 100 (default 25)
   introspection  signed-in
   naming         identifier
   schema         50c68965a5da, 88 classes, 828 fields
   string only    cjwnewsletteredition cjwnewsletterlist cjwnewsletterlistvirtual ezgmaplocation ezmultioption ezoption ezsrrating ezsubtreesubscription eztags ngclasslist ngenhancedlink sckenhancedselection xrowmetadata
   PASS the GraphQL API can run
   ```

3. **Ask it.** Anonymous, with the rights of the anonymous user:

   ```bash
   curl -s https://example.com/api/graphql \
        -H 'Content-Type: application/json' \
        -d '{"query": "{ node(urlAlias: \"/\") { name children(limit: 3) { totalCount nodes { name urlAlias } } } }"}'
   ```

   ```json
   {"data":{"node":{"name":"Fit & Healthy","children":{"totalCount":16,"nodes":[{"name":"Fitness","urlAlias":"fitness"},{"name":"Healthy eating","urlAlias":"healthy-eating"},{"name":"Recipes","urlAlias":"recipes"}]}}},"extensions":{"requestId":"40b27114794d909f","schemaHash":"50c68965a5da"}}
   ```

   With a personal API key (My account > API access, scope **Read content through GraphQL**, `graphql:read`), the query
   runs with the rights of the key's owner, and the client may read the schema:

   ```bash
   curl -s https://example.com/api/graphql \
        -H "Authorization: Bearer $EXP_API_KEY" \
        -H 'Content-Type: application/json' \
        -d '{"query": "{ currentUser { name signedInBy } siteaccess { name languages schemaHash } }"}'
   ```

4. **Try queries in the browser**: Setup > GraphQL in the administration, with your own rights.

## Requests and answers

The end point follows the usual GraphQL over HTTP conventions:

| Request | How the query is read |
|---|---|
| `POST` with `Content-Type: application/json` | the body `{ "query": ..., "variables": { ... }, "operationName": ... }` |
| `POST` with `Content-Type: application/graphql` | the body is the query alone |
| `POST` of a form | the fields `query`, `variables` (a JSON text) and `operationName` |
| `GET` | the same three in the query string; prefer `POST`: a query in an address ends up in access logs |

`/api/graphql` and `/api/graphql/` are the same end point. The siteaccess that answers is the one the request matches,
by host, path or the `X-Siteaccess` header, as for every REST request; it decides the languages, the root node, the
addresses in the answer and whether the API is switched on.

| Status | When | Body |
|---|---|---|
| 200 | the query ran, errors of the query included | `data`, `errors` (when there are any), `extensions` |
| 400 | the request cannot be read: the body is not JSON, `variables` is not a JSON object | `errors` with `BAD_USER_INPUT` |
| 404 | the API is switched off in this siteaccess | `errors` with `DISABLED` |
| 503 | the library is not installed | `errors` with `INTERNAL` |

Every answer is JSON in UTF-8 and has `Cache-Control: private, no-store`: it depends on the user and on the query, so
no shared cache may keep it, and the REST application cache is switched off for it
(`rest.ini [expGraphQLRestController_CacheSettings] ApplicationCache=disabled`). An answer of the query also has the
header `X-GraphQL-Schema` with the schema hash, and `extensions`:

```json
"extensions": { "requestId": "40b27114794d909f", "schemaHash": "50c68965a5da" }
```

`requestId` is new for every request (16 hexadecimal digits) and is repeated in every error, so a report from a client
can be matched with the logs. `schemaHash` (12 hexadecimal digits) changes when a class, an attribute or the naming
changes: a client that caches the schema, or code generated from it, compares it and fetches the schema again when it
differs. The same hash is in `siteaccess { schemaHash }`.

A query is at most 100 000 bytes long.

## Signing in

The REST layer reads the credentials, as for every route of `/api/`:

| The request carries | It runs as | `currentUser.signedInBy` |
|---|---|---|
| nothing | the anonymous user (or `rest.ini [Authentication] DefaultUserID`) | `anonymous` |
| `Authorization: Bearer <OAuth token>` | the user the token was issued to | `token` |
| `Authorization: Bearer expk_...`, a personal API key with the scope `graphql:read` | the owner of the key | `apikey` |
| the session cookie, at the session end point `graphql/query` | the signed-in user of the browser | `session` |
| `./console exp:graphql:query --as=<login>` | that user (anonymous without `--as`) | `console` |

- **Optional, not absent.** The route is listed in `rest.ini [RouteSettings] OptionalAuthentication[]`: it is
  authenticated only when the request presents credentials (an `Authorization` header, or a token or key parameter).
  A request with a wrong or expired token is refused as on every other route; it never falls back to the anonymous
  user.
- **The scope `graphql:read`.** `rest.ini` has `Scopes[graphql:read]=content/read` and
  `RouteScopes[expGraphQLRestController_query]=graphql:read`: a personal API key needs this scope, and its owner must
  still hold `content/read`. A key with only the scope `read` is refused at `/api/graphql`. Scope ids may now carry a
  namespace (`graphql:read`); see [API keys, Scopes](../../guides/api-keys.md#6-scopes). Send the key in the
  `Authorization` header: a key in the address is refused.
- **OAuth tokens** come from the REST interface's OAuth flow: [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md).

## The schema

The schema of a siteaccess is built from its defined content classes, filtered by `graphql.ini`, when a request comes
in. Print it with `./console exp:graphql:schema` (the schema language) or read it with any GraphQL client through
introspection.

### Root queries

```graphql
type Query {
  node(id: Int, remoteId: String, path: String, urlAlias: String, language: String): Content
  content(id: Int, remoteId: String, language: String): Content
  children(parentId: Int!, classes: [String!], sortField: ContentSortField, sortOrder: SortOrder,
           depth: Int = 1, limit: Int, offset: Int, first: Int, after: String, language: String): ContentConnection!
  currentUser: User!
  siteaccess: SiteAccess!
  translations: [Language!]!
}
```

| Query | Answers |
|---|---|
| `node` | the content at a location, by exactly one of: the node id, the remote id of the location, the path string (`/1/2/58/`) or the URL alias (`about/company`; `""` or `/` is the root node of the siteaccess) |
| `content` | a content object at its main location, by exactly one of its id or its remote id |
| `children` | the content below a location (see [Lists](#lists-limit-and-offset-or-cursors)) |
| `currentUser` | `id`, `login`, `name`, `anonymous`, `signedInBy` of the user the request runs as |
| `siteaccess` | `name`, `languages`, `rootNodeId`, `schemaHash` |
| `translations` | the content languages of the installation: `code` and `name` |

### Content: one type per class

Every content class in the schema is an object type that implements the interface `Content`. The interface has the
fields every content has:

| Field | Type | |
|---|---|---|
| `id` | `Int!` | the content object id |
| `remoteId` | `String!` | the remote id of the object |
| `name` | `String!` | the name, in the translation read |
| `classIdentifier`, `className` | `String!` | the class |
| `published`, `modified` | `String` | ISO 8601 (`2026-10-29T13:45:00+00:00`) |
| `language` | `String!` | the translation the fields are read in |
| `languages` | `[String!]!` | its translations |
| `mainLanguage` | `String!` | the language it was first written in |
| `ownerId`, `ownerName`, `sectionId` | | the owner and the section |
| `url` | `String` | the address of the location in this siteaccess |
| `urlAlias` | `String` | the URL alias of the location (`about/company`) |
| `location` | `Location` | the location it was reached by: the node a query or a list named, else the main location |
| `mainLocation` | `Location` | the main location |
| `locations` | `[Location!]!` | every location the user may read |
| `parent` | `Content` | the content of the parent location |
| `children` | `ContentConnection!` | the content below the location, with the arguments of the root query `children` |

The type of a class adds a field per attribute, after these. Its description is the class name and description, and
the class identifier; the description of a field is the attribute name and description, and the datatype. A
`Location` has `id`, `remoteId`, `parentId`, `depth`, `pathString`, `priority`, `hidden`, `invisible`, `urlAlias`,
`url`, `content`, `parent` and `children`.

### Naming styles

`[GraphQLSettings] NamingStyle` decides how classes and attributes are named:

| Style | Class `ng_article`, attribute `short_title` |
|---|---|
| `identifier` (the default) | type `ng_article`, field `short_title`: the identifiers of the administration as they are |
| `pascal` | type `NgArticle`, field `shortTitle` |
| `map` | as `identifier`, but `[GraphQLNaming] TypeNames[ng_article]=Article` and `FieldNames[ng_article/short_title]=shortTitle` win |

Rules that hold in every style, so that every class gets a valid, unique and stable name:

- A character GraphQL does not allow in a name (a dash, a dot) becomes `_`; a name that starts with a digit gets a `_`
  in front; a leading `__` (reserved for introspection) becomes a single `_`.
- **The suffix `_field`.** An attribute named like a field of the interface `Content` (`name`, `location`, `url`,
  `children` ...) gets the suffix `_field`: the attribute `name` of a class is the field `name_field`, next to `name`,
  the object's name.
- **The suffix `Content`.** A type name that is one of the schema's own types (`Image`, `Location`, `User`, `Link` ...)
  gets the suffix `Content`: the class `image` is the type `ImageContent` in the `pascal` style.
- Two classes whose names come out the same get `_2`, `_3` ... in the order of the classes; the same for two
  attributes of one class.

Choose the style before clients depend on it: a client's queries name the types and fields.

### Datatypes

| Datatype | GraphQL type |
|---|---|
| `ezstring`, `eztext`, `ezemail`, `ezisbn`, `ezidentifier` | `String` |
| `ezinteger` | `Int` |
| `ezfloat` | `Float` |
| `ezboolean` | `Boolean` |
| `ezdate` | `String`, `2026-10-29` |
| `ezdatetime` | `String`, ISO 8601 |
| `eztime` | `String`, `13:45:00` |
| `ezxmltext` | `RichText`: `html`, `text`, `xml`, `excerpt(length: 200)` |
| `ezimage` | `Image`: `url(alias:)`, `width`, `height`, `mimeType` (each with `alias`), `alt`, `variations(aliases:)` |
| `ezbinaryfile`, `ezmedia` | `BinaryFile`: `fileName`, `mimeType`, `size`, `url` (the download address) |
| `ezprice` | `Price`: `price`, `incVat`, `exVat`, `vatPercent` |
| `ezurl` | `Link`: `url`, `text` |
| `ezselection` | `[SelectionOption!]!`: `id`, `name` of the chosen options |
| `ezcountry` | `[Country!]!`: `code`, `name` |
| `ezkeyword` | `[String!]!` |
| `ezauthor` | `[Author!]!`: `name` |
| `ezmatrix` | `Matrix`: `columns`, `rows` |
| `ezobjectrelation` | `Content` |
| `ezobjectrelationlist` | `[Content!]!` |
| `ezgmaplocation` | `GeoLocation`: `latitude`, `longitude`, `address` |
| `ezsrrating` (star rating) | `Rating`: `average` (null without votes), `count` |
| `xrowmetadata` | `Metadata`: `title`, `description`, `keywords`, `priority`, `change`, `sitemapUse`, `canonicalUrl` |
| `ngenhancedlink` | `EnhancedLink`: `type` (`internal`, `external`), `url`, `label`, `target`, `suffix`, `rel`, `content` (the linked content, when readable) |
| `sckenhancedselection` | `[String!]!`: the identifiers chosen |
| `ngclasslist` | `[String!]!`: class identifiers |
| `ezoption` | `OptionList`: `name`, `options` (`id`, `value`, `additionalPrice`) |
| `ezmultioption` | `[OptionGroup!]!`: `name`, `options` |
| `ezrangeoption` | `Range`: `name`, `start`, `stop`, `step` |
| `ezmultiprice` | `[CurrencyPrice!]!`: `currency`, `value`, `type` |
| `ezenum` | `[EnumValue!]!`: `element`, `value` |
| `ezsubtreesubscription` | `Boolean` |
| `ezproductcategory` | `String`: the category's name |
| a datatype of a [schema extension](#schema-extensions) | the type the extension gives it |
| every other datatype | `String`: the field's text form (`toString()`) |

Every attribute is in the schema (unless hidden): a datatype without a mapping of its own, from the kernel or a schema
extension, is a `String` with the text the datatype gives for export. `exp:graphql:status` lists these datatypes under
`string only`, and `exp:graphql:schema --format=summary` marks each such field with `"mapped": false`. A datatype of an
extension that is not active in the siteaccess has no content there, so its field is `null` (or an empty list).

## Queries, with examples

Every example here was run with `./console exp:graphql:query` on a development installation with demo content; the
answers are shortened only where marked. Addresses in an answer start with the scheme and host of the request
(`https://example.com/...`); on the console they start with `/`.

### A node by URL alias, with its children

```graphql
{
  node(urlAlias: "/") {
    name
    classIdentifier
    children(limit: 3) {
      totalCount
      nodes { name classIdentifier urlAlias }
      pageInfo { hasNextPage endCursor }
    }
  }
}
```

```json
{
    "data": {
        "node": {
            "name": "Fit & Healthy",
            "classIdentifier": "ng_frontpage",
            "children": {
                "totalCount": 16,
                "nodes": [
                    { "name": "Fitness", "classIdentifier": "ng_landing_page", "urlAlias": "fitness" },
                    { "name": "Healthy eating", "classIdentifier": "ng_landing_page", "urlAlias": "healthy-eating" },
                    { "name": "Recipes", "classIdentifier": "ng_category", "urlAlias": "recipes" }
                ],
                "pageInfo": { "hasNextPage": true, "endCursor": "ZXhwOjI=" }
            }
        }
    }
}
```

By id: `node(id: 89)`; by the remote id of the location: `node(remoteId: "...")`; by path: `node(path: "/1/2/89/")`.
A content object at its main location: `content(id: 57)` or `content(remoteId: "...")`. Content that does not exist,
or that the user may not read, is `null`.

### Lists: limit and offset, or cursors

A list field (`children` of the root, of a content and of a location) takes:

| Argument | |
|---|---|
| `classes` | only content of these class identifiers (a class that is not in the schema is left out) |
| `sortField`, `sortOrder` | `PATH`, `PUBLISHED`, `MODIFIED`, `SECTION`, `DEPTH`, `CLASS_IDENTIFIER`, `CLASS_NAME`, `PRIORITY`, `NAME`, `NODE_ID`; `ASC` (the default) or `DESC`. Default: the sorting of the parent location |
| `depth` | how deep below the location: 1 (the default) the children, 2 their children too ... |
| `limit`, `offset` | a page by position |
| `first`, `after` | a page by cursor: `first` items after the cursor `after` |

Both pagination styles work on the same field; `first` and `after` win over `limit` and `offset` when given. Without
either, a list returns `DefaultListLimit` items (25); more than `MaxListLimit` (100) is lowered to it. `totalCount`
costs one count query and is only run when asked for.

The cursor style, as Relay clients expect it:

```graphql
query Articles($parent: Int!, $after: String) {
  children(parentId: $parent, classes: ["ng_article"], depth: 3,
           sortField: PUBLISHED, sortOrder: DESC, first: 2, after: $after) {
    totalCount
    edges { cursor node { name urlAlias } }
    pageInfo { hasNextPage hasPreviousPage startCursor endCursor }
  }
}
```

```bash
./console exp:graphql:query articles.graphql --variables='{"parent": 89}'
./console exp:graphql:query articles.graphql --variables='{"parent": 89, "after": "ZXhwOjE="}'
```

The second call gives the next two, with `"hasPreviousPage": true` and `"startCursor": "ZXhwOjI="`. A cursor is the
position of an item in the whole list; it stays valid while the list does not change. A cursor this API did not give
is refused with `BAD_USER_INPUT`.

### Fields of a class: inline fragments

`node`, `content`, `children` and the relation fields answer the interface `Content`. The fields of a class are read
with an inline fragment on its type:

```graphql
{
  children(parentId: 89, classes: ["ng_article"], depth: 3, first: 2) {
    nodes {
      name
      ... on ng_article {
        title
        teaser_intro { excerpt(length: 80) }
        related_content { name url }
      }
    }
  }
}
```

A field of a class asked for without the fragment is a validation error that names the types having it:
`Cannot query field "title" on type "Content". Did you mean to use an inline fragment on "article", ...?`

### Images

```graphql
... on ng_article {
  image {
    url(alias: "small")
    alt
    variations(aliases: ["small", "large"]) { alias url width height }
  }
}
```

```json
"image": {
    "url": "/var/site/storage/images/fit-healthy/fitness/effective-training-techniques-isolation-vs-compound/889-1-eng-US/effective-training-techniques-isolation-vs-compound_small.jpg",
    "alt": "",
    "variations": [
        { "alias": "small", "url": "/var/site/storage/images/.../effective-training-techniques-isolation-vs-compound_small.jpg", "width": 100, "height": 75 },
        { "alias": "large", "url": "/var/site/storage/images/.../effective-training-techniques-isolation-vs-compound_large.jpg", "width": 1066, "height": 800 }
    ]
}
```

(The `variations` URLs are shortened here.) Every size is an image alias of `settings/image.ini`, made the first time
it is asked for, as in a template. `url`, `width`, `height` and `mimeType` take `alias` (default `original`).
`variations` without `aliases` lists the aliases of `[GraphQLSettings] ImageAliasList[]` (`small`, `medium`, `large`).
An alias that `image.ini` does not have is an error with `BAD_USER_INPUT`.

### Rich text

```graphql
body { html }                  # rendered HTML, as the site shows it
body { text }                  # plain text, one line per block
body { xml }                   # the stored XML
teaser_intro { excerpt(length: 80) }   # the start of the plain text, cut at a word, ending in …
```

```json
"teaser_intro": { "excerpt": "This Cookie Policy explains what this website stores in your browser, why, and…" }
```

### Languages

Without `language`, the fields are read in the translation the siteaccess shows (the first of its languages the object
has). `language` on `node`, `content` and `children` asks for another one; when the object has no such translation, the
siteaccess's choice is used. `language` and `languages` of the content say what was read and what exists:

```graphql
{
  node(urlAlias: "fitness", language: "ger-DE") { name language languages mainLanguage }
  translations { code name }
}
```

```json
{
    "data": {
        "node": { "name": "Fitness", "language": "eng-US", "languages": [ "eng-US" ], "mainLanguage": "eng-US" },
        "translations": [
            { "code": "eng-US", "name": "English (American)" },
            { "code": "ger-DE", "name": "German" },
            { "code": "qlo-ZZ", "name": "Lorem Ipsum" }
        ]
    }
}
```

The page has no German translation, so it is read in English, and `language` says so.

### Who is asking, and where

```graphql
{
  siteaccess { name languages rootNodeId schemaHash }
  currentUser { id anonymous signedInBy }
}
```

```json
{
    "data": {
        "siteaccess": { "name": "site", "languages": [ "eng-US" ], "rootNodeId": 89, "schemaHash": "50c68965a5da" },
        "currentUser": { "id": 10, "anonymous": true, "signedInBy": "console" }
    }
}
```

## Persisted queries

A persisted query is stored on the server and called by an id instead of its text: the address stays short, the
query cannot be changed by whoever calls it, and only persisted queries are kept in the [response
cache](#speed). The id is the query's SHA-256 (64 hexadecimal digits) or the name it was stored under.

```bash
# check every .graphql file of a directory against the schema, store nothing
./console exp:graphql:persist frontend/queries/
# store them: each under its SHA-256 and its file name without .graphql
./console exp:graphql:persist frontend/queries/ --apply
./console exp:graphql:persist menu.graphql --name=main-menu --apply
./console exp:graphql:persist --list
./console exp:graphql:persist --remove=main-menu --apply
```

A query that does not fit the schema (a syntax error, an unknown field) is refused, and then nothing is stored. The
store is `var/<site>/graphql/persisted/`: `<sha256>.graphql` and `names.json`. An extension or a deployment ships
queries in a directory of `PersistedQueryDirectoryList[]` instead: each `<name>.graphql` there is the persisted query
`<name>`, read only.

Calling one:

```bash
# by name, GET: variables and operationName as for any GET
curl 'https://example.com/api/graphql?id=articles50'
curl 'https://example.com/api/graphql?id=menu&variables=%7B%22depth%22%3A2%7D'
# by hash, in a JSON body
curl -H 'Content-Type: application/json' -d '{"id":"21f7a4e0...ffad"}' https://example.com/api/graphql
# from the console
./console exp:graphql:query --id=articles50
```

**Apollo's automatic persisted queries** work as Apollo Client sends them: a request with
`extensions={"persistedQuery":{"version":1,"sha256Hash":"<sha256>"}}` and no query runs the stored query, or is
answered `PersistedQueryNotFound` (`PERSISTED_QUERY_NOT_FOUND`); the client then sends the text with the hash, and
the server stores it when the request may store queries (`AutomaticPersistedQueries`: `signed-in` by default, at most
`MaxPersistedQueries` of them) and runs it either way. A hash that is not the query's is refused (`BAD_USER_INPUT`).

**Persisted queries only.** `PersistedQueriesOnly=anonymous` lets anonymous requests run persisted queries only;
`enabled` does so for every request to `/api/graphql`. An ad-hoc query is then refused with
`PERSISTED_QUERY_REQUIRED`. The playground (the session end point) and the console run any query.
`PersistedQueries=disabled` switches persisted queries off (`PERSISTED_QUERY_NOT_SUPPORTED`).

## Security

- **Policies always apply.** Every content object, location and relation is read through the permission system with
  the rights of the user the request runs as: content the user may not read is `null` in a single field and left out
  of a list and of a relation list, exactly as a template's `fetch` leaves it out. `totalCount` counts what the user
  may read. The anonymous user reads what the role Anonymous allows.
- **What the schema shows.** A class or an attribute that is not in the schema cannot be asked for, whoever asks:
  `ExcludedClassList[]` keeps the user accounts, the user groups and the settings classes out by default
  (`user`, `user_group`, `common_ini_settings`, `template_look`); `ClassList[]` names the only classes shown instead;
  `HiddenFieldList[]` hides attributes (`ng_article/internal_note`, or `*/internal_note` in every class);
  `HiddenDatatypeList[]` hides datatypes (`ezuser` and `ezpassword` by default: logins, e-mail addresses and password
  hashes are never in the schema). Content of a class that is not in the schema is `null` even where the user may read
  it.
- **Introspection** (reading the schema, as a GraphQL client or the playground does) is allowed for signed-in requests
  only by default (`Introspection=signed-in`): anonymous visitors can run queries but cannot list the schema. `enabled`
  allows it for everyone, `disabled` for nobody (the console commands still print it).
- **Limits, checked before a query runs.** `MaxDepth` (12): the deepest nesting of fields. `MaxComplexity` (1000):
  every field counts 1, a list field counts its `limit` or `first` (or `DefaultListLimit`) times the fields below it,
  so a list of 100 items with a list of 100 below it is refused at once:
  `The query has a complexity of 20202; at most 1000 is allowed.` (`COMPLEXITY_LIMIT`). `MaxListLimit` (100) caps every page, and a
  query is at most `MaxQueryLength` bytes (100 000).
- **Persisted queries only**, for anonymous requests or for all (`PersistedQueriesOnly`, [above](#persisted-queries)):
  a production front end that only ever sends its own stored queries leaves nothing else to try.
- **Rate limits.** A signed-in user or a personal API key may send `RateLimit` requests (600) in `RateLimitWindow`
  seconds (60), an anonymous IP address `RateLimitAnonymous` (120); `0` switches a limit off. A request over its
  limit is answered `429 Too Many Requests` with `RATE_LIMITED` and `Retry-After`, before any schema is built or
  query runs; every answer carries `X-RateLimit-Limit`, `X-RateLimit-Remaining` and `X-RateLimit-Reset`. The counters
  are small files in `var/cache/graphql/rate/` with an exclusive lock, so every PHP-FPM and every
  [Velocity](velocity-web-server.md) worker counts the same requests, with no database connection. The playground
  counts per user; the console is not limited. A load test from one address needs `RateLimitAnonymous=0` (or a token)
  on the siteaccess it measures.
- **Read only.** Phase 1 has no mutations: nothing a query does changes content.
- **No caching across rights.** An ad-hoc answer is `private, no-store`, never kept. The answer of a persisted query is
  kept per user rights (the user's roles and the values of its limited role assignments, as the view cache keys
  them), so a user never gets an answer made for other rights; an answer that names the user (`currentUser`) is not
  kept for signed-in users, and an answer with errors is never kept. The REST application cache never keeps an answer.
- **Errors do not leak.** An error of the code (a PHP exception) is answered as `Internal server error` with
  `INTERNAL`; the details go to the error log. `Debug=enabled` puts the message and the trace in `extensions.debug`:
  only on a development siteaccess.
- **Off until switched on**, per siteaccess: a siteaccess without it answers `404` with `DISABLED`, and nothing else
  changes.

## Speed

A query asks for a whole page at once, so the API reads the content of a page the way a page is read, in groups, not
one item after the other:

- **Batched loading** (the DataLoader pattern). The executor runs every field of one level of the query first and
  collects what they need: the owners of 50 articles are read in one query, the parents of a list in one, the related
  objects of every relation field of a page in one (their main locations with the objects, then their fields), and
  the URL aliases of a page in two, however long the page is. A value once read is answered from memory for the rest
  of the request. Measured on the demo content: 50 articles with their image, owner and address cost 11 SQL
  statements, where reading them one by one cost 249.
- **The schema from a cache file.** The content classes the schema is made from are read from the database once and
  kept in `var/<site>/cache/graphql/catalogue-*.php`, one file per siteaccess, filter settings, languages and class
  change: storing a class (the expiry `class-identifier-cache`) makes the next request write a new file. OPcache keeps
  the file, so a request reads no class from the database.
- **Lists in one query.** A list reads its items with their fields in one subtree fetch, and `totalCount` costs a
  count query only when it is asked for.

**The response cache** (`ResponseCache=enabled`, the default) keeps the answers of [persisted
queries](#persisted-queries), in `var/cache/graphql/responses/`:

- An **anonymous `GET`** of a persisted query (`/api/graphql?id=articles`, or Apollo's `extensions`) is answered at the
  very start of `index_rest.php`, before autoload, settings, siteaccess, session or database: its key is made from the
  raw request (scheme, host, address, query string, `X-Siteaccess`, `Origin`), which is all that decides its answer. It
  still counts against the anonymous rate limit, and a CORS request gets the `Access-Control-*` headers
  `rest.ini [CorsSettings]` gave its origin when the answer was made.
- A **signed-in `GET`** (an OAuth token or a personal API key) is answered there too once the kernel has checked its
  credentials: the kernel remembers the rights of the credentials (a hash of the token, never the token) for
  `CredentialCacheTtl` seconds (60) in `var/cache/graphql/credentials/`, and forgets every one at once when an API key
  is revoked or removed, a role or policy changes, or the user caches are cleaned up. Within those seconds a token
  revoked any other way still reads the answers already cached for its rights; `CredentialCacheTtl=0` sends every
  signed-in request through the kernel. The request counts against the user's or key's rate limit.
- Anything else (a `POST`, the first request of new credentials) is looked up by the kernel once the user is known, by
  siteaccess, schema hash, user rights, query, variables and address.
- **Expired** whenever content is published or the view caches are cleared (every path of
  `eZContentCacheManager` writes a time stamp in `var/cache/graphql/expired`; an answer made before it is stale), when
  a class changes, when persisted queries are stored or removed, after `ResponseCacheTtl` seconds (86400), and by
  `./console ezcache --clear-id=graphql`, which `--clear-tag=content` and `--clear-tag=ini` include: clear the INI caches
  after changing `graphql.ini`, as after any setting.
- **Headers.** `X-GraphQL-Cache: HIT`, `MISS` or `BYPASS` (a persisted query whose answer is not kept); every answer
  has an `ETag` (weak for ad-hoc answers, whose request id differs), and a request with a matching `If-None-Match` gets
  `304 Not Modified` without a body. A cached anonymous answer is `public, max-age=0, must-revalidate` (a proxy may keep
  it but must ask again), a signed-in one `private, no-cache`. The request id of a cached answer is in the header
  `X-Request-Id`, not in the body, so the body is the same for every request.

Measured on alpha (PHP-FPM 8.5 with OPcache behind nginx, 4 workers, the demo content: 50 articles with image, owner
and address, a 20.8 KB answer; 3 runs of 250 requests each, one after the other, on a shared host with a load of 5
to 7):

| Request | Median | p95 | Max |
|---|---|---|---|
| cached, anonymous `GET` | 1.2 ms | 3.0 ms | 28.8 ms |
| cached, signed-in `GET` (credentials known) | 1.1 ms | 2.9 ms | 30.3 ms |
| cached, CORS `GET` | 1.7 ms | 3.8 ms | 11.1 ms |
| `304 Not Modified` | 1.0 ms | 2.2 ms | 13.8 ms |
| cached, but through the kernel (a `POST`, new credentials) | 17.7 ms | 22.7 ms | 45.1 ms |
| not cached (an ad-hoc query) | 78.8 ms | 111.9 ms | 178.8 ms |

The early answer is under 33 ms on every request and under 13 ms at the median and p95; an answer that has to start
the kernel costs its start (about 18 ms) on top.

Nothing of this is kept between requests: the loaders and their memory belong to one request, so a persistent worker
([Velocity](velocity-web-server.md)) never answers from another request's memory and keeps no database connection
of its own.

## Errors

An error follows the GraphQL specification, with `extensions.code` and `extensions.requestId`:

```json
{
    "errors": [
        {
            "message": "node needs exactly one of id, remoteId, path and urlAlias.",
            "locations": [ { "line": 1, "column": 3 } ],
            "path": [ "node" ],
            "extensions": { "code": "BAD_USER_INPUT", "requestId": "029c3f6310f931ef" }
        }
    ],
    "data": { "node": null },
    "extensions": { "requestId": "029c3f6310f931ef", "schemaHash": "50c68965a5da" }
}
```

| `extensions.code` | Meaning |
|---|---|
| `PARSE` | the query is not valid GraphQL syntax |
| `VALIDATION` | the query does not fit the schema (an unknown field, a missing fragment, a wrong argument type) |
| `BAD_USER_INPUT` | an argument is wrong (two of `id`/`urlAlias`, a cursor this API did not give, an unknown image alias), or the request cannot be read |
| `DEPTH_LIMIT` | deeper than `MaxDepth` |
| `COMPLEXITY_LIMIT` | more complex than `MaxComplexity` |
| `INTROSPECTION_DISABLED` | the request may not read the schema (`Introspection`) |
| `DISABLED` | the API is switched off in this siteaccess (HTTP 404) |
| `INTERNAL` | an error of the code, or the library is missing (HTTP 503) |
| `PERSISTED_QUERY_NOT_FOUND` | no persisted query has this id or hash (the message is `PersistedQueryNotFound`, as Apollo Client expects) |
| `PERSISTED_QUERY_NOT_SUPPORTED` | persisted queries are switched off (`PersistedQueries=disabled`; the message is `PersistedQueryNotSupported`) |
| `PERSISTED_QUERY_REQUIRED` | an ad-hoc query where only persisted queries are accepted (`PersistedQueriesOnly`) |
| `RATE_LIMITED` | over the rate limit (HTTP 429, with `Retry-After`) |
| `FORBIDDEN`, `NOT_FOUND` | reserved for later phases; not sent yet (content that cannot be read is `null`) |

The codes are a stable contract: a code, once released, keeps its name and meaning, and new codes are only added.

**The language of the messages.** A message is written in the language the request asks for with `Accept-Language`
(`de-DE,de;q=0.9`: the first language, by its `q`, that has an administration catalogue, by its full code or its
language), else in the language of the siteaccess (`site.ini [RegionalSettings] Locale`). The texts are in the
context `kernel/graphql` of `share/translations/<language>/translation.ts`, in every catalogue of the administration.
`extensions.code` never changes with the language: decide by the code, show the message. The messages of the GraphQL
specification's validation (an unknown field, a syntax error) come from the library and stay in English, since they
quote the query; `PersistedQueryNotFound` and `PersistedQueryNotSupported` stay as they are, since Apollo Client reads
them.

```bash
curl -H 'Accept-Language: de' 'https://example.com/api/graphql?query=%7Bnode%7Bid%7D%7D'
# {"errors":[{"message":"node braucht genau eines von id, remoteId, path und urlAlias.", ... "code":"BAD_USER_INPUT" ...
```

## The playground and the session end point

**Setup > GraphQL** (`graphql/playground`, in the group of the data tables and the secret store) shows whether the API
answers in the siteaccess, the end point address, the library and its version, the schema hash and the number of
classes, and a playground: a query, its variables (JSON) and **Run** (or Ctrl+Enter, Cmd+Enter). The query runs with
your rights, signed in with the session of the administration, and introspection is allowed since the request is
signed in. `/api/graphql/playground` redirects to it.

The playground posts to the **session end point** `graphql/query`: `POST /graphql/query` (in the siteaccess's address,
for example `/admin/graphql/query`), with the same JSON body as `/api/graphql`, the session cookie, and the form token
of the page in the header `X-CSRF-Token`, as every `POST` of a signed-in session needs it. It answers as `/api/graphql`
does (`200`, `400`, `404`, `503`); a `GET` is refused with `400`, so a query of a session never ends up in an address.
A page of the site may use it too, for the signed-in user of the browser.

| Policy | Allows |
|---|---|
| `graphql/playground` | opening Setup > GraphQL |
| `graphql/query` | running queries through the session end point (the playground needs it as well) |

Neither policy has limitations; what a query reads is decided by the content policies, as always. `/api/graphql`
needs no policy of its own: a token or key runs with its user's rights, and a key needs the scope `graphql:read`.

## Schema extensions

An extension adds to the schema with a class implementing `expGraphQLSchemaExtension`, listed in
`[GraphQLSettings] SchemaExtensionList[]` of its `settings/graphql.ini.append.php`. It is asked only where its
`isAvailable()` says it can answer (its extension active, its classes there), and it may add:

- **root queries** (`queryFields()`), next to `node`, `content` and `children` (a name the kernel uses is kept);
- **fields of `Location`** (`locationFields()`; the value is an `expGraphQLLocation`);
- **the type of its datatypes** (`datatypes()` and `fieldMapping()`): a field of such an attribute gets the extension's
  type instead of a `String`.

```php
class myColorGraphQLExtension implements expGraphQLSchemaExtension
{
    public function isAvailable() { return class_exists( 'myColorType' ); }
    public function name() { return 'mycolor/1'; }
    public function datatypes() { return array( 'mycolor' ); }
    public function queryFields( expGraphQLTypes $types ) { return array(); }
    public function locationFields( expGraphQLTypes $types ) { return array(); }
    public function fieldMapping( array $attribute, expGraphQLTypes $types )
    {
        $type = $types->register( 'MyColor', function () {
            return new GraphQL\Type\Definition\ObjectType( array( 'name' => 'MyColor', 'fields' => array(
                'hex' => array( 'type' => GraphQL\Type\Definition\Type::string(), 'resolve' => function ( $c ) { return $c['hex']; } ) ) ) );
        } );
        return array( $type, function ( eZContentObjectAttribute $field, expGraphQLContext $ctx ) {
            return $field->hasContent() ? array( 'hex' => (string)$field->attribute( 'data_text' ) ) : null;
        } );
    }
}
```

A type is made once per schema with `$types->register( name, factory )`. A resolver reads with the user's rights, as
the kernel's do, and asks a batch loader when a level of a list needs many values:
`$ctx->loader( 'mycolor', false, function ( array $keys ) { ... } )->load( $key )`. The extension's `name()` is part
of the schema hash, so clients see the schema change. `exp:graphql:status` and `exp:graphql:schema --format=summary`
list the extensions that answer.

## Console commands

| Command | Does |
|---|---|
| [`exp:graphql:status`](../../examples/commands/expgraphqlstatus.md) | switched on or off, end point, library and version, limits, introspection, naming, schema hash, classes, the datatypes without a mapping; `--format=text\|json` |
| [`exp:graphql:schema`](../../examples/commands/expgraphqlschema.md) | the schema: `--format=sdl` (the schema language, for code generators and editors), `json` (the introspection result), `summary` (classes, types, fields, datatypes, mapped or not); `--output=<file>` |
| [`exp:graphql:query`](../../examples/commands/expgraphqlquery.md) | runs a query, given as the argument, a `.graphql` or `.gql` file, or `-` for standard input, or a persisted query with `--id=<name or sha256>`; `--variables`, `--operation`, `--as=<login>`, `--format=json\|table` |
| [`exp:graphql:persist`](../../examples/commands/expgraphqlpersist.md) | stores `.graphql` files as [persisted queries](#persisted-queries) after checking them against the schema (`--apply`; `--name=`), `--list` (`--format=text\|json`), `--remove=<name or sha256> --apply` |

```bash
./console exp:graphql:status --format=json
./console exp:graphql:schema --siteaccess=site --output=schema.graphql
./console exp:graphql:schema --format=summary
./console exp:graphql:query '{ node(id: 2) { name children { totalCount } } }'
./console exp:graphql:query queries/menu.graphql --variables='{"depth": 2}' --as=admin
./console exp:graphql:query '{ siteaccess { name schemaHash } translations { code name } }' --format=table
```

`--format=table` prints one line per value, its path and the value, separated by a tab:

```text
data.siteaccess.name	site
data.siteaccess.schemaHash	50c68965a5da
data.translations.0.code	eng-US
data.translations.0.name	English (American)
data.translations.1.code	ger-DE
data.translations.1.name	German
```

`exp:graphql:schema` and `exp:graphql:query` work whether or not the API is switched on (a schema for development, a
query in a deploy check), and the console may always read the schema. A query runs as the anonymous user unless `--as`
names a login.

| Exit code | `status` | `schema` | `query` |
|---|---|---|---|
| 0 | the library is available | printed or written | an answer without errors |
| 1 | | the file could not be written | an answer with errors |
| 2 | wrong options | wrong options | wrong options: no query, variables not JSON, an unknown user |
| 3 | the library is missing | the library is missing | the library is missing |

The machine output is versioned: `exp-graphql-status/1` (`status --format=json`) and `exp-graphql-schema-summary/1`
(`schema --format=summary`) name themselves in their `schema` field.

**A stable contract.** Like the [`exp:template:*` commands](../../guides/template-syntax-check.md#compatibility-promise),
the `exp:graphql:*` commands are a public interface meant to hold for decades: command names and aliases, option names,
exit codes, formats and the fields of the machine output are never removed, renamed or changed in meaning; new ones are
only added. The same holds for the error codes of the API, the names of the schema's own types (`Content`, `Location`,
`ContentConnection`, `Image` ...) and the fields of the interface `Content`. A test in the kernel
(`expGraphQLCommandContractTest`) keeps it.

## The library

The API runs on [webonyx/graphql-php](https://github.com/webonyx/graphql-php) 15. The root `composer.json` requires
`webonyx/graphql-php ^15`, which installs on every PHP line Exponential 6.0.15 supports, PHP 7.4 through 8.5, with
Composer's security advisory blocking switched on; this was checked with Composer on each PHP line. A Composer
installation therefore has it.

An installation that does not install the kernel's libraries with Composer gives the library a directory of its own:

```bash
mkdir -p var/lib/graphql-vendor
cd var/lib/graphql-vendor && composer require webonyx/graphql-php:^15
```

```ini
[GraphQLSettings]
LibraryPath=var/lib/graphql-vendor
```

`LibraryPath` is a directory with `vendor/autoload.php` (a Composer directory that requires the library), or the
package itself (the directory holding `src/GraphQL.php`), relative to the installation root or absolute. The
environment variable `GRAPHQL_LIBRARY_PATH` is read the same way when `LibraryPath` is empty. Without the library, the
end points answer `503`, Setup > GraphQL says so, and `exp:graphql:status` exits with 3; nothing else in the
installation depends on it.

The API keeps no state between requests (only the fact that the library is loaded), so it runs unchanged in
[Velocity](velocity-web-server.md)'s persistent workers, which read the settings of each request anew.

## Settings

`settings/graphql.ini`, every setting with its default: [[GraphQLSettings]](../../examples/ini-settings/graphql/GraphQLSettings.md)
(`Enabled`, `SiteAccessList[]`, `LibraryPath`, `NamingStyle`, `ClassList[]`, `ExcludedClassList[]`,
`HiddenFieldList[]`, `HiddenDatatypeList[]`, `Introspection`, `MaxDepth`, `MaxComplexity`, `MaxListLimit`,
`DefaultListLimit`, `PersistedQueries`, `AutomaticPersistedQueries`, `MaxPersistedQueries`, `PersistedQueriesOnly`,
`PersistedQueryDirectoryList[]`, `MaxQueryLength`, `ResponseCache`, `ResponseCacheTtl`, `CredentialCacheTtl`, `RateLimit`,
`RateLimitAnonymous`, `RateLimitWindow`, `SchemaExtensionList[]`, `ImageAliasList[]`, `Debug`) and [[GraphQLNaming]](../../examples/ini-settings/graphql/GraphQLNaming.md)
(`TypeNames[]`, `FieldNames[]`). Never edit the shipped file: put changes in `settings/override/graphql.ini.append.php`
or a siteaccess's `graphql.ini.append.php`.

In `settings/rest.ini`: `[ApiProvider] ProviderClass[graphql]=expGraphQLRestProvider` (the route `/api/graphql`;
a provider's address now works without a slash after it), `[ApiKeySettings] Scopes[graphql:read]` and
`RouteScopes[expGraphQLRestController_query]`, `[RouteSettings] OptionalAuthentication[]` and
`[expGraphQLRestController_CacheSettings]`. The module `graphql` is in `module.ini [ModuleSettings] ModuleList[]`, its
views and policies are listed in [module views](../../examples/module-views/graphql/README.md) and
[policies](../../examples/policies/graphql.md), and Setup > GraphQL is `menu.ini [Leftmenu_setup] Links[graphql]`.

## What comes next

Phase 1 reads content. Later phases are planned to add, without dates yet:

- **Caching and persisted queries**: answers kept per user group and invalidated by publishing, and queries stored on
  the server and called by id.
- **Mutations**: creating, editing, publishing and removing content, with the same policies as the administration.
- **Search**: full-text and filtered search through the search engine of the installation.
- **Tags**: `eztags` as a type of its own, and lists by tag.
- **Layouts**: the layouts, zones and blocks of the page builder.
- **Webhooks**: a call to another system when content changes.
- **Subscriptions**: changes pushed to a client as they happen.
- **Starter kits**: front-end projects that read the API from the first minute.

None of this is part of 6.0.15 yet. The names released in phase 1 stay as they are; the later phases only add.

## Related pages

- [API keys](../../guides/api-keys.md): personal keys, their scopes and how the REST layer resolves them
- [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md): the REST interface and its OAuth tokens
- [Remote services: the whole admin as an HTTP API (expservices)](remote-services-expservices.md)
- [Roles and policies](../../guides/roles-and-policies.md)
- [The 6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
