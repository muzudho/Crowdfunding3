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

    use Goteo\Library\Text;

    class Image extends \Goteo\Core\Model {

        public
			$id,
            $name,
            $type,
            $tmp,
            $error,
            $size,
            $dir_originals,
            $dir_cache;

        public static $types = array('user','project', 'post', 'glossary', 'info');

        /**
         * Constructor.
         *
         * @param type array	$file	Array $_FILES.
         */
        public function __construct ($file) {

			$this->dir_originals = GOTEO_DATA_PATH . 'images' . DIRECTORY_SEPARATOR;
			$this->dir_cache = GOTEO_DATA_PATH . 'cache' . DIRECTORY_SEPARATOR;

            if(is_array($file)) {
                $this->name = self::check_filename($file['name'], $this->dir_originals);
                $this->type = $file['type'];
                $this->tmp = $file['tmp_name'];
                $this->error = $file['error'];
                $this->size = $file['size'];
            }
            elseif(is_string($file)) {
				$this->name = self::check_filename(basename($file), $this->dir_originals);
				$this->tmp = $file;
			}
            //die($this->dir_originals);
            if(!is_dir($this->dir_originals)) {
				mkdir($this->dir_originals);
			}
            if(!is_dir($this->dir_cache)) {
				mkdir($this->dir_cache);
			}
        }

        /**
         * Sobrecarga de métodos 'getter'.
         *
         * @param type string $name
         * @return type mixed
         */
        public function __get ($name) {
            if($name == "content") {
	            return $this->getContent();
	        }
            return $this->$name;
        }

        /**
         * (non-PHPdoc)
         * @see Goteo\Core.Model::save()
         */
        public function save(&$errors = array()) {
            if($this->validate($errors)) {
                $data[':name'] = $this->name;

                if(!empty($this->type)) {
                    $data[':type'] = $this->type;
                }

                if(!empty($this->size)) {
                    $data[':size'] = $this->size;
                }

                //si es un archivo que se sube
                if(is_uploaded_file($this->tmp)) {
                    move_uploaded_file($this->tmp,$this->dir_originals . $this->name);
                    chmod($this->dir_originals . $this->name, 0777);
                }
                else {
                    $errors[] = Text::get('image-upload-fail');
                    return false;
                }

                try {

                    // Construye SQL.
                    $query = "REPLACE INTO image (";
                    foreach($data AS $key => $row) {
                        $query .= substr($key, 1) . ", ";
                    }
                    $query = substr($query, 0, -2) . ") VALUES (";
                    foreach($data AS $key => $row) {
                        $query .= $key . ", ";
                    }
                    $query = substr($query, 0, -2) . ")";
                    // Ejecuta SQL.
                    $result = self::query($query, $data);
                    if(empty($this->id)) $this->id = self::insertId();
                    return true;
                } catch(\PDOException $e) {
                    $errors[] = Text::_("No se ha guardado correctamente. ") . $e->getMessage();
                    return false;
                }
            }
            return false;
        }
        public function saveExternalFile(&$errors = array()) {

/*            
die("test");

                        $url = $this->avatar['profile_image_url'];
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_URL,$url);
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                        $profile_image_file = curl_exec( $ch );
                        curl_close();
*/
            $data[':name'] = $this->name;

            if(!empty($this->type)) {
                $data[':type'] = $this->type;
            }

            if(!empty($this->size)) {
                $data[':size'] = $this->size;
            }

            //si es un archivo que se sube
            if(!empty($this->tmp)) {
                $url = $this->tmp;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
                curl_setopt($ch,CURLOPT_MAXREDIRS, 10);
                curl_setopt($ch,CURLOPT_URL,$url);
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                $profile_image_file = curl_exec( $ch );
                curl_close($ch);
                file_put_contents($this->dir_originals . $this->name, $profile_image_file);
                chmod($this->dir_originals . $this->name, 0777);
            }
            else {
                $errors[] = Text::get('image-upload-fail');
                return false;
            }

            try {

                // Construye SQL.
                $query = "REPLACE INTO image (";
                foreach($data AS $key => $row) {
                    $query .= substr($key, 1) . ", ";
                }
                $query = substr($query, 0, -2) . ") VALUES (";
                foreach($data AS $key => $row) {
                    $query .= $key . ", ";
                }
                $query = substr($query, 0, -2) . ")";
                // Ejecuta SQL.
                $result = self::query($query, $data);
                if(empty($this->id)) $this->id = self::insertId();
                return true;
            } catch(\PDOException $e) {
                $errors[] = Text::_("No se ha guardado correctamente. ") . $e->getMessage();
                return false;
            }
        }
		/**
		* Returns a secure name to store in file system, if the generated filename exists returns a non-existing one
		* @param $name original name to be changed-sanitized
		* @param $dir if specified, generated name will be changed if exists in that dir
		*/
		public static function check_filename($name='',$dir=null){
			$name = preg_replace("/[^a-z0-9_~\.-]+/","-",strtolower(self::idealiza($name, true)));
			if(is_dir($dir)) {
				while ( file_exists ( "$dir/$name" )) {
					$name = preg_replace ( "/^(.+?)(_?)(\d*)(\.[^.]+)?$/e", "'\$1_'.(\$3+1).'\$4'", $name );
				}
			}
			return $name;
		}

		/**
		 * (non-PHPdoc)
		 * @see Goteo\Core.Model::validate()
		 */
		public function validate(&$errors = array()) {
            
			if(empty($this->name)) {
                $errors['image'] = Text::get('error-image-name');
            }
            
            // checkeo de errores de $_FILES
            if($this->error !== UPLOAD_ERR_OK) {
                switch($this->error) {
                    case UPLOAD_ERR_INI_SIZE:
                        $errors['image'] = Text::get('error-image-size-too-large');
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $errors['image'] = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $errors['image'] = 'The uploaded file was only partially uploaded';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        if (isset($_POST['upload']))
                            $errors['image'] = 'No file was uploaded';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $errors['image'] = 'Missing a temporary folder';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $errors['image'] = 'Failed to write file to disk';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $errors['image'] = 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions';
                        break;
                }
                return false;
            }

            if(!empty($this->type)) {
                $allowed_types = array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'image/svg+xml',
                );
                if(!in_array($this->type, $allowed_types)) {
                    $errors['image'] = Text::get('error-image-type-not-allowed');
                }
            }
            else {
                $errors['image'] = Text::get('error-image-type');
            }

            if(empty($this->tmp) || $this->tmp == "none") {
                $errors['image'] = Text::get('error-image-tmp');
            }

            if(empty($this->size)) {
                $errors['image'] = Text::get('error-image-size');
            }
            
            return empty($errors);
		}

		/**
		 * Imagen.
		 *
		 * @param type int	$id
		 * @return type object	Image
		 */
	    static public function get ($id) {
            $db_prefix = "`" . GOTEO_DB_SCHEMA ."`.";
            try {
                $query = static::query("
                    SELECT
                        id,
                        name,
                        type,
                        size
                    FROM image
                    WHERE id = :id
                    ", array(':id' => $id));
                $image = $query->fetchObject(__CLASS__);
                return $image;
            } catch(\PDOException $e) {
                return false;
            }
		}

        /**
         * Galeria de imágenes de un usuario / proyecto
         *
         * @param  varchar(50)  $id    user id |project id
         * @param  string       $which    'user'|'project'
         * @return mixed        false|array de instancias de Image
         */
        public static function getAll ($id, $which) {

            if (!\is_string($which) || !\in_array($which, self::$types)) {
                return false;
            }

            $gallery = array();

            $_which = "`" . GOTEO_DB_SCHEMA ."`." . $which;

            $_id = $id;
            try {
                $sql = "SELECT image FROM {$_which}_image WHERE {$which} = :id";
                $sql .= ($which == 'project') ? " ORDER BY {$_which}_image.section ASC, `order` ASC, image DESC" : " ORDER BY image ASC";
                $query = self::query($sql, array(':id' => $_id));
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $image) {
                    $gallery[] = self::get($image['image']);
                }
                return $gallery;
            } catch(\PDOException $e) {
                return false;
            }

        }

        /**
         * Quita una imagen de la tabla de relaciones y de la tabla de imagenes
         *
         * @param  string       $which    'user'|'project'|'post'
         * @return bool        true|false
         *
         */
        public function remove($which) {

            try {
                self::query("START TRANSACTION");
                $sql = "DELETE FROM image WHERE id = ?";
                $query = self::query($sql, array($this->id));

                // para usuarios y proyectos que tienen N imagenes
                // por ahora post solo tiene 1
                if (\is_string($which) && \in_array($which, self::$types)) {
                    $sql = "DELETE FROM `" . \GOTEO_DB_SCHEMA . "`.{$which}_image WHERE image = ?";
                    $query = self::query($sql, array($this->id));
                }
                self::query("COMMIT");

                return true;
            } catch(\PDOException $e) {
                return false;
            }
        }

		/**
		 * Para montar la url de una imagen (porque las url con parametros no se cachean bien)
		 *  - Si el thumb está creado, montamos la url de /data/cache
         *  - Sino, monamos la url de /image/
         *
		 * @param type int $id
		 * @param type int $new_width
		 * @param type int $new_height
		 * @param type int $crop
		 * @return type string
		 */
		public function getLink ($new_width = 200, $new_height = 200, $crop = false) {

            $ret = "";

            $src_url = preg_replace('/[A-Za-z0-9.]+\.localgood/','static.localgood',SRC_URL);

            $tc = $crop ? 'c' : '';

            $filename2 = $this->dir_cache . "{$new_width}x{$new_height}{$tc}" . DIRECTORY_SEPARATOR . $this->name;
            if (\file_exists($filename2)) {
                $ret = $src_url . "/data/cache/{$new_width}x{$new_height}{$tc}/{$this->name}";
            } else {
                $ret = SRC_URL . "/image/{$this->id}/{$new_width}/{$new_height}/" . $crop;
            }

            return $ret;

		}

		/**
		 * Carga la imagen en el directorio temporal del sistema.
		 *
		 * @return type bool
		 */
		public function load () {
		    if(!empty($this->id) && !empty($this->name)) {
    		    $tmp = tempnam(sys_get_temp_dir(), 'Goteo');
                $file = fopen($tmp, "w");
                fwrite($file, $this->content);
                fclose($file);
                if(!file_exists($tmp)) {
                    throw \Goteo\Core\Exception("Error al cargar la imagen temporal.");
                }
                else {
                    $this->tmp = $tmp;
                    return true;
                }
		    }
		}

		/**
		 * Elimina la imagen temporal.
		 *
		 * @return type bool
		 */
    	public function unload () {
    	    if(!empty($this->tmp)) {
                if(!file_exists($this->tmp)) {
                    throw \Goteo\Core\Exception("Error, la imagen temporal no ha sido encontrada.");
                }
                else {
                    unlink($this->tmp);
                    unset($this->tmp);
                    return true;
                }
    	    }
    	    return false;
		}

		/**
		 * 例えば /data/images といったフォルダーにアップロードされている画像を、
         * /data/cache/128x128 といったフォルダーにコピーして、その画像バイナリを読み込みます。
         *
         * キャッシュがある場合はそれをしようし、なければコピーを作成します。
         * この際、crop の指定があれば 画像をリサイズします。
         *
		 * @param type int	$new_width
		 * @param type int	$new_height
         * @param type bool $crop
		 */
        public function display ($new_width, $new_height, $crop) {

            // アップロードされている画像のパス
            $filename1 = $this->dir_originals . $this->name;

            // キャッシュ・ディレクトリの画像パス
            $filename2 = $this->dir_cache . $new_width."x$new_height" . ($crop ? "c" : "") . DIRECTORY_SEPARATOR;
            if(!is_dir($filename2)) {
                mkdir($filename2);
            }

			$filename2 .= $this->name;
			if(!is_file($filename2)) {// キャッシュに画像がなければ
    			
                $b_toCopy = false;
				if($crop) {
                    
                    // 元画像のファイルサイズを取得
                    list($original_width, $original_height) = getimagesize($filename1);

                    // ドット抜き拡張子
                    $file_type = strtolower(end(explode('.', $filename1)));
            
                    $b_toSave = true;
                    if ($file_type === "jpg" || $file_type === "jpeg") {
            
                        $original_image = ImageCreateFromJPEG($filename1); //JPEGファイルを読み込む
                        $new_image = ImageCreateTrueColor($new_width, $new_height); // 画像作成
    
                    } elseif ($file_type === "gif") {
    
                        $original_image = ImageCreateFromGIF($filename1); //GIFファイルを読み込む
                        $new_image = ImageCreateTrueColor($new_width, $new_height); // 画像作成
                    
                        /* ----- 透過問題解決 ------ */
                        $alpha = imagecolortransparent($original_image);  // 元画像から透過色を取得する
                        imagefill($new_image, 0, 0, $alpha);       // その色でキャンバスを塗りつぶす
                        imagecolortransparent($new_image, $alpha); // 塗りつぶした色を透過色として指定する
    
                    } elseif ($file_type === "png") {
                    
                        $original_image = ImageCreateFromPNG($filename1); //PNGファイルを読み込む
                        $new_image = ImageCreateTrueColor($new_width, $new_height); // 画像作成
                    
                        /* ----- 透過問題解決 ------ */
                        imagealphablending($new_image, false);  // アルファブレンディングをoffにする
                        imagesavealpha($new_image, true);       // 完全なアルファチャネル情報を保存するフラグをonにする
                    
                    } else {
                        // 何も当てはまらなかった場合、画像をそのままコピーする処理へ。
                        $b_toSave = false;
                        $b_toCopy = true;

                    }

                    if($b_toSave){
                        // 元画像から再サンプリング
                        ImageCopyResampled($new_image,$original_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
            
                        // 画像を保存
                        if ($file_type === "jpg" || $file_type === "jpeg") {
                            ImageJPEG($new_image, $filename2);
                        } elseif ($file_type === "gif") {
                            ImageGIF($new_image, $filename2);
                        } elseif ($file_type === "png") {
                            ImagePNG($new_image, $filename2);
                        }
                        
                        // メモリを開放する
                        imagedestroy($new_image);
                        imagedestroy($original_image);
                    }
                                
                } else {
                    $b_toCopy = true;
                    
                }
                
                if($b_toCopy){
                    if( !copy($filename1, $filename2)){
                        echo "failed to copy $file...\n";
                    }
                }
                
                chmod($filename2, 0777);
            }

			header("Content-type: " . $this->type);
			readfile($filename2);
			return true;
		}

		public function isGIF () {
		    return ($this->type == 'image/gif');
		}

    	public function isJPG () {
		    return ($this->type == 'image/jpg') || ($this->type == 'image/jpeg');
		}

    	public function isPNG () {
		    return ($this->type == 'image/png');
		}

    	public function toGIF () {
    	    $this->load();
    	    if(!$this->isGIF()) {
                list($new_width, $new_height, $type) = getimagesize($this->tmp);
                switch($type) {
                	case 1:
                		$image = imagecreatefromgif($this->tmp);
                		break;
                	default:
                	case 2:
                		$image = imagecreatefromjpeg($this->tmp);
                		break;
                	case 3:
                		$image = imagecreatefrompng($this->tmp);
                		break;
                	case 6:
                		$image = imagecreatefromwbmp($this->tmp);
                		break;
                }
                $tmp = static::replace_extension($this->tmp, 'gif');
                $this->unload();
                $this->tmp = $tmp;
           		imagegif($image, $this->tmp);
           		imagedestroy($image);
                return true;
    	    }
    	    return;
    	}

        public function toJPG () {
    	    $this->load();
    	    if(!$this->isJPG()) {
                list($new_width, $new_height, $type) = getimagesize($this->tmp);
                switch($type) {
                	case 1:
                		$image = imagecreatefromgif($this->tmp);
                		break;
                	default:
                	case 2:
                		$image = imagecreatefromjpeg($this->tmp);
                		break;
                	case 3:
                		$image = imagecreatefrompng($this->tmp);
                		break;
                	case 6:
                		$image = imagecreatefromwbmp($this->tmp);
                		break;
                }
                $tmp = static::replace_extension($this->tmp, 'gif');
                $this->unload();
                $this->tmp = $tmp;
           		imagejpeg($image, $this->tmp, 100);
           		imagedestroy($image);
                return true;
    	    }
    	    return;
    	}

    	public function toPNG () {
    	    $this->load();
    	    if(!$this->isPNG()) {
                list($new_width, $new_height, $type) = getimagesize($this->tmp);
                switch($type) {
                	case 1:
                		$image = imagecreatefromgif($this->tmp);
                		break;
                	default:
                	case 2:
                		$image = imagecreatefromjpeg($this->tmp);
                		break;
                	case 3:
                		$image = imagecreatefrompng($this->tmp);
                		break;
                	case 6:
                		$image = imagecreatefromwbmp($this->tmp);
                		break;
                }
                $tmp = static::replace_extension($this->tmp, 'gif');
                $this->unload();
                $this->tmp = $tmp;
           		imagepng($image, $this->tmp, 100);
           		imagedestroy($image);
                return true;
    	    }
    	    return;
    	}

        private function getContent () {
            return file_get_contents($this->dir_originals . $this->name);
    	}

        /*
         * Devuelve la imagen en GIF.
         *
         * @return type object	Image
         */
        static public function gif ($id) {
            $img = static::get($id);
            if(!$img->isGIF())
                $img->toGIF();
            return $img;
        }

        /*
         * Devuelve la imagen en JPG/JPEG.
         *
         * @return type object	Image
         */
        static public function jpg ($id) {
            $img = static::get($id);
            if ($img->isJPG())
                $img->toJPG();
            return $img;
        }

        /*
         * Devuelve la imagen en PNG.
         *
         * @return type object	Image
         */
        static public function png ($id) {
            $img = self::get($id);
            if ($img->isPNG())
                $img->toPNG();
            return $img;
        }

        /**
         * Reemplaza la extensión de la imagen.
         *
         * @param type string	$src
         * @param type string	$new
         * @return type string
         */
    	static private function replace_extension($src, $new) {
    	    $pathinfo = pathinfo($src);
    	    unset($pathinfo["basename"]);
    	    unset($pathinfo["extension"]);
    	    return implode(DIRECTORY_SEPARATOR, $pathinfo) . '.' . $new;
    	}

        //
        //  DB接続先の横取り
        //
        public static function query ($query, $params = null) {

            $query = self::queryFilter($query);
            
            static $db = null;
            if ($db === null) {
                try {
                    $dsn = \GOTEO_DB_DRIVER . ':host=' . \GOTEO_DB_HOST . ';dbname=' . \COMMON_AUTH_DB_SCHEMA;
                    $db = new \PDO($dsn, \GOTEO_DB_USERNAME, \GOTEO_DB_PASSWORD, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"));;
                } catch (\PDOException $e) {
                    die ('No puede conectar la base de datos');
                }
            }

            $params = func_num_args() === 2 && is_array($params) ? $params : array_slice(func_get_args(), 1);

            // ojo que el stripslashes jode el contenido blob al grabar las imagenes
            if (\get_magic_quotes_gpc ()) {
                foreach ($params as $key => $value) {
                    if ($key != ':content') {
                        $params[$key] = \stripslashes(\stripslashes($value));
                    }
                }
            }

            $result = $db->prepare($query);

            try {

                $result->execute($params);
                return $result;

            } catch (\PDOException $e) {
                throw new Exception("Error PDO: " . \trace($e));
            }
        }

	}
}
