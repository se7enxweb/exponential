## What this pull request changes

<!-- What it changes and why. Link the issue it fixes, if there is one. -->

## Checklist

- [ ] **I have signed the 7x Contributor Licensing Agreement (CLA)**, or my employer has, and it has been sent to 7x.
      **I understand that by signing and submitting, I assign the copyright in this contribution to 7x, which becomes its owner, and give up control over it:** 7x may
      use, change, relicense and distribute it in any way, also commercially and under other licenses, without asking
      or paying me, and I cannot withdraw it or set conditions on it.
      A contribution to the core kernel (this repository, se7enxweb/exponential) is merged only once the signed CLA
      has been received. You sign once for all future contributions. How to sign:
      [Copyright assignment (CLA)](../CONTRIBUTING.md#copyright-assignment-cla) and the
      [CLA page](https://share.se7enx.com/community-project/contributor-licensing-agreement-cla).
      Your own custom extensions, site packages and themes are not affected.
- [ ] I am prepared for many review rounds: 7x reviews a pull request in many more rounds than usual, sometimes
      hundreds of revisions, until it is simple to accept ([Code reviews](../CONTRIBUTING.md#code-reviews)).
- [ ] Squashed and rebased onto main (`git fetch origin`, `git rebase -i origin/main`, `git push --force-with-lease`
      to this branch only; see [Code reviews](../CONTRIBUTING.md#code-reviews)).
- [ ] Commit subjects start with `Added: `, `Updated: `, `Fixed: `, `Removed: ` or `Renamed: `.
- [ ] The file manifest is current (`make manifest-fix`; `make hooks` checks it on every commit).
- [ ] The tests pass, and the documentation is updated where the behavior changed.
- [ ] Administration templates carry no `<style>` block or `style=""` attribute
      ([Styling the administration](../doc/eng-us/bc/6.0/admin4-styling.md)).

See [CONTRIBUTING.md](../CONTRIBUTING.md) for the whole procedure.
