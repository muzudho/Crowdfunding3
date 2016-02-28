<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *    This file is part of Goteo.
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


namespace Goteo\Model\User {

    use Goteo\Model\User,
        Goteo\Library\Text;

    class Web extends \Goteo\Core\Model {

        public
            $id,
            $user,
            $url;


        /**
         * Get the interests for a user
         * @param varcahr(50) $id  user identifier
         * @return array of interests identifiers
         */
        public static function get ($id) {
            $list = array();

            if(User::iAmRoot()){
                $nodeQuery1 = '';
                $nodeQuery2 = '';
            }else{
                $nodeQuery1 = 'INNER JOIN user_login_log ON user_web.user = user_login_log.user';
                $nodeQuery2 = "AND user_login_log.node = '" . LG_PLACE_NAME . "'";
            }

            try {
                $query = static::query("
                SELECT id, user_web.user, url FROM user_web
                $nodeQuery1
                WHERE user_web.user = :id
                $nodeQuery2", array( ':id' => $id ));
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $web) {
                    if (\substr($web->url, 0, 4) != 'http') {
                        $web->url = 'http://'.$web->url;
                    }
                    $list[] = $web;
                }
                return $list;
            } catch(\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        public function validate(&$errors = array()) {}

        /*
         *  Guarda las webs del usuario
         */
        public function save (&$errors = array()) {

            $values = array(':user'=>$this->user, ':id'=>$this->id, ':url'=>$this->url);

            try {
                if(User::hasLoginHere($this->user)){
                    $sql = "REPLACE INTO user_web (id, user, url) VALUES(:id, :user, :url)";
                    self::query($sql, $values);
                    return true;
                }else{
                    return false;
                }
            } catch(\PDOException $e) {
                $errors[] = Text::_("No se ha guardado correctamente. ") . $e->getMessage();
                return false;
            }

        }

        /**
         * Quitar una palabra clave de un proyecto
         *
         * @param varchar(50) $user id de un proyecto
         * @param INT(12) $id  identificador de la tabla keyword
         * @param array $errors
         * @return boolean
         */
        public function remove (&$errors = array()) {
            $values = array (
                ':user'=>$this->user,
                ':id'=>$this->id,
            );

            try {
                if(User::hasLoginHere($this->user)){
                    self::query("DELETE FROM user_web WHERE id = :id AND user = :user", $values);
                    return true;
                }else{
                    return false;
                }
            } catch(\PDOException $e) {
                $errors[] = Text::_('No se ha podido quitar la web ') . $this->id . Text::_(' del usuario ') . $this->user . ' ' . $e->getMessage();
                return false;
            }
        }

    }

}