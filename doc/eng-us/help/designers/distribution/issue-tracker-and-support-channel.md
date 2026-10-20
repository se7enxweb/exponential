---
title: "Issue tracker and support channel"
audience: designer
category: theme-distribution
order: 380
summary: Tell users where to ask and where to report - GitHub issues for bugs, a forum or chat for questions, a private address for security - with composer.json support fields, issue templates and response habits.
keywords: issue tracker, GitHub issues, support channel, forum, composer support, issue templates, SUPPORT.md, response
type: help
---
# Issue tracker and support channel

This page is for designers who publish a theme and want problems reported in one place instead of by private mail.

## One place for each kind of message

| Kind | Where | Example |
|---|---|---|
| Bugs and feature requests | The repository's issues | `https://github.com/se7enxweb/cjw_themes_jumper/issues` |
| Questions and help | A forum or chat | The **Extensions** or **Setup & design** forum on share.exponential.earth |
| Security problems | A private address, never a public issue | [A security policy for a theme](security-policy-for-a-theme.md) |
| Paid support | Your own contact page | [Support offerings](../sales/support-offerings.md) |

The `se7enxweb` organization keeps a `SUPPORT.md` in its `.github` repository that separates commercial support from
community support; it is a useful model.

## Step by step

1. Enable issues on the repository (`gh repo edit yourvendor/mytheme --enable-issues`).
2. Add the support fields to `composer.json`:

   ```json
   "support": {
       "issues": "https://github.com/yourvendor/mytheme/issues",
       "source": "https://github.com/yourvendor/mytheme"
   }
   ```

3. Add issue templates (`.github/ISSUE_TEMPLATE/bug_report.md`) that ask for the theme version, the Exponential
   version, the PHP version, the server engine, the steps and a screenshot, and that remind people not to post
   passwords or personal data.
4. Write a short "Support" section in the README with the table above.
5. Decide how often you look at new issues, and say so honestly ("I read issues weekly").

## Habits that help

- Label issues (`bug`, `question`, `accessibility`, `security`).
- Close an issue with a link to the release that fixed it.
- Thank reporters; credit them in the changelog with their consent.

## Related pages

- [When something goes wrong](../../when-something-goes-wrong.md)
- [Handling security reports](../lifecycle/handling-security-reports.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
