<div class="content-navigation-childlist">
    <table id="ezasi-subitems-list" class="list" cellspacing="0" summary="{'List of sub items of current node, with controlls to edit, remove and move them directly.'|i18n( 'design/admin/node/view/full' )}">
    <thead>
    <tr>
        {* Remove column *}
        <th class="remove"></th>

        {* Name column *}
        <th class="name">{'Name'|i18n( 'design/admin/node/view/full' )}</th>

        {* Hidden/Invisible column *}
        <th class="hidden_invisible">{'Visibility'|i18n( 'design/admin/node/view/full' )}</th>

        {* Class type column *}
        <th class="class nowrap">{'Type'|i18n( 'design/admin/node/view/full' )}</th>

        {* Modifier column *}
        <th class="modifier">{'Modifier'|i18n( 'design/admin/node/view/full' )}</th>

        {* Modified column *}
        <th class="modified nowrap">{'Modified'|i18n( 'design/admin/node/view/full' )}</th>

        {* Section column *}
        <th class="section">{'Section'|i18n( 'design/admin/node/view/full' )}</th>

        {* Priority column *}
        {if eq( $node.sort_array[0][0], 'priority' )}
            <th class="priority">{'Priority'|i18n( 'design/admin/node/view/full' )}</th>
        {/if}

        {* Edit column *}
        <th class="move">&nbsp;</th>

        {* Remove colum *}
        <th class="edit">&nbsp;</th>
    </tr>
    </thead>
<tbody>
{section var=Nodes loop=$children sequence=array( bglight, bgdark )}
{let child_name     = $Nodes.item.name|wash
     node_name      = $node.name
     section_object = fetch( section, object, hash( section_id, $Nodes.object.section_id ) )
     current_locale = $node.object.current_language}

        <tr class="{$Nodes.sequence}">

        {* Remove checkbox *}
        <td></td>

        {* Name *}
        <td>{$Nodes.item.name|wash}</td>

        {* Visibility. *}
        <td class="nowrap">{$Nodes.item.hidden_status_string}</td>

        {* Class type *}
        <td class="class nowrap">{$Nodes.item.class_name|wash}</td>

        {* Modifier *}
        <td class="modifier"><a href={$Nodes.item.object.current.creator.main_node.url_alias|ezurl}>{$Nodes.item.object.current.creator.name|wash}</a></td>

        {* Modified *}
        <td class="modified nowrap">{$Nodes.item.object.modified|l10n( shortdatetime )}</td>

        {* Section *}
        <td>{section show=$section_object}<a href={concat( '/section/view/', $Nodes.object.section_id )|ezurl}>{$section_object.name|wash}</a>{section-else}<i>{'Unknown'|i18n( 'design/admin/node/view/full' )}</i>{/section}</td>

        {* Priority *}
        {if eq( $node.sort_array[0][0], 'priority' )}
            <td>
            {if $node.can_edit}
                <input class="priority ezasi-priority-input" type="text" name="Priority[]" size="3" value="{$Nodes.item.priority}" title="{'Use the priority fields to control the order in which the items appear. You can use both positive and negative integers. Click the "Update priorities" button to apply the changes.'|i18n( 'design/admin/node/view/full' )|wash}" />
                <input type="hidden" name="PriorityID[]" value="{$Nodes.item.node_id}" />
                {else}
                <input class="priority" type="text" name="Priority[]" size="3" value="{$Nodes.item.priority}" title="{'You are not allowed to update the priorities because you do not have permission to edit <%node_name>.'|i18n( 'design/admin/node/view/full',, hash( '%node_name', $node_name ) )|wash}" disabled="disabled" />
            {/if}
            </td>
        {/if}

        {* Delete button. *}
        <td>
            {if and( $Nodes.item.can_edit, $newsletter.status|eq(0) )}
            <form method="post" action={"content/action"|ezurl}>
              <input type="image" src={"edit.png"|ezimage} name="EditButton" title="{'Edit <%child_name>.'|i18n( 'design/admin/node/view/full',, hash( '%child_name', $child_name ) )}" />
              <input type="hidden" name="ContentObjectID" value="{$Nodes.item.contentobject_id}" />
              <input type="hidden" name="NodeID" value="{$Nodes.item.node_id}" />
              <input type="hidden" name="ContentNodeID" value="{$Nodes.item.node_id}" />
              <input type="hidden" name="ContentObjectLanguageCode" value="{$current_locale}" />
              <input type="hidden" name="RedirectURIAfterPublish" value="{concat( '/nvnewsletter/view_newsletter/', $newsletter.id )}" />
              <input type="hidden" name="RedirectIfDiscarded" value="{concat('/nvnewsletter/view_newsletter/', $newsletter.id)}" />
              <input type="hidden" name="RedirectIfCancel" value="{concat('/nvnewsletter/view_newsletter/', $newsletter.id)}" />
            </form>
            {else}
                <img src={'edit-disabled.gif'|ezimage} alt="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to edit <%child_name>.'|i18n( 'design/admin/node/view/full',, hash( '%child_name', $child_name ) )|wash}" />
            {/if}
        </td>

        {* Edit button *}
        <td>
        {if and( $Nodes.item.can_remove, $newsletter.status|eq(0) )}
            <form method="post" action={"content/action"|ezurl}>
              <input type="image" src={"trash-icon-16x16.gif"|ezimage} name="ActionRemove" title="{'Remove <%child_name>.'|i18n( 'design/admin/node/view/full',, hash( '%child_name', $child_name ) )}" />
              <input type="hidden" name="ContentObjectID" value="{$Nodes.item.contentobject_id}" />
              <input type="hidden" name="NodeID" value="{$Nodes.item.node_id}" />
              <input type="hidden" name="ContentNodeID" value="{$Nodes.item.node_id}" />
              <input type="hidden" name="RedirectURIAfterRemove" value="{concat( '/nvnewsletter/view_newsletter/', $newsletter.id )}" />
              <input type="hidden" name="RedirectIfDiscarded" value="{concat('/nvnewsletter/view_newsletter/', $newsletter.id)}" />
              <input type="hidden" name="RedirectIfCancel" value="{concat('/nvnewsletter/view_newsletter/', $newsletter.id)}" />
            </form>
        {else}
            <img src={'trash-icon-16x16-disabled.png'|ezimage} alt="{'Remove'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to remove <%child_name>.'|i18n( 'design/admin/node/view/full',, hash( '%child_name', $child_name ) )|wash}" />
        {/if}
        </td>
  </tr>

{/let}
{/section}
</tbody>
</table>
</div>

