---
title: "Trainer FAQ"
audience: trainer
category: training
order: 8
summary: Short answers to the questions trainers ask most, about certification, licensing, versions, labs, accounts, mail, languages, accessibility and keeping the material up to date, each with the page that explains it.
keywords: faq, trainer questions, certification, license, version, lab, sqlite, accounts, mail, language, accessibility, updates, edit on github
type: help
---
# Trainer FAQ

Short answers, each with a link to the full explanation.

## About the material

**Is there an official Exponential certification?**
The Exponential Partner Program names certifications for partner teams (Developer, Design, Solution, Sales), but
their syllabus, exam format and examiners are not published ([Certifications](../partners/certifications.md)). The
courses here are not those certifications. Assessments and certificates you issue are your own; say so on them
([Certificates of attendance in your own name](train-the-trainer/module-06-assessment-and-follow-up/certificates-of-attendance.md)).

**May I use these pages in a paid course?**
Yes. The documentation is free documentation under the GNU FDL 1.3 or later; copies may be given for free or for a
fee, on the license's conditions ([Free documentation](../free-documentation.md)).

**May I change the lessons?**
Yes, and share your version under the same license, marked as changed, with a title of its own
([Lesson 1.4](train-the-trainer/module-01-preparing-to-teach/your-material-and-the-gfdl.md)).

**A lesson is wrong for the current version. What now?**
Fix it with **Edit on GitHub**, or report it on the Help center's **Report an error** page
([Lesson 3.5](train-the-trainer/module-03-designing-a-course/adapting-and-improving-these-courses.md)).

## About the lab

**Which database for a training lab?**
SQLite: no server, no password, one file per installation; it is the default of `exp:install`
([Lesson 2.3](train-the-trainer/module-02-building-the-lab/one-install-per-learner.md)).

**Do learners need a web server?**
No. `php bin/php/console exp:velocity start` serves the site with PHP's built-in server by default
([Lesson 2.4](train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md)).

**Which release should the class use?**
One fixed release for everyone, the one you rehearsed on
([Lesson 2.2](train-the-trainer/module-02-building-the-lab/getting-the-code-for-a-class.md)).

**How do I reset a lab between groups?**
Restore a golden copy of `var/storage`, or reinstall with `--force` after a dry run
([Lesson 2.6](train-the-trainer/module-02-building-the-lab/resetting-the-lab.md)).

**Locked laptops, no installs allowed?**
Use a shared training site and limit each learner's role to their own folder
([Lesson 2.7](train-the-trainer/module-02-building-the-lab/shared-server-or-one-per-learner.md)).

## About accounts and data

**Can the class share one administrator account?**
No. One account per person
([Lesson 2.5](train-the-trainer/module-02-building-the-lab/learner-accounts-and-roles.md)).

**Which e-mail addresses for test users?**
Addresses at `example.invalid`, which can never receive mail.

**May I use our real content to make it concrete?**
No real customer or personal data, not even temporarily. Use invented content
([Lesson 4.2](train-the-trainer/module-04-delivering/demonstrating-safely.md)).

**A learner found a security problem.**
It is reported privately, as `SECURITY.md` describes, never in a public issue
([When something goes wrong](../when-something-goes-wrong.md)).

## About teaching

**My learners speak another language.**
Switch the administration's **Interface language** and check its completeness figure
([Lesson 5.3](train-the-trainer/module-05-accessibility-and-inclusion/languages-and-right-to-left-in-class.md)).

**How do I make the course accessible?**
Accessible handouts, the keyboard and display features of the Admin 4 design, and accommodations planned in advance
([Module 5](train-the-trainer/module-05-accessibility-and-inclusion/README.md)).

**How long is each course?**
See the [Course catalogue](course-catalogue.md); each module page has a timing plan.

**Where do learners ask after the course?**
The community channels: forums, chat and issue trackers
([The community channels](../advocates/community-channels.md)).

## Related pages

- [Trainers: start here](trainers-start-here.md)
- [Trainer toolkit](trainer-toolkit.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
