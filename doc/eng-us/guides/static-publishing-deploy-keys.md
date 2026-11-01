# Static publishing: SSH deploy keys

This guide is for administrators who publish a static copy of an Exponential 6.0.15+ site to a git host over SSH. A
deploy key is an SSH key that opens one repository and nothing else: if it is ever lost, only that repository is at
risk, and it never expires the way a token does. Exponential makes the key for the target, keeps its private half in
the secret store and shows you the public half to add to the repository.

[Guides](README.md) · Related: [Repositories and credentials](static-publishing-targets-and-credentials.md),
[History and rollback](static-publishing-history-and-rollback.md), [Secret store](../features/6.0/secret-store.md)

## What you'll have at the end

- An ed25519 key pair made for one target, its private half in Setup > Secrets (never in a file of the site).
- The public half added to the repository as a deploy key with write access.
- `exp:static:publish` pushing over SSH with that key.

## Before you start

- OpenSSH on the server: `ssh -V` and `ssh-keygen` (on Windows, the OpenSSH client that comes with Windows or with
  Git for Windows).
- The secret store set up (Setup > Secrets opens; the table `exp_secret` exists).
- The tables of static publishing are not needed for the key.

## 1. Make the key

```bash
./console exp:static:keys github-public --generate
```

```text
Static publishing: the deploy key of github-public (secret staticpublish.github-public.deploy_key)
A new key was made. Its public half (add it to the repository as a deploy key with write access; GitHub: Settings > Deploy keys > Add deploy key, Allow write access):

ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIM3k... exponential-static-publish-github-public@web1

Fingerprint: SHA256:Qm1o0B2v6M1N0bUuJmYcZ7m2b6r0n2z1p7l0kq3xYbE
Setting:     Credential=sshkey:staticpublish.github-public.deploy_key
WARN the target's Credential is token:github_pages_token: set Credential=sshkey:staticpublish.github-public.deploy_key to sign in with this key
PASS
```

The key is made with `ssh-keygen` in a folder only the process can read and removed from the disk at once; its private
half is stored encrypted as the secret `staticpublish.github-public.deploy_key` (scope `staticpublish`), its public
half as the secret's description. The audit records `system.staticpublish.credential` with the fingerprint.

The target can have one key. `--generate` refuses when it has one; `--generate --replace` makes a new one, and the old
one stops working once you remove it from the repository.

## 2. Add it to the repository

- **GitHub:** the repository's **Settings > Deploy keys > Add deploy key**: a title such as "Exponential static
  publishing", the public key, and **Allow write access**.
- **GitLab:** **Settings > Repository > Deploy keys**, with **Grant write permissions to this key**.
- **Gitea, Forgejo:** **Settings > Deploy keys**, without "Read-only".
- **Your own server:** a line in the `authorized_keys` of the git user, limited to the repository by your git
  server's rules.

## 3. Use it

Set the target's `Credential` and an SSH `Remote`:

```ini
[Target_github-public]
Remote=git@github.com:owner/my-site.git
Credential=sshkey:staticpublish.github-public.deploy_key
```

Then publish as always:

```bash
./console exp:static:publish github-public --build
```

For each push the private key is written to a file only the process can read
(`var/<site>/static-publish/.keys/`), named in `GIT_SSH_COMMAND` with `IdentitiesOnly` and `BatchMode`, and
overwritten and removed when git is done. The host's key is learned on the first push and kept in
`var/<site>/static-publish/.known_hosts`; a host whose key changes later is refused.

## Show the key again

```bash
./console exp:static:keys github-public
```

prints the public half and the fingerprint of the key the target has, never the private half. Compare the fingerprint
with the one the git host lists for the deploy key.

## Troubleshooting

| Symptom | Cause and fix |
|---|---|
| `ssh-keygen could not make a key` | Install OpenSSH on the server |
| `the secret store is not set up` | `./console exp:upgrade --apply`, then Setup > Secrets |
| Push `pending`, `Permission denied (publickey)` | The public key is not on the repository, or without write access |
| Push `pending`, `Host key verification failed` | The host's key changed since the first push; check why, then remove its line from `.known_hosts` |
| `a deploy key signs in over SSH` | Set `Remote` to the SSH address, `git@github.com:owner/repo.git` |

## Next step

[Repositories and credentials](static-publishing-targets-and-credentials.md): the other ways to sign in, and what a
publish commits.
