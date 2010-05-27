{default attribute_parameters=array()}
{section show=$object.main_node_id|null|not}
    {if $#nvn_site_url|eq('')|not}
    {def $file = $object.data_map.file}
    <a href="{concat($#nvn_site_url, "/content/download/", $file.contentobject_id, "/", $file.id, "/file/", $file.content.original_filename)}">{$file.content.original_filename|wash("xhtml")}</a> ({$file.content.filesize|si(byte)})
    {else}
    <a href="{$object.main_node.url_alias|ezurl(no)}">{$object.name|wash}</a>
    {/if}
{section-else}
    {$object.name|wash}
{/section}
{/default}