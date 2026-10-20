---
title: Set the minimum password length to 12
audience: government, admin
category: public-sector
order: 20
summary: Step by step - raise Exponential's minimum password length from the shipped 10 to 12 (the OWASP ASVS 4.0.3 level 1 value) or more, without locking anyone out, and check that new passwords follow it.
keywords: minimum password length, MinPasswordLength, password policy, ASVS V2.1.1, 12 characters, 15 characters, NIST, settings override, exp:ini, public sector
type: help
---
# Set the minimum password length to 12

For administrators whose security policy or an audit asks for at least 12 characters. Exponential ships
`[UserSettings] MinPasswordLength=10`, below OWASP ASVS 4.0.3 V2.1.1, which asks for 12
([Mapping OWASP ASVS](../../security/owasp-asvs-mapping.md)). Many organizations choose 15, as the installation guide
recommends following NIST SP 800-63B-4.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [If something goes wrong](#if-something-goes-wrong)
5. [Related pages](#related-pages)

## Before you start

- Shell access to the installation, as the site's user or with `--allow-root-user`.
- Decide the value with your security officer: 12 meets ASVS level 1; 15 meets the NIST guidance as well.
- Tell staff: the rule applies the next time a password is set; nobody is locked out.

## Steps

1. Preview the change:

   ```bash
   ./console exp:ini set site.ini/UserSettings/MinPasswordLength 12 global --dry-run --allow-root-user
   ```

2. Write it (scope `global` is `settings/override/site.ini.append.php`):

   ```bash
   ./console exp:ini set site.ini/UserSettings/MinPasswordLength 12 global --allow-root-user
   ```

3. Clear the INI cache and let PHP see it: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, then reload
   PHP-FPM, or `./console exp:velocity deploy --allow-root-user` on Velocity.

## Check that it worked

- `./console exp:ini get site.ini/UserSettings/MinPasswordLength --allow-root-user` prints 12.
- As a test user, open **My account** > **Password** and try an 11-character password: it is refused.
- The audit shows the setting write: `/audit/console/(name)/system.setting.write`.

## If something goes wrong

- The length counts **bytes**: passwords in scripts with multi-byte characters reach it with fewer characters.
- Setting writes from the administration are refused when `ManagedByDeployment` is on: put the line into your
  versioned settings instead.
- To go back, set the old value the same way.

## Related pages

- [Password policy](../../security/password-policy.md)
- [13.6 Passwords and sign-in](../../../../install/13-security-hardening.md#136-passwords-and-sign-in)
- [Add your own words to the weak password list](add-your-own-words-to-the-weak-password-list.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
