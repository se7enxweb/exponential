# Changing the owner of content

Every content object has an owner, the user it belongs to. Roles can limit what someone may do to their own
content (the `Owner` limitation of `content/read`, `content/edit` and `content/remove`). When someone leaves, or
content was created under the wrong account, the owner has to change. The extension **ezownerchange**
(se7enxweb/ezownerchange) does this for one object or for a whole subtree. New installations have it switched on.

At the end of this guide you will have changed the owner of one object and of a subtree from the administration,
seen the preview first, and done the same on the command line with `--dry-run`.

## 1. In the administration

1. Open the context menu of a node and choose **Change owner**. The entry appears for users with the policy
   `owner/change` (the Administrator role has it).
2. Choose the new owner in the content browser and click **Select**. Nothing has changed yet.
3. The preview shows every object the change reaches and the outcome for each. Pick **This object only** or
   **This object and everything below it** (this needs `owner/subtree`), optionally limit it to the objects of
   the current owner, and click **Update preview**.
4. Click **Change the owner of N objects**. The page shows how many owners changed.

Each object changes only where you may also edit it (`content/edit`). Every change is recorded in the audit as
`content.ezownerchange.change`.

## 2. On the command line

For subtrees bigger than 500 objects, or for scripted handovers, preview first and then run the change. Run it as
the web server's user:

```bash
php extension/ezownerchange/bin/php/expownerchange.php --to-owner=<new owner id> --node=<node id> --from-owner=<old owner id> --dry-run --list
php extension/ezownerchange/bin/php/expownerchange.php --to-owner=<new owner id> --node=<node id> --from-owner=<old owner id>
```

The extension's [command line guide](../../extension/ezownerchange/doc/cli.md) has every option and real output.

## The extension's documentation

The extension's own guides are in the
[ezownerchange repository](https://github.com/se7enxweb/ezownerchange/tree/master/doc), and in every installation
under `extension/ezownerchange/doc/`:

- [Using it in the administration](../../extension/ezownerchange/doc/usage.md)
- [The command line script](../../extension/ezownerchange/doc/cli.md)
- [Configuration](../../extension/ezownerchange/doc/configuration.md)
- [Permissions and role setups](../../extension/ezownerchange/doc/permissions.md)
- [What the audit records](../../extension/ezownerchange/doc/audit.md)
- [Upgrading from releases up to 1.3](../../extension/ezownerchange/doc/upgrading.md)
- [Troubleshooting](../../extension/ezownerchange/doc/troubleshooting.md)

## Related pages

- [Roles, policies and unactivated users](roles-and-policies.md)
- [Security and audit](security-and-audit.md)
