{* Receipt styles, scoped to .order-receipt so they sit inside any site
   design; inlined into the downloadable file as well. Print hides the site
   around the receipt and the receipt's own buttons. *}
<style>
.order-receipt{ldelim}--r-ink:#1b1f24;--r-muted:#5b6470;--r-line:#d9dde3;--r-soft:#f5f7fa;--r-accent:#2f6fde;
  font-family:system-ui,-apple-system,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;color:var(--r-ink);
  max-width:960px;margin:24px auto;padding:0 16px;box-sizing:border-box{rdelim}
.order-receipt *{ldelim}box-sizing:border-box{rdelim}
.order-receipt-actions{ldelim}display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:0 0 16px{rdelim}
.order-receipt-actions a,.order-receipt-actions button{ldelim}font:inherit;font-size:15px;padding:8px 14px;border-radius:8px;
  border:1px solid var(--r-line);background:#fff;color:var(--r-ink);cursor:pointer;text-decoration:none;line-height:1.2{rdelim}
.order-receipt-actions .primary{ldelim}background:var(--r-accent);border-color:var(--r-accent);color:#fff{rdelim}
.order-receipt-hint{ldelim}color:var(--r-muted);font-size:14px;flex-basis:100%{rdelim}
.order-receipt-sheet{ldelim}background:#fff;border:1px solid var(--r-line);border-radius:12px;padding:24px{rdelim}
.order-receipt-head{ldelim}display:flex;flex-wrap:wrap;justify-content:space-between;gap:16px;border-bottom:2px solid var(--r-ink);padding-bottom:16px;margin-bottom:16px{rdelim}
.order-receipt-head h1{ldelim}margin:0;font-size:28px{rdelim}
.order-receipt-site{ldelim}margin:0 0 4px;color:var(--r-muted);font-size:14px;text-transform:uppercase;letter-spacing:.06em{rdelim}
.order-receipt-meta{ldelim}display:grid;grid-template-columns:auto auto;gap:4px 12px;margin:0;font-size:15px{rdelim}
.order-receipt-meta dt{ldelim}color:var(--r-muted){rdelim}
.order-receipt-meta dd{ldelim}margin:0;font-weight:600{rdelim}
.order-receipt h2{ldelim}font-size:18px;margin:24px 0 8px{rdelim}
.order-receipt-scroll{ldelim}overflow-x:auto{rdelim}
.order-receipt-table{ldelim}width:100%;border-collapse:collapse;font-size:15px{rdelim}
.order-receipt-table th,.order-receipt-table td{ldelim}text-align:left;padding:8px 10px;border-bottom:1px solid var(--r-line);vertical-align:top{rdelim}
.order-receipt-table th{ldelim}background:var(--r-soft);font-weight:600;white-space:nowrap{rdelim}
.order-receipt-table .num{ldelim}text-align:right;white-space:nowrap{rdelim}
.order-receipt-total td{ldelim}font-weight:700;border-top:2px solid var(--r-ink){rdelim}
.order-receipt-foot{ldelim}margin-top:24px;color:var(--r-muted);font-size:13px;word-break:break-all{rdelim}
@media (max-width:600px){ldelim}.order-receipt-sheet{ldelim}padding:16px{rdelim}.order-receipt-head h1{ldelim}font-size:22px{rdelim}{rdelim}
@media print{ldelim}
  body.order-receipt-printing > *:not(.order-receipt){ldelim}display:none !important{rdelim}
  body.order-receipt-printing .order-receipt{ldelim}position:static !important{rdelim}
  body *{ldelim}visibility:hidden{rdelim}
  .order-receipt,.order-receipt *{ldelim}visibility:visible{rdelim}
  .order-receipt{ldelim}position:absolute;left:0;top:0;width:100%;max-width:none;margin:0;padding:0{rdelim}
  .order-receipt-actions{ldelim}display:none{rdelim}
  .order-receipt-sheet{ldelim}border:0;border-radius:0;padding:0{rdelim}
  .order-receipt-scroll{ldelim}overflow:visible{rdelim}
{rdelim}
</style>
