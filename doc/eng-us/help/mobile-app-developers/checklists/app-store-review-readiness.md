---
title: App store review readiness
audience: mobile-app-developer, developer, project-manager
category: mobile-apps
order: 930
summary: General advice for preparing an app on Exponential content for app store review - a reviewer account, content that is present and stable, sign-in and account deletion questions, privacy statements about what the app sends, accessibility of images and text, and keeping the review build away from demos. Not a summary of any store's rules.
keywords: app store, review, submission, reviewer account, demo account, privacy, data collection, account deletion, accessibility, alternative text, content moderation, user generated content, mobile app
type: help
---
# App store review readiness

This page is for app developers and product owners who submit an app that shows Exponential content. It is general
advice about the parts of a review that depend on the content server. The rules of each store change and are not
repeated here: read the current guidelines of the store you submit to, and treat this page as a list of things to
prepare on the Exponential side.

## Contents

1. [A reviewer can use the app](#a-reviewer-can-use-the-app)
2. [Sign-in and accounts](#sign-in-and-accounts)
3. [Privacy statements](#privacy-statements)
4. [Content that users add](#content-that-users-add)
5. [Accessibility](#accessibility)

## A reviewer can use the app

- [ ] The review build points at production or at a stable staging installation, never at a public demo that is
      rebuilt on a schedule ([Testing against a demo](../testing/testing-against-a-demo.md#rules-for-shared-demos)).
- [ ] If the app needs a sign-in, there is a reviewer account on that installation with the same role as real users,
      and its credentials go only into the store's review notes.
- [ ] The folders the app shows have content during the review; an empty list looks like a broken app.
- [ ] The reviewer account cannot change or remove real content.

## Sign-in and accounts

- [ ] If users sign in with their Exponential account through OAuth, the sign-in opens in the system browser and comes
      back to the app ([OAuth2 authorization code flow](../auth/oauth2-authorization-code-flow.md#step-2-the-authorize-page)).
- [ ] Users who lack `oauth/authorize` see a clear message instead of a dead end (the site redirects with
      `error=access_denied`).
- [ ] If the app lets people create accounts, plan how a user asks for the account to be deleted; deleting users is
      an administrator's task on the site, so the app needs a route to that process (a form, a support address).
- [ ] Sign-out works and removes tokens ([Storing secrets on devices](../auth/storing-secrets-on-devices.md#signing-out)).

## Privacy statements

Store forms ask what data the app collects and sends. On the Exponential side, list honestly:

- [ ] Which user data the app sends to the installation (sign-in, content the user creates).
- [ ] That requests are recorded by the server: API key and token events in the audit trail with the client address
      for key use ([Auditing and limits](../../../guides/api-keys.md#8-auditing-and-limits)), and the web server's logs.
- [ ] Which third parties your back end or analytics add.
- [ ] Where the installation is hosted, if your privacy text names it. The organization's data protection basics:
      [GDPR basics](../../businesses/security/gdpr-basics.md) (basics, not legal advice).

## Content that users add

- [ ] If users can post content, new items land in a folder or state that editors review before the public sees
      them, or there is a way to report and remove content quickly.
- [ ] Editors know where app submissions arrive and who moderates them.
- [ ] Writes are limited by role to that folder ([Permissions for app users](../publishing-workflow/permissions-for-app-users.md)).

## Accessibility

- [ ] Every image has an accessibility label; use the alternative text from `expimage::alt` or `expimage::info`, and
      ask editors to fill it ([Images in REST answers](../media-and-images/images-in-rest-answers.md#alternative-text-and-image-information)).
- [ ] Text from rich text fields scales with the user's font size settings
      ([Rich text in apps](../content-modeling/rich-text-in-apps.md)).
- [ ] Right-to-left languages mirror the layout if the app supports them
      ([Multilingual content](../content-modeling/multilingual-content.md#fallbacks-in-the-app)).

## Related pages

- [Launch checklist for apps](launch-checklist.md)
- [Security checklist for apps](security-checklist.md)
- [Testing accessibility](../../creators/website-creators/testing-accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
