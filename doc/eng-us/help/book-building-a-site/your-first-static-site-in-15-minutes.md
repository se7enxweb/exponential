---
title: "Your first static site in 15 minutes: the club site on GitHub Pages"
audience: builder, admin
category: books
order: 180
summary: The last chapter - the finished club site published as a free static copy on GitHub Pages, with the setup wizard, a build, a check and a publish, and what changes for the guestbook and the members page.
keywords: static publishing, GitHub Pages, github.io, free hosting, free website, static site, club site, publish, tutorial, gh-pages, Building book
type: help
---
# Your first static site in 15 minutes: the club site on GitHub Pages

The club site is finished. The club has no budget for hosting, and the server it runs on sits in a member's office.
This chapter puts a copy of the public site on GitHub Pages for free, while the club keeps editing on that server.

## What you'll have at the end

The club site at `https://<account>.github.io/chess-club/`, published from **Setup > Static publishing**, and a way
to publish it again after each news item.

## What stays and what changes

| Page of the club site | On the static copy |
|---|---|
| Welcome page, news, archive, members list, links tree | the same |
| Guestbook entries | shown as they were at the last publish |
| Guestbook form | posts to the live site, or a short note with a link (step 7 of the wizard, **Forms**) |
| Members' login | left out |

The guestbook still works on the club's own server; approved entries reach the copy with the next publish. That is a
good example of the split static publishing makes: reading is public and free, writing stays with Exponential.

## The steps

Follow [Make a free website in 15 minutes](../static-publishing/make-a-free-website-in-15-minutes.md) with these
values:

1. Repository `chess-club` on GitHub, public, empty; a fine-grained token for it in **Setup > Secrets** as
   `github_chess_club`.
2. Wizard: id `club`, the club's siteaccess, GitHub Pages, the token, branch `gh-pages`, relative links, public
   address `https://<account>.github.io/chess-club/`.
3. **Leave out** the members' area if it has pages only members should see: path `/members/private/*`. Pages
   Anonymous cannot read are never in the copy anyway.
4. Build, open the preview, click through the news and the links tree, publish.

![The preview: what changes, the size against the limits and the buttons Publish and Discard](../images/static-publishing/preview-light.webp)

## After each news item

Press **Build** and **Publish** again, or let the site publish itself when content is published
([Publish automatically](../static-publishing/publish-automatically.md)). Every publish is kept, so a mistake is one
rollback away.

## Next step

Back to the [start of the book](README.md), or the whole [static publishing manual](../static-publishing/README.md).
