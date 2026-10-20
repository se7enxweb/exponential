---
title: "Lesson 3.2: Managing users and subscriptions"
audience: trainer, editor, admin
category: training
order: 32
summary: Learners find newsletter users with the filters of the Users page, read a user's subscriptions, create a test user, and approve a pending subscription. 45 minutes.
keywords: newsletters with cjw newsletter, newsletter users, user_list, subscription list, approve, create newsletter user, status filter, pending, confirmed, approved
type: help
---
# Lesson 3.2: Managing users and subscriptions

Most day-to-day newsletter work is looking people up: has this person subscribed, why do they not receive the
newsletter, can you add a colleague by hand. This lesson teaches the two places for that: the **Users** page across
all lists, and the subscription list of one list.

> **Note**
> Every user created in this lesson has an address at `example.invalid`. Any mail a step writes goes to a file
> in `var/log/mail`, because all transports of the training installation are set to `file` (lesson 1.2).

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 3: Subscribers](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Editors and administrators who look after subscribers |
| Prerequisites | [Lesson 3.1](the-subscribe-form-and-double-opt-in.md): Anna confirmed and Ben pending on the learner's list |

## Learning objectives

After this lesson, learners can:

- search the **Users** page by address, status and list;
- open a newsletter user with **Details** and read their subscriptions and statuses;
- create a newsletter user by hand with **Create Newsletter user**;
- approve a pending subscription in a list's subscription list.

## Trainer's notes

### How to present it

Sources: [Where subscribers are managed](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#where-subscribers-are-managed)
and the [view reference](../../../../specifications/6.0/cjw_newsletter.md#module-newsletter).

1. **Users and subscriptions** (3 minutes). A *newsletter user* is one address. Each of their *subscriptions* links
   them to one list with its own status. One person, several lists, several statuses.
2. **The Users page** (5 minutes). **Users** in the left menu (`newsletter/user_list`). Filters: **Email**,
   **Status** (*All*, *Confirmed*, *Pending*, *Removed*, *Bounced*, *Blacklisted*) and **List**, then
   **Search for existing user**. Columns **UID**, **Email**, **Name**, **Lists**, **Status**, **Bounce**,
   **Created**. Each row has **Details** and **Edit**. The page sorts, filters and pages; 10, 25, 50 or 100 per page.
3. **A list's subscriptions** (4 minutes). On a list's page, **Subscriptions** and the status links lead to
   `newsletter/subscription_list/<node>`. Columns include **Exponential User**, **Format** and **Status**; each row
   has **Details**, **Approve** and **Edit**.
4. **Adding by hand** (3 minutes). **Create Newsletter user** on the Users page, or **Create new Subscription** on the
   list page. Adding someone by hand needs their consent just like the form; note where it came from.

### Common questions

**What is the difference between confirmed and approved?** Confirmed means the person clicked the link; approved
means the list (or an administrator) accepted the subscription. Both receive editions.

**Why does a user show "Exponential User"?** A newsletter user can be linked to a user account of the site, for
example when a registered user subscribes.

**Can I delete a user?** Removing a newsletter user also removes all their subscriptions. In most cases the person
should rather unsubscribe or be blacklisted, so you keep a record (lesson 3.4).

**The dashboard reports orphaned subscriptions.** Those belong to newsletter users that no longer exist; the
dashboard's **Remove them** or `ext:cjw_newsletter:repair` cleans them up.

### Pitfalls

- **Searching other learners' users.** The Users page shows every list. Filter by your list first.
- **Approving the wrong row.** The subscription list has many rows; check the e-mail before **Approve**.
- **Editing the e-mail address.** Changing an address by hand skips the double opt-in for the new address. Prefer a
  new subscription through the form.

## Hands-on exercise

Use the training installation. Replace `<name>` with your first name.

1. Open **Newsletter** and press **Users** in the left menu.
2. In **List**, choose `Weekly tips <name>` and press **Search for existing user**. Anna and Ben are listed.
3. Set **Status** to *Pending* and search again. Only Ben remains.
4. Press **Details** on Ben's row and read his subscriptions: list, status, format.
5. Press **Create Newsletter user**. **Email**: `carla-<name>@example.invalid`, **First name** `Carla`,
   **Last name** `Test`. Subscribe her to your list in the form (the trainer shows where), then save.
6. Back on the Users page, find Carla with the **Email** filter.
7. Open your list's page and press **Pending** in its subscription links.
8. Find Ben's row and press **Approve**.
9. Press **Approved** and **Confirmed** in turn and note who is listed under each.
10. Write in one sentence why Ben now receives editions although he never clicked the confirmation link.

## Expected result

- The learner's list has Anna (confirmed), Ben (approved by an administrator) and Carla.
- The Users page finds each of them by e-mail and by list.
- Any mail these steps produced is a file in `var/log/mail` (check with `ls -lt var/log/mail | head -3`); nothing left the machine.
- The trainer checks the list's subscription counts on the projector.

## Quiz

1. Where do you search across all lists for one address?
2. Which statuses can you filter by on the **Users** page?
3. What does **Approve** do in a subscription list?
   A. sends a confirmation mail B. makes the subscription receive editions C. blacklists the address
   D. deletes the user
4. What else is removed when a newsletter user is removed?
5. Why should a person added by hand still have agreed to receive the newsletter?

<details>
<summary>Answers</summary>

1. On the **Users** page (`newsletter/user_list`) ([specification](../../../../specifications/6.0/cjw_newsletter.md#module-newsletter)).
2. Confirmed, pending, removed, bounced and blacklisted, or all ([specification](../../../../specifications/6.0/cjw_newsletter.md#module-newsletter)).
3. **B.** Approved subscriptions receive editions ([Statuses](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#statuses)).
4. All their subscriptions ([specification](../../../../specifications/6.0/cjw_newsletter.md#module-newsletter)).
5. Consent is needed however the address reaches the list ([Consent](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#consent)).

</details>

## Further reading

- [Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#where-subscribers-are-managed):
  where subscribers are managed.
- [Specification: the cjw_newsletter extension](../../../../specifications/6.0/cjw_newsletter.md#module-newsletter):
  every view and its parameters.
- [Roles and policies](../../../../guides/roles-and-policies.md): the `user_list`, `user_edit` and
  `subscription_list` policy functions.

## Related pages

- [Module 3: Subscribers](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
