---
title: Maps and privacy
audience: government, admin, project-manager
category: public-sector
order: 60
summary: What happens to visitor and editor data when a public site shows Google maps through ezgmaplocation, how to decide, what to put in the privacy notice and the Content Security Policy, and how to run the location features without any third party.
keywords: maps privacy, Google Maps, third party, API key, Content Security Policy, consent, privacy notice, data protection, without map, geolocation
type: help
---
# Maps and privacy

This page is for administrators, data protection officers and managers. With an API key, `ezgmaplocation` loads the
Google Maps JavaScript API from `maps.googleapis.com` in the edit form and on pages that show a location (the edit and
view templates of the extension). Google then receives requests from the browsers of editors and visitors.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What leaves the browser](#what-leaves-the-browser)
2. [Deciding](#deciding)
3. [If you use Google maps](#if-you-use-google-maps)
4. [Without any third party](#without-any-third-party)

## What leaves the browser

| When | Who | Data sent to Google |
|---|---|---|
| A visitor opens a page with a location | visitor | the request for the map script and tiles, with the usual browser data |
| An editor edits a location | editor | the map requests, and the address typed for **Find address** |
| An editor uses **My current location** | editor | the browser shares the editor's position with the page, which uses it in Google's map |

## Deciding

Review it like any third-party service (see "Third-party services to review" in the sovereignty section): purpose,
data, legal basis and consent, contract with the provider, alternatives. Exponential ships no cookie or consent banner
([Privacy standards](../../../../standards/privacy.md)).

## If you use Google maps

- Mention the service in the privacy notice.
- Load the map only where needed (pages with a location), which the extension already does.
- Allow Google's hosts in the Content Security Policy, as listed in the extension's README.
- Consider a click-to-load placeholder in your view template override, so the map script loads only when the visitor
  asks for it.

## Without any third party

Leave `[GMapSettings] ApiKey` empty: no Google script is loaded, the coordinates and address are shown without a map,
and the distance filter still works. Add a link to a map service of the visitor's choice if useful.

## Related pages

- [What exists for maps](what-exists-for-maps.md)
- [Privacy standards](../../../../standards/privacy.md)
- [Content Security Policy](../../../../features/6.0/content-security-policy.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
