---
title: Creating and removing content
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 180
summary: Write content from an app through the REST API at v2 - create and publish with parentNodeID, classIdentifier, languageLocale and the field values as form fields or JSON, the 201 answer, updating with POST or PUT as a new version, removing with DELETE, the permission checks, scopes for API keys and the _method override.
keywords: create, publish, POST, PUT, content/node/create, parentNodeID, classIdentifier, languageLocale, form fields, JSON, 201, delete, remove, DELETE, _method, update, new version, expContentWriter, content/create, content/edit, content/remove, REST, mobile app
type: help
---
# Creating and removing content

This page is for app developers whose app adds or changes content: a report from the field, a user submission, an
entry imported from a device. It explains the write routes of the REST API (create, update, remove), which answer at
v2 only. Update needs `ezprestapi` 1.2.9, which writes through the kernel of Exponential 6.0.15; on an older kernel
its write routes answer `501`.

## Contents

1. [Create and publish](#create-and-publish)
2. [How to send the body](#how-to-send-the-body)
3. [Remove](#remove)
4. [Update](#update)
5. [Permissions and scopes](#permissions-and-scopes)
6. [Design advice for writing apps](#design-advice-for-writing-apps)

## Create and publish

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/create \
     -d parentNodeID=<parent node id> -d classIdentifier=folder -d languageLocale=eng-US -d name='From a script'
```

```json
{"message":"Created","objectId":<object id>,"nodeId":<node id>}
```

| Field | Required | Meaning |
|---|---|---|
| `parentNodeID` | yes | the node below which the item is created |
| `classIdentifier` | yes | the content class, for example `article` |
| `languageLocale` | yes | the language to create in, for example `eng-US`; without it the answer is `400` |
| any field identifier of the class | as the class needs | the value of that field, for example `name` for a folder or `title` for an article |

The answer is `201` with `objectId` and `nodeId` (the new main node). The item is published at once: the controller
creates it through the kernel's `expContentWriter`, and "a create publishes when `content/create` allows it, and
the publish workflow runs as for the content module" ([Permissions of the REST interface](../../../guides/api-keys.md#32-permissions-of-the-rest-interface)).

Field values are strings. A rich text field is parsed by the online editor's input parser, so it takes simple HTML
such as `<p>Text</p>`; links and embeds become relations. An image or file field (`ezimage`, `ezbinaryfile`,
`ezmedia`) takes a public `http(s)` URL, or `<URL>|<alternative text or file name>`, which the server downloads: a
server path, `file://`, credentials in the URL and addresses in private, loopback, link-local or reserved networks are
refused, redirects are not followed, and a file may be at most 50 MB. No route takes a file from the device itself
([Files and uploads](../media-and-images/files-and-uploads.md)). Every other field takes its text form.

## How to send the body

Send the fields **form encoded** (`application/x-www-form-urlencoded`, which `curl -d` does, or
`multipart/form-data`), or as a **JSON object** with `Content-Type: application/json` (`ezprestapi` 1.2.9). Create and
update read both. In app code:

```kotlin
// Kotlin, OkHttp
val body = FormBody.Builder()
    .add("parentNodeID", parentId.toString())
    .add("classIdentifier", "article")
    .add("languageLocale", "eng-US")
    .add("title", title)
    .build()
```

```swift
// Swift: percent-encode each value before joining
request.httpMethod = "POST"
request.setValue("application/x-www-form-urlencoded", forHTTPHeaderField: "Content-Type")
request.httpBody = "parentNodeID=\(parentId)&classIdentifier=article&languageLocale=eng-US&title=\(encodedTitle)"
    .data(using: .utf8)
```

## Remove

```bash
curl -s -X DELETE -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/delete/<node id>
```

```json
{"message":"Removed","nodeId":<node id>,"objectId":<object id>}
```

This removes the node's **object** with all its locations and everything below them. `POST` to the same path works
too, and so does a `POST` with the form field `_method=DELETE`, which the request parser turns into a `DELETE` for
clients that cannot send one. Ask the user to confirm in the app; there is no undo route.

## Update

```bash
curl -s -X PUT -H "Authorization: Bearer $EXP_API_KEY" -H 'Content-Type: application/json' \
     https://example.com/api/ezp/v2/content/node/<node id> -d '{"name": "New title", "intro": "<p>New intro</p>"}'
```

```json
{"message":"Updated","objectId":<object id>,"nodeId":<node id>,"version":<version>,"languageLocale":"eng-US"}
```

`POST` or `PUT` on `/api/ezp/v2/content/node/<id>` (or a `POST` with `_method=PUT`) publishes a **new version** of the
node's object with the fields sent; fields left out keep their values. `languageLocale` is optional and defaults to
the object's main language; a language the object does not have yet adds that translation. The new version goes
through the publish operation, so workflows run: when one holds the publication, the answer is `202` with
"Waiting for a workflow". The values follow the same rules as for a create. A `400` means no values, an unknown
field, a value that is not text, a file value that is not a public `http(s)` URL, or an unknown language.

On a server older than Exponential 6.0.15 the route answers `501` with `not_implemented`; there, change an item with
`expnode::update` of the remote services ([Writing through expservices](../json-endpoints/writing-through-expservices.md)).

## Permissions and scopes

| Call | Policy checked for the current user | Scope of a personal API key |
|---|---|---|
| create | `content/create` of the class below the parent, in the language (Class, ParentClass, Section, Node, Subtree and Language limitations) | `publish` |
| remove | `content/remove` of every location and of everything below them | `remove` |
| update | `content/edit` of the node in the language written (and `user/selfedit` for one's own user) | `edit` |

A refusal answers `403` with `access_denied` and a message such as "You may not create 'article' below node 60 in
eng-US (content/create)." and changes nothing ([api-keys guide, 3.2](../../../guides/api-keys.md#32-permissions-of-the-rest-interface)).

## Design advice for writing apps

- **Create below one folder per app**, and give the app's users `content/create` only there, for only the classes
  they need ([Permissions for app users](../publishing-workflow/permissions-for-app-users.md)).
- **Make creates idempotent in the app.** A create that timed out may have succeeded; before you retry, check for
  the item, for example by a unique title or by listing the folder sorted by `published`.
- **Queue writes made offline** and send them when the device is online again, one at a time
  ([Syncing and offline reading](../offline-and-performance/syncing-and-offline-reading.md)).
- **Show the server's message** of a `400` or `403` to the user; it names what is missing or refused.

## Related pages

- [Errors and status codes](errors-and-status-codes.md)
- [REST API v1 and v2: examples with curl](../../../guides/rest-api-v1-and-v2.md#4-examples-with-curl)
- [Creating content in code](../../creators/app-creators/creating-content-in-code.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
