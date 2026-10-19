---
title: Members-only content
audience: businesses, website-creator, admin
category: business-solutions
order: 30
summary: How an Exponential member site keeps pages, documents and discussions for members - a members section, membership levels, public teasers, protected downloads, members-only search results, forums, and how to test it as a visitor.
keywords: members-only content, members area, members section, membership levels, protected downloads, teasers, Anonymous role, content/read, sections, forums, testing as a visitor, RequireUserLogin
type: help
---
# Members-only content

This page is for the people who run a member site and decide what is for members. It explains how Exponential keeps
member content away from the public, how to offer more to some members than others, how to tempt visitors to join
without giving content away, and how to check that nothing leaks.

## Contents

1. [The principle](#the-principle)
2. [A members area in five steps](#a-members-area-in-five-steps)
3. [Levels of membership](#levels-of-membership)
4. [Teasers for visitors](#teasers-for-visitors)
5. [Member documents](#member-documents)
6. [Search, menus and feeds](#search-menus-and-feeds)
7. [Forums and member contributions](#forums-and-member-contributions)
8. [Testing as a visitor](#testing-as-a-visitor)
9. [Checklist](#checklist)
10. [Related pages](#related-pages)

## The principle

Every visitor who is not signed in is the **Anonymous** user, and may only do what the Anonymous role allows. The
default Anonymous role has `content/read` limited to Section Standard. Content in any other section is therefore
invisible to visitors and gives "access denied" or a request to sign in
([What visitors may see](../../creators/website-creators/visitor-permissions.md#what-it-grants-by-default)).

Members-only content is simply content in a section that the Member role reads and the Anonymous role does not.

## A members area in five steps

The sections guide describes the exact procedure ([Sections, 13.1](../../../guides/sections.md#131-a-members-area)):

1. Create a section *Members area* (`members_area`).
2. Create a folder *Members* with the members' pages below it.
3. **Assign content**: give the *Members* folder and everything below it the *Members area* section.
4. Check logged out: the folder and its pages are gone from menus and ask for a sign-in.
5. Give the Member role `content/read` for Section( Members area ).

New pages created inside the folder take its section automatically, so editors do not need to think about it.

A whole site can require sign-in instead (`RequireUserLogin=true` in its siteaccess), which suits closed communities
with no public part ([A members-only area](../../creators/website-creators/member-registration-and-areas.md#a-members-only-area)).

## Levels of membership

| Level | Section it adds | Role |
|---|---|---|
| All members | Members area | Member: `content/read` Section( Members area ) |
| Full members | Full members | Full member: `content/read` Section( Full members ) |
| Board | Board | Board: `content/read` Section( Board ) |

Assign each role to the level's user group. Policies of several roles add up, so a full member who is also in the
Members group reads both sections ([Roles and policies](../../../guides/roles-and-policies.md)).

If the same item should move from "members only" to "public" after some time (for example a report that becomes
public after a year), an object state group can express it instead of moving the item
([Object states](../../../guides/object-states.md)).

## Teasers for visitors

A visitor who cannot read a page sees nothing of it. To advertise member content, publish a short public teaser in
Section Standard ("Members can read the full report") that links to the members page; the link leads non-members to
the sign-in, with registration offered there. Keep the teaser a separate object so that it can be public while the
full text is not.

## Member documents

Documents are File objects; every download goes through `content/download`, which checks that the person may read
the file. A file in the members section can only be downloaded by members, even if the link is shared
([Files and downloads, Protected downloads](../../creators/website-creators/files-and-downloads.md#protected-downloads)).
Do not link files directly from the storage directory. Upload limits, format and size labels and many-file uploads are
covered in [Documents and file libraries](../intranets/documents-and-file-libraries.md).

## Search, menus and feeds

- Search shows only what the person may read: visitors find teasers, members find full texts
  ([Site search](../../creators/website-creators/site-search.md)).
- Menus show only pages the person may read.
- An RSS export lists the newest objects below its sources ([RSS feeds](../../../guides/rss-feeds.md)); do not choose a
  members folder as a source of a public feed unless you mean to publish its titles. Test the feed logged out.

## Forums and member contributions

`ezwebin` ships the classes `forums`, `forum`, `forum_topic` and `forum_reply` with their templates
([ezwebin](../../../features/6.0/extensions/ezwebin.md)); they are present on the demonstration server. Members post
when their role has `content/create` for the topic and reply classes, limited to the forum's place. Comments under
articles work the same way ([What visitors may see, Giving visitors more](../../creators/website-creators/visitor-permissions.md#giving-visitors-more)).
Plan who moderates, and give that group `content/edit` and `content/remove` in the forum section only.

Star ratings for content are available with `ezstarrating`
([ezstarrating](../../../features/6.0/extensions/ezstarrating.md)).

## Testing as a visitor

Use a private browser window, not your admin session
([Testing as a visitor](../../creators/website-creators/visitor-permissions.md#testing-as-a-visitor)):

| Signed in as | Open | Expected |
|---|---|---|
| nobody | a members page | sign-in requested |
| nobody | a members document address | refused |
| nobody | search for a word only in member texts | no result |
| a member | the same page and document | shown |
| a member | a Board page | refused |

## Checklist

- [ ] A members section, assigned to the members folder.
- [ ] Member role reads it; Anonymous does not.
- [ ] Levels as separate sections and roles, if needed.
- [ ] Public teasers for important member content.
- [ ] Member documents inside the members section.
- [ ] Feeds checked logged out.
- [ ] Forum moderators named and limited to the forum section.
- [ ] The test table above passed.

## Related pages

- [Member registration](member-registration.md)
- [Sections and object states for site builders](../../creators/website-creators/sections-and-object-states.md)
- [Customer and partner areas](../extranets/customer-and-partner-areas.md)
- [Visitor permissions](../../creators/website-creators/visitor-permissions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
