---
title: "Teach web publishing with static sites"
audience: trainer
category: training
order: 9
summary: A lesson plan and exercise for a half-day class - learners edit content in Exponential, build a static copy, review what changes and publish it free to GitHub Pages, then roll it back.
keywords: trainer, teacher, lesson plan, exercise, course, classroom, static site, GitHub Pages, gh-pages, free hosting, free website, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing, git, rollback
type: help
---
# Teach web publishing with static sites

Static publishing makes the whole path from content to a public website visible in one session: edit, build, check,
publish, go back. Each learner ends with a live site, and nobody has to pay for hosting.

## What you'll have at the end

- A lesson plan for about four hours.
- An exercise with a clear result: each learner's site on GitHub Pages, with two publishes in its history.

## Before the class

- One Exponential installation per learner or per pair (a lab server with several sites works too), with static
  publishing switched on.
- Each learner has a GitHub account and a fine-grained token, or you hand out deploy keys made with
  `exp:static:keys`. See [Connect to GitHub](../static-publishing/connect-to-github-token-key-or-app.md).
- Give each learner a role with the `staticpublish/*` policies limited to their own target. See
  [Who may publish](../static-publishing/who-may-publish-roles-and-policies.md).

## Lesson plan

| Time | Topic | Activity |
|---|---|---|
| 20 min | What a static site is | Discuss [What static publishing is](../static-publishing/what-static-publishing-is.md); open the demo site (coming soon) |
| 30 min | Fit check | Learners sort example sites with [Is static right for my site?](../static-publishing/is-static-right-for-my-site.md) |
| 40 min | Set up | The setup wizard: siteaccess, repository, credential, links |
| 40 min | Build and review | Build, read the diff and the report, open the preview |
| 30 min | Publish | Publish, open the `github.io` address, find the commit and tag on GitHub |
| 30 min | Change and roll back | Edit a page, publish again, then roll back to publish 1 |
| 20 min | Wrap-up | What changes for forms and search; questions |

## The exercise

1. Change the front page title of your site and add one article.
2. Build your target and write down how many pages are new and changed.
3. Publish. Paste your public address into the course chat.
4. Change the article again and publish. Then roll back to publish 1 and check that the article's first version is
   back online.
5. Bonus: explain why `/<repository>/` is in your address. See [Links and base path](../static-publishing/links-and-base-path.md).

## Assessing it

- The site is reachable and the links work below `/<repository>/`.
- The repository has at least three tags `static/<target>/<n>`.
- The learner can say which parts of a site need a server.

When something goes wrong in class: [When a publish fails](../static-publishing/when-a-publish-fails.md).

## Make yours now

Try the exercise yourself first: [Make a free website in 15 minutes](../static-publishing/make-a-free-website-in-15-minutes.md).
