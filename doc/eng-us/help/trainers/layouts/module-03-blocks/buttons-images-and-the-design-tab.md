---
title: "Lesson 3.3: Buttons, images and the Design tab"
audience: trainer, editor, website-creator, designer
category: training
order: 33
summary: Learners add a Button / Link and an Image block, set a link target and an alternative text, and use the Design tab's view type, name and CSS class to change how a block looks without a template. 45 minutes.
keywords: exponential layouts, button block, link, target, image block, alt text, design tab, view type, block name, css class, css id
type: help
---
# Lesson 3.3: Buttons, images and the Design tab

Calls to action and pictures make a page work. This lesson adds a button and an image, and then shows the **Design**
tab: the place where a block's shape and style are chosen without a developer.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 3: Blocks](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Editors, site builders and designers |
| Prerequisites | [Lesson 3.2](text-blocks.md) |

## Learning objectives

After this lesson, learners can:

- add a **Button / Link** block with its text, link address and target;
- add an **Image** block with an alternative text;
- choose a block's **view type** where it has several;
- give a block a **Name** and a **CSS class** from the site's theme.

## Trainer's notes

### How to present it

Sources: [Exponential Layouts: Block parameters](../../../../bc/6.0/LAYOUTS.md#block-parameters) and
[Layout types, zones and blocks](../../../creators/website-creators/layout-types-zones-and-blocks.md#view-types-and-parameters).

1. **Button / Link** (3 minutes). Its settings include the button text, the link address, a target (open in the same
   or a new window) and a style or CSS class. The exact field names depend on the block handler of your installation;
   show them on the projector.
2. **Image** (3 minutes). An image with its address and an **alternative text**, and an optional link. Write the
   alternative text as for any image (Editors 201, lesson 8.2).
3. **View type** (3 minutes). A block's view type is the shape it renders in. Most simple blocks have one (`default`);
   listing blocks have several (module 4).
4. **Name, CSS ID, CSS class** (4 minutes). Every block carries a **Name** (an editor-facing label to tell similar
   blocks apart), a **CSS ID** and a **CSS class** written onto the block's wrapper. The CSS class is the seam between
   editor and designer: the theme ships classes (a background, a spacing), the editor applies them per block, nobody
   edits a template.
5. **Ask the designer** (2 minutes). Learners need a list of the classes their theme offers. Hand one out, or use the
   classes the trainer prepared.

### Common questions

**Where do I find the CSS classes I may use?** From your site's designer or theme documentation. A class the theme
does not define changes nothing.

**Should the image be a block or a content item?** For an image that belongs to this page's arrangement, a block is
fine; images used on several pages belong in the media library.

**Why does my button open the link in a new window?** The target is set to a new window; set it back to the same
window unless there is a reason.

### Pitfalls

- **Empty alternative texts.** The image block has a field for it; fill it.
- **Inventing CSS classes.** Only theme classes work.
- **Names left blank.** Ten blocks called "Rich text" are hard to tell apart. Name them.

## Hands-on exercise

Use the training installation and your course layout. The trainer wrote two CSS classes of the theme on the board.

1. Open your course layout with **Open** (continue your draft if asked).
2. Add a **Button / Link** block in *MAIN* below the Markdown block.
3. In **Content**, set the button text to `Contact us`, the link address to the address the trainer gives, and the
   target to the same window.
4. Add an **Image** block in *POST_HEADER* below the title. Set its image address (the trainer gives one from the
   site's media library) and an alternative text that describes it.
5. Select the **Rich text** block. In **Design**, give it the **Name** `Intro text` and one of the theme's CSS classes
   from the board. Watch the block change in the middle if the class affects the editor view.
6. Give the **Button / Link** block the **Name** `Main call to action`.
7. Look at the **Design** tab of each block: which blocks offer more than one view type?
8. Choose **Save draft and close**.

## Expected result

- The draft has a button with text, address and target, and an image with an alternative text.
- Two blocks carry clear names; the rich text block carries a theme class.
- The learner can say that simple blocks have one view type.
- The trainer checks one learner's draft and its **Design** tabs on the projector.

## Quiz

1. Which three settings does a button need at least?
2. What is the **Name** of a block for?
3. What does the **CSS class** field let editors do?
   A. Write new CSS B. Apply classes the theme defines C. Change the template D. Change the layout type
4. True or false: every block has several view types.
5. Why should an image block have an alternative text?

<details>
<summary>Answers</summary>

1. The text, the link address and the target.
2. An editor-facing label to tell similar blocks apart ([Exponential Layouts](../../../../bc/6.0/LAYOUTS.md#block-parameters)).
3. **B.**
4. **False.** Most simple blocks have one; listing blocks have several.
5. For people who cannot see the image and for when it cannot load.

</details>

## Further reading

- [Exponential Layouts: Block parameters](../../../../bc/6.0/LAYOUTS.md#block-parameters): the common fields.
- [Branding your site](../../../creators/website-creators/branding-your-site.md): where a site's look and its classes come from.

## Related pages

- [Module 3: Blocks](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
