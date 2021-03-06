<?php
//  ------------------------------------------------------------------------ //
//  mcmsimplecms                                                             //
//  MCM Web Solutions, LLC                                                   //
//  http://www.mcmwebsite.com                                                //
//  9/17/2015                                                                //
//  v. 0.4                                                                   //
//                                                                           //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License version 2 as        //
//  published by the Free Software Foundation;                               //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//   see gpl-2.0.txt or http://gnu.org                                       //
//  ------------------------------------------------------------------------ //

  include "include/app_top.php"; // include for config/db connect/etc.

  if ( isset($_GET["act"]) )
     $pageName = $_GET["act"];
  else
     $pageName = 'index';

  $metaTitle = getMetaAndTitle($pageName);

  include "header.php"; // include for top html

  if ($config['USE_LEFT_MENU'])
     include 'include/leftMenu.php';
  else
     include "include/topMenu.php";
?>
  <?=stripslashes(getContent($pageName))?>
<?php

  include "footer.php"; // include for bottom html

?>