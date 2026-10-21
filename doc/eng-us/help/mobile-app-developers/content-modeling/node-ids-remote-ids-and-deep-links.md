---
title: Node ids, remote ids and deep links
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 420
summary: Identify content in an app so it survives reinstalls and moves - node ids versus object ids versus remote ids, configuring entry points by remote id, looking items up with expnode::getByRemoteId and expnode::getByPath, fullUrl for sharing, and opening links of the website inside the app.
keywords: node id, object id, remote id, nodeRemoteId, objectRemoteId, getByRemoteId, getByPath, expurlalias, resolve, fullUrl, URL alias, deep link, universal link, app link, share, mobile app
type: help
---
# Node ids, remote ids and deep links

This page is for app developers who store references to content: the folder the app starts from, favorites, links
shared from the website into the app. It explains which id to keep and how to resolve each kind of reference.

## Contents

1. [Three ids](#three-ids)
2. [Entry points by remote id](#entry-points-by-remote-id)
3. [Resolving a URL path](#resolving-a-url-path)
4. [Sharing and deep links](#sharing-and-deep-links)

## Three ids

| Id | Where | Stable? |
|---|---|---|
| `nodeId` | REST metadata, lists, links | stable on one installation while the node exists; a different number on another installation |
| `objectId` | REST metadata | stable on one installation; the same object keeps it when it is moved or gets more locations |
| `nodeRemoteId`, `objectRemoteId` | REST metadata | strings that packages and imports can set, so the same content can carry the same remote id on every installation |

Numbers differ between a test and a production installation, and between installations built from the same package
at different times. Keep numbers in caches, not in app configuration.

## Entry points by remote id

Configure the app's entry points (the news folder, the product catalog) by remote id, and look them up once at start:

```bash
curl -s -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     'https://example.com/ezjscore/call/expnode::getByRemoteId::<node remote id>?ContentType=json'
```

`expnode::getByRemoteId` answers the node (with `node_id`); `expobject::getByRemoteId` does the same for an object.
Both need `content/read`. Then use the node id with the REST API for the session. A developer with `content/edit` can
give a folder a readable remote id with `expnode::setRemoteId` ([Writing through expservices](../json-endpoints/writing-through-expservices.md)
covers the write rules); agree on it with the site team so nobody changes it later.

## Resolving a URL path

| Service | Argument | Access |
|---|---|---|
| `expnode::getByPath` | a URL alias such as `Media/Images` | `content/read` |
| `expurlalias::resolve` | a URL path; answers the action and the node | signed-in user |
| `expnode::url` | a node id; answers `url_alias` and `system_url` | `content/read` |

Percent-encode the path as one argument. Test a path with several levels on your server early: some web server
configurations refuse an encoded slash (`%2F`) in a URL. This is how an app turns a link of the website into a node.

## Sharing and deep links

- **Share** the node's `fullUrl` from the REST metadata: it is the public address of the page on the site, so anyone
  without the app can open it. Use the main location's address ([Reading a node](../rest-api/reading-a-node.md#locations)).
- **Open website links in the app** with your platform's verified links (universal links, app links). The app gets the
  path of the website URL, resolves it with `expnode::getByPath`, and opens the node; if the path does not resolve,
  open the URL in the browser.
- **Moved content**: when editors move or rename an item, its URL alias changes. Exponential keeps redirects for old
  aliases in many cases ([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)),
  but an app should prefer ids it stored over paths.
- The verification files your platform needs on the website (for example the association files under
  `/.well-known/`) are served by the web server; adding them is a hosting task.

## Related pages

- [URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md)
- [URLs and aliases](../../../guides/urls-and-aliases.md)
- [How Exponential content reaches an app](../how-exponential-content-reaches-an-app.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
