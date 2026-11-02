---
title: "Deliver client sites as static sites"
audience: website-creator, editor
category: website-creation
order: 751
summary: Build a client's site in Exponential, keep the editing installation private, and hand over a fast static public site on GitHub Pages or any git host - with the setup, the handover and what to tell the client.
keywords: website creator, agency, freelancer, client site, handover, free hosting, static site, GitHub Pages, gh-pages, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing, maintenance
type: help
---
# Deliver client sites as static sites

Many client sites are brochures, portfolios or event pages that change now and then. For those, you can build in
Exponential as usual and deliver the public site as a static copy: no hosting bill for the client, almost no
security maintenance, and the editing installation can stay on a server only the client's editors reach.

## What you'll have at the end

- The client's public site on GitHub Pages (or another git host) at the client's domain.
- An editing installation the client uses as before, with a **Publish** button or automatic publishing.
- A repository the client owns, with every publish tagged.

## Is the project a fit?

Check with the client before you quote: [Is static right for my site?](../../static-publishing/is-static-right-for-my-site.md)
A shop, bookings, member areas or heavy forms need a server; a static copy is then the wrong tool.

## The setup

1. Build the site as usual. Use `ezurl` and `ezdesign` for links so the build can rewrite them.
2. Have the client create the GitHub account or organization and the repository, and give you access. The site must
   never depend on your personal account.
3. Make the target with the wizard, with a deploy key or a token that only reaches that repository. See
   [Connect to GitHub](../../static-publishing/connect-to-github-token-key-or-app.md).
4. Set the client's domain: [Use your own domain](../../static-publishing/use-your-own-domain.md).
5. Decide with the client who publishes: editors themselves, or a reviewer. Give the policies to match:
   [Who may publish](../../static-publishing/who-may-publish-roles-and-policies.md).

## The handover

Tell the client, in writing:

- where they edit, and that changes go public only on publish (or on the schedule you set);
- what the static site does not do (forms go to the live site or a link, no logins);
- how to go back: Setup > Static publishing > History > roll back;
- who owns the GitHub account, the domain and the token.

Send them [What changes for editors when the site is static](../../what-changes-for-editors-when-the-site-is-static.md).

## Maintenance you can offer

Updates of the editing installation, checking the publish notifications, the yearly domain renewal. See
[Notifications and webhooks](../../static-publishing/notifications-and-webhooks.md).

## Make yours now

Try it on a demo client first: [Make a free website in 15 minutes](../../static-publishing/make-a-free-website-in-15-minutes.md).
