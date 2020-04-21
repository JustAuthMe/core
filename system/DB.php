<?php
abstract class DB {
	private static
	$master_instance = null,
    $slave_instance = null;
	
	public static function getMaster(): PDO {
		if (self::$master_instance == null) {
			try	{
				self::$master_instance = new PDO('mysql:host=' . DB_MASTER_HOST . ';dbname=' . DB_MASTER_NAME, DB_MASTER_USER, DB_MASTER_PASS);
				self::$master_instance->exec("SET CHARACTER SET utf8");
			}
			catch (Exception $e) {
				die('Erreur : ' . $e->getMessage());
			}
		}
		
		return self::$master_instance;
	}

    public static function getSlave(): PDO {
        if (self::$slave_instance == null) {
            try	{
                self::$slave_instance = new PDO('mysql:host=' . DB_SLAVE_HOST . ';dbname=' . DB_SLAVE_NAME, DB_SLAVE_USER, DB_SLAVE_PASS);
                self::$slave_instance->exec("SET CHARACTER SET utf8");
            }
            catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }

        return self::$slave_instance;
    }
}