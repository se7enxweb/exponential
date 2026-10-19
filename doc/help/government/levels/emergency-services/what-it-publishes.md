---
title: What an emergency service publishes
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: The content fire and rescue services, civil protection and emergency management bodies typically publish - incident and warning information, preparedness, prevention, services, recruitment, reports - and what each needs from Exponential.
keywords: emergency services website, fire service, incidents, warnings, preparedness, prevention, evacuation, shelters, recruitment, volunteers, reports, scope
type: help
---
# What an emergency service publishes

This page is for project managers and website creators at a fire and rescue service, civil protection authority or
emergency management body who define the scope of a site. It lists what such bodies typically publish (general
public-sector practice) and what each needs.

## Contents

1. [The content](#the-content)
2. [What the CMS must do](#what-the-cms-must-do)
3. [What the website is not](#what-the-website-is-not)
4. [Questions to settle](#questions-to-settle)

## The content

| Content | Audience | Rhythm |
|---|---|---|
| Incident information: what is happening, where, what to do, road closures, shelters | the public, media | minutes to hours during incidents |
| Warnings: severe weather, flood, fire danger levels | the public | as issued |
| Preparedness: emergency kits, plans for households and businesses, evacuation routes | the public | stable, reviewed yearly |
| Prevention: fire safety at home, smoke alarms, safety checks | the public, businesses | stable |
| Services: inspections, permits, safety visits | businesses, residents | stable |
| Recruitment: careers, volunteers, cadets | applicants | campaigns |
| Reports: annual reports, incident statistics, plans | oversight, media | yearly |
| News and press | media | frequent |
| About, stations, contact (non-emergency numbers) | everyone | occasional |

## What the CMS must do

| Need | Exponential | Read |
|---|---|---|
| Publish an incident update in a minute, from any device | a short Incident update class; an on-call role without approval | [Content model](content-model.md), [Structure](structure-sections-and-roles.md) |
| Stay up under load | view cache, static cache, Velocity response cache | [Alerts, availability, maintenance mode and static cache](alerts-availability-maintenance-and-static-cache.md) |
| End old updates | `hide.php` with an end time | [Cronjobs](../../../../guides/cronjobs.md) |
| Feeds for media and partners | RSS export of incident updates | [RSS feeds](../../../../guides/rss-feeds.md) |
| Updates to followers | notifications | [Notifications: the user's guide](../../../../features/6.0/notifications.md) |
| Places | text first; a map location datatype exists (`ezgmaplocation`, Google Maps, a third party) | [ezgmaplocation](../../../../features/6.0/extensions/ezgmaplocation.md) |
| Several languages | translations per object | [Content languages](../../../../guides/content-languages.md) |

## What the website is not

| Not | Use instead |
|---|---|
| An emergency number or a way to report an emergency | the emergency number; say so on every page |
| A public warning system | national warning systems, broadcast, cell broadcast where available |
| A live map system | a GIS or map service; link or embed it with a text alternative |
| The only channel | social media, radio, partners; link them |

## Questions to settle

- [ ] Who publishes incident information at night, and how fast must it appear?
- [ ] What traffic did the largest past incident bring?
- [ ] Where else is incident information published, and which is authoritative?
- [ ] What happens to the website if the main hosting site is affected by the incident?

## Related pages

- [Emergency services](README.md)
- [Content model for emergency services](content-model.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
