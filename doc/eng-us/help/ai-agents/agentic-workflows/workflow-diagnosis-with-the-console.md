---
title: A diagnosis workflow with the console
audience: ai-agents, developer, admin
category: ai-agents
order: 71
summary: A worked example of a coding agent that diagnoses a problem on a development copy without changing anything - "a setting has no effect, and anonymous visitors cannot see an article" - using read-only console commands (exp:ini where, exp:ini get, exp:access:check, exp:logs, exp:audit, exp:help) and ending with a report for a person to act on.
keywords: diagnosis, troubleshooting with AI, read only agent, console, exp:ini where, exp:ini get, exp:access:check, exp:logs, exp:audit, exp:help, worked example, agentic workflow
type: help
---
# A diagnosis workflow with the console

This page is for developers and administrators who want a coding agent to find the cause of a problem without
touching the installation. It walks through a realistic case with read-only console commands that ship with the
kernel. The agent ends with a report; a person decides what to change.

## Contents

1. [The setup](#the-setup)
2. [The case](#the-case)
3. [Step by step](#step-by-step)
4. [The report](#the-report)
5. [Why this workflow is safe](#why-this-workflow-is-safe)

## The setup

- A development or staging copy of the installation, with the problem reproduced.
- A coding agent in a terminal on that copy, told to use only the commands below and the documentation
  ([Coding agents on an Exponential project](coding-agents-on-an-exponential-project.md)).
- No production data in the copy, or at least none the agent's model provider may not see.

## The case

A site manager reports two things: a setting in an extension's override "has no effect", and an article gives
anonymous visitors an "access denied" page.

## Step by step

| Step | The agent asks | Command | Why |
|---|---|---|---|
| 1 | What does the documentation say about the setting? | `./console exp:help search "<setting name>"` | Know the intended behavior first ([Agent-friendly documentation](agent-friendly-documentation.md)) |
| 2 | Which files set it, in load order, and which value wins? | `./console exp:ini where <file>/<Block>/<Variable>` | Shows every file that sets the variable and the value in effect |
| 3 | What is the value now? | `./console exp:ini get <file>/<Block>/<Variable>` | Confirms the effective value |
| 4 | May the anonymous user read the article, and which limitation refuses it? | `./console exp:access:check --user=anonymous --node=<id>` | Ends with ALLOWED or DENIED and names the policy and limitation |
| 5 | Did the logs record anything around the time of the report? | `./console exp:logs --tail=var/log/error.log --lines=50` | The last entries, with personal data masked by default |
| 6 | Did anyone change the role or the settings recently? | `./console exp:audit search --name=<event name>` and `./console exp:audit tail --channel=access` | Role changes and setting writes are in the audit trail |

Habits that make the steps reliable:

- One question per step; let the agent read each answer before the next command.
- Use `--json` where a command offers it (`exp:access:check`, `exp:help`), so the agent parses rather than guesses.
- Add `--allow-root-user` only where the copy runs commands as root, as the guides show.

## The report

```text
1. Setting: <file>/<Block>/<Variable> is set in three files. The extension's override sets it,
   but settings/override/<file>.append.php sets it again later in load order, so that value wins.
2. Access: the article is in section <n>. The Anonymous role's content/read is limited to
   section <m>. exp:access:check names the Section limitation as the refusal.
3. Suggested changes (not made): remove or change the later override; either move the article to
   section <m> or extend the Anonymous policy, after deciding which is intended.
4. Pages used: <help page ids>.
```

A person makes the change in the administration or the settings and clears the caches, then asks the agent to run
steps 2 to 4 again to confirm.

## Why this workflow is safe

- Every command used is read-only: `exp:ini where` and `get`, `exp:access:check` ("nobody signs in and nothing is
  changed"), `exp:logs` reading, `exp:audit` reading, `exp:help`.
- The agent works on a copy, not production.
- The report names its evidence; a person can run the same commands to check every claim.

Commands that change things (`exp:ini set`, cache clears, scripts that write) stay with the person.

## Related pages

- [When something goes wrong](../../when-something-goes-wrong.md)
- [Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)
- [The log viewer](../../../features/6.0/log-viewer.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
