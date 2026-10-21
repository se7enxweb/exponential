---
title: Privacy for your family
audience: individual, customer
category: personal-sites
order: 1705
summary: The privacy decisions behind a family site on Exponential - what never goes online, children and consent, photos and their metadata, how private a private area really is, search engines, backups that contain family data, outside services, removal requests and what to do after a mistake.
keywords: privacy, family, children, consent, photos, location data, EXIF, private area, search engines, robots, backups, personal data, GDPR, removal, leak, family site
type: help
---
# Privacy for your family

This page is for individuals who run a site with their family's photos, news and dates. The software can keep
strangers out of a private area; it cannot decide what is wise to put there. That part is yours. This page is
general information, not legal advice.

## Contents

1. [Three questions before anything goes up](#three-questions-before-anything-goes-up)
2. [What never goes online](#what-never-goes-online)
3. [Children](#children)
4. [Photos](#photos)
5. [How private is private?](#how-private-is-private)
6. [Search engines](#search-engines)
7. [Backups and exports](#backups-and-exports)
8. [Outside services](#outside-services)
9. [Requests to remove something](#requests-to-remove-something)
10. [After a mistake](#after-a-mistake)

## Three questions before anything goes up

1. **Would the person shown agree?** If you are not sure, ask.
2. **Could a stranger use it** to find, follow or impersonate someone?
3. **Would it hurt if it leaked?** Every private site can leak through a forwarded screenshot.

If any answer is uncomfortable, do not upload it.

## What never goes online

Even in a private area, leave out: home addresses, phone numbers, school and daycare names, travel dates while
people are away, full dates of birth of children, health details, identity documents, passwords and answers to
security questions.

## Children

- Ask parents before showing other people's children.
- Ask older children themselves; their wishes count.
- Prefer group photos and photos where faces are not the subject.
- The image editor can pixelate a face or a name tag in a selection before you publish
  ([Editing images with ezie](../portfolios/editing-images-with-ezie.md)).

## Photos

- **Location data**: phones store where a photo was taken. The original you upload is kept as it is (the `original`
  image variation), so remove location data on your phone or computer before uploading.
- **Backgrounds**: house numbers, car plates, street signs, school logos.
- **Resolution**: upload what is needed for viewing, not print quality
  ([Uploading many images](../portfolios/uploading-many-images.md)).

## How private is private?

| Protected by your roles | Not protected the same way |
|---|---|
| Pages in the private section | Image files: served directly by the web server; anyone with the exact image address can open it |
| File downloads (attachments) | What relatives copy, screenshot or forward |
| Search results on your site | RSS exports you create for private content |

Details: [Private areas with roles and sections](private-areas-with-roles-and-sections.md). Two-step sign-in reduces
the risk of a stolen password ([Two-factor sign-in](../running-it/two-factor-sign-in.md)).

## Search engines

Search engines index what anonymous visitors can read. Private sections are not readable, so they are not indexed.
A fully private site (`RequireUserLogin=true`) shows them nothing at all. Do not rely on `robots.txt` to hide
anything: it is a request, not a lock ([robots.txt](../../creators/website-creators/robots-txt.md)).

## Backups and exports

A backup of a family site is a copy of everything private: photos, accounts, addresses if you stored them.

- Encrypt backups where you can (the backup manager of `git_manager` offers encryption)
  ([Backups for one person](../running-it/backups-for-one-person.md)).
- Store them where only you (and your deputy) can get at them.
- Delete old generations on a schedule.

## Outside services

Every outside service sees some data: an analytics script sees visits, an embedded video player sees viewers, an AI
provider sees the text you send it. On a family site, think twice about each one. If you use the writing assistant,
do not send private family text to a provider ([The writing assistant](../running-it/writing-assistant-expai.md)).

## Requests to remove something

When a relative asks for something about them to be removed: remove it promptly, empty it from the trash, and say
when it is done. Old versions of an object can still hold the removed text or image; remove those versions too
([Publishing and versions](../../publishing-and-versions.md), [Data retention and deletion](../../businesses/security/data-retention-and-deletion.md)).

## After a mistake

1. Take the item down (move it to the trash, then remove it).
2. If an account was misused, disable it and reset passwords
   ([Inviting family members](inviting-family-members.md#when-someone-should-no-longer-have-access)).
3. Tell the people affected, plainly.
4. If an image address circulated, removing the object removes the file; check that the address no longer opens.
5. Note what happened and what you changed, so it does not happen again.

## Related pages

- [Family sites](README.md)
- [Family privacy checklist](../checklists/family-privacy-checklist.md)
- [GDPR basics](../../businesses/security/gdpr-basics.md)
- [Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
