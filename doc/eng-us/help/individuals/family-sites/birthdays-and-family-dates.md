---
title: Birthdays and family dates
audience: individual, website-creator
category: personal-sites
order: 1704
summary: What Exponential offers for recording birthdays, anniversaries and family gatherings - the birthday extension's ezbirthday datatype, which stores a date of birth and nothing more, the Event and Event calendar classes for gatherings, what is not available (no reminders, no greeting mails), and the privacy of dates of birth.
keywords: birthday, ezbirthday, birthday extension, date of birth, anniversaries, family dates, event, event calendar, reminders, greetings, CSV export, xrowextract, privacy, family site
type: help
---
# Birthdays and family dates

This page is for individuals who want their family site to hold birthdays, anniversaries and gatherings. It says
plainly what exists, because the name of one extension promises more than it does.

## Contents

1. [The birthday extension](#the-birthday-extension)
2. [What it does not do](#what-it-does-not-do)
3. [Recording birthdays](#recording-birthdays)
4. [Gatherings and anniversaries as events](#gatherings-and-anniversaries-as-events)
5. [A dates page without programming](#a-dates-page-without-programming)
6. [Dates of birth are personal data](#dates-of-birth-are-personal-data)

## The birthday extension

`birthday` ("eZ Birthday", composer package `se7enxweb/birthday`, in the root `composer.json`) provides one thing:
the datatype **`ezbirthday`**, a field that stores a date of birth so it can be sorted and searched, with the usual
edit and view templates. It also registers a CSV export handler used by the export tool `xrowextract`
([birthday: a birthday datatype](../../../features/6.0/extensions/birthday.md)). The extension's `composer.json`
describes it as a datatype that "stores the date in the format of YYYY-MM-DD".

To use it: activate the extension, add an attribute of the birthday datatype to a class in **Setup > Classes**, and
enter dates in objects of that class.

## What it does not do

Checked in the extension's code (`extension/birthday`: `datatypes/ezbirthday`, `classes/parsers`, templates and
settings only):

- **No reminders** to you or anyone before a birthday.
- **No greeting mails** on the day.
- **No calendar view** of upcoming birthdays.
- **No age calculation shown** by default.

Any of these would be template or cronjob work for someone who writes Exponential code
([When to hire help](../running-it/when-to-hire-help.md)). Your phone's calendar is a perfectly good reminder system.

## Recording birthdays

Two places can hold a date of birth:

| Where | How | Suits |
|---|---|---|
| On the relatives' user accounts | Add a birthday field to the *User* class | Relatives who have accounts |
| On a "Relative" class of your own | A class with name, photo, `ezbirthday` field, a short note | Including relatives without accounts, and ancestors |

Adding a field to the *User* class changes every account form, including registration; think before you do it
([Choosing datatypes](../../creators/website-creators/choosing-datatypes.md)).

## Gatherings and anniversaries as events

For reunions, celebrations and anniversaries, the shipped *Event* class is the right tool. Its fields are title,
short title, text, category, from time and to time (`class-event.xml` in the `sevenx_classes` package). Events live in
an *Event calendar*, which shows them by month
([Events and calendars](../../businesses/members/events-and-calendars.md)).

| Use | How |
|---|---|
| A reunion | An *Event* with date, place and what to bring |
| A wedding anniversary | An *Event* each year, or one article that lists them |
| Sign-up for a gathering | A form next to the event ([Forms, polls and sign-ups](../club-sites/forms-polls-and-sign-ups.md)) |

There is no export of events to phone calendars out of the box.

## A dates page without programming

The simplest thing that works: one *Article* called "Family dates" with a table of names and dates (day and month
only), sorted by month, inside the private area. You update it by hand once a year. It is less clever than a
database and much easier to keep private.

## Dates of birth are personal data

A full date of birth is one of the details used to impersonate people and answer security questions.

- Store **day and month only** where the year is not needed.
- Keep birthdays **inside the private area**, never on public pages.
- Do not record children's full dates of birth unless their parents agree.
- Remember that an export (CSV) of a class with birthdays is a list of personal data; keep it off shared drives.
- Delete the dates of people who ask.

See [Privacy for your family](privacy-for-your-family.md) and
[Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md).

## Related pages

- [Family sites](README.md)
- [birthday: a birthday datatype](../../../features/6.0/extensions/birthday.md)
- [Events and calendars for clubs](../club-sites/events-and-calendars-for-clubs.md)
- [What exists and what does not](../what-exists-and-what-does-not.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
