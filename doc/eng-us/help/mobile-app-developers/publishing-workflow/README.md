---
title: Publishing workflow for apps
audience: mobile-app-developer, app-creator, developer, editor
category: mobile-apps
order: 700
summary: The landing page of the publishing workflow pages for app developers - how editors' drafts, versions and publishing relate to what an app sees, previewing app screens before publishing, the user accounts and roles an app runs as, and the audit trail of app activity.
keywords: publishing, workflow, drafts, versions, preview, roles, policies, permissions, integration user, audit, editors, mobile app
type: help
---
# Publishing workflow for apps

This folder is for app developers who need to know how content gets from an editor to the app, and who set up the
accounts the app runs as. Editors work in the administration; the app sees only what is published and what its user
may read.

## Contents

1. [From draft to app](#from-draft-to-app)
2. [Pages in this folder](#pages-in-this-folder)

## From draft to app

```text
editor saves a draft  ->  (approval workflow, if any)  ->  published version  ->  REST read (may be cached)  ->  app
```

- The REST API reads the published version only.
- Approval and scheduled publishing are configured on the server; the app sees the item once it is published
  ([Drafts and pending items](../../../guides/drafts-and-pending.md), [Workflows](../../../guides/workflows.md)).
- Server and app caches add their delay ([Caching on the server](../offline-and-performance/caching-on-the-server.md)).
- What the app's user may read decides what appears at all
  ([Permissions for app users](permissions-for-app-users.md)).

## Pages in this folder

| Page | Answers |
|---|---|
| [Drafts, preview and publishing](drafts-preview-and-publishing.md) | What the app can see of drafts and versions, and how editors preview app screens |
| [Permissions for app users](permissions-for-app-users.md) | Integration users, roles, limitations, and auditing what the app does |

## Related pages

- [Publishing and versions](../../publishing-and-versions.md)
- [Roles and policies](../../../guides/roles-and-policies.md)
- [Headless Exponential: preview and drafts](../../creators/app-creators/headless-exponential.md#preview-and-drafts)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
