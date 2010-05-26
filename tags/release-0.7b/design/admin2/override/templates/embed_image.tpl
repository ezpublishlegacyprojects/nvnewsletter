{default $object_parameters=array()}
{let image_variation="false"
     align="center"
     attribute_parameters=$object_parameters}
{section show=is_set($attribute_parameters.size)}
{set image_variation=$object.data_map.image.content[$attribute_parameters.size]}
{section-else}
{set image_variation=$object.data_map.image.content[ezini( 'ImageSettings', 'DefaultEmbedAlias', 'content.ini' )]}
{/section}
{section show=is_set($attribute_parameters.align)}
{set align=$attribute_parameters.align}
{section-else}
{set align="center"}
{/section}
<div class="image{$align}">
{section show=is_set($link_parameters.href)}
<a href="{if and( $link_parameters.class|eq( 'tracker' ), $#nvn_tracker_url )}{$#nvn_tracker_url}{/if}{$link_parameters.href|ezurl(no)}" target="{$link_parameters.target}">{/section}
<img src="{if $#nvn_file_url|eq('')|not}{$#nvn_file_url}{/if}{$image_variation.full_path|ezroot(no)}" alt="{$object.data_map.image.content.alternative_text|wash(xhtml)}" border="0" />
{section show=is_set($link_parameters.href)}</a>{/section}
{if $object.data_map.caption.content.output.output_text|eq('')|not}
<div style="width: {$image_variation.width}px;">
{$object.data_map.caption.content.output.output_text}
</div>
{/if}
</div>

{/let}
{/default}
