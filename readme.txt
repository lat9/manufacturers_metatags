Instalación Manual (Recomendable)--> 
Instalation manual(Recommended)

1-->Ejecutar la query de install.txt  (Execute query)

CREATE TABLE IF NOT EXISTS `meta_tags_manufacturers_description` (
  `manufacturers_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL default '1',
  `metatags_title` varchar(255) NOT NULL default '',
  `metatags_keywords` text,
  `metatags_description` text,
  PRIMARY KEY  (`manufacturers_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


2-->poner este código en Meta-tags.php  (put the code)
	  
Fichero: /includes/modules/meta-tags.php	  

Linea Aprox:157 a 162

	      ///BOF-->Julian cortes Meta -tags manufacturers-modificacion
		  $manufacturer_metatag= $db->Execute("select *
                               from ".TABLE_MANUFACTURERS_META.
                               " where manufacturers_id = '" . (int)$_GET['manufacturers_id']. "'
                               and language_id = '" . (int)$_SESSION['languages_id'] . "'");
          if ($manufacturer_metatag->RecordCount() > 0) {
		            define('META_TAG_TITLE', str_replace('"','',$manufacturer_metatag->fields['metatags_title']));
					define('META_TAG_DESCRIPTION', str_replace('"','',$manufacturer_metatag->fields['metatags_description']));
					define('META_TAG_KEYWORDS', str_replace('"','',$manufacturer_metatag->fields['metatags_keywords']));
          }else{
	      ////EOF Julian MOdificacion Meta-tags manufacturer
		  .
		  .
		  .
Linea Aprox: 184		  
		 ///BOF-->Julian cortes Meta -tags manufacturers-modificacion
	  	  }
	     ////EOF Julian MOdificacion Meta-tags manufacturer  
		   

		queda Así(Total)
		
		
		 if (isset($_GET['manufacturers_id'])) {
        ///BOF-->Julian cortes Meta -tags manufacturers-modificacion
		  $manufacturer_metatag= $db->Execute("select *
                               from ".TABLE_MANUFACTURERS_META.
                               " where manufacturers_id = '" . (int)$_GET['manufacturers_id']. "'
                               and language_id = '" . (int)$_SESSION['languages_id'] . "'");
          if ($manufacturer_metatag->RecordCount() > 0) {
		            define('META_TAG_TITLE', str_replace('"','',$manufacturer_metatag->fields['metatags_title']));
					define('META_TAG_DESCRIPTION', str_replace('"','',$manufacturer_metatag->fields['metatags_description']));
					define('META_TAG_KEYWORDS', str_replace('"','',$manufacturer_metatag->fields['metatags_keywords']));
          }else{
	       ////EOF Julian MOdificacion Meta-tags manufacturer
				$sql = "select manufacturers_name from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "'";
				$manufacturer_metatags = $db->Execute($sql);
				if ($manufacturer_metatags->EOF) {
			define('META_TAG_TITLE', TITLE . TAGLINE);
			define('META_TAG_DESCRIPTION', PRIMARY_SECTION . str_replace(array("'",'"'),'',strip_tags(HEADING_TITLE)) . SECONDARY_SECTION . KEYWORDS);
			define('META_TAG_KEYWORDS', KEYWORDS . METATAGS_DIVIDER . str_replace(array("'",'"'),'',strip_tags(HEADING_TITLE)));
				} else {
					define('META_TAG_TITLE', str_replace('"','', $manufacturer_metatags->fields['manufacturers_name'] . PRIMARY_SECTION . TITLE . TAGLINE));
					define('META_TAG_DESCRIPTION', str_replace('"','',PRIMARY_SECTION . $manufacturer_metatags->fields['manufacturers_name'] . SECONDARY_SECTION . KEYWORDS));
			define('META_TAG_KEYWORDS', str_replace('"','', $manufacturer_metatags->fields['manufacturers_name'] . METATAGS_DIVIDER . KEYWORDS));
				}
      ///BOF-->Julian cortes Meta -tags manufacturers-modificacion
	  }
	    ////EOF Julian MOdificacion Meta-tags manufacturer		   


3-->MODIFICACION manufacturers.php
Fichero : /admin/manufacturers.php

Linea 124

	///MODIFICACION JULIAN CORTES ANTON
		   // bof: categories meta tags
      case 'update_manufacturer_meta_tags':
      // add or update meta tags
      //die('I SEE ' . $action . ' - ' . $_POST['categories_id']);
      $manufacturers_id = $_POST['manufacturer_id'];
      //$manufacturers_id= zen_db_prepare_input($_GET['mID']) ;
	  $languages = zen_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $language_id = $languages[$i]['id'];
        $check = $db->Execute("select *
                               from ".TABLE_MANUFACTURERS_META.
                               " where manufacturers_id = '" . (int)$manufacturers_id . "'
                               and language_id = '" . (int)$language_id . "'");
        if ($check->RecordCount() > 0) {
          $action = 'update_manufacturers_meta_tags';
        } else {
          $action = 'insert_manufacturers_meta_tags';
        }
        $sql_data_array = array('metatags_title' => zen_db_prepare_input($_POST['metatags_title'][$language_id]),
                                'metatags_keywords' => zen_db_prepare_input($_POST['metatags_keywords'][$language_id]),
                                'metatags_description' => zen_db_prepare_input($_POST['metatags_description'][$language_id]));

        if ($action == 'insert_manufacturers_meta_tags') {
          $insert_sql_data = array('manufacturers_id' => $manufacturers_id,
                                   'language_id' => $language_id);
          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          zen_db_perform(TABLE_MANUFACTURERS_META, $sql_data_array);
        } elseif ($action == 'update_manufacturers_meta_tags') {
          zen_db_perform(TABLE_MANUFACTURERS_META, $sql_data_array, 'update', "manufacturers_id = '" . (int)$manufacturers_id . "' and language_id = '" . (int)$language_id . "'");
        }
      }
       zen_redirect(zen_href_link(FILENAME_MANUFACTURERS, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'mID=' . $manufacturers_id));
      //zen_redirect(zen_href_link(FILENAME_MANUFACTURERS, 'cPath=' . $cPath . '&cID=' . $manufacturers_id));
      break;
  	 ///BOF-->MODIFICACION JULIAN CORTES ANTON Meta-tags  


Aprox-->linea 174

  //Bof-->Julian cortes anton Modificacion--->Meta-tags manufacturers
    case 'edit_manufacturer_meta_tags':
	 $manufacturers_id = zen_db_prepare_input($_GET['mID']);
    $heading[] = array('text' => '<strong>' . TEXT_INFO_HEADING_EDIT_MANUFACTURER_META_TAGS . '</strong>');
    $contents = array('form' => zen_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'action=update_manufacturer_meta_tags&mID=' . $manufacturers_id, 'post', 'enctype="multipart/form-data"') . zen_draw_hidden_field('manufacturer_id', $manufacturers_id ));
    $contents[] = array('text' => TEXT_EDIT_MANUFACTURER_META_TAGS_INTRO . ' - <strong>' . $manufacturers_id  . ' ' . $mInfo->manufacturers_name . '</strong>');

    $languages = zen_get_languages();

    $manufacturer_inputs_string_metatags_title = '';
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		
      $manufacturer_inputs_string_metatags_title .= '<br />' . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['metatags_title']) . '&nbsp;' . zen_draw_input_field('metatags_title[' . $languages[$i]['id'] . ']', zen_get_manufacturer_metatags_manu_title($manufacturers_id , $languages[$i]['id']), zen_set_field_length("TABLE_MANUFACTURERS_META", 'metatags_title'));
    }
	$contents[] = array('text' => '<br />' . TEXT_EDIT_MANUFACTURER_META_TAGS_TITLE . $manufacturer_inputs_string_metatags_title);

    $manufacturer_inputs_string_metatags_keywords = '';
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $manufacturer_inputs_string_metatags_keywords .= '<br />' . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['metatags_keywords']) . '&nbsp;' ;
      $manufacturer_inputs_string_metatags_keywords .= zen_draw_textarea_field('metatags_keywords[' . $languages[$i]['id'] . ']', 'soft', '100%', '20', zen_get_manufacturer_metatags_manu_keywords($manufacturers_id , $languages[$i]['id']));
    }
    $contents[] = array('text' => '<br />' . TEXT_EDIT_MANUFACTURER_META_TAGS_KEYWORDS . $manufacturer_inputs_string_metatags_keywords);

    $manufacturer_inputs_string_metatags_description = '';
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $manufacturer_inputs_string_metatags_description .= '<br />' . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' ;
      $manufacturer_inputs_string_metatags_description .= zen_draw_textarea_field('metatags_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '20', zen_get_manufacturer_metatags_manu_description($manufacturers_id , $languages[$i]['id']));
    }
