/*
 * Builds design/admin4/javascript/opentype.min.js: opentype.js as one minified IIFE that sets window.opentype with
 * the one function the SVG editor needs, parse() (an ArrayBuffer of a .ttf, .otf or .woff file to a font whose
 * glyphs give their outlines). Built from the package's ES module (dist/opentype.mjs, which carries tiny-inflate
 * for WOFF); everything parse() does not reach is left out by esbuild. Nothing in node_modules is changed.
 *
 *   npm ci && node build.mjs [--outfile=path]
 *
 * Copyright CJW Network and Exponential Foundation. All rights reserved.
 * GNU General Public License v2.0 (or any later version).
 */
import { build } from 'esbuild';
import { readFileSync, statSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { createRequire } from 'node:module';
import { gzipSync } from 'node:zlib';

const here = dirname(fileURLToPath(import.meta.url));
const require = createRequire(import.meta.url);
const args = process.argv.slice(2);
const option = (name) => (args.find((a) => a.startsWith(`--${name}=`)) || '').split('=').slice(1).join('=');
const outfile = resolve(option('outfile') || resolve(here, '../../design/admin4/javascript/opentype.min.js'));

const version = (name) => JSON.parse(readFileSync(require.resolve(`${name}/package.json`), 'utf8')).version;
const source = readFileSync(require.resolve('opentype.js/dist/opentype.mjs'), 'utf8');
// the version of tiny-inflate opentype.js carries in its bundle, for the banner
const inflate = (/\/\/ src\/tiny-inflate@([\d.]+)/.exec(source) || [])[1];
if (!inflate) { throw new Error('tiny-inflate not found in opentype.js/dist/opentype.mjs'); }

const banner = [
    '/*!',
    ` * opentype.js ${version('opentype.js')} | MIT | (c) Frederik De Bleser | https://github.com/opentypejs/opentype.js`,
    ` * bundled: tiny-inflate ${inflate} | MIT | (c) Devon Govett | https://github.com/foliojs/tiny-inflate`,
    ' * Licence texts: licenses/opentype-LICENSE, licenses/tiny-inflate-LICENSE',
    ` * Built by extension/expimageeditor/build/opentype (esbuild ${version('esbuild')}): window.opentype = { parse }`,
    ' */'
].join('\n');

await build({
    stdin: {
        contents: "import { parse } from 'opentype.js/dist/opentype.mjs'\nwindow.opentype = { parse }\n",
        resolveDir: here,
        sourcefile: 'entry.js',
        loader: 'js'
    },
    bundle: true,
    format: 'iife',
    minify: true,
    legalComments: 'none',
    target: ['es2022'],
    define: { DEBUG: 'false' },
    banner: { js: banner },
    outfile,
    logLevel: 'warning'
});

const code = readFileSync(outfile);
console.log(`${outfile}: ${statSync(outfile).size} bytes, ${gzipSync(code, { level: 9 }).length} gzip`);
