<?php

class Popular extends Model {

    public function findPostByPopular($offset, $limit) {
        $data = array();
        $sql_limit = "";
        $next = 0;
        if(is_numeric($limit) && is_numeric($offset))  {
            $limit = intval($limit) > 20 ? 20 : intval($limit);
            $next = intval($offset) + $limit;
            $sql_limit = "LIMIT $limit OFFSET $offset"; 
        } 

        $sql = "SELECT * FROM post WHERE flag = 1 ORDER BY views DESC $sql_limit";
        $result = $this->db->query($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        $this->close();
        return array(
            'data'=> $data,
            'next'=> $next,
            'back'=> count($data) < $limit
        );
    }

}