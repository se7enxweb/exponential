{if not( is_set( $self_edit ) )}
    {def $self_edit=false()}
{/if}

{if not( is_set( $self_delete ) )}
    {def $self_delete=false()}
{/if}

<div class="comment">
    <div class="profile-picture">
        <img src="{'user.gif'|ezimage( 'no' )}" alt="{'Avatar'|i18n('design/ezdemo/comment/view')}" />
    </div>
    <div class="comment-content-wrapper">
        <div class="header">
            <span class="profile-name">
                {if $comment.url|eq( '' )}
                    {$comment.name|wash}
                {else}
                    <a href="{$comment.url|wash}">
                        {$comment.name|wash}
                    </a>
                {/if}
            </span>
            <span class="comment-date pull-right">
                {$comment.created|l10n( 'shortdatetime' )}
            </span>
        </div>
    {if $comment.title}
        <div class="comment-headline">
            {$comment.title|wash}
        </div>
    {/if}
        <div class="comment-body">
            {$comment.text|wash|nl2br}
        </div>
    {def $can_edit=fetch( 'comment', 'has_access_to_function', hash( 'function', 'edit',
                                                                     'contentobject', $contentobject,
                                                                     'language_code', $language_code,
                                                                     'comment', $comment,
                                                                     'scope', 'role',
                                                                     'node', $node ) )
         $can_delete=fetch( 'comment', 'has_access_to_function', hash( 'function', 'delete',
                                                                       'contentobject', $contentobject,
                                                                       'language_code', $language_code,
                                                                       'comment', $comment,
                                                                       'scope', 'role',
                                                                       'node', $node ) )
         $user_display_limit_class=concat( ' class="limitdisplay-user limitdisplay-user-', $comment.user_id, '"' )}

    {if or( $can_edit, $can_self_edit, $can_delete, $can_self_delete )}
        <div class="ezcom-comment-tool">
            {if or( $can_edit, $can_self_edit )}
                {if and( $can_self_edit, not( $can_edit ) )}
                    {def $displayAttribute=$user_display_limit_class}
                {else}
                    {def $displayAttribute=''}
                {/if}
                <span{$displayAttribute}>
                    <a href={concat( '/comment/edit/', $comment.id )|ezurl} class="btn btn-warning pull-right">{'Edit'|i18n('design/ezdemo/comment/view')}</a>
                </span>
                {undef $displayAttribute}
            {/if}
            {if or( $can_delete, $can_self_delete )}
                {if and( $can_self_delete, not( $can_delete ) )}
                    {def $displayAttribute=$user_display_limit_class}
                {else}
                    {def $displayAttribute=''}
                {/if}
                <span {$displayAttribute}>
                    <a href={concat( '/comment/delete/',$comment.id )|ezurl} class="btn btn-danger">
                        {'Delete'|i18n('design/ezdemo/comment/view')}
                    </a>
                </span>
                {undef $displayAttribute}
            {/if}
        </div>
    {/if}
    {undef $can_edit $can_delete}
    </div>
</div>
