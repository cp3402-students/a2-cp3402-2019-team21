<?php global $contLDomain; ?>
<div class="contact_reset-theme">
    <input type="submit" name="theme_reset" class="button button-primary button-large contact_theme-reset" value="Reset to default"/>
</div>

<div id="cont_tabs">
    <ul id="cont_theme_ul">
        <li><a class="cont_li_setting" href="#theme_style_short">  <?php echo 'Short View Options'?>	 </a></li>
		<li><a class="cont_li_setting" href="#theme_style_full">   <?php echo 'Full View Options';?>	 </a></li>
        <li><a class="cont_li_setting" href="#theme_style_table">  <?php echo 'Table View Options'?>	 </a></li>
		<li><a class="cont_li_setting" href="#theme_style_chess">  <?php echo 'Chess View Options'?>	 </a></li>
		<li><a class="cont_li_setting" href="#theme_style_port">   <?php echo 'Portfolio View Options'?> </a></li>
		<li><a class="cont_li_setting" href="#theme_style_blog">   <?php echo 'Blog View Options'?>		 </a></li>
		<li><a class="cont_li_setting" href="#theme_style_circle"> <?php echo 'Circle View Options'?>	 </a></li>
		<li><a class="cont_li_setting" href="#theme_style_square"> <?php echo 'Square View Options'?>	 </a></li>
		<li><a class="cont_li_setting" href="#theme_style_contact"><?php echo 'Contact Page Options'?>	 </a></li>
    </ul>
    <div class="clear"></div>

	<!---------- SHORT SHOWCASE ---------->
    <div id="theme_style_short" class="adminform">
		<h1 class="staff_main_title" title="Click for close"><?php echo _e('Short View',$contLDomain); ?></h1>
        <div class="staff_view">
			<table class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'border'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_border_width-lbl" for="paramscube_short_border_width" class="hasTip" title="Border Width"><?php echo 'Border Width';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[short_border_width]" id="paramscube_short_border_width" value="<?php
						echo isset($param_values['short_border_width'])?$param_values['short_border_width']:'1'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<?php
					$check0 = ""; $check1 = ""; $check2 = ""; $check3 = "";
					$check4 = ""; $check5 = ""; $check6 = ""; $check7 = "";
					if (isset($param_values['short_border_style']) && $param_values['short_border_style'] == 'solid')
						$check0 = '  selected="selected"  ';
					if (isset($param_values['short_border_style']) && $param_values['short_border_style'] == 'double')
						$check1 = '  selected="selected"';
					if (isset($param_values['short_border_style']) && $param_values['short_border_style'] == 'dashed')
						$check2 = '  selected="selected"  ';
					if (isset($param_values['short_border_style']) && $param_values['short_border_style'] == 'dotted')
						$check3 = '  selected="selected"';
					if (isset($param_values['short_border_style']) && $param_values['short_border_style'] == 'groove')
						$check4 = '  selected="selected"  ';
					if (isset($param_values['short_border_style']) && $param_values['short_border_style'] == 'inset')
						$check5 = '  selected="selected"';
					if (isset($param_values['short_border_style']) && $param_values['short_border_style'] == 'outset')
						$check6 = '  selected="selected"  ';
					if (isset($param_values['short_border_style']) && $param_values['short_border_style'] == 'ridge')
						$check7 = '  selected="selected"'; ?>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_border_style-lbl" for="paramscube_short_border_style" class="hasTip" title="Border Style"><?php echo 'Border Style';?></label></span></td>
					<td class="paramlist_value">
						<select name="params[short_border_style]" id="paramscube_short_border_style" >
							<option value="solid"  <?php echo isset($check0)?$check0:''; ?> > solid </option>
							<option value="double" <?php echo isset($check1)?$check1:''; ?> > double </option>
							<option value="dashed" <?php echo isset($check2)?$check2:''; ?> > dashed </option>
							<option value="dotted" <?php echo isset($check3)?$check3:''; ?> > dotted </option>
							<option value="groove" <?php echo isset($check4)?$check4:''; ?> > groove </option>
							<option value="inset"  <?php echo isset($check5)?$check5:''; ?> > inset </option>
							<option value="outset" <?php echo isset($check6)?$check6:''; ?> > outset </option>
							<option value="ridge"  <?php echo isset($check7)?$check7:''; ?> > ridge </option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_border_color-lbl" for="paramscube_short_border_color" class="hasTip" title="Border Color"><?php echo 'Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_border_color]" type="text" class="sc_color" id="paramscube_short_border_color" value="<?php
							echo isset($param_values['short_border_color'])?$param_values['short_border_color']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_title_bg_color-lbl" for="paramscube_short_title_bg_color" class="hasTip" title="Title Background Color"><?php echo 'Title Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_title_bg_color]" type="text" class="sc_color" id="paramscube_short_title_bg_color" value="<?php
							echo isset($param_values['short_title_bg_color'])?$param_values['short_title_bg_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_title_color-lbl" for="paramscube_short_title_color" class="hasTip" title="Title Color"><?php echo 'Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_title_color]" type="text" class="sc_color" id="paramscube_short_title_color" value="<?php
							echo isset($param_values['short_title_color'])?$param_values['short_title_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_title_size-lbl" for="paramscube_short_title_size" class="hasTip" title="Title Size"><?php echo 'Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[short_title_size]" id="paramscube_short_title_size" value="<?php
						echo isset($param_values['short_title_size'])?$param_values['short_title_size']:'18'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_link_color-lbl" for="paramscube_short_link_color" class="hasTip" title="Link Color"><?php echo 'Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_link_color]" type="text" class="sc_color" id="paramscube_short_link_color" value="<?php
							echo isset($param_values['short_link_color'])?$param_values['short_link_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_link_hover_color-lbl" for="paramscube_short_link_hover_color" class="hasTip" title="Link Hover Color"><?php echo 'Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_link_hover_color]" type="text" class="sc_color" id="paramscube_short_link_hover_color" value="<?php
							echo isset($param_values['short_link_hover_color'])?$param_values['short_link_hover_color']:'#D9D9D9';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'social icons'; ?></td></tr>
				<tr>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo 'Social Icons View';?></label></span>
					</td>
					<td class="paramlist_value">
						<?php $checkFb = ""; $checkIs= ""; $checkTw = ""; $checkGp = ""; ?>
						<p><input type="checkbox" name="params[short_social_icons][]" id="params_short_social_icons0" value="0"
						<?php checked(isset($param_values['short_social_icons'])  && in_array(0,$param_values['short_social_icons'])); ?>
						<?php echo isset($checkFb)?$checkFb:'';  ?> />
							<label for="params_short_social_icons0" class="staff_soc_labels"><?php echo 'Facebook';?></label>
							<input type="text" name="params[short_social_fb]" value="<?php echo isset($param_values['short_social_fb'])?$param_values['short_social_fb']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[short_social_icons][]" id="params_short_social_icons1" value="1"
						<?php checked(isset($param_values['short_social_icons'])  && in_array(1,$param_values['short_social_icons'])); ?>
						<?php echo isset($checkIs)?$checkIs:'';  ?> />
							<label for="params_short_social_icons1" class="staff_soc_labels"><?php echo 'Instagram';?></label>
							<input type="text" name="params[short_social_ins]" value="<?php echo isset($param_values['short_social_ins'])?$param_values['short_social_ins']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[short_social_icons][]" id="params_short_social_icons2" value="2"
						<?php checked(isset($param_values['short_social_icons'])  && in_array(2,$param_values['short_social_icons'])); ?>
						<?php echo isset($checkTw)?$checkTw:'';  ?> />
							<label for="params_short_social_icons2" class="staff_soc_labels"><?php echo 'Twitter';?></label>
							<input type="text" name="params[short_social_tw]" value="<?php echo isset($param_values['short_social_tw'])?$param_values['short_social_tw']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[short_social_icons][]" id="params_short_social_icons3" value="3"
						<?php checked(isset($param_values['short_social_icons'])  && in_array(3,$param_values['short_social_icons'])); ?>
						<?php echo isset($checkGp)?$checkGp:'';  ?> />
							<label for="params_short_social_icons3" class="staff_soc_labels"><?php echo 'Google';?></label>
							<input type="text" name="params[short_social_gp]" value="<?php echo isset($param_values['short_social_gp'])?$param_values['short_social_gp']:''; ?>" placeholder="Write some link" /></p>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_soc_bg_color-lbl" for="paramscube_short_soc_bg_color" class="hasTip" title="Social Icons Background Color"><?php echo 'Social Icons Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_soc_bg_color]" type="text" class="sc_color" id="paramscube_short_soc_bg_color" value="<?php
							echo isset($param_values['short_soc_bg_color'])?$param_values['short_soc_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_soc_hover_bg_color-lbl" for="paramscube_short_soc_hover_bg_color" class="hasTip" title="Social Icons Hover Background Color"><?php echo 'Social Icons Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_soc_hover_bg_color]" type="text" class="sc_color" id="paramscube_short_soc_hover_bg_color" value="<?php
							echo isset($param_values['short_soc_hover_bg_color'])?$param_values['short_soc_hover_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_icons_color-lbl" for="paramscube_short_icons_color" class="hasTip" title="Social Icons Color"><?php echo 'Social Icons Color'; ?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_icons_color]" type="text" class="sc_color" id="paramscube_short_icons_color" value="<?php
							echo isset($param_values['short_icons_color'])?$param_values['short_icons_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_icons_hover_color-lbl" for="paramscube_short_icons_hover_color" class="hasTip" title="Social Icons Hover Color"><?php echo 'Social Icons Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_icons_hover_color]" type="text" class="sc_color" id="paramscube_short_icons_hover_color" value="<?php
							echo isset($param_values['short_icons_hover_color'])?$param_values['short_icons_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<?php
					$icon_check0 = " checked='checked' "; $icon_check1 = "";
					if (isset($param_values['short_icon_circle']) && $param_values['short_icon_circle'] == 1) { $icon_check1 = ' checked="checked" '; $icon_check0 = '';} ?>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo ' View Icons Circle';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="radio" name="params[short_icon_circle]" id="params_short_icon_radius0" value="0" <?php echo isset($icon_check0)?$icon_check0:''; ?>/>
							<label for="params_short_icon_radius0"><?php echo 'No';?></label>
						<input type="radio" name="params[short_icon_circle]" id="params_short_icon_radius1" value="1" <?php echo isset($icon_check1)?$icon_check1:''; ?>/>
							<label for="params_short_icon_radius1"><?php echo 'Yes';?></label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'search'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_search_border-lbl" for="paramscube_short_search_border" class="hasTip" title="Search Border Color"><?php echo 'Search Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_search_border]" type="text" class="sc_color" id="paramscube_short_search_border" value="<?php
							echo isset($param_values['short_search_border'])?$param_values['short_search_border']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_search_color-lbl" for="paramscube_short_search_color" class="hasTip" title="Search Text Color"><?php echo 'Search Text Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_search_color]" type="text" class="sc_color" id="paramscube_short_search_color" value="<?php
							echo isset($param_values['short_search_color'])?$param_values['short_search_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'pagination'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_pagination_font_size-lbl" for="paramscube_short_pagination_font_size" class="hasTip" title="Pagination Font Size"><?php echo 'Pagination Font Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[short_pagination_font_size]" id="paramscube_short_pagination_font_size" value="<?php
						echo isset($param_values['short_pagination_font_size'])?$param_values['short_pagination_font_size']:'16'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_pagination_font_color-lbl" for="paramscube_short_pagination_font_color" class="hasTip" title="Pagination Text Color"><?php echo 'Pagination Text Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_pagination_font_color]" type="text" class="sc_color" id="paramscube_short_pagination_font_color" value="<?php
							echo isset($param_values['short_pagination_font_color'])?$param_values['short_pagination_font_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_pagination_bg-lbl" for="paramscube_short_pagination_bg" class="hasTip" title="Pagination Background Color"><?php echo 'Pagination Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_pagination_bg]" type="text" class="sc_color" id="paramscube_short_pagination_bg" value="<?php
							echo isset($param_values['short_pagination_bg'])?$param_values['short_pagination_bg']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_active_pagination_bg-lbl" for="paramscube_short_active_pagination_bg" class="hasTip" title="Pagination Active Page Background Color"><?php echo 'Pagination Active Page Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_active_pagination_bg]" type="text" class="sc_color" id="paramscube_short_active_pagination_bg" value="<?php
							echo isset($param_values['short_active_pagination_bg'])?$param_values['short_active_pagination_bg']:'#00A99D';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_pagination_border_color-lbl" for="paramscube_short_pagination_border_color" class="hasTip" title="Pagination Border Color"><?php echo 'Pagination Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_pagination_border_color]" type="text" class="sc_color" id="paramscube_short_pagination_border_color" value="<?php
							echo isset($param_values['short_pagination_border_color'])?$param_values['short_pagination_border_color']:'#DADADA';  ?>" size="10" />
						</label>
					</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_cont_count_in_page-lbl" for="paramscube_short_cont_count_in_page" class="hasTip" title="Count of Contacts in the Page"><?php echo 'Count of Contacts in the Page';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[short_cont_count_in_page]" id="paramscube_short_cont_count_in_page" value="<?php
						echo isset($param_values['short_cont_count_in_page'])?$param_values['short_cont_count_in_page']:'5'; ?>" class="text_area" size="3" /></td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_picture_width-lbl" for="paramscube_short_picture_width" class="hasTip" title="Contact Width"><?php echo 'Contact Width';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[short_picture_width]" id="paramscube_short_picture_width" value="<?php
						echo isset($param_values['short_picture_width'])?$param_values['short_picture_width']:'48'; ?>" class="text_area" size="4" />%</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_picture_height-lbl" for="paramscube_short_picture_height" class="hasTip" title="Contact Height"><?php echo 'Contact Height';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[short_picture_height]" id="paramscube_short_picture_height" value="<?php
						echo isset($param_values['short_picture_height'])?$param_values['short_picture_height']:'310'; ?>" class="text_area" size="4" />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_hover_bg_color-lbl" for="paramscube_short_hover_bg_color"  class="hasTip" title="Contact Hover Background Color"><?php echo 'Contact Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_hover_bg_color]" type="text" class="sc_color" id="paramscube_short_hover_bg_color" value="<?php
							echo isset($param_values['short_hover_bg_color'])?$param_values['short_hover_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_no_image-lbl" for="paramscube_short_no_image" class="hasTip" title="No Image"><?php echo 'No Image';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="text" id="paramscube_short_no_image"  class="upload" name="params[short_no_image]" value="<?php echo isset($param_values['short_no_image'])?$param_values['short_no_image']:''; ?>" />
						<input class="upload-button sc_upload-button" type="button" value="<?php _e('Upload Image', $contLDomain); ?>"/>
					</td>
				</tr>
			</table>
		</div>

		<h1 class="staff_main_title_popup" title="Click for close"><?php echo _e('Short View Popup',$contLDomain); ?></h1>
		<div class="staff_view_popup">
			<table class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
				   <?php
					$popup_check0 = "";
					$popup_check1 = "";
					$popup_check2 = " checked='checked' ";
					if (isset($param_values['short_popup_position']) && $param_values['short_popup_position'] == '0') {
						$popup_check0 = " checked='checked' ";
						$popup_check1 = ""; $popup_check2 = "";
					}
					if (isset($param_values['short_popup_position']) && $param_values['short_popup_position'] == '1') {
						$popup_check1 = " checked='checked' ";
						$popup_check0 = ""; $popup_check2 = "";
					}
					if (isset($param_values['short_popup_position']) && $param_values['short_popup_position'] == '2') {
						$popup_check2 = " checked='checked' ";
						$popup_check0 = ""; $popup_check1 = "";
					} ?>
					<td class="paramlist_key"><span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius" title="Popup View Position"><?php echo ' Popup View Position';?></label></span></td>
					<td class="paramlist_value">
						<input type="radio" name="params[short_popup_position]" id="params_short_popup_position0" value="0"
						<?php echo isset($popup_check0)?$popup_check0:''; ?>/>
							<label for="params_short_popup_position0"><?php echo 'Left';?></label>

						<input type="radio" name="params[short_popup_position]" id="params_short_popup_position1" value="1"
						<?php echo isset($popup_check1)?$popup_check1:''; ?>/>
							<label for="params_short_popup_position1"><?php echo 'Middle';?></label>

						<input type="radio" name="params[short_popup_position]" id="params_short_popup_position2" value="2"
						<?php echo isset($popup_check2)?$popup_check2:''; ?>/>
							<label for="params_short_popup_position2"><?php echo 'Right';?></label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popup_bg_color-lbl" for="paramscube_short_popup_bg_color"  class="hasTip" title="Popup Background Color"><?php echo 'Popup Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_popup_bg_color]" type="text" class="sc_color" id="paramscube_short_popup_bg_color" value="<?php
							echo isset($param_values['short_popup_bg_color'])?$param_values['short_popup_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popup_content_bg_color-lbl" for="paramscube_short_popup_content_bg_color"  class="hasTip" title="Popup Content Background Color"><?php echo 'Popup Content Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_popup_content_bg_color]" type="text" class="sc_color" id="paramscube_short_popup_content_bg_color" value="<?php
							echo isset($param_values['short_popup_content_bg_color'])?$param_values['short_popup_content_bg_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popup_title_color-lbl" for="paramscube_short_popup_title_color" class="hasTip" title="Popup Title Color"><?php echo 'Popup Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_popup_title_color]" type="text" class="sc_color" id="paramscube_short_popup_title_color" value="<?php
							echo isset($param_values['short_popup_title_color'])?$param_values['short_popup_title_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popup_title_size-lbl" for="paramscube_short_popup_title_size" class="hasTip" title="Popup Title Size"><?php echo 'Popup Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[short_popup_title_size]" id="paramscube_short_popup_title_size" value="<?php
						echo isset($param_values['short_popup_title_size'])?$param_values['short_popup_title_size']:'30'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popup_text_color-lbl" for="paramscube_short_popup_text_color" class="hasTip" title="Popup Text Color"><?php echo 'Popup Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_popup_text_color]" type="text" class="sc_color" id="paramscube_short_popup_text_color" value="<?php
							echo isset($param_values['short_popup_text_color'])?$param_values['short_popup_text_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popup_text_size-lbl" for="paramscube_short_popup_text_size" class="hasTip" title="Popup Text Size"><?php echo 'Popup Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[short_popup_text_size]" id="paramscube_short_popup_text_size" value="<?php
						echo isset($param_values['short_popup_text_size'])?$param_values['short_popup_text_size']:'16'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popoup_link_color-lbl" for="paramscube_short_popoup_link_color" class="hasTip" title="Popup Link Color"><?php echo 'Popup Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_popoup_link_color]" type="text" class="sc_color" id="paramscube_short_popoup_link_color" value="<?php
							echo isset($param_values['short_popoup_link_color'])?$param_values['short_popoup_link_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popoup_link_hover_color-lbl" for="paramscube_short_popoup_link_hover_color" class="hasTip" title="Popup Link Hover Color"><?php echo 'Popup Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_popoup_link_hover_color]" type="text" class="sc_color" id="paramscube_short_popoup_link_hover_color" value="<?php
							echo isset($param_values['short_popoup_link_hover_color'])?$param_values['short_popoup_link_hover_color']:'#00A99D';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'close button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popoup_close_color-lbl" for="paramscube_short_popoup_close_color" class="hasTip" title="Popup Close Button Color"><?php echo 'Popup Close Button Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_popoup_close_color]" type="text" class="sc_color" id="paramscube_short_popoup_close_color" value="<?php
							echo isset($param_values['short_popoup_close_color'])?$param_values['short_popoup_close_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_button_bg_color-lbl" for="paramscube_short_button_bg_color" class="hasTip" title="Popup Button Background Color"><?php echo 'Popup Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_button_bg_color]" type="text" class="sc_color" id="paramscube_short_button_bg_color" value="<?php
							echo isset($param_values['short_button_bg_color'])?$param_values['short_button_bg_color']:'#00A99D';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_button_hover_bg_color-lbl" for="paramscube_short_button_hover_bg_color" class="hasTip" title="Popup Button Hover Background Color"><?php echo 'Popup Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_button_hover_bg_color]" type="text" class="sc_color" id="paramscube_short_button_hover_bg_color" value="<?php
							echo isset($param_values['short_button_hover_bg_color'])?$param_values['short_button_hover_bg_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_button_link_color-lbl" for="paramscube_short_button_link_color" class="hasTip" title="Popup Button Link Color"><?php echo 'Popup Button Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_button_link_color]" type="text" class="sc_color" id="paramscube_short_button_link_color" value="<?php
							echo isset($param_values['short_button_link_color'])?$param_values['short_button_link_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_button_hover_link_color-lbl" for="paramscube_short_button_hover_link_color" class="hasTip" title="Popup Button Hover Link Color"><?php echo 'Popup Button Hover Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_button_hover_link_color]" type="text" class="sc_color" id="paramscube_short_button_hover_link_color" value="<?php
							echo isset($param_values['short_button_hover_link_color'])?$param_values['short_button_hover_link_color']:'#000000';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'social icons'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><?php echo 'Popup Social Icons View';?></span></td>
					<td class="paramlist_value">
						<?php $checkFb = ""; $checkIs= ""; $checkTw = "";$checkGp = ""; ?>
						<p><input type="checkbox" name="params[short_popup_social_icons][]" id="params_short_social_icons0" value="0"
						<?php checked(isset($param_values['short_popup_social_icons'])  && in_array(0,$param_values['short_popup_social_icons'])); ?>
						<?php echo isset($checkFb)?$checkFb:'';  ?> />
							<label for="params_short_social_icons0"><?php echo 'Facebook';?></label></p>

						<p><input type="checkbox" name="params[short_popup_social_icons][]" id="params_short_social_icons1" value="1"
						<?php checked(isset($param_values['short_popup_social_icons'])  && in_array(1,$param_values['short_popup_social_icons'])); ?>
						<?php echo isset($checkIs)?$checkIs:'';  ?>  />
							<label for="params_short_popup_social_icons1"><?php echo 'Instagram';?></label></p>

						<p><input type="checkbox" name="params[short_popup_social_icons][]" id="params_short_popup_social_icons2" value="2"
						<?php checked(isset($param_values['short_popup_social_icons'])  && in_array(2,$param_values['short_popup_social_icons'])); ?>
						<?php echo isset($checkTw)?$checkTw:'';  ?>  />
							<label for="params_short_popup_social_icons2"><?php echo 'Twitter';?></label></p>

						<p><input type="checkbox" name="params[short_popup_social_icons][]" id="params_short_popup_social_icons3" value="3"
						<?php checked(isset($param_values['short_popup_social_icons'])  && in_array(3,$param_values['short_popup_social_icons'])); ?>
						<?php echo isset($checkGp)?$checkGp:'';  ?>  />
							<label for="params_short_popup_social_icons3"><?php echo 'Google';?></label></p>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popup_soc_bg_color-lbl" for="paramscube_short_popup_soc_bg_color" class="hasTip" title="Popup Social Icons Background Color"><?php echo 'Popup Social Icons Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_popup_soc_bg_color]" type="text" class="sc_color" id="paramscube_short_popup_soc_bg_color" value="<?php
							echo isset($param_values['short_popup_soc_bg_color'])?$param_values['short_popup_soc_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popup_soc_hover_bg_color-lbl" for="paramscube_short_popup_soc_hover_bg_color" class="hasTip" title="Popup Social Icons Hover Background Color"><?php echo 'Popup Social Icons Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_popup_soc_hover_bg_color]" type="text" class="sc_color" id="paramscube_short_popup_soc_hover_bg_color" value="<?php
							echo isset($param_values['short_popup_soc_hover_bg_color'])?$param_values['short_popup_soc_hover_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popup_icons_color-lbl" for="paramscube_short_popup_icons_color" class="hasTip" title="Social Icons Color"><?php echo 'Popup Social Icons Color'; ?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_popup_icons_color]" type="text" class="sc_color" id="paramscube_short_popup_icons_color" value="<?php
							echo isset($param_values['short_popup_icons_color'])?$param_values['short_popup_icons_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_short_popup_icons_hover_color-lbl" for="paramscube_short_popup_icons_hover_color" class="hasTip" title="Social Icons Hover Color"><?php echo 'Popup Social Icons Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[short_popup_icons_hover_color]" type="text" class="sc_color" id="paramscube_short_popup_icons_hover_color" value="<?php
							echo isset($param_values['short_popup_icons_hover_color'])?$param_values['short_popup_icons_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<?php
					$icon_check0 = " checked='checked' ";
					$icon_check1 = "";
					if (isset($param_values['short_popup_icon_circle']) && $param_values['short_popup_icon_circle'] == 1) {
						$icon_check1 = ' checked="checked" ';
						$icon_check0 = '';
					} ?>
					<td class="paramlist_key"><span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius" title="Popup View Icons Circle"><?php echo 'Popup View Icons Circle';?></label></span></td>
					<td class="paramlist_value">
						<input type="radio" name="params[short_popup_icon_circle]" id="params_short_popup_icon_radius0" value="0" <?php echo isset($icon_check0)?$icon_check0:''; ?>/>
							<label for="params_short_popup_icon_radius0"><?php echo 'No';?></label>
						<input type="radio" name="params[short_popup_icon_circle]" id="params_short_popup_icon_radius1" value="1" <?php echo isset($icon_check1)?$icon_check1:''; ?>/>
							<label for="params_short_popup_icon_radius1"><?php echo 'Yes';?></label>
					</td>
				</tr>
			</table>
		</div>
	</div>


	<!---------- FULL SHOWCASE ---------->
    <div id="theme_style_full" class="adminform">
		<h1 class="staff_main_title" title="Click for close"><?php echo _e('Full View',$contLDomain); ?></h1>
		<div class="staff_view">
			<table  class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'border'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_border_width-lbl" for="paramscube_full_border_width" class="hasTip" title="Border Width"><?php echo 'Border Width';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[full_border_width]" id="paramscube_full_border_width" value="<?php
						echo isset($param_values['full_border_width'])?$param_values['full_border_width']:'1'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<?php
					$check0 = ""; $check1 = "";
					$check2 = ""; $check3 = "";
					$check4 = ""; $check5 = "";
					$check6 = ""; $check7 = "";
					if (isset($param_values['full_border_style']) && $param_values['full_border_style'] == 'solid')
						$check0 = '  selected="selected"  ';
					if (isset($param_values['full_border_style']) && $param_values['full_border_style'] == 'double')
						$check1 = '  selected="selected"';
					if (isset($param_values['full_border_style']) && $param_values['full_border_style'] == 'dashed')
						$check2 = '  selected="selected"  ';
					if (isset($param_values['full_border_style']) && $param_values['full_border_style'] == 'dotted')
						$check3 = '  selected="selected"';
					if (isset($param_values['full_border_style']) && $param_values['full_border_style'] == 'groove')
						$check4 = '  selected="selected"  ';
					if (isset($param_values['full_border_style']) && $param_values['full_border_style'] == 'inset')
						$check5 = '  selected="selected"';
					if (isset($param_values['full_border_style']) && $param_values['full_border_style'] == 'outset')
						$check6 = '  selected="selected"  ';
					if (isset($param_values['full_border_style']) && $param_values['full_border_style'] == 'ridge')
						$check7 = '  selected="selected"'; ?>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_border_style-lbl" for="paramscube_full_border_style" class="hasTip" title="Border Style"><?php echo 'Border Style';?></label></span></td>
					<td class="paramlist_value">
						<select name="params[full_border_style]" id="paramscube_full_border_style" >
							<option value="solid"  <?php echo isset($check0)?$check0:''; ?> > solid </option>
							<option value="double" <?php echo isset($check1)?$check1:''; ?> > double </option>
							<option value="dashed" <?php echo isset($check2)?$check2:''; ?> > dashed </option>
							<option value="dotted" <?php echo isset($check3)?$check3:''; ?> > dotted </option>
							<option value="groove" <?php echo isset($check4)?$check4:''; ?> > groove </option>
							<option value="inset"  <?php echo isset($check5)?$check5:''; ?> > inset </option>
							<option value="outset" <?php echo isset($check6)?$check6:''; ?> > outset </option>
							<option value="ridge"  <?php echo isset($check7)?$check7:''; ?> > ridge </option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_border_color-lbl" for="paramscube_full_border_color" class="hasTip" title="Border Color"><?php echo 'Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_border_color]" type="text" class="sc_color" id="paramscube_full_border_color" value="<?php
							echo isset($param_values['full_border_color'])?$param_values['full_border_color']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_title_color-lbl" for="paramscube_full_title_color" class="hasTip" title="Title Color"><?php echo 'Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_title_color]" type="text" class="sc_color" id="paramscube_full_title_color" value="<?php
							echo isset($param_values['full_title_color'])?$param_values['full_title_color']:'#B3B3B3'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_title_size-lbl" for="paramscube_full_title_size" class="hasTip" title="Title Size"><?php echo 'Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[full_title_size]" id="paramscube_full_title_size" value="<?php
						echo isset($param_values['full_title_size'])?$param_values['full_title_size']:'20'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_text_color-lbl" for="paramscube_full_text_color" class="hasTip" title="Text Color"><?php echo 'Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_text_color]" type="text" class="sc_color" id="paramscube_full_text_color" value="<?php
							echo isset($param_values['full_text_color'])?$param_values['full_text_color']:'#393939'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_text_size-lbl" for="paramscube_full_text_size" class="hasTip" title="Text Size"><?php echo 'Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[full_text_size]" id="paramscube_full_text_size" value="<?php
						echo isset($param_values['full_text_size'])?$param_values['full_text_size']:'14'; ?>" class="text_area"  />px</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_link_color-lbl" for="paramscube_full_link_color" class="hasTip" title="Link Color"><?php echo 'Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_link_color]" type="text" class="sc_color" id="paramscube_full_link_color" value="<?php
							echo isset($param_values['full_link_color'])?$param_values['full_link_color']:'#B3B3B3'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_link_hover_color-lbl" for="paramscube_full_link_hover_color" class="hasTip" title="Link Hover Color"><?php echo 'Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_link_hover_color]" type="text" class="sc_color" id="paramscube_full_link_hover_color" value="<?php
							echo isset($param_values['full_link_hover_color'])?$param_values['full_link_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_button_bg_color-lbl" for="paramscube_full_button_bg_color" class="hasTip" title="Button Background Color"><?php echo 'Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_button_bg_color]" type="text" class="sc_color" id="paramscube_full_button_bg_color" value="<?php
							echo isset($param_values['full_button_bg_color'])?$param_values['full_button_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_button_hover_bg_color-lbl" for="paramscube_full_button_hover_bg_color" class="hasTip" title="Button Hover Background Color"><?php echo 'Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_button_hover_bg_color]" type="text" class="sc_color" id="paramscube_full_button_hover_bg_color" value="<?php
							echo isset($param_values['full_button_hover_bg_color'])?$param_values['full_button_hover_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_button_color-lbl" for="paramscube_full_button_color" class="hasTip" title="Button Link Color"><?php echo 'Button Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_button_color]" type="text" class="sc_color" id="paramscube_full_button_color" value="<?php
							echo isset($param_values['full_button_color'])?$param_values['full_button_color']:'#B3B3B3'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_button_hover_color-lbl" for="paramscube_full_button_hover_color" class="hasTip" title="Button Hover Link Color"><?php echo 'Button Hover Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_button_hover_color]" type="text" class="sc_color" id="paramscube_full_button_hover_color" value="<?php
							echo isset($param_values['full_button_hover_color'])?$param_values['full_button_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'social icons'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><?php echo 'Social Icons View';?></span></td>
					<td class="paramlist_value">
						<?php $checkFb = ""; $checkIs= ""; $checkTw = "";$checkGp = ""; ?>
						<p><input type="checkbox" name="params[full_social_icons][]" id="params_full_social_icons0" value="0"
						<?php checked(isset($param_values['full_social_icons'])  && in_array(0,$param_values['full_social_icons'])); ?>
						<?php echo isset($checkFb)?$checkFb:'';  ?> />
							<label for="params_full_social_icons0" class="staff_soc_labels"><?php echo 'Facebook';?></label>
							<input type="text" name="params[full_social_fb]" value="<?php echo isset($param_values['full_social_fb'])?$param_values['full_social_fb']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[full_social_icons][]" id="params_full_social_icons1" value="1"
						<?php checked(isset($param_values['full_social_icons'])  && in_array(1,$param_values['full_social_icons'])); ?>
						<?php echo isset($checkIs)?$checkIs:'';  ?> />
							<label for="params_full_social_icons1" class="staff_soc_labels"><?php echo 'Instagram';?></label>
							<input type="text" name="params[full_social_ins]" value="<?php echo isset($param_values['full_social_ins'])?$param_values['full_social_ins']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[full_social_icons][]" id="params_full_social_icons2" value="2"
						<?php checked(isset($param_values['full_social_icons'])  && in_array(2,$param_values['full_social_icons'])); ?>
						<?php echo isset($checkTw)?$checkTw:'';  ?> />
							<label for="params_full_social_icons2" class="staff_soc_labels"><?php echo 'Twitter';?></label>
							<input type="text" name="params[full_social_tw]" value="<?php echo isset($param_values['full_social_tw'])?$param_values['full_social_tw']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[full_social_icons][]" id="params_full_social_icons3" value="3"
						<?php checked(isset($param_values['full_social_icons'])  && in_array(3,$param_values['full_social_icons'])); ?>
						<?php echo isset($checkGp)?$checkGp:'';  ?> />
							<label for="params_full_social_icons3" class="staff_soc_labels"><?php echo 'Google';?></label>
							<input type="text" name="params[full_social_gp]" value="<?php echo isset($param_values['full_social_gp'])?$param_values['full_social_gp']:''; ?>" placeholder="Write some link" /></p>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_social_bg_color-lbl" for="paramscube_full_social_bg_color" class="hasTip" title="Social Icons Background Color"><?php echo 'Social Icons Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_social_bg_color]" type="text" class="sc_color" id="paramscube_full_social_bg_color" value="<?php
							echo isset($param_values['full_social_bg_color'])?$param_values['full_social_bg_color']:'#B3B3B3'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_social_hover_bg_color-lbl" for="paramscube_full_social_hover_bg_color" class="hasTip" title="Social Icons Hover Background Color"><?php echo 'Social Icons Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_social_hover_bg_color]" type="text" class="sc_color" id="paramscube_full_social_hover_bg_color" value="<?php
							echo isset($param_values['full_social_hover_bg_color'])?$param_values['full_social_hover_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_icons_color-lbl" for="paramscube_full_icons_color" class="hasTip" title="Social Icons Color"><?php echo 'Social Icons Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_icons_color]" type="text" class="sc_color" id="paramscube_full_icons_color" value="<?php
							echo isset($param_values['full_icons_color'])?$param_values['full_icons_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_icons_hover_color-lbl" for="paramscube_full_icons_hover_color" class="hasTip" title="Social Icons Hover Color"><?php echo 'Social Icons Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_icons_hover_color]" type="text" class="sc_color" id="paramscube_full_icons_hover_color" value="<?php
							echo isset($param_values['full_icons_hover_color'])?$param_values['full_icons_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<?php
					$icon_check0 = " checked='checked' ";
					$icon_check1 = "";
					if (isset($param_values['full_icon_circle']) && $param_values['full_icon_circle'] == 1) { $icon_check1 = ' checked="checked" '; $icon_check0 = '';} ?>
					<td class="paramlist_key"><span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo ' View Icons Circle';?></label></span></td>
					<td class="paramlist_value">
						<input type="radio" name="params[full_icon_circle]" id="params_full_icon_radius0" value="0" <?php echo isset($icon_check0)?$icon_check0:''; ?>/>
							<label for="params_full_icon_radius0"><?php echo 'No';?></label>
						<input type="radio" name="params[full_icon_circle]" id="params_full_icon_radius1" value="1" <?php echo isset($icon_check1)?$icon_check1:''; ?>/>
							<label for="params_full_icon_radius1"><?php echo 'Yes';?></label>
					</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'search'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_search_border-lbl" for="paramscube_full_search_border" class="hasTip" title="Search Border Color"><?php echo 'Search Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_search_border]" type="text" class="sc_color" id="paramscube_full_search_border" value="<?php
							echo isset($param_values['full_search_border'])?$param_values['full_search_border']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_search_color-lbl" for="paramscube_full_search_color" class="hasTip" title="Search Text Color"><?php echo 'Search Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_search_color]" type="text" class="sc_color" id="paramscube_full_search_color" value="<?php
							echo isset($param_values['full_search_color'])?$param_values['full_search_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'pagination'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_pagination_font-lbl" for="paramscube_full_pagination_font" class="hasTip" title="Pagination Font Size"><?php echo 'Pagination Font Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[full_pagination_font]" id="paramscube_full_pagination_font" value="<?php
						echo isset($param_values['full_pagination_font'])?$param_values['full_pagination_font']:'16'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_pagination_font_color-lbl" for="paramscube_full_pagination_font_color" class="hasTip" title="Pagination Text Color"><?php echo 'Pagination Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_pagination_font_color]" type="text" class="sc_color" id="paramscube_full_pagination_font_color" value="<?php
							echo isset($param_values['full_pagination_font_color'])?$param_values['full_pagination_font_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_pagination_bg-lbl" for="paramscube_full_pagination_bg" class="hasTip" title="Pagination Background Color"><?php echo 'Pagination Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_pagination_bg]" type="text" class="sc_color" id="paramscube_full_pagination_bg" value="<?php
							echo isset($param_values['full_pagination_bg'])?$param_values['full_pagination_bg']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_active_pagination_bg-lbl" for="paramscube_full_active_pagination_bg" class="hasTip" title="Pagination Active Page Background Color"><?php echo 'Pagination Active Page Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_active_pagination_bg]" type="text" class="sc_color" id="paramscube_full_active_pagination_bg" value="<?php
							  echo isset($param_values['full_active_pagination_bg'])?$param_values['full_active_pagination_bg']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_pagination_border_color-lbl" for="paramscube_full_pagination_border_color" class="hasTip" title="Pagination Border Color"><?php echo 'Pagination Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_pagination_border_color]" type="text" class="sc_color" id="paramscube_full_pagination_border_color" value="<?php
							echo isset($param_values['full_pagination_border_color'])?$param_values['full_pagination_border_color']:'#DADADA';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_cont_count_in_page-lbl" for="paramscube_full_cont_count_in_page" class="hasTip" title="Count of Contacts in the Page"><?php echo 'Count of Contacts in the Page';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[full_cont_count_in_page]" id="paramscube_full_cont_count_in_page" value="<?php
						echo isset($param_values['full_cont_count_in_page'])?$param_values['full_cont_count_in_page']:'4';  ?>" class="text_area" size="3" />
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_picture_width-lbl" for="paramscube_full_picture_width" class="hasTip" title="Contact Image Width"><?php echo 'Contact Image Width';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[full_picture_width]" id="paramscube_full_picture_width" value="<?php
						echo isset($param_values['full_picture_width'])?$param_values['full_picture_width']:'35'; ?>" class="text_area" size="4" />%
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_picture_height-lbl" for="paramscube_full_picture_height" class="hasTip" title="Contact Height"><?php echo 'Contact Height';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[full_contact_height]" id="paramscube_full_picture_height" value="<?php
						echo isset($param_values['full_contact_height'])?$param_values['full_contact_height']:'260'; ?>" class="text_area" size="4" />px
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_background_color-lbl" for="paramscube_full_background_color"  class="hasTip" title="Content Background Color"><?php echo 'Content Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_background_color]" type="text" class="sc_color" id="paramscube_full_background_color" value="<?php
							echo isset($param_values['full_background_color'])?$param_values['full_background_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_image_hover_bg_color-lbl" for="paramscube_full_image_hover_bg_color"  class="hasTip" title="Image Hover Background Color"><?php echo 'Image Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_image_hover_bg_color]" type="text" class="sc_color" id="paramscube_full_image_hover_bg_color" value="<?php
							echo isset($param_values['full_image_hover_bg_color'])?$param_values['full_image_hover_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_no_image-lbl" for="paramscube_full_no_image" class="hasTip" title="No Image"><?php echo 'No Image';?></label></span></td>
					<td class="paramlist_value">
						<input type="text" id="paramscube_full_no_image"  class="upload" name="params[full_no_image]" value="<?php echo isset($param_values['full_no_image'])?$param_values['full_no_image']:''; ?>" />
						<input class="upload-button sc_upload-button" type="button" value="<?php _e('Upload Image', $contLDomain); ?>"/>
					</td>
				</tr>
			</table>
		</div>

		<h1 class="staff_main_title_popup" title="Click for close"><?php echo _e('Full View Popup',$contLDomain); ?></h1>
		<div class="staff_view_popup">
			<table class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_bg_color-lbl" for="paramscube_full_popup_bg_color"  class="hasTip" title="Popup Background Color"><?php echo 'Popup Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_bg_color]" type="text" class="sc_color" id="paramscube_full_popup_bg_color" value="<?php
							echo isset($param_values['full_popup_bg_color'])?$param_values['full_popup_bg_color']:'#F3F3F4'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_pic_width-lbl" for="paramscube_full_popup_pic_width" class="hasTip" title="Popup Contact Width"><?php echo 'Popup Contact Width';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[full_popup_pic_width]" id="paramscube_full_popup_pic_width" value="<?php
						echo isset($param_values['full_popup_pic_width'])?$param_values['full_popup_pic_width']:'38'; ?>" class="text_area" size="4" />%</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_picture_height-lbl" for="paramscube_full_popup_picture_height" class="hasTip" title="Popup Contact Height"><?php echo 'Popup Contact Height';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[full_popup_picture_height]" id="paramscube_full_popup_picture_height" value="<?php
						echo isset($param_values['full_popup_picture_height'])?$param_values['full_popup_picture_height']:'275'; ?>" class="text_area" size="4" />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_title_bg_color-lbl" for="paramscube_full_popup_title_bg_color"  class="hasTip" title="Popup Title Background Color"><?php echo 'Popup Title Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_title_bg_color]" type="text" class="sc_color" id="paramscube_full_popup_title_bg_color" value="<?php
							echo isset($param_values['full_popup_title_bg_color'])?$param_values['full_popup_title_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_title_color-lbl" for="paramscube_full_popup_title_color" class="hasTip" title="Popup Title Color"><?php echo 'Popup Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_title_color]" type="text" class="sc_color" id="paramscube_full_popup_title_color" value="<?php
							echo isset($param_values['full_popup_title_color'])?$param_values['full_popup_title_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_title_size-lbl" for="paramscube_full_popup_title_size" class="hasTip" title="Popup Title Size"><?php echo 'Popup Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[full_popup_title_size]" id="paramscube_full_popup_title_size" value="<?php
						echo isset($param_values['full_popup_title_size'])?$param_values['full_popup_title_size']:'40'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_text_color-lbl" for="paramscube_full_popup_text_color" class="hasTip" title="Popup Text Color"><?php echo 'Popup Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_text_color]" type="text" class="sc_color" id="paramscube_full_popup_text_color" value="<?php
							echo isset($param_values['full_popup_text_color'])?$param_values['full_popup_text_color']:'#393939'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_text_size-lbl" for="paramscube_full_popup_text_size" class="hasTip" title="Popup Text Size"><?php echo 'Popup Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[full_popup_text_size]" id="paramscube_full_popup_text_size" value="<?php
						echo isset($param_values['full_popup_text_size'])?$param_values['full_popup_text_size']:'15'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_link_color-lbl" for="paramscube_full_popup_link_color" class="hasTip" title="Link Color"><?php echo 'Popup Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_link_color]" type="text" class="sc_color" id="paramscube_full_popup_link_color" value="<?php
							echo isset($param_values['full_popup_link_color'])?$param_values['full_popup_link_color']:'#B3B3B3'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_link_hover_color-lbl" for="paramscube_full_popup_link_hover_color" class="hasTip" title="Link Hover Color"><?php echo 'Popup Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_link_hover_color]" type="text" class="sc_color" id="paramscube_full_popup_link_hover_color" value="<?php
							echo isset($param_values['full_popup_link_hover_color'])?$param_values['full_popup_link_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'close button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popoup_close_color-lbl" for="paramscube_full_popoup_close_color" class="hasTip" title="Popup Close Button Color"><?php echo 'Popup Close Button Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popoup_close_color]" type="text" class="sc_color" id="paramscube_full_popoup_close_color" value="<?php
							echo isset($param_values['full_popoup_close_color'])?$param_values['full_popoup_close_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'parameters'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_param_bg_color-lbl" for="paramscube_full_popup_param_bg_color" class="hasTip" title="Popup Parameters Background Color"><?php echo 'Popup Parameters Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_param_bg_color]" type="text" class="sc_color" id="paramscube_full_popup_param_bg_color" value="<?php
							echo isset($param_values['full_popup_param_bg_color'])?$param_values['full_popup_param_bg_color']:'#F3F3F4'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_param_color-lbl" for="paramscube_full_popup_param_color" class="hasTip" title="Popup Parameters Color"><?php echo 'Popup Parameters Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_param_color]" type="text" class="sc_color" id="paramscube_full_popup_param_color" value="<?php
							echo isset($param_values['full_popup_param_color'])?$param_values['full_popup_param_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_button_bg-lbl" for="paramscube_full_popup_button_bg" class="hasTip" title="Popup Button Background Color"><?php echo 'Popup Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_button_bg]" type="text" class="sc_color" id="paramscube_full_popup_button_bg" value="<?php
							echo isset($param_values['full_popup_button_bg'])?$param_values['full_popup_button_bg']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_button_hover_bg-lbl" for="paramscube_full_popup_button_hover_bg" class="hasTip" title="Popup Button Hover Background Color"><?php echo 'Popup Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_button_hover_bg]" type="text" class="sc_color" id="paramscube_full_popup_button_hover_bg" value="<?php
							echo isset($param_values['full_popup_button_hover_bg'])?$param_values['full_popup_button_hover_bg']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_button_color-lbl" for="paramscube_full_popup_button_color" class="hasTip" title="Popup Button Link Color"><?php echo 'Popup Button Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_button_color]" type="text" class="sc_color" id="paramscube_full_popup_button_color" value="<?php
							echo isset($param_values['full_popup_button_color'])?$param_values['full_popup_button_color']:'#B3B3B3'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_button_hover_color-lbl" for="paramscube_full_popup_button_hover_color" class="hasTip" title="Popup Button Hover Link Color"><?php echo 'Popup Button Hover Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_button_hover_color]" type="text" class="sc_color" id="paramscube_full_popup_button_hover_color" value="<?php
							echo isset($param_values['full_popup_button_hover_color'])?$param_values['full_popup_button_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'social icons'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><?php echo 'Popup Social Icons View';?></span></td>
					<td class="paramlist_value">
						<?php $checkFb = ""; $checkIs= ""; $checkTw = ""; $checkGp = ""; ?>
						<p><input type="checkbox" name="params[full_popup_social_icons][]" id="params_full_popup_social_icons0" value="0"
						<?php checked(isset($param_values['full_popup_social_icons'])  && in_array(0,$param_values['full_popup_social_icons'])); ?>
						<?php echo isset($checkFb)?$checkFb:'';  ?> />
							<label for="params_full_popup_social_icons0"><?php echo 'Facebook';?></label></p>

						<p><input type="checkbox" name="params[full_popup_social_icons][]" id="params_full_popup_social_icons1" value="1"
						<?php checked(isset($param_values['full_popup_social_icons'])  && in_array(1,$param_values['full_popup_social_icons'])); ?>
						<?php echo isset($checkIs)?$checkIs:'';  ?> />
							<label for="params_full_popup_social_icons1"><?php echo 'Instagram';?></label></p>

						<p><input type="checkbox" name="params[full_popup_social_icons][]" id="params_full_popup_social_icons2" value="2"
						<?php checked(isset($param_values['full_popup_social_icons'])  && in_array(2,$param_values['full_popup_social_icons'])); ?>
						<?php echo isset($checkTw)?$checkTw:'';  ?> />
							<label for="params_full_popup_social_icons2"><?php echo 'Twitter';?></label></p>

						<p><input type="checkbox" name="params[full_popup_social_icons][]" id="params_full_popup_social_icons3" value="3"
						<?php checked(isset($param_values['full_popup_social_icons'])  && in_array(3,$param_values['full_popup_social_icons'])); ?>
						<?php echo isset($checkGp)?$checkGp:'';  ?> />
							<label for="params_full_popup_social_icons3"><?php echo 'Google';?></label></p>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_social_bg_color-lbl" for="paramscube_full_popup_social_bg_color" class="hasTip" title="Popup Social Icons Background Color"><?php echo 'Popup Social Icons Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_social_bg_color]" type="text" class="sc_color" id="paramscube_full_popup_social_bg_color" value="<?php
							echo isset($param_values['full_popup_social_bg_color'])?$param_values['full_popup_social_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_social_hover_bg_color-lbl" for="paramscube_full_popup_social_hover_bg_color" class="hasTip" title="Popup Social Icons Hover Background Color"><?php echo 'Popup Social Icons Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_social_hover_bg_color]" type="text" class="sc_color" id="paramscube_full_popup_social_hover_bg_color" value="<?php
							echo isset($param_values['full_popup_social_hover_bg_color'])?$param_values['full_popup_social_hover_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_icons_color-lbl" for="paramscube_full_popup_icons_color" class="hasTip" title="Popup Social Icons Color"><?php echo 'Popup Social Icons Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_icons_color]" type="text" class="sc_color" id="paramscube_full_popup_icons_color" value="<?php
							echo isset($param_values['full_popup_icons_color'])?$param_values['full_popup_icons_color']:'#B3B3B3'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_full_popup_icons_hover_color-lbl" for="paramscube_full_popup_icons_hover_color" class="hasTip" title="Popup Social Icons Hover Color"><?php echo 'Popup Social Icons Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[full_popup_icons_hover_color]" type="text" class="sc_color" id="paramscube_full_popup_icons_hover_color" value="<?php
							echo isset($param_values['full_popup_icons_hover_color'])?$param_values['full_popup_icons_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
			</table>
		</div>
	</div>


	<!---------- TABLE SHOWCASE ---------->
    <div id="theme_style_table" class="adminform">
		<h1 class="staff_main_title" title="Click for close"><?php echo _e('Table View',$contLDomain); ?></h1>
        <div class="staff_view">
			<table  class="paramlist admintable" cellspacing="1">
				<tr> <td class="admin_title" colspan="2"><?php echo 'border'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_border_width-lbl" for="paramscube_border_width" class="hasTip" title="Border Width"><?php echo 'Border Width';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[table_border_width]" id="paramscube_table_border_width" value="<?php
						echo isset($param_values['table_border_width'])?$param_values['table_border_width']:'1'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<?php
					$check0 = ""; $check1 = "";
					$check2 = ""; $check3 = "";
					$check4 = ""; $check5 = "";
					$check6 = ""; $check7 = "";
					if (isset($param_values['table_border_style']) && $param_values['table_border_style'] == 'solid')
						$check0 = '  selected="selected"  ';
					if (isset($param_values['table_border_style']) && $param_values['table_border_style'] == 'double')
						$check1 = '  selected="selected"';
					if (isset($param_values['table_border_style']) && $param_values['table_border_style'] == 'dashed')
						$check2 = '  selected="selected"  ';
					if (isset($param_values['table_border_style']) && $param_values['table_border_style'] == 'dotted')
						$check3 = '  selected="selected"';
					if (isset($param_values['table_border_style']) && $param_values['table_border_style'] == 'groove')
						$check4 = '  selected="selected"  ';
					if (isset($param_values['table_border_style']) && $param_values['table_border_style'] == 'inset')
						$check5 = '  selected="selected"';
					if (isset($param_values['table_border_style']) && $param_values['table_border_style'] == 'outset')
						$check6 = '  selected="selected"  ';
					if (isset($param_values['table_border_style']) && $param_values['table_border_style'] == 'ridge')
						$check7 = '  selected="selected"'; ?>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_border_style-lbl" for="paramscube_table_border_style" class="hasTip" title="Border Style"><?php echo 'Border Style';?></label></span></td>
					<td class="paramlist_value">
						<select name="params[table_border_style]" id="paramscube_table_border_style" >
							<option value="solid"  <?php echo isset($check0)?$check0:''; ?> > solid </option>
							<option value="double" <?php echo isset($check1)?$check1:''; ?> > double </option>
							<option value="dashed" <?php echo isset($check2)?$check2:''; ?> > dashed </option>
							<option value="dotted" <?php echo isset($check3)?$check3:''; ?> > dotted </option>
							<option value="groove" <?php echo isset($check4)?$check4:''; ?> > groove </option>
							<option value="inset"  <?php echo isset($check5)?$check5:''; ?> > inset </option>
							<option value="outset" <?php echo isset($check6)?$check6:''; ?> > outset </option>
							<option value="ridge"  <?php echo isset($check7)?$check7:''; ?> > ridge </option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_border_color-lbl" for="paramscube_table_border_color"  class="hasTip" title="Border Color"><?php echo 'Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_border_color]" type="text" class="sc_color" id="paramscube_table_border_color" value="<?php
							echo isset($param_values['table_border_color'])?$param_values['table_border_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'table head'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_head_bg_color-lbl" for="paramscube_table_head_bg_color"  class="hasTip" title="Table Head Background Color"><?php echo 'Table Head Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_head_bg_color]" type="text" class="sc_color" id="paramscube_table_head_bg_color" value="<?php
							echo isset($param_values['table_head_bg_color'])?$param_values['table_head_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_head_color-lbl" for="paramscube_table_head_color" class="hasTip" title="Table Head Text Color"><?php echo 'Table Head Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_head_color]" type="text" class="sc_color" id="paramscube_table_head_color" value="<?php
							echo isset($param_values['table_head_color'])?$param_values['table_head_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_head_text_size-lbl" for="paramscube_table_head_text_size" class="hasTip" title="Table Head Text Size"><?php echo 'Table Head Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[table_head_text_size]" id="paramscube_table_head_text_size" value="<?php
						echo isset($param_values['table_head_text_size'])?$param_values['table_head_text_size']:'19'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_title_color-lbl" for="paramscube_table_title_color" class="hasTip" title="Title Color"><?php echo 'Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_title_color]" type="text" class="sc_color" id="paramscube_table_title_color" value="<?php
							echo isset($param_values['table_title_color'])?$param_values['table_title_color']:'#000000';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_title_size-lbl" for="paramscube_table_title_size" class="hasTip" title="Title Size"><?php echo 'Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[table_title_size]" id="paramscube_table_title_size" value="<?php
						echo isset($param_values['table_title_size'])?$param_values['table_title_size']:'17'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_text_color-lbl" for="paramscube_table_text_color" class="hasTip" title="Text Color"><?php echo 'Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_text_color]" type="text" class="sc_color" id="paramscube_table_text_color" value="<?php
							echo isset($param_values['table_text_color'])?$param_values['table_text_color']:'#000000';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_text_size-lbl" for="paramscube_table_text_size" class="hasTip" title="Text Size"><?php echo 'Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[table_text_size]" id="paramscube_table_text_size" value="<?php
						echo isset($param_values['table_text_size'])?$param_values['table_text_size']:'14'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_links_color-lbl" for="paramscube_table_links_color" class="hasTip" title="Link Color"><?php echo 'Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_links_color]" type="text" class="sc_color" id="paramscube_table_links_color" value="<?php
							echo isset($param_values['table_links_color'])?$param_values['table_links_color']:'#000000';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_links_hover_color-lbl" for="paramscube_table_links_hover_color" class="hasTip" title="Link Hover Color"><?php echo 'Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_links_hover_color]" type="text" class="sc_color" id="paramscube_table_links_hover_color" value="<?php
							echo isset($param_values['table_links_hover_color'])?$param_values['table_links_hover_color']:'#000000';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'search'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_search_border-lbl" for="paramscube_table_search_border" class="hasTip" title="Search Border Color"><?php echo 'Search Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_search_border]" type="text" class="sc_color" id="paramscube_table_search_border" value="<?php
							echo isset($param_values['table_search_border'])?$param_values['table_search_border']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_search_color-lbl" for="paramscube_table_search_color" class="hasTip" title="Search Text Color"><?php echo 'Search Text Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_search_color]" type="text" class="sc_color" id="paramscube_table_search_color" value="<?php
							echo isset($param_values['table_search_color'])?$param_values['table_search_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'pagination'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_pagination_font-lbl" for="paramscube_table_pagination_font" class="hasTip" title="Pagination Font Size"><?php echo 'Pagination Font Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[table_pagination_font]" id="paramscube_table_pagination_font" value="<?php
						echo isset($param_values['table_pagination_font'])?$param_values['table_pagination_font']:'16'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_pagination_font_color-lbl" for="paramscube_table_pagination_font_color" class="hasTip" title="Pagination Text Color"><?php echo 'Pagination Text Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_pagination_font_color]" type="text" class="sc_color" id="paramscube_table_pagination_font_color" value="<?php
							echo isset($param_values['table_pagination_font_color'])?$param_values['table_pagination_font_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_pagination_bg-lbl" for="paramscube_table_pagination_bg" class="hasTip" title="Pagination Background Color"><?php echo 'Pagination Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_pagination_bg]" type="text" class="sc_color" id="paramscube_table_pagination_bg" value="<?php
							echo isset($param_values['table_pagination_bg'])?$param_values['table_pagination_bg']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_active_pagination_bg-lbl" for="paramscube_table_active_pagination_bg" class="hasTip" title="Pagination Active Page Background Color"><?php echo 'Pagination Active Page Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_active_pagination_bg]" type="text" class="sc_color" id="paramscube_table_active_pagination_bg" value="<?php
							echo isset($param_values['table_active_pagination_bg'])?$param_values['table_active_pagination_bg']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_pagination_border_color-lbl" for="paramscube_table_pagination_border_color" class="hasTip" title="Pagination Border Color"><?php echo 'Pagination Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_pagination_border_color]" type="text" class="sc_color" id="paramscube_table_pagination_border_color" value="<?php
							echo isset($param_values['table_pagination_border_color'])?$param_values['table_pagination_border_color']:'#DADADA';  ?>" size="10" />
						</label>
					</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_cont_count_in_page-lbl" for="paramscube_table_cont_count_in_page" class="hasTip" title="Count of Contacts in the Page"><?php echo 'Count of Contacts in the Page';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[table_cont_count_in_page]" id="paramscube_table_cont_count_in_page" value="<?php
						echo isset($param_values['table_cont_count_in_page'])?$param_values['table_cont_count_in_page']:'4'; ?>" class="text_area" size="3" /></td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_background_color-lbl" for="paramscube_table_background_color"  class="hasTip" title="Table Background Color"><?php echo 'Table Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_background_color]" type="text" class="sc_color" id="paramscube_table_background_color" value="<?php
							echo isset($param_values['table_background_color'])?$param_values['table_background_color']:'#FAFAFA'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_row_hover_bg_color-lbl" for="paramscube_table_row_hover_bg_color"  class="hasTip" title="Row Hover Background Color"><?php echo 'Row Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[table_row_hover_bg_color]" type="text" class="sc_color" id="paramscube_table_row_hover_bg_color" value="<?php
							echo isset($param_values['table_row_hover_bg_color'])?$param_values['table_row_hover_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_table_no_image-lbl" for="paramscube_table_no_image" class="hasTip" title="No Image"><?php echo 'No Image';?></label></span></td>
					<td class="paramlist_value">
						<input type="text" id="paramscube_table_no_image"  class="upload" name="params[table_no_image]" value="<?php echo isset($param_values['table_no_image'])?$param_values['table_no_image']:''; ?>" />
						<input class="upload-button sc_upload-button" type="button" value="<?php _e('Upload Image', $contLDomain); ?>"/>
					</td>
				</tr>
				<tr>
					<?php
					$check_circle0 = "checked='checked'";
					$check_circle1 = "";
					if (isset($param_values['table_image_circle']) && $param_values['table_image_circle'] == 1) { $check_circle1 = ' checked="checked"'; $check_circle0 = '';} ?>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramsviewcontact_radius" for="paramsviewcontact_radius"><?php echo ' Circle Images';?></label></span></td>
					<td class="">
						<input type="radio" name="params[table_image_circle]" id="paramsviewtablecontact_radius1" value="1" <?php echo isset($check_circle1)?$check_circle1:'';  ?>/>
							<label for="paramsviewtablecontact_radius1"><?php echo 'Yes';?></label>
						<input type="radio" name="params[table_image_circle]" id="paramsviewtablecontact_radius0" value="0" <?php echo isset($check_circle0)?$check_circle0:'';  ?>/>
							<label for="paramsviewtablecontact_radius0"><?php echo 'No';?></label>
					</td>
				</tr>
				<!--== 1.Image ==-->
				<tr>
					<?php
					$check_hide_img0 = "checked='checked'";
					$check_hide_img1 = "";
					if (isset($param_values['table_image_hide']) && $param_values['table_image_hide'] == 1) { $check_hide_img1 = 'checked="checked"'; $check_hide_img0 = '';} ?>
					<td class="paramlist_key"><span class="editlinktip"><label><?php echo '1. Show Image';?></label></span></td>
					<td class="">
						<input type="radio" name="params[table_image_hide]" id="params_img_hide0" value="0" <?php echo isset($check_hide_img0)?$check_hide_img0:'';  ?>/>
							<label for="params_img_hide0"><?php echo 'Yes';?></label>
						<input type="radio" name="params[table_image_hide]" id="params_img_hide1" value="1" <?php echo isset($check_hide_img1)?$check_hide_img1:'';  ?>/>
							<label for="params_img_hide1"><?php echo 'No';?></label>
					</td>
				</tr>
				<!--== 2.Name ==-->
				<tr>
					<?php
					$check_hide_name0 = "checked='checked'";
					$check_hide_name1 = "";
					if (isset($param_values['table_name_hide']) && $param_values['table_name_hide'] == 1) { $check_hide_name1 = 'checked="checked"'; $check_hide_name0 = '';} ?>
					<td class="paramlist_key"><span class="editlinktip"><label><?php echo '2. Show Name';?></label></span></td>
					<td class="">
						<input type="radio" name="params[table_name_hide]" id="params_name_hide0" value="0" <?php echo isset($check_hide_name0)?$check_hide_name0:'';  ?>/>
							<label for="params_name_hide0"><?php echo 'Yes';?></label>
						<input type="radio" name="params[table_name_hide]" id="params_name_hide1" value="1" <?php echo isset($check_hide_name1)?$check_hide_name1:'';  ?>/>
							<label for="params_name_hide1"><?php echo 'No';?></label>
					</td>
				</tr>
				<!--== 3.Category ==-->
				<tr>
					<?php
					$check_hide_cat0 = "checked='checked'";
					$check_hide_cat1 = "";
					if (isset($param_values['table_categ_hide']) && $param_values['table_categ_hide'] == 1) { $check_hide_cat1 = 'checked="checked"'; $check_hide_cat0 = '';} ?>
					<td class="paramlist_key"><span class="editlinktip"><label><?php echo '3. Show Category';?></label></span></td>
					<td class="">
						<input type="radio" name="params[table_categ_hide]" id="params_cat_hide0" value="0" <?php echo isset($check_hide_cat0)?$check_hide_cat0:'';  ?>/>
							<label for="params_cat_hide0"><?php echo 'Yes';?></label>
						<input type="radio" name="params[table_categ_hide]" id="params_cat_hide1" value="1" <?php echo isset($check_hide_cat1)?$check_hide_cat1:'';  ?>/>
							<label for="params_cat_hide1"><?php echo 'No';?></label>
					</td>
				</tr>
				<!--== 4.Email ==-->
				<tr>
					<?php
					$check_hide_email0 = "checked='checked'";
					$check_hide_email1 = "";
					if (isset($param_values['table_email_hide']) && $param_values['table_email_hide'] == 1) { $check_hide_email1 = 'checked="checked"'; $check_hide_email0 = '';} ?>
					<td class="paramlist_key"><span class="editlinktip"><label><?php echo '4. Show Email';?></label></span></td>
					<td class="">
						<input type="radio" name="params[table_email_hide]" id="params_email_hide0" value="0" <?php echo isset($check_hide_email0)?$check_hide_email0:'';  ?>/>
							<label for="params_email_hide0"><?php echo 'Yes';?></label>
						<input type="radio" name="params[table_email_hide]" id="params_email_hide1" value="1" <?php echo isset($check_hide_email1)?$check_hide_email1:'';  ?>/>
							<label for="params_email_hide1"><?php echo 'No';?></label>
					</td>
				</tr>
				<!--== 5.Parameters ==-->
				<tr>
					<?php
					$check_hide_param0 = "checked='checked'";
					$check_hide_param1 = "";
					if (isset($param_values['table_param_hide']) && $param_values['table_param_hide'] == 1) { $check_hide_param1 = 'checked="checked"'; $check_hide_param0 = '';} ?>
					<td class="paramlist_key"><span class="editlinktip"><label><?php echo '5. Show Parameters';?></label></span></td>
					<td class="">
						<input type="radio" name="params[table_param_hide]" id="params_par_hide0" value="0" <?php echo isset($check_hide_param0)?$check_hide_param0:'';  ?>/>
							<label for="params_par_hide0"><?php echo 'Yes';?></label>
						<input type="radio" name="params[table_param_hide]" id="params_par_hide1" value="1" <?php echo isset($check_hide_param1)?$check_hide_param1:'';  ?>/>
							<label for="params_par_hide1"><?php echo 'No';?></label>
					</td>
				</tr>
			</table>
		</div>
	</div>


	<!---------- CHESS SHOWCASE ---------->
    <div id="theme_style_chess" class="adminform">
		<h1 class="staff_main_title" title="Click for close"><?php echo _e('Chess View',$contLDomain); ?></h1>
		<div class="staff_view">
			<table  class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'border'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_border_width-lbl" for="paramscube_chess_border_width" class="hasTip" title="Border Width"><?php echo 'Border Width';?></label></span>
					</td>
					<td class="paramlist_value"><input type="text" name="params[chess_border_width]" id="paramscube_chess_border_width" value="<?php
						echo isset($param_values['chess_border_width'])?$param_values['chess_border_width']:'1'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<?php
					$check0 = ""; $check1 = ""; $check2 = ""; $check3 = "";
					$check4 = ""; $check5 = ""; $check6 = ""; $check7 = "";
					if (isset($param_values['chess_border_style']) && $param_values['chess_border_style'] == 'solid')
						$check0 = '  selected="selected"  ';
					if (isset($param_values['chess_border_style']) && $param_values['chess_border_style'] == 'double')
						$check1 = '  selected="selected"';
					if (isset($param_values['chess_border_style']) && $param_values['chess_border_style'] == 'dashed')
						$check2 = '  selected="selected"  ';
					if (isset($param_values['chess_border_style']) && $param_values['chess_border_style'] == 'dotted')
						$check3 = '  selected="selected"';
					if (isset($param_values['chess_border_style']) && $param_values['chess_border_style'] == 'groove')
						$check4 = '  selected="selected"  ';
					if (isset($param_values['chess_border_style']) && $param_values['chess_border_style'] == 'inset')
						$check5 = '  selected="selected"';
					if (isset($param_values['chess_border_style']) && $param_values['chess_border_style'] == 'outset')
						$check6 = '  selected="selected"  ';
					if (isset($param_values['chess_border_style']) && $param_values['chess_border_style'] == 'ridge')
						$check7 = '  selected="selected"'; ?>

					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_border_style-lbl" for="paramscube_chess_border_style" class="hasTip" title="Border Style"><?php echo 'Border Style';?></label></span></td>
					<td class="paramlist_value">
						<select name="params[chess_border_style]" id="paramscube_chess_border_style" >
							<option value="solid"  <?php echo isset($check0)?$check0:''; ?> > solid </option>
							<option value="double" <?php echo isset($check1)?$check1:''; ?> > double </option>
							<option value="dashed" <?php echo isset($check2)?$check2:''; ?> > dashed </option>
							<option value="dotted" <?php echo isset($check3)?$check3:''; ?> > dotted </option>
							<option value="groove" <?php echo isset($check4)?$check4:''; ?> > groove </option>
							<option value="inset"  <?php echo isset($check5)?$check5:''; ?> > inset </option>
							<option value="outset" <?php echo isset($check6)?$check6:''; ?> > outset </option>
							<option value="ridge"  <?php echo isset($check7)?$check7:''; ?> > ridge </option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_border_color-lbl" for="paramscube_chess_border_color" class="hasTip" title="Border Color"><?php echo 'Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_border_color]" type="text" class="sc_color" id="paramscube_chess_border_color" value="<?php
							echo isset($param_values['chess_border_color'])?$param_values['chess_border_color']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_title_color-lbl" for="paramscube_chess_title_color" class="hasTip" title="Title Color"><?php echo 'Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_title_color]" type="text" class="sc_color" id="paramscube_chess_title_color" value="<?php
							echo isset($param_values['chess_title_color'])?$param_values['chess_title_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_title_size-lbl" for="paramscube_chess_title_size" class="hasTip" title="Title Size"><?php echo 'Title Font Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[chess_title_size]" id="paramscube_chess_title_size" value="<?php
						echo isset($param_values['chess_title_size'])?$param_values['chess_title_size']:'20'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_text_color-lbl" for="paramscube_chess_text_color" class="hasTip" title="Text Color"><?php echo 'Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_text_color]" type="text" class="sc_color" id="paramscube_chess_text_color" value="<?php
							echo isset($param_values['chess_text_color'])?$param_values['chess_text_color']:'#949494'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_text_size-lbl" for="paramscube_chess_text_size" class="hasTip" title="Text Size"><?php echo 'Text Font Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[chess_text_size]" id="paramscube_chess_text_size" value="<?php
						echo isset($param_values['chess_text_size'])?$param_values['chess_text_size']:'15'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_links_color-lbl" for="paramscube_chess_links_color" class="hasTip" title="Links Color"><?php echo 'Links Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_links_color]" type="text" class="sc_color" id="paramscube_chess_links_color" value="<?php
							echo isset($param_values['chess_links_color'])?$param_values['chess_links_color']:'#D9D9D9';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_links_hover_color-lbl" for="paramscube_chess_links_hover_color" class="hasTip" title="Links Hover Color"><?php echo 'Links Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_links_hover_color]" type="text" class="sc_color" id="paramscube_chess_links_hover_color" value="<?php
							echo isset($param_values['chess_links_hover_color'])?$param_values['chess_links_hover_color']:'#00A99D';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_button_bg-lbl" for="paramscube_chess_button_bg" class="hasTip" title="Button Background Color"><?php echo 'Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_button_bg]" type="text" class="sc_color" id="paramscube_chess_button_bg" value="<?php
							echo isset($param_values['chess_button_bg'])?$param_values['chess_button_bg']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_button_hover_bg-lbl" for="paramscube_chess_button_hover_bg" class="hasTip" title="Button Hover Background Color"><?php echo 'Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_button_hover_bg]" type="text" class="sc_color" id="paramscube_chess_button_hover_bg" value="<?php
							echo isset($param_values['chess_button_hover_bg'])?$param_values['chess_button_hover_bg']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_button_color-lbl" for="paramscube_chess_button_color" class="hasTip" title="Button Link Color"><?php echo 'Button Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_button_color]" type="text" class="sc_color" id="paramscube_chess_button_color" value="<?php
							echo isset($param_values['chess_button_color'])?$param_values['chess_button_color']:'#B3B3B3'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_button_hover_color-lbl" for="paramscube_chess_button_hover_color" class="hasTip" title="Button Hover Link Color"><?php echo 'Button Hover Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_button_hover_color]" type="text" class="sc_color" id="paramscube_chess_button_hover_color" value="<?php
							echo isset($param_values['chess_button_hover_color'])?$param_values['chess_button_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'social icons'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><?php echo 'Social Icons View';?></span></td>
					<td class="paramlist_value">
						<?php $checkFb = ""; $checkIs= ""; $checkTw = ""; $checkGp = ""; ?>
						<p><input type="checkbox" name="params[chess_social_icons][]" id="params_chess_social_icons0" value="0"
						<?php checked(isset($param_values['chess_social_icons'])  && in_array(0,$param_values['chess_social_icons'])); ?>
						<?php echo isset($checkFb)?$checkFb:'';  ?> />
							<label for="params_chess_social_icons0" class="staff_soc_labels"><?php echo 'Facebook';?></label>
							<input type="text" name="params[chess_social_fb]" value="<?php echo isset($param_values['chess_social_fb'])?$param_values['chess_social_fb']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[chess_social_icons][]" id="params_chess_social_icons1" value="1"
						<?php checked(isset($param_values['chess_social_icons'])  && in_array(1,$param_values['chess_social_icons'])); ?>
						<?php echo isset($checkIs)?$checkIs:'';  ?> />
							<label for="params_chess_social_icons1" class="staff_soc_labels"><?php echo 'Instagram';?></label>
							<input type="text" name="params[chess_social_ins]" value="<?php echo isset($param_values['chess_social_ins'])?$param_values['chess_social_ins']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[chess_social_icons][]" id="params_chess_social_icons2" value="2"
						<?php checked(isset($param_values['chess_social_icons'])  && in_array(2,$param_values['chess_social_icons'])); ?>
						<?php echo isset($checkTw)?$checkTw:'';  ?> />
							<label for="params_chess_social_icons2" class="staff_soc_labels"><?php echo 'Twitter';?></label>
							<input type="text" name="params[chess_social_tw]" value="<?php echo isset($param_values['chess_social_tw'])?$param_values['chess_social_tw']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[chess_social_icons][]" id="params_chess_social_icons3" value="3"
						<?php checked(isset($param_values['chess_social_icons'])  && in_array(3,$param_values['chess_social_icons'])); ?>
						<?php echo isset($checkGp)?$checkGp:'';  ?> />
							<label for="params_chess_social_icons3" class="staff_soc_labels"><?php echo 'Google';?></label>
							<input type="text" name="params[chess_social_gp]" value="<?php echo isset($param_values['chess_social_gp'])?$param_values['chess_social_gp']:''; ?>" placeholder="Write some link" /></p>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_social_bg_color-lbl" for="paramscube_chess_social_bg_color" class="hasTip" title="Social Icons Background Color"><?php echo 'Social Icons Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_social_bg_color]" type="text" class="sc_color" id="paramscube_chess_social_bg_color" value="<?php
							echo isset($param_values['chess_social_bg_color'])?$param_values['chess_social_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_social_hover_bg_color-lbl" for="paramscube_chess_social_hover_bg_color" class="hasTip" title="Social Icons Hover Background Color"><?php echo 'Social Icons Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_social_hover_bg_color]" type="text" class="sc_color" id="paramscube_chess_social_hover_bg_color" value="<?php
							echo isset($param_values['chess_social_hover_bg_color'])?$param_values['chess_social_hover_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_icons_color-lbl" for="paramscube_chess_icons_color" class="hasTip" title="Social Icons Color"><?php echo 'Social Icons Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_icons_color]" type="text" class="sc_color" id="paramscube_chess_icons_color" value="<?php
							echo isset($param_values['chess_icons_color'])?$param_values['chess_icons_color']:'#B5B5B5'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_icons_hover_color-lbl" for="paramscube_chess_icons_hover_color" class="hasTip" title="Social Icons Hover Color"><?php echo 'Social Icons Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_icons_hover_color]" type="text" class="sc_color" id="paramscube_chess_icons_hover_color" value="<?php
							echo isset($param_values['chess_icons_hover_color'])?$param_values['chess_icons_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'search'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_search_border-lbl" for="paramscube_chess_search_border" class="hasTip" title="Search Border Color"><?php echo 'Search Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_search_border]" type="text" class="sc_color" id="paramscube_chess_search_border" value="<?php
							echo isset($param_values['chess_search_border'])?$param_values['chess_search_border']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_search_color-lbl" for="paramscube_chess_search_color" class="hasTip" title="Search Text Color"><?php echo 'Search Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_search_color]" type="text" class="sc_color" id="paramscube_chess_search_color" value="<?php
							echo isset($param_values['chess_search_color'])?$param_values['chess_search_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'pagination'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_pagination_font-lbl" for="paramscube_chess_pagination_font" class="hasTip" title="Pagination Font Size"><?php echo 'Pagination Font Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[chess_pagination_font]" id="paramscube_chess_pagination_font" value="<?php
						echo isset($param_values['chess_pagination_font'])?$param_values['chess_pagination_font']:'16'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_pagination_font_color-lbl" for="paramscube_chess_pagination_font_color" class="hasTip" title="Pagination Text Color"><?php echo 'Pagination Text Color' ;?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_pagination_font_color]" type="text" class="sc_color" id="paramscube_chess_pagination_font_color" value="<?php
							echo isset($param_values['chess_pagination_font_color'])?$param_values['chess_pagination_font_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_pagination_bg-lbl" for="paramscube_chess_pagination_bg" class="hasTip" title="Pagination Background Color"><?php echo 'Pagination Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_pagination_bg]" type="text" class="sc_color" id="paramscube_chess_pagination_bg" value="<?php
							echo isset($param_values['chess_pagination_bg'])?$param_values['chess_pagination_bg']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_active_pagination_bg-lbl" for="paramscube_chess_active_pagination_bg" class="hasTip" title="Pagination Active Page Background Color"><?php echo 'Pagination Active Page Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_active_pagination_bg]" type="text" class="sc_color" id="paramscube_chess_active_pagination_bg" value="<?php
							echo isset($param_values['chess_active_pagination_bg'])?$param_values['chess_active_pagination_bg']:'#00A99D';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_pagination_border_color-lbl" for="paramscube_chess_pagination_border_color" class="hasTip" title="Pagination Border Color"><?php echo 'Pagination Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_pagination_border_color]" type="text" class="sc_color" id="paramscube_chess_pagination_border_color" value="<?php
							echo isset($param_values['chess_pagination_border_color'])?$param_values['chess_pagination_border_color']:'#DADADA';  ?>" size="10" />
						</label>
					</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_cont_count_in_page-lbl" for="paramscube_chess_cont_count_in_page" class="hasTip" title="Count of Contacts in the Page"><?php echo 'Count of Contacts in the Page';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[chess_cont_count_in_page]" id="paramscube_chess_cont_count_in_page" value="<?php
						echo isset($param_values['chess_cont_count_in_page'])?$param_values['chess_cont_count_in_page']:'4'; ?>" class="text_area" size="3" /></td>
				</tr>
				<tr>
					<?php
					$width_check0 = "checked='checked' ";
					$width_check1 = "";
					if (isset($param_values['chess_contact_width']) && $param_values['chess_contact_width'] == 1) { $width_check1 = ' checked="checked" '; $width_check0 = '';} ?>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo 'Contact Full Width';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="radio" name="params[chess_contact_width]" id="params_chess_icon_radius0" value="0" <?php echo isset($width_check0)?$width_check0:''; ?>/>
							<label for="params_chess_icon_radius0"><?php echo 'Yes';?></label>
						<input type="radio" name="params[chess_contact_width]" id="params_chess_icon_radius1" value="1" <?php echo isset($width_check1)?$width_check1:''; ?>/>
							<label for="params_chess_icon_radius1"><?php echo 'No';?></label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_contact_height-lbl" for="paramscube_chess_contact_height" class="hasTip" title="Contact Height"><?php echo 'Contact Height';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[chess_contact_height]" id="paramscube_chess_contact_height" value="<?php
						echo isset($param_values['chess_contact_height'])?$param_values['chess_contact_height']:'300'; ?>" class="text_area" size="4" />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_background_color-lbl" for="paramscube_chess_background_color" class="hasTip" title="Background Color"><?php echo 'Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_background_color]" type="text" class="sc_color" id="paramscube_chess_background_color" value="<?php
							echo isset($param_values['chess_background_color'])?$param_values['chess_background_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_hover_bg_color-lbl" for="paramscube_chess_hover_bg_color" class="hasTip" title="Image Hover Background Color"><?php echo 'Image Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_hover_bg_color]" type="text" class="sc_color" id="paramscube_chess_hover_bg_color" value="<?php
							echo isset($param_values['chess_hover_bg_color'])?$param_values['chess_hover_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_no_image-lbl" for="paramscube_chess_no_image" class="hasTip" title="No Image"><?php echo 'No Image';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="text" id="paramscube_chess_no_image"  class="upload" name="params[chess_no_image]" value="<?php echo isset($param_values['chess_no_image'])?$param_values['chess_no_image']:''; ?>" />
						<input class="upload-button sc_upload-button" type="button" value="<?php _e('Upload Image', $contLDomain); ?>"/>
					</td>
				</tr>
			</table>
		</div>

		<h1 class="staff_main_title_popup" title="Click for close"><?php echo _e('Chess View Popup',$contLDomain); ?></h1>
		<div class="staff_view_popup">
			<table class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_bg_color-lbl" for="paramscube_chess_popup_bg_color"  class="hasTip" title="Popup Background Color"><?php echo 'Popup Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_bg_color]" type="text" class="sc_color" id="paramscube_chess_popup_bg_color" value="<?php
							echo isset($param_values['chess_popup_bg_color'])?$param_values['chess_popup_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_content_bg_color-lbl" for="paramscube_chess_popup_content_bg_color"  class="hasTip" title="Popup Content Background Color"><?php echo 'Popup Content Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_content_bg_color]" type="text" class="sc_color" id="paramscube_chess_popup_content_bg_color" value="<?php
							echo isset($param_values['chess_popup_content_bg_color'])?$param_values['chess_popup_content_bg_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_title_color-lbl" for="paramscube_chess_popup_title_color" class="hasTip" title="Title Color"><?php echo 'Popup Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_title_color]" type="text" class="sc_color" id="paramscube_chess_popup_title_color" value="<?php
							echo isset($param_values['chess_popup_title_color'])?$param_values['chess_popup_title_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_title_size-lbl" for="paramscube_chess_popup_title_size" class="hasTip" title="Popup Title Size"><?php echo 'Popup Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[chess_popup_title_size]" id="paramscube_chess_popup_title_size" value="<?php
						echo isset($param_values['chess_popup_title_size'])?$param_values['chess_popup_title_size']:'30'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_text_color-lbl" for="paramscube_chess_popup_text_color" class="hasTip" title="Text Color"><?php echo 'Popup Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_text_color]" type="text" class="sc_color" id="paramscube_chess_popup_text_color" value="<?php
							echo isset($param_values['chess_popup_text_color'])?$param_values['chess_popup_text_color']:'#949494'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_text_size-lbl" for="paramscube_chess_popup_text_size" class="hasTip" title="Popup Text Size"><?php echo 'Popup Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[chess_popup_text_size]" id="paramscube_chess_popup_text_size" value="<?php
						echo isset($param_values['chess_popup_text_size'])?$param_values['chess_popup_text_size']:'15'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_links_color-lbl" for="paramscube_chess_popup_links_color" class="hasTip" title="Links Color"><?php echo 'Popup Links Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_links_color]" type="text" class="sc_color" id="paramscube_chess_popup_links_color" value="<?php
							echo isset($param_values['chess_popup_links_color'])?$param_values['chess_popup_links_color']:'#D9D9D9';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_links_hover_color-lbl" for="paramscube_chess_popup_links_hover_color" class="hasTip" title="Links Hover Color"><?php echo 'Popup Links Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_links_hover_color]" type="text" class="sc_color" id="paramscube_chess_popup_links_hover_color" value="<?php
							echo isset($param_values['chess_popup_links_hover_color'])?$param_values['chess_popup_links_hover_color']:'#00A99D';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'close button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popoup_close_color-lbl" for="paramscube_chess_popoup_close_color" class="hasTip" title="Popup Close Button Color"><?php echo 'Popup Close Button Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popoup_close_color]" type="text" class="sc_color" id="paramscube_chess_popoup_close_color" value="<?php
							echo isset($param_values['chess_popoup_close_color'])?$param_values['chess_popoup_close_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_button_bg-lbl" for="paramscube_chess_popup_button_bg" class="hasTip" title="Popup Button Background Color"><?php echo 'Popup Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_button_bg]" type="text" class="sc_color" id="paramscube_chess_popup_button_bg" value="<?php
							echo isset($param_values['chess_popup_button_bg'])?$param_values['chess_popup_button_bg']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_button_hover_bg-lbl" for="paramscube_chess_popup_button_hover_bg" class="hasTip" title="Popup Button Hover Background Color"><?php echo 'Popup Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_button_hover_bg]" type="text" class="sc_color" id="paramscube_chess_popup_button_hover_bg" value="<?php
							echo isset($param_values['chess_popup_button_hover_bg'])?$param_values['chess_popup_button_hover_bg']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_button_color-lbl" for="paramscube_chess_popup_button_color" class="hasTip" title="Popup Button Link Color"><?php echo 'Popup Button Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_button_color]" type="text" class="sc_color" id="paramscube_chess_popup_button_color" value="<?php
							echo isset($param_values['chess_popup_button_color'])?$param_values['chess_popup_button_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_button_hover_color-lbl" for="paramscube_chess_popup_button_hover_color" class="hasTip" title="Popup Button Hover Link Color"><?php echo 'Popup Button Hover Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_button_hover_color]" type="text" class="sc_color" id="paramscube_chess_popup_button_hover_color" value="<?php echo isset($param_values['chess_popup_button_hover_color'])?$param_values['chess_popup_button_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'social icons'; ?></td></tr>
				<tr>
					<td class="paramlist_key"> <span class="editlinktip"><?php echo 'Popup Social Icons View';?></span></td>
					<td class="paramlist_value">
						<?php $checkFb = ""; $checkIs= ""; $checkTw = ""; $checkGp = ""; ?>
						<p><input type="checkbox" name="params[chess_popup_social_icons][]" id="params_chess_popup_social_icons0" value="0"
						<?php checked(isset($param_values['chess_popup_social_icons'])  && in_array(0,$param_values['chess_popup_social_icons'])); ?>
						<?php echo isset($checkFb)?$checkFb:'';  ?> />
							<label for="params_chess_popup_social_icons0"><?php echo 'Facebook';?></label></p>

						<p><input type="checkbox" name="params[chess_popup_social_icons][]" id="params_chess_popup_social_icons1" value="1"
						<?php checked(isset($param_values['chess_popup_social_icons'])  && in_array(1,$param_values['chess_popup_social_icons'])); ?>
						<?php echo isset($checkIs)?$checkIs:'';  ?> />
							<label for="params_chess_popup_social_icons1"><?php echo 'Instagram';?></label></p>

						<p><input type="checkbox" name="params[chess_popup_social_icons][]" id="params_chess_popup_social_icons2" value="2"
						<?php checked(isset($param_values['chess_popup_social_icons'])  && in_array(2,$param_values['chess_popup_social_icons'])); ?>
						<?php echo isset($checkTw)?$checkTw:'';  ?> />
							<label for="params_chess_popup_social_icons2"><?php echo 'Twitter';?></label></p>

						<p><input type="checkbox" name="params[chess_popup_social_icons][]" id="params_chess_popup_social_icons3" value="3"
						<?php checked(isset($param_values['chess_popup_social_icons'])  && in_array(3,$param_values['chess_popup_social_icons'])); ?>
						<?php echo isset($checkGp)?$checkGp:'';  ?> />
							<label for="params_chess_popup_social_icons3"><?php echo 'Google';?></label></p>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_social_bg_color-lbl" for="paramscube_chess_popup_social_bg_color" class="hasTip" title="Popup Social Icons Background Color"><?php echo 'Popup Social Icons Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_social_bg_color]" type="text" class="sc_color" id="paramscube_chess_popup_social_bg_color" value="<?php
							echo isset($param_values['chess_popup_social_bg_color'])?$param_values['chess_popup_social_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_social_hover_bg_color-lbl" for="paramscube_chess_popup_social_hover_bg_color" class="hasTip" title="Popup Social Icons Hover Background Color"><?php echo 'Popup Social Icons Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_social_hover_bg_color]" type="text" class="sc_color" id="paramscube_chess_popup_social_hover_bg_color" value="<?php
							echo isset($param_values['chess_popup_social_hover_bg_color'])?$param_values['chess_popup_social_hover_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_icons_color-lbl" for="paramscube_chess_popup_icons_color" class="hasTip" title="Popup Social Icons Color"><?php echo 'Popup Social Icons Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_icons_color]" type="text" class="sc_color" id="paramscube_chess_popup_icons_color" value="<?php
							echo isset($param_values['chess_popup_icons_color'])?$param_values['chess_popup_icons_color']:'#B5B5B5'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_chess_popup_icons_hover_color-lbl" for="paramscube_chess_popup_icons_hover_color" class="hasTip" title="Popup Social Icons Hover Color"><?php echo 'Popup Social Icons Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[chess_popup_icons_hover_color]" type="text" class="sc_color" id="paramscube_chess_popup_icons_hover_color" value="<?php
							echo isset($param_values['chess_popup_icons_hover_color'])?$param_values['chess_popup_icons_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<?php
					$icon_check0 = " checked='checked' ";
					$icon_check1 = "";
					if (isset($param_values['chess_popup_icon_circle']) && $param_values['chess_popup_icon_circle'] == 1) { $icon_check1 = ' checked="checked" '; $icon_check0 = '';}?>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius" title="Popup View Icons Circle"><?php echo 'Popup View Icons Circle';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="radio" name="params[chess_popup_icon_circle]" id="params_chess_icon_radius0" value="0" <?php echo isset($icon_check0)?$icon_check0:''; ?>/>
							<label for="params_chess_icon_radius0"><?php echo 'No';?></label>
						<input type="radio" name="params[chess_popup_icon_circle]" id="params_chess_icon_radius1" value="1" <?php echo isset($icon_check1)?$icon_check1:''; ?>/>
							<label for="params_chess_icon_radius1"><?php echo 'Yes';?></label>
					</td>
				</tr>
			</table>
		</div>
	</div>


	<!-------- PORTFOLIO SHOWCASE -------->
    <div id="theme_style_port" class="adminform">
		<h1 class="staff_main_title" title="Click for close"><?php echo _e('Portfolio View',$contLDomain); ?></h1>
		<div class="staff_view">
			<table  class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'border'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_border_width-lbl" for="paramscube_port_border_width" class="hasTip" title="Border Width"><?php echo 'Border Width';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[port_border_width]" id="paramscube_port_border_width" value="<?php
						echo isset($param_values['port_border_width'])?$param_values['port_border_width']:'1'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<?php
					$check0 = ""; $check1 = ""; $check2 = ""; $check3 = "";
					$check4 = ""; $check5 = ""; $check6 = ""; $check7 = "";
					if (isset($param_values['port_border_style']) && $param_values['port_border_style'] == 'solid')
						$check0 = '  selected="selected"  ';
					if (isset($param_values['port_border_style']) && $param_values['port_border_style'] == 'double')
						$check1 = '  selected="selected"';
					if (isset($param_values['port_border_style']) && $param_values['port_border_style'] == 'dashed')
						$check2 = '  selected="selected"  ';
					if (isset($param_values['port_border_style']) && $param_values['port_border_style'] == 'dotted')
						$check3 = '  selected="selected"';
					if (isset($param_values['port_border_style']) && $param_values['port_border_style'] == 'groove')
						$check4 = '  selected="selected"  ';
					if (isset($param_values['port_border_style']) && $param_values['port_border_style'] == 'inset')
						$check5 = '  selected="selected"';
					if (isset($param_values['port_border_style']) && $param_values['port_border_style'] == 'outset')
						$check6 = '  selected="selected"  ';
					if (isset($param_values['port_border_style']) && $param_values['port_border_style'] == 'ridge')
						$check7 = '  selected="selected"'; ?>

					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_border_style-lbl" for="paramscube_port_border_style" class="hasTip" title="Border Style"><?php echo 'Border Style';?></label></span></td>
					<td class="paramlist_value">
						<select name="params[port_border_style]" id="paramscube_port_border_style" >
							<option value="solid"  <?php echo isset($check0)?$check0:''; ?> > solid </option>
							<option value="double" <?php echo isset($check1)?$check1:''; ?> > double </option>
							<option value="dashed" <?php echo isset($check2)?$check2:''; ?> > dashed </option>
							<option value="dotted" <?php echo isset($check3)?$check3:''; ?> > dotted </option>
							<option value="groove" <?php echo isset($check4)?$check4:''; ?> > groove </option>
							<option value="inset"  <?php echo isset($check5)?$check5:''; ?> > inset </option>
							<option value="outset" <?php echo isset($check6)?$check6:''; ?> > outset </option>
							<option value="ridge"  <?php echo isset($check7)?$check7:''; ?> > ridge </option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_border_color-lbl" for="paramscube_port_border_color" class="hasTip" title="Border Color"><?php echo 'Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_border_color]" type="text" class="sc_color" id="paramscube_port_border_color" value="<?php
							echo isset($param_values['port_border_color'])?$param_values['port_border_color']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_title_color-lbl" for="paramscube_port_title_color" class="hasTip" title="Title Color"><?php echo 'Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_title_color]" type="text" class="sc_color" id="paramscube_port_title_color" value="<?php
							echo isset($param_values['port_title_color'])?$param_values['port_title_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_title_size-lbl" for="paramscube_port_title_size" class="hasTip" title="Title Size"><?php echo 'Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[port_title_size]" id="paramscube_port_title_size" value="<?php
						echo isset($param_values['port_title_size'])?$param_values['port_title_size']:'20'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_text_color-lbl" for="paramscube_port_text_color" class="hasTip" title="Text Color"><?php echo 'Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_text_color]" type="text" class="sc_color" id="paramscube_port_text_color" value="<?php
							echo isset($param_values['port_text_color'])?$param_values['port_text_color']:'#F2F2F2'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_link_color-lbl" for="paramscube_port_link_color" class="hasTip" title="Link Color"><?php echo 'Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_link_color]" type="text" class="sc_color" id="paramscube_port_link_color" value="<?php
							echo isset($param_values['port_link_color'])?$param_values['port_link_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_link_hover_color-lbl" for="paramscube_port_link_hover_color" class="hasTip" title="Link Hover Color"><?php echo 'Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_link_hover_color]" type="text" class="sc_color" id="paramscube_port_link_hover_color" value="<?php
							echo isset($param_values['port_link_hover_color'])?$param_values['port_link_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'search'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_search_border-lbl" for="paramscube_port_search_border" class="hasTip" title="Search Border Color"><?php echo 'Search Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_search_border]" type="text" class="sc_color" id="paramscube_port_search_border" value="<?php
							echo isset($param_values['port_search_border'])?$param_values['port_search_border']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_search_color-lbl" for="paramscube_port_search_color" class="hasTip" title="Search Text Color"><?php echo 'Search Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_search_color]" type="text" class="sc_color" id="paramscube_port_search_color" value="<?php
							echo isset($param_values['port_search_color'])?$param_values['port_search_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'pagination'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_pagination_font-lbl" for="paramscube_port_pagination_font" class="hasTip" title="Pagination Font Size"><?php echo 'Pagination Font Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[port_pagination_font]" id="paramscube_port_pagination_font" value="<?php
						echo isset($param_values['port_pagination_font'])?$param_values['port_pagination_font']:'16'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_pagination_font_color-lbl" for="paramscube_port_pagination_font_color" class="hasTip" title="Pagination Text Color"><?php echo 'Pagination Text Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_pagination_font_color]" type="text" class="sc_color" id="paramscube_port_pagination_font_color" value="<?php
							echo isset($param_values['port_pagination_font_color'])?$param_values['port_pagination_font_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_pagination_bg-lbl" for="paramscube_port_pagination_bg" class="hasTip" title="Pagination Background Color"><?php echo 'Pagination Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_pagination_bg]" type="text" class="sc_color" id="paramscube_port_pagination_bg" value="<?php
							echo isset($param_values['port_pagination_bg'])?$param_values['port_pagination_bg']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_active_pagination_bg-lbl" for="paramscube_port_active_pagination_bg" class="hasTip" title="Pagination Active Page Background Color"><?php echo 'Pagination Active Page Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_active_pagination_bg]" type="text" class="sc_color" id="paramscube_port_active_pagination_bg" value="<?php
							echo isset($param_values['port_active_pagination_bg'])?$param_values['port_active_pagination_bg']:'#00A99D';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_pagination_border_color-lbl" for="paramscube_port_pagination_border_color" class="hasTip" title="Pagination Border Color"><?php echo 'Pagination Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_pagination_border_color]" type="text" class="sc_color" id="paramscube_port_pagination_border_color" value="<?php
							echo isset($param_values['port_pagination_border_color'])?$param_values['port_pagination_border_color']:'#DADADA';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_cont_count_in_page-lbl" for="paramscube_port_cont_count_in_page" class="hasTip" title="Count of Contacts in the Page"><?php echo 'Count of Contacts in the Page';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[port_cont_count_in_page]" id="paramscube_port_cont_count_in_page" value="<?php
						echo isset($param_values['port_cont_count_in_page'])?$param_values['port_cont_count_in_page']:'8'; ?>" class="text_area" size="3" /></td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_contact_width-lbl" for="paramscube_port_contact_width" class="hasTip" title="Contact Width"><?php echo 'Contact Width';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[port_contact_width]" id="paramscube_port_contact_width" value="<?php
						echo isset($param_values['port_contact_width'])?$param_values['port_contact_width']:'47'; ?>" class="text_area" size="4" />%</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_contact_height-lbl" for="paramscube_port_contact_height" class="hasTip" title="Contact Height"><?php echo 'Contact Height';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[port_contact_height]" id="paramscube_port_contact_height" value="<?php
						echo isset($param_values['port_contact_height'])?$param_values['port_contact_height']:'310'; ?>" class="text_area" size="4" />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_hover_bg_color-lbl" for="paramscube_port_hover_bg_color" class="hasTip" title="Hover Background Color"><?php echo 'Hover Background Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_hover_bg_color]" type="text" class="sc_color" id="paramscube_port_hover_bg_color" value="<?php
							echo isset($param_values['port_hover_bg_color'])?$param_values['port_hover_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_no_image-lbl" for="paramscube_port_no_image" class="hasTip" title="No Image"><?php echo 'No Image';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="text" id="paramscube_port_no_image"  class="upload" name="params[port_no_image]" value="<?php echo isset($param_values['port_no_image'])?$param_values['port_no_image']:''; ?>" />
						<input class="upload-button sc_upload-button" type="button" value="<?php _e('Upload Image', $contLDomain); ?>"/>
					</td>
				</tr>
			</table>
		</div>

		<h1 class="staff_main_title_popup" title="Click for close"><?php echo _e('Portfolio View Popup',$contLDomain); ?></h1>
		<div class="staff_view_popup">
			<table class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
				   <?php
					$popup_port_check0 = "";
					$popup_port_check1 = "";
					$popup_port_check2 = " checked='checked' ";
					if (isset($param_values['port_popup_position']) && $param_values['port_popup_position'] == '0') {
						$popup_port_check0 = " checked='checked' ";
						$popup_port_check1 = "";$popup_port_check2 = "";
					}
					if (isset($param_values['port_popup_position']) && $param_values['port_popup_position'] == '1') {
						$popup_port_check1 = " checked='checked' ";
						$popup_port_check0 = "";$popup_port_check2 = "";
					}
					if (isset($param_values['port_popup_position']) && $param_values['port_popup_position'] == '2') {
						$popup_port_check2 = " checked='checked' ";
						$popup_port_check0 = "";$popup_port_check1 = "";
					} ?>
					<td class="paramlist_key"><span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo 'Popup View Position';?></label></span></td>
					<td class="paramlist_value">
						<input type="radio" name="params[port_popup_position]" id="params_port_popup_position0" value="0"
						<?php echo isset($popup_port_check0)?$popup_port_check0:''; ?>/>
							<label for="params_port_popup_position0"><?php echo 'Left';?></label>

						<input type="radio" name="params[port_popup_position]" id="params_port_popup_position1" value="1"
						<?php echo isset($popup_port_check1)?$popup_port_check1:''; ?>/>
							<label for="params_port_popup_position1"><?php echo 'Middle';?></label>

						<input type="radio" name="params[port_popup_position]" id="params_port_popup_position2" value="2"
						<?php echo isset($popup_port_check2)?$popup_port_check2:''; ?>/>
							<label for="params_port_popup_position2"><?php echo 'Right';?></label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_bg_color-lbl" for="paramscube_port_popup_bg_color" class="hasTip" title="Popup Background Color"><?php echo 'Popup Background Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_bg_color]" type="text" class="sc_color" id="paramscube_port_popup_bg_color" value="<?php
							echo isset($param_values['port_popup_bg_color'])?$param_values['port_popup_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_content_bg_color-lbl" for="paramscube_port_popup_content_bg_color"  class="hasTip" title="Popup Content Background Color"><?php echo 'Popup Content Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_content_bg_color]" type="text" class="sc_color" id="paramscube_port_popup_content_bg_color" value="<?php
							echo isset($param_values['port_popup_content_bg_color'])?$param_values['port_popup_content_bg_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_img_height-lbl" for="paramscube_port_popup_img_height" class="hasTip" title="Popup Image Height"><?php echo 'Popup Image Height';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[port_popup_img_height]" id="paramscube_port_popup_img_height" value="<?php
						echo isset($param_values['port_popup_img_height'])?$param_values['port_popup_img_height']:'310'; ?>" class="text_area" size="4" />px</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_title_color-lbl" for="paramscube_port_popup_title_color" class="hasTip" title="Popup Title Color"><?php echo 'Popup Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_title_color]" type="text" class="sc_color" id="paramscube_port_popup_title_color" value="<?php
							echo isset($param_values['port_popup_title_color'])?$param_values['port_popup_title_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_title_size-lbl" for="paramscube_port_popup_title_size" class="hasTip" title="Popup Text Size"><?php echo 'Popup Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[port_popup_title_size]" id="paramscube_port_popup_title_size" value="<?php
						echo isset($param_values['port_popup_title_size'])?$param_values['port_popup_title_size']:'30'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_text_color-lbl" for="paramscube_port_popup_text_color" class="hasTip" title="Popup Text Color"><?php echo 'Popup Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_text_color]" type="text" class="sc_color" id="paramscube_port_popup_text_color" value="<?php
							echo isset($param_values['port_popup_text_color'])?$param_values['port_popup_text_color']:'#949494'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_text_size-lbl" for="paramscube_port_popup_text_size" class="hasTip" title="Popup Text Size"><?php echo 'Popup Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[port_popup_text_size]" id="paramscube_port_popup_text_size" value="<?php
						echo isset($param_values['port_popup_text_size'])?$param_values['port_popup_text_size']:'16'; ?>" class="text_area"  />px</td>
				</tr>

				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_link_color-lbl" for="paramscube_port_popup_link_color" class="hasTip" title="Popup Link Color"><?php echo 'Popup Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_link_color]" type="text" class="sc_color" id="paramscube_port_popup_link_color" value="<?php
							echo isset($param_values['port_popup_link_color'])?$param_values['port_popup_link_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_link_hover_color-lbl" for="paramscube_port_popup_link_hover_color" class="hasTip" title="Popup Hover Color"><?php echo 'Popup Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_link_hover_color]" type="text" class="sc_color" id="paramscube_port_popup_link_hover_color" value="<?php
							echo isset($param_values['port_popup_link_hover_color'])?$param_values['port_popup_link_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'close button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popoup_close_color-lbl" for="paramscube_port_popoup_close_color" class="hasTip" title="Popup Close Button Color"><?php echo 'Popup Close Button Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popoup_close_color]" type="text" class="sc_color" id="paramscube_port_popoup_close_color" value="<?php
							echo isset($param_values['port_popoup_close_color'])?$param_values['port_popoup_close_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_button_bg-lbl" for="paramscube_port_popup_button_bg" class="hasTip" title="Popup Button Background Color"><?php echo 'Popup Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_button_bg]" type="text" class="sc_color" id="paramscube_port_popup_button_bg" value="<?php
							echo isset($param_values['port_popup_button_bg'])?$param_values['port_popup_button_bg']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_button_hover_bg-lbl" for="paramscube_port_popup_button_hover_bg" class="hasTip" title="Popup Button Hover Background Color"><?php echo 'Popup Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_button_hover_bg]" type="text" class="sc_color" id="paramscube_port_popup_button_hover_bg" value="<?php
							echo isset($param_values['port_popup_button_hover_bg'])?$param_values['port_popup_button_hover_bg']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_button_color-lbl" for="paramscube_port_popup_button_color" class="hasTip" title="Popup Button Link Color"><?php echo 'Popup Button Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_button_color]" type="text" class="sc_color" id="paramscube_port_popup_button_color" value="<?php
							echo isset($param_values['port_popup_button_color'])?$param_values['port_popup_button_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_button_hover_color-lbl" for="paramscube_port_popup_button_hover_color" class="hasTip" title="Popup Button Hover Link Color"><?php echo 'Popup Button Hover Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_button_hover_color]" type="text" class="sc_color" id="paramscube_port_popup_button_hover_color" value="<?php
							echo isset($param_values['port_popup_button_hover_color'])?$param_values['port_popup_button_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'social icons'; ?></td></tr>
				<tr>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo 'Popup Social Icons View';?></label></span>
					</td>
					<td class="paramlist_value">
						<?php $checkFb = ""; $checkIs= ""; $checkTw = ""; $checkGp = ""; ?>
						<p><input type="checkbox" name="params[port_popup_social_icons][]" id="params_port_popup_social_icons0" value="0"
						<?php checked(isset($param_values['port_popup_social_icons'])  && in_array(0,$param_values['port_popup_social_icons'])); ?>
						<?php echo isset($checkFb)?$checkFb:'';  ?> />
							<label for="params_port_popup_social_icons0" class="staff_soc_labels"><?php echo 'Facebook';?></label>
							<input type="text" name="params[port_social_fb]" value="<?php echo isset($param_values['port_social_fb'])?$param_values['port_social_fb']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[port_popup_social_icons][]" id="params_port_popup_social_icons1" value="1"
						<?php checked(isset($param_values['port_popup_social_icons'])  && in_array(1,$param_values['port_popup_social_icons'])); ?>
						<?php echo isset($checkIs)?$checkIs:'';  ?> />
							<label for="params_port_popup_social_icons1" class="staff_soc_labels"><?php echo 'Instagram';?></label>
							<input type="text" name="params[port_social_ins]" value="<?php echo isset($param_values['port_social_ins'])?$param_values['port_social_ins']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[port_popup_social_icons][]" id="params_port_popup_social_icons2" value="2"
						<?php checked(isset($param_values['port_popup_social_icons'])  && in_array(2,$param_values['port_popup_social_icons'])); ?>
						<?php echo isset($checkTw)?$checkTw:'';  ?> />
							<label for="params_port_popup_social_icons2" class="staff_soc_labels"><?php echo 'Twitter';?></label>
							<input type="text" name="params[port_social_tw]" value="<?php echo isset($param_values['port_social_tw'])?$param_values['port_social_tw']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[port_popup_social_icons][]" id="params_port_popup_social_icons3" value="3"
						<?php checked(isset($param_values['port_popup_social_icons'])  && in_array(3,$param_values['port_popup_social_icons'])); ?>
						<?php echo isset($checkGp)?$checkGp:'';  ?> />
							<label for="params_port_popup_social_icons3" class="staff_soc_labels"><?php echo 'Google';?></label>
							<input type="text" name="params[port_social_gp]" value="<?php echo isset($param_values['port_social_gp'])?$param_values['port_social_gp']:''; ?>" placeholder="Write some link" /></p>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_soc_bg_color-lbl" for="paramscube_port_popup_soc_bg_color" class="hasTip" title="Popup Social Icons Background Color"><?php echo 'Popup Social Icons Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_soc_bg_color]" type="text" class="sc_color" id="paramscube_port_popup_soc_bg_color" value="<?php
							echo isset($param_values['port_popup_soc_bg_color'])?$param_values['port_popup_soc_bg_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_soc_hover_bg_color-lbl" for="paramscube_port_popup_soc_hover_bg_color" class="hasTip" title="Popup Social Icons Hover Background Color"><?php echo 'Popup Social Icons Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_soc_hover_bg_color]" type="text" class="sc_color" id="paramscube_port_popup_soc_hover_bg_color" value="<?php
							echo isset($param_values['port_popup_soc_hover_bg_color'])?$param_values['port_popup_soc_hover_bg_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_icons_color-lbl" for="paramscube_port_popup_icons_color" class="hasTip" title="Popup Social Icons Color"><?php echo 'Popup Social Icons Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_icons_color]" type="text" class="sc_color" id="paramscube_port_popup_icons_color" value="<?php
							echo isset($param_values['port_popup_icons_color'])?$param_values['port_popup_icons_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_port_popup_icons_hover_color-lbl" for="paramscube_port_popup_icons_hover_color" class="hasTip" title="Popup Social Icons Hover Color"><?php echo 'Popup Social Icons Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[port_popup_icons_hover_color]" type="text" class="sc_color" id="paramscube_port_popup_icons_hover_color" value="<?php
							echo isset($param_values['port_popup_icons_hover_color'])?$param_values['port_popup_icons_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<?php
					$icon_check0 = " checked='checked' ";
					$icon_check1 = "";
					if (isset($param_values['port_popup_icon_circle']) && $param_values['port_popup_icon_circle'] == 1) { $icon_check1 = ' checked="checked" '; $icon_check0 = '';} ?>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo 'Popup View Icons Circle';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="radio" name="params[port_popup_icon_circle]" id="params_port_popup_icon_radius0" value="0" <?php echo isset($icon_check0)?$icon_check0:''; ?>/>
							<label for="params_port_popup_icon_radius0"><?php echo 'No';?></label>
						<input type="radio" name="params[port_popup_icon_circle]" id="params_port_popup_icon_radius1" value="1" <?php echo isset($icon_check1)?$icon_check1:''; ?>/>
							<label for="params_port_popup_icon_radius1"><?php echo 'Yes';?></label>
					</td>
				</tr>
			</table>
		</div>
	</div>


	<!---------- BLOG SHOWCASE ---------->
    <div id="theme_style_blog" class="adminform">
		<h1 class="staff_main_title" title="Click for close"><?php echo _e('Blog View',$contLDomain); ?></h1>
		<div class="staff_view">
			<table  class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_title_color-lbl" for="paramscube_blog_title_color" class="hasTip" title="Title Color"><?php echo 'Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_title_color]" type="text" class="sc_color" id="paramscube_blog_title_color" value="<?php
							echo isset($param_values['blog_title_color'])?$param_values['blog_title_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_title_size-lbl" for="paramscube_blog_title_size" class="hasTip" title="Title Size"><?php echo 'Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[blog_title_size]" id="paramscube_blog_title_size" value="<?php
						echo isset($param_values['blog_title_size'])?$param_values['blog_title_size']:'20'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_text_color-lbl" for="paramscube_blog_text_color" class="hasTip" title="Text Color"><?php echo 'Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_text_color]" type="text" class="sc_color" id="paramscube_blog_text_color" value="<?php
							echo isset($param_values['blog_text_color'])?$param_values['blog_text_color']:'#686666'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_text_size-lbl" for="paramscube_blog_text_size" class="hasTip" title="Text Size"><?php echo 'Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[blog_text_size]" id="paramscube_blog_text_size" value="<?php
						echo isset($param_values['blog_text_size'])?$param_values['blog_text_size']:'15'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_link_color-lbl" for="paramscube_blog_link_color" class="hasTip" title="Link Color"><?php echo 'Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_link_color]" type="text" class="sc_color" id="paramscube_blog_link_color" value="<?php
							echo isset($param_values['blog_link_color'])?$param_values['blog_link_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_link_hover_color-lbl" for="paramscube_blog_link_hover_color" class="hasTip" title="Link Hover Color"><?php echo 'Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_link_hover_color]" type="text" class="sc_color" id="paramscube_blog_link_hover_color" value="<?php
							echo isset($param_values['blog_link_hover_color'])?$param_values['blog_link_hover_color']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_button_bg-lbl" for="paramscube_blog_button_bg" class="hasTip" title="Button Background Color"><?php echo 'Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_button_bg]" type="text" class="sc_color" id="paramscube_blog_button_bg" value="<?php
							echo isset($param_values['blog_button_bg'])?$param_values['blog_button_bg']:'#F2F2F2'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_button_hover_bg-lbl" for="paramscube_blog_button_hover_bg" class="hasTip" title="Button Hover Background Color"><?php echo 'Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_button_hover_bg]" type="text" class="sc_color" id="paramscube_blog_button_hover_bg" value="<?php
							echo isset($param_values['blog_button_hover_bg'])?$param_values['blog_button_hover_bg']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_button_color-lbl" for="paramscube_blog_button_color" class="hasTip" title="Button Link Color"><?php echo 'Button Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_button_color]" type="text" class="sc_color" id="paramscube_blog_button_color" value="<?php
							echo isset($param_values['blog_button_color'])?$param_values['blog_button_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_button_hover_color-lbl" for="paramscube_blog_button_hover_color" class="hasTip" title="Button Link Hover Color"><?php echo 'Button Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_button_hover_color]" type="text" class="sc_color" id="paramscube_blog_button_hover_color" value="<?php
							echo isset($param_values['blog_button_hover_color'])?$param_values['blog_button_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'search'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_search_border-lbl" for="paramscube_blog_search_border" class="hasTip" title="Search Border Color"><?php echo 'Search Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_search_border]" type="text" class="sc_color" id="paramscube_blog_search_border" value="<?php
							echo isset($param_values['blog_search_border'])?$param_values['blog_search_border']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_search_color-lbl" for="paramscube_blog_search_color" class="hasTip" title="Search Text Color"><?php echo 'Search Text Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_search_color]" type="text" class="sc_color" id="paramscube_blog_search_color" value="<?php
							echo isset($param_values['blog_search_color'])?$param_values['blog_search_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'pagination'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_pagination_font-lbl" for="paramscube_blog_pagination_font" class="hasTip" title="Pagination Font Size"><?php echo 'Pagination Font Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[blog_pagination_font]" id="paramscube_blog_pagination_font" value="<?php
						echo isset($param_values['blog_pagination_font'])?$param_values['blog_pagination_font']:'16'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_pagination_font_color-lbl" for="paramscube_blog_pagination_font_color" class="hasTip" title="Pagination Text Color"><?php echo 'Pagination Text Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_pagination_font_color]" type="text" class="sc_color" id="paramscube_blog_pagination_font_color" value="<?php
							echo isset($param_values['blog_pagination_font_color'])?$param_values['blog_pagination_font_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_pagination_bg-lbl" for="paramscube_blog_pagination_bg" class="hasTip" title="Pagination Background Color"><?php echo 'Pagination Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_pagination_bg]" type="text" class="sc_color" id="paramscube_blog_pagination_bg" value="<?php
							echo isset($param_values['blog_pagination_bg'])?$param_values['blog_pagination_bg']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_active_pagination_bg-lbl" for="paramscube_blog_active_pagination_bg" class="hasTip" title="Pagination Active Page Background Color"><?php echo 'Pagination Active Page Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_active_pagination_bg]" type="text" class="sc_color" id="paramscube_blog_active_pagination_bg" value="<?php
							echo isset($param_values['blog_active_pagination_bg'])?$param_values['blog_active_pagination_bg']:'#00A99D';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_pagination_border_color-lbl" for="paramscube_blog_pagination_border_color" class="hasTip" title="Pagination Border Color"><?php echo 'Pagination Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_pagination_border_color]" type="text" class="sc_color" id="paramscube_blog_pagination_border_color" value="<?php
							echo isset($param_values['blog_pagination_border_color'])?$param_values['blog_pagination_border_color']:'#DADADA';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_cont_count_in_page-lbl" for="paramscube_blog_cont_count_in_page" class="hasTip" title="Count of Contacts in the Page"><?php echo 'Count of Contacts in the Page';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[blog_cont_count_in_page]" id="paramscube_blog_cont_count_in_page" value="<?php
						echo isset($param_values['blog_cont_count_in_page'])?$param_values['blog_cont_count_in_page']:'4'; ?>" class="text_area" size="3" /></td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_border_color-lbl" for="paramscube_blog_border_color" class="hasTip" title="Image Border Color"><?php echo 'Image Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_border_color]" type="text" class="sc_color" id="paramscube_blog_border_color" value="<?php
							echo isset($param_values['blog_border_color'])?$param_values['blog_border_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_background_color-lbl" for="paramscube_blog_background_color" class="hasTip" title="Background Color"><?php echo 'Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_background_color]" type="text" class="sc_color" id="paramscube_blog_background_color" value="<?php
							echo isset($param_values['blog_background_color'])?$param_values['blog_background_color']:'#F2F2F2'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_no_image-lbl" for="paramscube_blog_no_image" class="hasTip" title="No Image"><?php echo 'No Image';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="text" id="paramscube_blog_no_image"  class="upload" name="params[blog_no_image]" value="<?php echo isset($param_values['blog_no_image'])?$param_values['blog_no_image']:''; ?>" />
						<input class="upload-button sc_upload-button" type="button" value="<?php _e('Upload Image', $contLDomain); ?>"/>
					</td>
				</tr>
			</table>
		</div>

		<h1 class="staff_main_title_popup" title="Click for close"><?php echo _e('Blog View Popup',$contLDomain); ?></h1>
		<div class="staff_view_popup">
			<table class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_header_bg_color-lbl" for="paramscube_blog_popup_header_bg_color" class="hasTip" title="Popup Header Background Color "><?php echo 'Popup Header Background Color ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_header_bg_color]" type="text" class="sc_color" id="paramscube_blog_popup_header_bg_color" value="<?php
							echo isset($param_values['blog_popup_header_bg_color'])?$param_values['blog_popup_header_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_bg_color-lbl" for="paramscube_blog_popup_bg_color" class="hasTip" title="Popup Content Background Color "><?php echo 'Popup Content Background Color ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_bg_color]" type="text" class="sc_color" id="paramscube_blog_popup_bg_color" value="<?php
							echo isset($param_values['blog_popup_bg_color'])?$param_values['blog_popup_bg_color']:'#F2F2F2'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_title_color-lbl" for="paramscube_blog_popup_title_color" class="hasTip" title="Title Color"><?php echo 'Popup Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_title_color]" type="text" class="sc_color" id="paramscube_blog_popup_title_color" value="<?php
							echo isset($param_values['blog_popup_title_color'])?$param_values['blog_popup_title_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_title_size-lbl" for="paramscube_blog_popup_title_size" class="hasTip" title="Popup Title Size"><?php echo 'Popup Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[blog_popup_title_size]" id="paramscube_blog_popup_title_size" value="<?php
						echo isset($param_values['blog_popup_title_size'])?$param_values['blog_popup_title_size']:'30'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_text_color-lbl" for="paramscube_blog_popup_text_color" class="hasTip" title="Text Color"><?php echo 'Popup Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_text_color]" type="text" class="sc_color" id="paramscube_blog_popup_text_color" value="<?php
							echo isset($param_values['blog_popup_text_color'])?$param_values['blog_popup_text_color']:'#949494'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_text_size-lbl" for="paramscube_blog_popup_text_size" class="hasTip" title="Popup Text Size"><?php echo 'Popup Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[blog_popup_text_size]" id="paramscube_blog_popup_text_size" value="<?php
						echo isset($param_values['blog_popup_text_size'])?$param_values['blog_popup_text_size']:'17'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_link_color-lbl" for="paramscube_blog_popup_link_color" class="hasTip" title="Popup Link Color"><?php echo 'Popup Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_link_color]" type="text" class="sc_color" id="paramscube_blog_popup_link_color" value="<?php
							echo isset($param_values['blog_popup_link_color'])?$param_values['blog_popup_link_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_link_hover_color-lbl" for="paramscube_blog_popup_link_hover_color" class="hasTip" title="Popup Link Hover Color"><?php echo 'Popup Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_link_hover_color]" type="text" class="sc_color" id="paramscube_blog_popup_link_hover_color" value="<?php
							echo isset($param_values['blog_popup_link_hover_color'])?$param_values['blog_popup_link_hover_color']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'close button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popoup_close_color-lbl" for="paramscube_blog_popoup_close_color" class="hasTip" title="Popup Close Button Color"><?php echo 'Popup Close Button Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popoup_close_color]" type="text" class="sc_color" id="paramscube_blog_popoup_close_color" value="<?php
							echo isset($param_values['blog_popoup_close_color'])?$param_values['blog_popoup_close_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_button_bg-lbl" for="paramscube_blog_popup_button_bg" class="hasTip" title="Popup Button Background Color"><?php echo 'Popup Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_button_bg]" type="text" class="sc_color" id="paramscube_blog_popup_button_bg" value="<?php
							echo isset($param_values['blog_popup_button_bg'])?$param_values['blog_popup_button_bg']:'#686666'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_button_hover_bg-lbl" for="paramscube_blog_popup_button_hover_bg" class="hasTip" title="Popup Button Hover Background Color"><?php echo 'Popup Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_button_hover_bg]" type="text" class="sc_color" id="paramscube_blog_popup_button_hover_bg" value="<?php
							echo isset($param_values['blog_popup_button_hover_bg'])?$param_values['blog_popup_button_hover_bg']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_button_color-lbl" for="paramscube_blog_popup_button_color" class="hasTip" title="Popup Button Link Color"><?php echo 'Popup Button Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_button_color]" type="text" class="sc_color" id="paramscube_blog_popup_button_color" value="<?php
							echo isset($param_values['blog_popup_button_color'])?$param_values['blog_popup_button_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_button_hover_color-lbl" for="paramscube_blog_popup_button_hover_color" class="hasTip" title="Popup Popup Button Link Hover Color"><?php echo 'Popup Button Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_button_hover_color]" type="text" class="sc_color" id="paramscube_blog_popup_button_hover_color" value="<?php
							echo isset($param_values['blog_popup_button_hover_color'])?$param_values['blog_popup_button_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'social icons'; ?></td></tr>
				<tr>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo 'Social Icons View';?></label></span>
					</td>
					<td class="paramlist_value">
						<?php $checkFb = ""; $checkIs= ""; $checkTw = "";$checkGp = ""; ?>
						<p><input type="checkbox" name="params[blog_popup_social_icons][]" id="params_blog_popup_social_icons0" value="0"
						<?php checked(isset($param_values['blog_popup_social_icons'])  && in_array(0,$param_values['blog_popup_social_icons'])); ?>
						<?php echo isset($checkFb)?$checkFb:'';  ?> />
							<label for="params_blog_popup_social_icons0" class="staff_soc_labels"><?php echo 'Facebook';?></label>
							<input type="text" name="params[blog_social_fb]" value="<?php echo isset($param_values['blog_social_fb'])?$param_values['blog_social_fb']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[blog_popup_social_icons][]" id="params_blog_popup_social_icons1" value="1"
						<?php checked(isset($param_values['blog_popup_social_icons'])  && in_array(1,$param_values['blog_popup_social_icons'])); ?>
						<?php echo isset($checkIs)?$checkIs:'';  ?> />
							<label for="params_blog_popup_social_icons1" class="staff_soc_labels"><?php echo 'Instagram';?></label>
							<input type="text" name="params[blog_social_ins]" value="<?php echo isset($param_values['blog_social_ins'])?$param_values['blog_social_ins']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[blog_popup_social_icons][]" id="params_blog_popup_social_icons2" value="2"
						<?php checked(isset($param_values['blog_popup_social_icons'])  && in_array(2,$param_values['blog_popup_social_icons'])); ?>
						<?php echo isset($checkTw)?$checkTw:'';  ?> />
							<label for="params_blog_popup_social_icons2" class="staff_soc_labels"><?php echo 'Twitter';?></label>
							<input type="text" name="params[blog_social_tw]" value="<?php echo isset($param_values['blog_social_tw'])?$param_values['blog_social_tw']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[blog_popup_social_icons][]" id="params_blog_popup_social_icons3" value="3"
						<?php checked(isset($param_values['blog_popup_social_icons'])  && in_array(3,$param_values['blog_popup_social_icons'])); ?>
						<?php echo isset($checkGp)?$checkGp:'';  ?>/>
							<label for="params_blog_popup_social_icons3" class="staff_soc_labels"><?php echo 'Google';?></label>
							<input type="text" name="params[blog_social_gp]" value="<?php echo isset($param_values['blog_social_gp'])?$param_values['blog_social_gp']:''; ?>" placeholder="Write some link" /></p>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_soc_bg_color-lbl" for="paramscube_blog_popup_soc_bg_color" class="hasTip" title="Popup Social Icons Background Color"><?php echo 'Popup Social Icons Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_soc_bg_color]" type="text" class="sc_color" id="paramscube_blog_popup_soc_bg_color" value="<?php
							echo isset($param_values['blog_popup_soc_bg_color'])?$param_values['blog_popup_soc_bg_color']:'#686666'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_soc_hover_bg_color-lbl" for="paramscube_blog_popup_soc_hover_bg_color" class="hasTip" title="Popup Social Icons Hover Background Color"><?php echo 'Popup Social Icons Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_soc_hover_bg_color]" type="text" class="sc_color" id="paramscube_blog_popup_soc_hover_bg_color" value="<?php
							echo isset($param_values['blog_popup_soc_hover_bg_color'])?$param_values['blog_popup_soc_hover_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_icons_color-lbl" for="paramscube_blog_popup_icons_color" class="hasTip" title="Popup Social Icons Color"><?php echo 'Popup Social Icons Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_icons_color]" type="text" class="sc_color" id="paramscube_blog_popup_icons_color" value="<?php
							echo isset($param_values['blog_popup_icons_color'])?$param_values['blog_popup_icons_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_blog_popup_icons_hover_color-lbl" for="paramscube_blog_popup_icons_hover_color" class="hasTip" title="Popup Social Icons Hover Color"><?php echo 'Popup Social Icons Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[blog_popup_icons_hover_color]" type="text" class="sc_color" id="paramscube_blog_popup_icons_hover_color" value="<?php
							echo isset($param_values['blog_popup_icons_hover_color'])?$param_values['blog_popup_icons_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<?php
					$icon_check0 = " checked='checked' ";
					$icon_check1 = "";
					if (isset($param_values['blog_popup_icon_circle']) && $param_values['blog_popup_icon_circle'] == 1) { $icon_check1 = ' checked="checked" '; $icon_check0 = '';} ?>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo ' View Popup Icons Circle';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="radio" name="params[blog_popup_icon_circle]" id="params_blog_popup_icon_radius0" value="0" <?php echo isset($icon_check0)?$icon_check0:''; ?>/>
							<label for="params_blog_popup_icon_radius0"><?php echo 'No';?></label>
						<input type="radio" name="params[blog_popup_icon_circle]" id="params_blog_popup_icon_radius1" value="1" <?php echo isset($icon_check1)?$icon_check1:''; ?>/>
							<label for="params_blog_popup_icon_radius1"><?php echo 'Yes';?></label>
					</td>
				</tr>
			</table>
		</div>
	</div>


	<!---------- CIRCLE SHOWCASE ---------->
    <div id="theme_style_circle" class="adminform">
		<h1 class="staff_main_title" title="Click for close"><?php echo _e('Circle View',$contLDomain); ?></h1>
		<div class="staff_view">
			<table class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'border'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_border_width-lbl" for="paramscube_circle_border_width" class="hasTip" title="Border Width"><?php echo 'Border Width';?></label></span>
					</td>
					<td class="paramlist_value"><input type="text" name="params[circle_border_width]" id="paramscube_circle_border_width" value="<?php
						echo isset($param_values['circle_border_width'])?$param_values['circle_border_width']:'2'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<?php
					$check0 = ""; $check1 = "";
					$check2 = ""; $check3 = "";
					$check4 = ""; $check5 = "";
					$check6 = ""; $check7 = "";
					if (isset($param_values['circle_border_style']) && $param_values['circle_border_style'] == 'solid')
						$check0 = '  selected="selected"  ';
					if (isset($param_values['circle_border_style']) && $param_values['circle_border_style'] == 'double')
						$check1 = '  selected="selected"';
					if (isset($param_values['circle_border_style']) && $param_values['circle_border_style'] == 'dashed')
						$check2 = '  selected="selected"  ';
					if (isset($param_values['circle_border_style']) && $param_values['circle_border_style'] == 'dotted')
						$check3 = '  selected="selected"';
					if (isset($param_values['circle_border_style']) && $param_values['circle_border_style'] == 'groove')
						$check4 = '  selected="selected"  ';
					if (isset($param_values['circle_border_style']) && $param_values['circle_border_style'] == 'inset')
						$check5 = '  selected="selected"';
					if (isset($param_values['circle_border_style']) && $param_values['circle_border_style'] == 'outset')
						$check6 = '  selected="selected"  ';
					if (isset($param_values['circle_border_style']) && $param_values['circle_border_style'] == 'ridge')
						$check7 = '  selected="selected"'; ?>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_border_style-lbl" for="paramscube_circle_border_style" class="hasTip" title="Border Style"><?php echo 'Border Style';?></label></span></td>
					<td class="paramlist_value">
						<select name="params[circle_border_style]" id="paramscube_circle_border_style" >
							<option value="solid"  <?php echo isset($check0)?$check0:''; ?> > solid </option>
							<option value="double" <?php echo isset($check1)?$check1:''; ?> > double </option>
							<option value="dashed" <?php echo isset($check2)?$check2:''; ?> > dashed </option>
							<option value="dotted" <?php echo isset($check3)?$check3:''; ?> > dotted </option>
							<option value="groove" <?php echo isset($check4)?$check4:''; ?> > groove </option>
							<option value="inset"  <?php echo isset($check5)?$check5:''; ?> > inset </option>
							<option value="outset" <?php echo isset($check6)?$check6:''; ?> > outset </option>
							<option value="ridge"  <?php echo isset($check7)?$check7:''; ?> > ridge </option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_border_color-lbl" for="paramscube_circle_border_color" class="hasTip" title="Border Color"><?php echo 'Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_border_color]" type="text" class="sc_color" id="paramscube_circle_border_color" value="<?php
							echo isset($param_values['circle_border_color'])?$param_values['circle_border_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_title_color-lbl" for="paramscube_circle_title_color" class="hasTip" title="Title Color"><?php echo 'Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_title_color]" type="text" class="sc_color" id="paramscube_circle_title_color" value="<?php
							echo isset($param_values['circle_title_color'])?$param_values['circle_title_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_title_size-lbl" for="paramscube_circle_title_size" class="hasTip" title="Title Size"><?php echo 'Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[circle_title_size]" id="paramscube_circle_title_size" value="<?php
						echo isset($param_values['circle_title_size'])?$param_values['circle_title_size']:'20'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_text_color-lbl" for="paramscube_circle_text_color" class="hasTip" title="Text Color"><?php echo 'Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_text_color]" type="text" class="sc_color" id="paramscube_circle_text_color" value="<?php
							echo isset($param_values['circle_text_color'])?$param_values['circle_text_color']:'#808080'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_link_color-lbl" for="paramscube_circle_link_color" class="hasTip" title="Link Color"><?php echo 'Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_link_color]" type="text" class="sc_color" id="paramscube_circle_link_color" value="<?php
							echo isset($param_values['circle_link_color'])?$param_values['circle_link_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_link_hover_color-lbl" for="paramscube_circle_link_hover_color" class="hasTip" title="Link Hover Color"><?php echo 'Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_link_hover_color]" type="text" class="sc_color" id="paramscube_circle_link_hover_color" value="<?php
							echo isset($param_values['circle_link_hover_color'])?$param_values['circle_link_hover_color']:'#DADADA'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_button_bg_color-lbl" for="paramscube_circle_button_bg_color" class="hasTip" title="Button Background Color"><?php echo 'Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_button_bg_color]" type="text" class="sc_color" id="paramscube_circle_button_bg_color" value="<?php
							echo isset($param_values['circle_button_bg_color'])?$param_values['circle_button_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_button_bg_hover_color-lbl" for="paramscube_circle_button_bg_hover_color" class="hasTip" title="Button Hover Background Color"><?php echo 'Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_button_bg_hover_color]" type="text" class="sc_color" id="paramscube_circle_button_bg_hover_color" value="<?php
							echo isset($param_values['circle_button_bg_hover_color'])?$param_values['circle_button_bg_hover_color']:'#808080'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_button_color-lbl" for="paramscube_circle_button_color" class="hasTip" title="Button Link Color"><?php echo 'Button Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_button_color]" type="text" class="sc_color" id="paramscube_circle_button_color" value="<?php
							echo isset($param_values['circle_button_color'])?$param_values['circle_button_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_button_hover_color-lbl" for="paramscube_circle_button_hover_color" class="hasTip" title="Button Hover Link Color"><?php echo 'Button Hover Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_button_hover_color]" type="text" class="sc_color" id="paramscube_circle_button_hover_color" value="<?php
							echo isset($param_values['circle_button_hover_color'])?$param_values['circle_button_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'search'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_search_border-lbl" for="paramscube_circle_search_border" class="hasTip" title="Search Border Color"><?php echo 'Search Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_search_border]" type="text" class="sc_color" id="paramscube_circle_search_border" value="<?php
							echo isset($param_values['circle_search_border'])?$param_values['circle_search_border']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_search_color-lbl" for="paramscube_circle_search_color" class="hasTip" title="Search Text Color"><?php echo 'Search Text Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_search_color]" type="text" class="sc_color" id="paramscube_circle_search_color" value="<?php
							echo isset($param_values['circle_search_color'])?$param_values['circle_search_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'pagination'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_pagination_font-lbl" for="paramscube_circle_pagination_font" class="hasTip" title="Pagination Font Size"><?php echo 'Pagination Font Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[circle_pagination_font]" id="paramscube_circle_pagination_font" value="<?php
						echo isset($param_values['circle_pagination_font'])?$param_values['circle_pagination_font']:'16'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_pagination_font_color-lbl" for="paramscube_circle_pagination_font_color" class="hasTip" title="Pagination Text Color"><?php echo 'Pagination Text Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_pagination_font_color]" type="text" class="sc_color" id="paramscube_circle_pagination_font_color" value="<?php
							echo isset($param_values['circle_pagination_font_color'])?$param_values['circle_pagination_font_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_pagination_bg-lbl" for="paramscube_circle_pagination_bg" class="hasTip" title="Pagination Background Color"><?php echo 'Pagination Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_pagination_bg]" type="text" class="sc_color" id="paramscube_circle_pagination_bg" value="<?php
							echo isset($param_values['circle_pagination_bg'])?$param_values['circle_pagination_bg']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_pagination_active_bg-lbl" for="paramscube_circle_pagination_active_bg" class="hasTip" title="Pagination Active Page Background Color"><?php echo 'Pagination Active Page Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_pagination_active_bg]" type="text" class="sc_color" id="paramscube_circle_pagination_active_bg" value="<?php
							echo isset($param_values['circle_pagination_active_bg'])?$param_values['circle_pagination_active_bg']:'#00A99D';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_pagination_border_color-lbl" for="paramscube_circle_pagination_border_color" class="hasTip" title="Pagination Border Color"><?php echo 'Pagination Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_pagination_border_color]" type="text" class="sc_color" id="paramscube_circle_pagination_border_color" value="<?php
							echo isset($param_values['circle_pagination_border_color'])?$param_values['circle_pagination_border_color']:'#DADADA';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_cont_count_in_page-lbl" for="paramscube_circle_cont_count_in_page" class="hasTip" title="Count of Contacts in the Page"><?php echo 'Count of Contacts in the Page';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[circle_cont_count_in_page]" id="paramscube_circle_cont_count_in_page" value="<?php
						echo isset($param_values['circle_cont_count_in_page'])?$param_values['circle_cont_count_in_page']:'4'; ?>" class="text_area" size="3" /></td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_no_image-lbl" for="paramscube_circle_no_image" class="hasTip" title="No Image"><?php echo 'No Image';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="text" id="paramscube_circle_no_image"  class="upload" name="params[circle_no_image]" value="<?php echo isset($param_values['circle_no_image'])?$param_values['circle_no_image']:''; ?>" />
						<input class="upload-button sc_upload-button" type="button" value="<?php _e('Upload Image', $contLDomain); ?>"/>
					</td>
				</tr>
			</table>
		</div>

		<h1 class="staff_main_title_popup" title="Click for close"><?php echo _e('Circle View Popup',$contLDomain); ?></h1>
		<div class="staff_view_popup">
			<table class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_header_bg_color-lbl" for="paramscube_circle_popup_header_bg_color" class="hasTip" title="Popup Header Background Color "><?php echo 'Popup Header Background Color ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_header_bg_color]" type="text" class="sc_color" id="paramscube_circle_popup_header_bg_color" value="<?php
							echo isset($param_values['circle_popup_header_bg_color'])?$param_values['circle_popup_header_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_bg_color-lbl" for="paramscube_circle_popup_bg_color" class="hasTip" title="Popup Content Background Color "><?php echo 'Popup Content Background Color ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_bg_color]" type="text" class="sc_color" id="paramscube_circle_popup_bg_color" value="<?php
							echo isset($param_values['circle_popup_bg_color'])?$param_values['circle_popup_bg_color']:'#F2F2F2'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_title_color-lbl" for="paramscube_circle_popup_title_color" class="hasTip" title="Title Color"><?php echo 'Popup Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_title_color]" type="text" class="sc_color" id="paramscube_circle_popup_title_color" value="<?php
							echo isset($param_values['circle_popup_title_color'])?$param_values['circle_popup_title_color']:'#000000'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_title_size-lbl" for="paramscube_circle_popup_title_size" class="hasTip" title="Popup Title Size"><?php echo 'Popup Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[circle_popup_title_size]" id="paramscube_circle_popup_title_size" value="<?php
						echo isset($param_values['circle_popup_title_size'])?$param_values['circle_popup_title_size']:'30'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_text_color-lbl" for="paramscube_circle_popup_text_color" class="hasTip" title="Text Color"><?php echo 'Popup Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_text_color]" type="text" class="sc_color" id="paramscube_circle_popup_text_color" value="<?php
							echo isset($param_values['circle_popup_text_color'])?$param_values['circle_popup_text_color']:'#949494'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_text_size-lbl" for="paramscube_circle_popup_text_size" class="hasTip" title="Popup Text Size"><?php echo 'Popup Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[circle_popup_text_size]" id="paramscube_circle_popup_text_size" value="<?php
						echo isset($param_values['circle_popup_text_size'])?$param_values['circle_popup_text_size']:'17'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_link_color-lbl" for="paramscube_circle_popup_link_color" class="hasTip" title="Link Color"><?php echo 'Popup Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_link_color]" type="text" class="sc_color" id="paramscube_circle_popup_link_color" value="<?php
							echo isset($param_values['circle_popup_link_color'])?$param_values['circle_popup_link_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_link_hover_color-lbl" for="paramscube_circle_popup_link_hover_color" class="hasTip" title="Link Hover Color"><?php echo 'Popup Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_link_hover_color]" type="text" class="sc_color" id="paramscube_circle_popup_link_hover_color" value="<?php
							echo isset($param_values['circle_popup_link_hover_color'])?$param_values['circle_popup_link_hover_color']:'#DADADA'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'close button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popoup_close_color-lbl" for="paramscube_circle_popoup_close_color" class="hasTip" title="Popup Close Button Color"><?php echo 'Popup Close Button Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popoup_close_color]" type="text" class="sc_color" id="paramscube_circle_popoup_close_color" value="<?php
							echo isset($param_values['circle_popoup_close_color'])?$param_values['circle_popoup_close_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_button_bg-lbl" for="paramscube_circle_popup_button_bg" class="hasTip" title="Popup Button Background Color"><?php echo 'Popup Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_button_bg]" type="text" class="sc_color" id="paramscube_circle_popup_button_bg" value="<?php
							echo isset($param_values['circle_popup_button_bg'])?$param_values['circle_popup_button_bg']:'#686666'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_button_hover_bg-lbl" for="paramscube_circle_popup_button_hover_bg" class="hasTip" title="Popup Button Hover Background Color"><?php echo 'Popup Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_button_hover_bg]" type="text" class="sc_color" id="paramscube_circle_popup_button_hover_bg" value="<?php
							echo isset($param_values['circle_popup_button_hover_bg'])?$param_values['circle_popup_button_hover_bg']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_button_color-lbl" for="paramscube_circle_popup_button_color" class="hasTip" title="Popup Button Link Color"><?php echo 'Popup Button Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_button_color]" type="text" class="sc_color" id="paramscube_circle_popup_button_color" value="<?php
							echo isset($param_values['circle_popup_button_color'])?$param_values['circle_popup_button_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_button_hover_color-lbl" for="paramscube_circle_popup_button_hover_color" class="hasTip" title="Popup Button Hover Link Color"><?php echo 'Popup Button Hover Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_button_hover_color]" type="text" class="sc_color" id="paramscube_circle_popup_button_hover_color" value="<?php
							echo isset($param_values['circle_popup_button_hover_color'])?$param_values['circle_popup_button_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'social icons'; ?></td></tr>
				<tr>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo 'Popup Social Icons View';?></label></span>
					</td>
					<td class="paramlist_value">
						<?php $checkFb = ""; $checkIs= ""; $checkTw = ""; $checkGp = ""; ?>
						<p><input type="checkbox" name="params[circle_popup_social_icons][]" id="params_circle_popup_social_icons0" value="0"
						<?php checked(isset($param_values['circle_popup_social_icons'])  && in_array(0,$param_values['circle_popup_social_icons'])); ?>
						<?php echo isset($checkFb)?$checkFb:'';  ?> />
							<label for="params_circle_popup_social_icons0" class="staff_soc_labels"><?php echo 'Facebook';?></label>
							<input type="text" name="params[circle_social_fb]" value="<?php echo isset($param_values['circle_social_fb'])?$param_values['circle_social_fb']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[circle_popup_social_icons][]" id="params_circle_popup_social_icons1" value="1"
						<?php checked(isset($param_values['circle_popup_social_icons'])  && in_array(1,$param_values['circle_popup_social_icons'])); ?>
						<?php echo isset($checkIs)?$checkIs:'';  ?> />
							<label for="params_circle_popup_social_icons1" class="staff_soc_labels"><?php echo 'Instagram';?></label>
							<input type="text" name="params[circle_social_ins]" value="<?php echo isset($param_values['circle_social_ins'])?$param_values['circle_social_ins']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[circle_popup_social_icons][]" id="params_circle_popup_social_icons2" value="2"
						<?php checked(isset($param_values['circle_popup_social_icons'])  && in_array(2,$param_values['circle_popup_social_icons'])); ?>
						<?php echo isset($checkTw)?$checkTw:'';  ?> />
							<label for="params_circle_popup_social_icons2" class="staff_soc_labels"><?php echo 'Twitter';?></label>
							<input type="text" name="params[circle_social_tw]" value="<?php echo isset($param_values['circle_social_tw'])?$param_values['circle_social_tw']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[circle_popup_social_icons][]" id="params_circle_popup_social_icons3" value="3"
						<?php checked(isset($param_values['circle_popup_social_icons'])  && in_array(3,$param_values['circle_popup_social_icons'])); ?>
						<?php echo isset($checkGp)?$checkGp:'';  ?> />
							<label for="params_circle_popup_social_icons3" class="staff_soc_labels"><?php echo 'Google';?></label>
							<input type="text" name="params[circle_social_gp]" value="<?php echo isset($param_values['circle_social_gp'])?$param_values['circle_social_gp']:''; ?>" placeholder="Write some link" /></p>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_soc_bg_color-lbl" for="paramscube_circle_popup_soc_bg_color" class="hasTip" title="Popup Social Icons Background Color"><?php echo 'Popup Social Icons Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_soc_bg_color]" type="text" class="sc_color" id="paramscube_circle_popup_soc_bg_color" value="<?php
							echo isset($param_values['circle_popup_soc_bg_color'])?$param_values['circle_popup_soc_bg_color']:'#686666'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_soc_hover_bg_color-lbl" for="paramscube_circle_popup_soc_hover_bg_color" class="hasTip" title="Popup Social Icons Hover Background Color"><?php echo 'Popup Social Icons Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_soc_hover_bg_color]" type="text" class="sc_color" id="paramscube_circle_popup_soc_hover_bg_color" value="<?php
							echo isset($param_values['circle_popup_soc_hover_bg_color'])?$param_values['circle_popup_soc_hover_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_icons_color-lbl" for="paramscube_circle_popup_icons_color" class="hasTip" title="Popup Social Icons Color"><?php echo 'Popup Social Icons Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_icons_color]" type="text" class="sc_color" id="paramscube_circle_popup_icons_color" value="<?php
							echo isset($param_values['circle_popup_icons_color'])?$param_values['circle_popup_icons_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_circle_popup_icons_hover_color-lbl" for="paramscube_circle_popup_icons_hover_color" class="hasTip" title="Popup Social Icons Hover Color"><?php echo 'Popup Social Icons Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[circle_popup_icons_hover_color]" type="text" class="sc_color" id="paramscube_circle_popup_icons_hover_color" value="<?php
							echo isset($param_values['circle_popup_icons_hover_color'])?$param_values['circle_popup_icons_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<?php
					$icon_check0 = " checked='checked' ";
					$icon_check1 = "";
					if (isset($param_values['circle_popup_icon_circle']) && $param_values['circle_popup_icon_circle'] == 1) { $icon_check1 = ' checked="checked" '; $icon_check0 = '';} ?>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo ' View Popup Icons Circle';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="radio" name="params[circle_popup_icon_circle]" id="params_circle_popup_icon_radius0" value="0" <?php echo isset($icon_check0)?$icon_check0:''; ?>/>
							<label for="params_circle_popup_icon_radius0"><?php echo 'No';?></label>
						<input type="radio" name="params[circle_popup_icon_circle]" id="params_circle_popup_icon_radius1" value="1" <?php echo isset($icon_check1)?$icon_check1:''; ?>/>
							<label for="params_circle_popup_icon_radius1"><?php echo 'Yes';?></label>
					</td>
				</tr>
			</table>
		</div>
	</div>


	<!---------- SQUARE SHOWCASE ---------->
    <div id="theme_style_square" class="adminform">
		<h1 class="staff_main_title" title="Click for close"><?php echo _e('Square View',$contLDomain); ?></h1>
		<div class="staff_view">
			<table  class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'border'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_border_width-lbl" for="paramscube_square_border_width" class="hasTip" title="Border Width"><?php echo 'Border Width';?></label></span>
					</td>
					<td class="paramlist_value"><input type="text" name="params[square_border_width]" id="paramscube_square_border_width" value="<?php
						echo isset($param_values['square_border_width'])?$param_values['square_border_width']:'1'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<?php
					$check0 = ""; $check1 = "";
					$check2 = ""; $check3 = "";
					$check4 = ""; $check5 = "";
					$check6 = ""; $check7 = "";
					if (isset($param_values['square_border_style']) && $param_values['square_border_style'] == 'solid')
						$check0 = '  selected="selected"  ';
					if (isset($param_values['square_border_style']) && $param_values['square_border_style'] == 'double')
						$check1 = '  selected="selected"';
					if (isset($param_values['square_border_style']) && $param_values['square_border_style'] == 'dashed')
						$check2 = '  selected="selected"  ';
					if (isset($param_values['square_border_style']) && $param_values['square_border_style'] == 'dotted')
						$check3 = '  selected="selected"';
					if (isset($param_values['square_border_style']) && $param_values['square_border_style'] == 'groove')
						$check4 = '  selected="selected"  ';
					if (isset($param_values['square_border_style']) && $param_values['square_border_style'] == 'inset')
						$check5 = '  selected="selected"';
					if (isset($param_values['square_border_style']) && $param_values['square_border_style'] == 'outset')
						$check6 = '  selected="selected"  ';
					if (isset($param_values['square_border_style']) && $param_values['square_border_style'] == 'ridge')
						$check7 = '  selected="selected"'; ?>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_border_style-lbl" for="paramscube_square_border_style" class="hasTip" title="Border Style"><?php echo 'Border Style';?></label></span></td>
					<td class="paramlist_value">
						<select name="params[square_border_style]" id="paramscube_square_border_style" >
							<option value="solid"  <?php echo isset($check0)?$check0:''; ?> > solid </option>
							<option value="double" <?php echo isset($check1)?$check1:''; ?> > double </option>
							<option value="dashed" <?php echo isset($check2)?$check2:''; ?> > dashed </option>
							<option value="dotted" <?php echo isset($check3)?$check3:''; ?> > dotted </option>
							<option value="groove" <?php echo isset($check4)?$check4:''; ?> > groove </option>
							<option value="inset"  <?php echo isset($check5)?$check5:''; ?> > inset </option>
							<option value="outset" <?php echo isset($check6)?$check6:''; ?> > outset </option>
							<option value="ridge"  <?php echo isset($check7)?$check7:''; ?> > ridge </option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_border_color-lbl" for="paramscube_square_border_color" class="hasTip" title="Border Color"><?php echo 'Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_border_color]" type="text" class="sc_color" id="paramscube_square_border_color" value="<?php
							echo isset($param_values['square_border_color'])?$param_values['square_border_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_title_color-lbl" for="paramscube_square_title_color" class="hasTip" title="Title Color"><?php echo 'Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_title_color]" type="text" class="sc_color" id="paramscube_square_title_color" value="<?php
							echo isset($param_values['square_title_color'])?$param_values['square_title_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_title_size-lbl" for="paramscube_square_title_size" class="hasTip" title="Title Size"><?php echo 'Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[square_title_size]" id="paramscube_square_title_size" value="<?php
						echo isset($param_values['square_title_size'])?$param_values['square_title_size']:'16'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_link_color-lbl" for="paramscube_square_link_color" class="hasTip" title="Link Color"><?php echo 'Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_link_color]" type="text" class="sc_color" id="paramscube_square_link_color" value="<?php
							echo isset($param_values['square_link_color'])?$param_values['square_link_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_link_hover_color-lbl" for="paramscube_square_link_hover_color" class="hasTip" title="Link Hover Color"><?php echo 'Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_link_hover_color]" type="text" class="sc_color" id="paramscube_square_link_hover_color" value="<?php
							echo isset($param_values['square_link_hover_color'])?$param_values['square_link_hover_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'social icons'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo 'Social Icons View';?></label></span></td>
					<td class="paramlist_value">
						<?php $checkFb = ""; $checkIs= ""; $checkTw = ""; $checkGp = ""; ?>
						<p><input type="checkbox" name="params[square_social_icons][]" id="params_square_social_icons0" value="0"
						<?php checked(isset($param_values['square_social_icons'])  && in_array(0,$param_values['square_social_icons'])); ?>
						<?php echo isset($checkFb)?$checkFb:'';  ?> />
							<label for="params_square_social_icons0" class="staff_soc_labels"><?php echo 'Facebook';?></label>
							<input type="text" name="params[square_social_fb]" value="<?php echo isset($param_values['square_social_fb'])?$param_values['square_social_fb']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[square_social_icons][]" id="params_square_social_icons1" value="1"
						<?php checked(isset($param_values['square_social_icons'])  && in_array(1,$param_values['square_social_icons'])); ?>
						<?php echo isset($checkIs)?$checkIs:'';  ?> />
							<label for="params_square_social_icons1" class="staff_soc_labels"><?php echo 'Instagram';?></label>
							<input type="text" name="params[square_social_ins]" value="<?php echo isset($param_values['square_social_ins'])?$param_values['square_social_ins']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[square_social_icons][]" id="params_square_social_icons2" value="2"
						<?php checked(isset($param_values['square_social_icons'])  && in_array(2,$param_values['square_social_icons'])); ?>
						<?php echo isset($checkTw)?$checkTw:'';  ?> />
							<label for="params_square_social_icons2" class="staff_soc_labels"><?php echo 'Twitter';?></label>
							<input type="text" name="params[square_social_tw]" value="<?php echo isset($param_values['square_social_tw'])?$param_values['square_social_tw']:''; ?>" placeholder="Write some link" /></p>

						<p><input type="checkbox" name="params[square_social_icons][]" id="params_square_social_icons3" value="3"
						<?php checked(isset($param_values['square_social_icons'])  && in_array(3,$param_values['square_social_icons'])); ?>
						<?php echo isset($checkGp)?$checkGp:'';  ?> />
							<label for="params_square_social_icons3" class="staff_soc_labels"><?php echo 'Google';?></label>
							<input type="text" name="params[square_social_gp]" value="<?php echo isset($param_values['square_social_gp'])?$param_values['square_social_gp']:''; ?>" placeholder="Write some link" /></p>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_social_bg_color-lbl" for="paramscube_square_social_bg_color" class="hasTip" title="Social Icons Background Color"><?php echo 'Social Icons Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_social_bg_color]" type="text" class="sc_color" id="paramscube_square_social_bg_color" value="<?php
							echo isset($param_values['square_social_bg_color'])?$param_values['square_social_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_social_hover_bg_color-lbl" for="paramscube_square_social_hover_bg_color" class="hasTip" title="Social Icons Hover Background Color"><?php echo 'Social Icons Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_social_hover_bg_color]" type="text" class="sc_color" id="paramscube_square_social_hover_bg_color" value="<?php
							echo isset($param_values['square_social_hover_bg_color'])?$param_values['square_social_hover_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_social_color-lbl" for="paramscube_square_social_color" class="hasTip" title="Social Icons Color"><?php echo 'Social Icons Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_social_color]" type="text" class="sc_color" id="paramscube_square_social_color" value="<?php
							echo isset($param_values['square_social_color'])?$param_values['square_social_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_social_hover_color-lbl" for="paramscube_square_social_hover_color" class="hasTip" title="Social Icons Hover Color"><?php echo 'Social Icons Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_social_hover_color]" type="text" class="sc_color" id="paramscube_square_social_hover_color" value="<?php
							echo isset($param_values['square_social_hover_color'])?$param_values['square_social_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'search'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_search_border-lbl" for="paramscube_square_search_border" class="hasTip" title="Search Border Color"><?php echo 'Search Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_search_border]" type="text" class="sc_color" id="paramscube_square_search_border" value="<?php
							echo isset($param_values['square_search_border'])?$param_values['square_search_border']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_search_color-lbl" for="paramscube_square_search_color" class="hasTip" title="Search Text Color"><?php echo 'Search Text Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_search_color]" type="text" class="sc_color" id="paramscube_square_search_color" value="<?php
							echo isset($param_values['square_search_color'])?$param_values['square_search_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'pagination'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_pagination_font-lbl" for="paramscube_square_pagination_font" class="hasTip" title="Pagination Font Size"><?php echo 'Pagination Font Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[square_pagination_font]" id="paramscube_square_pagination_font" value="<?php
						echo isset($param_values['square_pagination_font'])?$param_values['square_pagination_font']:'16'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_pagination_font_color-lbl" for="paramscube_square_pagination_font_color" class="hasTip" title="Pagination Text Color"><?php echo 'Pagination Text Color  ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_pagination_font_color]" type="text" class="sc_color" id="paramscube_square_pagination_font_color" value="<?php
							echo isset($param_values['square_pagination_font_color'])?$param_values['square_pagination_font_color']:'#999999'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_pagination_bg-lbl" for="paramscube_square_pagination_bg" class="hasTip" title="Pagination Background Color"><?php echo 'Pagination Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_pagination_bg]" type="text" class="sc_color" id="paramscube_square_pagination_bg" value="<?php
							echo isset($param_values['square_pagination_bg'])?$param_values['square_pagination_bg']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_active_pagination_bg-lbl" for="paramscube_square_active_pagination_bg" class="hasTip" title="Pagination Active Page Background Color"><?php echo 'Pagination Active Page Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_active_pagination_bg]" type="text" class="sc_color" id="paramscube_square_active_pagination_bg" value="<?php
							echo isset($param_values['square_active_pagination_bg'])?$param_values['square_active_pagination_bg']:'#00A99D';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_pagination_border_color-lbl" for="paramscube_square_pagination_border_color" class="hasTip" title="Pagination Border Color"><?php echo 'Pagination Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_pagination_border_color]" type="text" class="sc_color" id="paramscube_square_pagination_border_color" value="<?php
							echo isset($param_values['square_pagination_border_color'])?$param_values['square_pagination_border_color']:'#DADADA';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_cont_count_in_page-lbl" for="paramscube_square_cont_count_in_page" class="hasTip" title="Count of Contacts in the Page"><?php echo 'Count of Contacts in the Page';?> </label></span></td>
					<td class="paramlist_value"><input type="text" name="params[square_cont_count_in_page]" id="paramscube_square_cont_count_in_page" value="<?php
						echo isset($param_values['square_cont_count_in_page'])?$param_values['square_cont_count_in_page']:'4'; ?>" class="text_area" size="3" /></td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_image_width-lbl" for="paramscube_square_image_width" class="hasTip" title="Contact Width"><?php echo 'Contact Width';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[square_image_width]" id="paramscube_square_image_width" value="<?php
						echo isset($param_values['square_image_width'])?$param_values['square_image_width']:'48'; ?>" class="text_area" size="4" />%
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_image_height-lbl" for="paramscube_square_image_height" class="hasTip" title="Contact Height"><?php echo 'Contact Height';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[square_image_height]" id="paramscube_square_image_height" value="<?php
						echo isset($param_values['square_image_height'])?$param_values['square_image_height']:'300'; ?>" class="text_area" size="4" />px
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_bg_hover_color-lbl" for="paramscube_square_bg_hover_color" class="hasTip" title="Hover Background Color"><?php echo 'Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_bg_hover_color]" type="text" class="sc_color" id="paramscube_square_bg_hover_color" value="<?php
							echo isset($param_values['square_bg_hover_color'])?$param_values['square_bg_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
			</table>
		</div>

		<h1 class="staff_main_title_popup" title="Click for close"><?php echo _e('Square View Popup',$contLDomain); ?></h1>
		<div class="staff_view_popup">
			<table class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'main options'; ?></td></tr>
				<tr>
				   <?php
					$popup_check0 = "";
					$popup_check1 = "";
					$popup_check2 = " checked='checked' ";
					if (isset($param_values['square_popup_position']) && $param_values['square_popup_position'] == '0') {
						$popup_check0 = " checked='checked' ";
						$popup_check1 = ""; $popup_check2 = "";
					}
					if (isset($param_values['square_popup_position']) && $param_values['square_popup_position'] == '1') {
						$popup_check1 = " checked='checked' ";
						$popup_check0 = ""; $popup_check2 = "";
					}
					if (isset($param_values['square_popup_position']) && $param_values['square_popup_position'] == '2') {
						$popup_check2 = " checked='checked' ";
						$popup_check0 = ""; $popup_check1 = "";
					} ?>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo ' Popup View Position';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="radio" name="params[square_popup_position]" id="params_square_popup_position0" value="0" <?php echo isset($popup_check0)?$popup_check0:''; ?>/>
							<label for="params_square_popup_position0"><?php echo 'Left';?></label>

						<input type="radio" name="params[square_popup_position]" id="params_square_popup_position1" value="1" <?php echo isset($popup_check1)?$popup_check1:''; ?>/>
							<label for="params_square_popup_position1"><?php echo 'Middle';?></label>

						<input type="radio" name="params[square_popup_position]" id="params_square_popup_position2" value="2" <?php echo isset($popup_check2)?$popup_check2:''; ?>/>
							<label for="params_square_popup_position2"><?php echo 'Right';?></label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_popup_bg_color-lbl" for="paramscube_square_popup_bg_color" class="hasTip" title="Popup Content Background Color "><?php echo 'Popup Content Background Color ';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_popup_bg_color]" type="text" class="sc_color" id="paramscube_square_popup_bg_color" value="<?php
							echo isset($param_values['square_popup_bg_color'])?$param_values['square_popup_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_popup_title_color-lbl" for="paramscube_square_popup_title_color" class="hasTip" title="Title Color"><?php echo 'Popup Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_popup_title_color]" type="text" class="sc_color" id="paramscube_square_popup_title_color" value="<?php
							echo isset($param_values['square_popup_title_color'])?$param_values['square_popup_title_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_popup_text_color-lbl" for="paramscube_square_popup_text_color" class="hasTip" title="Popup Text Color"><?php echo 'Popup Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_popup_text_color]" type="text" class="sc_color" id="paramscube_square_popup_text_color" value="<?php
							echo isset($param_values['square_popup_text_color'])?$param_values['square_popup_text_color']:'#949494'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_popup_text_size-lbl" for="paramscube_square_popup_text_size" class="hasTip" title="Popup Text Size"><?php echo 'Popup Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[square_popup_text_size]" id="paramscube_square_popup_text_size" value="<?php
						echo isset($param_values['square_popup_text_size'])?$param_values['square_popup_text_size']:'15'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_popup_link_color-lbl" for="paramscube_square_popup_link_color" class="hasTip" title="Popup Link Color"><?php echo 'Popup Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_popup_link_color]" type="text" class="sc_color" id="paramscube_square_popup_link_color" value="<?php
							echo isset($param_values['square_popup_link_color'])?$param_values['square_popup_link_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_popup_link_hover_color-lbl" for="paramscube_square_popup_link_hover_color" class="hasTip" title="Popup Link Hover Color"><?php echo 'Popup Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_popup_link_hover_color]" type="text" class="sc_color" id="paramscube_square_popup_link_hover_color" value="<?php
							echo isset($param_values['square_popup_link_hover_color'])?$param_values['square_popup_link_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'close button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_popoup_close_color-lbl" for="paramscube_square_popoup_close_color" class="hasTip" title="Popup Close Button Color"><?php echo 'Popup Close Button Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_popoup_close_color]" type="text" class="sc_color" id="paramscube_square_popoup_close_color" value="<?php
							echo isset($param_values['square_popoup_close_color'])?$param_values['square_popoup_close_color']:'#FFFFFF';  ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_button_bg-lbl" for="paramscube_square_button_bg" class="hasTip" title="Popup Button Background Color"><?php echo 'Popup Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_button_bg]" type="text" class="sc_color" id="paramscube_square_button_bg" value="<?php
							echo isset($param_values['square_button_bg'])?$param_values['square_button_bg']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_button_bg_hover_color-lbl" for="paramscube_square_button_bg_hover_color" class="hasTip" title="Popup Button Hover Background Color"><?php echo 'Popup Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_button_bg_hover_color]" type="text" class="sc_color" id="paramscube_square_button_bg_hover_color" value="<?php
							echo isset($param_values['square_button_bg_hover_color'])?$param_values['square_button_bg_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_button_color-lbl" for="paramscube_square_button_color" class="hasTip" title="Popup Button Link Color"><?php echo 'Popup Button Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_button_color]" type="text" class="sc_color" id="paramscube_square_button_color" value="<?php
							echo isset($param_values['square_button_color'])?$param_values['square_button_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_button_hover_color-lbl" for="paramscube_square_button_hover_color" class="hasTip" title="Popup Button Hover Link Color"><?php echo 'Popup Button Hover Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_button_hover_color]" type="text" class="sc_color" id="paramscube_square_button_hover_color" value="<?php
							echo isset($param_values['square_button_hover_color'])?$param_values['square_button_hover_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'social icons'; ?></td></tr>
				<tr>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="params_icons_radius" for="params_icons_radius"><?php echo 'Popup Social Icons View';?></label></span>
					</td>
					<td class="paramlist_value">
						<?php $checkFb = ""; $checkIs= ""; $checkTw = ""; $checkGp = ""; ?>
						<p><input type="checkbox" name="params[square_popup_social_icons][]" id="params_square_popup_social_icons0" value="0"
						<?php checked(isset($param_values['square_popup_social_icons'])  && in_array(0,$param_values['square_popup_social_icons'])); ?>
						<?php echo isset($checkFb)?$checkFb:'';  ?> />
							<label for="params_square_popup_social_icons0"><?php echo 'Facebook';?></label></p>

						<p><input type="checkbox" name="params[square_popup_social_icons][]" id="params_square_popup_social_icons1" value="1"
						<?php checked(isset($param_values['square_popup_social_icons'])  && in_array(1,$param_values['square_popup_social_icons'])); ?>
						<?php echo isset($checkIs)?$checkIs:'';  ?> />
							<label for="params_square_popup_social_icons1"><?php echo 'Instagram';?></label></p>

						<p><input type="checkbox" name="params[square_popup_social_icons][]" id="params_square_popup_social_icons2" value="2"
						<?php checked(isset($param_values['square_popup_social_icons'])  && in_array(2,$param_values['square_popup_social_icons'])); ?>
						<?php echo isset($checkTw)?$checkTw:'';  ?> />
							<label for="params_square_popup_social_icons2"><?php echo 'Twitter';?></label></p>

						<p><input type="checkbox" name="params[square_popup_social_icons][]" id="params_square_popup_social_icons3" value="3"
						<?php checked(isset($param_values['square_popup_social_icons'])  && in_array(3,$param_values['square_popup_social_icons'])); ?>
						<?php echo isset($checkGp)?$checkGp:'';  ?> />
							<label for="params_square_popup_social_icons3"><?php echo 'Google';?></label></p>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_popup_social_bg_color-lbl" for="paramscube_square_popup_social_bg_color" class="hasTip" title="Popup Social Icons Background Color"><?php echo 'Popup Social Icons Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_popup_social_bg_color]" type="text" class="sc_color" id="paramscube_square_popup_social_bg_color" value="<?php
							echo isset($param_values['square_popup_social_bg_color'])?$param_values['square_popup_social_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_popup_social_hover_bg_color-lbl" for="paramscube_square_popup_social_hover_bg_color" class="hasTip" title="Social Icons Hover Background Color"><?php echo 'Social Icons Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_popup_social_hover_bg_color]" type="text" class="sc_color" id="paramscube_square_popup_social_hover_bg_color" value="<?php
							echo isset($param_values['square_popup_social_hover_bg_color'])?$param_values['square_popup_social_hover_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_popup_social_color-lbl" for="paramscube_square_popup_social_color" class="hasTip" title="Social Icons Color"><?php echo 'Social Icons Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_popup_social_color]" type="text" class="sc_color" id="paramscube_square_popup_social_color" value="<?php
							echo isset($param_values['square_popup_social_color'])?$param_values['square_popup_social_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_square_popup_social_hover_color-lbl" for="paramscube_square_popup_social_hover_color" class="hasTip" title="Social Icons Hover Color"><?php echo 'Social Icons Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[square_popup_social_hover_color]" type="text" class="sc_color" id="paramscube_square_popup_social_hover_color" value="<?php
							echo isset($param_values['square_popup_social_hover_color'])?$param_values['square_popup_social_hover_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
			</table>
		</div>
	</div>


	<!---------- SINGLE SHOWCASE ---------->
	<div id="theme_style_contact" class="adminform">
        <h1 class="staff_main_title" title="Click for close"><?php echo _e('Single Contact',$contLDomain); ?></h1>
		<div class="staff_view">
			<table  class="paramlist admintable" cellspacing="1">
				<tr><td class="admin_title" colspan="2"><?php echo 'border'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_border_width-lbl" for="paramscube_single_border_width" class="hasTip" title="Border Width"><?php echo 'Border Width';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[single_border_width]" id="paramscube_single_border_width" value="<?php
						echo isset($param_values['single_border_width'])?$param_values['single_border_width']:'1'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<?php
					$check0 = ""; $check1 = "";
					$check2 = ""; $check3 = "";
					$check4 = ""; $check5 = "";
					$check6 = ""; $check7 = "";
					if (isset($param_values['single_border_style']) && $param_values['single_border_style'] == 'solid')
						$check0 = '  selected="selected"  ';
					if (isset($param_values['single_border_style']) && $param_values['single_border_style'] == 'double')
						$check1 = '  selected="selected"';
					if (isset($param_values['single_border_style']) && $param_values['single_border_style'] == 'dashed')
						$check2 = '  selected="selected"  ';
					if (isset($param_values['single_border_style']) && $param_values['single_border_style'] == 'dotted')
						$check3 = '  selected="selected"';
					if (isset($param_values['single_border_style']) && $param_values['single_border_style'] == 'groove')
						$check4 = '  selected="selected"  ';
					if (isset($param_values['single_border_style']) && $param_values['single_border_style'] == 'inset')
						$check5 = '  selected="selected"';
					if (isset($param_values['single_border_style']) && $param_values['single_border_style'] == 'outset')
						$check6 = '  selected="selected"  ';
					if (isset($param_values['single_border_style']) && $param_values['single_border_style'] == 'ridge')
						$check7 = '  selected="selected"'; ?>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_border_style-lbl" for="paramscube_single_border_style" class="hasTip" title="Border Style"><?php echo 'Border Style';?></label></span></td>
					<td class="paramlist_value">
						<select name="params[single_border_style]" id="paramscube_single_border_style" >
						   <option value="solid"  <?php echo isset($check0)?$check0:''; ?> > solid </option>
							<option value="double" <?php echo isset($check1)?$check1:''; ?> > double </option>
							<option value="dashed" <?php echo isset($check2)?$check2:''; ?> > dashed </option>
							<option value="dotted" <?php echo isset($check3)?$check3:''; ?> > dotted </option>
							<option value="groove" <?php echo isset($check4)?$check4:''; ?> > groove </option>
							<option value="inset"  <?php echo isset($check5)?$check5:''; ?> > inset </option>
							<option value="outset" <?php echo isset($check6)?$check6:''; ?> > outset </option>
							<option value="ridge"  <?php echo isset($check7)?$check7:''; ?> > ridge </option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_border_color-lbl" for="paramscube_single_border_color" class="hasTip" title="Border Color"><?php echo 'Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_border_color]" type="text" class="sc_color" id="paramscube_single_border_color" value="<?php
							echo isset($param_values['single_border_color'])?$param_values['single_border_color']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'text'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_title_color-lbl" for="paramscube_single_title_color" class="hasTip" title="Title Color"><?php echo 'Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_title_color]" type="text" class="sc_color" id="paramscube_single_title_color" value="<?php
							echo isset($param_values['single_title_color'])?$param_values['single_title_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_title_size-lbl" for="paramscube_single_title_size" class="hasTip" title="Title Size"><?php echo 'Title Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[single_title_size]" id="paramscube_single_title_size" value="<?php
						echo isset($param_values['single_title_size'])?$param_values['single_title_size']:'25'; ?>" class="text_area"  />px</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_text_color-lbl" for="paramscube_single_text_color" class="hasTip" title="Text Color"><?php echo 'Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_text_color]" type="text" class="sc_color" id="paramscube_single_text_color" value="<?php
							echo isset($param_values['single_text_color'])?$param_values['single_text_color']:'#797979'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_text_size-lbl" for="paramscube_single_text_size" class="hasTip" title="Text Size"><?php echo 'Text Size';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[single_text_size]" id="paramscube_single_text_size" value="<?php
						echo isset($param_values['single_text_size'])?$param_values['single_text_size']:'15'; ?>" class="text_area"  />px</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'link'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_link_color-lbl" for="paramscube_single_link_color" class="hasTip" title="Link Color"><?php echo 'Link Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_link_color]" type="text" class="sc_color" id="paramscube_single_link_color" value="<?php
							echo isset($param_values['single_link_color'])?$param_values['single_link_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_link_hover_color-lbl" for="paramscube_single_link_hover_color" class="hasTip" title="Link Hover Color"><?php echo 'Link Hover Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_link_hover_color]" type="text" class="sc_color" id="paramscube_single_link_hover_color" value="<?php
							echo isset($param_values['single_link_hover_color'])?$param_values['single_link_hover_color']:'#797979'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'parameters'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_cont_param_bg_color-lbl" for="paramscube_single_cont_param_bg_color" class="hasTip" title="Contact Parameters Background Color"><?php echo 'Contact Parameters Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_cont_param_bg_color]" type="text" class="sc_color" id="paramscube_single_cont_param_bg_color" value="<?php
							echo isset($param_values['single_cont_param_bg_color'])?$param_values['single_cont_param_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_cont_param_title_color-lbl" for="paramscube_single_cont_param_title_color" class="hasTip" title="Contact Parameters Title Color"><?php echo 'Contact Parameters Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_cont_param_title_color]" type="text" class="sc_color" id="paramscube_single_cont_param_title_color" value="<?php
							echo isset($param_values['single_cont_param_title_color'])?$param_values['single_cont_param_title_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_cont_param_text_color-lbl" for="paramscube_single_cont_param_text_color" class="hasTip" title="Contact Parameters Value Color"><?php echo 'Contact Parameters Value Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_cont_param_text_color]" type="text" class="sc_color" id="paramscube_single_cont_param_text_color" value="<?php
							echo isset($param_values['single_cont_param_text_color'])?$param_values['single_cont_param_text_color']:'#797979'; ?>" size="10" />
						</label>
					</td>
				</tr>

				<tr>
					<td class="paramlist_value admin_title"><?php echo 'message parameters'; ?></td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_mess_param_bg_color-lbl" for="paramscube_single_mess_param_bg_color" class="hasTip" title="Message Parameters Background Color"><?php echo 'Message Parameters Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_mess_param_bg_color]" type="text" class="sc_color" id="paramscube_single_mess_param_bg_color" value="<?php
							echo isset($param_values['single_mess_param_bg_color'])?$param_values['single_mess_param_bg_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_mess_param_title_color-lbl" for="paramscube_single_mess_param_title_color" class="hasTip" title="Message Parameters Title Color"><?php echo 'Message Parameters Title Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_mess_param_title_color]" type="text" class="sc_color" id="paramscube_single_mess_param_title_color" value="<?php
							echo isset($param_values['single_mess_param_title_color'])?$param_values['single_mess_param_title_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr><td class="admin_title" colspan="2"><?php echo 'button'; ?></td></tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_button_bg_color-lbl" for="paramscube_single_button_bg_color" class="hasTip" title="Button Background Color"><?php echo 'Button Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_button_bg_color]" type="text" class="sc_color" id="paramscube_single_button_bg_color" value="<?php
							echo isset($param_values['single_button_bg_color'])?$param_values['single_button_bg_color']:'#00A99D'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_button_hover_bg_color-lbl" for="paramscube_single_button_hover_bg_color" class="hasTip" title="Button Hover Background Color"><?php echo 'Button Hover Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_button_hover_bg_color]" type="text" class="sc_color" id="paramscube_single_button_hover_bg_color" value="<?php
							echo isset($param_values['single_button_hover_bg_color'])?$param_values['single_button_hover_bg_color']:'#797979'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_button_border_color-lbl" for="paramscube_single_button_border_color" class="hasTip" title="Button Border Color"><?php echo 'Button Border Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_button_border_color]" type="text" class="sc_color" id="paramscube_single_button_border_color" value="<?php
							echo isset($param_values['single_button_border_color'])?$param_values['single_button_border_color']:'#D9D9D9'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_button_text_color-lbl" for="paramscube_single_button_text_color" class="hasTip" title="Button Text Color"><?php echo 'Button Text Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_button_text_color]" type="text" class="sc_color" id="paramscube_single_button_text_color" value="<?php
							echo isset($param_values['single_button_text_color'])?$param_values['single_button_text_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="admin_title" colspan="2"><?php echo 'main options'; ?></td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_background_color-lbl" for="paramscube_single_background_color" class="hasTip" title="Background Color"><?php echo 'Background Color';?></label></span></td>
					<td class="paramlist_value">
						<label>
							<input name="params[single_background_color]" type="text" class="sc_color" id="paramscube_single_background_color" value="<?php
							echo isset($param_values['single_background_color'])?$param_values['single_background_color']:'#FFFFFF'; ?>" size="10" />
						</label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_image_width-lbl" for="paramscube_single_image_width" class="hasTip" title="Image Width on Middle Position"><?php echo 'Image Width on Middle Position';?></label></span></td>
					<td class="paramlist_value"><input type="text" name="params[single_image_width]" id="paramscube_single_image_width" value="<?php
						echo isset($param_values['single_image_width'])?$param_values['single_image_width']:'300'; ?>" class="text_area" size="4" />px</td>
				</tr>
				<tr>
					<?php
					$check0 = " checked='checked' ";
					$check1 = "";
					if (isset($param_values['single_image_middle']) && $param_values['single_image_middle'] == 1) { $check1 = ' checked="checked" '; $check0 = '';} ?>
					<td class="paramlist_key">
						<span class="editlinktip"><label id="paramsviewcontact_radius" for="paramsviewcontact_radius"><?php echo 'Image Position';?></label></span>
					</td>
					<td class="">
						<input type="radio" name="params[single_image_middle]" id="paramsviewsinglecontact_radius0" value="0" <?php echo isset($check0)?$check0:''; ?>/>
							<label for="paramsviewsinglecontact_radius0"><?php echo 'Middle';?></label>
						<input type="radio" name="params[single_image_middle]" id="paramsviewsinglecontact_radius1" value="1" <?php echo isset($check1)?$check1:''; ?>/>
							<label for="paramsviewsinglecontact_radius1"><?php echo 'Left';?></label>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><span class="editlinktip"><label id="paramscube_single_no_image-lbl" for="paramscube_single_no_image" class="hasTip" title="No Image"><?php echo 'No Image';?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="text" id="paramscube_single_no_image"  class="upload" name="params[single_no_image]" value="<?php echo isset($param_values['single_no_image'])?$param_values['single_no_image']:''; ?>" />
						<input class="upload-button sc_upload-button" type="button" value="<?php _e('Upload Image', $contLDomain); ?>"/>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<input type="hidden" name="activeAdminTab" id="activeAdminTab"/>

	<a id="go_to_top" href="#" title="Back to top"><?php echo 'Go Top'; ?></a>
</div>

<script type="text/javascript">
	jQuery(document).ready( function() {
		jQuery('.meta-box-sortables').sortable({
			disabled: true
		});
		jQuery('.postbox .hndle').css('cursor', 'pointer');

		jQuery("#cont_theme_ul li").click(function() {
			var active_index = jQuery(this).index();
			jQuery('#activeAdminTab').val(active_index);
		});

		/*- show_hide -*/
		jQuery(".staff_main_title").click(function() {
			jQuery(".staff_view").slideToggle("slow");
		});

		jQuery(".staff_main_title_popup").click(function() {
			jQuery( ".staff_view_popup" ).slideToggle("slow");
		});
	});
</script>