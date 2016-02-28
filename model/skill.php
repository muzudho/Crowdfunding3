<?php
namespace Goteo\Model {

    use Goteo\Library\Check;
    
    class Skill extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $description,
            $parent_skill_id,
            $used; // numero de proyectos que usan la skill

        /*
         *  Devuelve datos de una skill
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        skill.id,
                        IFNULL(skill_lang.name, skill.name) as name,
                        IFNULL(skill_lang.description, skill.description) as description,
                        skill.parent_skill_id
                    FROM    skill
                    LEFT JOIN skill_lang
                        ON  skill_lang.id = skill.id
                        AND skill_lang.lang = :lang
                    WHERE skill.id = :id
                    ", array(':id' => $id, ':lang'=>\LANG));
                $skill = $query->fetchObject(__CLASS__);

                return $skill;
        }

        /*
         * Lista de skills para proyectos
         * @TODO añadir el numero de usos
         */
        public static function getAll () {

            $list = array();

            if(User::iAmRoot()){
                $nodeQuery1 = '';
                $nodeQuery2 = '';
            }else{
                $nodeQuery1 = 'INNER JOIN user_login_log ON user_skill.user = user_login_log.user';
                $nodeQuery2 = "AND user_login_log.node = '" . LG_PLACE_NAME . "'";
            }

            $sql = "
                SELECT
                    skill.id as id,
                    IFNULL(skill_lang.name, skill.name) as name,
                    IFNULL(skill_lang.description, skill.description) as description,
                    (   SELECT 
                            COUNT(project_skill.project)
                        FROM project_skill
                        WHERE project_skill.skill = skill.id
                    ) as numProj,
                    (   SELECT
                            COUNT(user_skill.user)
                        FROM user_skill
                          $nodeQuery1
                        WHERE user_skill.skill = skill.id
                        $nodeQuery2
                    ) as numUser,
                    skill.order as `order`,
                    skill.parent_skill_id as parent_skill_id
                FROM    skill
                LEFT JOIN skill_lang
                    ON  skill_lang.id = skill.id
                    AND skill_lang.lang = :lang
                ORDER BY `parent_skill_id` ASC, `order` ASC
                ";

            $query = static::query($sql, array( ':lang'=>\LANG ));

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $skill) {
                $list[$skill->id] = $skill;
            }
            foreach ($list as $key => $value) {
                if(!empty($value->parent_skill_id)){
                    $temp[$value->parent_skill_id][$key] = $value;
                }else{
                    $temp[$value->id][0] = $value;
                }
            }
            unset($list);
            foreach ($temp as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    $list[$value2->id] = $value2;
                }
            }

            return $list;
        }
        public static function getAllParent () {

            $list = array();

            if(User::iAmRoot()){
                $nodeQuery1 = '';
                $nodeQuery2 = '';
            }else{
                $nodeQuery1 = 'INNER JOIN user_login_log ON user_skill.user = user_login_log.user';
                $nodeQuery2 = "AND user_login_log.node = '" . LG_PLACE_NAME . "'";
            }

            $sql = "
                SELECT
                    skill.id as id,
                    IFNULL(skill_lang.name, skill.name) as name,
                    IFNULL(skill_lang.description, skill.description) as description,
                    (   SELECT 
                            COUNT(project_skill.project)
                        FROM project_skill
                        WHERE project_skill.skill = skill.id
                    ) as numProj,
                    (   SELECT
                            COUNT(user_skill.user)
                        FROM user_skill
                          $nodeQuery1
                        WHERE user_skill.skill = skill.id
                        $nodeQuery2
                    ) as numUser,
                    skill.order as `order`,
                    skill.parent_skill_id as parent_skill_id
                FROM    skill
                LEFT JOIN skill_lang
                    ON  skill_lang.id = skill.id
                    AND skill_lang.lang = :lang
                WHERE skill.parent_skill_id IS NULL
                ORDER BY `parent_skill_id` ASC, `order` ASC
                ";

            $query = static::query($sql, array(':lang'=>\LANG));

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $skill) {
                $list[$skill->id] = $skill;
            }

            return $list;
        }

        /**
         * Get all skills used in published projects
         *
         * @param void
         * @return array
         */
		public static function getList () {
            $array = array ();
            try {
                $sql = "SELECT 
                            skill.id as id,
                            IFNULL(skill_lang.name, skill.name) as name
                        FROM skill
                        LEFT JOIN skill_lang
                            ON  skill_lang.id = skill.id
                            AND skill_lang.lang = :lang
                        GROUP BY skill.id
                        ORDER BY skill.order ASC";

                $query = static::query($sql, array(':lang'=>\LANG));
                $skills = $query->fetchAll();
                foreach ($skills as $cat) {
                    // la 15 es de testeos
                    if ($cat[0] == 15) continue;
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        
        public function validate (&$errors = array()) { 
            if (empty($this->name))
                $errors[] = Text::_('Falta nombre');

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'name',
                'description',
                'parent_skill_id'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
                if($field == 'parent_skill_id'){
                    if(empty($this->$field)){
                        $values[":$field"] = null;
                    }
                }
            }

            try {
                $sql = "REPLACE INTO skill SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = Text::_("No se ha guardado correctamente. ") . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una catgoria de la tabla
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM skill WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id) {
            return self::reorder($id, 'up', 'id');
        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id) {
            return self::reorder($id, 'down', 'id');
        }

        public static function reorder($idReg, $updown) {

            //uso el modelo core para hacer los querys
            $model = '\Goteo\Core\Model';
            $regs = array();

            $skill = self::get($idReg);

            if(!empty($skill->parent_skill_id)){
                $sql = "SELECT `id` FROM `skill` WHERE parent_skill_id IS NOT NULL AND parent_skill_id = :parent_skill_id ORDER  BY `order` ASC";   
            }else{
                $sql = "SELECT `id` FROM `skill` WHERE parent_skill_id is null ORDER  BY `order` ASC";
            }
            if ($query = $model::query($sql,array(':parent_skill_id'=>$skill->parent_skill_id))) {
                $order = 10;
                while ($row = $query->fetchObject()) {
                    $regs[$row->id] = $order;
                    $order+=10;
                }

                //al elemento target cambiarle segun 'up'-5  'down'+5
                if ($updown == 'up') {
                    $regs[$idReg] -= 15;
                } elseif ($updown == 'down') {
                    $regs[$idReg] += 15;
                }
                //reordenar array
                \asort($regs);

                // hacer updates segun el nuevo orden en una transaccion
                try {
                    $model::query("START TRANSACTION");
                    $order = 1;
                    foreach ($regs as $id=>$ordenquenoponemos) {
                        $sql = "UPDATE `skill` SET `order`=:order WHERE id = :id";
                        $query = $model::query($sql, array(':order'=>$order, ':id'=>$id));
                        $order++;
                    }
                    $model::query("COMMIT");

                    return true;
                } catch(\PDOException $e) {
                    return false;
                }
            }

        }

        /*
         * Orden para añadirlo al final
         */
        public static function next () {
            $query = self::query('SELECT MAX(`order`) FROM skill');
            $order = $query->fetchColumn(0);
            return ++$order;

        }
        
        /**
         * Get a list of used keywords
         *
         * can be of users, projects or  all
         * 
         */
		public static function getKeyWords () {
            $array = array ();
            try {
                
                $sql = "SELECT 
                            keywords
                        FROM project
                        WHERE status > 1
                        AND keywords IS NOT NULL
                        AND keywords != ''
                        ";
/*
                     UNION
                        SELECT 
                            keywords
                        FROM user
                        WHERE keywords IS NOT NULL
                        AND keywords != ''
* 
 */
                $query = static::query($sql);
                $keywords = $query->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($keywords as $keyw) {
                    $kw = $keyw['keywords'];
//                    $kw = str_replace('|', ',', $keyw['keywords']);
//                    $kw = str_replace(array(' ','|'), ',', $keyw['keywords']);
//                    $kw = str_replace(array('-','.'), '', $kw);
                    $kwrds = explode(',', $kw);
                    
                    foreach ($kwrds as $word) {
                        $array[] = strtolower(trim($word));
                    }
                }

                asort($array);
                
                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        public static function getChildren ($parent_id = 0) {
            $array = array ();
            try {

//                $sql = "SELECT
//                            id
//                        FROM skill
//                        WHERE
//                        parent_skill_id IS NOT NULL
//                        AND parent_skill_id > 0
//                        AND parent_skill_id = :parent_id
//                        ";

                $sql = "SELECT `id` FROM `skill` WHERE parent_skill_id IS NOT NULL AND parent_skill_id = :parent_id ORDER  BY `order` ASC";
                $query = static::query($sql, array(':parent_id'=>$parent_id));
                $array = $query->fetchAll(\PDO::FETCH_ASSOC);
//                foreach ($keywords as $keyw) {
//                    $kw = $keyw['keywords'];
////                    $kw = str_replace('|', ',', $keyw['keywords']);
////                    $kw = str_replace(array(' ','|'), ',', $keyw['keywords']);
////                    $kw = str_replace(array('-','.'), '', $kw);
//                    $kwrds = explode(',', $kw);
//
//                    foreach ($kwrds as $word) {
//                        $array[] = strtolower(trim($word));
//                    }
//                }

                asort($array);

                return $array;
            } catch(\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }



    }
    
}