$contents[] = array('text' => '<br />' . TEXT_EDIT_MANUFACTURERS_META_TAGS_DESCRIPTION.$manufacturer_inputs_string_metatags_description);

    $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . zen_href_link(FILENAME_MANUFACTURERS, '&mID=' . $manufacturers_id ) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
    break;
    ///EOF-> JULIAN cortes Modificacion--->Meta-tags manufacturers
	
	
Linea 185: 
	<!--BOFJulian cortes Modificacion Meta manufacturers-->
	<?php if ($action != 'edit_category_meta_tags') { // bof: manufacturer meta tags ?>
	<?php if ($editor_handler != '') include ($editor_handler); ?>
	<?php } // meta tags disable editor eof: manufacturer meta tags?>
	<!--EOF--Fin Julian cortes-->


Linea 242 :

 <!--BOFJULIAN CORTES MODIFICACION Meta-tags-manufacturers-->
				<? if (zen_get_manufacturer_metatags_manu_keywords($manufacturers->fields['manufacturers_id'] , (int)$_SESSION['languages_id']) or zen_get_manufacturer_metatags_manu_description($manufacturers->fields['manufacturers_id'] , (int)$_SESSION['languages_id'])) {?>
        <?php echo '<a href="' . zen_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers->fields['manufacturers_id'] . '&action=edit_manufacturer_meta_tags') . '">' . zen_image(DIR_WS_IMAGES . 'icon_edit_metatags_on.gif', ICON_METATAGS_ON) . '</a>'; ?>
        <?} else {?>
        <?php echo '<a href="' . zen_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers->fields['manufacturers_id'] . '&action=edit_manufacturer_meta_tags') . '">' . zen_image(DIR_WS_IMAGES . 'icon_edit_metatags_off.gif', ICON_METATAGS_OFF) . '</a>'; 
		}?>
	<!--EOF --fin Modificacion Meta-tags-manufacturers-->
