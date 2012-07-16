<?php
class medicine
{
  private $id;
  private $name;
  private $regular;
  private $kinds;
  private $frequency;
  private $comment;
  private $timestamp;
  private $times;
  private $term_start;
  private $date;
  //private $term_end;
  private $lot;

  function setId($key){
    $this->id = $key;
  }
  function setName($key){
    $this->name = $key;
  }
  function setRegular($key){
    $this->regular = $key;
  }
  function setKinds($key){
    $this->kinds = $key;
  }
  function setFrequency($key){
    $this->frequency = $key;
  }
  function setComment($key){
    $this->comment = $key;
  }
  function setTimestamp($key){
    $this->timestamp = $key;
  }
  function setTimes($key){
    $this->times = $key;
  }
  function setTermStart($key){
    $this->term_start = $key;
  }
  //function setTermEnd($key){
  //  $this->term_end;
  //}
  function setDate($key){
    $this->date = $key;
  }
  function setLot($key){
    $this->lot = $key;
  }

  function getId(){
    return $this->id;
  }
  function getName(){
    return $this->name;
  }
  function getRegular(){
    return $this->regular;
  }
  function getKinds(){
    return $this->kinds;
  }
  function getFrequency(){
    return $this->frequency;
  }
  function getComment(){
    return $this->comment;
  }
  function getTimestamp(){
    return $this->timestamp;
  }
  function getTimes(){
    return $this->times;
  }
  function getTermStart(){
    return $this->term_start;
  }
  //function getTermEnd(){
  //  return $this->term_end;
  //}
  function getDate(){
    return $this->date;
  }
  function getLot(){
    return $this->lot;
  }
  
}

?>