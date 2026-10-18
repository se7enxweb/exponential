---
title: Asking the community for help
audience: user, developer, designer, app-creator
category: extension-development
order: 200
summary: Where to ask a development question (forums, chat, issue trackers, the extension's own repository), how to write a question people can answer, what never to post, and how to report a security problem privately.
keywords: help, ask, question, forum, telegram, issue, github issues, minimal example, bug report, security, vulnerability, private disclosure, community
type: help
---
# Asking the community for help

Part 1, chapter 20. Sooner or later you will be stuck. The people who build Exponential and its extensions answer
questions in public places, and a well asked question usually gets an answer. This chapter tells you where to ask, how
to ask, and what never to post.

## Before you ask

Most questions are answered by one of these, in this order:

1. The debug report and `var/log/error.log` ([Debug output and logs for developers](debug-output-and-logs-for-developers.md)).
2. The help center's search: type the class, setting or error message.
3. The extension's own `README.md` and `doc/`.
4. `git log -S "<the text of the error>"` in the kernel or the extension: maybe it was changed recently, with a
   message that explains why.
5. The forums and eZpedia, searched for the error message.

If none of these answers it, ask. Say in your question what you already tried; it saves everyone a round trip.

## Where to ask

| Your question is about | Ask in |
|---|---|
| How to do something with the kernel's APIs | [Forums: Developer](https://share.exponential.earth/forums/developer) |
| A specific extension | The extension's GitHub issues (if it is maintained), or [Forums: Extensions](https://share.exponential.earth/forums/extensions) |
| Templates, overrides, designs | [Forums: Setup & design](https://share.exponential.earth/forums/setup-design) |
| A quick question, a discussion | The Telegram channel [t.me/exponentialcms](https://t.me/exponentialcms) and the other channels listed on [Share Exponential](share-exponential-earth.md) |
| A bug in the kernel, the administration or the bundled extensions | [github.com/se7enxweb/exponential/issues](https://github.com/se7enxweb/exponential/issues) |
| A bug in the Velocity application server | [github.com/se7enxweb/exponential-velocity/issues](https://github.com/se7enxweb/exponential-velocity/issues) |
| The project, the website, the ecosystem | [github.com/se7enxweb/exponential-community/issues](https://github.com/se7enxweb/exponential-community/issues) |
| A security problem | **Privately**, see below |

The administration has a guide for bug reports: **Help > Report an error** (`exphelp/report`) fills in a summary of
the installation and opens a prepared GitHub issue. See [When something goes wrong](../../when-something-goes-wrong.md).

## How to write a question people can answer

A good question has five parts:

```text
What I want to do:   <one sentence>
What I did:          <the code or template, as small as possible>
What I expected:     <the result you wanted>
What happened:       <the exact error message, the debug report lines>
My environment:      Exponential <version>, PHP <version>, database <type>, design <name>
```

- **Make it small.** Cut your code down to the smallest piece that still shows the problem. Often you find the answer
  while doing it.
- **Paste text, not screenshots** of code and errors, in code blocks, so people can copy and search them.
- **Name versions.** `./console --version` prints Exponential's version; `php -v` the PHP version. **Setup > System
  information** has **Copy report as text**, which leaves out passwords.
- **Say what you tried** and what each attempt did.
- **Come back.** Answer follow-up questions, and when it is solved, say how. The next person with the same problem
  will find your thread.

## What never to post

- Passwords, API keys, tokens, the contents of `settings/override/` or `config.php`.
- Database dumps, user lists, e-mail addresses or any personal data of your site's users or customers.
- Internal host names and IP addresses of your servers.
- Code you are not allowed to share (a client's proprietary code). Reduce it to a neutral example instead.

Check the error lines you paste: some contain paths or user names. Replace them with placeholders.

## Security problems: privately

A security problem (a way to read data without permission, to run code, to take over an account) is **never** reported
in a public tracker or forum. The kernel's `SECURITY.md` says how: by e-mail to `security@se7enx.com`, or through
GitHub's private vulnerability reporting on the repository. You will be told whether the report is accepted and kept
informed while a fix is prepared. For a third-party extension, look for its own `SECURITY.md`; if there is none,
contact its maintainers privately before saying anything in public.

## Give back

Once you are not stuck any more, someone else is. Answer one question a week in the forum you learned most from.
Growing the community through code has more
ways to help.

## You finished Part 1

You know what an extension is, you have a development installation, you can read the code and the logs, and you know
where to find ideas and how to reuse them fairly. Continue with
[Part 2: The builder](../part-2-builder/README.md).

## Related pages

- Previous: [Debug output and logs for developers](debug-output-and-logs-for-developers.md)
- Next: [Part 2: The builder: settings, templates and overrides](../part-2-builder/README.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- Writing good issues
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
