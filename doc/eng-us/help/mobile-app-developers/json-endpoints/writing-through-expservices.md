---
title: Writing through expservices
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 330
summary: Create, update and remove content from an app with the expservices node services - POST with a token and no form token, expnode::create with class, attributes as JSON and language, expnode::update which publishes a new version, expnode::remove with the trash, the fields that cannot be written remotely, and the audit of every write.
keywords: write, create, update, remove, expnode::create, expnode::update, expnode::remove, POST, attributes, JSON, language, remote_id, move_to_trash, form token, X-CSRF-Token, deniedInputTypes, audit, mobile app
type: help
---
# Writing through expservices

This page is for app developers whose app changes content in ways the REST API does not offer, above all updating an
existing item. The node services are declared in `extension/expservices/classes/content/expnodeservices.php`; the
rules for writes are in the [expservices reference](../../../bc/6.0/backend_ezjscore_services.md#writes).

## Contents

1. [Rules for every write](#rules-for-every-write)
2. [Create](#create)
3. [Update](#update)
4. [Remove](#remove)
5. [Fields that cannot be written](#fields-that-cannot-be-written)
6. [After a write](#after-a-write)

## Rules for every write

- A write needs `POST`; its arguments are form fields in the body, except the ids that the service takes in the URL.
- With an expservices token (`Bearer` and `X-Exp-Token`), no form token is needed. With a session cookie, fetch the
  form token from `expsession::token` and send it as the field `ezxform_token` and the header `X-CSRF-Token`.
- The service checks its policy as the calling user. A missing policy answers `403` inside the envelope.
- Every successful write records the audit event `service.<domain>.<method>`.

## Create

```bash
curl -s -H "Authorization: Bearer $EXPSERVICES_TOKEN" -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     --data-urlencode 'class=article' \
     --data-urlencode 'attributes={"title":"Report from the field","intro":"<p>Short text</p>"}' \
     --data-urlencode 'language=eng-US' \
     'https://example.com/ezjscore/call/expnode::create::<parent node id>?ContentType=json'
```

`expnode::create` needs `content/create` and publishes at once. POST fields: `class`, `attributes` (a JSON map of
identifier to string), `language`, and optionally `remote_id`. The service refuses a class the user may not create
below that parent (`403`), checks the class's required fields, and answers `409` when the `remote_id` is already in
use. Setting a `remote_id` of your own (for example a UUID made on the device) therefore lets the app retry safely:
a second attempt answers `409` instead of creating the item twice
([Node ids, remote ids and deep links](../content-modeling/node-ids-remote-ids-and-deep-links.md)). The field
identifiers in the example are placeholders; use those of your class.

## Update

```bash
curl -s -H "Authorization: Bearer $EXPSERVICES_TOKEN" -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     --data-urlencode 'attributes={"title":"Corrected title"}' --data-urlencode 'language=eng-US' \
     'https://example.com/ezjscore/call/expnode::update::<node id>?ContentType=json'
```

`expnode::update` needs `content/edit`, changes the attributes you send and publishes a new version. Fields you do not
send keep their values. Smaller changes have their own services: `expnode::rename` (POST `name`),
`expnode::setPriority`, `expnode::hide`, `expnode::reveal`.

## Remove

```bash
curl -s -H "Authorization: Bearer $EXPSERVICES_TOKEN" -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     -X POST 'https://example.com/ezjscore/call/expnode::remove::<node id>?ContentType=json'
```

`expnode::remove` needs `content/remove` and removes a node and its subtree. The POST field `move_to_trash` defaults to
`1`, so the content goes to the trash, from where an editor can restore it. Large removals may be routed to a content
job and answer with the job instead of `{removed}`.

## Fields that cannot be written

The node services never write fields of the datatypes `ezimage`, `ezbinaryfile`, `ezmedia`, `ezuser`, `ezpassword`
and `ezflowblock` remotely: their string import reads a local path or URL, or they hold credentials
(`expContentBase::$deniedInputTypes`). An app therefore cannot attach photos or files through these services
([Files and uploads](../media-and-images/files-and-uploads.md)).

## After a write

- Read the item again (by node id, with the REST API) to show what was published; workflows on the server may have
  changed it.
- Invalidate the app's cache for the item and for its parent list
  ([Caching in the app](../offline-and-performance/caching-in-the-app.md)).
- If a write timed out, check for the item by its `remote_id` (`expnode::getByRemoteId`) before you retry.

## Related pages

- [Calling expservices from an app](calling-expservices-from-an-app.md)
- [Creating and removing content](../rest-api/creating-and-removing-content.md)
- [Permissions for app users](../publishing-workflow/permissions-for-app-users.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
