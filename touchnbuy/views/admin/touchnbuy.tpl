{*
 * Touch 'n buy
 * Copyright (C) 2015 ALAA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *}
<table class="table tableDnD" id="imageTable">
	<thead>
		<tr class="nodrag nodrop">
			<th class="fixed-width-xs"><span class="title_box">{l s='Position'}</span></th>
			<th class="fixed-width-lg"><span class="title_box">{l s='Image'}</span></th>
			<th class="fixed-width-lg"><span class="title_box">Haptic data</span></th>
			<th></th> <!-- action -->
            <th class="fixed-width-xs"></th>
		</tr>
	</thead>
	<tbody id="imageList">
        {foreach from=$images item=image}
        <tr id="himage_{$image->id_image}">
            <td class="center">
                {$image->position}
            </td>
            <td class="center">
                <img src="{$smarty.const._THEME_PROD_DIR_}{$image->id_image}/{$image->id_image}-small_default.jpg" />
            </td>

            <td class="center">
                {if $image->hasHaptic}
                <img src="{$smarty.const._THEME_PROD_DIR_}{$image->id_image}/{$image->id_image}-haptic.png" />
                {else}
                no haptic data
                {/if}
            </td>
            <td></td>
            <td style="text-align: left">
                {if $image->hasHaptic}
                <a href="#" class="delete_haptic_image btn btn-default" >
        			<i class="icon-trash"></i> {l s='Delete this image'}
        		</a>
                {else}
                {$image->hapticUploader}
                {/if}
            </td>
        </tr>
        {/foreach}
	</tbody>
</table>
{literal}
<script type="text/javascript">
        $(document).ready(function(){
            function afterDeleteProductImage(data) {
				data = $.parseJSON(data);
				if (data && data.done == true) {
                    $('#himage_'+data.id).remove();
				}
			}

			$('.delete_haptic_image').die().live('click', function(e) {
				e.preventDefault();
				id = $(this).parent().parent().attr('id').split("_");
				if (confirm("{/literal}{l s='Are you sure?' js=1}{literal}"))
				doAdminAjax({
						"method":"remove",
						"id_image":id[1],
						"token" : "{/literal}{$token}{literal}",
						"controller" : "AdminHapticData",
                        processEvents: true,
                        ajax: 'true'}, afterDeleteProductImage
				);
			});
        });
</script>
{/literal}
