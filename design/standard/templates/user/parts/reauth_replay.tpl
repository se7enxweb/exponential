{* The request's own fields, sent again with the answer of "Verify it's you" (expReauth::flatten()), so the action runs as
   it was first sent; for a GET only the mark that it is asked for again; for a POST with a file actually chosen the mark
   that the form opens again after the answer (a browser cannot send the file twice). Variables: replay (name, value),
   replay_get, had_files. *}
{if $replay_get}<input type="hidden" name="ExpReauthReplayGet" value="1" />{/if}
{if and( is_set( $had_files ), $had_files )}<input type="hidden" name="ExpReauthReplayFiles" value="1" />{/if}
{foreach $replay as $field}<input type="hidden" name="{$field.name|wash}" value="{$field.value|wash}" />
{/foreach}
