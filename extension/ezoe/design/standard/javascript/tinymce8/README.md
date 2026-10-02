# TinyMCE 8.9.2 for the eZ Online Editor

This directory contains the TinyMCE editor used by ezoe when
`ezoe.ini [EditorSettings] EditorEngine=tinymce8` is set (or the user preference
`ezoe_engine=tinymce8`). The ezoe specific plugins, dialogs and skin are not part
of TinyMCE, they are in `../tinymce8_ez/`.

## License

TinyMCE is Copyright (c) Ephox Corporation DBA Tiny Technologies, Inc. and is
licensed under the GNU General Public License version 2 or later, or under the
Tiny Technologies commercial terms, see `license.md`. ezoe uses it under the
GPL, which is why the editor is initialized with `license_key: 'gpl'`.

Third party libraries bundled in TinyMCE are listed with their licenses in
`notices.txt` (DOMPurify under MPL-2.0 OR Apache-2.0, PrismJS and prism-themes
under MIT).

## Source code

Only the minified build is checked in to keep the extension small. The
corresponding source code of exactly this version is available at:

- TinyMCE 8.9.2 source: https://github.com/tinymce/tinymce/tree/8.9.2/modules/tinymce
  (git tag `8.9.2` of https://github.com/tinymce/tinymce)
- npm package with the non-minified build: https://registry.npmjs.org/tinymce/-/tinymce-8.9.2.tgz
  (`npm pack tinymce@8.9.2`)

## Contents

Taken from the npm package `tinymce@8.9.2`:

- `tinymce.min.js`
- `icons/`, `models/`, `themes/`, `plugins/`, `skins/`: only the `*.min.js` and
  `*.min.css` files, plus `plugins/help/js/` complete (the help plugin loads its
  keyboard navigation pages from there)
- `license.md`, `notices.txt`

Language packs in `langs/`:

- `de.js` from the npm package `tinymce-i18n@26.9.28` (`langs8/de.js`),
  https://github.com/mklkj/tinymce-i18n. The translations are licensed under the
  same terms as the corresponding TinyMCE version, see the license of that package.

## Updating

```sh
npm pack tinymce@<version> && tar xzf tinymce-<version>.tgz
rsync -a --include='*/' --include='*.min.js' --include='*.min.css' \
      --include='license.md' --include='notices.txt' --exclude='*' \
      package/ extension/ezoe/design/standard/javascript/tinymce8/
cp -r package/plugins/help/js extension/ezoe/design/standard/javascript/tinymce8/plugins/help/
npm pack tinymce-i18n && tar xzf tinymce-i18n-*.tgz   # then copy langs8/de.js to langs/
```

Afterwards update the version in this file and in `extension/ezoe/extension.xml`.
Browsers reload the changed files automatically, the cache key of the editor files
is computed from their modification times (`eZOEXMLInput::getTinyMCE8CacheKey()`).
