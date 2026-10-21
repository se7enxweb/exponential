---
title: Alt text for images
audience: ai-agents, editor, admin
category: ai-agents
order: 28
summary: How Exponential Intelligence suggests the alternative text of images with a vision model - the Write alt text and Decorative buttons in the edit form, which model looks at the image and how the data policy can keep images away from a provider, the Missing alt text report for a subtree, and why the decorative choice always stays with the editor.
keywords: alt text, alternative text, accessibility, images, vision model, Write alt text, decorative image, Missing alt text report, AltTextSettings, data policy for images, WCAG
type: help
---
# Alt text for images

This page is for editors who must give every image a useful alternative text, and for administrators who decide
whether images may go to an AI provider. Exponential Intelligence can suggest the alt text; the editor decides.

## Contents

1. [In the edit form](#in-the-edit-form)
2. [Which model looks at the image](#which-model-looks-at-the-image)
3. [Images and the data policy](#images-and-the-data-policy)
4. [Many images at once](#many-images-at-once)
5. [Good practice](#good-practice)

## In the edit form

Every image field gets two buttons above its "Alternative image text" field
([extension documentation: alt text](https://github.com/se7enxweb/expai/blob/main/doc/alt-text.md)):

| Button | What it does |
|---|---|
| **Write alt text** | Sends the stored image of this draft, the page title, the text of the other fields and the language to a vision model. The answer goes into the alt text field; **Undo** restores what was there. A note names the model, the size of the image sent and the cost |
| **Decorative (empty alt text)** | Your explicit choice for an image that carries no information: the field is emptied, screen readers skip the image. No request is made |

- **The model never decides that an image is decorative.** When it thinks so, it still writes a description and
  says so in the note. You choose.
- On a field whose class requires an alternative text, the Decorative button is disabled, with the reason.
- The image must be **stored in the draft** first: a newly chosen file is used only after "Store draft".
- Values go into the form only; the draft is stored or published as usual, and labeled as written with Exponential
  Intelligence.

## Which model looks at the image

Only models with the capability `vision`. The first that fits, in this order: the alt text model set by the
administrator, your own model choice, the site default, then any other vision model. A model is skipped when you may
not use it, its server is stopped, its provider has no key, or the data policy keeps the image away from it. When
none is left, the button is disabled and says why.

## Images and the data policy

The image is content of its object: the section and class rules of the data policy apply to it, and
`[AltTextSettings] ImageRegions[]` and `ImageZeroRetention` can keep images to stricter rules than text. The image
travels inside the request, never as a link the provider could fetch later, and the request log never keeps the
pixels ([Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md)).

## Many images at once

The **Missing alt text** report (`expai/alttext`, with `expai/write`) lists the images of a subtree without an
alternative text and queues suggestions for them as **drafts**, through the job queue. Each job checks the data policy
again when it runs. Nothing is published; every suggestion waits for review.

## Good practice

- Read every suggestion against the image and the page: alt text describes what the image means here, not just what
  it shows.
- Remove guesses: names of people, places or products the model could not know.
- Keep it short; put long descriptions in the page text.
- Use Decorative only for images that really carry no information.

The accessibility pages of the help go further: [Advocating for accessibility](../../advocates/advocating-for-accessibility.md).

## Related pages

- [What Exponential Intelligence does](what-exponential-intelligence-does.md)
- [Reviewing AI drafts](../checklists/reviewing-ai-drafts.md)
- [Media and uploads](../../media-and-uploads.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
