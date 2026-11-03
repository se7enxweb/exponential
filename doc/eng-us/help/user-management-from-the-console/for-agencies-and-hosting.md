---
title: "For agencies and hosting"
audience: hosting, admin, developer
category: administration
order: 18
summary: Run the exp:user commands across many installations - scripted onboarding of a client's editors, the same offboarding everywhere, regular account reviews per site, and the rules for passwords, siteaccesses and personal data when you act for clients.
keywords: agency, hosting, many installations, multi-site, client sites, scripted onboarding, offboarding everywhere, staff leaves, account review, exp:user, --siteaccess, --as, service account, personal data, data processing
type: help
---
# For agencies and hosting

An agency or host often looks after dozens of installations. The `exp:user` commands behave the same on each, so
one script serves them all.

## What you'll have at the end

Onboarding, offboarding and reviews that run the same way on every installation you look after.

## Scripted onboarding of a client's editors

Keep a CSV file per client with the editors to create, and import it with the activation mail:

```bash
cd /var/www/client-a
./console exp:user:import var/tmp/client-a-editors.csv --group=Editors --send-activation --as=agency-bot --apply
```

Each person sets a password through the mail. No password passes through your hands or your files.

## When one of your staff leaves

Your own people often have accounts on many client sites. Disable them everywhere in one loop:

```bash
for site in /var/www/client-*; do
    (cd "$site" && ./console exp:user:disable jane@example.com --as=agency-bot --apply --format=json) \
        | jq -r '"\(.command) \(.exit) \(.errors | join("; "))"'
done
```

An installation where the address is not found reports exit code 1 and changes nothing.

## A review per installation

```bash
for site in /var/www/client-*; do
    echo "$site"
    (cd "$site" && ./console exp:user:hygiene --format=json) | jq -c '.counts'
done
```

Send each client the counts, and the lists behind them only to the people allowed to see them.

## Rules when you act for clients

- Create a service account per installation, for example `agency-bot`, and pass `--as=agency-bot`. The client's
  audit then shows which changes came from you.
- Run each command in the installation root, and add `--siteaccess=` when mails must link to a particular site.
- Never write a password into a script or a file. Use `--send-activation`, `--send-link` or `--password-stdin` from
  your secret store.
- **Data protection:** Exports and import files hold the client's personal data. Keep them only as long as the task
  needs, and only where your contract with the client allows.

## Related pages

- [For developers: scripts and CI](for-developers-scripts-and-ci.md)
- [Bulk-import members from CSV](bulk-import-members-from-csv.md)
- [Offboard someone who leaves](offboard-someone-who-leaves.md)
