// Runs design/standard/javascript/page/content_search_filters.js (the filter bar of the search page) against
// mini_dom.js and prints one JSON line per case: Apply is disabled until a filter changed, the from/to dates show
// for a custom range only and a typed date chooses it, the sort order submits on change, empty fields are left out
// of the address, the bar folds under its button.
// Usage: node content_search_filters.js <path to content_search_filters.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const source = fs.readFileSync(process.argv[2], 'utf8');
const PAGE = '<form id="search-form" data-search-filters="">'
    + '<input type="search" name="SearchText" id="Search" value="car" />'
    + '<aside id="search-filters" class="a4-search-filters">'
    + '<div class="a4-search-filters-head"><button type="button" id="toggle" hidden="hidden" data-search-toggle="" data-text-show="Show filters" data-text-hide="Hide filters">Hide filters</button>'
    + '<button type="submit" id="apply1" data-search-apply="">Apply</button></div>'
    + '<input type="checkbox" name="ClassID[]" value="2" id="class2" />'
    + '<fieldset id="dates" data-search-dates=""><select id="period" name="Published" data-search-period=""></select>'
    + '<div id="range" data-search-range=""><input type="date" id="from" name="PublishedFrom" value="" /><input type="date" id="to" name="PublishedTo" value="" /></div></fieldset>'
    + '<input type="text" id="owner" name="Owner" value="" />'
    + '<button type="submit" id="apply2" data-search-apply="">Apply</button>'
    + '</aside>'
    + '<select id="sort" name="SortBy" data-search-sort=""></select><button type="submit" id="sortbutton" data-search-sort-button="">Sort</button>'
    + '</form>';

function load() {
    const document = createDocument(PAGE);
    document.getElementById('period').value = '';
    document.getElementById('sort').value = 'relevance';
    const sandbox = { document, console };
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);
    vm.runInContext(source, sandbox); // loaded twice: set up once
    return document;
}
function out(name, value) { console.log(JSON.stringify(Object.assign({ name }, value))); }
function applyState(d) { return [d.getElementById('apply1').disabled, d.getElementById('apply2').disabled]; }

{
    const d = load();
    out('initial', { apply: applyState(d), range: d.getElementById('range').hidden, sortButton: d.getElementById('sortbutton').hidden,
        toggle: d.getElementById('toggle').hidden, folded: d.getElementById('search-filters').classList.contains('is-folded'),
        toggleText: d.getElementById('toggle').textContent, expanded: d.getElementById('toggle').getAttribute('aria-expanded') });
}
{
    const d = load();
    const box = d.getElementById('class2');
    box.checked = true;
    d.dispatch('change', box);
    const changed = applyState(d);
    box.checked = false;
    d.dispatch('change', box);
    out('a filter changed and back', { changed, back: applyState(d) });
}
{
    const d = load();
    const period = d.getElementById('period');
    period.value = 'custom';
    d.dispatch('change', period);
    const custom = d.getElementById('range').hidden;
    period.value = 'week';
    d.dispatch('change', period);
    const week = d.getElementById('range').hidden;
    const from = d.getElementById('from');
    from.value = '2026-01-01';
    d.dispatch('input', from);
    out('dates', { custom, week, typed: period.value, rangeAfterTyping: d.getElementById('range').hidden, apply: applyState(d) });
}
{
    const d = load();
    const sort = d.getElementById('sort');
    sort.value = 'name_asc';
    d.dispatch('change', sort);
    out('sort', { submitted: d.submitted, ownerDisabled: d.getElementById('owner').disabled, textDisabled: d.getElementById('Search').disabled,
        fromDisabled: d.getElementById('from').disabled });
}
{
    const d = load();
    d.getElementById('owner').value = 'admin';
    d.getElementById('period').value = 'custom';
    d.getElementById('from').value = '2026-01-01';
    d.dispatch('submit', d.getElementById('search-form'));
    out('submit', { owner: d.getElementById('owner').disabled, from: d.getElementById('from').disabled, to: d.getElementById('to').disabled, period: d.getElementById('period').disabled });
}
{
    const d = load();
    const toggle = d.getElementById('toggle');
    d.dispatch('click', toggle);
    out('unfold', { folded: d.getElementById('search-filters').classList.contains('is-folded'), text: toggle.textContent, expanded: toggle.getAttribute('aria-expanded') });
}
