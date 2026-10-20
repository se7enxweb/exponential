---
title: Social login and privacy
audience: government, project-manager, admin
category: public-sector
order: 250
summary: For public bodies considering "sign in with" buttons - the social login handlers of the sevenx_authentication_2fa extension (skeletons for Google, Facebook, Twitter/X, Instagram, Meta and ID.me), what data flows to and from the provider, and the questions to settle before offering them.
keywords: social login, OAuth, sign in with Google, identity provider, sevenx_authentication_2fa, user2fa/oauth, privacy, third party, verified addresses, government identity, public sector
type: help
---
# Social login and privacy

This page is for project managers, administrators and data protection officers. The extension
`sevenx_authentication_2fa` includes social login handlers, described as "skeletons ready to customise", for Google,
Facebook, Twitter/X, Instagram, Meta and ID.me, a base class, and a command-line builder for more providers
([sevenx_authentication_2fa](../../../features/6.0/extensions/sevenx_authentication_2fa.md)). Buttons such as "Continue
with Google" appear on the login page for each provider enabled in `sevenxauthentication2fa.ini`
([Two-step sign-in: Social login](../../../guides/two-factor-login.md#social-login)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What happens](#what-happens)
2. [Questions to settle](#questions-to-settle)
3. [Related pages](#related-pages)

## What happens

- The visitor chooses a provider and signs in there; the provider sends back an identity the site matches to an
  account.
- The extension checks a one-time state bound to the provider and refuses addresses the provider has not verified.
- A user found this way still has to pass their own second step, if two-step sign-in is on.
- The provider learns that the person uses your site; the site receives what the provider shares.

The privacy statement lists social login among third-party services: "the user chooses it; mention in the privacy
notice" ([Privacy standards](../../../standards/privacy.md#third-party-services-in-shipped-extensions)).

## Questions to settle

1. **Who needs it?** Staff should sign in with organizational accounts and two-step sign-in, not social accounts.
   For citizens' accounts, a public body may prefer its country's government identity service where one exists; the
   handler base class and builder exist for adding providers, which is development work.
2. **Which data do you request?** As little as the scopes allow (identity and verified e-mail).
3. **Is there an alternative?** People without an account at the provider must be able to use the service.
4. **Notice**: name each provider, the data exchanged and the purpose.
5. **Accounts**: what happens to the local account when the provider account goes away.

## Related pages

- [Two-step sign-in](../../../guides/two-factor-login.md)
- [Third-party extensions and privacy](third-party-extensions.md)
- [Two-step sign-in for public staff](../security/two-step-sign-in.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
