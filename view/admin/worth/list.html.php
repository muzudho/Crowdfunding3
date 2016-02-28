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

use Goteo\Library\Text;

?>
<div class="widget board">
    <?php if (!empty($this['worthcracy'])) : ?>
    <table>
        <thead>
            <tr>
                <th class="edit_btn"><!-- Editar--></th>
                <th class="level"><?php echo Text::_('Nivel'); ?></th>
                <th class="total_support"><?php echo Text::_('Caudal'); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['worthcracy'] as $worth) : ?>
            <tr>
                <td><a href="/admin/worth/edit/<?php echo $worth->id; ?>">[<?php echo Text::_("Editar"); ?>]</a></td>
                <td><?php echo $worth->name; ?></td>
                <td><?php echo $worth->amount . Text::_('yen'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p><?php echo Text::_('IMPOSIBLE!!! No se han encontrado registros'); ?>:</p>
    <?php endif; ?>
</div>