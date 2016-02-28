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
<a href="/admin/promote/add" class="button"><?php echo Text::_('Nuevo destacado'); ?></a>

<div class="widget board">
    <?php if (!empty($this['promoted'])) : ?>
    <table>
        <thead>
            <tr>
                <th class="view_btn"></th> <!-- preview -->
                <th><?php echo Text::_("Proyecto"); ?></th> <!-- title -->
                <th class="support_status"><?php echo Text::_("Estado"); ?></th> <!-- status -->
                <th><?php echo Text::_('Posición'); ?></th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- On/Off --></th>
                <th><!-- Traducir--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['promoted'] as $promo) : ?>
            <tr>
                <td><a href="/project/<?php echo $promo->project; ?>" target="_blank" title="<?php echo Text::get(''); ?>">[<?php echo Text::_("Ver"); ?>]</a></td>
                <td class="pj_name"><?php echo ($promo->active) ? '<strong>'.$promo->name.'</strong>' : $promo->name; ?></td>
                <td><?php echo $promo->status; ?></td>
                <td><?php echo $promo->order; ?></td>
                <td><a href="/admin/promote/up/<?php echo $promo->id; ?>">[&uarr;]</a></td>
                <td><a href="/admin/promote/down/<?php echo $promo->id; ?>">[&darr;]</a></td>
                <td><a href="/admin/promote/edit/<?php echo $promo->id; ?>">[<?php echo Text::_("Editar"); ?>]</a></td>
                <td><?php if ($promo->active) : ?>
                <a href="/admin/promote/active/<?php echo $promo->id; ?>/off">[<?php echo Text::_("Ocultar"); ?>]</a>
                <?php else : ?>
                <a href="/admin/promote/active/<?php echo $promo->id; ?>/on">[<?php echo Text::_("Mostrar"); ?>]</a>
                <?php endif; ?></td>
                <?php if ($translator) : ?>
                <td><a href="/translate/promote/edit/<?php echo $promo->id; ?>" >[<?php echo Text::_("Traducir"); ?>]</a></td>
                <?php endif; ?>
                <td><a href="/admin/promote/remove/<?php echo $promo->id; ?>" onclick="return confirm('<?php echo Text::_("Seguro que deseas eliminar este registro?"); ?>');">[<?php echo Text::_("Quitar"); ?>]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p><?php echo Text::_("No se han encontrado registros"); ?></p>
    <?php endif; ?>
</div>