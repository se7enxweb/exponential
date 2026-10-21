---
title: "Project 7: reading content over the REST API"
audience: student
category: learning
order: 31
summary: Study project 7 - check that the ezprestapi extension is active, give a test user the right to make a personal API key with the read scope, make a key, read nodes, children and fields with curl at v1 and v2, handle error answers, write a small script that keeps the key out of the code, and hand in evidence, graded with a rubric.
keywords: study project, REST API, ezprestapi, personal API key, expk, Bearer, curl, v1, v2, content/node, fields, scopes, apikey/create, JSON, learner
type: help
---
# Project 7: reading content over the REST API

Exponential serves its content as JSON over a REST API, which apps and other systems use. In this project you read
your club site's content from the command line with a personal API key, the way a script or a mobile app does. The
steps follow [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) and [Personal API keys](../../guides/api-keys.md).

## Contents

1. [At a glance](#at-a-glance)
2. [Goal](#goal)
3. [Before you start](#before-you-start)
4. [Tasks](#tasks)
5. [Hand in](#hand-in)
6. [Rubric](#rubric)
7. [Stretch goals](#stretch-goals)

## At a glance

| | |
|---|---|
| Level | Intermediate |
| Time | About 3 hours |
| Paths | Developer |
| Prerequisites | [project 2](project-02-a-small-site-structure.md); a terminal with `curl`; [project 9](project-09-roles-for-a-restricted-editor.md) helps with task 2 |

## Goal

A test user with a read-only personal API key, a set of curl calls that read your club folder, its children and
their fields at v1 and v2, a table of the error answers you met, and a short script that reads the key from the
environment.

## Before you start

Know the facts from the guides:

- One extension, `ezprestapi`, serves both versions. **Reads** answer at `/api/ezp/v1/...` and `/api/ezp/v2/...`;
  **writes** answer at v2 only ([REST API v1 and v2](../../guides/rest-api-v1-and-v2.md#in-short)).
- Every read and write checks the user's policies, whatever the credential.
- A personal API key is made on the site, shown **once**, and sent as `Authorization: Bearer expk_...`. Keys are on
  in every installation, but nobody may make one until a role gives the policy `apikey/create`
  ([Personal API keys](../../guides/api-keys.md#5-switching-keys-on-for-your-users)).

## Tasks

1. **Check the extension.** Open **Setup > Extensions** and confirm that `ezprestapi` is active. The default site
   package activates it ([Extensions sevenx_multisite activates](../site-packages/sevenx-multisite/extensions-it-activates.md)).
2. **Prepare a test user.** Create a user "api-reader" with an address at `example.invalid`, in a group whose role
   lets it sign in to the site (the guide's example is the Member role). Give it a role with
   `content/read` and `apikey/create` limited to the **Scope** `read` ([Personal API keys](../../guides/api-keys.md#5-switching-keys-on-for-your-users)).
   Do not use the administrator for API work: a key never goes beyond its owner's policies, so a limited owner
   limits the damage of a lost key.
3. **Make a key.** Sign in as the test user, open **My account > API access** (`/apikey/list`), name the key "Study
   project 7", tick only the read scope, choose 7 days and press **Make the key**. Copy it into an environment
   variable, not into a file:

   ```bash
   export EXP_API_KEY='expk_...'
   ```

   The API access page shows a ready-made `curl` line with the path of your installation.
4. **Read one node.** `curl -s -H "Authorization: Bearer $EXP_API_KEY" <your site>/api/ezp/v1/content/node/<node id>`,
   with the node number of your club folder.
5. **Read the children** with `/list/offset/0/limit/20?ResponseGroups=Fields`, then their number with
   `/childrenCount`, then one field of one article with `/content/object/<object id>/field/title`
   ([The routes](../../guides/rest-api-v1-and-v2.md#2-the-routes)).
6. **Compare v1 and v2** for the same node, and try `?OutputFormat=xhtml` at v2.
7. **Meet the errors.** Call without the header, with a wrong key, with an unknown node, and try a write such as a
   create at v2. Record each status and body; the read-only key should be refused for the write.
8. **Write a script** in Python or PHP that reads the club folder's children and prints their names, reading the key
   from `EXP_API_KEY` ([Using a key with curl](../../guides/api-keys.md#3-using-a-key-with-curl) has a Python example).
9. **Revoke the key** on `/apikey/list` when you are done, and show that the next call is refused.

## Hand in

- The commands you ran, with the key replaced by `expk_...`, and shortened answers.
- A table of the status codes you met and what each means
  ([Using a key with curl](../../guides/api-keys.md#3-using-a-key-with-curl) lists them).
- The script, in your Git repository, without any key in it or in its history.
- A short report: why the test user, why the read scope only, and what changed between v1 and v2.

## Rubric

Score the four common criteria of [Grading your own work](grading-your-own-work.md), and these checks (8):

1. `ezprestapi` was shown to be active.
2. The key belongs to a limited test user, not to the administrator.
3. The key has the read scope only and a short lifetime.
4. Node, children, child count and one field were read.
5. At least four different error answers are documented.
6. A write with the read-only key was refused, and the report explains why.
7. No key appears in the hand-in, the script or the repository history.
8. The key was revoked at the end.

## Stretch goals

- Read about OAuth2 tokens for applications that act for other users
  ([Authentication](../../guides/rest-api-v1-and-v2.md#3-authentication)) and explain when a key is the better choice.
- Give a second key the publish scope on a separate subtree, create a folder at v2 and remove it again.
- Follow the trainers' [Developers](../trainers/developers/README.md) course, module 11, on the REST API.

## Related pages

- [Study projects: overview](study-projects-overview.md)
- [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md)
- [Personal API keys](../../guides/api-keys.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
