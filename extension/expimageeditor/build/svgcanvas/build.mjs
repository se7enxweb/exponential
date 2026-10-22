/*
 * Builds design/admin4/javascript/svgcanvas.min.js: @svgedit/svgcanvas as one minified IIFE that sets
 * window.SvgCanvas. Built from the package's ES module sources (not its dist/, which bundles jsPDF, svg2pdf.js,
 * html2canvas, canvg, core-js and more for PDF and raster export, which the editor does not use).
 *
 *   npm ci && node build.mjs [--outfile=path] [--no-csp-patch]
 *
 * Two changes to the sources, applied here while bundling (nothing in node_modules is edited):
 *
 * 1. jspdf and svg2pdf.js are replaced by a stub: svgCanvas.exportPDF() throws instead of bundling ~1 MB.
 * 2. CSP: svgcanvas writes inline styles with setAttribute( 'style', ... ) (pointer-events of the selector layer
 *    and of new shapes, the resize cursors of the grips) and copies style attributes when it duplicates. Under
 *    style-src 'self' without 'unsafe-inline' the browser blocks and reports each of these. The patch writes the
 *    same declarations with style.setProperty(), which CSP does not restrict (Firefox does restrict
 *    style.cssText); the rotate grip's cursor:url(imgPath/rotate.svg) becomes cursor:grab, as there is no
 *    such image here. Every replacement must match exactly as often as listed, or the build fails,
 *    so an update of svgcanvas cannot drop a patch silently. --no-csp-patch builds without it (for measuring).
 *
 * Copyright CJW Network and Exponential Foundation. All rights reserved.
 * GNU General Public License v2.0 (or any later version).
 */
import { build } from 'esbuild';
import { readFileSync, statSync } from 'node:fs';
import { dirname, resolve, sep } from 'node:path';
import { fileURLToPath } from 'node:url';
import { createRequire } from 'node:module';
import { gzipSync } from 'node:zlib';

const here = dirname(fileURLToPath(import.meta.url));
const require = createRequire(import.meta.url);
const args = process.argv.slice(2);
const option = (name) => (args.find((a) => a.startsWith(`--${name}=`)) || '').split('=').slice(1).join('=');
const outfile = resolve(option('outfile') || resolve(here, '../../design/admin4/javascript/svgcanvas.min.js'));
const cspPatch = !args.includes('--no-csp-patch');

const version = (name) => JSON.parse(readFileSync(require.resolve(`${name}/package.json`), 'utf8')).version;
const svgcanvasDir = dirname(require.resolve('@svgedit/svgcanvas/package.json'));

/**
 * The statements that replace elem.setAttribute( 'style', text ): one CSSOM call per declaration. Firefox reports
 * (and blocks) style.cssText = ... under CSP like a style attribute; style.setProperty() it leaves alone.
 */
const setStyle = (elem, text) =>
    `{ const s = ${elem}.style; while (s.length) { s.removeProperty(s[0]) } ` +
    `for (const d of String(${text}).split(';')) { const i = d.indexOf(':'); if (i > 0) { s.setProperty(d.slice(0, i).trim(), d.slice(i + 1).trim()) } } }`;

// [ file below the svgcanvas package, search, replacement, expected count ]
const patches = [
    ['core/utilities.js',
        "    if (ns) {\n      elem.setAttributeNS(ns, key, value)\n    } else if (!unitCheck) {",
        `    if (key === 'style' && !ns) {\n      ${setStyle('elem', 'value')}\n    } else if (ns) {\n      elem.setAttributeNS(ns, key, value)\n    } else if (!unitCheck) {`,
        1],
    ['core/copy-elem.js',
        "    } else {\n      newEl.setAttribute(attr.name, attr.value)\n    }",
        `    } else if (attr.name === 'style') {\n      ${setStyle('newEl', 'attr.value')}\n    } else {\n      newEl.setAttribute(attr.name, attr.value)\n    }`,
        1],
    ['core/event.js', "element.setAttribute('style', 'pointer-events:inherit')", setStyle('element', "'pointer-events:inherit'"), 1],
    ['core/draw.js', "elem.setAttribute('style', 'pointer-events: inherit')", setStyle('elem', "'pointer-events: inherit'"), 1],
    ['core/draw.js', "curthis.setAttribute('style', 'pointer-events: none')", setStyle('curthis', "'pointer-events: none'"), 1],
    ['core/select.js', "gripElement.setAttribute('style', `cursor:${dirArr[i]}-resize`)", setStyle('gripElement', '`cursor:${dirArr[i]}-resize`'), 1],
    ['core/select.js', 'style: `cursor:url(${svgCanvas.curConfig.imgPath}/rotate.svg) 12 12, auto;`', "style: 'cursor:grab'", 1]
];

const pdfStub = {
    name: 'no-pdf',
    setup(b) {
        b.onResolve({ filter: /^(jspdf|svg2pdf\.js)$/ }, (a) => ({ path: a.path, namespace: 'no-pdf' }));
        b.onLoad({ filter: /.*/, namespace: 'no-pdf' }, () => ({
            contents: 'export function jsPDF () { throw new Error("PDF export is not part of this build") }',
            loader: 'js'
        }));
    }
};

const used = new Set();
const cspStylePatch = {
    name: 'csp-style-patch',
    setup(b) {
        b.onLoad({ filter: /\.js$/ }, (a) => {
            if (!a.path.startsWith(svgcanvasDir + sep)) { return undefined; }
            const rel = a.path.slice(svgcanvasDir.length + 1).split(sep).join('/');
            let source = readFileSync(a.path, 'utf8');
            patches.forEach((p, index) => {
                if (p[0] !== rel) { return; }
                const count = source.split(p[1]).length - 1;
                if (count !== p[3]) { throw new Error(`patch ${index} (${rel}): found ${count} times, expected ${p[3]}`); }
                source = source.split(p[1]).join(p[2]);
                used.add(index);
            });
            return { contents: source, loader: 'js' };
        });
    }
};

const banner = [
    '/*!',
    ` * @svgedit/svgcanvas ${version('@svgedit/svgcanvas')} | MIT | (c) SVG-edit authors | https://github.com/SVG-Edit/svgedit`,
    ` * bundled: pathseg ${version('pathseg')} | BSD-3-Clause ("Chromium's License") | (c) The Chromium Authors | https://github.com/progers/pathseg`,
    ' * Licence texts: licenses/svgcanvas-LICENSE, licenses/pathseg-LICENSE',
    ` * Built by extension/expimageeditor/build/svgcanvas (esbuild ${version('esbuild')})${cspPatch ? ', with its CSP style patch' : ', WITHOUT the CSP style patch'}; jsPDF/svg2pdf.js left out (no exportPDF)`,
    ' */'
].join('\n');

await build({
    stdin: {
        contents: "import SvgCanvas from '@svgedit/svgcanvas/svgcanvas.js'\nwindow.SvgCanvas = SvgCanvas\n",
        resolveDir: here,
        sourcefile: 'entry.js',
        loader: 'js'
    },
    bundle: true,
    format: 'iife',
    minify: true,
    legalComments: 'none',
    target: ['es2022'],
    banner: { js: banner },
    plugins: cspPatch ? [pdfStub, cspStylePatch] : [pdfStub],
    outfile,
    logLevel: 'warning'
});

if (cspPatch && used.size !== patches.length) {
    throw new Error(`patches not applied: ${patches.map((p, i) => i).filter((i) => !used.has(i)).join(', ')}`);
}
const code = readFileSync(outfile);
console.log(`${outfile}: ${statSync(outfile).size} bytes, ${gzipSync(code, { level: 9 }).length} gzip`);
