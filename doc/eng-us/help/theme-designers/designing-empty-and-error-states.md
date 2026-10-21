---
title: "Designing empty and error states"
audience: theme-designer
category: theme-design
order: 21
summary: How theme designers design the moments when there is nothing to show or something went wrong in an Exponential site - empty folders and blocks, missing images, no search results, not-found and access-denied pages, form errors, and the shared pattern for all of them.
keywords: theme designer, empty state, error state, not found, 404, access denied, error page, kernel errors, empty folder, no results, missing image, error templates
type: help
---
# Designing empty and error states

This page is for theme designers who want the edge cases of a site to look designed. Content managed sites produce
empty and error states all the time: a new folder with no children, a block whose query finds nothing, a link to a
page that was moved, a visitor who is not allowed to see a page. The templates involved are documented in
[Error templates](../designers/templates/error-templates.md); this page is about what they should look like.

## Contents

1. [One pattern for all of them](#one-pattern-for-all-of-them)
2. [Empty content](#empty-content)
3. [Missing images and optional fields](#missing-images-and-optional-fields)
4. [Error pages](#error-pages)
5. [Not found](#not-found)
6. [Access denied](#access-denied)
7. [Errors in forms](#errors-in-forms)
8. [An inventory of states](#an-inventory-of-states)

## One pattern for all of them

Make one **empty state** component and one **message** component (alert) and use them everywhere
([Component inventory](component-inventory.md)). Each answers three questions in plain words:

1. What happened? ("This folder has no articles yet.")
2. Why, if it helps? ("You may need to sign in to see this page.")
3. What can I do now? A link or a button.

Keep the tone calm and the language plain. No humor that fails in translation, no blame, no error codes as the main
text.

## Empty content

| Situation | What visitors see | What the design should do |
|---|---|---|
| A folder with no children | the folder's full view with an empty list | a short sentence, or nothing at all if the folder has its own text |
| A layout block whose collection is empty | an empty block | the block should render nothing, so the zone closes up |
| A related list with no relations | an empty heading | no heading, no box |
| A list filtered to nothing | an empty list | say which filter caused it and how to remove it |

The usual rule for visitors: **hide empty containers**. A box with a heading and nothing in it looks broken. An empty
message is right only where the visitor asked for something (a search, a filter, a list they opened on purpose).

Editors are the exception: in a preview of an empty block they need to see that the block is there. Ask the developer
whether block templates can show a hint in the editor's preview only ([Layouts for designers](../designers/theme-design/layouts-for-designers.md)).

## Missing images and optional fields

Each optional field needs a design without it ([Designing for editors](designing-for-editors.md#rule-2-every-optional-field-has-an-empty-design)).
For images in particular, prefer a text-only variant over a gray placeholder; a grid of identical gray boxes tells
visitors nothing.

## Error pages

When the kernel cannot show a page it renders an error template inside your frame. The templates are numbered by the
kernel error code, under `error/kernel/` (the kernel ships `1.tpl` to `6.tpl` and `20.tpl` to `22.tpl`); the
designer help lists which number means what ([Error templates](../designers/templates/error-templates.md#the-templates-and-their-numbers)).
Design them as normal pages:

- the frame with header, navigation and footer, so visitors can continue;
- a clear heading and one or two sentences;
- the search field;
- links to the front page and the main sections.

## Not found

The "not found" page is the most visited error page. Design it to help visitors recover, since many arrive from old
links after a reorganization: search with the words from the address prefilled is one useful idea, but needs a
developer to build it ([Error templates](../designers/templates/error-templates.md#a-helpful-not-found-page)).

## Access denied

Two different cases need two different messages:

- **Not signed in**: the content may be visible after signing in. With the shipped error settings, an anonymous
  visitor who is denied access gets the error page with the sign-in form embedded in it, so design the access-denied
  page and the sign-in form together ([Designing login and user pages](designing-login-and-user-pages.md)).
- **Signed in but not allowed**: say so plainly and offer a way back. Do not invite the visitor to sign in again.

## Errors in forms

Validation errors are a state of the form, not an error page: a summary at the top with links to each field, and a
message at each field ([Designing forms](designing-forms.md)).

## An inventory of states

Add these to your mockup list and your [design QA checklist](design-qa-checklist.md):

- [ ] Empty folder, empty block, empty related list, empty filtered list.
- [ ] Card and full view without image and without each optional field.
- [ ] No search results.
- [ ] Not found, access denied (signed out and signed in), and a generic error page.
- [ ] Form with errors.
- [ ] All of them in light and dark, at three widths.

## Related pages

- [Error templates](../designers/templates/error-templates.md)
- [Designing search results](designing-search-results.md#no-results)
- [Designing interactive states](designing-interactive-states.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
