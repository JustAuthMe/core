<?php


namespace Model;


class UniqidUpdate {
    public static function getActiveUpdate($user_id): \Entity\UniqidUpdate {
        $req = \DB::getSlave()->prepare("SELECT * FROM uniqid_update WHERE user_id = ? AND active = 1 ORDER BY timestamp DESC LIMIT 1");
        $req->execute([$user_id]);
        $response = $req->fetch();

        return \Persist::getFilledObject('\Entity\UniqidUpdate', $response);
    }
    public static function getActiveUpdateByNewUniqid($new_uniqid): \Entity\UniqidUpdate {
        $req = \DB::getSlave()->prepare("SELECT * FROM uniqid_update WHERE new_uniqid = ? AND active = 1 ORDER BY timestamp DESC LIMIT 1");
        $req->execute([$new_uniqid]);
        $response = $req->fetch();

        return \Persist::getFilledObject('\Entity\UniqidUpdate', $response);
    }

    public static function isThisEmailAlmostTaken($email) {
        $req = \DB::getSlave()->prepare("SELECT COUNT(*) AS cnt FROM uniqid_update WHERE new_uniqid = ? AND active = 1");
        $req->execute([User::hashInfo($email)]);
        $response = $req->fetch();

        return $response['cnt'] > 0;
    }

    public static function isThereAnActiveUpdate($user_id) {
        $req = \DB::getSlave()->prepare("SELECT COUNT(*) AS cnt FROM uniqid_update WHERE user_id = ? AND active = 1");
        $req->execute([$user_id]);
        $response = $req->fetch();

        return $response['cnt'] > 0;
    }

    public static function isThereAnActiveUpdateByNewUniqid($new_uniqid) {
        $req = \DB::getSlave()->prepare("SELECT COUNT(*) AS cnt FROM uniqid_update WHERE new_uniqid = ? AND active = 1");
        $req->execute([$new_uniqid]);
        $response = $req->fetch();

        return $response['cnt'] > 0;
    }

    public static function removeActiveUpdates($user_id) {
        $req = \DB::getMaster()->prepare("DELETE FROM uniqid_update WHERE user_id = ? AND active = 1");
        $req->execute([$user_id]);
    }

    public static function getLatestUpdate($old_uniqid) {
        $req = \DB::getSlave()->prepare("SELECT * FROM uniqid_update WHERE old_uniqid = ? AND active = 0 ORDER BY timestamp DESC LIMIT 1");
        $req->execute([$old_uniqid]);
        $response = $req->fetch();

        return $response !== false ? \Persist::getFilledObject('\Entity\UniqidUpdate', $response) : false;
    }
}