<?php
/**
 * Created by PhpStorm.
 * User: peter_000
 * Date: 16/06/2016
 * Time: 19:59
 */

abstract class Persist {
    private static function getSetterName(string $column): string {
        return 'set'.Utils::fromSnakeCaseToCamelCase($column);
    }

    private static function getGetterName(string $column): string {
        return 'get'.Utils::fromSnakeCaseToCamelCase($column);
    }

    public static function getFilledObject(string $classname, array $rep) {
        $res = new $classname();
        foreach ($rep as $key => $value) {
            if (!is_numeric($key)) {
                $setter = self::getSetterName($key);
                $value = (@unserialize($value) === false) ? $value : unserialize($value);
                $res->$setter($value);
            }
        }

        return $res;
    }

    public static function fetchAll(string $classname, string $cond = '', array $values = []): array {
        $classname = '\Entity\\'.$classname;
        $table_name = $classname::getTableName();
        $cond = ($cond != '') ? ' '.$cond : '';
        $req = DB::getSlave()->prepare("SELECT * FROM $table_name".$cond);
        $req->execute($values);
        $res = [];
        $i = 0;
        while ($rep = $req->fetch()) {
            $res[$i] = self::getFilledObject($classname, $rep);
            $i++;
        }
        $req->closeCursor();

        return $res;
    }

    public static function create(\Resourceable $object): int {
        $classname = get_class($object);
        $table_name = $classname::getTableName();
        $ref = new ReflectionClass($classname);
        $props = $ref->getProperties();

        $columns = [];
        $values = [];
        $qms = [];
        foreach ($props as $prop) {
            $getter = self::getGetterName($prop->getName());
            $val = $object->$getter();
            if ($val === null) {
                continue;
            }

            $columns[] = $prop->getName();
            if (is_array($val) || is_object($val)) {
                $val = serialize($val);
            }
            $values[] = $val;
            $qms[] = '?';
        }
        $columns = implode(', ', $columns);
        $qms = implode(', ', $qms);

        $req = DB::getMaster()->prepare("INSERT INTO $table_name ($columns) VALUES ($qms)");
        $req->execute($values);

        return DB::getMaster()->lastInsertId();
    }

    public static function exists(string $classname, string $column, string $value): bool {
        $classname = '\Entity\\'.$classname;
        $table_name = $classname::getTableName();
        $req = DB::getSlave()->prepare("SELECT COUNT(*) AS nb FROM $table_name WHERE $column = ?");
        $req->execute([$value]);
        $res = $req->fetch();
        $req->closeCursor();
        return ($res['nb'] > 0);
    }

    public static function read(string $classname, int $id): \Resourceable {
        $classname = '\Entity\\'.$classname;
        $table_name = $classname::getTableName();
        $req = DB::getSlave()->prepare("SELECT * FROM $table_name WHERE id = ?");
        $req->execute([$id]);
        $res = $req->fetch();
        return self::getFilledObject($classname, $res);
    }

    public static function readBy(string $classname, string $column, string $value): \Resourceable {
        $classname = '\Entity\\'.$classname;
        $table_name = $classname::getTableName();
        $req = DB::getSlave()->prepare("SELECT * FROM $table_name WHERE $column = ?");
        $req->execute([$value]);
        $res = $req->fetch();
        $req->closeCursor();
        return self::getFilledObject($classname, $res);
    }

    public static function count(string $classname, string $cond = '', array $values = []): int {
        $classname = '\Entity\\'.$classname;
        $table_name = $classname::getTableName();
        $req = DB::getSlave()->prepare("SELECT COUNT(*) AS nb FROM $table_name $cond");
        $req->execute($values);
        $res = $req->fetch();
        $req->closeCursor();
        return $res['nb'];
    }

    public static function update(\Resourceable $object) {
        $classname = get_class($object);
        $table_name = $classname::getTableName();
        $id = $object->getId();
        $ref = new ReflectionClass($classname);
        $props = $ref->getProperties();

        $values = [];
        $qms = [];
        foreach ($props as $prop) {
            $getter = self::getGetterName($prop->getName());
            $values[] = $object->$getter();
            $qms[] = $prop->getName()." = ?";
        }
        $qms = implode(', ', $qms);

        $req = DB::getMaster()->prepare("UPDATE $table_name SET $qms WHERE id = ?");
        $req->execute(array_merge($values, [$id]));
    }

    public static function delete(\Resourceable $object) {
        $classname = get_class($object);
        $table_name = $classname::getTableName();
        $id = $object->getId();
        $req = DB::getMaster()->prepare("DELETE FROM $table_name WHERE id = ?");
        $req->execute([$id]);
    }

    public static function deleteBy(string $classname, string $column, $value) {
        $classname = '\Entity\\'.$classname;
        $table_name = $classname::getTableName();
        $req = DB::getMaster()->prepare("DELETE FROM $table_name WHERE $column = ?");
        $req->execute([$value]);
    }

    public static function deleteById(string $classname, int $id) {
        $classname = '\Entity\\'.$classname;
        $table_name = $classname::getTableName();
        $req = DB::getMaster()->prepare("DELETE FROM $table_name WHERE id = ?");
        $req->execute([$id]);
    }
}