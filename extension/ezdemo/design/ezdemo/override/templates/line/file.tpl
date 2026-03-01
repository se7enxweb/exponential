{* File - Line view *}

<div class="content-view-line">
    <div class="attribute-header">
        <h2>
            <a href="{$node.url_alias|ezurl( 'no' )}" class="teaser-link">{$node.name|wash()}</a>
        </h2>
    </div>
    <article class="class-file file-to-download">
        <div class="header">
            <span class="file-size">{'File size:'|i18n( 'design/ezdemo/line/file' )} {$node.data_map.file.content.filesize|si( byte )}</span>
            <span class="modified-date">({'Modified:'|i18n( 'design/ezdemo/line/file' )} {$node.object.modified|l10n( shortdatetime )})</span>
            <a href="{concat( 'content/download/', $node.data_map.file.contentobject_id, '/', $node.data_map.file.id,'/version/', $node.data_map.file.version , '/file/', $node.data_map.file.content.original_filename|urlencode )|ezurl( 'no' )}" class="pull-right">{'Download file'|i18n( 'design/ezdemo/line/file' )}</a>
        </div>
        <div class="description">
            {attribute_view_gui attribute=$node.data_map.description}
        </div>
    </article>
</div>
