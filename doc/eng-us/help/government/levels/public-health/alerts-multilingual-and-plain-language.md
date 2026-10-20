---
title: Alerts in many languages and plain language
audience: government, editor, project-manager, website-creator
category: public-sector
order: 70
summary: How a public health body publishes an alert fast in Exponential, translates it into the languages people speak, keeps it plain, updates it during the event, and ends it - with the Alert class, translations, caches and notifications.
keywords: health alert, outbreak, warning, translation, multilingual, plain language, update alert, end alert, hide date, front page, cache, notifications, RSS
type: help
---
# Alerts in many languages and plain language

This page is for alert publishers, editors, website creators and project managers at a public health body. It
describes an alert from first publication to its end, using the Alert class of
[Content model for public health](content-model.md) and the roles of
[Structure, sections and roles for public health](structure-sections-and-roles.md). Exponential has no dedicated
alert or broadcast system; this is a publishing practice built on content, translations and caches.

## Contents

1. [Write it plain](#write-it-plain)
2. [Publish it](#publish-it)
3. [Translate it](#translate-it)
4. [Update it](#update-it)
5. [End it](#end-it)
6. [Tell people](#tell-people)

## Write it plain

| Part | Rule | Example of the shape |
|---|---|---|
| Headline | what is happening and where, in under ten words | "<Illness> cases in <area>" |
| What to do | the action, first, as an instruction | "Wash your hands often. Stay home if you are ill." |
| Details | who is at risk, symptoms, when to get help | short paragraphs under questions |
| Last updated | always shown | |

Plain language guidance: [plainlanguage.gov](https://www.plainlanguage.gov/). Prepare templates of typical alerts
(heat, water, outbreak) in advance as unpublished drafts or as text in the procedure, approved by clinicians, so the
on-call team fills in the specifics rather than writing under pressure.

## Publish it

1. Open the Alerts folder, **Create new** > **Alert**.
2. Fill headline, severity, what to do, details, area, issued time, and the end time if known.
3. **Send for publishing**. Alerts are outside the clinical approval workflow, so the alert is published at once.
4. Open the front page logged out and check the alert appears. Publishing clears the view cache of the alert and its
   related pages; if the front page is served from a static cache, Velocity's response cache or a CDN, refresh those
   as rehearsed ([Caches](../../../../guides/caches.md), [Velocity response cache](../../../../features/6.0/velocity-response-cache.md)).

## Translate it

1. On the alert, choose the language in the list next to **Edit** (or **New translation** in the language list), then
   **Edit**; the edit form shows the original as a reference ([Content languages](../../../../guides/content-languages.md)).
2. Translate headline, what to do and details; dates and severity are not translatable and stay the same.
3. **Send for publishing**. The translation appears in the siteaccess of that language.

| Situation | Do |
|---|---|
| A language is not ready yet | publish the original first; whether it shows in other language siteaccesses depends on the class's "always available" setting and your siteaccess language list |
| Many languages, few translators | translate "what to do" first, then the details |
| A translation differs after an update | update every language; the **Translations** tab shows which languages exist |

Right-to-left languages are supported by the administration and by templates that set `dir`
([Right-to-left languages](../../../../features/6.0/right-to-left.md)).

## Update it

1. **Edit** the alert; change the text; set **Last updated** to now.
2. Write the change at the top of the details ("Updated <time>: <what changed>").
3. **Send for publishing**, then update each translation.
4. Earlier wording stays in the version history ([Versions of an object](../../../../guides/content-history.md)).

## End it

| Way | When |
|---|---|
| The end time and `hide.php` | the end is known in advance; the alert is hidden at the next cronjob run after the time |
| **Hide / unhide** in the item's menu | the end is decided now |
| A final update, then hide later | people need to read "The alert has ended" for a while |

Ended alerts stay in the administration as a record. Do not remove them unless your records rules allow it.

## Tell people

| Channel | Setup |
|---|---|
| RSS | an RSS export of the Alerts folder (**Setup > RSS**) for media and partner sites ([RSS feeds](../../../../guides/rss-feeds.md)) |
| E-mail to followers | people with an account follow the Alerts folder with **Notify me**; the notification cronjob sends the mail ([Notifications: the user's guide](../../../../features/6.0/notifications.md)) |
| Newsletter | for slower updates ([Newsletters with CJW Newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md)) |
| Social media | outside Exponential; link to the alert's address |

Notifications go only to people who chose to follow, after the next notification run; they are not an emergency
broadcast system. For urgent warnings to everyone, use the warning systems your country provides.

## Related pages

- [Public health](README.md)
- [Emergency services](../emergency-services/README.md)
- [Multilingual sites](../../../creators/website-creators/multilingual-sites.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
