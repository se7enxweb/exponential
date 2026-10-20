---
title: "Lesson 5.5 (optional): The system card"
audience: trainer, hosting, admin
category: training
order: 55
summary: An optional 25-minute lesson - learners read a server with ./console exp:expfetch, act on its health hints, watch load and Velocity workers live with --watch, and compare two installations with exp:info --json. Works on any training installation.
keywords: hosting and operations, system card, exp:expfetch, exp:info, exp:about, health hints, --watch, --json, jq, inventory, NO_COLOR, training, exercise
type: help
---
# Lesson 5.5 (optional): The system card

A host who opens a terminal on a server wants one screen that says what runs there and what is wrong. This lesson
teaches the system card, `./console exp:expfetch`, and its two larger forms. It fits after lesson 5.1 or as a warm-up
at the start of a day. Reference: [The system card](../../../../features/6.0/system-card.md).

## At a glance

| | |
|---|---|
| Module | [Module 5: Monitoring and logs](README.md) |
| Duration | 25 minutes |
| Level | Beginner to intermediate |
| Format | Hands-on, alone; the last step in pairs |

## Learning objectives

After this lesson learners can:

1. Read the card and say which section a fact comes from.
2. Explain each hint on their machine and what would fix it.
3. Use `--watch` to follow load, memory and Velocity workers while something runs.
4. Produce `exp:info --json` and pick values out of it with `jq`.
5. Explain why the output is safe to paste into a ticket.

## Trainer's notes

- Show the card once in color on a wide terminal (the "e" with the wordmark), then narrow the window to 60 and 40
  columns: the logo shrinks and finally stacks. Then run it with `NO_COLOR=1` and through `| cat` to show that colors
  appear only on a terminal.
- On a training installation the Hints block is rarely empty (the cron job is often missing, the help index stale):
  that is good material.
- Common question: "Why does it say OPcache is on when the site is slow?" The card shows the command-line PHP; the
  web server's PHP is on Setup > System information.
- Pitfall: as root the command needs `--allow-root-user` like every console command.

## Hands-on exercise

1. Run `./console exp:expfetch`. Write down the Exponential version, the PHP version and whether Velocity runs.
2. Read the Hints block. For each hint, write one sentence: what it means and what you would do.
3. Fix one hint that is safe to fix on a training machine (for example `./console exp:help index` for a stale help
   index), and run the card again: the hint is gone.
4. In a second terminal start `./console exp:expfetch --watch=1 --section=system,engine`. In the first, run
   `./console exp:benchmark` (or reload the site in a browser a few times). Watch the load and the workers move.
   Stop with Ctrl-C.
5. Run `./console exp:info --json --no-logo > me.json`, then `jq '.sections.php' me.json` and
   `jq '[.hints[] | .severity] | group_by(.) | map({(.[0]): length}) | add' me.json`.
6. In pairs: exchange JSON files and list three differences between the two installations
   (`diff <(jq -S . me.json) <(jq -S . them.json)`).
7. Search both files for the database user or password. You will not find them: explain why.

## Expected result

- Every learner has a list of hints with a fix for each, and one hint fixed.
- Learners saw `--watch` refresh in place and know how to stop it.
- Each pair has three differences and can say which of them matters.

## Quiz

1. Which command shows the versions of all active extensions? (`exp:info`)
2. What happens when the database does not answer? (The database fact is an error with a hint; with isolation a
   provider that hangs shows `n/a (timeout)` and the card still appears.)
3. How do you get no colors in a terminal? (`NO_COLOR=1`, `--no-color` or `--plain`.)
4. Which option prints one document for a monitoring system? (`--json`)

## Further reading

- [The system card](../../../../features/6.0/system-card.md): every option, the hints and the settings.
- [The system card for hosts](../../../hosting/operations/system-card-for-hosts.md)

## Related pages

- [Module 5: Monitoring and logs](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
