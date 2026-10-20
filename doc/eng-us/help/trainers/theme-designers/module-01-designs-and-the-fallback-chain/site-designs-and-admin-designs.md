---
title: "Lesson 1.4: Site designs and administration designs"
audience: trainer, designer, developer
category: training
order: 14
summary: Learners tell the public site designs from the administration designs (admin, admin3, admin4, admin4l, editor), read the admin chain, and try another administration design for their own session with the Interface tool. 45 minutes.
keywords: theme design, site design, administration design, admin, admin3, admin4, admin4l, editor, adminui, uiswitch, Interface tool, Administration design, admintest_admin4
type: help
---
# Lesson 1.4: Site designs and administration designs

Exponential draws two kinds of pages: the public site that visitors see, and the administration where editors work.
Both are made of designs and follow the same chain rules, but they are built and changed for different people. This
lesson separates the two, so that learners know which one a change of theirs will touch.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 1: Designs and the fallback chain](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Beginner |
| Audience | Designers and front-end developers |
| Prerequisites | [Lesson 1.2](reading-the-chain-of-a-siteaccess.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- name the administration designs that ship (`admin`, `admin3`, `admin4`, `admin4l`) and the `editor` design;
- read the design chain of the administration siteaccess;
- switch the administration design for their own session with the **Interface** tool, and back;
- explain why a public theme never changes the administration, and which rules apply to the administration instead.

## Trainer's notes

### How to present it

Sources: [The admin4 design](../../../../features/6.0/admin4-design.md),
[admin4l](../../../../features/6.0/admin4l.md) and
[Choosing the administration design for your session](../../../../features/6.0/ui-switch-design.md).

1. **Two worlds, one mechanism** (4 minutes). A siteaccess is either a public site or an administration. Both have a
   `SiteDesign` and a chain. A public siteaccess uses a site design such as `plain` or a theme's design; the
   administration uses an admin design.
2. **The administration designs** (6 minutes). Show the folders and the session switch's table:

   | Design | Label in the switch | Falls back to |
   |---|---|---|
   | `admin4l` | Admin 4 (layouts) | admin4, admin3, admin2, admin |
   | `admin4` | Admin 4 | admin3, admin2, admin |
   | `admin3` | Admin 3 | admin2, admin |
   | `admin` | Classic | the standard design |

   admin4 is complete on its own, with a light and a dark mode; admin4l is admin4 assembled from layouts and holds only
   what differs. The `editor` siteaccess (`SiteDesign=editor`) is the simplified administration for editors and falls
   back to `admin4l` first, then `admin4`, `admin3`, `admin2`, `admin`. AdminUI (`adminui`) comes with the
   `exp_adminui` extension.
3. **Switching for yourself** (4 minutes). In the right column of the administration, the **Interface** tool lists
   **Interface language** first and then the administration designs, one button per design (only for a role with the
   policy `uiswitch/design` and when there are at least two designs to choose). The choice affects only your own
   requests, until you sign out, and choosing the siteaccess's own design clears it. Nobody else's view changes, no
   file is written.
4. **Switching for everyone** (3 minutes). An administrator sets `SiteDesign` for the administration siteaccess, for
   example `admin4`, and clears the caches. Show the command from the guide, but do not run it in class:

   ```bash
   php bin/php/console exp:ini set site/DesignSettings/SiteDesign admin4 siteaccess:admin
   ```

5. **Different rules** (3 minutes). Public themes are the designer's own work. Administration designs have strict
   shared rules: no inline CSS, shared components, tokens with a dark value. Module 4 teaches them.

### Common questions

**Will my public theme change the administration?** Not when `SiteDesign` is set per siteaccess. It does if someone
sets it in `settings/override/` (lesson 1.2).

**Why do I not see the design buttons in the Interface tool?** The role lacks `uiswitch/design`, or there is only one
design to offer. Ask the trainer, who can show it with an administrator account.

**What is `admintest_admin4`?** On installations that have it, a siteaccess to try admin4 without changing the main
administration ([Templates and design, part 8](../../../../guides/templates-and-design.md#8-switch-the-administration-interface-to-admin4)).

### Pitfalls

- **Learners switch the design of the shared training administration for everyone.** On a shared installation, allow
  only the session switch; the `exp:ini set` command belongs on each learner's own installation.
- **Expecting the editor siteaccess to look like the admin.** It is a reduced interface on purpose; its look comes
  from `admin4l`/`admin4` through the fallback.
- **Mixing up interface language and content language.** The Interface tool changes the interface language, not the
  language of content. Lesson 5.4 uses it for right-to-left.

## Hands-on exercise

On your training installation, signed in to the administration (for example `/admin`) with your training
administrator account.

1. Read the administration's site design:
   `php bin/php/console exp:ini where site/DesignSettings/SiteDesign admin`. Note the value in effect.
2. Read its additional designs:
   `php bin/php/console exp:ini get site/DesignSettings/AdditionalSiteDesignList admin`.
3. Write the administration chain under the public chain on your sheet from lesson 1.2.
4. In the browser, open the administration's dashboard. Find the **Interface** tool in the right column (open the
   right column with **Right column** in the header if it is closed).
5. Note which administration design buttons are offered and which one is marked as current.
6. Press another design, for example **Admin 3** or **Classic**. The page reloads in that design.
7. Compare the header and the left column with the design you came from. Note two differences.
8. Open the **Interface** tool again and press the first design of the list (the siteaccess's own) to clear the
   switch.
9. If the header has the color mode button (**Color mode: light** in admin4 and admin4l), press it and watch the
   page change to dark; press it until it shows the mode you started with.

## Expected result

- The sheet shows the public chain and the administration chain side by side, with different site designs.
- Step 6: the administration is drawn in the other design, on the same page; step 8 brings the original design back.
- Step 9: the dark mode changes cards and columns as well as the page background.
- The trainer checks that every learner ended in the siteaccess's own design and their own color mode.

![The Administration design list of the right column, where each user picks an admin design](../../../images/trainers/td-design-switch.webp)

## Quiz

1. Which administration design is complete on its own, with a light and a dark mode?
2. A learner switches to **Classic** in the **Interface** tool. Who sees the classic design?
   A. Every user of the administration B. Only that learner, until sign-out C. All editors D. Visitors of the site
3. Which design does the `editor` siteaccess fall back to first?
4. Which policy does a role need so that the design buttons appear?
5. Where does an administrator set the administration design for everyone?

<details>
<summary>Answers</summary>

1. `admin4` ([The admin4 design](../../../../features/6.0/admin4-design.md)).
2. **B.** The switch changes the design for the user's own requests only
   ([Choosing the administration design for your session](../../../../features/6.0/ui-switch-design.md)).
3. `admin4l`.
4. `uiswitch/design`.
5. `SiteDesign` in the administration siteaccess's settings, `settings/siteaccess/admin/site.ini.append.php`, for
   example with `exp:ini set ... siteaccess:admin`, followed by a cache clear.

</details>

## Further reading

- [The admin4 design](../../../../features/6.0/admin4-design.md): switching, light and dark mode, remembered choices.
- [The admin4l design](../../../../features/6.0/admin4l.md): the administration page built from layouts.
- [Choosing the administration design for your session](../../../../features/6.0/ui-switch-design.md): the
  **Interface** tool in detail.
- [The setup wizard's new look, and the editor siteaccess](../../../../features/6.0/setup-wizard-and-editor-siteaccess.md).

## Related pages

- [Module 1: Designs and the fallback chain](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
