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
    Goteo\Core\ACL;

$translator = ACL::check('/translate') ? true : false;
?>
<?php if (!isset($_SESSION['admin_node'])) : ?>
<a href="/admin/pages/add" class="button"><?php echo Text::_('Nueva P&aacute;gina'); ?></a>
<?php endif; ?>

<div class="widget board">
    <?php if (!empty($this['pages'])) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th class="t_title"><?php echo Text::_('Páginas'); ?></th>
                <th><?php echo Text::_('Descripción'); ?></th>
                <th><!-- Abrir --></th>
                <th><!-- Traducir --></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this['pages'] as $page) : ?>
            <tr>
                <td><a href="/admin/pages/edit/<?php echo $page->id; ?>">[<?php echo Text::_("Editar"); ?>]</a></td>
                <td class="w300"><?php echo $page->name; ?></td>
                <td class="w300"><?php echo $page->description; ?></td>
                <td><a href="<?php echo $page->url; ?>" target="_blank">[<?php echo Text::_("Ver"); ?>]</a></td>
                <td>
                <?php if ($translator && $node == \GOTEO_NODE) : ?>
                <a href="/translate/pages/edit/<?php echo $page->id; ?>" >[<?php echo Text::_("Traducir"); ?>]</a>
                <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p><?php echo Text::_("No se han encontrado registros"); ?></p>
    <?php endif; ?>
</div>