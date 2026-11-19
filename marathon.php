<?php
class Marathon {
    //Properties
    public $id;
    public $raceName;
    public $location;
    public $year;
    public $completed;
    public $raceType;
    public $image;

    //Constructor 
    public function __construct($id, $raceName, $location, $year, $completed, $raceType, $image) {
    $this->id = $id;
    $this->raceName = $raceName;
    $this->location = $location;
    $this->year = $year;
    $this->completed = $completed;
    $this->raceType = $raceType;
    $this->image = $image;
    }
}

?>