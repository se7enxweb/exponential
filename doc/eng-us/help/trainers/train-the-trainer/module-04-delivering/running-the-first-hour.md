---
title: "Lesson 4.1: Running the first hour"
audience: trainer
category: training
order: 41
summary: Open a course so that everyone is signed in, safe and oriented within the first hour, introductions, the lab check, the first sign-in, changing the generated password, and finding the Help center. 60 minutes.
keywords: first hour, course opening, welcome, lab check, first sign-in, change password, initial-admin-password, help center, orientation
type: help
---
# Lesson 4.1: Running the first hour

The first hour decides whether a course runs smoothly. By its end, every learner should be signed in to a working
installation with a password of their own, and know where the help is. This lesson is a script for that hour, which
trainers practise by running it for each other.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 4: Delivering a course](README.md) |
| Duration | 60 minutes (15 presenting, 45 practice: each learner runs a short opening) |
| Level | Intermediate |
| Audience | Trainers |
| Prerequisites | [Module 2](../module-02-building-the-lab/README.md); [Lesson 1.5](../module-01-preparing-to-teach/names-and-history-in-class.md) |

## Learning objectives

After this lesson, learners can:

- run an opening that covers introductions, goals, the lab check and the first sign-in;
- get every learner to change a generated administrator password and delete the password file;
- show the "?" button and the Help center as the place to look first;
- spot a learner whose lab does not work, and route them to a helper.

## Trainer's notes

### How to present it

Show the script on one slide, then let learners run parts of it for each other (15 minutes of presenting):

| Minute | What happens |
|---|---|
| 0-10 | Welcome, the two-minute opening (lesson 1.5), what the course covers, breaks, how to ask |
| 10-25 | Lab check: every learner opens the administration of their installation (or the shared site) |
| 25-35 | First sign-in; change the password: user menu, **Change password**; delete `var/log/initial-admin-password` |
| 35-45 | The "?" button, the Help center, the search; "look here first" |
| 45-60 | First small task (for example open **Content structure** and find one article), questions |

### Common questions

**What if a lab does not start?** A helper takes over; the learner pairs with a neighbour until it works. Do not stop
the room.

**Why change the password in a training lab?** It is the first habit of a safe administrator, and the exp:install
summary itself says to log in, change it, then delete the file.

### Pitfalls

- **Starting the content before the lab works.** Everything after depends on it.
- **The generated password shared on a projector.** Learners read their own from their own terminal.
- **Skipping the help.** Learners who know the "?" ask fewer questions later.

## Hands-on exercise

Work in groups of three: trainer, learner, observer. Rotate.

1. The "trainer" gives the two-minute opening (lesson 1.5).
2. The "learner" opens the administration of their training installation and signs in as `admin` with the password
   from the install summary or `var/log/initial-admin-password`.
3. The learner opens the user menu (their name in the header) and chooses **Change password**. They fill in
   **Current password**, **New password** and **Confirm new password** (or use **Generate a strong password**), then
   press **Change password**.
4. In the terminal, the learner deletes the password file: `rm var/log/initial-admin-password`.
5. The trainer asks the learner to open the Help center through the "?" button and search for `publish`.
6. The observer times each part and notes one thing to improve.

## Expected result

- Every learner in the group is signed in with a password of their own; the password file is gone.
- Each learner found the Help center and a search result for `publish`.
- The observer's notes name one improvement per opening; the group's total time is under 60 minutes.

## Quiz

1. What must every learner have done by the end of the first hour?
2. Where is the generated administrator password written, and what happens to that file?
3. Where is **Change password** in the administration?
4. A learner's lab does not start. What do you do?

<details>
<summary>Answers</summary>

1. Be signed in to a working installation with their own password, and know where the help is.
2. `var/log/initial-admin-password`; delete it after the first login and the password change
   ([The console install, 7.5](../../../../install/07-console-install.md#75-the-administrator-password)).
3. In the user menu (the user's name in the header), entry **Change password**.
4. Give them a helper and pair them with a neighbour; keep the room moving.

</details>

## Further reading

- [Logging into the admin](../../../logging-into-the-admin.md)
- [Change the admin password](../../../change-the-admin-password.md)
- [Getting started](../../../getting-started.md)

## Related pages

- [Module 4: Delivering a course](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
