---
title: "Lesson 11.1: The REST API at a glance"
audience: trainer, developer
category: training
order: 111
summary: Learners locate the parts of the REST interface, read the v1 and v2 routes, explain providers, prefixes and versions, and find every REST setting in rest.ini. 40 minutes.
keywords: developers course, REST, API, ezprestapi, v1, v2, routes, provider, prefix, ezp, ezpl, auth, rest.ini, ApiPrefix, ProviderClass, RequireAuthentication, ezp7xRestApiProvider, ezp7xRestContentController
type: help
---
# Lesson 11.1: The REST API at a glance

Apps, other sites and scripts talk to Exponential through its REST interface. Before learners call it (lesson 11.2)
or extend it (lesson 11.3), they need the map: which extension serves what, which routes answer at which version,
and which settings decide how it behaves.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 11: The REST API](README.md) |
| Duration | 40 minutes (20 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Module 8](../module-08-settings-and-siteaccesses/README.md) |

## Learning objectives

After this lesson, learners can:

- name the kernel's REST framework, the `ezprestapi` extension and what each serves;
- read a route: `/api/<prefix>/<version>/<path>`;
- tell which routes answer at v1 and v2, and that writes answer at v2 only;
- explain that every call checks the user's content policies;
- find and read the REST settings with `exp:ini`.

## Trainer's notes

### How to present it

Source: [REST API v1 and v2: one extension](../../../../guides/rest-api-v1-and-v2.md).

1. **What serves what** (5 minutes). The REST framework is in the kernel (`kernel/private/rest/`,
   `settings/rest.ini`); the content routes come from the extension `ezprestapi`, whose provider
   `ezp7xRestApiProvider` serves the prefixes `ezp` and `ezpl`; the kernel's `ezpRestAuthProvider` serves `auth`
   (the OAuth2 token endpoint). `ezprestapiprovider` is deprecated.
2. **An address** (3 minutes). `/api/ezp/v1/content/node/2`: `ApiPrefix` `/api`, prefix `ezp`, version `v1`, path
   `/content/node/2`.
3. **The routes** (5 minutes). Project the table of section 2 of the guide: node, children (`list` with offset,
   limit and sort), `childrenCount`, fields, one field, the same by object; writes (create, update, delete) at v2
   only.
4. **Permissions** (3 minutes). Every read and write checks `content/read`, `content/create`, `content/edit` or
   `content/remove` of the user the request runs as, whatever it was authenticated with. Answers: 200, 201, 400, 401,
   403, 404.
5. **Settings** (2 minutes). `rest.ini`: `[System] ApiPrefix`, `[ApiProvider] ProviderClass[<prefix>]`,
   `[Authentication] RequireAuthentication`, `AuthenticationStyle`, `RequireHTTPS`.

### Common questions

**Why v1 and v2?** Existing clients call v1 and get the same answers as before; writes were added at v2.

**Is there a known problem?** `listAtom` answers 500 (section 8 of the guide). Tell learners before they try it.

**Does the REST interface use the view cache?** It has its own answer cache in the framework; content permissions are
checked for every request.

### Pitfalls

- **`ezprestapi` not active.** Without it the content routes answer as unknown. Check `ActiveExtensions`.
- **Changing `rest.ini` without clearing the INI cache.** Clear it, and reload PHP workers.
- **`RequireAuthentication=disabled` on a live site.** Requests then run as `DefaultUserID` or anonymous; only for
  public read-only data.

## Hands-on exercise

On the development installation.

1. Check that `ezprestapi` is active: `php bin/php/console exp:ini get site.ini/ExtensionSettings/ActiveExtensions | grep ezprestapi`.
2. Read the providers: `php bin/php/console exp:ini where rest.ini/ApiProvider/ProviderClass`.
3. Read the authentication settings: `php bin/php/console exp:ini list rest.ini/Authentication`.
4. Read the route table in the provider: `grep -n "new ezpMvc" extension/ezprestapi/classes/rest_provider.php`.
5. For each route, write its path and whether it answers at v1, v2 or both.
6. Call the API without credentials and read the status:
   `curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8087/api/ezp/v1/content/node/2` (use your address).
7. Find the kernel's REST framework folder and list its classes: `ls kernel/private/rest/classes/`.

## Expected result

- Step 1 prints `ActiveExtensions[]=ezprestapi` (or the extension is listed in the value).
- Step 2: `ProviderClass[ezp]=ezp7xRestApiProvider` and `ProviderClass[ezpl]`.
- Step 3: `RequireAuthentication=enabled` and the authentication style.
- Step 6: `401`, because authentication is required.
- The trainer checks the route table of step 5 with the guide's table.

## Quiz

1. Which extension serves the content routes?
2. At which versions do writes answer?
   A. v1 only B. v2 only C. v1 and v2 D. none
3. What are the parts of `/api/ezp/v2/content/node/2`?
4. Which policies does the REST interface check?
5. What does a request without credentials get when authentication is required?

<details>
<summary>Answers</summary>

1. `ezprestapi`, with the provider `ezp7xRestApiProvider`.
2. **B.** v2 only.
3. `ApiPrefix` `/api`, prefix `ezp`, version `v2`, path `/content/node/2`.
4. The user's content policies: `content/read`, `content/create`, `content/edit`, `content/remove`.
5. `401`.

</details>

## Further reading

- [REST API v1 and v2: one extension](../../../../guides/rest-api-v1-and-v2.md).
- [Remote services and apps](../../../../guides/remote-services-and-apps.md).
- [API and protocol standards](../../../../standards/apis-and-protocols.md).

## Related pages

- [Module 11: The REST API](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
