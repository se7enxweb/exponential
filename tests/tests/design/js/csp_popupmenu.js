// Runs design/admin4/javascript/popupmenu/ezpopupmenu.js against mini_dom.js with a menu declared in data-menu-*
// attributes (no inline script, no handler attribute): opened by a click on its opener, its items filled in, a
// submenu opened by pointing at its item, a form submitted by a click; and the ways extensions still use: an item
// added to menuArray by a script, an item with its own onmouseover attribute. Prints one JSON line per case.
// Usage: node csp_popupmenu.js <path to ezpopupmenu.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const source = fs.readFileSync(process.argv[2], 'utf8');

const html = ''
    + '<a id="opener" href="/class/view/3" data-menu-open="M" data-menu-values="{&quot;%nodeID%&quot;:5,&quot;%objectID%&quot;:7}"'
    + ' data-menu-languages="[{&quot;locale&quot;:&quot;eng-GB&quot;,&quot;name&quot;:&quot;English &lt;b&gt;\\&quot;UK\\&quot;&quot;}]"'
    + ' data-menu-classes="\'\'" data-menu-title="Tom &amp; Jerry &lt;i&gt;" data-menu-disable="[&quot;off&quot;]" data-menu-disable-item="create"><img alt="" /></a>'
    + '<div class="popupmenu" id="M" data-menu-depth="0" data-menu-header="M-head">'
    + '<div class="popupmenuheader"><h3 id="M-head">XXX</h3></div>'
    + '<a id="edit" href="#" data-menu-url="/content/edit/%objectID%">Edit</a>'
    + '<a id="move" href="#" data-menu-form="move-form">Move</a>'
    + '<a id="sub" class="more" href="#" data-menu-submenu="S">Edit in</a>'
    + '<a id="off" href="#" data-menu-url="/content/history/%objectID%" data-menu-disabled-class="menu-item-disabled" data-menu-disabled-for="off">History</a>'
    + '<a id="create" class="more" href="#" data-menu-submenu="C" data-menu-disabled-class="menu-item-disabled">Create here</a>'
    + '<a id="legacy" href="#" onmouseover="window.legacyHover = 1">Legacy</a>'
    + '<a id="ext" href="#">Extension</a>'
    + '<a id="redirect" data-menu-redirect="/visual/templatecreate/(nodeID)/%nodeID%">Override</a>'
    + '</div>'
    + '<div class="popupmenu" id="S" data-menu-depth="1">'
    + '<template id="S-item"><a href="/content/edit/%objectID%/f/%locale%">%name%</a></template>'
    + '<div id="langs" data-menu-variable="%languages%" data-menu-template="S-item"></div>'
    + '</div>'
    + '<div class="popupmenu" id="C" data-menu-depth="1">'
    + '<template id="C-item"><a href="#" data-menu-form="create-form" data-menu-form-values=\'["classID","%classID%"]\'>%name%</a></template>'
    + '<div id="classes" data-menu-variable="%classList%" data-menu-template="C-item"></div>'
    + '</div>'
    + '<form id="move-form" method="post" action="/content/action/%nodeID%"><input type="hidden" name="ContentNodeID" value="%nodeID%" /></form>'
    + '<form id="create-form" method="post" action="/content/action"><input type="hidden" name="ClassID" value="%classID%" /><input type="hidden" name="NodeID" value="%nodeID%" /></form>';

const document = createDocument(html);
// the script's global object is the window, as in a browser
const sandbox = { innerWidth: 1200, innerHeight: 800, pageXOffset: 0, pageYOffset: 0, scrollX: 0, scrollY: 0, document,
                  getComputedStyle: () => ({ position: 'static' }), location: { href: '' }, navigator: { userAgent: 'node' }, console };
sandbox.window = sandbox;
const window = sandbox;
vm.createContext(sandbox);
vm.runInContext(source, sandbox);

// what a submitted form carried at the moment it was sent
const sent = [];
document.body.querySelectorAll('form').forEach((form) => {
    form.submit = () => sent.push({ id: form.id, action: form.getAttribute('action'),
        values: form.querySelectorAll('input').map((i) => i.name + '=' + i.value) });
});
function out(name, value) { console.log(JSON.stringify(Object.assign({ name }, value))); }
const $ = (id) => document.getElementById(id);
const shown = (id) => $(id).style.display === 'block';

// an extension adds an item by script before the menu is used: the declared menu is created for it
vm.runInContext("menuArray['M']['elements']['ext'] = { 'url': '/extension/%nodeID%' };", sandbox);
out('extension entry', { depth: vm.runInContext("menuArray['M']['depth']", sandbox), header: vm.runInContext("menuArray['M']['headerID']", sandbox) });

const open = document.dispatch('click', $('opener'));
out('open', { prevented: open.defaultPrevented, shown: shown('M'), header: $('M-head').textContent,
              edit: $('edit').getAttribute('href'), ext: $('ext').getAttribute('href'),
              off: [$('off').className, $('off').getAttribute('href'), $('off').getAttribute('aria-disabled')],
              create: [$('create').className, $('create').getAttribute('aria-disabled')],
              role: $('M').getAttribute('role'), items: $('M').querySelectorAll('a').map((a) => a.getAttribute('role')) });

document.dispatch('mouseover', $('sub'));
out('submenu', { shown: shown('S'), items: $('langs').innerHTML, opener: $('sub').getAttribute('aria-expanded') });

document.dispatch('mouseover', $('edit'));
out('pointing at another item', { submenuShown: shown('S'), menuShown: shown('M') });

document.dispatch('mouseover', $('create'));
out('disabled item', { submenuShown: shown('C') });

document.dispatch('mouseover', $('sub'));
document.dispatch('mouseover', $('legacy'));
out('legacy item', { submenuShown: shown('S'), note: 'the delegated listener leaves an item with its own onmouseover alone' });

const move = document.dispatch('click', $('move'));
out('form', { prevented: move.defaultPrevented, sent: sent.slice(), actionAfter: $('move-form').getAttribute('action'),
              valueAfter: $('move-form').querySelector('input').value });

const offClick = document.dispatch('click', $('off'));
out('disabled click', { prevented: offClick.defaultPrevented });

// the create here entries: the class id of the entry, JSON-escaped into the form values
vm.runInContext("ezpopmenu_showTopLevel( { type: 'click', detail: 1, clientX: 5, clientY: 5, pageX: 5, pageY: 5 }, 'M', "
    + "{ '%nodeID%': 9, '%objectID%': 4, '%languages%': [], '%classList%': [ { classID: '12\"x', name: 'Article' } ] }, 'N', -1, -1 );", sandbox);
document.dispatch('mouseover', $('create'));
const entry = $('classes').querySelector('a');
sent.length = 0;
document.dispatch('click', entry);
out('create here', { shown: shown('C'), values: entry.getAttribute('data-menu-form-values'), sent: sent.slice() });

document.dispatch('click', $('redirect'));
out('redirect', { location: window.location.href, hidden: !shown('M') });
