---
title: How do I give an external application an API key?
audience: admin, developer
category: manual
order: 560
summary: For a script that acts as one user, that user makes a personal API key under My account > API keys; for an application that signs users in, an administrator registers it under Setup > oAuth admin > New application and gives it the client identifier and secret.
keywords: API key, REST API, external application, oAuth, client identifier, client secret, personal API key, apikey/create, oauthadmin
context: oauthadmin/list
type: help
---
# How do I give an external application an API key?

There are two kinds of access. Choose by what the application does:

- a script or tool that publishes **as one user** gets a **personal API key** of that user;
- an application that lets **many users sign in** (a mobile app, a portal) is registered as a **REST application**
  and gets a client identifier and secret.

## Who can do this

A personal key: the user, with the policy `apikey/create`. A REST application: users with access to **oAuth admin**
(administrators).

## Steps

1. **Personal API key:** sign in as the user the script acts for (a dedicated account with only the rights it needs).
   Open **My account** and click **Manage my API keys**.
2. Under **Make a new key**, enter a **Name** that says where the key is used, tick the **Scopes** it needs and choose
   how long it is **Valid for**. Click **Make the key**.
3. Copy the key with **Copy key** and put it into the application's settings. It is shown only once.
4. **REST application:** open **Setup** > **oAuth admin** and click **New application**.
5. Enter the **Name**, a **Description** shown to users, and the **Endpoint URI** the application returns to. Save it.
6. Give the application's developers the **Client identifier** and the **Client secret** (**Show the secret**) through
   a safe channel.

## What can go wrong

- **The key was lost.** Nobody can show it again. Revoke it (**Revoke the key**) and make a new one.
- **The application may do too much.** A key never exceeds its user's rights, so use an account with few rights, and
  tick only the scopes needed.
- **A key leaked.** Revoke it at once. Administrators see all keys under **Setup** > **oAuth admin** > **API keys**, and
  every use is in the audit trail.

## Related pages

- [Personal API keys](../../guides/api-keys.md)
- [Remote services and apps](../../guides/remote-services-and-apps.md)
- [REST API for owners](../customers/growth/rest-api-for-owners.md)
- [User manual](README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
