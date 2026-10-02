// Test content set into the editor at the start of a test (independent of the edited object)
'use strict';

const env = require( './env' );

module.exports = {
    basic: () =>
        '<h2>Testüberschrift</h2>' +
        '<p>Erster Absatz mit <strong>fett</strong> und <em>kursiv</em> und <a href="eznode://2">Link</a>.</p>' +
        '<p>Zweiter Absatz für die Tests.</p>' +
        '<p>Dritter Absatz.</p>' +
        '<table border="0" width="100%"><tbody><tr><td>A1</td><td>B1</td></tr><tr><td>A2</td><td>B2</td></tr></tbody></table>' +
        '<p>Letzter Absatz.</p>',

    withEmbed: () =>
        module.exports.basic() +
        '<div id="eZObject_' + env.embedObject + '" class="ezoeItemNonEditable ezoeItemContentTypeObjects" inline="false" view="embed" alt="medium">ezembed</div>' +
        '<p>Nach dem Embed.</p>',

    withTags: () =>
        module.exports.withEmbed() +
        '<div class="ezoeItemCustomTag factbox" type="custom" customattributes="title|Box"><p>Inhalt der Box</p></div>' +
        '<pre>Literal Zeile</pre>' +
        '<p><u class="ezoeItemCustomTag underline" type="custom">unterstrichen</u> und <a name="marke" class="mceItemAnchor"></a>Anker.</p>' +
        '<ul><li>Punkt</li></ul>'
};
