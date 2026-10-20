---
title: "Lesson 3.3: Overrides in the Template Editor"
audience: trainer, developer
category: training
order: 33
summary: Learners create, reorder and remove an override in Design > Template Editor of the admin4 administration, read the settings file it writes, and decide when the click path or the file path fits better. 40 minutes.
keywords: developers course, template editor, Design, visual/templatelist, templateview, New override, Copy of default template, Object ID, Save conditions, Remove selected, Priority, drag and drop, admin4
type: help
---
# Lesson 3.3: Overrides in the Template Editor

The administration has a visual editor for the same override rules learners wrote by hand in lesson 3.2. It is the
fastest way to try an idea and the safest way to see the order in which rules are tried. In this lesson learners
create an override for one object with the editor, reorder rules, read the file the editor wrote, and remove the
override again.

> **Note**
> The steps describe the **admin4** design. Do this only on the training installation: the editor writes settings
> and template files.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 3: Designs, overrides and debugging](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 3.2](override-rules.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- open **Design** > **Template Editor** and find a template's overrides;
- create an override for one object with **New override** and **Copy of default template**;
- reorder overrides and change their conditions with **Save conditions**;
- find the rule the editor wrote in `settings/siteaccess/<sa>/override.ini.append.php`;
- remove an override with **Remove selected**, and say when the file path of lesson 3.2 is the better choice.

## Trainer's notes

### How to present it

Source: [Template editor](../../../../features/6.0/template-editor-overrides.md) and
[Template override ordering](../../../../features/6.0/template-override-ordering.md).

1. **Opening it** (2 minutes). The **Design** tab, left menu **Template Editor** (`/visual/templatelist`). The page
   **Template list** lists every template with its **Design resource**, and **Most common templates** below.
2. **A template's overrides** (4 minutes). Open `/node/view/full.tpl`. The heading reads "Overrides for
   </node/view/full.tpl> template in <site> siteaccess" with the count. Choose another siteaccess in the
   **Siteaccess** list and press **Set**. The overrides are shown in exactly the order they are tried; drag a card
   or use its arrows to reorder, and the new order is saved at once as `Priority` values.
3. **New override** (4 minutes). The form **Create new template override** asks for **Extension:** (one of the
   design extensions, or **No extension**),
   **Filename:**, **Override keys:** (**Class**, **Section**, **Object ID**, **Node ID**) and **Base template on:**
   (**Empty file**, **Copy of default template** and others). **OK** creates it with `Priority=0`, the highest.
4. **What it writes** (3 minutes). The rule goes into `settings/siteaccess/<sa>/override.ini.append.php`, the editor
   keeps a copy of the file before it writes, and it expires the content view cache.
5. **Click path or file path?** (2 minutes). The editor is for trying and ordering. For a site under version
   control, the rule and template belong in your extension (lesson 3.2); the editor removes only rules the siteaccess
   file defines and leaves an extension's rules alone.

### Common questions

**Why does the list show overrides from extensions I did not write?** It shows every rule in effect for the
siteaccess, wherever it is defined. Each card says whether the siteaccess settings or an extension define it.

**Does a new override win immediately?** Yes, with `Priority=0` it is tried first.

**Who may use the editor?** Users with the `visual/templatelist` policy of the Design module.

### Pitfalls

- **Wrong siteaccess.** The editor works on the siteaccess shown in the heading. Check it before **New override**.
- **Pressing Enter.** In the condition fields, Enter presses **Save conditions**, not **Remove selected**; still,
  read the button before pressing it.
- **Two learners, one installation.** On a shared training installation, two learners editing the same template's
  overrides change each other's order. Give each learner their own installation, or their own object to match.

## Hands-on exercise

On your training installation, signed in to the administration as an administrator.

1. Open the **Design** tab and choose **Template Editor** in the left menu.
2. Type `full` in the filter and press **Filter**; open `/node/view/full.tpl`.
3. Check the siteaccess in the heading; if it is not your public siteaccess, choose it in **Siteaccess** and press
   **Set**.
4. Count the overrides in the heading and find the one you wrote in lesson 3.2 (`mysite_full_article`). Note that
   it is defined by an extension.
5. Press **New override**.
6. In **Extension:** choose **No extension** (the last entry), so the file goes into the siteaccess's design. In
   **Filename:** type `mysite_one_object`; the form adds `.tpl` itself.
7. In **Object ID** enter the object ID of one article (shown on the article's page in the administration).
8. Choose **Copy of default template** and press **OK**.
9. Open the new override's template for editing from the list, add `<p>Template Editor override</p>` at the top
   and press **Save**.
10. Open the article on the site; the line appears.
11. In a shell, open `settings/siteaccess/<sa>/override.ini.append.php` and find the new block with `Priority=0`.
12. Back in the editor, tick the new override, press **Remove selected**, and confirm the rule and the file are
    gone.

## Expected result

- Step 4: the course override is in the list with its condition `class_identifier`.
- Step 10: the article shows the line; other articles still show "Course override:" from lesson 3.2, because the
  object rule is tried first.
- Step 11: a block with `Source=node/view/full.tpl`, the `MatchFile`, `Subdir=templates`, `Match[object]` and
  `Priority=0`.
- Step 12: the block and its template file are removed; the article falls back to the lesson 3.2 override.
- The trainer checks step 11 on one learner's screen.

## Quiz

1. Where is the Template Editor in the admin4 administration?
2. Which **Base template on:** choice starts from the template being overridden?
   A. **Empty file** B. **Copy of default template** C. **Container (with children)** D. **View (without children)**
3. Which `Priority` does a new override get, and what does that mean?
4. Into which file does the editor write the rule?
5. Why does **Remove selected** leave an override defined by an extension alone?

<details>
<summary>Answers</summary>

1. **Design** tab, left menu **Template Editor** (`/visual/templatelist`).
2. **B.** **Copy of default template**.
3. `Priority=0`, the highest: it is tried before every other rule.
4. `settings/siteaccess/<sa>/override.ini.append.php` of the siteaccess shown in the heading.
5. The editor removes only rules the siteaccess file defines; an extension's rules belong to the extension
   ([Template editor](../../../../features/6.0/template-editor-overrides.md)).

</details>

## Further reading

- [Template editor](../../../../features/6.0/template-editor-overrides.md): everything the editor does and how it
  writes safely.
- [Template override ordering](../../../../features/6.0/template-override-ordering.md): drag and drop order.
- [Comparing overrides with the standard](../../../designers/lifecycle/comparing-overrides-with-the-standard.md):
  keeping overrides current after an update.

## Related pages

- [Module 3: Designs, overrides and debugging](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
