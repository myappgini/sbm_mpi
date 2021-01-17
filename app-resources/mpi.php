<?php

//
// Author: Alejandro Landini
// Mpi.php 5/6/18
//      manage profile image
// TODO:
//
// revision: 16/01/21

class Mpi
{
    public $user;
    public $image;
    public $thumb;
    public $message = [];
    public $Version = '2.0'; //mpi version
    private $path;
    private $dataFileName = 'mpi_data.json';
    private $json; //in string
    private $data; //in array
    private $noImage = 'no_image.png';

    public function __construct($user, $path, $newImage = '', $thumb = '')
    {
        $this->user = $user;
        $this->path = $path;
        $this->data = $this->get_data_file();

        if (!$this->get_value('image')) {
            //set no image if user is new
            $this->data['mpi'][$this->user]['image'] = $this->noImage;
            $this->data['mpi'][$this->user]['thumb'] = $this->noImage;
        }

        if (empty($newImage)) {
            $this->image = $this->get_value('image');
            $this->thumb = $this->get_value('thumb');
        } else {
            $this->image = $newImage;
            $this->thumb = $thumb;
            $this->add_user_data();
        }
    }

    public function remove_default($value = null)
    {
        if (is_null($value)) {
            return $this->get_value('remove_default');
        } else {
            $this->data['mpi'][$this->user]['remove_default'] = $value;
            $this->add_user_data();
            return $value;
        }
    }

    private function set_data_file()
    {
        //set the dataFileName
        if (!$this->is_file_exist()) {
            $handle = fopen($this->path . $this->dataFileName, 'w+');
            fwrite($handle, $this->json);
            fclose($handle);
            $this->message[] = 'Data successfully saved in new file';
        } else {
            if (
                file_put_contents(
                    $this->path . $this->dataFileName,
                    $this->json
                )
            ) {
                $this->message[] = 'Data successfully saved';
            } else {
                $this->message[] = 'error';
            }
        }
        return;
    }

    private function is_file_exist()
    {
        return file_exists($this->path . $this->dataFileName);
    }

    private function get_data_file()
    {
        if ($this->is_file_exist()) {
            return json_decode(
                file_get_contents($this->path . $this->dataFileName),
                true
            );
        }
        return false;
    }

    private function get_value($key)
    {
        if (!empty($this->data)) {
            $data = $this->data['mpi'][$this->user];
            return $data[$key];
        }
        return false;
    }

    private function add_user_data()
    {
        $data = $this->data;

        if (empty($data)) {
            //not file found, add initial data
            $data = [];
            $data['mpi'] = [];
            $data['dafault_image'] = $this->noImage;
            $data['mpi_version'] = $this->Version;
            $this->message[] = 'Database not found, Added initial data';
        }
        //new data user
        $d = [
            'user_name' => $this->user,
            'image' => empty($this->image) ? $this->noImage : $this->image,
            'thumb' => empty($this->thumb) ? $this->noImage : $this->thumb,
            'date_add' => empty($this->get_value('date_add'))
                ? date('d.m.y')
                : $this->get_value('date_add'),
            'date_change' => date('d.m.y'),
            'remove_default' => $this->remove_default(),
        ];

        $user_data = $data['mpi'][$this->user]; //old data user if exist
        if (empty($user_data)) {
            $user_data = [];
        } //if not existe set empty array
        $user_data = array_merge($user_data, $d); //new data user
        $data['mpi'][$this->user] = $user_data; //add or replace user data y data base

        //update database
        $this->data = $data;
        $this->json = json_encode($this->data);
        $this->set_data_file();
        return;
    }
}
//test
//$a = new Mpi('alea',$hooks_dir.'/../images/');
//echo $a->user;
//echo '<br>';
//echo $a->image;
//echo '<br>';
//echo $a->message;
//echo '<br>';
//echo $a->image;
