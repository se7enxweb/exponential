{* Administration 3: the look of the form token refusal (error/parts/formtoken.tpl
   from design/admin), in the colours of the admin3 buttons (theme/rounded.css). *}
<style>
{literal}
div.formtoken-error{max-width:42rem;margin:1rem 0 2rem;padding:1rem 1.25rem 1.25rem;line-height:1.5}
div.formtoken-error h2{margin:0 0 .75rem}
div.formtoken-error p{margin:0 0 .5rem}
div.formtoken-error .formtoken-actions{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:1rem}
div.formtoken-error .formtoken-actions a{display:inline-block;padding:.35em .9em;border:1px solid #cfd1d3;border-radius:3px;text-decoration:none;color:#44484d;background:#fff;transition:all 300ms ease}
div.formtoken-error .formtoken-actions a:hover,div.formtoken-error .formtoken-actions a:focus{background-color:rgba(0,0,0,.1);color:#000}
div.formtoken-error .formtoken-actions a.formtoken-primary{background-color:#567975;border-color:#567975;color:#f5f3dd}
div.formtoken-error .formtoken-actions a.formtoken-primary:hover,div.formtoken-error .formtoken-actions a.formtoken-primary:focus{background-color:#425c59;color:#ffe9bd}
{/literal}
</style>
