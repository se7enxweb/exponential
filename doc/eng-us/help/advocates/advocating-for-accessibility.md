---
title: Advocating for accessibility
audience: advocate
category: advocacy
order: 110
summary: How advocates stand up for accessibility in and around Exponential - testing the administration with assistive technology and reporting problems, holding contributions to the project's styling rules, helping site builders make accessible themes and content, and making community material accessible.
keywords: accessibility, a11y, advocacy, WCAG, assistive technology, screen reader, keyboard, testing, themes, alternative text, contrast, reporting, inclusion
type: help
---
# Advocating for accessibility

This page is for advocates who care that everyone can use Exponential and the sites built with it. The project already
tests its administration for accessibility ([Talking points: accessibility](talking-points-accessibility.md)); advocates
can help keep it that way, extend it to public sites, and make the community itself accessible.

## Contents

1. [Where you can make a difference](#where-you-can-make-a-difference)
2. [Test the administration](#test-the-administration)
3. [Hold contributions to the rules](#hold-contributions-to-the-rules)
4. [Help site builders](#help-site-builders)
5. [Make the community accessible](#make-the-community-accessible)

## Where you can make a difference

| Area | What exists | What you can do |
|---|---|---|
| The administration | admin4 tests for contrast and target size, landmarks, keyboard | Test with your assistive technology; report problems |
| Contributions | Styling rules and a test that enforces them | Review pull requests with those rules in mind |
| Public sites | Each site's own design | Teach theme builders and editors |
| Content | Alternative text can be required per class | Encourage editors to write it |
| Community material | Your posts, slides, videos | Make them accessible |

## Test the administration

1. Use what you use every day: a screen reader, keyboard only, magnification, high contrast, voice control.
2. Walk the main tasks: sign in, edit, publish, upload, use the layouts editor, search the help.
3. Write down where you get stuck: what you did, what your tool announced or did, what you expected.
4. Report it as a bug with your assistive technology and browser named ([Reporting bugs well](reporting-bugs-well.md)).

What the design is meant to provide is documented in [The admin4 design](../../features/6.0/admin4-design.md#landmarks-and-names)
and [Keyboard](../../features/6.0/admin4-design.md#keyboard): use it as the expected result.

## Hold contributions to the rules

CONTRIBUTING.md asks every administration template to follow [Styling the administration](../../bc/6.0/admin4-styling.md):
no inline styles, shared components, tokens with dark values; a test checks it
([CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md)). When you review a pull
request, check for:

- every form field has a name;
- keyboard access to new controls;
- visible focus;
- contrast in light and dark mode;
- no information by color alone.

## Help site builders

- Point theme builders to the administration's own practices as an example, and to WCAG.
- Encourage switching on required alternative text for image classes
  ([Accessibility checklists](../leadership/accessibility-security-and-compliance-checklists.md#accessibility)).
- Show editors the "Missing alt text" report of Exponential Intelligence where it is set up
  ([expai, For editors](../../features/6.0/extensions/expai.md#for-editors)).
- Write a tutorial on accessible content: headings, link text, alternative text, tables
  ([Writing a tutorial that works](writing-a-tutorial-that-works.md)).

## Make the community accessible

- Accessible slides, captions and transcripts ([Accessible slides and visuals](accessible-slides-and-visuals.md)).
- Meetups in step-free places, with breaks; online meetups with captions ([Event checklists](event-checklists.md)).
- Plain language in answers; patience with people who type slowly or use dictation.

## Related pages

- [Talking points: accessibility](talking-points-accessibility.md)
- [Accessible slides and visuals](accessible-slides-and-visuals.md)
- [Accessibility, security and compliance checklists](../leadership/accessibility-security-and-compliance-checklists.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
