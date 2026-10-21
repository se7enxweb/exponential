---
title: Members-only episodes
audience: podcaster
category: podcasting-streaming
order: 36
summary: How to keep bonus episodes, early releases or a premium back catalog for signed-in members on an Exponential site - sections and roles, the permission check on downloads, why a members-only file cannot be in a public podcast feed, and what is not available, such as private per-subscriber feeds.
keywords: members only, premium, bonus episodes, subscribers, paywall, sections, roles, content/read, protected download, private feed, members area, early access, Patreon
type: help
---
# Members-only episodes

This page is for shows that offer something to members only: bonus episodes, early access, a premium archive.
Exponential protects content with **sections** and **roles**: visitors who are not signed in see only what the
Anonymous role may read. The same check protects the audio files. What Exponential does not have is a private
podcast feed per subscriber.

## Contents

1. [How protection works](#how-protection-works)
2. [Setting up a members area for a show](#setting-up-a-members-area-for-a-show)
3. [Files are protected too](#files-are-protected-too)
4. [Members-only episodes and the podcast feed](#members-only-episodes-and-the-podcast-feed)
5. [What is not available](#what-is-not-available)
6. [Testing](#testing)

## How protection works

- Every item belongs to a **section** ([Sections](../../guides/sections.md)).
- **Roles** grant `content/read` for sections, subtrees or classes ([Roles and policies](../../guides/roles-and-policies.md)).
- The **Anonymous** role decides what visitors who are not signed in may see
  ([What visitors may see](../creators/website-creators/visitor-permissions.md)).
- A members role, given to a members user group, adds the members section
  ([Member registration and members-only areas](../creators/website-creators/member-registration-and-areas.md)).

## Setting up a members area for a show

For the administrator:

1. Create a section, for example **Members**.
2. Create a folder "Bonus episodes" in the show and assign it, with everything below it, to the section.
3. Give the members role `content/read` for the **Members** section; leave the Anonymous role without it.
4. Make sure the members user group has the members role, and decide how people become members (registration,
   import, a shop, or by hand).

The recipe in detail: [Sections and object states for site builders](../creators/website-creators/sections-and-object-states.md#recipe-a-members-area).

## Files are protected too

Episode files are delivered by `content/download`, which checks that the visitor may read the item before sending
the file ([Files and downloads](../creators/website-creators/files-and-downloads.md#protected-downloads)). A visitor
who is not signed in gets no file, even with the exact address. Do not copy files to a public place or link them from
`var/.../storage/`; that bypasses the check.

## Members-only episodes and the podcast feed

Podcast apps and directories fetch feeds and files **without signing in**. So:

- An episode only members may read cannot be played by an app from a public feed: the enclosure download is refused
  for the anonymous app.
- Do not put members-only episodes into the public show's feed source. Keep them in their own folder, outside the
  public feed's sources, or in a section the public feed's readers cannot see.
- Check the public feed signed out after setting up the area: it must list only public episodes.

Members listen on the site, signed in, with the player of the episode page.

## What is not available

| Not available | Instead |
|---|---|
| A private feed address per subscriber (a feed with a personal token) | members listen on the site; or use a podcast hosting service that offers private feeds |
| Sign-in inside podcast apps | not possible with the shipped feed |
| Time-limited early access that opens by itself | publish to the members folder, then move the episode to the public folder on the public date, by hand; moving keeps the item, so its `guid` stays the same |
| Payment for membership in the podcast feature | the shop and payment gateways are separate features ([Shop overview](../creators/website-creators/shop-overview.md)) |

## Testing

- [ ] Signed out: the bonus folder and its episodes do not appear in menus, lists or search.
- [ ] Signed out: the download address of a bonus episode does not deliver the file.
- [ ] Signed in as a test member: the episode page and its player work.
- [ ] The public podcast feed, signed out, lists no members-only episode.

Use a test member account with an `example.invalid` address, never a real member's account.

## Related pages

- [Sections and object states for site builders](../creators/website-creators/sections-and-object-states.md)
- [Member registration and members-only areas](../creators/website-creators/member-registration-and-areas.md)
- [Rights and licensing of what you publish](rights-and-licensing.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
