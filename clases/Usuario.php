<?php

/*Llamadas de archivos necesarios
por medio de require*/

require __DIR__ . '/../libs/db/db.php';
require_once __DIR__ . '/interfaceCRUD.php';

/**
 * Clase y su contenido
 *
 */

class Usuario implements Crud {
	private $id_usuario;
	private $codigo_perfil;
	private $login;
	private $pass;
	private $nombre;
	private $apellido;
	private $correo;
	private $edad;
	private $fechaNac;
	private $db;

	function __construct($per = null, $log = null, $psw = null, $nom = null, $ape = null, $mail = null, $fen = null) {

		$this->codigo_perfil = $per;
		$this->login = $log;
		$this->pass = md5($psw);
		$this->nombre = $nom;
		$this->apellido = $ape;
		$this->correo = $mail;
		$this->edad = $this->calculaEdad($fen);
		$this->fechaNac = $fen;

		$this->db = new DB();
	}

	/*Metodos*/
	public function login() {
		$sqlsel = "select id_usuario,id_perfil,login_usuario,pass_usuario,nombre_usuario,apellido_usuario,correo_usuario,edad_usuario,fechanacimiento_usuario from usuario
        where login_usuario=:usr and pass_usuario=:pwd";

		$query = $this->db->conexion->prepare($sqlsel);

		$query->bindParam(':usr', $this->login);
		$query->bindParam(':pwd', $this->pass);

		$query->execute();

		if ($query->rowcount() == 1) {
			//Si existe el usuario reasignamos los valores traidos de la DB
			$usuario = $query->fetch();
			$this->id_usuario = $usuario['id_usuario'];
			$this->codigo_perfil = $usuario['id_perfil'];
			$this->login = $usuario['login_usuario'];
			$this->nombre = $usuario['nombre_usuario'];
			$this->apellido = $usuario['apellido_usuario'];
			$this->correo = $usuario['correo_usuario'];
			$this->edad = $usuario['edad_usuario'];
			$this->fechaNac = $usuario['fechanacimiento_usuario'];
			return true;
		}

		return false;
	}
	/*Implementacion de metodos Crud*/
	public function insert() {
		/*Definición del query que permitira ingresar un nuevo usuario*/
		$sqlins = "insert into usuario values(null,:tip,:lgn,:psw,:nom,:ape,:ema,:ed,:fec)";
		/*Verifica que el nombre de usuario no exista*/
		if ($this->existe($this->login)) {
			$_SESSION['error_tmp'] = "El usuario $this->login ya existe.";
			return false;
		}
		/*Preparación SQL*/
		try {
			$queryins = $this->db->conexion->prepare($sqlins);
		} catch (PDOException $Exception) {
			echo "Clase Usuario:ERROR:Preparacion Query " . $Exception->getMessage() . '/' . $Exception->getCode();
			return false;
		}

		/*Asignación de parametros utilizando bindparam*/
		$queryins->bindParam(':tip', $this->codigo_perfil);
		$queryins->bindParam(':lgn', $this->login);
		$queryins->bindParam(':psw', $this->pass);
		$queryins->bindParam(':nom', $this->nombre);
		$queryins->bindParam(':ape', $this->apellido);
		$queryins->bindParam(':ema', $this->correo);
		$queryins->bindParam(':ed', $this->edad);
		$queryins->bindParam(':fec', $this->fechaNac);

		try {
			$queryins->execute();
		} catch (PDOException $Exception) {
			echo "Clase Producto:ERROR:Ejecución Query " . $Exception->getMessage() . '/' . $Exception->getCode();
			die();
			return false;
		}
		return true;
	}
	public function read() {
		/*Definicion de query*/
		$sql = "select * from usuario u INNER JOIN perfil p ON u.ID_PERFIL=p.ID_PERFIL";
		/*Preparacion sql*/
		try {
			$query = $this->db->conexion->prepare($sql);
		} catch (PDOException $Exception) {
			echo "Clase usuario:ERROR:Preparacion Query " . $Exception->getMessage() . '/' . $Exception->getCode();
			return false;
		}

		try {
			$query->execute();
		} catch (PDOException $Exception) {
			echo "Clase Producto:ERROR:Ejecución Query " . $Exception->getMessage() . '/' . $Exception->getCode();
			die();
			return false;
		}
		return $query;
	}

