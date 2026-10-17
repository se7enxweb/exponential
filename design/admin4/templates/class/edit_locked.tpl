{* Shown instead of the class edit form while changes to the class wait for a cronjob (a class with many objects:
   the script syncobjectattributes.php brings the objects in line, ezscriptmonitor shows its progress).

   Says what and since when, and how to run the script by hand; Retry (RetryButton) asks again, Cancel
   (CancelConflictButton) leaves. Every name is the one the view has always read. *}
<form action={concat( 'class/edit/', $class.id )|ezurl} method="post" name="ClassEdit" class="a4-page exp-classgroups a4-standalone">
<div class="context-block">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Edit <%class_name> [Class]'|i18n( 'design/standard/class/edit_locked',, hash( '%class_name', $class.name ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="a4-notice is-warn" role="status">
    <p><strong>{'Class locked'|i18n( 'design/standard/class/edit_locked' )}</strong></p>
    <p>{'This class has pending modifications defered to cronjob and thus it cannot be edited.'|i18n( 'design/standard/class/edit_locked' )}</p>
    {* the source text closes the link itself after %urlend, most translations too, a few do not: the link is closed
       once either way *}
    <p>{'Wait until the script is finished. You might see the status in the %urlstart script monitor%urlend</a>.'|i18n( 'design/standard/class/edit_locked',, hash( '%urlstart', concat( '<a href=', 'scriptmonitor/list'|ezurl, '>' ), '%urlend', '</a>' ) )|explode( '</a></a>' )|implode( '</a>' )}</p>
    <p>{'To force the modification of the class you may run the following command'|i18n( 'design/standard/class/edit_locked' )}:</p>
    <pre class="a4-pre">php extension/ezscriptmonitor/bin/syncobjectattributes.php -s {$access_type.name|wash} --classid={$class.id|int}</pre>
</div>

<div class="a4-card">
<dl class="a4-facts">
    <div><dt>{'Class'|i18n( 'design/standard/class/edit_locked' )}</dt><dd>{$class.name|wash}</dd></div>
    <div><dt>{'Last modifier'|i18n( 'design/standard/class/edit_locked' )}</dt><dd>{if $class.modifier.contentobject}<a href={$class.modifier.contentobject.main_node.url_alias|ezurl}>{$class.modifier.contentobject.name|wash}</a>{/if}</dd></div>
    <div><dt>{'Last modified on'|i18n( 'design/standard/class/edit_locked' )}</dt><dd>{$class.modified|l10n( shortdatetime )}</dd></div>
</dl>
<p class="a4-help a4-card-body">{'The class will be available for editing after the script has been run by the cronjob.'|i18n( 'design/standard/class/edit_locked' )}</p>
</div>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button class="a4-btn a4-btn-primary" type="submit" name="RetryButton" value="1">{'Retry'|i18n( 'design/standard/class/edit_locked' )}</button>
        <button class="a4-btn" type="submit" name="CancelConflictButton" value="1">{'Cancel'|i18n( 'design/standard/class/edit_locked' )}</button>
    </div>
</div>

</div></div></div>
</div>
</form>
