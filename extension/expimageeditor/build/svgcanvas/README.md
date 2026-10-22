# svgcanvas.min.js

`design/admin4/javascript/svgcanvas.min.js` is `@svgedit/svgcanvas` 7.4.2 (MIT) with `pathseg` 1.2.1
(BSD-3-Clause), bundled by esbuild as one IIFE that sets `window.SvgCanvas`. `expimageeditor_svg.js` is the
editor around it.

Rebuild (Node 20+) in a directory outside the repository, so that `node_modules` never lands in the tree:

    cp package.json package-lock.json build.mjs <build dir>/
    cd <build dir> && npm ci
    node build.mjs --outfile=<repo>/extension/expimageeditor/design/admin4/javascript/svgcanvas.min.js

`build.mjs` documents the two changes it makes while bundling: jsPDF/svg2pdf.js are stubbed out (no PDF
export), and the `setAttribute( 'style', ... )` calls are replaced by `style.setProperty()` so that svgcanvas
runs under the admin's CSP (`style-src 'self'`). A patch that no longer matches stops the build. The same
lock file gives the same bytes. On an update, copy the licence texts to `design/admin4/javascript/licenses/`.
