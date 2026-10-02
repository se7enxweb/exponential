{* The styles of the audit views in admin4: its tokens (--a4-*), which follow its light and dark themes. The views'
   markup is in design/admin/templates/audit/ and shared by every admin design. *}
<style type="text/css">
{literal}
.au-view { --au-ink: var(--a4-ink, #1f2430); --au-muted: var(--a4-muted, #5d6573); --au-line: var(--a4-line, #e3e6eb);
           --au-soft: var(--a4-soft, #f6f7f9); --au-radius: var(--a4-radius-s, 9px); --au-accent: var(--a4-orange, #f26a21);
           --au-ok-bg: #e1f1e2; --au-ok: #1e5e22; --au-warn-bg: #fde7d9; --au-warn: #8a3a0c; --au-bad-bg: #fbe3e6; --au-bad: #9b001c;
           --au-s1: #2a78d6; --au-s2: #eb6834; --au-s3: #1baf7a; --au-s4: #eda100; --au-s5: #e87ba4; --au-s6: #008300; --au-s7: #4a3aa7; --au-s8: #e34948;
           --au-serious: #ec835a; --au-critical: #d03b3b; }
{/literal}
</style>
{include uri='design:audit/style_common.tpl'}
