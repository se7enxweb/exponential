---
title: "Lesson 8.3: Your account and notifications"
audience: trainer, editor
category: training
order: 83
summary: Learners change their password, know how two-step sign-in protects them, follow items with notifications, set an e-mail digest and choose which e-mail they receive. 45 minutes.
keywords: editors 101, my account, change password, generate a strong password, two-step sign-in, notifications, my notification settings, items I follow, digest, e-mail preferences
type: help
---
# Lesson 8.3: Your account and notifications

An editor's account is the key to the site, and the site can tell its editors when something changes. This lesson
covers both: changing the password, two-step sign-in, following items with notifications, and choosing which e-mail
to receive.

> **Note**
> A training installation usually cannot send mail, and should not. Learners set their preferences; nobody waits for
> a message. Never put real e-mail addresses on a training site.

## At a glance

| | |
|---|---|
| Course | [Editors 101](../README.md) |
| Module | [Module 8: Dashboard, bookmarks and your account](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | New editors and authors |
| Prerequisites | [Lesson 8.2](bookmarks.md) |

## Learning objectives

After this lesson, learners can:

- open **My account** and say what each card is for;
- change their password on **Change password**, with **Generate a strong password** if they like;
- explain how two-step sign-in works and where it is turned on;
- follow an item and set an e-mail digest on **My notification settings**;
- choose which e-mail they receive on **My e-mail preferences**.

## Trainer's notes

### How to present it

Source: [Your account and signing in](../../../accounts-and-sign-in.md).

1. **My account** (3 minutes). **Edit profile** in the dashboard's left column, or your name in the user menu, leads
   to **My account**: the cards **Profile**, **Password**, **Two-step sign-in**, **Bookmarks**, **Notifications**,
   **E-mail preferences**, and for some roles **Account settings** and **API keys**.
2. **Change password** (3 minutes). **Current password**, **New password**, **Confirm new password**. The page shows
   what a new password needs (for example *At least 10 characters*) and checks it as you type. **Generate a strong
   password** makes one; **Show** reveals what you typed; **Change password** saves. A password change signs out every
   other session of the account.
3. **Two-step sign-in** (3 minutes). After the password, a 6-digit code from an authenticator app (recommended) or
   from an e-mail. Turned on with **Turn on** on the **Two-step sign-in** card; the setup asks for a code from the app
   before anything changes. Show the screens; do not make learners set it up on a training account unless they want
   to.
4. **Notifications** (4 minutes). **My notification settings**: **Items I follow** with **Add items**, and **E-mail
   digest** (*Receive all messages combined in one digest*; switched off, one e-mail per change). A digest can be
   **Daily, at** an hour, **Once per week, on** a day, or **Once per month, on day number**; **Save digest settings**
   stores it. To follow an item, choose **Add to my notifications** in its menu.
5. **E-mail preferences** (2 minutes). **My e-mail preferences** chooses which e-mail the site sends you; every
   optional e-mail can be stopped there.

### Common questions

**I lost my phone with the authenticator app.** Ask an administrator: they can switch your account to e-mail codes or
turn the second step off, and you set up the app again.

**Will I get an e-mail for every change below an item I follow?** Yes, unless you switch on the digest; then one
e-mail per day, week or month collects them.

**Why can I not see the API keys card?** Only some roles may use API keys.

### Pitfalls

- **Forgotten new passwords.** Learners change their password and forget it before the next lesson. Ask them to use
  **Generate a strong password** only if they can store it in a password manager; otherwise choose a long passphrase.
- **Two-step sign-in on shared training accounts.** If a learner turns it on and leaves, the next class cannot use
  the account. Reset training accounts after the course.
- **Real addresses.** Training accounts use `example.invalid` addresses, so notification mail cannot reach anyone.

## Hands-on exercise

Use the training installation and your Editor training account.

1. Open **Edit profile** in the dashboard's left column. Write down the cards **My account** shows.
2. Open **Change password** (from the user menu or **My account**).
3. Type your current password. Press **Generate a strong password** and **Show**, and read the generated password.
   Press **Cancel**: nothing is changed. (Change it for real only if the trainer asks you to and you can store it.)
4. Back on **My account**, open the **Two-step sign-in** card and read the choices under **Second step at sign-in**.
   Leave the page without saving.
5. In the tree, open the item menu of your course folder and choose **Add to my notifications**.
6. Open **My notification settings**. Check that your folder is listed under **Items I follow**.
7. Under **E-mail digest**, switch on *Receive all messages combined in one digest*, choose **Once per week, on**
   **Friday**, and press **Save digest settings**.
8. Open **My e-mail preferences** and read which kinds of e-mail the site offers. Leave the page without changes.

## Expected result

- The learner's notes list the cards of **My account** (at least **Profile**, **Password**, **Two-step sign-in**,
  **Bookmarks**, **Notifications**, **E-mail preferences**).
- The password is unchanged (unless the trainer asked otherwise).
- **My notification settings** lists the course folder under **Items I follow**, with a weekly digest on Friday.
- The trainer checks by opening **My notification settings** of the trainer's own account after doing the same steps.

## Quiz

1. Where do you change your password? Name one way to get there.
2. What does a password change do to your other sessions?
3. Which second step does the help recommend for two-step sign-in?
   A. E-mail one-time code B. Authenticator app (TOTP) C. Off D. A second password
4. How do you start following an item?
5. What does the e-mail digest change?

<details>
<summary>Answers</summary>

1. On **Change password**, from the user menu, the dashboard's left column or **My account**
   ([Your account and signing in](../../../accounts-and-sign-in.md#change-your-password)).
2. It signs them out ([Logging into the admin](../../../logging-into-the-admin.md#staying-signed-in-and-signing-out)).
3. **B.** It works without a network ([Your account and signing in](../../../accounts-and-sign-in.md#two-step-sign-in)).
4. Choose **Add to my notifications** in the item's menu, or **Add items** on **My notification settings**.
5. Instead of one e-mail per change, one e-mail per day, week or month collects the messages.

</details>

## Further reading

- [Your account and signing in](../../../accounts-and-sign-in.md): the account, passwords and two-step sign-in.
- [Two-step sign-in](../../../../guides/two-factor-login.md): for administrators who set it up.
- [Notifications](../../../../features/6.0/notifications.md): how notifications and digests work.

## Related pages

- [Module 8: Dashboard, bookmarks and your account](README.md)
- [Editors 101: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
