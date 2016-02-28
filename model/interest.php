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


namespace Goteo\Model {

    class Interest extends \Goteo\Model\Category {

        public
            $id,
            $name,
            $description,
            $used; // numero de usuarios que tienen este interés

        /*
         * Lista de intereses para usuarios
         */
        public static function getAll() {

            $list = array();

            if(User::iAmRoot()){
                $nodeQuery1 = '';
                $nodeQuery2 = '';
            }else{
                $nodeQuery1 = 'INNER JOIN user_login_log ON user_interest.user = user_login_log.user';
                $nodeQuery2 = "AND user_login_log.node = '" . LG_PLACE_NAME . "'";
            }

            $sql = "
                SELECT
                  category.id                                             AS id,
                  IFNULL(category_lang.name, category.name)               AS name,
                  IFNULL(category_lang.description, category.description) AS description,
                  (SELECT COUNT(user_interest.user)
                   FROM user_interest
                     $nodeQuery1
                   WHERE user_interest.interest = category.id
                         $nodeQuery2
                  )                                                       AS used,
                  category.order                                          AS `order`
                FROM category
                  LEFT JOIN category_lang
                    ON category_lang.id = category.id
                       AND category_lang.lang = :lang
                ORDER BY `order` ASC";

            $query = static::query( $sql, array( ':lang' => \LANG ) );

            foreach ( $query->fetchAll( \PDO::FETCH_CLASS, __CLASS__ ) as $interest ) {
                if ( $interest->id == 15 ) {
                    continue;
                }
                $list[ $interest->id ] = $interest;
            }

            return $list;
        }

    }

}


/**
 *
 * use Goteo\Library\Check;
 *
 *
 * //  Devuelve datos de un interés
 * public static function get ($id) {
 * $query = static::query("
 * SELECT
 * id,
 * name,
 * description
 * FROM    interest
 * WHERE id = :id
 * ", array(':id' => $id));
 * $interest = $query->fetchObject(__CLASS__);
 *
 * return $interest;
 * }
 *
 * public function validate (&$errors = array()) {
 * if (empty($this->name))
 * $errors[] = Text::_('Falta nombre');
 *
 * if (empty($errors))
 * return true;
 * else
 * return false;
 * }
 *
 * public function save (&$errors = array()) {
 * if (!$this->validate($errors)) return false;
 *
 * $fields = array(
 * 'id',
 * 'name',
 * 'description'
 * );
 *
 * $set = '';
 * $values = array();
 *
 * foreach ($fields as $field) {
 * if ($set != '') $set .= ", ";
 * $set .= "`$field` = :$field ";
 * $values[":$field"] = $this->$field;
 * }
 *
 * try {
 * $sql = "REPLACE INTO interest SET " . $set;
 * self::query($sql, $values);
 * if (empty($this->id)) $this->id = self::insertId();
 *
 * return true;
 * } catch(\PDOException $e) {
 * $errors[] = Text::_("No se ha guardado correctamente. ") . $e->getMessage();
 * return false;
 * }
 * }
 *
 * // Para quitar un interes de la tabla
 * public static function delete ($id) {
 *
 * $sql = "DELETE FROM interest WHERE id = :id";
 * if (self::query($sql, array(':id'=>$id))) {
 * return true;
 * } else {
 * return false;
 * }
 *
 * }
 *
 * // Para que salga antes  (disminuir el order)
 * public static function up ($id) {
 * return Check::reorder($id, 'up', 'interest', 'id', 'order');
 * }
 *
 * // Para que salga despues  (aumentar el order)
 * public static function down ($id) {
 * return Check::reorder($id, 'down', 'interest', 'id', 'order');
 * }
 *
 * // Orden para añadirlo al final
 * public static function next () {
 * $query = self::query('SELECT MAX(`order`) FROM interest');
 * $order = $query->fetchColumn(0);
 * return ++$order;
 *
 * }
 */