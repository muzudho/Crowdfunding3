<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *	This file is part of Goteo.
 *
 *  Goteo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Goteo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Goteo.  If not, see <http://www.gnu.org/licenses/agpl.txt>.
 *
 */

use Goteo\Library\Text,
    Goteo\Model\Category,
    Goteo\Model\Post,
    Goteo\Model\Sponsor;
//@NODESYS

$lang = (LANG != 'es') ? '?lang='.LANG : '';

$categories = Category::getList();  // categorias que se usan en proyectos
$posts      = Post::getList('footer');
$sponsors   = Sponsor::getList();
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('.scroll-pane').jScrollPane({showArrows: true});
});
</script>

    <!-- <div id="footer"> -->
    <div class="footer">

        <div class="footer_link_wrapper">
            <div id="to_page_top" style="bottom:0; margin-left: 880px;">
                <a href="#page_top"><img src="/view/css/page_topBtn.png" alt="ページの上部へ" /></a>
            </div><!--#to_page_top-->
            <div class="inner cf">
                <ul>
                    <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/about/">LOCAL GOODについて</a></li>
                    <li><a href="<?= LG_INTEGRATION_URL ?>/riyou_kiyaku_menu/">利用規約</a></li>
                    <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/syoutorihikihou/">特定商取引法に基づく表記</a></li>
                    <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/user_guide/">ユーザーガイド</a></li>
                    <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/privacypolicy/">プライバシーポリシー</a></li>
                    <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/mailnews/">メルマガ登録</a></li>
                    <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/contact/">お問い合わせ</a></li>
                    <li class="integration"><a href="<?= LG_INTEGRATION_URL ?>/">LOCAL GOOD全国版トップページ</a></li>
                </ul>
            </div>
        </div>
        <div class="foot_bar_wrapper">
            <div class="foot_bar_inner cf">
                <a class="three_left" href="http://yokohamalab.jp/" target="_blank"><img src="/view/css/ycdl_logo.png" alt="Yokohama Community Design Lab." /></a>
                <a class="three_center" href="http://www.accenture.com/jp-ja/Pages/index.aspx" target="_blank"><img src="/view/css/accenture_logo.png" alt="accenture" /></a>
                <img class="three_right" src="/view/css/open_yokohama_logo.png" alt="Open Yokohama" />
            </div>
            <p class="copyright">
                <span>&copy; LOCAL GOOD YOKOHAMA. Some rights reserved.</span>
            </p>
            <div class="platoniq">
                <?php // You are not allowed to remove this links. If so, you'll make a legal fault regarding the release license. You can read it at https://github.com/Goteo/Goteo/blob/master/GNU-AGPL-3.0 ?>
                <span class="text"><a href="http://goteo.org" target="_blank" class="poweredby">Powered by Goteo.org</a></span>
                <?php // You are not allowed to remove this links. If so, you'll make a legal fault regarding the release license. You can read it at https://github.com/Goteo/Goteo/blob/master/GNU-AGPL-3.0 ?>
                <span class="logo"><a href="http://fuentesabiertas.org" target="_blank" class="foundation">FFA</a></span>
                <?php // You are not allowed to remove this links. If so, you'll make a legal fault regarding the release license. You can read it at https://github.com/Goteo/Goteo/blob/master/GNU-AGPL-3.0 ?>
                <span class="logo"><a href="https://github.com/Goteo/Goteo" target="_blank" class="growby">GNU-AGPL-3</a></span>
                <?php // You are not allowed to remove this links. If so, you'll make a legal fault regarding the release license. You can read it at https://github.com/Goteo/Goteo/blob/master/GNU-AGPL-3.0 ?>
            </div>

        </div>
    </div>


