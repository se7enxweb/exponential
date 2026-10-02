{* The "run now or in the background" choice on a confirmation page. Variables: $job_mode (Job::modeChoice()),
   optional $form_name: the name of a form elsewhere on the page that the choice is sent with (the browse page,
   whose form does not contain this block). The choice is remembered as the user's preference for the operation. *}
{default form_name=''}
<style type="text/css">
{literal}
.exp-jobmode { border: 1px solid #ddd; border-radius: 6px; padding: .6em 1em .4em; margin: .8em 0; }
.exp-jobmode legend { font-weight: bold; padding: 0 .3em; }
.exp-jobmode label { display: block; margin: .3em 0; }
.exp-jobmode label.disabled { color: #888; }
.exp-jobmode .why { display: block; margin-left: 1.6em; font-size: .9em; color: var(--a4-muted, #5d6573); }
{/literal}
</style>
<fieldset class="exp-jobmode" data-prefs={"user/preferences/set"|ezurl} data-op="{$job_mode.op|wash}"{if $form_name} data-form="{$form_name|wash}"{/if}>
    <legend>{'How to run it'|i18n( 'design/admin/content/job' )}</legend>
    <label><input type="radio" name="ContentJobMode" value="job"{if eq( $job_mode.default, 'job' )} checked="checked"{/if} />
        {if eq( $job_mode.recommended, 'job' )}{'Run in the background (recommended for %count items: you can leave the page)'|i18n( 'design/admin/content/job',, hash( '%count', $job_mode.count ) )}{else}{'Run in the background (%count items: you can leave the page)'|i18n( 'design/admin/content/job',, hash( '%count', $job_mode.count ) )}{/if}</label>
    <label{if $job_mode.now_allowed|not} class="disabled"{/if}><input type="radio" name="ContentJobMode" value="now"{if eq( $job_mode.default, 'now' )} checked="checked"{/if}{if $job_mode.now_allowed|not} disabled="disabled"{/if} />
        {'Run now (this page waits until it is done)'|i18n( 'design/admin/content/job' )}
        {if $job_mode.now_allowed|not}<span class="why">{$job_mode.now_reason|wash}</span>{/if}</label>
    <span class="why">{'Below %limit items running now is preselected, from %limit on the background job (content.ini SynchronousLimit). Your last choice is remembered.'|i18n( 'design/admin/content/job',, hash( '%limit', $job_mode.sync_limit ) )}</span>
</fieldset>
<script type="text/javascript">
{literal}
(function () {
  var sets = document.querySelectorAll('fieldset.exp-jobmode'), set = sets[sets.length - 1];
  if (!set) return;
  var op = set.getAttribute('data-op'), formName = set.getAttribute('data-form'), prefs = set.getAttribute('data-prefs');
  set.addEventListener('change', function (e) {
    if (!e.target || e.target.name !== 'ContentJobMode' || !prefs) return;
    fetch(prefs + '/admin_content_job_mode_' + op + '/' + e.target.value, {credentials: 'same-origin', cache: 'no-store'}).catch(function () {});
  });
  // the browse page renders this above its form: listen on the document, the form need not exist yet
  if (formName) {
    document.addEventListener('submit', function (e) {
      var f = e.target, c = set.querySelector('input[name="ContentJobMode"]:checked');
      if (!f || f.getAttribute('name') !== formName || !c) return;
      var h = f.querySelector('input[type="hidden"][name="ContentJobMode"]');
      if (!h) { h = document.createElement('input'); h.type = 'hidden'; h.name = 'ContentJobMode'; f.appendChild(h); }
      h.value = c.value;
    });
  }
})();
{/literal}
</script>
{/default}
