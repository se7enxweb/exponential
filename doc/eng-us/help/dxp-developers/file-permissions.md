---
title: File permissions
audience: dxp-developer
category: dxp-development
order: 12
summary: Which directories of a DXP project the web server must write, how to grant it with ACLs so the command line user and the web server can both write, the fallback without ACLs, and what to check when you see "Permission denied".
keywords: permissions, setfacl, ACL, var, public/var, www-data, chown, chmod, Permission denied, web server user, CLI user
type: help
---
# File permissions

This page is for developers and administrators who set up a DXP project on a server or a workstation. Two users
write to the project: you on the command line (`php bin/console`) and the web server. Both must be able to write the
same directories.

## Contents

1. [Which directories](#which-directories)
2. [With ACLs (recommended)](#with-acls-recommended)
3. [Without ACLs](#without-acls)
4. [Checking](#checking)
5. [Common mistakes](#common-mistakes)

## Which directories

| Directory | Written by | Holds |
|---|---|---|
| `var/` | both | Symfony cache, logs, sessions, the SQLite file if you use one |
| `public/var/` | both | Stored files and generated image variations |
| `config/jwt/` | the command line, once | The JWT keypair (readable by the web server) |

Everything else (`src/`, `config/`, `templates/`, `vendor/`) is written only when you deploy.

## With ACLs (recommended)

Replace `www-data` with your web server's user (`apache`, `nginx`, `_www` on macOS):

```bash
setfacl -R  -m u:www-data:rwX -m g:www-data:rwX var/ public/var/
setfacl -dR -m u:www-data:rwX -m g:www-data:rwX var/ public/var/
```

The first line sets the rights on what exists, the second (`-d`) makes them the default for new files. Install the
`acl` package if `setfacl` is missing (`apt install acl`, `dnf install acl`).

## Without ACLs

On file systems without ACLs (some NFS and BSD mounts, macOS APFS):

```bash
chown -R www-data:www-data var/ public/var/
chmod -R 775 var/ public/var/
```

Then add your command line user to the web server's group, or run console commands as that user
(`sudo -u www-data php bin/console cache:clear`).

## Checking

```bash
getfacl var/ | head                       # the ACL entries
sudo -u www-data touch var/log/.write-test && sudo -u www-data rm var/log/.write-test
ls -l var/data_*.db                       # SQLite: owner and group
```

## Common mistakes

- **Running `cache:clear` as root.** The cache files then belong to root and the web server cannot rewrite them. Run
  console commands as your normal user or the web server user.
- **Forgetting `public/var/`.** Uploads fail and image variations return errors.
- **Making everything 777.** Never needed; use ACLs or the group.

## Related pages

- [Installing with the skeleton](installing-with-the-skeleton.md)
- [SQLite for development](sqlite-for-development.md)
- [Troubleshooting](troubleshooting-dxp.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
