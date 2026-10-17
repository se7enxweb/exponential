{* "Select all" / "Select none" for a multiple choice of a content check's filter form (shared by every check): two
   submit buttons <name>Choice=all|none, read on the server, so they work without JavaScript. Put the include AFTER the
   form's Show button: Enter in a field presses the form's first button, which must stay Show.

     {include uri='design:contentcheck/filter_choice.tpl' name='Class' label='Classes'|i18n( 'kernel/contentcheck' )}

   Variables: name (the field group, e.g. Class, Kind, Relation), label (its visible name). The same file is in
   design/admin and design/admin4. *}
    <div class="a4-field" role="group" aria-labelledby="cc-{$name|wash}-choice">
        <span class="a4-label" id="cc-{$name|wash}-choice">{$label|wash}</span>
        <div class="a4-row">
            <button type="submit" class="a4-btn a4-btn-small" name="{$name|wash}Choice" value="all">{'Select all'|i18n( 'kernel/contentcheck' )}</button>
            <button type="submit" class="a4-btn a4-btn-small" name="{$name|wash}Choice" value="none">{'Select none'|i18n( 'kernel/contentcheck' )}</button>
        </div>
    </div>
