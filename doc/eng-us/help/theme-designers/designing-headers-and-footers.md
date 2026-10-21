---
title: "Designing headers and footers"
audience: theme-designer
category: theme-design
order: 18
summary: How theme designers design the frame of an Exponential site - the header and footer drawn by the pagelayout around every page, including search, sign-in and error pages; what usually goes in them, the signed-in state, language and theme switches, and small screens.
keywords: theme designer, header, footer, pagelayout, frame, logo, site name, skip link, language switcher, theme switch, signed-in, sticky header, small screens
type: help
---
# Designing headers and footers

This page is for theme designers who design the frame of a site. In Exponential the frame is drawn by one template,
the **pagelayout**, around the result of every request: content pages, but also search, sign-in, user pages and error
pages. Designing it well means designing it once for all of them. The template side is in
[The pagelayout](../designers/theme-design/the-pagelayout.md) and [Pagelayout variables](../designers/theme-design/pagelayout-variables.md).

## Contents

1. [The frame is everywhere](#the-frame-is-everywhere)
2. [What a header usually holds](#what-a-header-usually-holds)
3. [What a footer usually holds](#what-a-footer-usually-holds)
4. [Signed in or not](#signed-in-or-not)
5. [Switches: language and color scheme](#switches-language-and-color-scheme)
6. [Small screens](#small-screens)
7. [Sticky headers](#sticky-headers)
8. [What to hand over](#what-to-hand-over)

## The frame is everywhere

Draw the frame around at least these pages, not only around an article:

- a content page (full view), and the front page;
- the search page with results and with none ([Designing search results](designing-search-results.md));
- the sign-in page ([Designing login and user pages](designing-login-and-user-pages.md));
- a "not found" error page ([Designing empty and error states](designing-empty-and-error-states.md)).

If the front page is a layout built by editors, the frame still surrounds it, and its first block sits directly under
your header ([Designing layout blocks](designing-layout-blocks.md)).

## What a header usually holds

| Element | Notes |
|---|---|
| Skip link | first focusable element, visible on focus ([Skip links](../designers/theme-design/skip-links.md)) |
| Logo or site name | links to the front page; light and dark versions |
| Main navigation | see [Designing navigation and menus](designing-navigation-and-menus.md) |
| Search | a field or a button that opens one |
| Language switcher | on multilingual sites |
| Sign-in or account link | if visitors have accounts |
| Color scheme switch | optional |

Keep the header shallow. Every element in it appears on every page, is cached with the frame, and costs space on a
phone.

## What a footer usually holds

Contact information, secondary links (privacy, terms, accessibility statement), social profiles, a newsletter sign-up
if the site has one, the copyright line. The footer is a good place for links editors need to change: ask whether
they come from content (a folder of links, a menu block) or are fixed in the template. Fixed text in a template must
still go through translation ([Translating template strings](../designers/templates/translating-template-strings.md)).

## Signed in or not

The same frame is shown to anonymous visitors and to signed-in users. Design both states: "Sign in" versus the
user's name and a sign-out link. The parts of the frame that differ by user must be cached per user or left out of the
shared cache, which is a developer decision ([User and login templates](../designers/templates/user-and-login-templates.md#the-signed-in-state-in-the-frame)).
Keep the user-specific part small.

## Switches: language and color scheme

**Language.** Shipped designs build the switcher with the `language_switcher` template operator, which lists the
siteaccesses (languages) the administrator connected; the media theme's version is
`pagelayout/header/language_selector.tpl`. Design it with each language written in its own language ("Deutsch",
"Français"), with the current language marked, and never with flags alone ([Designing for many languages](designing-for-many-languages.md)).

**Color scheme.** A switch between light, dark and system needs a small script and a pre-paint guard loaded as a file,
so the page does not flash ([The theme toggle and the pre-paint guard](../designers/theme-design/theme-toggle-pre-paint-guard.md)).
Design three states, and the icon or label for each.

## Small screens

At narrow widths, the header usually becomes logo, a menu button and a search button. Decide:

- what stays visible and what moves into the menu;
- whether the menu opens as a panel, a sheet or a full screen;
- where the language and account links go;
- the focus order when the menu is open ([Keyboard and focus](../designers/theme-design/keyboard-and-focus.md#menus-disclosures-and-dialogs)).

Test at 375 px and at 960 px with 200% zoom; zoomed desktop browsers often get the small-screen header.

## Sticky headers

A header that stays on screen while scrolling takes space from the content on every page, more so at 200% zoom, and
can hide the element that has keyboard focus. If you want one, make it compact, let it collapse on small heights, and
ask the developer to set `scroll-padding-top` so focused elements and anchor targets are not hidden under it.

## What to hand over

- Header and footer at three widths, light and dark, anonymous and signed in.
- The open small-screen menu.
- The frame around a search, sign-in and error page.
- A list of which elements come from content and which are fixed text.

## Related pages

- [The pagelayout](../designers/theme-design/the-pagelayout.md)
- [Landmarks and headings](../designers/theme-design/landmarks-and-headings.md)
- [Favicons and touch icons](../designers/theme-design/favicons-and-touch-icons.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
