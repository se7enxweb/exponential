/** Forums: forums, topics of a forum, replies of a topic, reply and new-topic forms (login required for writes).
 *  Services: children (forums), forumTopics, forumReplies, forumReply, forumNewTopic. */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal, el = Exp.util.el, ui = Exp.ui;

    function postForm(label, feature, fixed, done) {
        var $msg = el('p', { role: 'status', 'class': 'meta' }), t = el('input', { id: 'pf-title', name: 'title', required: true }), b = el('textarea', { id: 'pf-body', name: 'body', required: true });
        return el('form', { 'class': 'stack', on: { submit: function (e) {
            e.preventDefault();
            Exp.api.call(feature, [], $.extend({ title: t.val(), body: b.val() }, fixed)).then(function () { t.val(''); b.val(''); ui.announce('Posted'); done(); },
                function (err) { $msg.text(err.code === 401 || err.code === 403 ? 'Please log in to post.' : (err.code === 'unavailable' ? 'Posting is not available yet.' : err.message)); });
        } } }, el('h2', { text: label }), el('label', null, 'Subject', t), el('label', null, 'Message', b), el('button', { type: 'submit', text: 'Post' }), $msg);
    }

    function forums(ctx) {
        var $v = ctx.$view; ui.title('Forums'); $v.empty().append(el('h1', { text: 'Forums' }));
        var $b = el('div'); $v.append($b); ui.loading($b);
        Exp.api.call('children', [Exp.config.nodes.forums, 0, 50]).then(function (env) {
            var l = Exp.api.list(env); $b.attr('aria-busy', 'false').empty();
            if (!l.items.length) { return ui.empty($b, 'No forums yet.'); }
            $b.append(el('ul', { 'class': 'grid' }, $.map(l.items, function (n) { n = Exp.util.node(n);
                return el('li', { 'class': 'card' }, el('h3', null, el('a', { href: '#/forums/' + n.id, text: n.name })), el('p', { 'class': 'meta', text: n.summary || '' })); })));
        }, function (e) { ui.failure($b, e); });
    }

    function topics(ctx) {
        var $v = ctx.$view, off = +ctx.query.offset || 0, lim = Exp.config.pageSize, fid = ctx.params.id;
        $v.empty().append(el('p', null, el('a', { href: '#/forums', text: 'All forums' })), el('h1', { text: 'Topics' }));
        var $b = el('div'); $v.append($b); ui.loading($b);
        function load() {
            Exp.api.call('forumTopics', [fid, off, lim]).then(function (env) {
                var l = Exp.api.list(env); $b.attr('aria-busy', 'false').empty();
                if (!l.items.length) { ui.empty($b, 'No topics yet.'); }
                else { $b.append(el('ul', { 'class': 'thread' }, $.map(l.items, function (n) { n = Exp.util.node(n);
                    return el('li', { 'class': 'card' }, el('a', { href: '#/forums/topic/' + n.id, text: n.name }), ' ', el('span', { 'class': 'meta', text: Exp.util.date(n.date) })); })),
                    ui.pager({ total: l.total, offset: off, limit: lim }, function (o) { Exp.router.go('/forums/' + fid + '?offset=' + o); })); }
                $b.append(postForm('New topic', 'forumNewTopic', { forum_node: fid }, load));
            }, function (e) { ui.failure($b, e); });
        }
        load();
    }

    function topic(ctx) {
        var $v = ctx.$view, tid = ctx.params.id; $v.empty().append(el('h1', { text: 'Topic' }));
        var $b = el('div'); $v.append($b); ui.loading($b);
        function load() {
            Exp.api.call('forumReplies', [tid, 0, 100]).then(function (env) {
                var l = Exp.api.list(env); $b.attr('aria-busy', 'false').empty();
                if (!l.items.length) { ui.empty($b, 'No replies yet.'); }
                else { $b.append(el('ol', { 'class': 'thread' }, $.map(l.items, function (n) { n = Exp.util.node(n);
                    return el('li', { 'class': 'card' }, el('strong', { text: n.name }), ' ', el('span', { 'class': 'meta', text: Exp.util.date(n.date) }), el('p', { text: n.body || n.summary || '' })); }))); }
                $b.append(postForm('Reply', 'forumReply', { parent_node: tid }, load));
            }, function (e) { ui.failure($b, e); });
        }
        load();
    }
    Exp.router.add('/forums', forums); Exp.router.add('/forums/topic/:id', topic); Exp.router.add('/forums/:id', topics);
}(window, jQuery));
