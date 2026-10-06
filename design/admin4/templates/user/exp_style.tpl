{* The look of the account page (user/edit, user/edit/<id>), in the visual language of the role, section, link and
   cache pages. Included once by user/edit.tpl.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-account and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs; in admin4's dark mode the card stays white, as on the other redesigned pages. user/edit is an edit
   view, which admin4 draws without its main card; .exp-standalone gives the page its own. No shared stylesheet is
   changed. Guide: doc/guides/security-and-audit.md, section 6 *}
{literal}
<style>
.exp-account {
    --ua-ink: var(--a4-ink, #1f2430);
    --ua-muted: var(--a4-muted, #5d6573);
    --ua-line: var(--a4-line, #e3e6eb);
    --ua-soft: var(--a4-soft, #f6f7f9);
    --ua-card: #fff;
    --ua-accent: #c2410c;          /* white text on it is 5.2:1 */
    --ua-accent-hover: #9a3412;
    --ua-ring: rgba(194, 65, 12, 0.45);
    --ua-ok: #166534;   --ua-ok-bg: #e7f5ea;
    --ua-warn: #8a4b00; --ua-warn-bg: #fff3df;
    --ua-bad: #b91c1c;  --ua-bad-bg: #fdecec;
    --ua-info: #1e4fa8; --ua-info-bg: #e8effd;
    --ua-radius: 12px;
    --ua-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--ua-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-account *, .exp-account *::before, .exp-account *::after { box-sizing: border-box; }
.exp-account .box-content { padding-bottom: 20px; }
.exp-account h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-account h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--ua-ink); }
.exp-account h3 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--ua-ink); overflow-wrap: anywhere; }
.exp-account p { margin: 0; }
.exp-account code { font-family: var(--ua-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-account a { color: var(--ua-accent-hover); }
.exp-account a:hover { color: var(--ua-ink); }
.exp-account :focus-visible { outline: 3px solid var(--ua-ring); outline-offset: 2px; }
.exp-account .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-account .exp-meta { color: var(--ua-muted); font-size: 13px; }

/* The page's own card: an edit view has none */
.exp-account.exp-standalone { max-width: 1080px; margin: 16px auto 24px; padding: 18px 20px; border-radius: 18px; background: var(--ua-card);
    box-shadow: 0 0 0 1px rgba(16, 24, 40, 0.06), 0 12px 32px -6px rgba(16, 24, 40, 0.12); }
#maincontent .exp-account.exp-standalone { max-width: none; margin: 0; padding: 0; border-radius: 0; background: transparent; box-shadow: none; }

.exp-account .exp-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
.exp-account .exp-intro { margin: 8px 0 18px; max-width: 78ch; color: var(--ua-muted); }
.exp-account .exp-section { margin: 0 0 22px; }
.exp-account .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }

/* Buttons */
.exp-account .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; max-width: 100%; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--ua-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-account a.exp-btn { color: var(--ua-ink); }
.exp-account .exp-btn:hover { border-color: var(--ua-accent); color: var(--ua-accent-hover); }
.exp-account .exp-btn-primary, .exp-account a.exp-btn-primary { border-color: var(--ua-accent); background: var(--ua-accent); color: #fff; }
.exp-account .exp-btn-primary:hover, .exp-account a.exp-btn-primary:hover { border-color: var(--ua-accent-hover); background: var(--ua-accent-hover); color: #fff; }
.exp-account .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-account .exp-actionbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 0 0 18px; padding: 12px 14px; border: 1px solid var(--ua-line); border-radius: var(--ua-radius); background: var(--ua-soft); }
.exp-account .exp-actionbar .exp-meta { flex: 1 1 260px; }

/* Messages and hints */
.exp-account .exp-hints { display: grid; gap: 8px; margin: 0 0 18px; padding: 0; list-style: none; }
.exp-account .exp-feedback { margin: 0; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-account .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--ua-bad); background: var(--ua-bad-bg); color: var(--ua-bad); }
.exp-account .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--ua-warn); background: var(--ua-warn-bg); color: var(--ua-warn); }
.exp-account .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--ua-info); background: var(--ua-info-bg); color: var(--ua-info); }
.exp-account .exp-feedback strong { color: inherit; }
.exp-account .exp-feedback a { color: inherit; font-weight: 650; }

/* Badges */
.exp-account .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--ua-soft); color: var(--ua-muted); }
.exp-account .exp-badge.is-ok { background: var(--ua-ok-bg); color: var(--ua-ok); }
.exp-account .exp-badge.is-warn { background: var(--ua-warn-bg); color: var(--ua-warn); }
.exp-account .exp-badge.is-bad { background: var(--ua-bad-bg); color: var(--ua-bad); }
.exp-account .exp-badge.is-info { background: var(--ua-info-bg); color: var(--ua-info); }

/* The overview card */
.exp-account .exp-who { display: flex; flex-wrap: wrap; align-items: center; gap: 12px 16px; margin: 0 0 14px; }
.exp-account .exp-avatar { display: inline-flex; align-items: center; justify-content: center; flex: 0 0 auto; width: 52px; height: 52px; border-radius: 50%;
    background: var(--ua-accent); color: #fff; font-size: 21px; font-weight: 700; line-height: 1; }
.exp-account .exp-who-text { flex: 1 1 260px; min-width: 0; }
.exp-account .exp-who-text h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 19px; font-weight: 700; color: var(--ua-ink); overflow-wrap: anywhere; }
.exp-account .exp-who-text p { margin: 2px 0 0; color: var(--ua-muted); overflow-wrap: anywhere; }
.exp-account .exp-panel { margin: 0; padding: 16px 18px; border: 1px solid var(--ua-line); border-radius: var(--ua-radius); background: var(--ua-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-account .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 12px 22px; margin: 0; }
.exp-account .exp-facts > div { min-width: 0; }
.exp-account .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--ua-muted); }
.exp-account .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-account .exp-facts dd .exp-meta { display: block; }
.exp-account ul.exp-inline { margin: 0; padding: 0; list-style: none; }
.exp-account ul.exp-inline li { display: inline; }
.exp-account ul.exp-inline li + li::before { content: ", "; color: var(--ua-muted); }

/* The action cards */
.exp-account .exp-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr)); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-account .exp-card { display: flex; flex-direction: column; gap: 8px; min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--ua-line);
    border-radius: var(--ua-radius); background: var(--ua-card); box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-account .exp-card.is-attention { box-shadow: inset 4px 0 0 var(--ua-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-account .exp-card-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 10px; }
.exp-account .exp-card-head h3 { display: flex; align-items: center; gap: 8px; }
.exp-account .exp-card-head svg { flex: 0 0 auto; color: var(--ua-accent); }
.exp-account .exp-card p { flex: 1 1 auto; color: var(--ua-muted); font-size: 13.5px; }
.exp-account .exp-card .exp-btn { align-self: flex-start; margin-top: auto; }

@media (max-width: 600px) {
    .exp-account.exp-standalone { margin: 8px; padding: 14px 12px; }
    .exp-account .exp-btn { white-space: normal; text-align: center; }
    .exp-account .exp-actionbar .exp-actions { width: 100%; }
    .exp-account .exp-actionbar .exp-btn { flex: 1 1 auto; }
    .exp-account .exp-card .exp-btn { align-self: stretch; }
}
</style>
{/literal}
