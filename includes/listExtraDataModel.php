<?php

class SRModelListExtraFields
{

    /**
     * Class constructor
     */
    private $wpdb;
    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;

        $this->table_name = $this->wpdb->prefix.'sr_list_extra_data';
        if (!$this->checkTableExistance()){
            $this->prepareDB();
        }
    }

    /**
     * Prepare DB
     */
    public function prepareDB()
    {
        $this->createTable();

    }

    function dropDB()
    {
        $query = "DROP TABLE ".$this->table_name;
        return $this->wpdb->query($query);
    }

    function checkTableExistance()
    {
        $result=$this->wpdb->query("SHOW TABLES LIKE '".$this->table_name."'");
        print_r($result);

    }
    /**
     * Create Manage Tasks table
     */
    public function createTable()
    {
        $q = " CREATE TABLE IF NOT EXISTS $this->table_name (
              `id` int(8) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `type` varchar(255) NOT NULL,
              `extra_data` text NOT NULL,
              PRIMARY KEY (`id`)
            ) CHARACTER SET utf8 COLLATE utf8_general_ci;";
        return $this->wpdb->query($q);
    }

    public function insert($Name,$pageID)
    {
        $insert_query = 'INSERT INTO `'.$this->table_name.'` (
        `cityName`,`pageID`
        )
        VALUES ("'.$Name.'","'.$pageID.'")';
        return $this->wpdb->query($insert_query);

    }

    function update($id,$Name,$pageID){
        $result=$this->wpdb->update($this->table_name,
            array('cityName' => $Name,'pageID'=>$pageID),array('id' => $id));
        return $result;
    }

    public function delete($id)
    {
        $query = "DELETE FROM $this->table_name WHERE id=".$id;
        return $this->wpdb->query($query);
    }

    public function get($data=array())
    {

        if (array_key_exists('what',$data)){
            $query = "SELECT ".$data['what']." FROM {$this->tabe_name}";
        }
        else{
            $query = "SELECT * FROM {$this->table_name}";
        }
        if (array_key_exists('join',$data)){
            $query.=" INNER JOIN ".$data['join'][0]." ON ".$data['join'][1].' ';
        }
        if (array_key_exists('where',$data)){

            if (isset($data['where']['condition'][0])) $query.= $data['where']['condition'][0];

            if (isset($data['where']['where'][0]['function'])){

                $query.=' '." WHERE ".$data['where']['where'][0]['column'].' '.$data['where']['where'][0]['function']."('".$data['where']['where'][0]['value']."')";
            }
            else{
                $query.=' '." WHERE ".$data['where']['where'][0]['column']."='".$data['where']['where'][0]['value']."'";
            }
            unset($data['where']['where'][0]);

            foreach ($data['where']['where'] as $where){

                if (isset($where['function'])){

                    $query.=' '.$where['condition'].' '.$where['column'].' '.$where['function']."('".$where['value']."')";
                }
                else{
                    $query.=' '.$where['condition'].' '.$where['column']."='".$where['value']."'";
                }
            }

            if (isset($data['where']['condition'][1])) $query.= $data['where']['condition'][1];


        }
        if (array_key_exists('order',$data)){
            $query.=' ORDER BY '.$data['order'];
        }
        if (array_key_exists('limit',$data)){
            $query.=" LIMIT ".$data['limit'][0].",".$data['limit'][1];
        }
        $result= $this->wpdb->get_results($query, ARRAY_A);
        return $result;
    }




}
?>