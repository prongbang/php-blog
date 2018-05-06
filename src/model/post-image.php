<?php 

class PostImage extends Model {

    public function findById($id) { 
        $data = array();
        $sql = "SELECT * FROM post_image WHERE id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $id = intval($id);
            $stmt->bind_param("i", $id);
            $stmt->execute(); 
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->free_result();
            $stmt->close();
        }
        return $data;
    }

    public function update($post) { 

        // เตรียม set sql
        // i - integer
        // d - double
        // s - string
        // b - BLOB
        $set = "";
        $type = "";
        $map = array();
        if(!empty($post['post_id'])) {
            $set .= "post_id = ?,";
            $type .= "i";
            array_push($map, $post['post_id']);
        }
        if(!empty($post['image'])) {
            $set .= "image = ?,";
            $type .= "s";
            array_push($map, $post['image']);
        }
        if(!empty($post['path'])) {
            $set .= "path = ?,";
            $type .= "s";
            array_push($map, $post['path']);
        }        
        
        if(!empty($post['id'])) {
            $type .= "i";
            array_push($map, intval($post['id'].""));
        }

        // delete ,
        $set = substr($set, 0, strlen($set) - 1); 
        $sql = "UPDATE post_image SET $set WHERE id = ?";
        
        // prepare sql and bind parameters
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param($type, ...$map);
        // $stmt->bind_param($type, 1,2,3,4);

        $rs = $stmt->execute();

        $stmt->close();

        return $rs;        
    }

    public function create($post) {
        $sql = "INSERT INTO post_image (id, post_id, image, path) VALUES (NULL, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "iss", 
            $post['post_id'], 
            $post['image'],
            $post['path']
        );
        $status = $stmt->execute();
        $last_id = $this->db->insert_id;
        $stmt->close();
        return $last_id;
    }

    public function delete($pk) {
        $status = false;
        $sql = "DELETE FROM post_image WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $pk);
        $status = $stmt->execute();
        $stmt->close();
        return $status;
    }

}