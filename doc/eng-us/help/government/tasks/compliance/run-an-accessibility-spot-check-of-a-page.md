---
title: Run an accessibility spot check of a page
audience: government, editor, designer, project-manager
category: public-sector
order: 430
summary: Step by step - a 15-minute accessibility check of one public page (automated check with an axe-based browser tool, keyboard pass, zoom, a quick screen reader pass, content review), and how to record and route findings to template or content owners.
keywords: accessibility spot check, axe, browser extension, keyboard test, zoom 200%, screen reader, alt text, headings, link text, contrast, WCAG 2.2, findings, public sector
type: help
---
# Run an accessibility spot check of a page

For editors, designers and accessibility coordinators: a short check of one page after it is published or changed.
Exponential ships no accessibility testing tool for public pages; the check uses common free tools and follows
[Testing your site's accessibility](../../../creators/website-creators/testing-accessibility.md).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Recording findings](#recording-findings)
4. [Related pages](#related-pages)

## Before you start

- A desktop browser with an axe-based accessibility extension installed (or your organization's approved checker).
- Your operating system's screen reader, if you know how to use it.
- The page's address on the public siteaccess, signed out (private window).

## Steps

1. **Automated check** (3 minutes): run the checker on the page. Note every violation with its rule and element.
2. **Keyboard** (4 minutes): reload, press Tab: the skip link appears first; Tab through the page: every link and control
   is reachable in a sensible order and the focus is always visible; menus open and close with Enter and Escape;
   nothing traps the focus (maps, video, forms).
3. **Zoom** (2 minutes): 200% zoom and a narrow window: no horizontal scrolling, nothing cut off.
4. **Content** (4 minutes): headings in order (a heading outline tool or the screen reader's heading list); images
   described (and alternative texts are not just file or object names); link texts meaningful; tables with headers;
   linked documents accessible.
5. **Screen reader** (2 minutes, optional): page title, landmarks, the main heading, one image, one form field.

## Recording findings

| Page | Problem | WCAG criterion | Severity | Source | Owner |
|---|---|---|---|---|---|
| `<address>` | `<what happens>` | `<for example 1.1.1>` | `<blocker / major / minor>` | template / content / extension | `<who>` |

Send content problems to the page's editor; template problems to the designer or supplier, once for all pages using that
template.

## Related pages

- [Testing with axe and the keyboard](../../accessibility/testing-with-axe-and-the-keyboard.md)
- [Testing your site's accessibility](../../../creators/website-creators/testing-accessibility.md)
- [Accessible content for editors](../../accessibility/accessible-content-for-editors.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
