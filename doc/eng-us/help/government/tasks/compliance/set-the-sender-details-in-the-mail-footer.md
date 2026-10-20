---
title: Set the sender details in the mail footer
audience: government, admin
category: public-sector
order: 300
summary: Step by step - fill in the organization name, postal address and privacy notice link that Exponential's mail gate adds to every optional mail (they ship empty), and check them on the status page and in a test mail.
keywords: mail footer, OrganisationName, OrganisationAddress, PrivacyURL, FooterSettings, mailpreferences.ini, sender details, postal address, CAN-SPAM, CASL, exp:mail:status, public sector
type: help
---
# Set the sender details in the mail footer

For administrators. Every optional mail ends with a footer naming the sender, with an unsubscribe link. The
organization name and postal address ship **empty**; the status page and `exp:mail:status` warn while they are
([Mail preferences for administrators: The footer](../../../../guides/mail-preferences-administrator.md#22-the-footer-who-sends-and-from-where)).

## Contents

1. [Steps](#steps)
2. [Check that it worked](#check-that-it-worked)
3. [Related pages](#related-pages)

## Steps

1. Preview and set the name and the address (a line break is written `\n`):

   ```bash
   ./console exp:ini set mailpreferences.ini/FooterSettings/OrganisationName "Department of Example" global --dry-run --allow-root-user
   ./console exp:ini set mailpreferences.ini/FooterSettings/OrganisationName "Department of Example" global --allow-root-user
   ./console exp:ini set mailpreferences.ini/FooterSettings/OrganisationAddress "Example Street 1\n12345 Exampletown" global --allow-root-user
   ```

2. The privacy notice link: `[FooterSettings] PrivacyURL` takes a full `https://` address or a path such as `/privacy`;
   empty uses the node of `menu.ini [SiteInfo] PrivacyPolicyID` of the public siteaccess, if there is one.

   ```bash
   ./console exp:ini set mailpreferences.ini/FooterSettings/PrivacyURL /privacy global --allow-root-user
   ```

3. Clear the INI cache; reload PHP; restart Velocity.
4. To change the footer's wording, override `design:mailpreferences/mail/footer.tpl` in your design, keeping the
   unsubscribe link, the manage link and the address.

## Check that it worked

- `php bin/php/console exp:mail:status --allow-root-user` no longer warns about sender details.
- **Setup > E-mail preferences** status page shows the sender details.
- A test mail of an optional category (to a test address, with the file transport on a test system) ends with the
  footer and carries `List-Unsubscribe`.

## Related pages

- [One-click unsubscribe](../../privacy/one-click-unsubscribe.md)
- [13.13 Mail: consent and suppression](../../../../install/13-security-hardening.md#1313-mail-consent-and-suppression)
- [Mail categories and opt-in](../../privacy/mail-categories-and-opt-in.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
