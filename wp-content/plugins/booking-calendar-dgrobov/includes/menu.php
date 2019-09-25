 <div class="menu">

  <?php
    foreach ($menuList as $key => $value) {
        print "<div class=\"item\"><a href=\"".$value['menu_link']."\">".(!empty($value['menu_icon'])?"<img src=\"".$value['menu_icon']."\" border=\"0\" /><br />":"").$value['menu_title']."</a></div>";
    }
  ?>
  <br clear="left" /><br />
 </div>
 
  