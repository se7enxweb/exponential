---
title: Tasks for sites and languages
audience: government, admin, editor, website-creator
category: public-sector
order: 10
summary: Step-by-step tasks for public bodies on multilingual sites, department sites, intranets and extranets, open data, transparency publishing, search, newsletters and maps in Exponential, each with prerequisites, steps, checks and common problems.
keywords: tasks, how to, step by step, languages, translation, multisite, intranet, extranet, open data, FOI, search, newsletter, maps, index
type: help
---
# Tasks for sites and languages

This section holds short task pages: one job each, with what you need first, the steps, how to check the result and
the usual problems. The background for each task is in the guide sections it links: languages, open data,
transparency and FOI, sovereignty, and the building sections for search, maps, newsletters, multisite, intranets and
extranets.

Up: [Government tasks](../README.md), [Governments: start here](../../government-start-here.md).

## Contents

1. [Languages](#languages)
2. [Sites, intranets and extranets](#sites-intranets-and-extranets)
3. [Open data and transparency](#open-data-and-transparency)
4. [Search, newsletters and maps](#search-newsletters-and-maps)

## Languages

| Task | What it does |
|---|---|
| [Translate a page into the second official language](translate-a-page-into-the-second-official-language.md) | Add the translation of an existing page in the second official language, starting from the first language, publish it and check it on that language's site. |
| [Add a new content language](add-a-new-content-language.md) | Add a content language in Setup > Languages, then make it visible through a siteaccess, and give translators access. |
| [Add a language switcher](add-a-language-switcher.md) | Put an accessible language switcher on every page of a multilingual public site with TranslationSA and the language_switcher operator. |
| [Give a language its own address](give-a-language-its-own-address.md) | Create a siteaccess for a language and reach it by a path (/fr/) or a host (fr.example.gov), with the RegionalSettings that make it show that language first. |
| [Redirect the start page to the visitor's language](redirect-the-start-page-to-the-visitors-language.md) | Make www.example.gov/ send each visitor once to the language their browser asks for, with DefaultHostUriMatchMapItems, and test it with curl. |
| [Make shared items always available](make-shared-items-always-available.md) | Mark images, media folders, user accounts and single-language documents as always available so every language site shows them. |
| [Limit a translator to one language](limit-a-translator-to-one-language.md) | Create a role whose content policies are limited by Language, assign it to a translators' group, and test it with a test account. |
| [Change the main language of an item](change-the-main-language-of-an-item.md) | Make another existing translation the main language of an item in its Translations tab, for example before removing a translation created in the wrong language. |
| [Remove a wrong translation safely](remove-a-wrong-translation-safely.md) | Move an item created in the wrong language into the right one (translate, set main, remove), knowing that removing a translation deletes it in every version. |
| [Add a right-to-left language site](add-a-right-to-left-language-site.md) | Add an Arabic, Hebrew, Persian or Urdu language site, with the direction printed in the pagelayout and logical CSS, and test it. |
| [Switch the administration to another language](switch-the-administration-to-another-language.md) | Choose the language of the administration's menus and buttons for yourself with the Interface language list, and know what changes and what does not. |
| [Check interface translation coverage](check-interface-translation-coverage.md) | Find out how complete the administration's translation into a language is, which translation a regional locale really shows, and what is untranslated, with ezchecktranslation, exp:i18n and Setup > Interface languages. |
| [Set up a translation approval workflow](set-up-a-translation-approval-workflow.md) | Have translations into one language approved by that language's reviewers before they are published, with an Approve event limited to Affected languages, connected to Before publishing content. |
| [Test translations with Lorem Ipsum](test-translations-with-lorem-ipsum.md) | Add the test language Lorem Ipsum (qlo-ZZ), fill translations with exp:loremipsum, check fallbacks and untranslated texts on a test siteaccess, and remove it again. |
| [Add a custom locale for a minority language](add-a-custom-locale-for-a-minority-language.md) | Add a language that has no locale file yet by writing share/locale/<code>.ini from a related locale, adding it as a Custom content language, and having a speaker check it. |
| [Publish meeting agenda and minutes in two languages](publish-meeting-agenda-and-minutes-in-two-languages.md) | Publish a council or committee meeting with its agenda and later its minutes in both official languages, with translated meeting objects and one document per language. |

## Sites, intranets and extranets

| Task | What it does |
|---|---|
| [Set up a department subsite](set-up-a-department-subsite.md) | Add a site for a department to an existing installation, with its own folder, siteaccess, host and administration link. |
| [Give a department its own design](give-a-department-its-own-design.md) | Create a thin design layer for one department site that overrides the shared base design only where needed, set it in the siteaccess, and test accessibility. |
| [Share a page between two sites](share-a-page-between-two-sites.md) | Make one page appear on two department sites without copying it, by giving it a second location, so a correction reaches both sites at once. |
| [Link from one department site to another](link-from-one-department-site-to-another.md) | Make links from one site to content of another site in the same installation point to the right host, with the ezurlmultisite settings, and check them. |
| [Set up a staff intranet siteaccess](set-up-a-staff-intranet-siteaccess.md) | Create an intranet siteaccess that requires sign-in for every page, with its own folder in a section visitors cannot read and a Staff role, and test it from outside and inside. |
| [Connect staff sign-in to LDAP](connect-staff-sign-in-to-ldap.md) | Let intranet staff sign in with their directory account through the kernel's LDAP login handler, configured in ldap.ini, tested on a test installation first because no 6.x guide exists. |
| [Set up two-step sign-in for staff](set-up-two-step-sign-in-for-staff.md) | Turn on two-step sign-in (authenticator app or e-mail code) with sevenx_authentication_2fa, require it for staff, protect the secrets, and test it. |
| [Give contractors extranet access](give-contractors-extranet-access.md) | Give one contractor's staff access to their own project area only, with a user group, a section, a limited role assignment and personal accounts, and record the end date. |
| [Review and disable stale contractor accounts](review-and-disable-stale-contractor-accounts.md) | Once a month, find external accounts that were never used or not used for a long time in Users > Check user accounts, export the list for project managers, and disable what is no longer needed. |

## Open data and transparency

| Task | What it does |
|---|---|
| [Publish a dataset with a stable URL](publish-a-dataset-with-a-stable-url.md) | Create a dataset page with its files in the catalog, give it a short permanent alias to cite, and check the addresses as a visitor. |
| [Add a license field to a dataset class](add-a-license-field-to-a-dataset-class.md) | Add a required License selection to the dataset class with the licenses your body approved, and fill it for existing datasets. |
| [Publish a feed of new datasets](publish-a-feed-of-new-datasets.md) | Create an RSS 2.0 or Atom export of the data catalog in Setup > RSS, map the dataset fields, and link the feed from the catalog page. |
| [Export a list of content as CSV](export-a-list-of-content-as-csv.md) | Export the objects of one class below a folder (a register, a list of service points) as CSV, with xrowextract in the administration or the kernel's exp:ezcsvexport, and check it before publishing. |
| [Publish the disclosure log](publish-the-disclosure-log.md) | Set up the public disclosure log (a class for entries without requester data, a folder per year, a list view, a feed) and publish the first entry. |
| [Publish a FOI response with redactions checked](publish-a-foi-response-with-redactions-checked.md) | Check the redactions of released documents outside Exponential, then publish the response as a disclosure log entry with its files, have it approved, and verify it signed out. |
| [Set up a FOI request form](set-up-a-foi-request-form.md) | Build an online request form with the information collector, mail each request to the FOI team mailbox, redirect to a thank-you page, and test it. |
| [Export and delete collected requests](export-and-delete-collected-requests.md) | Export the submissions of a request form as CSV from Setup > Collected information for the case file, then remove them from the website according to the retention rule. |
| [Raise the version history limit for records](raise-the-version-history-limit-for-records.md) | Keep more versions of record-relevant classes (decisions, disclosure entries, meetings) than the default 45, with VersionHistoryClass in content.ini, and check the effect. |
| [Find who changed a page](find-who-changed-a-page.md) | Find out who changed, moved or removed a page and what changed, with the Versions page (compare two versions) and the audit trail (console or exp:audit). |
| [Schedule a release for a set time](schedule-a-release-for-a-set-time.md) | Publish a page (a statistics release, an agenda) at a set date and time with the Wait until date workflow event, and know how precise the timing is. |
| [Hide content after a closing date](hide-content-after-a-closing-date.md) | Hide consultations, vacancies or calls automatically when their closing date passes, with the hide.php cronjob and content.ini HideSettings, keeping the content as a record (unlike unpublish.php, which removes it). |

## Search, newsletters and maps

| Task | What it does |
|---|---|
| [Set up search for documents](set-up-search-for-documents.md) | Make the text inside PDF (and Word) documents searchable with the binaryfile.ini text extraction handlers and an installed extraction tool, then rebuild the index and test. |
| [Turn on search statistics](turn-on-search-statistics.md) | Record what visitors search for on a public siteaccess (LogSearchStats), read Setup > Search statistics, and reset the phrases on a schedule because they can contain personal data. |
| [Add a newsletter list for alerts](add-a-newsletter-list-for-alerts.md) | Add an alert list (for example consultations) to cjw_newsletter, link its subscribe form from the topic's pages, and send a first alert after a rehearsal. |
| [Rehearse a newsletter with the file transport](rehearse-a-newsletter-with-the-file-transport.md) | Run a whole newsletter cycle (subscribe, confirm, send, unsubscribe) with cjw_newsletter writing mail to .eml files, so no real person gets mail. |
| [Add a map location to a service point](add-a-map-location-to-a-service-point.md) | Give the service point class a GMap Location field (ezgmaplocation) and set the location of an office; Google Maps is a third party and needs an API key, without which coordinates show without a map. |

## Related pages

- [Languages](../../languages/README.md)
- [Open data](../../open-data/README.md)
- [Transparency and freedom of information](../../transparency-and-foi/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
