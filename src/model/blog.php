<?php

class Blog extends Model {

    public function update($blog) {

        $where = "";
        if(!empty($blog['id'])) $where .= " WHERE id = ?";

        $sql = "UPDATE blog SET views = views + 1 $where";

        $type = "";
        $data = array();

        // prepare sql and bind parameters
        $stmt = $this->db->prepare($sql);
        if(!empty($blog['id'])) {
            $type .= "i";
            array_push($data, $blog['id']);
        }

        if(count($data) > 0) 
            $stmt->bind_param($type, ...$data);
        // $stmt->bind_param($type, 1,2,3,4);

        $rs = $stmt->execute();

        $stmt->close();
        $this->close();

        return $rs;
    }

}