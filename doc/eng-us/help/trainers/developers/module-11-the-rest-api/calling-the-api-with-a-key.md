---
title: "Lesson 11.2: Calling the API with a personal key"
audience: trainer, developer
category: training
order: 112
summary: Learners allow API keys for a test account with the apikey/create policy, make a key with the read and publish scopes, read nodes and children with curl, create and remove a folder through v2, and revoke the key. 55 minutes.
keywords: developers course, REST, API key, expk_, Bearer, apikey/create, scopes, My account, API access, curl, v1, v2, create, delete, OAuth2, token, revoke, 401, 403
type: help
---
# Lesson 11.2: Calling the API with a personal key

A script of your own talks to the REST interface with a **personal API key**: no password in the script, limited to
the scopes you chose, and never beyond the owner's own rights. In this lesson learners allow keys for a test account,
make a key, read and write content with `curl`, and revoke the key again.

> **Note**
> Use a test account with an address at `example.invalid` on your training installation. Keep the key in an
> environment variable, never in a file you commit.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 11: The REST API](README.md) |
| Duration | 55 minutes (15 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 11.1](the-rest-api-at-a-glance.md); an administrator account and a test user account |

## Learning objectives

After this lesson, learners can:

- grant `apikey/create` (with a scope limitation) to a role;
- make a key on **My account** > **API access** and copy it once;
- read a node and its children at v1 and v2 with `curl` and `Authorization: Bearer`;
- create and remove a folder through v2, and read 401, 403 and 404 answers;
- explain when OAuth2 tokens fit better than personal keys, and revoke a key.

## Trainer's notes

### How to present it

Sources: [Personal API keys](../../../../guides/api-keys.md) and
[REST API v1 and v2, sections 3 and 4](../../../../guides/rest-api-v1-and-v2.md#3-authentication).

1. **Keys versus tokens** (3 minutes). A personal key is for a script of the user's own; OAuth2 is for an application
   that lets other users sign in and act for them (registered on **Setup** > **oAuth admin**).
2. **Switching keys on** (3 minutes). Keys are on by default (`rest.ini [ApiKeySettings] ApiKeys=enabled`) but no
   role may make one until it has the policy `apikey/create`, optionally limited by `Scope`.
3. **Making a key** (3 minutes). **My account** > **API access** (`/apikey/list`): a name, the scopes, the validity,
   **Make the key**. The key is shown once.
4. **Calls** (4 minutes). `curl -H "Authorization: Bearer $EXP_API_KEY" .../api/ezp/v1/content/node/2`; writes at v2.
5. **Answers** (2 minutes). 200 and 201 on success; 401 without a valid key; 403 `access_denied` when the user may not;
   404 for an unknown node.

### Common questions

**Can a key do more than its owner?** Never: a request runs as the key's owner, limited to the key's scopes and the
owner's policies.

**What if the key leaks?** Revoke it on **API access** and make a new one; keys are shown once and not stored in
clear text.

**Do I need HTTPS?** On any machine reachable by others, yes; `[Authentication] RequireHTTPS=enabled` refuses plain
HTTP.

### Pitfalls

- **Keys in shell history on a shared machine.** Use `read -s` or an environment file that is not committed.
- **Testing as an administrator.** Use a normal test account so permissions behave like a real client's.
- **Writes at v1.** They do not exist there; use v2.

## Hands-on exercise

On your training installation. You need an administrator account and a test user (e-mail at `example.invalid`).

1. As administrator, open **Users** > **Roles and policies**, open the role of your test user (or a new role
   assigned to it), **Edit**, **New policy**, module `apikey`, function `create`; leave the limitation empty or choose
   **Scope**; **Save**.
2. Make sure the role allows `content/read` and `content/create` in your course folder.
3. Sign in to the site as the test user, open **My account**, then **API access**.
4. Under **Make a new key**, name it `Course script`, tick the read and publish scopes, choose 7 days, and press
   **Make the key**. Copy the key.
5. In the shell: `read -s EXP_API_KEY` (paste the key, press Enter) and `export EXP_API_KEY`.
6. Read node 2 at v1 and at v2:

   ```bash
   curl -s -H "Authorization: Bearer $EXP_API_KEY" http://127.0.0.1:8087/api/ezp/v1/content/node/2
   curl -s -H "Authorization: Bearer $EXP_API_KEY" "http://127.0.0.1:8087/api/ezp/v2/content/node/2?OutputFormat=xhtml"
   ```

7. Read the first five children with fields:
   `curl -s -H "Authorization: Bearer $EXP_API_KEY" "http://127.0.0.1:8087/api/ezp/v1/content/node/2/list/offset/0/limit/5?ResponseGroups=Fields"`.
8. Create a folder in your course folder (v2):

   ```bash
   curl -s -H "Authorization: Bearer $EXP_API_KEY" http://127.0.0.1:8087/api/ezp/v2/content/node/create \
        -d parentNodeID=<course folder node id> -d classIdentifier=folder -d languageLocale=eng-US -d name='From a script'
   ```

9. Try to create one below node 2, where the test user may not create, and read the answer.
10. Remove the folder of step 8 (v2): `curl -s -X DELETE -H "Authorization: Bearer $EXP_API_KEY" http://127.0.0.1:8087/api/ezp/v2/content/node/delete/<node id>`.
11. On **API access**, revoke the key, then repeat step 6.

## Expected result

- Step 6: JSON of node 2; at v2 with `renderedOutput` filled.
- Step 8: `{"message":"Created","objectId":...,"nodeId":...}` and the folder in the administration.
- Step 9: `403` with `access_denied`.
- Step 10: `{"message":"Removed",...}`.
- Step 11: `401` after the revoke.
- The trainer checks that every learner revoked the key.

## Quiz

1. Which policy lets a user make API keys?
2. How is a personal key sent?
   A. As a cookie B. As `Authorization: Bearer expk_...` C. As a form field `password` D. In the URL path
3. A key with the publish scope belongs to a user without `content/create` below node 2. What does a create there
   answer?
4. When should an application use OAuth2 instead of personal keys?
5. What do you do when a key leaked?

<details>
<summary>Answers</summary>

1. `apikey/create`, optionally limited by `Scope`.
2. **B.** `Authorization: Bearer expk_...`.
3. `403 access_denied`: a key never goes beyond its owner's policies.
4. When it lets other users sign in and act for them.
5. Revoke it on **API access** and make a new one.

</details>

## Further reading

- [Personal API keys](../../../../guides/api-keys.md): scopes, auditing, limits and every setting.
- [REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md): routes, authentication, configuration.
- [Security and audit](../../../../guides/security-and-audit.md).

## Related pages

- [Module 11: The REST API](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
