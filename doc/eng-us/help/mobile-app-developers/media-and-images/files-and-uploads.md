---
title: Files and uploads
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 530
summary: Download files from Exponential in an app (expfile services, the content/download URL, time-limited signed links for clients without a session) and why uploading photos or files from a device needs a custom endpoint - with what to put in the brief for one.
keywords: files, downloads, ezbinaryfile, expfile, downloadurl, signedurl, content/download, uploads, photos, camera, deniedInputTypes, custom endpoint, mobile app
type: help
---
# Files and uploads

This page is for app developers whose app opens documents (PDFs, brochures) from Exponential, or would like to let
users send photos and files. Downloads are covered by the remote services; uploads are not, and this page says what
to do instead.

## Contents

1. [Downloading files](#downloading-files)
2. [Uploads: what is and is not there](#uploads-what-is-and-is-not-there)
3. [A brief for an upload endpoint](#a-brief-for-an-upload-endpoint)

## Downloading files

File fields (datatype `ezbinaryfile`) come through REST as the datatype's string form. For an app, the `expfile`
services are clearer (declared in `extension/expservices/classes/media/expfileservices.php`):

| Service | Arguments | Gives |
|---|---|---|
| `expfile::attributes` | node id | the file fields of a node, with `has_content` |
| `expfile::info` | node id, field identifier | names, MIME type, size, download count |
| `expfile::downloadurl` | node id, field identifier | `url` and `absolute_url` of the site's `content/download` address; the download itself checks `content/read` |
| `expfile::signedurl` | node id, field identifier, seconds | a time-limited signed link, described as "for clients without a session" |

The `content/download` address checks the rights of the requesting user, and a native app usually has no site
session; that is what the signed link is for. Ask for it just before opening the file, with a short lifetime, and do
not store it.

## Uploads: what is and is not there

- **REST create** takes field values as form fields and has no documented file upload; the guides' examples set
  text fields only ([Creating and removing content](../rest-api/creating-and-removing-content.md)).
- **expservices** refuses remote writes to the datatypes `ezimage`, `ezbinaryfile`, `ezmedia`, `ezuser`,
  `ezpassword` and `ezflowblock`, "whose fromString() reads a local path or URL, or that hold credentials"
  (`expContentBase::$deniedInputTypes`).
- No service in the shipped catalog takes an uploaded file (the `expfile::safename` service only checks a file name).

So an app that sends photos, voice notes or documents needs an endpoint built for it.

## A brief for an upload endpoint

For the application creator who builds it (as a REST provider, an expservices domain or a module view; see
[Your own REST provider](../../creators/app-creators/rest-api.md#your-own-rest-provider) and
[Forms, CSRF protection and HTTP input](../../creators/app-creators/forms-csrf-and-http-input.md)):

- multipart upload of one file per request, with a maximum size and a list of allowed types, checked by content and
  not only by extension;
- the user it runs as, and the folder and class it creates (for example an `image` object below an "App uploads"
  folder), checked with `content/create` like every other write;
- an answer with the new node id, and a clear `413` or `415`-style error for files that are too large or of the wrong
  type;
- no file read from a URL or a server path supplied by the client;
- moderation: uploads land unpublished or in a section only editors see, if users are not trusted.

In the app: resize photos before upload, upload on Wi-Fi where the user prefers it, and retry an interrupted upload
from the start.

## Related pages

- [Writing through expservices](../json-endpoints/writing-through-expservices.md)
- [Files and downloads](../../creators/website-creators/files-and-downloads.md)
- [What the APIs do not do](../what-the-apis-do-not-do.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
