---
title: Keep editors on the admin4 design
audience: government, admin
category: public-sector
order: 450
summary: Step by step - make admin4, the administration design Exponential tests against WCAG 2.2 AA, the design of the administration siteaccess, and limit the per-session design switch so staff with disabilities stay in the tested environment.
keywords: admin4, admin4l, administration design, SiteDesign, design switch, uiswitch, accessibility, WCAG 2.2, classic admin, editors, assistive technology, public sector
type: help
---
# Keep editors on the admin4 design

For administrators. The project's accessibility work and tests cover admin4 and admin4l; the classic administration and
admin3 are only partly fixed ([What Exponential's admin4 covers](../../accessibility/admin4-what-is-covered.md)). An
accessibility statement or procurement answer about the editing environment holds only if editors use admin4.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- Rights to change the administration siteaccess's settings (or the versioned settings with `ManagedByDeployment`).
- Know which extensions' admin screens your editors use; some exist only in classic designs.

## Steps

1. In the administration siteaccess's `site.ini.append.php`:

   ```ini
   [DesignSettings]
   SiteDesign=admin4
   ```

   ([The admin4 design: Switch to it](../../../../features/6.0/admin4-design.md#switch-to-it)).
2. Clear the caches: `php bin/php/ezcache.php --clear-all --allow-root-user`; reload PHP or deploy on Velocity.
3. If the per-session design switch is offered, limit its list to admin4 and admin4l in
   `settings/override/uiswitch.ini.append.php` (`[UISwitch_design] AllowedValues[]`), keeping a way back in every offered
   design ([The administration design switch](../../../../guides/ui-switch-design.md)). Or give the policy
   `uiswitch/design` only to those who need other designs.
4. Tell editors about [Keyboard shortcuts](../../../keyboard-shortcuts.md) and the light, dark and system color modes.

## Check that it worked

- The administration header shows the color mode switch; the first Tab shows "Skip to main content".
- A test editor cannot switch to a design outside the list.

## Related pages

- [What Exponential's admin4 covers](../../accessibility/admin4-what-is-covered.md)
- [The admin4 design](../../../../features/6.0/admin4-design.md)
- [UI switch settings](../../../../guides/ui-switch-settings.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
