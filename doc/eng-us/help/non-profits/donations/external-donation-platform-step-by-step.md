---
title: External donation platform, step by step
audience: non-profit, businesses, editor, website-creator
category: non-profits
order: 30
summary: Building the simplest donation route on an Exponential site - choosing a donation platform, a Donate page, a Donate button in the header or with the Layouts Button block, links from campaign pages, telling donors they leave the site, the privacy notice, a return thank-you page, embedding only what your Content Security Policy allows, and testing.
keywords: external donation platform, donate button, donate page, link, Layouts button block, ezurl, embed, iframe, Content Security Policy, thank-you page, privacy notice, testing
type: help
---
# External donation platform, step by step

This page is for the editor or website creator who sets up the donation route most small non-profits use: a Donate
button on your site that leads to a donation page hosted elsewhere. No payment data touches your site, and the
platform handles recurring giving and receipts as far as it offers them.

## Contents

1. [Before you start](#before-you-start)
2. [Step 1: the Donate page](#step-1-the-donate-page)
3. [Step 2: the Donate button](#step-2-the-donate-button)
4. [Step 3: links from campaigns and the newsletter](#step-3-links-from-campaigns-and-the-newsletter)
5. [Step 4: the return page](#step-4-the-return-page)
6. [Embedding a platform's form](#embedding-a-platforms-form)
7. [Step 5: privacy and testing](#step-5-privacy-and-testing)
8. [Checklist](#checklist)

## Before you start

- Choose the platform yourselves: compare fees, payout times, recurring giving, receipts and data export on their
  published terms. This help names no platform and no fees.
- Have the platform account opened in your organization's name, with at least two people who can sign in.
- Decide the address of your donation page on the platform; you will link to it.

## Step 1: the Donate page

Create a normal page (an article or a folder with text, from the shipped classes) called **Donate** in the main
navigation. It explains why to give before it asks; [The giving page](the-giving-page.md) lists what belongs on it.
Put the link to the platform near the top and again at the end.

Give the page a short address such as `/Donate`; Exponential builds the address from the name, and you can add an
alias ([URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md)).

## Step 2: the Donate button

Pick what your design offers:

| Where | How | Source |
|---|---|---|
| A layout (home page, campaign page) | The standard block **Button / Link** (`button`) of Exponential Layouts | [Layouts reference](../../../bc/6.0/LAYOUTS.md), [Landing pages with Layouts](../../creators/website-creators/landing-pages-with-layouts.md) |
| On a page class | A link field (`ezurl`) the template shows as a button | [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md) |
| In the header of every page | The shared header and footer | [Shared header and footer](../../creators/website-creators/shared-header-and-footer.md) |
| In the menu | A menu item pointing at the Donate page | [Menus and navigation](../../creators/website-creators/menus-and-navigation.md) |

Point the button at your own Donate page, not straight at the platform: the page explains, the platform collects.

## Step 3: links from campaigns and the newsletter

- Each campaign page carries its own Donate button; see [Campaigns and appeals](../communications/campaigns-and-appeals.md).
- Newsletter editions link to the Donate page ([Supporter newsletter](../communications/supporter-newsletter-with-cjw-newsletter.md)).
- If the platform lets you add a code to the link to tell campaigns apart, use one per campaign so you can see which
  appeal worked.

## Step 4: the return page

Most platforms can send donors back to an address you choose after the gift. Create a **Thank you** page on your site
and give its address to the platform. Keep it out of the menu. See
[Donor trust and thank-you](donor-trust-and-thank-you.md) for what it says.

## Embedding a platform's form

Some platforms offer a form to embed on your page. Before you do:

- The form loads a script or frame from the platform. A site that sends a Content Security Policy must allow the
  platform's hosts, or the form does not appear ([Content Security Policy](../../../features/6.0/content-security-policy.md)).
- Adding a script is template work for the website creator, not something to paste into rich text
  ([Stylesheets and scripts](../../creators/website-creators/stylesheets-and-scripts.md)).
- An embedded form still sends data to the platform; name it in your privacy notice
  ([Maps and embeds](../../government/privacy/maps-and-embeds.md) explains the same issue for other embeds).
- Check the embedded form is usable by keyboard and screen reader; if not, link instead.

A plain link is simpler, faster and easier to make accessible.

## Step 5: privacy and testing

- [ ] The privacy notice names the platform and what it receives.
- [ ] The Donate page says, next to the button, that donors continue on the platform's site.
- [ ] Test on a phone and a computer: button, platform page, a small real gift, return to the thank-you page.
- [ ] Test with the keyboard only ([Keyboard and focus](../../government/accessibility/keyboard-and-focus.md)).
- [ ] Ask a second person to follow the path without help.

## Checklist

- [ ] Platform account in the organization's name, two people with access.
- [ ] Donate page live, linked from the menu and the header.
- [ ] Button blocks on the home page and campaign pages.
- [ ] Thank-you page set as the platform's return address.
- [ ] Privacy notice updated.
- [ ] A person named to check gifts on the platform weekly.

## Related pages

- [Donations](README.md)
- [Donation options at a glance](donation-options-at-a-glance.md)
- [Donations and fundraising pages](../../businesses/nonprofits/donations-and-fundraising-pages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
