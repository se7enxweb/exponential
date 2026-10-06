{* What the drafts and pending pages (content/draft, content/pendinglist) add to the look of the versions page
   (content/history_exp_style.tpl, .exp-history), which both include first: a confirmation that opens inside a card's
   actions, and the facts of a pending version. Scoped to .exp-drafts. The same file is in design/admin and
   design/admin4. No shared stylesheet is changed. Guide: doc/guides/drafts-and-pending.md *}
{literal}
<style>
.exp-drafts details.exp-confirm-inline { flex: 0 1 auto; border-color: #c9ced6; }
.exp-drafts details.exp-confirm-inline > summary { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-drafts details.exp-confirm-inline[open] { flex: 1 1 100%; }
.exp-drafts details.exp-confirm-inline > div { padding: 0 10px 10px; }
.exp-drafts details.exp-confirm > div label { font-size: 13px; font-weight: 650; color: var(--hi-ink); }
.exp-drafts details.exp-confirm > div select { min-height: 34px; max-width: 100%; padding: 4px 8px; border: 1px solid #8f96a3; border-radius: 8px; background: #fff; color: var(--hi-ink); font: inherit; font-size: 13.5px; }
.exp-drafts .exp-card-title h3 img { vertical-align: -2px; }
.exp-drafts .exp-holds { margin: 10px 0 0; padding: 10px 12px; border-radius: 10px; background: var(--hi-soft); font-size: 13.5px; }
.exp-drafts .exp-holds p + p { margin-top: 4px; }
@media (max-width: 600px) {
    .exp-drafts details.exp-confirm-inline { flex: 1 1 100%; }
}
</style>
{/literal}
