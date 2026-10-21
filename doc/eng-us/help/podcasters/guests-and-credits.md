---
title: Guests and credits
audience: podcaster
category: podcasting-streaming
order: 32
summary: How to present guests, hosts and crew of a show on an Exponential site - person items and relation fields, the Authors datatype, credits in show notes, what the feed says about authors - and how to treat guests' personal data and consent.
keywords: guests, hosts, credits, crew, person, ng_person, relations, ezobjectrelationlist, ezauthor, authors, itunes:author, consent, personal data, guest page, contributors
type: help
---
# Guests and credits

This page is for producers who credit the people on and behind a show. A guest can be a line in the show notes, or a
page of their own that every episode with them links to. Both work; the second makes "all episodes with this guest"
possible.

## Contents

1. [Three ways to credit](#three-ways-to-credit)
2. [Guest pages](#guest-pages)
3. [Linking guests to episodes](#linking-guests-to-episodes)
4. [Hosts and crew](#hosts-and-crew)
5. [What the feed says](#what-the-feed-says)
6. [Consent and personal data](#consent-and-personal-data)

## Three ways to credit

| Way | How | Good for |
|---|---|---|
| In the show notes | a line with name, role and link ([Writing show notes](writing-show-notes.md)) | occasional guests |
| A person item per guest, related to episodes | a relation field on the episode class | recurring guests, a guest archive |
| An **Authors** field (`ezauthor`) on the episode | name and e-mail per contributor | internal records of who worked on it |

## Guest pages

On installations with the media site package, the **Person** class (`ng_person`) has **Name**, **CV** (rich text),
**Image**, **Copyright** (for the photo credit), **Facebook URL**, **Twitter URL**, **Instagram URL** and **Meta
Data**. On other sites, your administrator can create a similar class, or use an article.

1. Create a folder "Guests" ([Setting up the show structure](setting-up-the-show-structure.md)).
2. Create a person item per guest: name, a short biography in the guest's own words, a photo they supplied, its
   credit, and only the links they want shown.

## Linking guests to episodes

Give the episode class a field of the type **Object relations** (`ezobjectrelationlist`), named for example
**Guests** ([Building an episode class](building-an-episode-class.md)). In the episode's edit form, **Add existing**
picks the person items ([Relations and images](../editing-content.md#relations-and-images)).

What the site then shows depends on the design: the episode page can list its guests, and a guest's page can list the
episodes that relate to it (reverse relations). Ask your designer for both views; the techniques are on
[Relations and reuse](../creators/website-creators/relations-and-reuse.md).

## Hosts and crew

- **On the site**: hosts are persons like guests; relate them through a separate field (for example **Hosts**) so
  lists can tell hosts from guests.
- **In the audio**: say the credits at the end of the episode as well.
- **The Authors datatype** stores names and e-mail addresses. Its standard view shows every name **with its e-mail
  address** (`design/standard/templates/content/datatype/view/ezauthor.tpl`); do not use it for addresses that must
  stay private, or have the design leave it out of the public page.

## What the feed says

The podcast feed has two author elements, and neither comes from guests:

| Element | Source |
|---|---|
| channel `itunes:author` | the export's **Author** field: the show's author as listeners should see it |
| item `itunes:author` | the name of the **user who created** the episode item (`podcastItemList()` in `kernel/classes/ezrssexport.php`) |

So the user account that creates an episode is named in the feed. If that should be the host, let hosts create their
episodes with their own accounts; a shared technical account puts its name into every item. Guests are credited in the
description, if it is mapped to the show notes.

## Consent and personal data

Guests are people with rights over their personal data, and a published episode is public and long-lived.

- Ask before you publish a guest's photo, biography, links or e-mail address, and publish only what they agree to.
- Never publish a guest's private phone number, address or e-mail address. The **Owner email** of the feed is public;
  it is the show's address, not a guest's.
- Record consent outside the public content (your team's records); a hidden field is still in the database and in
  exports.
- When a guest withdraws consent, update or hide their person item and check the episodes' notes
  ([Hiding and retiring episodes](hiding-and-retiring-episodes.md)).

The press section covers the interview side: [Podcast and radio guests](../press/podcast-and-radio-guests.md) and
[Privacy on camera](../press/privacy-on-camera.md).

## Related pages

- [Writing show notes](writing-show-notes.md)
- [Rights and licensing of what you publish](rights-and-licensing.md)
- [Compliance and GDPR](../hosting/security/compliance-and-gdpr.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
