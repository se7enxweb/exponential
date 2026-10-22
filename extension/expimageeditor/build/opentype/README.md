# opentype.min.js

`design/admin4/javascript/opentype.min.js` is opentype.js 2.0.0 (MIT) with the tiny-inflate 1.0.3 (MIT) it
carries for WOFF, bundled by esbuild as one IIFE that sets `window.opentype = { parse }`. The SVG editor
(`expimageeditor_svg.js`) loads it only when it saves with "Save text in added fonts as paths" and a text uses
one of the fonts of `expimageeditor.ini [EditorSettings] Fonts[]`: it reads the font file and writes the glyph
outlines of the text as `<path>`.

opentype.js reads TrueType and CFF fonts as `.ttf`, `.otf` and `.woff`, not `.woff2` (it has no Brotli
decompressor). A font given only as `.woff2` is therefore shown and offered, but its text stays text on save;
`Fonts[]` takes a fourth part for the file to read the outlines from (see the comment in
`settings/expimageeditor.ini.append.php`).

Rebuild (Node 20+) in a directory outside the repository, so that `node_modules` never lands in the tree:

    cp package.json package-lock.json build.mjs <build dir>/
    cd <build dir> && npm ci
    node build.mjs --outfile=<repo>/extension/expimageeditor/design/admin4/javascript/opentype.min.js

Nothing is patched. The same lock file gives the same bytes (244647 bytes, sha1
968823894fea1d7380a384bf472bda0a6dbf1a60 for 2.0.0). Most of opentype.js stays in the bundle although only
`parse()` is called: the font object it returns reaches the rest. On an update, copy the licence texts to
`design/admin4/javascript/licenses/` (`opentype-LICENSE`, `tiny-inflate-LICENSE`).