	public function update($id) {
		/*Definicion del query que permitira actualizar */
		$sqlupd = "update usuario set ID_PERFIL=:tip,LOGIN_USUARIO=:lgn,NOMBRE_USUARIO=:nom
		,APELLIDO_USUARIO=:ape,CORREO_USUARIO=:ema,EDAD_USUARIO=:ed,FECHANACIMIENTO_USUARIO=:fec where ID_USUARIO=:id";

		/*Preparación SQL*/
		try {
			$queryup = $this->db->conexion->prepare($sqlupd);
		} catch (PDOException $Exception) {
			echo "Clase usuario:ERROR:Preparacion Query " . $Exception->getMessage() . '/' . $Exception->getCode();
			return false;
		}

		/*Asignacion de parametros utilizando bindparam*/
		$queryup->bindParam(':id', $id);
		$queryup->bindParam(':tip', $this->codigo_perfil);
		$queryup->bindParam(':lgn', $this->login);
		$queryup->bindParam(':nom', $this->nombre);
		$queryup->bindParam(':ape', $this->apellido);
		$queryup->bindParam(':ema', $this->correo);
		$queryup->bindParam(':ed', $this->edad);
		$queryup->bindParam(':fec', $this->fechaNac);

		try {
			$queryup->execute();
		} catch (PDOException $Exception) {
			echo "Clase Producto:ERROR:Ejecución Query " . $Exception->getMessage() . '/' . $Exception->getCode();
			die();
			return false;
		}
		return true;
	}

	public function delete($id) {
		/*Seleccionamos las ordenes del usuario para eliminarlas*/
		$sqlorden= "select * from orden_compras where ID_USUARIO=:id";
		/*Preparamos la consulta*/
		$sqlorden = $this->db->conexion->prepare($sqlorden);
		/*Asignacion del mismo id*/
		$sqlorden->bindParam(':id', $id);
		/*execute*/
		$sqlorden->execute();

		foreach ($sqlorden as $row ) {
			$idoc=$row['ID_OC'];
			/*Eliminamos los detalles de las ordenes*/
			$sqldeta= "delete from detalle_oc where ID_OC=:idc";
			/*Preparamos la consulta*/
			$sqldeta = $this->db->conexion->prepare($sqldeta);
			/*Asignacion del mismo id*/
			$sqldeta->bindParam(':idc', $idoc);
			/*execute*/
			$sqldeta->execute();
		}

		/*Eliminamos las ordenes*/
			$sqlelim= "delete from orden_compras where ID_USUARIO=:id";
			/*Preparamos la consulta*/
			$sqlelim = $this->db->conexion->prepare($sqlelim);
			/*Asignacion del mismo id*/
			$sqlelim->bindParam(':id', $id);
			/*execute*/
			$sqlelim->execute();

			
		/*Definición del query que permitira eliminar un registro*/
		$sqldel = "delete from usuario where ID_USUARIO=:id";

		/*Preparación SQL*/
		$querydel = $this->db->conexion->prepare($sqldel);

		$querydel->bindParam(':id', $id);

		try {
			$querydel->execute();
		} catch (PDOException $Exception) {
			echo "Clase Usuario:ERROR:Ejecución Query " . $Exception->getMessage() . '/' . $Exception->getCode();
			return false;
		}
		return true;
	}

	public function existe($nombre) {
		/*Definición del query que permitira traer un nuevo registro*/
		$sqlsel = "select * from usuario	where LOGIN_USUARIO=:prod";

		/*Preparación SQL*/
		$querysel = $this->db->conexion->prepare($sqlsel);

		/*Asignación de parametros utilizando bindparam*/
		$querysel->bindParam(':prod', $nombre);

		$querysel->execute();

		if ($querysel->rowcount() == 1) {
			return true;
		} else {
			return false;
		}

	}
	public function traeid() {
		/*Definición del query que permitira traer un nuevo registro*/
		$sqlsel = "select ID_PERFIL from perfil	where DESCRIPCION_PERFIL='Consulta'";

		/*Preparación SQL*/
		$querysel = $this->db->conexion->prepare($sqlsel);

		$querysel->execute();

		if ($querysel->rowcount() == 1) {
			foreach ($querysel as $row) {
				return $row['ID_PERFIL'];
			}

		} else {
			return false;
		}

	}
	public function calculaEdad($fechaNac) {
		$datetime1 = new DateTime($fechaNac);
		$datetime2 = new DateTime();
		$interval = $datetime1->diff($datetime2);
		return $interval->y;
	}

	/**
	 * Getters.
	 */
	public function getId_usuario() {
		return $this->id_usuario;
	}

	public function getCodigo_perfil() {
		return $this->codigo_perfil;
	}

	public function getLogin() {
		return $this->login;
	}

	public function getNombre() {
		return $this->nombre;
	}

	public function getApellido() {
		return $this->apellido;
	}

	public function getCorreo() {
		return $this->correo;
	}

	public function getFechaNac() {
		return $this->fechaNac;
	}

}
?>