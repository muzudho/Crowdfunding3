<?php


namespace Goteo\Model\User {

    use Goteo\Model\Image;

    class Skill extends \Goteo\Model\Skill {

        public
            $id,
            $user;


        /**
         * Get the skills for a user
         * @param varcahr(50) $id  user identifier
         * @return array of skills identifiers
         */
	 	public static function get ($id) {
            $array = array ();
            try {
                $query = static::query("SELECT skill FROM user_skill WHERE user = ?", array($id));
                $skills = $query->fetchAll();
                foreach ($skills as $int) {
                    $array[$int[0]] = $int[0];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all skills available
         *
         *
         * @param user isset get all skills of a user
         * @return array
         */
		public static function getAll ($user = null,$child=false) {
            $array = array ();
            try {
                $values = array(':lang'=>\LANG);
                $sql = "
                    SELECT
                        skill.id as id,
                        IFNULL(skill_lang.name, skill.name) as name
                    FROM    skill
                    LEFT JOIN skill_lang
                        ON  skill_lang.id = skill.id
                        AND skill_lang.lang = :lang

                        ";
                if (!empty($user)) {
                    $sql .= "INNER JOIN user_skill
                                ON  user_skill.skill = skill.id
                                AND user_skill.user = :user
                                ";
                    $values[':user'] = $user;
                }
                if($child){
                $sql .= " WHERE skill.parent_skill_id is not null
                        ";
                }
                $sql .= "ORDER BY name ASC
                        ";

                $query = static::query($sql, $values);
                $skills = $query->fetchAll();
                foreach ($skills as $int) {
                    $array[$int[0]] = $int[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = Text::_('No hay ningun interes para guardar');

            if (empty($this->user))
                $errors[] = Text::_('No hay ningun usuario al que asignar');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $values = array(':user'=>$this->user, ':skill'=>$this->id);

			try {
	            $sql = "REPLACE INTO user_skill (user, skill) VALUES(:user, :skill)";
				self::query($sql, $values);
				return true;
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
				':skill'=>$this->id,
			);

            try {
                self::query("DELETE FROM user_skill WHERE skill = :skill AND user = :user", $values);
				return true;
			} catch(\PDOException $e) {
                $errors[] = Text::_('No se ha podido quitar el interes ') . $this->id . Text::_(' del usuario ') . $this->user . ' ' . $e->getMessage();
                //Text::get('remove-skill-fail');
                return false;
			}
		}

        /*
         * Lista de usuarios que comparten intereses con el usuario
         *
         * Si recibimos una categoría de interés, solamente los que comparten esa categoría
         *
         */
        public static function share ($user, $skill = null, $limit = null) {
             $array = array ();
            try {

                $values = array(':me'=>$user);

               $sql = "SELECT 
                            DISTINCT(user_skill.user) as id, 
                            user.name as name,
                            user.avatar as avatar
                        FROM user_skill
                        INNER JOIN user_skill as mine
                            ON user_skill.skill = mine.skill
                            AND mine.user = :me
                        INNER JOIN user
                            ON  user.id = user_skill.user
                            AND (user.hide = 0 OR user.hide IS NULL)
                        WHERE user_skill.user != :me
                        ";
               if (!empty($skill)) {
                   $sql .= "AND user_skill.skill = :skill
                       ";
                   $values[':skill'] = $skill;
               }
               $sql .= " ORDER BY RAND()";
               if (!empty($limit)) {
                   $sql .= " LIMIT $limit";
               }
                $query = static::query($sql, $values);
                $shares = $query->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($shares as $share) {

                    // nombre i avatar vienen en la sentencia, hay que sacar la imagen
                    $share['user'] = $share['id'];
                    $queryI = static::query("SELECT COUNT(DISTINCT(project)) FROM invest WHERE user = ? AND status IN ('0', '1', '3')", array($share['id']));
                    $share['invests'] = $queryI->fetchColumn(0);
                    $queryP = static::query('SELECT COUNT(id) FROM project WHERE owner = ? AND status > 2', array($share['id']));
                    $share['projects'] = $queryP->fetchColumn(0);
                    $share['avatar'] = (empty($share['avatar'])) ? Image::get(1) : Image::get($share['avatar']);
                    if (!$share['avatar'] instanceof Image) {
                        $share['avatar'] = Image::get(1);
                    }
                    
                    $array[] = (object) $share;
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        /*
         * Lista de usuarios de la comunidad que comparten un interés
         *
         */
        public static function shareAll ($skill) {
             $array = array ();
            try {

                $values = array(':skill'=>$skill);

               $sql = "SELECT DISTINCT(user_skill.user) as id
                        FROM user_skill
                        INNER JOIN user
                            ON  user.id = user_skill.user
                            AND (user.hide = 0 OR user.hide IS NULL)
                        WHERE user_skill.skill = :skill
                        ";

                $query = static::query($sql, $values);
                $shares = $query->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($shares as $share) {

                    // nombre i avatar
                    $user = \Goteo\Model\User::get($share['id']);
                    if (empty($user->avatar)) $user->avatar = (object) array('id'=>1);
                    // meritocracia
                    $support = (object) $user->support;
                    // proyectos publicados
                    $query = self::query('SELECT COUNT(id) FROM project WHERE owner = ? AND status > 2', array($share['id']));
                    $projects = $query->fetchColumn(0);

                    $array[] = (object) array(
                        'user' => $share['id'],
                        'avatar' => $user->avatar,
                        'name' => $user->name,
                        'projects' => $projects,
                        'invests' => $support->invests
                    );
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

	}
    
}